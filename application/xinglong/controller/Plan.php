<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
use think\Db;
use think\Session;
use think\Cache;

/*此控制器 负责各望远镜的计划发送*/
class Plan extends Base
{
    //定义所需变量
    protected $sequence = 0;    //指令的序号
    protected $at = 0;  //望远镜序号
    protected $device = 66;  //设备对应序号
    protected $msg = 0;  //指令类型 计划数据时msg = 8; 执行 
    protected $magic = 439041101;  //转台对应序号
    protected $version = 1;  //版本号
    protected $plan = 0;  //计划
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port

    //导入计划文件//////////////////////////////////////////////
	public function importPlan ()
	{
		//首先判断是否已登录
        if ($this->ajaxAuthErr == 'not_log')
        {
            return '请先登录再进行操作!';
		}
		
		//首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
		}*/
		$postData = input();

		if ( !isset($postData['at']) || empty($postData['at']) )
		{
			return '未收到望远镜id';
		}
		
		$aperture = $postData['aperture']; //标记是否为80望远镜，如果是将增益置为1
		//halt($aperture);
		// 获取表单上传文件 
		$file = request()->file('plan');
		//halt($file);
		//halt($file);
		//上传文件验证
		/*$result = $this->validate(
				['file' => $file],
				['file' => 'file|require|fileExt:txt|fileSize:4096000000|fileMime:text/plain'],
				['file.require' => '请选择上传文件',
				 'file.fileExt' => '文件后缀名必须为txt',
				 'file.fileSize' => '文件大小超出限制',
				 'file.fileMime' => '文件格式或文件内容不符合要求']);*/
		$result = $this->validate(
		['file' => $file],
		['file' => 'file|require|fileSize:123800|fileMime:text/plain'],
		['file.require' => '请选择上传文件',
			//'file.fileExt' => '文件后缀须为strat',
			'file.fileSize' => '文件大小超出限制',
			'file.fileMime' => '文件格式或文件内容不符合要求']);
		if (true !== $result)
		{
			return $result;
		}
		// 移动到框架应用根目录/public/uploads/ 目录下
		$dir = date('Ymd', time());
		$name = time(). session('login');
		//文件存储存储路径
		$path = ROOT_PATH . 'public' . DS . 'uploads/' .$dir;
		$info = $file->move($path, $name.'.txt');

