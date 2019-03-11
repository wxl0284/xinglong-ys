<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
use think\Db;

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
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port
    protected $command_length = [//各指令长度
        'connect' => 50,
        'findhome' => 48,
        'stop' => 48,
        'set_objPos' => 56,
        'fix_speed' => 56,
        'tempera_enable' => 50,
        'temperature_coef' => 56,
    ];
    protected $operation = [//各指令之编号
        'connect' => 1,
        'findhome' => 7,
        'stop' => 4,
        'set_objPos' => 2,
        'fix_speed' => 3,
        'tempera_enable' => 5,
        'temperature_coef' => 6,
    ];
    
    /*接收参数，根据不同参数，向不同望远镜的调焦器-focus 发指令*/
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

        $command = $postData['command']; //获取提交的指令

        switch ($command) { //根据不同参数 调用相应方法发送指令
            case 'connect':
                return $this->connect (1, 'connect');
            case 'disConnect':
                return $this->connect (2, 'connect');
            case 'findhome':
                return $this->btn_command ('findhome');
            case 'stop':
                return $this->btn_command ('stop');
            case 'set_objPos':
                return $this->set_position ($postData, 'set_objPos');
            case 'fix_speed':
                return $this->set_speed ($postData, 'fix_speed');
            case 'tempera_enable':
                return $this->enable ($postData, 'tempera_enable');
            case 'temperature_coef':
                return $this->set_coefficient ($postData, 'temperature_coef');
            default:
                break;
        }

    } /*接收参数，根据不同参数，向不同望远镜的调焦器-focus 发指令  结束*/

    protected function connect ($connect, $param)  /*调焦器连接-断开*/
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
        
        $sendMsg = pack('S', $connect); //unsigned short

        $sendMsg = $headInfo . $sendMsg;
        
        if ($connect == 1)
        {
           return '调焦器连接指令：' . udpSend($sendMsg, $this->ip, $this->port);
        }elseif ($connect == 2)
        {
            return '调焦器断开指令：' . udpSend($sendMsg, $this->ip, $this->port);
        } 
    }/*调焦器连接-断开 结束*/

    protected function btn_command ($param)  /*调焦器 找零 停止运动*/
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
        
        $sendMsg = $headInfo;  //socket发送数据
        if ( $param == 'stop' )
        {
            return '调焦器停止指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }elseif ( $param == 'findhome' ){
            return '调焦器找零指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }
    }/*调焦器 停止运动 结束*/
 
    protected function set_position ($postData, $param)  /*调焦器 设置目标位置*/
    {
        $res = Db::table('focusconf')->where('teleid', $postData['at'])->field('maxvalue, minvalue')->find(); //获取该望远镜的focusconf表中的:最大值 最小值   
        if ( !is_numeric($postData['pos']) || $postData['pos'] > $res['maxvalue'] || $postData['pos'] < $res['minvalue'])
        {
            return '目标位置超限!';
        }

        $sendMsg = pack('d', $postData['pos']);    //double64
    
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
		return '设置目标位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*调焦器 设置目标位置 结束*/

    protected function set_speed ($postData, $param)  /*调焦器 设置恒速转动*/
    {
        $res = Db::table('focusconf')->where('teleid', $postData['at'])->field('maxspeed')->find(); //获取该望远镜的focusconf表中的:最大速度   
        if ( !is_numeric($postData['speed']) || $postData['speed'] > $res['maxspeed'] || $postData['speed'] <= 0)
        {
            return '调焦器速度值超限!';
        }
        $sendMsg = pack('d', $postData['speed']);   //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
		return '恒速转动指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*调焦器 设置恒速转动 结束*/

 
    protected function enable ($postData, $param)   /*调焦器 使能温度补偿*/
    {
        $sendMsg = pack('S', $postData['enable']);      //uint16

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
		return '使能温度补偿指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*调焦器 使能温度补偿 结束*/

    
    protected function set_coefficient ($postData, $param) /*调焦器 使能温度补偿系数*/
    {
       if (!is_numeric($postData['coefficient']) || $postData['coefficient'] <= 0 )
       {
           return '温度补偿系数超限!';
       }
        $sendMsg = pack('d', $postData['coefficient']);      //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
		return '温度补偿系数：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*调焦器 使能温度补偿系数 结束*/

}