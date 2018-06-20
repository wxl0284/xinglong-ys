<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
use think\Db;

/*此控制器 负责各望远镜的ccd指令发送*/
class Ccd extends Base
{
    //定义所需变量
    protected $sequence = 0;    //指令的序号
    protected $at = 0;  //望远镜序号
    protected $device = 65;  //ccd对应序号
    protected $msg = 6;  //单指令 msg 
    protected $magic = 439041101;  //转台对应序号
    protected $version = 1;  //版本号
    protected $plan = 0;  //计划
    protected $user = 0;  //操作者
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port
    protected $ccdNo = '';  //ccd 序号
    protected $command_length = [//各指令长度
        'connect' => 50,
        'stop_expose' => 48,
        'abort_expose' => 48,
        'set_cool' => 56,
        'start_expose' => 54,
        'expose_param' => 48,
        // 'trackStar' => 68,
        // 'set_obj_name' => 98,
        // 'slewAzEl' => 64,
        // 'slewDerotator' => 56,
        // 'axis3Mode' => 58,
        // 'speed_alter' => 58,
        // 'speed_fixed' => 58,
        // 'position_alter' => 58,
        // 'cover_op' => 50,
        // 'setFocusType' => 50,
        // 'save_sync_data' => 48,
    ];
    protected $operation = [//各指令之编号
        'connect' => 1,
        'stop_expose' => 5,
        'abort_expose' => 6,
        'set_cool' => 2,
        'start_expose' => 4,
        'expose_param' => 3,
        // 'set_obj_name' => 4,
        // 'slewAzEl' => 5,
        // 'slewDerotator' => 6,
        // 'axis3Mode' => 7,
        // 'speed_alter' => 9,
        // 'speed_fixed' => 11,
        // 'position_alter' => 12,
        // 'cover_op' => 13,
        // 'setFocusType' => 14,
        // 'save_sync_data' => 16,
    ];
    
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

        switch ($postData['at_aperture']) { //根据望远镜口径，给 $this->$at赋值
            case '50cm':
                $this->at = 38;
                break;
            case '60cm':
                $this->at = 37;
                break;
            case '80cm':
                $this->at = 36;
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
        $postData['ccdNo'] == '-1' ? $this->ccdNo = 1 : $this->ccdNo = $postData['ccdNo'];
        $command = $postData['command']; //要执行的指令
        //根据不同参数 调用相应方法发送指令
        switch ($command) {
            case 'connect':
                return $this->connect (1, 'connect');
                break;
            case 'disConnect':
                return $this->connect (2, 'connect');
                break;
            case 'stop_expose':
                return $this->button_command ('stop_expose');
                break;
            case 'abort_expose':
                return $this->button_command ('abort_expose');
                break;
            case 'set_cool':
                return $this->set_cool ($postData, 'set_cool');
                break;
            case 'start_expose':
                return $this->start_expose ($postData, 'start_expose');
                break;
            case 'expose_param':
                return $this->expose_strategy ($postData, 'expose_param');
                break;
            default:
                break;
        }

        // }else if( $command == 4 ){//设置增益     
        //     return $this->set_gain(); //执行发送
        // }else if( $command == 5 ){////设置 读出速度模式值     
        //     return $this->set_readSpeedMode(); //执行发送
        // }else if( $command == 6 ){////设置 转移速度模式值     
        //     return $this->set_transferSpeed(); //执行发送
        // }else if( $command == 7 ){//设置BIN     
        //     return $this->set_bin(); //执行发送
        // }else if( $command == 8 ){//设置ROI 指令    
        //     return $this->set_roi(); //执行发送
        // }else if( $command == 9 ){//设置快门指令 指令    
        //     return $this->set_shutter(); //执行发送
        // }else if( $command == 10 ){//设置帧转移 指令    
        //     return $this->set_isFullFrame(); //执行发送
        // }else if( $command == 11 ){//SetEM 指令    
        //     return $this->set_em(); //执行发送
        // }else if( $command == 12 ){//isNoiseFilter 指令    
        //     return $this->set_isNoiseFilter(); //执行发送
        // }else if( $command == 13 ){//SetBaseline 指令    
        //     return $this->set_baseLine(); //执行发送
        // }else if( $command == 14 ){//set over scan 指令    
        //     return $this->set_overScan(); //执行发送
        // }
    }//接收参数，根据不同参数，向不同望远镜的CCD指令 结束////////

