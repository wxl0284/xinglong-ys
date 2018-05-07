<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
//use think\Db;

/*此控制器 负责各望远镜的随动圆顶指令发送*/
class Slavedome extends Base
{
    //定义所需变量
    protected $sequence = 0;    //指令的序号
    protected $at = 0;  //望远镜序号
    protected $device = 67;  //随动圆顶对应序号
    protected $msg = 6;  //单指令 msg 
    protected $magic = 439041101;  //转台对应序号
    protected $version = 1;  //版本号
    protected $plan = 0;  //计划
    protected $user = 0;  //操作者
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port
    
    //接收参数，根据不同参数，向不同望远镜的随动圆顶发指令
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
        }else if ($postData['at']== 126){
            $this->at = 33;
        }else if ($postData['at']== 216){
            $this->at = 32;
        }

        $command = input('command'); //获取提交的指令
        //根据不同参数 调用相应方法发送指令
        if ( ($sDomeConnect=input('sDomeConnect')) !== null ) //连接或断开指令
        {
            if (!($sDomeConnect == 1 || $sDomeConnect == 2))
			{
                return '随动圆顶连接指令无效!';
			}
         
            return $this->connect($sDomeConnect);   //执行发送
        }else if( input('sDomeStop') !== null){//停止运动 指令      
            return $this->stop(); //执行发送
        }else if( ($OpenScuttle=input('OpenScuttle')) !== null ){//开关天窗 指令      
            return $this->scuttle($OpenScuttle); //执行发送
        }else if( $command == 1 ){//目标方位 指令      
            return $this->set_domePosition(); //执行发送
        }else if( $command == 2 ){//转动速度 指令      
            return $this->rotateSpeed(); //执行发送
        }else if( $command == 3 ){//风帘位置 指令      
            return $this->shadePosition(); //执行发送
        }else if( $command == 4 ){//风帘运动 指令      
            return $this->shadeAction(); //执行发送
        }

    }/*接收参数，根据不同参数，向不同望远镜的随动圆顶发指令 结束*/

    /*随动圆顶 连接/断开*/
    protected function connect ($connect)
    {
        $length = 48 +2;      //结构体长度
        $sendMsg = pack('S', $connect); //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=1);

        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        if ($connect == 1)
        {
            return '随动圆顶连接指令：' .udpSend($sendMsg, $this->ip, $this->port);	
        }elseif ($connect == 2)
        {
            return '随动圆顶断开指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }
    }/*随动圆顶 连接/断开*/

    /*随动圆顶 停止运动*/
    protected function stop ()
    {
        $length = 48;      //结构体长度

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=5);

        //socket发送数据
        $sendMsg = $headInfo;
        return '随动圆顶停止运动指令:' .udpSend($sendMsg, $this->ip, $this->port);
    }/*随动圆顶 停止运动 结束*/

    /*随动圆顶 开/关天窗*/
    protected function scuttle ($scuttle)
    {
        $length = 48 + 2;      //结构体长度
 
        if (!preg_match('/^\d{1,10}$/', $scuttle))
        {
            return '开关天窗值必须是数字！'; 
        }
        $sendMsg = pack('S', $scuttle); //unsinged short
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=6);

        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        if ($scuttle == 1)
        {
            return '打开天窗指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }elseif ($scuttle == 2)
        {
            return '关闭天窗指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }
    }/*随动圆顶 开/关天窗 结束*/

    /*随动圆顶 目标方位*/
    protected function set_domePosition ()
    {
        $length = 48 + 8;      //结构体长度
 
        $domePosition = input('domePosition');
        if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $domePosition))
        {
            return '目标方位值必须是数字！';
        }
        $sendMsg = pack('d', $domePosition);    //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=2);

        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '目标方位指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*随动圆顶 目标方位 结束*/

     /*随动圆顶 转动速度*/
     protected function rotateSpeed ()
     {
         $length = 48 + 8;      //结构体长度
  
         $RotateSpeed = input('RotateSpeed');
         if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $RotateSpeed))
         {
            return '转动速度值必须是数字！';
         }
         $sendMsg = pack('d', $RotateSpeed);    //double64
 
         $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
         $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=4);
 
         //socket发送数据
         $sendMsg = $headInfo . $sendMsg;
         return '转动速度指令：' .udpSend($sendMsg, $this->ip, $this->port);
     }/*随动圆顶 转动速度 结束*/

     /*随动圆顶 风帘位置*/
     protected function shadePosition ()
     {
         $length = 48 + 8;      //结构体长度
  
         $shadePosition = input('shadePosition');
         if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $shadePosition))
         {
            return '风帘位置的值必须是数字！';
         }
     
         $sendMsg = pack('d', $shadePosition);  //double64
 
         $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
         $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=3);
 
         //socket发送数据
         $sendMsg = $headInfo . $sendMsg;
         return '风帘位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
     }/*随动圆顶 风帘位置 结束*/

     /*随动圆顶 风帘运动*/
     protected function shadeAction ()
     {
        $length = 48 + 2;      //结构体长度
  
        $shadeAction = input('shadeAction');
        if (!preg_match('/^\d{1,10}$/', $shadeAction))
        {
            return '风帘运动值必须是数字！'; 
        }
        $sendMsg = pack('S', $shadeAction); //unsigned short
 
        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);
 
        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=7);
 
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '风帘运动指令：' .udpSend($sendMsg, $this->ip, $this->port);
     }/*随动圆顶 风帘运动 结束*/
}