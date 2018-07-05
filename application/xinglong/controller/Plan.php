<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
use think\Db;
use think\Session;

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
    protected $user = 0;  //操作者
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port

    //导入计划文件//////////////////////////////////////////////
	public function importPlan ()
	{
		//首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/
		
		// 获取表单上传文件 
		$file = request()->file('plan');
	
		//上传文件验证
		$result = $this->validate(
				['file' => $file],
				['file' => 'file|require|fileExt:txt|fileSize:4096000000|fileMime:text/plain'],
				['file.require' => '请选择上传文件',
				 'file.fileExt' => '文件后缀名必须为txt',
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

		if($info){
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

			}
			return json_encode($plan);
			
		}else{// 上传失败获取错误信息
			return $file->getError();
		}		
    }//导入计划文件 结束////////////////////////////////////////

    //观测计划 根据提交的参数 调用函数
    public function sendData ()
    {
        //首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

        //接受表单数据
        $postData = input ();
        //验证数据
        if (!$postData['at'])
        {//未接收到望远镜编号
            return '网络异常,请刷新页面后再次提交指令!';
        }

        //定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$this->sequence = Cookie::get('sequence');
			Cookie::set('sequence', $this->sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$this->sequence = 0;
		}
        
        $this->ip = config('ip');
        $this->port = config('port');

        switch ($postData['at_aperture']) { //根据望远镜口径，给 $this->$at赋值
            case '50cm':
                $this->at = 38;
                break;
            case '60cm':
                $this->at = 37;
                break;
            case '80cm':
				$this->at = 36;
				break;
            case '85cm':
                $this->at = 35;
                break;
            case '100cm':
                $this->at = 34;
                break;
            case '126cm':
                $this->at = 33;
                break;
            case '216cm':
                $this->at = 32;
                break;
            default:
                return '提交的望远镜参数有误!';
        }

        //$command = input('command'); //获取提交的指令
        //根据不同参数 调用相应方法
        if ( $postData['command'] == 1 ) //验证计划的数据，并发送计划数据
        {      
            return $this->sendPlan($postData);   //执行发送
        }else if( $postData['command'] == 2 ){//根据计划执行方式，开始执行计划       
            return $this->planOption($postData); //执行发送
        }else if( $postData['command'] == 'get_plan' ){//查询当前用户是否有观测计划正在执行      
            return $this->get_plan($postData['at']);
        }

    }//观测计划 根据提交的参数 调用函数 结束 
    
    //获取计划数据 验证并发送计划数据/////////////////////////////
	protected function sendPlan ($postData)  //即原来的savePlan函数
	{
		//halt($planData['plan_filter_option']);
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
			
			if( !preg_match('/^[0-9]$/', $type) && !in_array($type, ['恒星','太阳','月亮','彗星','行星','卫星','固定位置','本底','暗流','平场']) )
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
			if ( !preg_match('/^[0-3]$/', $epoch) && !in_array($epoch, ['real','j2000','b1950','j2050']) )
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
			$filter = strtoupper ($postData['planData'][$i]['filter']);
	
			if ( !preg_match('/^[0-9]$/', $filter) && !in_array($filter, $filter_option) )
			{
				$errMsg .= '第'. ($i+1) .'条计划:滤光片参数超限!<br>';
			}

			//验证增益
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
			}
			
		}	//循环验证每条计划数据 结束///////

		if ($errMsg !== '' )	//若验证有错误
		{
			return $errMsg;
		}
        
        $this->msg = 8; $length =28 + 208;
	   
		$headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
        
        //接下来 给中控 发送数据 ///////////////
		for ( $i = 0; $i < $planNum; $i ++ )
		{
			$sendMsg = pack('L', $i+1);  //每条计划的tag unsigned int 
			
			$sendMsg .= pack('S', $this->at);
            $sendMsg .= pack('a48', $this->user); //user
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

			if ( preg_match('/^\d+$/', $filter) ) //数字类型
			{
				$sendMsg .= pack('a8', $filter_option[$filter]); 
			}else{//直接为字符类型数据
				$filter = strtoupper ($filter);
				$sendMsg .= pack('a8', $filter); 
			 }	//历元结束

			//增益 gain
			$gain = $postData['planData'][$i]['gain'];
			$sendMsg .= pack('S', $gain); 
			
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

		// 把计划数据写入表plandata中
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
		}

		return '观测计划发送完毕!';
    } //获取计划数据 验证并发送计划数据 结束////////////////////////////////////////
    
    //观测计划的 开始 停止 下一个 ////////////////////////////////
	protected function planOption ($postData)
	{	
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
	protected function get_plan ($at)
	{
		$plan_table = ''; //中控之 各望远镜的观测计划表

		switch ($this->at)
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

		try{
			//Db::table($plan_table)->where('user', $this->user)->order('id', 'desc')->field('tag, executing')->find();
			//如果此用户用正在执行的计划，
			//........
			//去表plandata中倒序获取第一条，以json格式返回给ajax
			//.............
			//$exetue = Db::table($plan_table)->where('user', $this->user)->order('id', 'desc')->field('tag')->find();
			$plan_data = Db::table('plandata')->where('atuser', $this->user)->where('at', $at)->order('id', 'desc')->field('plan')->find();
			if ($plan_data)
			{
				return $plan_data['plan'];
			}else{
				return '无正执行计划!';
			}
		}catch(\Exception $e){
			return '查询正在执行计划遇异常!';
		}


	}
	/****ajax 请求  是否有观测计划在执行 结束*******/
}