		if($info)
		{
			//获取计划文件的字符编码
			$planData = file_get_contents($path.'/'.$name.'.txt');
			
			$encoding = mb_detect_encoding($planData, array('GB2312','GBK','UTF-16','UCS-2','UTF-8','BIG5','ASCII','CP936'));
			$fileData=[]; //存储计划的每行数据
			//halt($encoding);
			$file = fopen($path.'/'.$name.'.txt', 'r'); //只读方式打开文件
			
			while(!feof($file)) //输出文本中所有的行，直到文件结束为止。
			{
				$str = trim(fgets($file));   //fgets()从文件指针中读取一行
				if ($encoding !== false && $encoding !== 'CP936')
				{ 
					$str = iconv($encoding, "UTF-8//IGNORE", $str);
					  if ($str)
					  {
						  $fileData[] = $str;
					  }
				}else{
					$str = mb_convert_encoding ($str, 'UTF-8');

					  if ($str)
					  {
						  $fileData[] = $str;
					  }
				}
			}
			fclose($file);
			array_filter($fileData);  //删除$fileData中等值为false的元素
			
			$data_perLine_onePlan = []; //将上传文件的原始数据转为每行一条完整计划的格式 返回给页面显示
			$line = 0; //读取的第几行数据
			$object_num = 0; //第几个观测目标

			//检查每个目标数据的后半部分，是否符合6个参数或8个参数；前半部分是否6个参数
			$param_num_err = ''; //标记参数

			foreach ($fileData as $k => $v)
			{
				if ( strpos($v, '$') === false )//是数据的后半部分
				{
					$param_num = count( explode(',', $v) ); //后半部分参数个数
					//halt($param_num);
					if ( !($param_num == 7 || $param_num == 9) )
					{
						$param_num_err .= '计划文件第' . ($k + 1) . '行参数个数有误!<br>';
					}
				}else{
					$param_num = count( explode(',', $v) ); //前半部分参数个数
					if ( $param_num != 7 )
					{
						$param_num_err .= '计划文件第' . ($k + 1) . '行参数个数有误!<br>';
					}
				}
			}
			//查数据库中默认bin, 默认读出速度，默认增益档位
			$default_bin_read_gain = Db::table('ccdconf')->where('teleid', $postData['at'])->where('ccdno', 1)->field('default_bin, default_readout, default_gain')->find();
			if ( $default_bin_read_gain['default_bin'] === null ) $param_num_err .= '未配置默认bin<br>';
			if ( $default_bin_read_gain['default_gain'] === null ) $param_num_err .= '未配置默认增益<br>';
			if ( $default_bin_read_gain['default_readout'] === null ) $param_num_err .= '未配置默认读出速度';

			if ( $param_num_err !== '' )
			{
				return $param_num_err;
			}
			//检查每个目标数据的后半部分，是否符合6个参数或8个参数 结束

			foreach ($fileData as $k => $v) //遍历每行数据 文件中数据的验证稍后再说（就在此遍历中验证每一条计划是否有错误）
			{
				$line ++;
				$temp_line = str_replace (';', '', $v); //去掉每行末尾的';' 暂时存储每行计划数据
				
				//组装一条完整计划 每条以'$'开头的表示一个观测目标
				if ( strpos($v, '$') !== false ) //一个观测目标
				{
					$object_num ++;
					$temp_line = ltrim ($temp_line, '$,'); //去掉开头的'$,'
					$perLine_pre = $temp_line; //每个观测目标的相同部分 即前段
				}else{//当前观测目标的后段
					$temp_line = rtrim ($temp_line, ','); //去掉末尾的','
					$index = $line - $object_num -1; //当前行数减当前目标数再减1即为$data_perLine_onePlan数组的下标（从0开始，每一条在页面显示为一行）
					$data_perLine_onePlan[$index] = $perLine_pre .$temp_line;

					//接下来把刚组装的计划数据整理为页面所需的一个数组，并以json格式返回
					$row = explode( ',', $data_perLine_onePlan[$index] );
					$row_param_num = count( $row ); //每行计划的总参数个数（11个或13个），若为13个，则$row[11]为增益档位，$row[12]为bin

					$plan["plan".$index]["target"] = trim($row[0]); //目标名称
					$plan["plan".$index]["type"] = '恒星';  //目标类型 默认为恒星
					//处理赤经
					$rightAscension = explode (':', trim($row[1]));
					$plan["plan".$index]["rightAscension1"] = trim( $rightAscension[0] );
					$plan["plan".$index]["rightAscension2"] = trim( $rightAscension[1] );
					$plan["plan".$index]["rightAscension3"] = trim( $rightAscension[2] );

					//处理赤纬
					$declination = explode (':', trim($row[2]));
					$plan["plan".$index]["declination1"] = trim( $declination[0] );
					$plan["plan".$index]["declination2"] = trim( $declination[1] );
					$plan["plan".$index]["declination3"] = trim( $declination[2] );

					if ( trim($row[3]) == '2000' )
					{
						$plan["plan".$index]["epoch"] = 'J2000'; //历元
					}
					
					$plan["plan".$index]["exposureTime"] = trim($row[6]); //曝光时间
					$plan["plan".$index]["delayTime"] = trim($row[9]); //延迟时间
					$plan["plan".$index]["exposureCount"] = trim($row[10]); //曝光数量					
					$plan["plan".$index]["filter"] = trim($row[7]); //滤光片

					if ( $row_param_num == 11 )//上传文件中无增益参数
					{
						$plan["plan".$index]["gain"] = $default_bin_read_gain['default_gain'];//取数据库默认增益
					}else if ( $row_param_num == 13 ){//上传文件中有增益参数
						$plan["plan".$index]["gain"] = trim($row[11]);
					}
					/*if ( $aperture == '80cm' )
					{
						$plan["plan".$index]["gain"] = '0'; //增益 文件中无此参数 我给默认为1
					}else{
						$plan["plan".$index]["gain"] = '2';
					}*/
					
					if ( $row_param_num == 11 )//上传文件中无bin参数
					{
						$plan["plan".$index]["bin"] = $default_bin_read_gain['default_bin'];//取数据库默认bin
					}else if ( $row_param_num == 13 ){//上传文件中有bin参数
						$plan["plan".$index]["bin"] = trim($row[12]);
					}

					//$plan["plan".$index]["bin"] = '1'; //bin 文件中无此参数 我给默认为0(即1*1)
					$plan["plan".$index]["readout"] = trim($row[8]);; //读出速度
					$plan["plan".$index]["id"] = $index + 1;
					//整理数组 结束
					$index ++;					
				} //组装一条完整计划 结束
			} //遍历每行数据 结束

			/*此代码为遍历 原来最原始我的txt文本格式文件
			//遍历每行数据
			foreach ($fileData as $k => $data)
			{
				//解析每行计划的每个参数
				$row = explode('#', $data);
				if (count($row) !== 12)	//计划的参数  个数不对
				{
					return '第'.($k+1).'行计划参数的数量错误';
				}else{
					$plan["plan".$k]["target"] = trim($row[0]);
					$plan["plan".$k]["type"] = trim($row[1]);
					
					//处理赤经
					$rightAscension = explode (':', trim($row[2]));
					$plan["plan".$k]["rightAscension1"] = $rightAscension [0];
					$plan["plan".$k]["rightAscension2"] = $rightAscension [1];
					$plan["plan".$k]["rightAscension3"] = $rightAscension [2];
					
					//处理赤纬
					$declination = explode (':', trim($row[3]));
					$plan["plan".$k]["declination1"] = $declination [0];
					$plan["plan".$k]["declination2"] = $declination [1];
					$plan["plan".$k]["declination3"] = $declination [2];
					
					$plan["plan".$k]["epoch"] = trim($row[4]);
					$plan["plan".$k]["exposureTime"] = trim($row[5]);
					$plan["plan".$k]["delayTime"] = trim($row[6]);
					$plan["plan".$k]["exposureCount"] = trim($row[7]);
					$plan["plan".$k]["filter"] = trim($row[8]);
					$plan["plan".$k]["gain"] = trim($row[9]);
					$plan["plan".$k]["bin"] = trim($row[10]);
					$plan["plan".$k]["readout"] = trim($row[11]);
					$plan["plan".$k]["id"] = $k+1;
				}
			} //遍历每行数据 结束 */
			return json_encode($plan);	
		}else{// 上传失败获取错误信息
			return $file->getError();
		}		
    }//导入计划文件 结束////////////////////////////////////////

    public function sendData () //观测计划 根据提交的参数 调用函数
    {
		//首先判断是否已登录
		if ($this->ajaxAuthErr == 'not_log')
		{
			return '请先登录再进行操作!';
		}

		//首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

        //接受表单数据
        $postData = input ();
        //验证数据
        // if (!$postData['at'])
        // {//未接收到望远镜编号
        //     return '网络异常,请刷新页面后再次提交指令!';
        // }

        //定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$this->sequence = Cookie::get('sequence');
			Cookie::set('sequence', $this->sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$this->sequence = 0;
		}
        
        $ip = config('ip');
        $port = config('port');

        switch ($postData['at_aperture']) { //根据望远镜口径，给 $this->$at赋值
			case '50cm':
				$this->at = 38;
				$this->ip = $ip['at50'];
				$this->port = $port;
				break;
			case '60cm':
				$this->at = 37;
				$this->ip = $ip['at60'];
				$this->port = $port;
				break;
			case '80cm':
				$this->at = 36;
				$this->ip = $ip['at80'];
				$this->port = $port;
				break;
			case '85cm':
				$this->at = 35;
				$this->ip = $ip['at85'];
				$this->port = $port;
				break;
			case '100cm':
				$this->at = 34;
				$this->ip = $ip['at100'];
				$this->port = $port;
				break;
			case '126cm':
				$this->at = 33;
				$this->ip = $ip['at126'];
				$this->port = $port;
				break;
			case '216cm':
				$this->at = 32;
				$this->ip = $ip['at216'];
				$this->port = $port;
				break;
			default:
				return '提交的望远镜参数有误!';
        }

        //$command = input('command'); //获取提交的指令
        //根据不同参数 调用相应方法
        if ( $postData['command'] == 1 ) //验证计划的数据，并发送计划数据
        {      
            return $this->sendPlan($postData);   //执行发送
		}else if( $postData['command'] == 2 )
		{//根据计划执行方式，开始执行计划       
            return $this->planOption($postData);
		}else if( $postData['command'] == 'get_plan' )
		{//查询当前用户是否有观测计划正在执行      
            return $this->get_plan($postData, $this->at);
		}else if( $postData['command'] == 'get_cached_plan' )
		{//点击'正执行的计划'按钮 查询Cache中的计划数据和被选中的计划索引
			$cached_plans = Cache::get( $postData['at_aperture'] ); //每个望远镜都以口径值命名缓存的计划
			$cached_checked_index = Cache::get( $postData['at_aperture']. 'checked_plans_index' );
			
			if ( $cached_plans && $cached_checked_index  ) //若有缓存
			{
				return 'cache' .'#'. $cached_plans .'#'. $cached_checked_index; //以#分割
			}else{
				return '还没有正在执行的计划';
			}
			
        }else if( $postData['command'] == 'get_submited_plan' )
		{//点击'已提交的计划'按钮 查询Cache中的被提交后缓存的计划数据
			$cached_plans = Cache::get( $postData['at_aperture'] ); //每个望远镜都以口径值命名缓存的计划
			if ( $cached_plans ) //若有缓存
			{
				return 'cache' .'#'. $cached_plans; //以#分割
			}else{//无缓存时
				return '还没有被提交的计划数据';
			}
			
        }

    }//观测计划 根据提交的参数 调用函数 结束 
    
    //获取计划数据 验证并发送计划数据/////////////////////////////
	protected function sendPlan ($postData)  //即原来的savePlan函数
	{
		//halt($postData);die();
		//定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$this->sequence = Cookie::get('sequence');
			Cookie::set('sequence', $this->sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$this->sequence = 0;
		}
                  
		//验证计划数据
		$planNum = count($postData['planData']);
		$errMsg = '';	//错误提示
		$filter_option = $postData['plan_filter_option'];  //['u','v','b'] 每台望远镜配置的滤光片名称
		//循环验证每条计划数据
		for ($i = 0; $i < $planNum; $i ++)
		{
			
			$target = $postData['planData'][$i]['target'];  //验证目标
			$target_length = strlen ($target);

			if ( preg_match('/[\x{4e00}-\x{9af5} ]/u', $target) || $target_length > 48 || $target_length < 0 )
			{
				$errMsg .= '第'. ($i+1) .'条计划:目标名称参数超限!<br>';
			}

			//验证目标类型
			$type = $postData['planData'][$i]['type'];
			
			if( !(preg_match('/^[0-9]$/', $type) || in_array($type, ['恒星','太阳','月亮','彗星','行星','卫星','固定位置','本底','暗流','平场'])) )
			{
				$errMsg .= '第'. ($i+1) .'条计划:目标类型参数超限!<br>';
			}

			//处理赤经数据
			$rightAscension = $postData['planData'][$i]['rightAscension1']. ':' .$postData['planData'][$i]['rightAscension2'] .':'. $postData['planData'][$i]['rightAscension3'];
			
			//验证 rightAscension
			if ($rightAscension !== '::')	//赤经：有输入时
			{
				if ( !is_numeric(str_replace(':', '', $rightAscension)) )
				{ //rightAscension 若不是数字，>24，或＜0时
					$errMsg .= '第'. ($i+1) .'条计划:赤经数据参数超限!<br>';
				}else{//rightAscension: 是数字 >24，或＜0时
					$rightAscension = time2Data($rightAscension);
					if ( $rightAscension > 24 || $rightAscension < 0)
					{
						$errMsg .= '第'. ($i+1) .'条计划:赤经数据参数超限!<br>';
					}
				}
			}else{ //赤经：无输入时
				$errMsg .= '第'. ($i+1) .'条计划:赤经数据参数超限!<br>';
			}

			//处理赤纬数据
			$declination = $postData['planData'][$i]['declination1']. ':' .$postData['planData'][$i]['declination2']. ':' .$postData['planData'][$i]['declination3'];
			
			//验证 赤纬
			if ($declination !== '::')	//赤纬：有输入时
			{
				if ( !is_numeric(str_replace(':', '', $declination)) )
				{	//赤纬 不是数字
					$errMsg .= '第'. ($i+1) .'条计划:赤纬数据参数超限!<br>';
				}else{	//赤纬 > 90 || < -90
					$declination = time2Data($declination);
					if( $declination > 90 || $declination < -90)
					{
						$errMsg .= '第'. ($i+1) .'条计划:赤纬数据参数超限!<br>';
					}
				}

			}else{	//赤纬 无输入时
				$errMsg .= '第'. ($i+1) .'条计划:赤纬数据参数超限!<br>';
			}

			//验证历元 
			$epoch = $postData['planData'][$i]['epoch'];

			$epoch = strtolower ($epoch);
			if ( !(preg_match('/^[0-3]$/', $epoch) || in_array($epoch, ['real','j2000','b1950','j2050'])) )
			{
				$errMsg .= '第'. ($i+1) .'条计划:历元参数超限!<br>';
			}
	
			//验证 曝光时间
			$exposureTime = $postData['planData'][$i]['exposureTime'];

			if( !is_numeric($exposureTime) || $exposureTime > $postData['maxExpose'] || $exposureTime < $postData['minExpose'] ) //若不是数字
			{
				$errMsg .= '第'. ($i+1) .'条计划:曝光时间参数超限!<br>'; 
			}

			//验证 delayTime
			$delayTime = $postData['planData'][$i]['delayTime'];

			if( !is_numeric($delayTime) || $delayTime < 0 ) //delayTime 若不是数字
			{
				$errMsg .= '第'. ($i+1) .'条计划:delayTime参数超限!<br>';
			}

			//验证 exposureCount
			$exposureCount = $postData['planData'][$i]['exposureCount'];

			if ( !preg_match('/^\d+$/', $exposureCount) || $exposureCount < 1 )
			{
				$errMsg .= '第'. ($i+1) .'条计划:曝光数量超限!<br>';
			}

			//验证 滤光片
			$filter = $postData['planData'][$i]['filter'];
	
			//if ( !preg_match('/^[0-9]$/', $filter) && !in_array($filter, $filter_option) )
			if ( !in_array($filter, $filter_option) )
			{
				$errMsg .= '第'. ($i+1) .'条计划:滤光片参数超限!<br>';
			}

			/*//验证增益
			$gain = $postData['planData'][$i]['gain'];
			if($gain === '')//增益 未填写
			{
				$errMsg .= '第'. ($i+1) .'条计划:增益参数超限!<br>';
			}else{
				if ( !preg_match('/^\d+$/', $gain) || $gain < 1)
				{
					$errMsg .= '第'. ($i+1) .'条计划:增益参数超限!<br>';
				}
			}

			//验证Bin 
			$bin = $postData['planData'][$i]['bin'];
			if($bin === '')//Bin 未填写
			{
				$errMsg .= '第'. ($i+1) .'条计划:bin参数超限!<br>';
			}else{
				if ( !preg_match('/^\d+$/', $bin) || $bin < 1)
				{
					$errMsg .= '第'. ($i+1) .'条计划:bin参数超限!<br>';
				}
			}

			//验证读出速度 
			$readout = $postData['planData'][$i]['readout'];
			if($readout === '')//读出速度 未填写
			{
				$errMsg .= '第'. ($i+1) .'条计划:读出速度参数超限!<br>';
			}else{
				if ( !preg_match('/^\d+$/', $readout) ||$readout < 1)
				{
					$errMsg .= '第'. ($i+1) .'条计划:读出速度参数超限!<br>';
				}
			}*/			
		}	//循环验证每条计划数据 结束///////

		if ($errMsg !== '' )	//若验证有错误
		{
			return $errMsg;
		}
        
       /*//接下来 给中控 发送数据 ///////////////
		//首先执行一个停止的指令
		
		$this->msg = 9; //指令类型
        $length =28 + 16; //数据长度
	   
		$headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
               
		$sendMsg = pack('L', 2);
		$sendMsg .= pack('L', '4'); //执行模式 ？此模式值共有4个（1,2,3,4）
		$sendMsg .= pack('L', 1); //从第几条开始
		$sendMsg .= pack('L', 0);
		
		$sendMsg = $headInfo . $sendMsg;
		udpSend($sendMsg, $this->ip, $this->port);
		//停止指令 发送完毕

		sleep (1); //1秒后，再发送计划数据，保证停止指令执行后，再接受计划数据并执行*/

		//接下来开始给中控发送计划数据
		$this->msg = 8; $length =28 + 208;
	   
		$headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
        
        //接下来 给中控 发送数据 ///////////////
		for ( $i = 0; $i < $planNum; $i ++ )
		{
			$sendMsg = pack('L', $i+1);  //每条计划的tag unsigned int 
			
			$sendMsg .= pack('S', $this->at);
            $sendMsg .= pack('a48', $this->userName); //登录的用户名
			$sendMsg .= pack('a48', '02'); //project
			
			$target = $postData['planData'][$i]['target']; //目标名		
			$sendMsg .= pack('a48', $target);
			
			//目标类型
			if (preg_match('/^[0-9]$/', $type)) //数字类型
			{
				$sendMsg .= pack('L', $type); 
			}else{//直接为汉字类型数据
				if($type == '恒星')
				{
					$sendMsg .= pack('L', 0); 
				}elseif($type == '太阳'){
					$sendMsg .= pack('L', 1); 
				}elseif($type == '月亮'){
					$sendMsg .= pack('L', 2); 
				}elseif($type == '彗星'){
					$sendMsg .= pack('L', 3); 
				}elseif($type == '行星'){
					$sendMsg .= pack('L', 4); 
				}elseif($type == '卫星'){
					$sendMsg .= pack('L', 5); 
				}elseif($type == '固定位置'){
					$sendMsg .= pack('L', 6); 
				}elseif($type == '本底'){
					$sendMsg .= pack('L', 7); 
				}elseif($type == '暗流'){
					$sendMsg .= pack('L', 8); 
				}elseif($type == '平场'){
					$sendMsg .= pack('L', 9); 
				}
			}

			//赤经 rightAscension	
			$rightAscension = $postData['planData'][$i]['rightAscension1'].':'.$postData['planData'][$i]['rightAscension2'].':'.$postData['planData'][$i]['rightAscension3'];			
			$rightAscension = time2Data($rightAscension);
			$sendMsg .= pack('d', $rightAscension);
			//赤纬
			$declination = $postData['planData'][$i]['declination1'].':'.$postData['planData'][$i]['declination2'].':'.$postData['planData'][$i]['declination3'];
			$declination = time2Data($declination);
			$sendMsg .= pack('d', $declination);
			//历元
			$epoch = $postData['planData'][$i]['epoch'];
		
			if ( preg_match('/^[0-9]$/', $epoch) ) //数字类型
			{
				$sendMsg .= pack('L', $epoch); 
			}else{//直接为字符类型数据 
				$epoch = strtolower ($epoch);
				if( $epoch == 'real' )
				{
					$sendMsg .= pack('L', 0);
				}elseif( $epoch == 'j2000' ){
					$sendMsg .= pack('L', 1); 
				}elseif( $epoch == 'b1950' ){
					$sendMsg .= pack('L', 2); 
				}elseif( $epoch == 'j2050' ){
					$sendMsg .= pack('L', 3); 
				}
			}	//历元结束

			//曝光时间
			$exposureTime = $postData['planData'][$i]['exposureTime'];
			$sendMsg .= pack('d', $exposureTime); 

			//delayTime
			 $delayTime = $postData['planData'][$i]['delayTime'];
			 $sendMsg .= pack('d', $delayTime);

			 //曝光数量
			 $exposureCount = $postData['planData'][$i]['exposureCount'];
			 $sendMsg .= pack('L', $exposureCount);

			 //滤光片
			$filter = $postData['planData'][$i]['filter'];
			$sendMsg .= pack('a8', $filter);
			// if ( preg_match('/^\d+$/', $filter) ) //数字类型
			// {
			// 	$sendMsg .= pack('a8', $filter_option[$filter]); 
			// }else{//直接为字符类型数据
			// 	$filter = strtoupper ($filter);
			// 	$sendMsg .= pack('a8', $filter);
			//  }	//滤光片

			//增益 gain
			$gain = $postData['planData'][$i]['gain'];
			/*if ( $this->at == 36 )
			{//如果是at80, 
				$gain = '0'; //发送的增益默认为0
			}
			$sendMsg .= pack('S', $gain);*/
			
			//bin
			$bin = $postData['planData'][$i]['bin'];
			$sendMsg .= pack('S', $bin);
			
			//读出速度
			$readout = $postData['planData'][$i]['readout'];
			$sendMsg .= pack('S', $readout);
			
			//发送 计划数据
        	$sendMsg = $headInfo . $sendMsg;
           
            udpSendPlan($sendMsg, $this->ip, $this->port); //无返回值
		}//给中控 发送数据 结束///////////////

		/*// 把计划数据写入plandata中
		$plan_data_json = json_encode ($postData['planData']); //将提交上来的计划数据转为json字串
		$data = [
			'atuser' => $this->user,
			'at' => $postData['at'],
			'plan' => $plan_data_json
		];

		$res = Db::table('plandata')->insert($data);

		if (!$res)
		{
			return '观测计划缓存失败, 请重新提交观测计划!';
		} //计划数据写入plandata中 结束*/

		// 把计划数据写入Cache中 planDataStr 缓存的名称是望远镜口径
		Cache::set ( $postData['at_aperture'], $postData['planDataStr'] );
		//计划数据写入Cache中 结束

		return '观测计划发送完毕!';
    } //获取计划数据 验证并发送计划数据 结束////////////////////////////////////////
    
	protected function planOption ($postData) //观测计划的 开始 停止
	{	
		//首先判断是否已登录
		if ($this->ajaxAuthErr == 'not_log')
		{
			return '请先登录再进行操作!';
		}

		//首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
		}*/
		
		//定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$this->sequence = Cookie::get('sequence');
			Cookie::set('sequence', $this->sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$this->sequence = 0;
		}
           
        $this->msg = 9; //指令类型
        $length =28 + 16; //数据长度
	   
		$headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
               
		if($postData['planOption'] == 'planStart')
		{
			Cache::set( $postData['at_aperture'] . 'checked_plans_index', $postData['checked_plans_index'] ); //点击‘开始’按钮时发送过来的被选中的计划索引
			
			$sendMsg = pack('L', 1);
			$sendMsg .= pack('L', $postData['mode']);
			$sendMsg .= pack('L', $postData['start']);
			$sendMsg .= pack('L', 0);
			
			$sendMsg = $headInfo . $sendMsg;
			return '计划开始:' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif($postData['planOption'] == 'planStop'){
			$sendMsg = pack('L', 2);
			$sendMsg .= pack('L', $postData['mode']);
			$sendMsg .= pack('L', $postData['start']);
			$sendMsg .= pack('L', 0);
			
			$sendMsg = $headInfo . $sendMsg;
			Cache::rm($postData['at_aperture']);//删除此望远镜计划的缓存
			return '计划停止:' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif($postData['planOption'] == 'planNext'){
			$sendMsg = pack('L', 3);
			$sendMsg .= pack('L', $postData['mode']);
			$sendMsg .= pack('L', $postData['start']);
			$sendMsg .= pack('L', 0);
			
			$sendMsg = $headInfo . $sendMsg;
			return '下一条计划:' .udpSend($sendMsg, $this->ip, $this->port);
		}
	}//观测计划的 开始 停止 下一个 结束////////////////////////////

	/*ajax 请求  是否有观测计划在执行*/
	protected function get_plan ($postData, $at)
	{
		$plan_table = ''; //中控之 各望远镜的观测计划表

		switch ($at)
		{
			case 37:
				$plan_table = 'at60plan';
				break;
			case 36:
				$plan_table = 'at80plan';
				break;
			case 38:
				$plan_table = 'at50plan';
				break;
			case 35:
				$plan_table = 'at85plan';
				break;
			case 34:
				$plan_table = 'at100plan';
				break;
			case 33:
				$plan_table = 'at126plan';
				break;
			case 32:
				$plan_table = 'at216plan';
				break;
			case 39:
				//$plan_table = '大气消光镜';
				break;
		}

		/*20181129兴隆验收时注释，改为从计划数据表中读最新一条，比对目标名称和滤光片来确定正执行哪条
		$sql = 'select tag from ' . $plan_table . ' where "user" ='. "'". $this->user. "'" . ' order by id desc limit 1';
		
		$exetue = Db::query($sql);
		
		if ( $exetue )
		{
			return 'tagOk#' . $exetue[0]['tag']; //返回tag, 'tagOk'用来供前端判断
		}else{
			return '无正执行计划!';
		}*/

		/*最新的比对目标名称和滤光片的代码*/
		$data = Db::table($plan_table)->order('id', 'desc')->find();
		if ($data)
		{
			return 'tagOk#' . json_encode($data);
		}else{
			return '无正执行计划!';
		}
		
		/*最新的比对目标名称和滤光片的代码*/
		
		/*halt($exetue);
		//$plan_data = Db::table('plandata')->where('atuser', $this->user)->where('at', $at)->order('id', 'desc')->field('plan')->find();
		//halt($plan_data);
		if ( $exetue )
		{
			return 'tagOk#' . $exetue[0]['tag']; //返回tag, 'tagOk'用来供前端判断
			//return $plan_data['plan']. '#' . $exetue[0]['tag']; //将计划的json数据连接上#tag
			//return '{}' . '#' . $exetue[0]['tag']; //将计划的json数据连接上#tag
		}else{
			return '无正执行计划!';
		}
		/*try{
			//Db::table($plan_table)->where('user', $this->user)->order('id', 'desc')->field('tag')->find();
			//如果此用户用正在执行的计划，
			//........
			//去表plandata中倒序获取第一条，以json格式返回给ajax
			$sql = 'select tag from ' . $plan_table . ' where "user" ='. "'". $this->user. "'" . ' order by id desc limit 1';
			$exetue = Db::query($sql);
			//halt($exetue);
			//$plan_data = Db::table('plandata')->where('atuser', $this->user)->where('at', $at)->order('id', 'desc')->field('plan')->find();
			//halt($plan_data);
			if ( $exetue && $plan_data )
			{
				return $plan_data['plan']. '#' . '2'; //将计划的json数据连接上#tag
				//return $plan_data['plan']. '#' . $exetue[0]['tag']; //将计划的json数据连接上#tag
				//return '{}' . '#' . $exetue[0]['tag']; //将计划的json数据连接上#tag
			}else{
				return '无正执行计划!';
			}
		}catch(\Exception $e){
			return '查询正在执行计划遇异常!';
		}*/
	}/****ajax 请求  是否有观测计划在执行 结束*******/
}