<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cookie;
use think\Db;

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
    protected $ip = '';  //中控通信 ip
    protected $port = '';  //中控通信 port
    protected $command_length = [//各指令长度
        'connect' => 50,
        'stop' => 48,
        'open' => 50,
        'set_objPos' => 56,
        'set_speed' => 56,
        'set_shade' => 56,
        //'set_action' => 50,	//钟
        'set_action' => 52,	//胡 加2
    ];
    protected $operation = [//各指令之编号
        'connect' => 1,
        'stop' => 5,
        'open' => 6,
        'set_objPos' => 2,
        'set_speed' => 4,
        'set_shade' => 3,
        'set_action' => 7,
    ];
    
    //接收参数，根据不同参数，向不同望远镜的随动圆顶发指令
    public function sendCommand ()
    {
        //首先判断是否已登录
        if ($this->ajaxAuthErr == 'not_log')
        {
            return '请先登录再进行操作!';
        }

        //首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

       
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
        //根据不同参数 调用相应方法发送指令
        switch ($command) {
            case 'connect':
                return $this->connect (1, 'connect');
            case 'disConnect':
                return $this->connect (2, 'connect');
            case 'stop':
                return $this->stop ('stop');
            case 'open':
                return $this->scuttle (1, 'open');
            case 'close':
                return $this->scuttle (2, 'open');
            case 'set_objPos':
                return $this->set_domePosition ($postData, 'set_objPos');
            case 'set_speed':
                return $this->rotateSpeed ($postData, 'set_speed');
            case 'set_shade':
                return $this->shadePosition ($postData, 'set_shade');
            case 'set_action':
                return $this->shadeAction ($postData, 'set_action');
            default:
                break;
        }
    }/*接收参数，根据不同参数，向不同望远镜的随动圆顶发指令 结束*/

    protected function connect ($connect, $param) /*随动圆顶 连接/断开*/
    {
        $sendMsg = pack('S', $connect); //unsigned short

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        if ($connect == 1)
        {
            return '随动圆顶连接指令：' .udpSend($sendMsg, $this->ip, $this->port);	
        }elseif ($connect == 2)
        {
            return '随动圆顶断开指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }
    }/*随动圆顶 连接/断开*/

    protected function stop ($param) /*随动圆顶 停止运动*/
    {
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo; //socket发送数据
        return '随动圆顶停止运动指令:' .udpSend($sendMsg, $this->ip, $this->port);
    }/*随动圆顶 停止运动 结束*/

    protected function scuttle ($scuttle, $param)  /*随动圆顶 开/关天窗*/
    {
        $sendMsg = pack('S', $scuttle);
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
        
        $sendMsg = $headInfo . $sendMsg; //socket发送数据
        if ($scuttle == 1)
        {
            return '打开天窗指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }elseif ($scuttle == 2)
        {
            return '关闭天窗指令：' .udpSend($sendMsg, $this->ip, $this->port);
        }
    }/*随动圆顶 开/关天窗 结束*/

    protected function set_domePosition ($postData, $param)  /*随动圆顶 目标方位*/
    { 
        $sendMsg = pack('d', $postData['position']);    //double64

        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);

        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '目标方位指令：' .udpSend($sendMsg, $this->ip, $this->port);
    }/*随动圆顶 目标方位 结束*/

     protected function rotateSpeed ($postData, $param)    /*随动圆顶 转动速度*/
     { 
        $res = Db::table('sdomeconf')->where('teleid', $postData['at'])->field('maxspeed')->find();  //获取该望远镜的sdomeconf表中的:最大转动速度
        if ( !is_numeric($postData['speed']) || $postData['speed'] <= 0 || $postData['speed'] > $res['maxspeed'] )
        {
            return '转动速度值超限!';
        }
 
        $sendMsg = pack('d', $postData['speed']);    //double64
 
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
 
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '转动速度指令：' .udpSend($sendMsg, $this->ip, $this->port);
     }/*随动圆顶 转动速度 结束*/

     protected function shadePosition ($postData, $param)  /*随动圆顶 风帘位置*/
     {  
        if ( !is_numeric($postData['position']) || $postData['position'] < 0 || $postData['position'] > 90 )
        {
            return '风帘位置的值超限！';
        }
     
        $sendMsg = pack('d', $postData['position']);  //double64
 
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
        
        $sendMsg = $headInfo . $sendMsg;   //socket发送数据
        return '风帘位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
     }/*随动圆顶 风帘位置 结束*/
     
     protected function shadeAction ($postData, $param)  /*随动圆顶 风帘运动*/
     {  
        //$sendMsg = pack('S', $postData['action']); //unsigned short 钟
        $sendMsg = pack('I', $postData['action']); //unsigned int 胡2018.7.11
 
        $headInfo = packHead($this->magic,$this->version,$this->msg,$this->command_length[$param],$this->sequence,$this->at,$this->device);

        $headInfo .= packHead2 ($this->userId,$this->plan,$this->at,$this->device,$this->sequence,$this->operation[$param]);
        
        $sendMsg = $headInfo . $sendMsg;  //socket发送数据
        return '风帘运动指令：' .udpSend($sendMsg, $this->ip, $this->port);
     }/*随动圆顶 风帘运动 结束*/
}