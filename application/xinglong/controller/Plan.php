<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
//use think\Db;

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

        //根据望远镜编号，给 $this->$at赋值
        if ($postData['at']== 60)
        {
            $this->at = 37;
        }else if ($postData['at']== 80){
            $this->at = 36;
        }else if ($postData['at']== 50){
            $this->at = 38;
        }else if ($postData['at']== 85){
            $this->at = 35;
        }else if ($postData['at']== 100){
            $this->at = 34;
        }

        //$command = input('command'); //获取提交的指令
        //根据不同参数 调用相应方法
        if ( $postData['command'] == 1 ) //验证计划的数据，并发送技划数据
        {      
            return $this->sendPlan();   //执行发送
        }else if( $postData['command'] == 2 ){//根据计划执行方式，开始执行计划       
            return $this->planOption(); //执行发送
        }

    }//观测计划 根据提交的参数 调用函数 结束
    
    //获取计划数据 验证并发送计划数据/////////////////////////////
	protected function sendPlan ()  //即原来的savePlan函数
	{		
		//获取计划数据
		if (!($planData = input()))
		{
			return '网络异常,请重新提交计划!';
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
                  
		//return $planData['planData'][0]['type'];
		//验证计划数据
		$planNum = count($planData['planData']);
		$errMsg = '';	//错误提示

		//循环验证每条计划数据
		for ($i = 0; $i < $planNum; $i ++)
		{
			//验证目标
			$target = $planData['planData'][$i]['target'];
			if ($target === '')
			{
				$errMsg .= '第'. ($i+1) .'条计划:目标名称参数超限!<br>';
			}

			//验证目标类型
			$type = $planData['planData'][$i]['type'];
			
			
			if( $type === '' || !(preg_match('/^[0-9]{1}$/', $type) || in_array($type, ['恒星','太阳','月亮','彗星','行星','卫星','固定位置','本底','暗流','平场'])) )
			{
				$errMsg .= '第'. ($i+1) .'条计划:目标类型参数超限!<br>';
			}

			//处理赤经数据
			$rightAscension = $planData['planData'][$i]['rightAscension1']. ':' .$planData['planData'][$i]['rightAscension2'] .':'. $planData['planData'][$i]['rightAscension3'];
			
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
			$declination = $planData['planData'][$i]['declination1']. ':' .$planData['planData'][$i]['declination2']. ':' .$planData['planData'][$i]['declination3'];
			
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
			$epoch = $planData['planData'][$i]['epoch'];
			if($epoch === '') //历元 未填写
			{
				$errMsg .= '第'. ($i+1) .'条计划:历元参数超限!<br>';
			}else{ //历元 有值
				$epoch = strtolower ($epoch);
				if ( !( preg_match('/^[0-3]{1}$/', $epoch) || in_array($epoch, ['real','j2000','b1950','j2050']) ) )
				{
					$errMsg .= '第'. ($i+1) .'条计划:历元参数超限!<br>';
				}
			}

			//验证 曝光时间
			$exposureTime = $planData['planData'][$i]['exposureTime'];
			if($exposureTime === '')
			{
				$errMsg .= '第'. ($i+1) .'条计划:曝光时间参数超限!<br>';
			}else{
				if( !is_numeric($exposureTime) ) //若不是数字
				{
					$errMsg .= '第'. ($i+1) .'条计划:曝光时间参数超限!<br>'; 
				}
			}

			//验证 delayTime
			$delayTime = $planData['planData'][$i]['delayTime'];

			if($delayTime === '') //delayTime 未输入时
			{
				$errMsg .= '第'. ($i+1) .'条计划:delayTime参数超限!<br>'; 
			}else{
				if( !is_numeric($delayTime) ) //delayTime 若不是数字
				{
					$errMsg .= '第'. ($i+1) .'条计划:delayTime参数超限!<br>';
				}
			}

			//验证 exposureCount
			$exposureCount = $planData['planData'][$i]['exposureCount'];

			if($exposureCount === '') //曝光数量 未输入
			{
				$errMsg .= '第'. ($i+1) .'条计划:曝光数量参数超限!<br>';
			}else{	//曝光数量 不是整数 或小于1
				if ( !preg_match('/^[1-9]{1,3}$/', $exposureCount) )
				{
					$errMsg .= '第'. ($i+1) .'条计划:曝光数量参数超限!<br>';
				}
			}

			//验证 滤光片 
			$filter = $planData['planData'][$i]['filter'];
			if($filter === '')//滤光片 未填写
			{
				$errMsg .= '第'. ($i+1) .'条计划:滤光片参数超限!<br>';
			}else{ //filter有值
				if ( !in_array($filter, ['U','V','B','R','I', 'u','b', 'v', 'r', 'i']) )
				{
					$errMsg .= '第'. ($i+1) .'条计划:滤光片参数超限!<br>';
				}
			}

			//验证增益
			$gain = $planData['planData'][$i]['gain'];
			if($gain === '')//增益 未填写
			{
				$errMsg .= '第'. ($i+1) .'条计划:增益参数超限!<br>';
			}else{
				if ( !preg_match('/^[0-9]{1}$/', $gain) )
				{
					$errMsg .= '第'. ($i+1) .'条计划:增益参数超限!<br>';
				}
			}

			//验证Bin 
			$bin = $planData['planData'][$i]['bin'];
			if($bin === '')//Bin 未填写
			{
				$errMsg .= '第'. ($i+1) .'条计划:bin参数超限!<br>';
			}else{
				if ( !preg_match('/^[0-9]{1}$/', $bin) )
				{
					$errMsg .= '第'. ($i+1) .'条计划:bin参数超限!<br>';
				}
			}

			//验证读出速度 
			$readout = $planData['planData'][$i]['readout'];
			if($readout === '')//读出速度 未填写
			{
				$errMsg .= '第'. ($i+1) .'条计划:读出速度参数超限!<br>';
			}else{
				if ( !preg_match('/^[1-9]{1}$/', $readout) )
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
			
			$sendMsg .= pack('S', $at);
            $sendMsg .= pack('a48', $user); //user
			$sendMsg .= pack('a48', '02'); //project
			
			$target = $planData['planData'][$i]['target']; //目标名		
			$sendMsg .= pack('a48', $target);
			
			//目标类型
			if (preg_match('/^[0-9]{1}$/', $type)) //数字类型
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
			$rightAscension = $planData['planData'][$i]['rightAscension1'].':'.$planData['planData'][$i]['rightAscension2'].':'.$planData['planData'][$i]['rightAscension3'];			
			$rightAscension = time2Data($rightAscension);
			$sendMsg .= pack('d', $rightAscension);
			//赤纬
			$declination = $planData['planData'][$i]['declination1'].':'.$planData['planData'][$i]['declination2'].':'.$planData['planData'][$i]['declination3'];
			$declination = time2Data($declination);
			$sendMsg .= pack('d', $declination);
			//历元
			$epoch = $planData['planData'][$i]['epoch'];
		
			if ( preg_match('/^[0-9]{1}$/', $epoch) ) //数字类型
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
			$exposureTime = $planData['planData'][$i]['exposureTime'];
			$sendMsg .= pack('d', $exposureTime); 

			//delayTime
			 $delayTime = $planData['planData'][$i]['delayTime'];
			 $sendMsg .= pack('d', $delayTime);

			 //曝光数量
			 $exposureCount = $planData['planData'][$i]['exposureCount'];
			 $sendMsg .= pack('L', $exposureCount);

			 //滤光片
			 $filter = trim($planData['planData'][$i]['filter']);
			 $filter = strtoupper ($filter);
			 $sendMsg .= pack('a8', $filter); 

			//增益 gain
			$gain = $planData['planData'][$i]['gain'];
			$sendMsg .= pack('S', $gain); 
			
			//bin
			$bin = $planData['planData'][$i]['bin'];
			$sendMsg .= pack('S', $bin);
			
			//读出速度
			$readout = $planData['planData'][$i]['readout'];
			$sendMsg .= pack('S', $readout);
			
			//发送 计划数据
        	$sendMsg = $headInfo . $sendMsg;
           
            udpSendPlan($sendMsg, $this->ip, $this->port); //无返回值
		}//给中控 发送数据 结束///////////////
		//在此 把计划数据写入cache
		
		return '观测计划发送完毕!';

    } //获取计划数据 验证并发送计划数据 结束////////////////////////////////////////
    
    //观测计划的 开始 停止 下一个 ////////////////////////////////
	protected function planOption ()
	{	
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
        
        $postData = input();    //获取 表单数据        
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
			echo '下一条计划:' .udpSend($sendMsg, $this->ip, $this->port);
		}
	}//观测计划的 开始 停止 下一个 结束////////////////////////////
}