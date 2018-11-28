<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
use think\Db;

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
    protected $command_length = [//各指令长度
        'connect' => 50,
        'findhome' => 50,
        'park' => 48,
        'stop' => 48,
        'emergstop' => 48,
        'trackStar' => 68,
        'set_obj_name' => 98,
        'slewAzEl' => 64,
        'slewDerotator' => 56,
        'axis3Mode' => 58,
        //'speed_alter' => 58,	//钟
        //'speed_fixed' => 58,	//钟
		//'position_alter' => 58, //钟
		'speed_alter' => 60,	//胡
        'speed_fixed' => 60,	//胡
		'position_alter' => 60, //胡        
        'cover_op' => 50,
        'setFocusType' => 50,
        'save_sync_data' => 48,
    ];
    protected $operation = [//各指令之编号
        'connect' => 1,
        'findhome' => 2,
        'park' => 10,
        'stop' => 8,
        'emergstop' => 15,
        'trackStar' => 3,
        'set_obj_name' => 4,
        'slewAzEl' => 5,
        'slewDerotator' => 6,
        'axis3Mode' => 7,
        'speed_alter' => 9,
        'speed_fixed' => 11,
        'position_alter' => 12,
        'cover_op' => 13,
        'setFocusType' => 14,
        'save_sync_data' => 16,
    ];

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
        
        $ip = config('ip');
        $port = config('port');

        switch ($postData['at_aperture']) { //根据望远镜口径，给 $this->$at赋值
            case '50cm':
                $this->at = 38;
                $this->ip = $ip['at50'] ;
                $this->port = $port['at50'] ;
                break;
            case '60cm':
                $this->at = 37;
                $this->ip = $ip['at60'] ;
                $this->port = $port['at60'] ;
                break;
            case '80cm':
                $this->at = 36;
                $this->ip = $ip['at80'] ;
                $this->port = $port['at80'] ;
                break;
            case '85cm':
                $this->at = 35;
                $this->ip = $ip['at85'] ;
                $this->port = $port['at85'] ;
                break;
            case '100cm':
                $this->at = 34;
                $this->ip = $ip['at100'] ;
                $this->port = $port['at100'] ;
                break;
            case '126cm':
                $this->at = 33;
                $this->ip = $ip['at126'] ;
                $this->port = $port['at126'] ;
                break;
            case '216cm':
                $this->at = 32;
                $this->ip = $ip['at216'] ;
                $this->port = $port['at216'] ;
                break;
            default:
                return '提交的望远镜参数有误!';
        }

        $command = $postData['command']; //要执行的指令
        //halt($this->command_length[$command]);
        switch ($command) {
            case 'connect':
                return $this->connect (1, 'connect');
            case 'disConnect':
                return $this->connect (2, 'connect');
            case 'findhome':
                return $this->findHome ('findhome');
            case 'park':
                return $this->button_command ('park');
            case 'stop':
                return $this->button_command ('stop');
            case 'emergstop':
                return $this->button_command ('emergstop');
            case 'trackStar':
                return $this->trackStar ($postData, 'trackStar');
            case 'set_obj_name':
                return $this->set_obj_name ($postData, 'set_obj_name');
            case 'slewAzEl':
                return $this->direct ($postData, 'slewAzEl');
            case 'slewDerotator':
                return $this->axis3_direct ($postData, 'slewDerotator');
            case 'axis3Mode':
                return $this->axis3_mode ($postData, 'axis3Mode');
            case 'speed_alter': //速度修正
                return $this->speed_alter ($postData, 'speed_alter');
            case 'speed_fixed': //恒速运动
                return $this->fixed_move ($postData, 'speed_fixed');
            case 'position_alter': //位置修正
                return $this->pos_correct ($postData, 'position_alter');
            case 'cover_op': //镜盖操作
                return $this->cover_op ($postData, 'cover_op');
            case 'setFocusType': //切换镜操作
                return $this->setFocusType ($postData, 'setFocusType');
            case 'save_sync_data': //保存同步数据
                return $this->saveSynchData ('save_sync_data');
            default:
                break;
        }
        // }else if( $command == 12 ){//跟踪卫星            
        //     return $this->track_satellite();    //执行发送
        // }else if( $command == 13 ){//属性设置            
        //     return $this->property_config();    //执行发送
        // }
    }//接收参数，根据不同参数，向不同望远镜的转台指令 结束/////

    
    protected function connect ($connect, $param) //连接 断开 指令
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
        
        $sendMsg = pack('S', $connect);  //unsigned short
      
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        if ($connect == 1)
        {
            return '连接指令：'. udpSend($sendMsg, $this->ip, $this->port);
        }elseif ($connect == 2)
        {
            return '断开指令：'. udpSend($sendMsg, $this->ip, $this->port);
        }
    }//连接 断开 指令 结束

    protected function findHome ($param)  //找零 指令
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
        $sendMsg = pack('S', 1);  //unsigned short
        
        $sendMsg = $headInfo . $sendMsg; //socket发送数据
        return '找零指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//找零 指令 结束
    
    protected function button_command ($param) //复位 停止 急停 指令
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
        
        $sendMsg = $headInfo; //socket发送数据
        if ( $param == 'park' )
        {
            return '复位指令：'. udpSend($sendMsg, $this->ip, $this->port);
        }else if ( $param == 'stop' ){
            return '停止指令：'. udpSend($sendMsg, $this->ip, $this->port);
        }else if ( $param == 'emergstop' ){
            return '急停指令：'. udpSend($sendMsg, $this->ip, $this->port);
        }
        
    }//复位 停止 急停 指令 结束

    protected function trackStar ($postData, $param) //跟踪恒星 指令
    {          
        if ( !preg_match('/^\d{2}$/', $postData['rightAscension1']) || $postData['rightAscension1'] > 24 || $postData['rightAscension1'] < 0)
        {
        	return '赤经之小时参数超限!';
        }
        
        if ( !preg_match('/^\d{2}$/', $postData['rightAscension2']) || $postData['rightAscension2'] > 59 || $postData['rightAscension2'] < 0)
        {
        	return '赤经之分钟参数超限!';
        }
        
        if (!is_numeric($postData['rightAscension3']) || $postData['rightAscension3'] >= 60 || $postData['rightAscension3'] < 0)
        {
        	return '赤经之秒参数超限!';
        }

        $rightAscension = $postData['rightAscension1'].':'.$postData['rightAscension2'].':'.$postData['rightAscension3'];
        
        $rightAscension = time2Data($rightAscension);
        
        if ($rightAscension > 24 || $rightAscension < 0) //赤经
        {
            return '赤经参数超限!';
        }else{
            $sendMsg = pack('d', $rightAscension);     //double64
        }
        
        //处理赤纬数据
        if ( !preg_match('/^\d{2}$/', $postData['declination1']) || $postData['declination1'] > 90 || $postData['declination1'] < -90)
        {
        	return '赤纬之小时参数超限!';
        }
        
        if ( !preg_match('/^\d{2}$/', $postData['declination2']) || $postData['declination2'] > 59 || $postData['declination2'] < 0)
        {
        	return '赤纬之分钟参数超限!';
        }
        
        if (!is_numeric($postData['declination3']) || $postData['declination3'] >= 60 || $postData['declination3'] < 0)
        {
        	return '赤纬之秒参数超限!';
        }
        
        $declination = $postData['declination1'].':'.$postData['declination2'].':'.$postData['declination3'];
        
        $declination = time2Data($declination);
        
        if ($declination > 90 || $declination < -90) //赤纬
        {
            return '赤纬参数超限!';
        }else{
            $sendMsg .= pack('d', $declination);     //double64
        }
            
        if ( !($postData['epoch'] == 0 || $postData['epoch'] == 1 || $postData['epoch'] == 2 || $postData['epoch'] == 3) ) //历元
        {
            return '历元参数超限！';
        }
        $sendMsg .= pack('S', $postData['epoch']);     //unsigned short
            
        if ( !($postData['speed'] == 0 || $postData['speed'] == 1 || $postData['speed'] == 2 || $postData['speed'] == 3) ) //历元 //跟踪速度
        {
            return '跟踪速度参数超限！';
        }
        $sendMsg .= pack('S', $postData['speed']);     //unsigned short
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
       
        $sendMsg = $headInfo . $sendMsg; //socket发送数据
        return '跟踪恒星指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//跟踪恒星 指令 结束

    protected function set_obj_name ($postData, $param)  //设置目标名称
    {   
        $length = strlen( $postData['objectName'] );
        if ( preg_match('/[\x{4e00}-\x{9af5} ]/u', $postData['objectName']) || $length > 48 || $length < 1 )
        {
            return '目标名称须48字符不能含汉字或空格！';
        }
        $sendMsg = pack('a48', $postData['objectName']);   //uint8,48表示长度
        
        if ( !preg_match('/^[0-6]$/', $postData['objectType']) )
        {
            return '目标类型参数超限！';
        }
        $sendMsg .= pack('S', $postData['objectType']);     //unsigned short
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
       
        $sendMsg = $headInfo . $sendMsg; //socket发送数据
        return '设置目标名称指令：' . udpSend($sendMsg, $this->ip, $this->port);
    }//设置目标名称 结束
    
    protected function direct ($postData, $param)    //指向固定位置
    {        
        if ( !is_numeric($postData['azimuth']) ||  $postData['azimuth'] > 360 || $postData['azimuth'] < 0 )
        {
            return '方位参数超限';
        }
        $sendMsg = pack('d', $postData['azimuth']);     //double64

        $elevation = Db::table('gimbalconf')->where('teleid', $postData['at'])->field('minelevation')->find(); //获取该望远镜的gimbalconf表中的:俯仰最低值
 
        if ( !is_numeric($postData['elevation']) ||  $postData['elevation'] > 90 || $postData['elevation'] < $elevation['minelevation'] )
        {
            return '俯仰参数超限';
        }
        $sendMsg .= pack('d', $postData['elevation']);     //double64
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
       
        $sendMsg = $headInfo . $sendMsg; //socket发送数据
        return '指向固定位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }//指向固定位置 结束

    protected function axis3_direct ($postData, $param) //轴3指向固定位置
    {       
        if ( !is_numeric($postData['slewDerotator']) ||  $postData['slewDerotator'] > 360 || $postData['slewDerotator'] < 0 )
        {
            return '轴3指向位置参数超限!';
        }

        $sendMsg = pack('d', $postData['slewDerotator']);  //double64
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '轴3指向固定位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }//轴3指向固定位置 结束

    protected function axis3_mode($postData, $param)  //轴3工作模式
    {
        if (!preg_match('/^[0-2]$/', $postData['mode']))
        {
            return '轴三工作模式参数无效!';
        }
        $sendMsg = pack('S', $postData['mode']);     //unsigned short

        if ( $postData['mode'] == 2 )
        {
            if ( !is_numeric($postData['polarizingAngle']) ||  $postData['polarizingAngle'] > 360 || $postData['polarizingAngle'] < 0 ) // 起偏角
            {
                return '轴三起偏角参数超限!';
            }
            $sendMsg .= pack('d', $postData['polarizingAngle']);     //double64
        }else{
            $sendMsg .= pack('d', 0);     //double64
        }
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '轴3工作模式指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//轴3工作模式  结束

    protected function speed_alter($postData, $param) //速度修正
    {
        $all_correction = ['0.000013889', '0.000027778', '0.000138889', '0.000277778',
                          '0.001388889', '0.002777778', '0.008333333', '0.016666667',
                          '0.083333333', '0.166666667', '0.5', '1.0'];
        if ( !in_array($postData['correction'], $all_correction) ) //验证提交的速度值
        {
            return '速度值参数超限!';
        }

        switch ($postData['axis']) { //根据不同的按钮 所代表的轴 对提交数据进行处理
            case 'e': //东：轴1 赤经为减
                $postData['axis'] = 1;
                $postData['correction'] *= -1;
                return $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 'w': //西
                $postData['axis'] = 1;
                return $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 's': //南 轴2 赤纬为减
                $postData['axis'] = 2;
                $postData['correction'] *= -1;
                return $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 'n': //北
                $postData['axis'] = 2;
                return $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 'n_w': //西北
                $postData['axis'] = 1;
                $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                $postData['axis'] = 2;
                return $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 'n_e': //东北
                $postData['axis'] = 2;
                $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                $postData['axis'] = 1;
                $postData['correction'] *= -1;
                return $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 's_e': //东南
                $postData['axis'] = 1;
                $postData['correction'] *= -1;
                $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                $postData['axis'] = 2;
                return $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 's_w': //西南
                $postData['axis'] = 1;
                $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                $postData['axis'] = 2;
                $postData['correction'] *= -1;
                return $this->speed_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            default:
                return '参数非法!';
                break;
        }
    }//速度修正  结束

    protected function speed_alter_sendData ($axix, $correction, $param) //发送 速度修正指令的参数
    {
        //$sendMsg = pack('S', $axix);   //unsigned short 钟
        $sendMsg = pack('I', $axix);   //unsigned int 胡 201807
        $sendMsg .= pack('d', $correction); //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '速度修正指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }

    protected function fixed_move ($postData, $param) //恒速运动 ///////
    {
        if ( !is_numeric($postData['speed']) || $postData['speed'] == 0 )
        {
            return '速度参数超限!';
        }
       
        $max_speed = Db::table('gimbalconf')->where('teleid', $postData['at'])->field('maxaxis1speed, maxaxis2speed, maxaxis3speed')->find(); //获取该望远镜的gimbalconf表中的:3个轴的最大速度

        switch ($postData['axis']) {
            case '1':
                $max_speed = $max_speed['maxaxis1speed'];
                break;
            case '2':
                $max_speed = $max_speed['maxaxis2speed'];
                break;
            case '3':
                $max_speed = $max_speed['maxaxis3speed'];
                break;
            default:
                break;
        }

        if ( $postData['speed'] > $max_speed || $postData['speed'] < $max_speed*-1 )
        {
            return '速度参数超限!';
        }

        //$sendMsg = pack('S', $postData['axis']);  //unsigned short 钟
        $sendMsg = pack('I', $postData['axis']);  //unsigned int 胡
        $sendMsg .= pack('d', $postData['speed']);  //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '恒速运动指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }//恒速运动 结束///////

    protected function pos_correct($postData, $param)  //位置修正
    {
        $all_correction = ['0.000013889', '0.000027778', '0.000138889', '0.000277778',
                          '0.001388889', '0.002777778', '0.008333333', '0.016666667',
                          '0.083333333', '0.166666667', '0.5', '1.0'];
        if ( !in_array($postData['correction'], $all_correction) ) //验证提交的偏移量
        {
            return '偏移量参数超限!';
        }

        switch ($postData['axis']) { //根据不同的按钮 所代表的轴 对提交数据进行处理
            case 'e': //东：轴1 赤经为减
                $postData['axis'] = 1;
                $postData['correction'] *= -1;
                return $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 'w': //西
                $postData['axis'] = 1;
                return $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 's': //南 轴2 赤纬为减
                $postData['axis'] = 2;
                $postData['correction'] *= -1;
                return $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 'n': //北
                $postData['axis'] = 2;
                return $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 'n_w': //西北
                $postData['axis'] = 1;
                $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                $postData['axis'] = 2;
                return $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 'n_e': //东北
                $postData['axis'] = 2;
                $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                $postData['axis'] = 1;
                $postData['correction'] *= -1;
                return $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 's_e': //东南
                $postData['axis'] = 1;
                $postData['correction'] *= -1;
                $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                $postData['axis'] = 2;
                return $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            case 's_w': //西南
                $postData['axis'] = 1;
                $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                $postData['axis'] = 2;
                $postData['correction'] *= -1;
                return $this->pos_alter_sendData ($postData['axis'], $postData['correction'], $param);
                break;
            default:
                return '参数非法!';
                break;
        }
    }/*位置修正 结束**********/

    protected function pos_alter_sendData ($axix, $correction, $param) //发送 位置修正指令的参数
    {
        //$sendMsg = pack('S', $axix);   //unsigned short 钟
        $sendMsg = pack('I', $axix);   //unsigned int 胡
        $sendMsg .= pack('d', $correction); //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '位置修正指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//发送 位置修正指令的参数 结束

    protected function cover_op($postData, $param) /*镜盖操作 **********/
    {
        $postData['operation'] == 'open' && $postData['operation'] = 1;
        $postData['operation'] == 'close' && $postData['operation'] = 2;
        $postData['operation'] == 'stop' && $postData['operation'] = 0;
        $sendMsg = pack('S', $postData['operation']);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '镜盖指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*镜盖操作 结束**********/

    
    protected function setFocusType($postData, $param) /*焦点切换镜 **********/
    {
        $sendMsg = pack('S', $postData['focusType']); //unsigned short
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '焦点切换镜指令：' .udpSend($sendMsg, $this->ip, $this->port);		
    }/*焦点切换镜 结束**********/
 
    protected function saveSynchData($param)  /*保存同步数据 **********/
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo;  //socket发送数据
        return '保存同步数据指令：' .udpSend($sendMsg, $this->ip, $this->port);		
    }/*保存同步数据 结束**********/

     /*属性设置 **********/
     protected function property_config()
     {
         /*$length = 48;
 
         $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
         $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=16);
         //socket发送数据
         $sendMsg = $headInfo . $sendMsg;
         return '跟踪卫星指令：' .udpSend($sendMsg, $this->ip, $this->port);	*/	
     }/*属性设置 结束**********/
    
    /*跟踪卫星 **********/
    protected function track_satellite()
    {
        /*$length = 48;

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=16);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '跟踪卫星指令：' .udpSend($sendMsg, $this->ip, $this->port);	*/	
    }/*跟踪卫星 结束**********/
}/*gimbal控制器 结束**********/