    /*ccd连接或断开*/
    protected function connect ($connect, $param)
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = pack('S', $connect);  //unsigned short
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        if ($connect == 1)
        {
            return 'ccd连接指令：' .udpSend($sendMsg, $this->ip, $this->port); 
        }elseif ($connect == 2)
        {
            return 'ccd断开指令：' .udpSend($sendMsg, $this->ip, $this->port); 
        }
    }/*ccd连接或断开 结束*/

    
    protected function button_command ($param) /*停止曝光和终止曝光*/
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        //socket发送数据
        $sendMsg = $headInfo;
        if ( $param == 'stop_expose' )
        {
            return '停止曝光：'. udpSend($sendMsg, $this->ip, $this->port);
        }else if ( $param == 'abort_expose' ){
            return '终止曝光：'. udpSend($sendMsg, $this->ip, $this->port);
        }
    }/*停止曝光和终止曝光 结束*/

     
     protected function set_cool ($postData, $param)  /*设置制冷温度*/
     {
        $res = Db::table('ccdconf')->where('teleid', $postData['at'])->where('ccdno', $this->ccdNo)->field('lowcoolert')->find(); //获取该望远镜的ccdconf表中的:最低制冷温度

        if( !is_numeric($postData['temp']) || $postData['temp'] > 20 || $postData['temp'] < $res['lowcoolert'])
        {
            return '制冷温度参数超限！';
        }

        $sendMsg = pack('d', $postData['temp']);     //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '制冷温度指令：' .udpSend($sendMsg, $this->ip, $this->port);	 
     }/*设置制冷温度 结束*/

    /*设置曝光策略*/
    protected function expose_strategy ($postData, $param) /*设置曝光策略*/
    {        
        if ( $postData['validFlag'] !== '' )   //数据有效标志位
        {
            if (!preg_match('/^\d{1,5}$/', $postData['validFlag']))
            {
                return '数据有效标志位只能是数字！';
            }
            $sendMsg = pack('d', $postData['validFlag']); //原来为Q格式
        }else{
            $sendMsg = pack('d', 0); //unsigned long long
        }
        
        if ( $postData['startTime'] !== '' )      //起始时刻
        {
            if (!preg_match('/^\d{1,30}$/', $postData['startTime']))
            {
                return '起始时刻只能是数字！';
            }
            $sendMsg .= pack('L', $postData['startTime']);     
        }else{
            $sendMsg .= pack('L', 0);   //unsigned int
        }
        
        $res = Db::table('ccdconf')->where('ccdno', $this->ccdNo)->where('teleid', $postData['at'])->field('maxexposuretime, minexposuretime')->find();
        if ( !is_numeric($postData['duration']) || $postData['duration'] > $res['maxexposuretime'] || $postData['duration'] < $res['minexposuretime'])
        {
            return '曝光时间参数超限!';
        }

        $sendMsg .= pack('d', $postData['duration']);     //double64

        if ( $postData['delay'] !== '' )   //延迟时间
        {
            if ( !preg_match('/^-?\d+(\.\d{0,15})?$/', $postData['delay']) )
            {
                return '延迟时间参数超限！';
            }
            $sendMsg .= pack('d', $postData['delay']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['objName'] !== '' )  //拍摄目标
        {
            if (preg_match('/[\x{4e00}-\x{9af5} ]/u', $postData['objName']))
            {
                return '目标名称不能含汉字或空格！';
            }
            $sendMsg .= pack('a48', $postData['objName']);  //objectName uint8-48
        }else{
            $sendMsg .= pack('a48', '0');
        }
        
        $sendMsg .= pack('S', $postData['objType']);  //目标类型
			
        $objectRightAscension = $postData['objectRightAscension1'].':'.$postData['objectRightAscension2'].':'.$postData['objectRightAscension3'];
        
        if ($objectRightAscension !== '::')
        {//拍摄目标赤经
            if (!preg_match('/^\d{2}$/', $postData['objectRightAscension1']) || $postData['objectRightAscension1'] > 24 || $postData['objectRightAscension1'] < 0)
            {
            	return '曝光策略:赤经之小时参数超限!';
            }
            
            if (!preg_match('/^\d{2}$/', $postData['objectRightAscension2']) || $postData['objectRightAscension2'] > 59 || $postData['objectRightAscension2'] < 0)
            {
            	return '曝光策略:赤经之分钟参数超限!';
            }
            
            if (!is_numeric($postData['objectRightAscension3']) || $postData['objectRightAscension3'] >= 60 || $postData['objectRightAscension3'] < 0)
            {
            	return '曝光策略:赤经之秒参数超限!';
            }
            
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
            if (!preg_match('/^\d{2}$/', $postData['objectDeclination1']) || $postData['objectDeclination1'] > 90 || $postData['objectDeclination1'] < -90)
            {
            	return '曝光策略:赤纬之小时参数超限!';
            }
            
            if (!preg_match('/^\d{2}$/', $postData['objectDeclination2']) || $postData['objectDeclination2'] > 59 || $postData['objectDeclination2'] < 0)
            {
            	return '曝光策略:赤纬之分钟参数超限!';
            }
            
            if (!is_numeric($postData['objectDeclination3']) || $postData['objectDeclination3'] >= 60 || $postData['objectDeclination3'] < 0)
            {
            	return '曝光策略:赤纬之秒参数超限!';
            }

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

    protected function start_expose ($postData, $param)  /*开始曝光*/
    {
       
        $sendMsg = pack('S', $postData['isReadFrameSeq']);

        
        if ( $postData['frameSequence'] !== '' )    //帧序号
        {
            $sendMsg .= pack('L', $postData['frameSequence']);    //unsigned int
        }else{
            $sendMsg .= pack('L', 0);
        }

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
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
        return '设置增益指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*设置增益 结束*/

    /*设置 读出速度模式值*/
    protected function set_readSpeedMode ()
    {
        $length = 48 + 2;      //该结构体总长度
        $ReadSpeedMode = input('ReadSpeedMode');
        if (!preg_match('/^\d{1,10}$/', $ReadSpeedMode))
        {
            return '读出速度模式值只能是数字！';
        }
        $sendMsg = pack('S', $ReadSpeedMode);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=8);

        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return '设置读出速度模式指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*设置 读出速度模式值  结束*/

    /*设置 转移速度模式值*/
    protected function set_transferSpeed ()
    {
        $length = 48 + 2;      //该结构体总长度
        $SetTransferSpeed = input('SetTransferSpeed');
        if (!preg_match('/^\d{1,10}$/', $SetTransferSpeed))
        {
            return '转移速度模式值只能是数字！';
        }
        $sendMsg = pack('S', $SetTransferSpeed);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=9);

        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return  '转移速度模式指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*设置 转移速度模式值  结束*/

    /*设置Bin*/
    protected function set_bin ()
    {
        $length = 48 + 8;      //该结构体总长度

        if (input('BinX') !== '')      //binx
        {
            $BinX = input('BinX');
            if (!preg_match('/^\d{1,10}$/', $BinX))
            {
                return 'BinX值只能是数字！';
            }
            $sendMsg = pack('L', $BinX);     //uint32
        }else{
            $sendMsg = pack('L', 0);
        }
        
        if (input('BinY') !== '')      //BinY
        {
            $BinY = input('BinY');
            if (!preg_match('/^\d{1,10}$/', $BinY))
            {
                return 'BinY值只能是数字！';
            }
            $sendMsg .= pack('L', $BinY);     //uint32
        }else{
            $sendMsg .= pack('L', 0);
        }

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=10);

        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return  '设置BIN指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*设置Bin 结束*/

    /*设置Roi*/
    protected function set_roi ()
    {
        $length = 48 + 16;      //该结构体总长度

        if ( ( $startX=input('startX') ) !== '' )      //startX
        {
            if (!preg_match('/^\d{1,10}$/', $startX))
            {
                return 'startX值只能是数字！';
            }
            $sendMsg = pack('L', $startX);     //unsigned int
        }else{
            $sendMsg = pack('L', 0);
        }
        
        if ( ( $startY=input('startY') ) !== '' )     //unsigned int
        {
            if (!preg_match('/^\d{1,10}$/', $startY))
            {
                return 'startY值只能是数字！';
            }
            $sendMsg .= pack('L', $startY);     //unsigned int
        }else{
            $sendMsg .= pack('L', 0);
        }
        
        if ( ( $imageWidth=input('imageWidth') ) !== '' )      //imageWidth
        {
            if (!preg_match('/^\d{1,10}$/', $imageWidth))
            {
                return 'imageWidth值只能是数字！';
            }
            $sendMsg .= pack('L', $imageWidth);     //unsigned int
        }else{
            $sendMsg .= pack('L', 0);
        }
        
        if ( ( $imageHeight=input('imageHeight') ) !== '' )  //imageWidth
        {
            if (!preg_match('/^\d{1,10}$/', $imageHeight))
            {
                return 'imageHeight值只能是数字！';
            }
            $sendMsg .= pack('L', $imageHeight);     //unsigned int
        }else{
            $sendMsg .= pack('L', 0);
        }

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=11);

        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return  '设置Roi指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*设置Roi 结束*/

    /*设置快门*/
    protected function set_shutter ()
    {
        $length = 48 + 2;      //该结构体总长度

        $shutter = input('shutter');
        if (!preg_match('/^\d{1,10}$/', $shutter))
        {
            return 'shutter值只能是数字！';
        }
        $sendMsg = pack('S', $shutter);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=12);

        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return  '设置快门指令：'. udpSend($sendMsg, $this->ip, $this->port);	
    }/*设置快门 结束*/

    /*设置帧转移*/
    protected function set_isFullFrame ()
    {
        $length = 48 + 2;      //该结构体总长度

        $isFullFrame = input('isFullFrame');
        if (!preg_match('/^\d{1,10}$/', $isFullFrame))
        {
            return 'isFullFrame值只能是数字！';
        }
        $sendMsg = pack('S', $isFullFrame);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=13);

        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return  '设置帧转移指令：'. udpSend($sendMsg, $this->ip, $this->port);	
    }/*设置帧转移 结束*/

    /*SetEM*/
    protected function set_em ()
    {
        $length = 48 + 6;      //该结构体总长度

        if ( ($isEM = input('isEM')) !== '' )      //isEM
        {
            if (!preg_match('/^\d{1,5}$/', $isEM))
            {
                return 'isEM值只能是数字！';
            }
            $sendMsg = pack('S', $isEM);     //unsigned short
        }else{
            $sendMsg = pack('S', 0);
        }
        
        if ( ($eMValue = input('eMValue')) !== '' )      //eMValue
        {
            if (!preg_match('/^\d{1,10}$/', $eMValue))
            {
                return 'eMValue值只能是数字！';
            }
            $sendMsg .= pack('L', $eMValue);     //unsigned int
        }else{
            $sendMsg .= pack('L', 0);
        }

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=14);

        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return  'SetEM指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*SetEM 结束*/

    /*isNoiseFilter*/
    protected function set_isNoiseFilter ()
    {
        $length = 48 + 2;      //该结构体总长度

        $isNoiseFilter = input('isNoiseFilter');
        if (!preg_match('/^\d{1,5}$/', $isNoiseFilter))
        {
            return 'isNoiseFilter值只能是数字！';
        }
        $sendMsg = pack('S', $isNoiseFilter);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=15);

        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return  'CMOS noise filter指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*isNoiseFilter 结束*/

    /*set_baseLine*/
    protected function set_baseLine ()
    {
         $length = 48 + 6;      //该结构体总长度
 
        if ( ($isBaseline = input('isBaseline')) !== '' )    //isBaseline
        {
            if (!preg_match('/^\d{1,5}$/', $isBaseline))
            {
                return 'isBaseline值只能是数字！';
            }
            $sendMsg = pack('S', $isBaseline);     //unsigned short
        }else{
            $sendMsg = pack('S', 0);
        }
        
        if ( ($baselineValue = input('baselineValue')) !== '' )   //baselineValue
        {
            if (!preg_match('/^\d{1,10}$/', $baselineValue))
            {
                return 'baseline值只能是数字！';
            }
            $sendMsg .= pack('L', $baselineValue);     //unsigned int
        }else{
            $sendMsg .= pack('L', 0);
        }
 
         $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
  
         $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=16);
 
         //socket发送数据        
         $sendMsg = $headInfo . $sendMsg;
         return  'Baseline指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*set_baseLine 结束*/

    /*set over scan*/
    protected function set_overScan ()
    {
        $length = 48 + 2;      //该结构体总长度

        $isOverScan = input('isOverScan');  //isOverScan
        if (!preg_match('/^\d{1,5}$/', $isOverScan))
        {
            return 'isOverScan值只能是数字！';
        }
        $sendMsg = pack('S', $isOverScan);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=17);

        //socket发送数据        
        $sendMsg = $headInfo . $sendMsg;
        return  'Over Scan指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*set over scan 结束*/
}