<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
//use think\Db;

/*此控制器 负责各望远镜的调焦器 指令发送*/
class Focus extends Base
{
    //定义所需变量
    protected $sequence = 0;    //指令的序号
    protected $at = 0;  //望远镜序号
    protected $device = 69;  //调焦器对应序号
    protected $msg = 6;  //单指令 msg 
    protected $magic = 439041101;  //转台对应序号
    protected $version = 1;  //版本号
    protected $plan = 0;  //计划
    protected $user = 0;  //操作者
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port
    
    /*接收参数，根据不同参数，向不同望远镜的调焦器-focus 发指令*/
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
        if ( ($focusConnect = input('focusConnect')) !== null )
        {
            if ( !($focusConnect == 1 || $focusConnect == 2) )
			{
                return '调焦器连接/断开指令无效!';
            }
           
            return $this->connect($focusConnect);   //执行发送
        }else if( input('focusStop') == 1 ){//调焦器:停止运动 指令   
            return $this->stop(); //执行发送
        }else if( input('findHome') == 1 ){//调焦器:找零 指令   
            return $this->find_home(); //执行发送
        }else if( $command == 1 ){//调焦器:设置目标位置 指令   
            return $this->set_position(); //执行发送
        }else if( $command == 2 ){//调焦器:恒速转动 指令   
            return $this->set_speed(); //执行发送
        }else if( $command == 3 ){//调焦器:使能温度补偿 指令   
            return $this->enable(); //执行发送
        }else if( $command == 4 ){//调焦器:使能温度补偿系数 指令   
            return $this->set_coefficient(); //执行发送
        }
    } /*接收参数，根据不同参数，向不同望远镜的调焦器-focus 发指令  结束*/

    /*调焦器连接-断开*/
    protected function connect ($connect)
    {
        $length = 48 + 2;     //该结构体总长度

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=1);
        
        $sendMsg = pack('S', $connect); //unsigned short
        //halt ($sendMsg);
        $sendMsg = $headInfo . $sendMsg;
        //$sendMsg = $headInfo;
       // halt($sendMsg);
        
        if ($connect == 1)
        {
           return '调焦器连接指令：' . udpSend($sendMsg, $this->ip, $this->port);
        }elseif ($connect == 2)
        {
            return '调焦器断开指令：' . udpSend($sendMsg, $this->ip, $this->port);
        } 
    }/*调焦器连接-断开 结束*/

    /*调焦器 停止运动*/
    protected function stop ()
    {
        $length = 48;     //该结构体总长度

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=4);

        //socket发送数据
        $sendMsg = $headInfo;
        return '调焦器停止指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*调焦器 停止运动 结束*/

    /*调焦器 找零*/
    protected function find_home ()
    {
        $length = 48;     //该结构体总长度

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=7);

        //socket发送数据
        $sendMsg = $headInfo;
        return '调焦器找零指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*调焦器 找零 结束*/

    /*调焦器 设置目标位置*/
    protected function set_position ()
    {
        $length = 48 + 8;     //该结构体总长度

        $setPosition = input('setPosition');
        if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $setPosition))
        {
            return '目标位置的值必须是数字！';
        }
        $sendMsg = pack('d', $setPosition);    //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=2);

        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
		return '设置目标位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*调焦器 设置目标位置 结束*/

    /*调焦器 设置恒速转动*/
    protected function set_speed ()
    {
        $length = 48 + 8;     //该结构体总长度

        $speed = input('speed');
        if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $speed))
        {
            return '恒速转动的值必须是数字！'; 
        }
        $sendMsg = pack('d', $speed);    //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=3);

        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
		return '恒速转动指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*调焦器 设置恒速转动 结束*/

    /*调焦器 使能温度补偿*/
    protected function enable ()
    {
        $length = 48 + 2;     //该结构体总长度

        $enable = input('enable');
        if (!preg_match('/^\d{1,5}$/', $enable))
        {
            return '使能温度补偿的值必须是数字！';
        }
        $sendMsg = pack('S', $enable);      //uint16

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=5);

        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
		return '使能温度补偿指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*调焦器 使能温度补偿 结束*/

    /*调焦器 使能温度补偿系数*/
    protected function set_coefficient ()
    {
        $length = 48 + 8;     //该结构体总长度

        $coefficient = input('coefficient');
        if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $coefficient))
        {
            return '温度补偿系数的值必须是数字！';
        }
        $sendMsg = pack('d', $coefficient);      //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=6);

        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
		return '温度补偿系数：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*调焦器 使能温度补偿系数 结束*/

}