<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
//use think\Db;

/*此控制器 负责各望远镜的转台指令发送*/
class Gimbal extends Base
{
     //定义所需变量
     protected $sequence = 0;    //指令的序号
     protected $at = 0;  //望远镜序号
     protected $device = 64;  //转台对应序号
     protected $msg = 6;  //单指令 msg 
     protected $magic = 439041101;  //转台对应序号
     protected $version = 1;  //版本号
     protected $plan = 0;  //计划
     protected $user = 0;  //操作者
     protected $ip = '';  //中控通信 ip
	 protected $port = '';  //中控通信 port

    //接收参数，根据不同参数，向不同望远镜的转台指令
    public function sendCommand ()
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

        //根据不同参数 调用相应方法发送指令
        if ( ($connect = input('connect')) !== null ) //连接或断开指令
        {
            if (!($connect== 1 || $connect == 2)) //匹配1和2
			
			{
                return '连接/断开指令无效!'; 
            }
            
            //执行发送
            return $this->connect($connect);
        }else if( ($findHome=input('findHome')) !== null){//找零
            if ($findHome != 1)
			{
                return '找零指令无效!';  
            }
            
            //执行发送
            return $this->findHome($findHome);
        }else if( ($park=input('park')) !== null){//复位
            if ($park != 1)
			{
                return '复位指令无效!';  
            }
            
            //执行发送
            return $this->park();
        }else if( ($stop=input('stop')) !== null){//停止
            if ($stop != 1)
			{
                return '停止指令无效!';  
            }
            
            //执行发送
            return $this->stop();
        }else if( ($EmergStop=input('EmergenceStop')) !== null){//急停
            if ($EmergStop != 1)
			{
                return '急停指令无效!';  
            }
            
            //执行发送
            return $this->emergStop();
        }else if( input('command') == 1 ){//跟踪恒星指令            
            //执行发送
            return $this->trackStar();
        }


        


    }//接收参数，根据不同参数，向不同望远镜的转台指令 结束/////

    //连接 断开 指令
    protected function connect ($connect)
    {
        $length = 48 + 2;
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=1);
        
        $sendMsg = pack('S', $connect);  //unsigned short
        
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        if ($connect == 1)
        {
            return '连接指令：'. udpSend($sendMsg, $this->ip, $this->port);
        }elseif ($connect == 2)
        {
            return '断开连接指令：'. udpSend($sendMsg, $this->ip, $this->port);
        }
    }//连接 断开 指令 结束


    //找零 指令
    protected function findHome ($findHome)
    {
        $length = 48 + 2;    //该结构体总长度
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=2);
        $sendMsg = pack('S', $findHome);  //unsigned short
        
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '找零指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//找零 指令 结束
    
    //复位 指令
    protected function park ()
    {
        $length = 48;    //该结构体总长度
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=10);
        
        //socket发送数据
        $sendMsg = $headInfo;
        return '复位指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//park 复位 指令 结束

    //停止 指令
    protected function stop ()
    {
        $length = 48;    //该结构体总长度
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=8);
        
        //socket发送数据
        $sendMsg = $headInfo;
        return '停止指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//停止 指令 结束
    
    //急停 指令
    protected function emergStop ()
    {
        $length = 48;    //该结构体总长度
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=15);
        
        //socket发送数据
        $sendMsg = $headInfo;
        return '急停指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//急停 指令 结束

    //跟踪恒星 指令
    protected function trackStar ()
    {
        $length = 48 + 20;    //该结构体总长度        
        //处理赤经数据
        $postData = input();
    
        // if (!is_numeric($postData['rightAscension1']) || $postData['rightAscension1'] > 24 || $postData['rightAscension1'] < 0)
        // {
        // 	return '赤经之小时参数超限!';
        // }
        
        // if (!preg_match('/^\d{1,2}$/', $postData['rightAscension2']) || $postData['rightAscension2'] > 59 || $postData['rightAscension2'] < 0)
        // {
        // 	return '赤经之分钟参数超限!';
        // }
        
        // if (!is_numeric($postData['rightAscension3']) || $postData['rightAscension3'] >= 60 || $postData['rightAscension3'] < 0)
        // {
        // 	return '赤经之秒参数超限!';
        // }
        $rightAscension = $postData['rightAscension1'].':'.$postData['rightAscension2'].':'.$postData['rightAscension3'];
        
        $rightAscension = time2Data($rightAscension);
        
        if ($rightAscension > 24 || $rightAscension < 0) //赤经
        {
            return '赤经参数超限!';
        }else{
            $sendMsg = pack('d', $rightAscension);     //double64
        }
        
        //处理赤纬数据
        // if (!is_numeric($postData['declination1']) || $postData['declination1'] > 90 || $postData['declination1'] < -90)
        // {
        // 	return '赤纬之小时参数超限!';
        // }
        
        // if (!preg_match('/^\d{1,2}$/', $postData['declination2']) || $postData['declination2'] > 59 || $postData['declination2'] < 0)
        // {
        // 	return '赤纬之分钟参数超限!';
        // }
        
        // if (!is_numeric($postData['declination3']) || $postData['declination3'] >= 60 || $postData['declination3'] < 0)
        // {
        // 	return '赤纬之秒参数超限!';
        // }
        
        $declination = $postData['declination1'].':'.$postData['declination2'].':'.$postData['declination3'];
        
        $declination = time2Data($declination);
        
        if ($declination > 90 || $declination < -90) //赤纬
        {
            return '赤纬参数超限!';
        }else{
            $sendMsg .= pack('d', $declination);     //double64
        }
            
        if (($epoch=input('epoch')) !== '') //历元
        {  
            if (!preg_match('/^\d{1}$/', $epoch))
            {
                return '历元参数超限！';
            }
            $sendMsg .= pack('S', $epoch);     //unsigned short
        }
            
        if (($speed=input('speed')) !== '')  //跟踪类型
        {      
            if (!preg_match('/^\d{1}$/', $speed))
            {
                return '跟踪类型参数超限！';
            }
            $sendMsg .= pack('S', $speed);     //unsigned short
        }
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=3);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '跟踪恒星指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//跟踪恒星 指令 结束

}