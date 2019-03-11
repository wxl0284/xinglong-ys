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
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port
    protected $command_length = [//各指令长度
        'connect' => 50,
        'findhome' => 48,
        'set_filterPos' => 50,
    ];
    protected $operation = [//各指令之编号
        'connect' => 1,
        'findhome' => 3,
        'set_filterPos' => 2,
    ];
    //接收参数，根据不同参数，向不同望远镜的滤光片指令
    public function sendCommand ()
    {
        //首先判断是否已登录
        if ($this->ajaxAuthErr == 'not_log')
        {
            return '请先登录再进行操作!';
        }

        //判断是否有权限
        if ($this->ajaxAuthErr == 'no_auth')
        {
            return '您无权进行此操作!';
        }

        $postData = $this->input; //接受表单数据
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
                $this->ip = $ip['at50'];
                $this->port = $port;
                break;
            case '60cm':
                $this->at = 37;
                $this->ip = $ip['at60'];
                $this->port = $port;
                break;
            case '80cm':
                $this->at = 36;
                $this->ip = $ip['at80'];
                $this->port = $port;
                break;
            case '85cm':
                $this->at = 35;
                $this->ip = $ip['at85'];
                $this->port = $port;
                break;
            case '100cm':
                $this->at = 34;
                $this->ip = $ip['at100'];
                $this->port = $port;
                break;
            case '126cm':
                $this->at = 33;
                $this->ip = $ip['at126'];
                $this->port = $port;
                break;
            case '216cm':
                $this->at = 32;
                $this->ip = $ip['at216'];
                $this->port = $port;
                break;
            default:
                return '提交的望远镜参数有误!';
        }

        $command = $postData['command']; //要执行的指令
        switch ($command) {
            case 'connect':
                return $this->connect (1, 'connect');
            case 'disConnect':
                return $this->connect (2, 'connect');
            case 'findhome':
                return $this->findHome (1,'findhome');
            case 'set_filterPos':
                return $this->set_filterPos ($postData, 'set_filterPos');
            default:
                break;
        }
    }//接收参数，根据不同参数，向不同望远镜的filter指令 结束////////

    protected function connect ($connect, $param)  /*滤光片连接/断开*/
    {
        $sendMsg = pack('S', $connect ); //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        
        if ($connect == 1)
        {
            return '滤光片连接指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }elseif ($connect == 2)
        {
            return '滤光片断开指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }
    }/*滤光片连接/断开 结束*/

    protected function findHome ($find_home, $param) /*滤光片找零*/
    {
        $sendMsg = pack('S', $find_home ); //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        //socket发送数据
        $sendMsg = $headInfo;
        return '滤光片找零指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*滤光片找零 结束*/

    protected function set_filterPos ($postData, $param)  /*滤光片位置*/
    {
        $sendMsg = pack('S', $postData['filter_pos']);  //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '滤光片位置指令：'. udpSend($sendMsg, $this->ip, $this->port);
    }/*滤光片位置 结束*/
}