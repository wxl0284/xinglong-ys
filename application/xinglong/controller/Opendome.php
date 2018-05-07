<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
//use think\Db;

/*此控制器 负责各望远镜的全开圆顶 指令发送*/
class Opendome extends Base
{
    //定义所需变量
    protected $sequence = 0;    //指令的序号
    protected $at = 0;  //望远镜序号
    protected $device = 68;  //全开圆顶对应序号
    protected $msg = 6;  //单指令 msg 
    protected $magic = 439041101;  //转台对应序号
    protected $version = 1;  //版本号
    protected $plan = 0;  //计划
    protected $user = 0;  //操作者
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port
    
    //接收参数，根据不同参数，向不同望远镜的全开圆顶发指令
    public function sendCommand ()
    {
        //首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

        //接受表单数据
        $postData = input ();
        halt($postData);
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

        //根据不同参数 调用相应方法发送指令
        if ( $postData['fDomeConnect'] !== '' ) //连接或断开指令
        {
            if ( !( $fDomeConnect == 1 || $fDomeConnect == 2) )
			{
                return '全开圆顶连接指令无效!';
			}
         
            return $this->connect($fDomeConnect);   //执行发送
        }else if( $postData['fDomeConnect'] !== '' ){//打开/关闭圆顶  
            if ( !($openDome == 0 || $openDome == 1 || $openDome == 2) )
			{
                return '全开圆顶打开/关闭指令无效!';
			}    
            return $this->openDome($openDome); //执行发送
        }
    }//接收参数，根据不同参数，向不同望远镜的全开圆顶发指令 结束

    /*全开圆顶 连接/断开 */
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
            return '全开圆顶连接指令：' .udpSend($sendMsg, $this->ip, $this->port); 
        }elseif ($connect == 2)
        {
            return '全开圆顶断开指令：' .udpSend($sendMsg, $this->ip, $this->port); 
        }
    }/*全开圆顶 连接/断开 结束*/

    /*全开圆顶 连接/断开 */
    protected function openDome ($openDome)
    {
        $length = 48 + 2;     //该结构体总长度

        $headInfo = packHead($this->magic,$this->version,$this->msg,$length,$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$operation=2);

        $sendMsg = pack('S', $openDome);    //unsigned short
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        if ($openDome == 0)
        {
            return '全开圆顶关闭指令：'. udpSend($sendMsg, $this->ip, $this->port);
        }elseif ($openDome == 1)
        {
            return '全开圆顶打开指令：'. udpSend($sendMsg, $this->ip, $this->port);
        }elseif ($openDome == 2)
        {
            return '全开圆顶停止指令：'. udpSend($sendMsg, $this->ip, $this->port);
        }
    }/*全开圆顶 连接/断开 结束*/
}