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
        'expose_param' => 312,
        'set_gain' => 52,
        'readout_speed' => 50,
        'transfer_speed' => 50,
        'set_bin' => 56,
        'set_roi' => 64,
        'set_shutter' => 50,
        'set_frame' => 50,
        'set_em' => 54,
        'set_cmos' => 50,
        'set_baseLine' => 54,
        'over_scan' => 50,
    ];
    protected $operation = [//各指令之编号
        'connect' => 1,
        'stop_expose' => 5,
        'abort_expose' => 6,
        'set_cool' => 2,
        'start_expose' => 4,
        'expose_param' => 3,
        'set_gain' => 7,
        'readout_speed' => 8,
        'transfer_speed' => 9,
        'set_bin' => 10,
        'set_roi' => 11,
        'set_shutter' => 12,
        'set_frame' => 13,
        'set_em' => 14,
        'set_cmos' => 15,
        'set_baseLine' => 16,
        'over_scan' => 17,
    ]; 
    
    public function sendCommand ()  //接收参数，根据不同参数，向不同望远镜的CCD指令
    {
        //首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

        //接受表单数据
        $postData = input ();   //halt($postData);
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
        $postData['ccdNo'] == '-1' ? $this->ccdNo = 1 : $this->ccdNo = $postData['ccdNo'];
        $command = $postData['command']; //要执行的指令
        //根据不同参数 调用相应方法发送指令
        switch ($command) {
            case 'connect':
                return $this->connect (1, 'connect');
            case 'disConnect':
                return $this->connect (2, 'connect');
            case 'stop_expose':
                return $this->button_command ('stop_expose');
            case 'abort_expose':
                return $this->button_command ('abort_expose');
            case 'set_cool':
                return $this->set_cool ($postData, 'set_cool');
            case 'start_expose':
                return $this->start_expose ($postData, 'start_expose');
            case 'expose_param':
                return $this->expose_strategy ($postData, 'expose_param');
            case 'set_gain':
                return $this->set_gain ($postData, 'set_gain');
            case 'readout_speed':
                return $this->set_readSpeedMode ($postData, 'readout_speed');
            case 'transfer_speed':
                return $this->set_transferSpeed ($postData, 'transfer_speed');
            case 'set_bin':
                return $this->set_bin ($postData, 'set_bin');
            case 'set_roi':
                return $this->set_roi ($postData, 'set_roi');
            case 'set_shutter':
                return $this->set_shutter ($postData, 'set_shutter');
            case 'set_frame':
                return $this->set_isFullFrame ($postData, 'set_frame');
            case 'set_em':
                return $this->set_em ($postData, 'set_em');
            case 'set_cmos':
                return $this->set_isNoiseFilter ($postData, 'set_cmos');
            case 'set_baseLine':
                return $this->set_baseLine ($postData, 'set_baseLine');
            case 'over_scan':
                return $this->set_overScan ($postData, 'over_scan');
            default:
                break;
        }
    }//接收参数，根据不同参数，向不同望远镜的CCD指令 结束////////

    protected function connect ($connect, $param)  /*ccd连接或断开*/
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = pack('S', $connect);  //unsigned short

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
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

        $sendMsg = $headInfo;  //socket发送数据
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

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '制冷温度指令：' .udpSend($sendMsg, $this->ip, $this->port);	 
    }/*设置制冷温度 结束*/

    protected function expose_strategy ($postData, $param) /*设置曝光策略*/
    {  // halt($postData['filter']);
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
        { $leng = strlen($postData['objName']);
            if ( preg_match('/[\x{4e00}-\x{9af5} ]/u', $postData['objName']) || $leng > 48 || $leng < 1)
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
            if (!preg_match('/^-?\d{2}$/', $postData['objectDeclination1']) || $postData['objectDeclination1'] > 90 || $postData['objectDeclination1'] < -90)
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
        
        if ( $postData['objectEpoch'] !== '' )    //拍摄目标历元
        {
            if ( !preg_match('/^\d{1}$/', $postData['objectEpoch'] ))
            {
                return '目标历元参数有误！';
            }
            $sendMsg .= pack('S', $postData['objectEpoch']);   
        }else{
            $sendMsg .= pack('S', 0);   //unsigned short
        }
        
        if ( $postData['objectBand'] !== '' )   //拍摄波段
        { 
            $length = strlen($postData['objectBand']);

            if ( !in_array($postData['objectBand'], $postData['filter']) )
            {
                return '拍摄波段有误！';
            }

            $sendMsg .= pack('a8', $postData['objectBand']);     //uint8-8
        }else{
            $sendMsg .= pack('a8', '0');
        }
        
        if ( $postData['objectFilter'] !== '' )  //拍摄波段滤光片系统
        {
            if (!preg_match('/^[0-3]$/', $postData['objectFilter']))
            {
                return '当前拍摄波段输入有误！';
            }
            $sendMsg .= pack('S', $postData['objectFilter']);     //uint16
        }else{
            $sendMsg .= pack('S', 0);
        }

        if ( $postData['isSaveImage'] !== '' )  //是否保存图像
        {
            if ( !($postData['isSaveImage'] == 1 || $postData['isSaveImage'] == 2) )
            {
                return '是否保存图像输入有误！';
            }
            $sendMsg .= pack('S', $postData['isSaveImage']);     
        }else{
            $sendMsg .= pack('S', 0);   //uint16
        }
        
        if ( $postData['weatherGatherTime'] !== '' )   
        {//气象数据采集时间
            // if (!preg_match('/^\d{1,10}$/', $weatherGatherTime))
            // {
            //     return '象数据采集时间只能是数字！';
            // }
            $sendMsg .= pack('L', $postData['weatherGatherTime']);     //uint32
        }else{
            $sendMsg .= pack('L', 0); //unsigned int
        }
        
        if ( $postData['weatherGatherTime'] !== '' )    //温度
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $postData['weatherGatherTime']))
            // {
            //     return '温度只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['weatherGatherTime']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['humidity'] !== '' )    //湿度
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $humidity))
            // {
            //     return '湿度只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['humidity']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['windSpeed'] !== '' )  //风速
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $postData['windSpeed'])
            // {
            //     return '风速只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['windSpeed']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['pressure'] !== '' )      //气压
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $pressure))
            // {
            //     return '气压只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['pressure']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['skyGatherTime'] !== '')   //天气状态采集时间
        {
            // if (!preg_match('/^\d{1,10}$/', $skyGatherTime))
            // {
            //     return '气象数据采集时间只能是数字！';
            // }
            $sendMsg .= pack('L', $postData['skyGatherTime']);     //uint32
        }else{
            $sendMsg .= pack('L', 0);   //unsigned int
        }
        
        if ( $postData['skyState'] !== '' )  //天气状态
        {
            // if (!preg_match('/^\d{1,5}$/', $skyState))
            // {
            //     return '天气状态只能是数字！';
            // }
            $sendMsg .= pack('S', $postData['skyState']);     
        }else{
            $sendMsg .= pack('S', 0);   //unsigned short
        }
        
        if ( $postData['clouds'] !== '' )      //云量
        {
            // if (!preg_match('/^\d{1,5}$/', $clouds))
            // {
            //     return '云量只能是数字！';
            // }
            $sendMsg .= pack('S', $postData['clouds']);  
        }else{
            $sendMsg .= pack('S', 0); //unsigned short
        }
        
        if ( $postData['seeingGatherTime'] !== '' )   //视宁度采集时间
        {
            // if (!preg_match('/^\d{1,10}$/', $seeingGatherTime))
            // {
            //     return '视宁度采集时间只能是数字！';
            // }
            $sendMsg .= pack('L', $postData['seeingGatherTime']);     //uint32
        }else{
            $sendMsg .= pack('L', 0); //unsigned int
        }
        
        if ( $postData['seeing'] !== '' )   //视宁度
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $seeing))
            // {
            //     return '视宁度只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['seeing']);     //double
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['dustGatherTime'] !== '' )      //粉尘采集时间
        {
            // $dustGatherTime = input('dustGatherTime');
            // if (!preg_match('/^\d{1,10}$/', $dustGatherTime))
            // {
            //     return '粉尘采集时间只能是数字！';
            // }
            $sendMsg .= pack('L', $postData['dustGatherTime']);     //uint32
        }else{
            $sendMsg .= pack('L', 0);
        }
        
        if ( $postData['dust'] !== '')      //粉尘
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $dust))
            // {
            //     return '粉尘数据只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['dust']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['AMS'] !== '')      //AMS
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $AMS))
            // {
            //     return 'AMS数据只能是数字！';
            // }
            $sendMsg .= pack('d',  $postData['AMS']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['extinctionGatherTime'] !== '' )    //消光系数采集时间
        {
            // if (!preg_match('/^\d{1,10}$/', $extinctionGatherTime))
            // {
            //     return '消光系数采集时间只能是数字！';
            // }
            $sendMsg .= pack('L', $postData['extinctionGatherTime']);    
        }else{
            $sendMsg .= pack('L', 0); //unsigned int
        }
        
        if ( $postData['rightAscension'] !== '')    //赤经
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $rightAscension))
            // {
            //     return '赤经只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['rightAscension']);     
        }else{
            $sendMsg .= pack('d', 0); //double64
        }
        
        if ( $postData['declination'] !== '')      //赤纬
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $declination))
            // {
            //     return '赤纬只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['declination']);     
        }else{
            $sendMsg .= pack('d', 0); //double64
        }
        
         if ( $postData['band'] !== '' )      //波段
        {
            // if (!preg_match('/^[a-zA-Z0-9]{1,8}$/', $band))
            // {
            //     return '波段只能是数字！';
            // }
            $sendMsg .= pack('a8', $postData['band']);     //band  uint8-8
        }else{
            $sendMsg .= pack('a8', '0');
        }
        
        if ( $postData['extinctionFactor1'] !== '')      
        {//消光系数1
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $postData['extinctionFactor1']))
            // {
            //     return '消光系数1只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['extinctionFactor1']);     
        }else{
            $sendMsg .= pack('d', 0); //double64
        }
        
        if ( $postData['extinctionFactor2'] !== '')      //消光系数2
        {
            $sendMsg .= pack('d', $postData['extinctionFactor2']);     
        }else{
            $sendMsg .= pack('d', 0);//double64
        }
        
        if ( $postData['extinctionFactor3'] !== '')      //消光系数3
        {
            $sendMsg .= pack('d', $postData['extinctionFactor3']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['telescopeRightAscension'] !== '')      //望远镜赤经
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $telescopeRightAscension))
            // {
            //     return '望远镜赤经只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['telescopeRightAscension']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['telescopeDeclination'] !== '')      //望远镜赤纬
        {
            $sendMsg .= pack('d', $postData['telescopeDeclination']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['focusLength'] !== '' )      //焦距
        {
            // if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $focusLength))
            // {
            //     return '焦距数据只能是数字！';
            // }
            $sendMsg .= pack('d', $postData['focusLength']);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        if ( $postData['frameNum'] !== '' )      //帧数
        {
            // if (!preg_match('/^\d{1,10}$/', $frameNum))
            // {
            //     return '帧数只能是数字！';
            // }
            $sendMsg .= pack('L', $postData['frameNum']);     //uint32
        }else{
            $sendMsg .= pack('L', 1);
        }
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
    
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
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
   
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '开始曝光指令：'. udpSend($sendMsg, $this->ip, $this->port);	
    }/*开始曝光 结束*/

    protected function set_gain ($postData, $param)  /*设置增益*/
    {
        $res = Db::table('ccdconf')->where('ccdno', $this->ccdNo)->where('teleid', $postData['at'])->field('gainmode, gainnumber')->find();

        if ( $postData['gear'] < 1 || $postData['gear'] > $res['gainnumber'] )
        {
            return '增益档位参数超限!';
        }
        $sendMsg = pack('S', $postData['mode']);
        $sendMsg .= pack('S', $postData['gear']);

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
   
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '设置增益指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*设置增益 结束*/

    protected function set_readSpeedMode ($postData, $param)  /*设置 读出速度模式值*/
    {
        $sendMsg = pack('S', $postData['readout_mode']);     //unsigned short
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
      
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '设置读出速度模式指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*设置 读出速度模式值  结束*/

    
    protected function set_transferSpeed ($postData, $param) /*设置 转移速度模式值*/
    {
        $sendMsg = pack('S', $postData['transfer_mode']);     //unsigned short
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
      
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '设置转移速度模式指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*设置 转移速度模式值  结束*/

    protected function set_bin ($postData, $param)  /*设置Bin*/
    {
        $res = Db::table('ccdconf')->where('teleid', $postData['at'])->where('ccdno', $this->ccdNo)->field('binarray')->find(); //获取该望远镜的ccdconf表中的:bin值

        if ( $postData['binX'] > $res['binarray'] || $postData['binY'] > $res['binarray'])
        {
            return 'bin值参数超限!';
        }
        
        $sendMsg = pack ('L2', $postData['binX'], $postData['binY']);
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
       
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return  '设置BIN指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*设置Bin 结束*/

    protected function set_roi ($postData, $param)  /*设置Roi*/
    {
        $res = Db::table('ccdconf')->where('teleid', $postData['at'])->where('ccdno', $this->ccdNo)->field('xpixel, ypixel')->find(); //获取该望远镜的ccdconf表中的:x y像素值

        if ( !preg_match('/^\d+$/', $postData['startX']) || $postData['startX'] < 0 || $postData['startX'] > $res['xpixel']-1 )
        {
            return 'x坐标参数超限!';
        }
        if ( !preg_match('/^\d+$/', $postData['startY']) || $postData['startY'] < 0 || $postData['startY'] > $res['ypixel']-1 )
        {
            return 'y坐标参数超限!';
        }
        if ( !preg_match('/^\d+$/', $postData['imageW']) || $postData['imageW'] < 1 || ($postData['imageW']+$postData['startX']) > $res['xpixel'] )
        {
            return 'Width参数超限!';
        }
        if ( !preg_match('/^\d+$/', $postData['imageH']) || $postData['imageH'] < 1 || ($postData['imageH']+$postData['startY']) > $res['ypixel'] )
        {
            return 'Height参数超限!';
        }
        $sendMsg = pack('L4', $postData['startX'], $postData['startY'], $postData['imageW'], $postData['imageH']);

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
      
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return  '设置Roi指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*设置Roi 结束*/
    
    protected function set_shutter ($postData, $param) /*设置快门*/
    {
        $sendMsg = pack('S', $postData['shutter']);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);  
      
        $sendMsg = $headInfo . $sendMsg; //socket发送数据
        return  '设置快门指令：'. udpSend($sendMsg, $this->ip, $this->port);	
    }/*设置快门 结束*/

    protected function set_isFullFrame ($postData, $param) /*设置帧转移*/
    {
        $sendMsg = pack('S', $postData['frame']);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);       
        $sendMsg = $headInfo . $sendMsg; //socket发送数据 
        return  '设置帧转移指令：'. udpSend($sendMsg, $this->ip, $this->port);	
    }/*设置帧转移 结束*/

    protected function set_em ($postData, $param)  /*SetEM*/
    {
        if ( $postData['em'] == 1 )
        {
            $res = Db::table('ccdconf')->where('teleid', $postData['at'])->where('ccdno', $this->ccdNo)->field('emmaxvalue, emminvalue')->find(); //获取该望远镜的ccdconf表中的:em 最大 最小值
            
            if ( !preg_match('/^\d+$/', $postData['emV']) || $postData['emV'] > $res['emmaxvalue'] || $postData['emV'] < $res['emminvalue'])
            {
                return 'em值超限!';
            }
            $sendMsg = pack('S', $postData['em']);
            $sendMsg .= pack('L', $postData['emV']);
        }else if ( $postData['em'] == 0 ){//
            $sendMsg = pack('S', $postData['em']);
            $sendMsg .= pack('L', 0);
        }
       
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据 
        return  'SetEM指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*SetEM 结束*/

    protected function set_isNoiseFilter ($postData, $param) /*isNoiseFilter*/
    {
        $sendMsg = pack('S', $postData['isNoiseFilter']);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据  
        return  'CMOS noise filter指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*isNoiseFilter 结束*/

    protected function set_baseLine ($postData, $param) /*set_baseLine*/
    {
        if ( $postData['isBaseLine'] == 1 )
        {
            $sendMsg = pack('S', $postData['isBaseLine']);
            $sendMsg .= pack('L', $postData['baseLineV']);
        }else if ( $postData['isBaseLine'] == 0 ){//
            $sendMsg = pack('S', $postData['isBaseLine']);
            $sendMsg .= pack('L', 0);
        }
 
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
         
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据 
        return  'Baseline指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*set_baseLine 结束*/

    protected function set_overScan ($postData, $param)  /*set over scan*/
    {
        $sendMsg = pack('S', $postData['over_scan']);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
             
        $sendMsg = $headInfo . $sendMsg; //socket发送数据  
        return  'Over Scan指令：' .udpSend($sendMsg, $this->ip, $this->port);	
    }/*set over scan 结束*/
}