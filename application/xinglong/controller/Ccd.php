<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
//use think\Db;

/*此控制器 负责各望远镜的ccd指令发送*/
class Ccd extends Base
{
    //定义所需变量
    protected $sequence = 0;    //指令的序号
    protected $at = 0;  //望远镜序号
    protected $device = 65;  //转台对应序号
    protected $msg = 6;  //单指令 msg 
    protected $magic = 439041101;  //转台对应序号
    protected $version = 1;  //版本号
    protected $plan = 0;  //计划
    protected $user = 0;  //操作者
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port
    
    //接收参数，根据不同参数，向不同望远镜的CCD指令
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

        $command = input('command'); //获取提交的指令
        //根据不同参数 调用相应方法发送指令
        if ( ($ccdConnect=input('ccdConnect')) !== null ) //连接或断开指令
        {
            if (!($ccdConnect == 1 || $ccdConnect == 2))
			{
                return 'ccd连接指令无效!';
			}
         
            return $this->connect($ccdConnect);   //执行发送
        }else if( input('StopExpose') == 1){//停止曝光       
            return $this->stop_expose(); //执行发送
        }else if( input('AbortExpose') == 1){//终止曝光       
            return $this->abort_expose(); //执行发送
        }else if( $command == 1 ){//设置制冷温度       
            return $this->set_cool(); //执行发送
        }else if( $command == 2 ){//设置曝光策略       
            return $this->expose_strategy(); //执行发送
        }else if( $command == 3 ){//开始曝光       
            return $this->start_expose(); //执行发送
        }else if( $command == 4 ){//设置增益     
            return $this->set_gain(); //执行发送
        }
    }//接收参数，根据不同参数，向不同望远镜的CCD指令 结束////////

    /*ccd连接或断开*/
    protected function connect ($connect)
    {
        $length = 48 + 2;     //该结构体总长度

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=1);

        $sendMsg = pack('S', $connect);  //unsigned short
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        if ($connect == 1)
        {
            return 'ccd连接指令：' .udpSend($sendMsg, $this->ip, $this->port); 
        }elseif ($connect == 2)
        {
            return '断开ccd指令：' .udpSend($sendMsg, $this->ip, $this->port); 
        }
    }/*ccd连接或断开 结束*/

    /*停止曝光*/
    protected function stop_expose ()
    {
        $length = 48;     //该结构体总长度

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=5);

        //socket发送数据
        $sendMsg = $headInfo;
        return '停止曝光：' .udpSend($sendMsg, $this->ip, $this->port); 
    }/*停止曝光 结束*/

     /*终止曝光*/
     protected function abort_expose ()
     {
         $length = 48;     //该结构体总长度
 
         $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
         $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=6);
 
         //socket发送数据
         $sendMsg = $headInfo;
         return '终止曝光：' .udpSend($sendMsg, $this->ip, $this->port); 
     }/*终止曝光 结束*/

     /*设置制冷温度*/
     protected function set_cool ()
     {
        $length = 48 +8;      //该结构体总长度
 
        //socket发送数据
        $temperature = input('temperature');
        if (!is_numeric($temperature) || $temperature >20 || $temperature < -80)
        {
            return '制冷温度参数超限！';
        }
        $sendMsg = pack('d', $temperature);     //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=2);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '制冷温度指令：' .udpSend($sendMsg, $this->ip, $this->port);	 
     }/*设置制冷温度 结束*/

    /*设置曝光策略*/
    protected function expose_strategy ()
    {
        $length = 48 + 264;      //该结构体总长度
        
        if (($validFlag=input('validFlag')) !== '')    //数据有效标志位
        {
            if (!preg_match('/^\d{1,5}$/', $validFlag))
            {
                echo '数据有效标志位只能是数字！'; return;
            }
            $sendMsg = pack('d', $validFlag); //原来为Q格式
        }else{
            $sendMsg = pack('d', 0); //unsigned long long
        }
        
        if (($startTime=input('startTime')) !== '')      //起始时刻
        {
            if (!preg_match('/^\d{1,10}$/', $startTime))
            {
                echo '起始时刻只能是数字！'; return;
            }
            $sendMsg .= pack('L', $startTime);     
        }else{
            $sendMsg .= pack('L', 0);   //unsigned int
        }
        
        if (($duration=input('duration')) !== '')   //曝光时间
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $duration))
            {
                echo '曝光时间只能是数字！'; return;
            }
            $sendMsg .= pack('d', $duration);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (($delay=input('delay')) !== '')   //延迟时间
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $delay))
            {
                echo '延迟时间只能是数字！'; return;
            }
            $sendMsg .= pack('d', $delay);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( ($postData['objectName']=input('objectName')) !== '')  //拍摄目标
        {
            if (preg_match('/[\x{4e00}-\x{9af5} ]/u', $postData['objectName']))
            {
                return '目标名称不能含汉字或空格！';
            }
            $sendMsg .= pack('a48', $postData['objectName']);  //objectName uint8-48
        }else{
            $sendMsg .= pack('a48', '0');
        }
        
        if ( ($postData['objectType']=input('objectType')) !== '')    //拍摄目标类型
        {
            if (!preg_match('/^\d{1}$/', $postData['objectType']))
            {
                return '目标类型参数超限！';
            }
            $sendMsg .= pack('S', $postData['objectType']); 
        }else{
            $sendMsg .= pack('S', 0);  //unsigned short
        }
        
        //接收表单数据
        $postData = input();
        //处理赤经数据			
        $objectRightAscension = $postData['objectRightAscension1'].':'.$postData['objectRightAscension2'].':'.$postData['objectRightAscension3'];
        
        if ($objectRightAscension !== '::')  
        {//拍摄目标赤经
            // if (!preg_match('/^\d{1,2}$/', $postData['objectRightAscension1']) || $postData['objectRightAscension1'] > 24 || $postData['objectRightAscension1'] < 0)
            // {
            // 	return '曝光策略:赤经之小时参数超限!';
            // }
            
            // if (!preg_match('/^\d{1,2}$/', $postData['objectRightAscension2']) || $postData['objectRightAscension2'] > 59 || $postData['objectRightAscension2'] < 0)
            // {
            // 	return '曝光策略:赤经之分钟参数超限!';
            // }
            
            // if (!is_numeric($postData['objectRightAscension3']) || $postData['objectRightAscension3'] >= 60 || $postData['objectRightAscension3'] < 0)
            // {
            // 	return '曝光策略:赤经之秒参数超限!';
            // }
            
            $objectRightAscension = time2Data($objectRightAscension);
            
            if ($objectRightAscension > 24 || $objectRightAscension < 0) //赤经
            {
                return '曝光策略:赤经参数超限!';
            }else{
                $sendMsg .= pack('d', $objectRightAscension);  //double64
            }
            
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        //处理赤纬数据
        $objectDeclination = $postData['objectDeclination1'].':'.$postData['objectDeclination2'].':'.$postData['objectDeclination3'];
        
        if ($objectDeclination !== '::')    
        {//当前拍摄目标赤纬
            // if (!preg_match('/^\d{1,2}$/', $postData['objectDeclination1']) || $postData['objectDeclination1'] > 90 || $postData['objectDeclination1'] < -90)
            // {
            // 	return '曝光策略:赤纬之小时参数超限!';
            // }
            
            // if (!preg_match('/^\d{1,2}$/', $postData['objectDeclination2']) || $postData['objectDeclination2'] > 59 || $postData['objectDeclination2'] < 0)
            // {
            // 	return '曝光策略:赤纬之分钟参数超限!';
            // }
            
            // if (!is_numeric($postData['objectDeclination3']) || $postData['objectDeclination3'] >= 60 || $postData['objectDeclination3'] < 0)
            // {
            // 	return '曝光策略:赤纬之秒参数超限!';
            // }

            $objectDeclination = time2Data($objectDeclination);
            if ($objectDeclination > 90 || $objectDeclination < -90)
            { //赤纬
                return '曝光策略:赤纬参数超限!';
            }else{
                $sendMsg .= pack('d', $objectDeclination);//double64
            }
            
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (($objectEpoch=input('objectEpoch')) !== '')    //拍摄目标历元
        {
            if (!preg_match('/^\d{1}$/', $objectEpoch))
            {
                return '目标历元只能是数字！';
            }
            $sendMsg .= pack('S', $objectEpoch);   
        }else{
            $sendMsg .= pack('S', 0);   //unsigned short
        }
        
        if (($objectBand = input('objectBand')) !== '')      //拍摄波段
        {  
            if (!preg_match('/^[a-zA-Z0-9_-]{1,8}$/', $objectBand))
            {
                return '当前拍摄波段只能是8位字母数字！';
            }
            $sendMsg .= pack('a8', $objectBand);     //uint8-8
        }else{
            $sendMsg .= pack('a8', '0');
        }
        
        if (($objectFilter=input('objectFilter')) !== '')  //拍摄波段滤光片系统
        {
            if (!preg_match('/^\d{1,5}$/', $objectFilter))
            {
                return '当前拍摄波段只能是数字！';
            }
            $sendMsg .= pack('S', $objectFilter);     //uint16
        }else{
            $sendMsg .= pack('S', 0);
        }
        
        if (($isSaveImage=input('isSaveImage')) !== '')  //是否保存图像
        {
            if (!preg_match('/^\d{1,5}$/', $isSaveImage))
            {
                return '是否保存图像只能是数字！';
            }
            $sendMsg .= pack('S', $isSaveImage);     
        }else{
            $sendMsg .= pack('S', 0);   //uint16
        }
        
        if (($weatherGatherTime=input('weatherGatherTime')) !== '')   
        {//气象数据采集时间
            if (!preg_match('/^\d{1,10}$/', $weatherGatherTime))
            {
                return '象数据采集时间只能是数字！';
            }
            $sendMsg .= pack('L', $weatherGatherTime);     //uint32
        }else{
            $sendMsg .= pack('L', 0); //unsigned int
        }
        
        if (($temperature1=input('temperature1')) !== '')    //温度
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $temperature1))
            {
                return '温度只能是数字！';
            }
            $sendMsg .= pack('d', $temperature1);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (($humidity=input('humidity')) !== '')    //湿度
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $humidity))
            {
                return '湿度只能是数字！';
            }
            $sendMsg .= pack('d', $humidity);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (($windSpeed=input('windSpeed')) !== '')  //风速
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $windSpeed))
            {
                return '风速只能是数字！';
            }
            $sendMsg .= pack('d', $windSpeed);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (($pressure=input('pressure')) !== '')      //气压
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $pressure))
            {
                return '气压只能是数字！';
            }
            $sendMsg .= pack('d', $pressure);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (($skyGatherTime=input('skyGatherTime')) !== '')   //天气状态采集时间
        {
            if (!preg_match('/^\d{1,10}$/', $skyGatherTime))
            {
                return '气象数据采集时间只能是数字！';
            }
            $sendMsg .= pack('L', $skyGatherTime);     //uint32
        }else{
            $sendMsg .= pack('L', 0);   //unsigned int
        }
        
        if (($skyState=input('skyState')) !== '')  //天气状态
        {
            if (!preg_match('/^\d{1,5}$/', $skyState))
            {
                return '天气状态只能是数字！';
            }
            $sendMsg .= pack('S', $skyState);     
        }else{
            $sendMsg .= pack('S', 0);   //unsigned short
        }
        
        if (($clouds=input('clouds')) !== '')      //云量
        {
            if (!preg_match('/^\d{1,5}$/', $clouds))
            {
                return '云量只能是数字！';
            }
            $sendMsg .= pack('S', $clouds);  
        }else{
            $sendMsg .= pack('S', 0); //unsigned short
        }
        
        if (($seeingGatherTime=input('seeingGatherTime')) !== '')   //视宁度采集时间
        {
            if (!preg_match('/^\d{1,10}$/', $seeingGatherTime))
            {
                return '视宁度采集时间只能是数字！';
            }
            $sendMsg .= pack('L', $seeingGatherTime);     //uint32
        }else{
            $sendMsg .= pack('L', 0); //unsigned int
        }
        
        if (($seeing=input('seeing')) !== '')   //视宁度
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $seeing))
            {
                return '视宁度只能是数字！';
            }
            $sendMsg .= pack('d', $seeing);     //double
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (input('dustGatherTime') !== '')      //粉尘采集时间
        {
            $dustGatherTime = input('dustGatherTime');
            if (!preg_match('/^\d{1,10}$/', $dustGatherTime))
            {
                return '粉尘采集时间只能是数字！';
            }
            $sendMsg .= pack('L', $dustGatherTime);     //uint32
        }else{
            $sendMsg .= pack('L', 0);
        }
        
        if (input('dust') !== '')      //粉尘
        {
            $dust = input('dust');
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $dust))
            {
                return '粉尘数据只能是数字！';
            }
            $sendMsg .= pack('d', $dust);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (input('AMS') !== '')      //AMS
        {
            $AMS = input('AMS');
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $AMS))
            {
                return 'AMS数据只能是数字！';
            }
            $sendMsg .= pack('d', $AMS);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (($extinctionGatherTime=input('extinctionGatherTime')) !== '')    //消光系数采集时间
        {
            if (!preg_match('/^\d{1,10}$/', $extinctionGatherTime))
            {
                return '消光系数采集时间只能是数字！';
            }
            $sendMsg .= pack('L', $extinctionGatherTime);     
        }else{
            $sendMsg .= pack('L', 0); //unsigned int
        }
        
        if (($rightAscension=input('rightAscension')) !== '')    //赤经
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $rightAscension))
            {
                return '赤经只能是数字！';
            }
            $sendMsg .= pack('d', $rightAscension);     
        }else{
            $sendMsg .= pack('d', 0); //double64
        }
        
        if (($declination = input('declination')) !== '')      //赤纬
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $declination))
            {
                return '赤纬只能是数字！';
            }
            $sendMsg .= pack('d', $declination);     
        }else{
            $sendMsg .= pack('d', 0); //double64
        }
        
         if (($band = input('band')) !== '')      //波段
        {
            if (!preg_match('/^[a-zA-Z0-9]{1,8}$/', $band))
            {
                return '波段只能是数字！';
            }
            $sendMsg .= pack('a8', $band);     //band  uint8-8
        }else{
            $sendMsg .= pack('a8', '0');
        }
        
        if (($extinctionFactor1 = input('extinctionFactor1')) !== '')      
        {//消光系数1
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $extinctionFactor1))
            {
                return '消光系数1只能是数字！';
            }
            $sendMsg .= pack('d', $extinctionFactor1);     
        }else{
            $sendMsg .= pack('d', 0); //double64
        }
        
        if (($extinctionFactor2 = input('extinctionFactor2')) !== '')      //消光系数2
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $extinctionFactor2))
            {
                return '消光系数2只能是数字！';
            }
            $sendMsg .= pack('d', $extinctionFactor2);     
        }else{
            $sendMsg .= pack('d', 0);//double64
        }
        
        if ( ($extinctionFactor3 = input('extinctionFactor3')) !== '')      //消光系数3
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $extinctionFactor3))
            {
                return '消光系数3只能是数字！';
            }
            $sendMsg .= pack('d', $extinctionFactor3);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (($telescopeRightAscension = input('telescopeRightAscension')) !== '')      //望远镜赤经
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $telescopeRightAscension))
            {
                return '望远镜赤经只能是数字！';
            }
            $sendMsg .= pack('d', $telescopeRightAscension);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if (($telescopeDeclination = input('telescopeDeclination')) !== '')      //望远镜赤纬
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $telescopeDeclination))
            {
                return '望远镜赤纬只能是数字！';
            }
            $sendMsg .= pack('d', $telescopeDeclination);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( ($focusLength = input('focusLength')) !== '' )      //焦距
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $focusLength))
            {
                return '焦距数据只能是数字！';
            }
            $sendMsg .= pack('d', $focusLength);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( ($frameNum = input('frameNum')) !== '' )      //帧数
        {
            if (!preg_match('/^\d{1,10}$/', $frameNum))
            {
                return '帧数只能是数字！';
            }
            $sendMsg .= pack('L', $frameNum);     //uint32
        }else{
            $sendMsg .= pack('L', 0);
        }
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=3);
        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return '设置曝光策略指令：' .udpSend($sendMsg, $this->ip, $this->port);	 
    }/*设置曝光策略 结束*/

    /*开始曝光*/
    protected function start_expose ()
    {
        $length = 48 +6;

        if ( ($isReadFrameSeq = input('isReadFrameSeq')) !== '' )      //是否读取帧序号
        {
            if (!preg_match('/^\d{1}$/', $isReadFrameSeq))
            {
                return '是否读取帧序号只能是数字！';
            }
            $sendMsg = pack('S', $isReadFrameSeq);    //unsigned short
        }else{
            $sendMsg = pack('S', 0);
        }
        
        if ( ($frameSequence = input('frameSequence')) !== '' )      //帧序号
        {
            if (!preg_match('/^\d{1,10}$/', $frameSequence))
            {
                return '起始时间只能是数字！';
            }
            $sendMsg .= pack('L', $frameSequence);    //unsigned int
        }else{
            $sendMsg .= pack('L', 0);
        }

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=4);
        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return '开始曝光指令：'. udpSend($sendMsg, $this->ip, $this->port);	
    }/*开始曝光 结束*/

    /*设置增益*/
    protected function set_gain ()
    {
        $length = 48 +4;

        if ( ($mode = input('mode')) !== '' )      //增益模式
        {
            if (!preg_match('/^\d{1,5}$/', $mode))
            {
                return '增益模式只能是数字！';
            }
            $sendMsg = pack('S', $mode);     //unsigned short
        }else{
            $sendMsg = pack('S', 0);
        }
        
        if ( ($gear = input('gear')) !== '' )      //增益档位
        {
            if (!preg_match('/^\d{1,5}$/', $gear))
            {
                return '增益模式只能是数字！';
            }
            $sendMsg .= pack('S', $gear);     //unsigned short
        }else{
            $sendMsg .= pack('S', 0);
        }

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=7);
        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        echo '设置增益指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*设置增益 结束*/
}