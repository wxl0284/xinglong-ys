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
        if ( $postData['at'] == md5 ('60cm望远镜') )
        {
            $this->at = 37;
        }else if ( $postData['at'] == md5 ('80cm望远镜') ){
            $this->at = 36;
        }else if ( $postData['at'] == md5 ('50cm望远镜')  ){
            $this->at = 38;
        }else if ( $postData['at'] == md5 ('85cm望远镜')  ){
            $this->at = 35;
        }else if ( $postData['at'] == md5 ('100cm望远镜')  ){
            $this->at = 34;
        }else if ( $postData['at'] == md5 ('126cm望远镜')  ){
            $this->at = 33;
        }else if ( $postData['at'] == md5 ('216cm望远镜')  ){
            $this->at = 32;
        }else if ( $postData['at'] == md5 ('大气消光望远镜')  ){
            $this->at = 39;
        }

        $command = input('command'); //获取提交的指令
        //根据不同参数 调用相应方法发送指令
        if ( ($connect = input('connect')) !== null ) //连接或断开指令
        {
            if (!($connect== 1 || $connect == 2)) //匹配1和2
			{
                return '连接/断开指令无效!'; 
            }
         
            return $this->connect($connect);   //执行发送
        }else if( ($findHome=input('findHome')) !== null){//找零
            if ($findHome != 1)
			{
                return '找零指令无效!';  
            }
            
            return $this->findHome($findHome); //执行发送
        }else if( ($park=input('park')) !== null){//复位
            if ($park != 1)
			{
                return '复位指令无效!';  
            }
          
            return $this->park();  //执行发送
        }else if( ($stop=input('stop')) !== null){//停止
            if ($stop != 1)
			{
                return '停止指令无效!';  
            }
            
            return $this->stop(); //执行发送
        }else if( ($EmergStop=input('EmergenceStop')) !== null){//急停
            if ($EmergStop != 1)
			{
                return '急停指令无效!';  
            }
            
            return $this->emergStop(); //执行发送
        }else if( $command == 1 ){//跟踪恒星指令            
            return $this->trackStar(); //执行发送
        }else if( $command == 2 ){//设置目标名称             
            return $this->objectName();  //执行发送
        }else if( $command == 3 ){//指向固定位置            
            return $this->direct();   //执行发送
        }else if( $command == 4 ){//轴3指向固定位置            
            return $this->axis3_direct();    //执行发送
        }else if( $command == 5 ){//设置轴3工作模式            
            return $this->axis3_mode();    //执行发送
        }else if( $command == 6 ){//速度修正            
            return $this->speed_alter();    //执行发送
        }else if( $command == 7 ){//恒速运动            
            return $this->fixed_move();    //执行发送
        }else if( $command == 8 ){//位置修正            
            return $this->pos_correct();    //执行发送
        }else if( $command == 9 ){//镜盖操作            
            return $this->cover_op();    //执行发送
        }else if( $command == 10 ){//焦点切换镜操作            
            return $this->setFocusType();    //执行发送
        }else if( $command == 11 ){//保存同步数据            
            return $this->saveSynchData();    //执行发送
        }else if( $command == 12 ){//跟踪卫星            
            return $this->track_satellite();    //执行发送
        }else if( $command == 13 ){//属性设置            
            return $this->property_config();    //执行发送
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

    //设置目标名称
    protected function objectName ()
    {
        $length = 48 + 50;    //该结构体总长度
                
        if (($objectName=input('objectName')) !== '')
        {//目标名称
            if (preg_match('/[\x{4e00}-\x{9af5} ]/u', $objectName))
            {
                return '目标名称不能含汉字或空格！';
            }
            $sendMsg = pack('a48', $objectName);   //uint8,48表示长度
        }else{
            $sendMsg = pack('a48', '0');
        }
        
        if (($objectType=input('objectType')) !== '') //目标类型
        {
            if (!preg_match('/^\d{1}$/', $objectType))
            {
                return '目标类型参数超限！';
            }
            $sendMsg .= pack('S', $objectType);     //unsigned short
        }
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=4);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '设置目标名称指令：' . udpSend($sendMsg, $this->ip, $this->port);
    }//设置目标名称 结束

    //指向固定位置
    protected function direct ()
    {
        $length = 48 + 16;    //该结构体总长度
        
        if (($azimuth=input('azimuth')) !== '') //方位
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $azimuth))
            {
                return '方位必须为数字！';
            }
            $sendMsg = pack('d', $azimuth);     //double64
        }else{
            $sendMsg = pack('d', 0);
        }
        
        if (($elevation=input('elevation'))  !== '') //俯仰
        {
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $elevation))
            {
                return '俯仰必须为数字！';
            }
            $sendMsg .= pack('d', $elevation);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=5);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '指向固定位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }//指向固定位置 结束

    //轴3指向固定位置
    protected function axis3_direct ()
    {
        $length = 48 + 8;    //该结构体总长度
        
        $slewDerotator = input('slewDerotator');
        if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $slewDerotator))
        {
            return '轴3指向固定位置必须为数字！';
        }
        $sendMsg = pack('d', $slewDerotator);  //double64
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=6);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '轴3指向固定位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }//轴3指向固定位置 结束

    //轴3工作模式
    protected function axis3_mode()
    {
        $length = 48 + 10;

        if (($mode=input('mode')) !== '')
        {//工作模式
            if (!preg_match('/^\d{1,15}$/', $mode))
            {
                return '轴三工作模式只能是数字！';
            }
            $sendMsg = pack('S', $mode);     //unsigned short
        }else{
            $sendMsg = pack('S', 0);
        }
        
        if (($polarizingAngle=input('polarizingAngle')) !== '')
        {//轴三 起偏角
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $polarizingAngle))
            {
                return '轴三起偏角只能是数字！';
            }
            $sendMsg .= pack('d', $polarizingAngle);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=7);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '轴3工作模式指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//轴3工作模式  结束

    //速度修正
    protected function speed_alter()
    {
        $length = 48 + 10;

        if (($axis=input('axis')) !== '')  //轴
        {
            if (!preg_match('/^\d{1}$/', $axis))
            {
                return '速度修正:轴必须为数字！';
            }
            $sendMsg = pack('S', $axis);   //unsigned short
        }else{
            $sendMsg = pack('S', 0);
        }
        
        if (($correction=input('correction')) !== '')
        {//修正值
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $correction))
            {
                return '速度修正值必须为数字！';
            }
            $sendMsg .= pack('d', $correction);   //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=9);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '速度修正指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }//速度修正  结束

    //恒速运动 ///////
    protected function fixed_move ()
    {
        $length = 48 + 10;

        if (($FixedMoveAxis=input('FixedMoveAxis')) !== '')
        {//恒速运动  轴
            if (!preg_match('/^\d{1,2}$/', $FixedMoveAxis))
            {
                return '恒速运动(轴参数)只能是数字！';
            }
            $sendMsg = pack('S', $FixedMoveAxis);  //unsigned short
        }else{
            $sendMsg = pack('S', 0);
        }
        
        if (($FixedMoveSpeed=input('FixedMoveSpeed')) !== '')
        {//恒速运动  速度
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $FixedMoveSpeed))
            {
                return '恒速运动(速度)只能是数字！';
            }
            $sendMsg .= pack('d', $FixedMoveSpeed);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=11);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '恒速运动指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }//恒速运动 结束///////

    /*位置修正**********/
    protected function pos_correct()
    {
        $length = 48 + 10;

        if (($PositionCorrectAxis=input('PositionCorrectAxis')) !== '')
        {//位置修正 轴
            if (!preg_match('/^\d{1,2}$/', $PositionCorrectAxis))
            {
                return '位置修正(轴参数)只能是数字！';
            }
            $sendMsg = pack('S', $PositionCorrectAxis);  //16位
        }else{
            $sendMsg = pack('S', 0);
        }
        
        if (( $PositionCorrectVal=input('PositionCorrectVal')) !== '')
        {// 修正值
            if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $PositionCorrectVal))
            {
                return '位置修正值只能是数字！';
            }
            $sendMsg .= pack('d', $PositionCorrectVal);     //double64
        }else{
            $sendMsg .= pack('d', 0);
        }
        
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=12);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '位置修正指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*位置修正 结束**********/

    /*镜盖操作 **********/
    protected function cover_op()
    {
        $length = 48 + 2;

        $openCover = input('openCover');
        if (!preg_match('/^[0-2]$/', $openCover))
        {
            return '镜盖操作参数超限！';
        }
        $sendMsg = pack('S', $openCover);     //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=13);
		//socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '镜盖指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*镜盖操作 结束**********/

    /*焦点切换镜 **********/
    protected function setFocusType()
    {
        $length = 48 + 2;

        $setFocusType = input('setFocusType');
        if (!preg_match('/^\d{1,5}$/', $setFocusType))
        {
            return '焦点切换镜只能是数字！';
        }
        $sendMsg = pack('S', $setFocusType); //unsigned short
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=14);
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '焦点切换镜指令：' .udpSend($sendMsg, $this->ip, $this->port);		
    }/*焦点切换镜 结束**********/

    /*保存同步数据 **********/
    protected function saveSynchData()
    {
        $length = 48;

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=16);
        //socket发送数据
        $sendMsg = $headInfo;
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