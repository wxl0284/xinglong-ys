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
    protected $command_length = [//各指令长度
        'connect' => 50,
        'open' => 50,
    ];
    protected $operation = [//各指令之编号
        'connect' => 1,
        'open' => 2,
    ]; 
    
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
        //halt($postData);
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

        $command = $postData['command'];
       
        switch ($command) { //根据不同参数 调用相应方法发送指令
            case 'connect':
                return $this->connect (1, 'connect');
            case 'disConnect':
                return $this->connect (2, 'connect');
            case 'open':
                return $this->openDome (1, 'open');
            case 'close':
                return $this->openDome (0, 'open');
            case 'stop':
                return $this->openDome (2, 'open');
            default:
                break;
        }
    }//接收参数，根据不同参数，向不同望远镜的全开圆顶发指令 结束

    protected function connect ($connect, $param)  /*全开圆顶 连接/断开 */
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = pack('S', $connect);  //unsigned short
       
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        if ($connect == 1)
        {
            return '全开圆顶连接指令：' .udpSend($sendMsg, $this->ip, $this->port); 
        }elseif ($connect == 2)
        {
            return '全开圆顶断开指令：' .udpSend($sendMsg, $this->ip, $this->port); 
        }
    }/*全开圆顶 连接/断开 结束*/

    protected function openDome ($openDome, $param)  /*全开圆顶 打开/关闭  */
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->user,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = pack('S', $openDome);    //unsigned short
    
        $sendMsg = $headInfo . $sendMsg;    //socket发送数据

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
    }/*全开圆顶 打开/关闭 结束*/
}