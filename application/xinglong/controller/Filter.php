<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
//use think\Db;

/*此控制器 负责各望远镜的滤光片指令发送*/
class Filter extends Base
{
    //定义所需变量
    protected $sequence = 0;    //指令的序号
    protected $at = 0;  //望远镜序号
    protected $device = 66;  //滤光片对应序号
    protected $msg = 6;  //单指令 msg 
    protected $magic = 439041101;  //转台对应序号
    protected $version = 1;  //版本号
    protected $plan = 0;  //计划
    protected $user = 0;  //操作者
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port
    
    //接收参数，根据不同参数，向不同望远镜的滤光片指令
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
        if ( ($filterConnect=input('filterConnect')) !== null ) //连接或断开 指令
        {
            if (!($filterConnect == 1 || $filterConnect == 2))
			{
                return '滤光片连接/断开指令无效!';
			}
         
            return $this->connect($filterConnect);   //执行发送
        }else if( input('filterFindHome') == 1 ){//找零 指令      
            return $this->findHome(); //执行发送
        }else if( ($filterPos=input('filterPos')) !== null ){//滤光片位置 指令      
            return $this->set_filterPos($filterPos); //执行发送
        }

    }//接收参数，根据不同参数，向不同望远镜的滤光片指令 结束

    /*滤光片连接/断开*/
    protected function connect ($connect)
    {
        $length = 48 +2;      //结构体长度
        $sendMsg = pack('S', $connect ); //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=1);

        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        
        if ($connect == 1)
        {
            return '滤光片连接指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }elseif ($connect == 2)
        {
            return '滤光片断开指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }
    }/*滤光片连接/断开 结束*/

    /*滤光片找零*/
    protected function findHome ()
    {
        $length = 48;      //结构体长度
        $sendMsg = pack('S', $connect ); //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=3);

        //socket发送数据
        $sendMsg = $headInfo;
        return '滤光片找零指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*滤光片找零 结束*/

    /*滤光片位置*/
    protected function set_filterPos ($filterPos)
    {
        $length = 48 + 2;      //结构体长度
        if (!preg_match('/^\d{1,5}$/', $filterPos))
        {
            return '滤光片位置必须是数字！';
        }
        $sendMsg = pack('S', $filterPos);  //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=2);

        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        return '滤光片位置指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }/*滤光片位置 结束*/
}