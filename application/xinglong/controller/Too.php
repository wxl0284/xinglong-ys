<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
use think\Db;
use think\Session;
use think\Cache;

/*此控制器 负责协同观测计划发送*/
class Too extends Base
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

    public function ToO ()  //显示协同计划页面
    {
        return $this->fetch();
    }

    public function send_ToO_plan ()  //发送协同计划数据给 中控
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
        
        $postData = input();

        $at60_data = []; //存储at60的计划数据
        $at80_data = []; //存储at80的计划数据

        foreach ($postData['planData'] as $k => $v) 
        {
            if ( $v['at'] == '60cm' ) //获取at60的计划数据
            {
                $at60_data[] = $v;
            }

            if ( $v['at'] == '80cm' ) //获取at80的计划数据
            {
                $at80_data[] = $v;
            }
        }

		//var_dump($at60_data); halt();
		$ip = config('ip');
        $port = config('port');
		//接下来开始给中控发送at60计划数据
		$this->msg = 8; $length =28 + 208; $this->at = 37; //37表示at60
		$this->ip = $ip['at60'];
		$this->port = $port['at60'];
	   
		$headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
        
        //接下来 给中控 发送数据 ///////////////
		foreach ( $at60_data as $k => $v )
		{
			$sendMsg = pack('L', $k+1);  //每条计划的tag unsigned int 
			
			$sendMsg .= pack('S', $this->at);
            $sendMsg .= pack('a48', $this->user); //user
			$sendMsg .= pack('a48', '02'); //project
			
			$target = $v['target']; //目标名		
			$sendMsg .= pack('a48', $target);
			
			$sendMsg .= pack('L', 0); //目标类型

			//赤经 rightAscension	
			$rightAscension = $v['rightAscension1'].':'.$v['rightAscension2'].':'.$v['rightAscension3'];			
			$rightAscension = time2Data($rightAscension);
			$sendMsg .= pack('d', $rightAscension);
			//赤纬
			$declination = $v['declination1'].':'.$v['declination2'].':'.$v['declination3'];
			$declination = time2Data($declination);
			$sendMsg .= pack('d', $declination);
			//历元
			$epoch = $v['epoch'];
		
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
			$exposureTime = $v['exposureTime'];
			$sendMsg .= pack('d', $exposureTime); 

			//delayTime
			 $delayTime = $v['delayTime'];
			 $sendMsg .= pack('d', $delayTime);

			 //曝光数量
			 $exposureCount = $v['exposureCount'];
			 $sendMsg .= pack('L', $exposureCount);

			 //滤光片
			$filter = $v['filter'];
			$sendMsg .= pack('a8', $filter);
			// if ( preg_match('/^\d+$/', $filter) ) //数字类型
			// {
			// 	$sendMsg .= pack('a8', $filter_option[$filter]); 
			// }else{//直接为字符类型数据
			// 	$filter = strtoupper ($filter);
			// 	$sendMsg .= pack('a8', $filter);
			//  }	//滤光片

			//增益 gain
			$gain = $v['gain'];
			$sendMsg .= pack('S', $gain); 
			
			//bin
			$bin = $v['bin'];
			$sendMsg .= pack('S', $bin);
			
			//读出速度
			$readout = $v['readout'];
			$sendMsg .= pack('S', $readout);
			
			//发送 计划数据
        	$sendMsg = $headInfo . $sendMsg;
           
            udpSendPlan($sendMsg, $this->ip, $this->port); //无返回值
        }//给中控 发送at60数据 结束///////////////
        
        //接下来 给中控 发送at80数据 ///////////////
		$this->at = 36;
		$this->ip = $ip['at80'];
		$this->port = $port['at80'];

		foreach ( $at80_data as $k => $v )
		{
			$sendMsg = pack('L', $k+1);  //每条计划的tag unsigned int 
			
			$sendMsg .= pack('S', $this->at);
            $sendMsg .= pack('a48', $this->user); //user
			$sendMsg .= pack('a48', '02'); //project
			
			$target = $v['target']; //目标名		
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
			$rightAscension = $v['rightAscension1'].':'.$v['rightAscension2'].':'.$v['rightAscension3'];			
			$rightAscension = time2Data($rightAscension);
			$sendMsg .= pack('d', $rightAscension);
			//赤纬
			$declination = $v['declination1'].':'.$v['declination2'].':'.$v['declination3'];
			$declination = time2Data($declination);
			$sendMsg .= pack('d', $declination);
			//历元
			$epoch = $v['epoch'];
		
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
			$exposureTime = $v['exposureTime'];
			$sendMsg .= pack('d', $exposureTime); 

			//delayTime
			 $delayTime = $v['delayTime'];
			 $sendMsg .= pack('d', $delayTime);

			 //曝光数量
			 $exposureCount = $v['exposureCount'];
			 $sendMsg .= pack('L', $exposureCount);

			 //滤光片
			$filter = $v['filter'];
			$sendMsg .= pack('a8', $filter);
			// if ( preg_match('/^\d+$/', $filter) ) //数字类型
			// {
			// 	$sendMsg .= pack('a8', $filter_option[$filter]); 
			// }else{//直接为字符类型数据
			// 	$filter = strtoupper ($filter);
			// 	$sendMsg .= pack('a8', $filter);
			//  }	//滤光片

			//增益 gain
			$gain = $v['gain'];
			$sendMsg .= pack('S', $gain); 
			
			//bin
			$bin = $v['bin'];
			$sendMsg .= pack('S', $bin);
			
			//读出速度
			$readout = $v['readout'];
			$sendMsg .= pack('S', $readout);
			
			//发送 计划数据
        	$sendMsg = $headInfo . $sendMsg;
           
            udpSendPlan($sendMsg, $this->ip, $this->port); //无返回值
		}//给中控 发送at80数据 结束///////////////

		return '协同观测计划发送完毕!';
    }//send_ToO_plan () 结束

    public function start_stop_ToO () //开始执行观测或停止
    {
		$ip = config('ip');
        $port = config('port');
        //分别给at60和at80发送开始指令
        
        if (Cookie::has('sequence'))
		{
			$this->sequence = Cookie::get('sequence');
			Cookie::set('sequence', $this->sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$this->sequence = 0;
		}
        
        $postData = input();
        $this->msg = 9; //指令类型
        $length =28 + 16; //数据长度
               
		if($postData['planOption'] == 'planStart')
		{	$this->at = 37; //at60
            $headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
			$sendMsg = pack('L', 1);
			$sendMsg .= pack('L', $postData['mode']);
			$sendMsg .= pack('L', $postData['start']);
			$sendMsg .= pack('L', 0);
			
			$sendMsg = $headInfo . $sendMsg;
			$this->ip = $ip['at60'];
			$this->port = $port['at60'];

            udpSend($sendMsg, $this->ip, $this->port);

            $this->at = 36; //at80
            $headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
			$sendMsg = $headInfo . $sendMsg;
			$this->ip = $ip['at80'];
			$this->port = $port['at80'];
            udpSend($sendMsg, $this->ip, $this->port);

            return '计划开始指令发送成功!';
		}elseif($postData['planOption'] == 'planStop'){
            $this->at = 37; //at60
            $headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
			$sendMsg = pack('L', 2);
			$sendMsg .= pack('L', $postData['mode']);
			$sendMsg .= pack('L', $postData['start']);
			$sendMsg .= pack('L', 0);
			
			$sendMsg = $headInfo . $sendMsg;
			$this->ip = $ip['at60'];
			$this->port = $port['at60'];
            udpSend($sendMsg, $this->ip, $this->port);

            $this->at = 37; //at60
            $headInfo = planPackHead($this->magic, $this->version, $this->msg, $length, $this->sequence, $this->at, $this->device);
			$sendMsg = $headInfo . $sendMsg;
			$this->ip = $ip['at80'];
			$this->port = $port['at80'];
            udpSend($sendMsg, $this->ip, $this->port);

            return '计划停止指令发送成功!';
		}
    } //start_stop_ToO () 结束
}