<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Session;
use think\Request;
use think\Cookie;
use think\Db;
use think\Config;
use think\Cache;

//60cm号望远镜控制器
class At60 extends Controller
{
	public $ip = '';  //socket通信 ip
	public $port = '';  //socket通信 port
    //检测是否登录////////////////////////////////////////////////
    public function _initialize ()
    {
		//初始化soket的ip和端口
        $this->ip = Config::get('ip');
        $this->port = Config::get('port');
		
		//未登录
        if (!Session::has('login'))
        {
			if (Request::instance()->isAjax())
			{
				return '请完成登录后，再进行相关操作！';
			}
            $request = Request::instance();
            Cookie::set('url', $request->url(true));
            $this->error('请完成登录后，再进行相关操作！', '/');
        }   
    }
    
    //显示60cm望远控制镜页面////////////////////////////////////////
    public function index ()
    {
		//判断60cm望远镜是否已配置，否则先配置并存入缓存
		$at60Page = Cache::get('at60Page');
		
		if(!isset($at60Page['data']))
		{
			$this->redirect('/xinglong/page_config/at60config');
		}else{
			$this->assign([
                'cacheStr' => $at60Page,
            ]);
		}
		return view('at60-m');  
		
    }
    
    //at60 望远镜 接管指令////////////////////////////////////////////
    public function at60TakeOver ()
    {
		//权限和是否登录的判断
		
		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
		
		
        if (input('takeOver') !== 1)
        {
            echo '数据无效！';return;
        }
        //接管望远镜的 指令
        //定义全局$sequence 此变量在packHead()函数中要使用
        if (Cookie::has('sequence'))
        {
            $sequence = Cookie::get('sequence');
            Cookie::set('sequence', $sequence+1);
        }else{
            Cookie::set('sequence', 1);
            $sequence = 0;
        }
        if (input('takeOver') != 1 )
        {
            echo '提交数据无效！';
            return;
        } 
        //望远镜
        $at = 37;
        $msg = 11; $magic = 439041101; $version = 1;
        
        $length = 28 +20;      //只发送头部前面的信息
        $headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device=0);
        dump(input('takeOver'));return;
        $takeOver = trim(input('takeOver'));
        if (!preg_match('/^[0-1]$/', $takeOver))
        {
           echo '连接指令无效!'; return; 
        }
       //接管指令数据体
       $user = 1; 
       $occupation = input('takeOver');
       $start = time();
       $end = time() + 1000;
       $priority = 1;
       $sendMsg = pack('L', $user);     //uint32
       $sendMsg .= pack('S', $at);     //uint16
       $sendMsg .= pack('S', $occupation);     //uint16
       $sendMsg .= pack('L', $start);     //uint32
       $sendMsg .= pack('L', $end);     //uint32
       $sendMsg .= pack('L', $priority);     //uint32
        //socket发送数据
        $sendMsg = $headInfo . $sendMsg;
        echo udpSend($sendMsg, $this->ip, $this->port);	
    }
	
    //at60 发送转台指令的方法/////////////////////////////////
    public function at60GimbalSendData ()
    {
		//判断权限和登录
		
		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
		
		//定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$sequence = Cookie::get('sequence');
			Cookie::set('sequence', $sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$sequence = 0;
		}

		if (!Request::instance()->param())
		{
			echo '提交数据失败！';
			return;
		} 
		//60cm 望远镜
		$at  = 37; 
		 //转台
		$device = 64;            
		$msg = 6; $magic = 439041101; $version = 1;
		//头部后部数据
		$user = 1;  $plan = 0; 
		
		//发送连接指令
		if (($connect=input('connect')) !== null)
		{
			if (!preg_match('/^[1-2]$/', $connect)) //匹配1和2
			{
			   echo '连接指令无效!'; return; 
			}
			$length = 48 + 2;
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=1);
		  
			$sendMsg = pack('S', $connect);  //unsigned short
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			if ($connect == 1)
			{
				echo '连接指令：'. udpSend($sendMsg, $this->ip, $this->port);
			}elseif ($connect == 2)
			{
				echo '断开连接指令：'. udpSend($sendMsg, $this->ip, $this->port);
			}
			
		}elseif (($findHome=input('findHome')) !== null)	//发送 找零指令
		{
			if ($findHome != 1)
			{
			   echo '找零指令：格式错误!'; return; 
			}
			$length = 48 + 2;    //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=2);
			$sendMsg = pack('S', $findHome);  //unsigned short
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '找零指令：'. udpSend($sendMsg, $this->ip, $this->port);
		}elseif (($park=input('park')) !== null)	//发送 复位指令
		{
			if ($park != 1)
			{
			   echo '复位指令：格式错误!'; return; 
			}
			
			$length = 48;    //该结构体总长度, 只发送头部信息
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=10);
			//socket发送数据
			$sendMsg = $headInfo;
			echo '复位指令：'. udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (($stop=input('stop')) !== null)	//发送 停止指令
		{
			if ($stop != 1)
			{
			   echo '停止指令：格式错误!'; return; 
			}
			$length = 48;    //该结构体总长度, 只发送头部信息
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=8);
			//socket发送数据
			$sendMsg = $headInfo;
			echo '停止指令：'. udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (($EmergenceStop=input('EmergenceStop')) !== null) //急停指令
		{
			if ($EmergenceStop != 1)
			{
			   echo '急停指令：格式错误!'; return; 
			}
			$length = 48;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);
			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=15);
			
			//soc4ket发送数据
			$sendMsg = $headInfo;
			echo '急停指令：'.udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 1)  //跟踪恒星指令
		{
			//var_dump(input());return;
			$length = 48 + 20;    //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=3);
			
			//处理赤经数据
			$postData = input();
			if (!preg_match('/^\d{1,2}$/', $postData['rightAscension1']) || $postData['rightAscension1'] > 24 || $postData['rightAscension1'] < 0)
			{
				return '赤经之小时参数超限!';
			}
			
			if (!preg_match('/^\d{1,2}$/', $postData['rightAscension2']) || $postData['rightAscension2'] > 59 || $postData['rightAscension2'] < 0)
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
			if (!preg_match('/^\d{1,2}$/', $postData['declination1']) || $postData['declination1'] > 90 || $postData['declination1'] < -90)
			{
				return '赤纬之小时参数超限!';
			}
			
			if (!preg_match('/^\d{1,2}$/', $postData['declination2']) || $postData['declination2'] > 59 || $postData['declination2'] < 0)
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
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '跟踪恒星指令：'. udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 2)  //设置目标名称
		{
			$length = 48 + 50;    //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=4);
			
			if (($objectName=trim(input('objectName'))) !== '')
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
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '设置目标名称指令：' . udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 3)  //指向固定位置
		{
			$length = 48 + 16;    //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=5);
			
			if (($azimuth=trim(input('azimuth'))) !== '') //方位
			{
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $azimuth))
				{
					echo '方位必须为数字！';return;
				}
				$sendMsg = pack('d', $azimuth);     //double64
			}else{
				$sendMsg = pack('d', 0);
			}
			
			if (($elevation=trim(input('elevation')))  !== '') //俯仰
			{
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $elevation))
				{
					echo '俯仰必须为数字！';return;
				}
				$sendMsg .= pack('d', $elevation);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '指向固定位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 4) 
		{//轴3指向固定位置
			$length = 48 + 8;    //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=6);
			$slewDerotator=trim(input('slewDerotator'));
			if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $slewDerotator))
			{
				echo '轴3指向固定位置必须为数字！';return;
			}
			$sendMsg = pack('d', $slewDerotator);  //double64
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '轴3指向固定位置指令：'. udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 5)
		{//设置轴3工作模式
			$length = 48 + 10;    //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=7);
			
			if (($mode=trim(input('mode'))) !== '')
			{//工作模式
				if (!preg_match('/^\d{1,15}$/', $mode))
				{
					echo '轴三工作模式只能是数字！';return;
				}
				$sendMsg = pack('S', $mode);     //unsigned short
			}else{
				$sendMsg = pack('S', 0);
			}
			
			if (($polarizingAngle=trim(input('polarizingAngle'))) !== '')
			{//轴三 起偏角
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $polarizingAngle))
				{
					echo '轴三起偏角只能是数字！';return;
				}
				$sendMsg .= pack('d', $polarizingAngle);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '轴3工作模式指令：'. udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('command') == 6) //速度修正
		{
			$length = 48 + 10;    //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=9);
			
			if (($axis=trim(input('axis'))) !== '')  //轴
			{
				if (!preg_match('/^\d{1}$/', $axis))
				{
					echo '速度修正:轴必须为数字！';return;
				}
				$sendMsg = pack('S', $axis);   //unsigned short
			}else{
				$sendMsg = pack('S', 0);
			}
			
			if (($correction=trim(input('correction'))) !== '')
			{//修正值
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $correction))
				{
					echo '速度修正值必须为数字！';return;
				}
				$sendMsg .= pack('d', $correction);   //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '位置修正指令：' .udpSend($sendMsg, $this->ip, $this->port);		
		}elseif (input('command') == 7)  //恒速运动
		{
			$length = 48 + 10;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=11);
			
			if (($FixedMoveAxis=trim(input('FixedMoveAxis'))) !== '')
			{//恒速运动  轴
				if (!preg_match('/^\d{1,5}$/', $FixedMoveAxis))
				{
					echo '恒速运动(轴参数)只能是数字！';return;
				}
				$sendMsg = pack('S', $FixedMoveAxis);  //unsigned short
			}else{
				$sendMsg = pack('S', 0);
			}
			
			if (($FixedMoveSpeed=trim(input('FixedMoveSpeed'))) !== '')
			{//恒速运动  速度
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $FixedMoveSpeed))
				{
					echo '恒速运动(速度)只能是数字！';return;
				}
				$sendMsg .= pack('d', $FixedMoveSpeed);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '恒速运动指令：' .udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('command') == 8) //位置修正
		{
			$length = 48 + 10;     //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=12);
			
			if (($PositionCorrectAxis=trim(input('PositionCorrectAxis'))) !== '')
			{//位置修正 轴
				if (!preg_match('/^\d{1,5}$/', $PositionCorrectAxis))
				{
					echo '位置修正(轴参数)只能是数字！';return;
				}
				$sendMsg = pack('S', $PositionCorrectAxis);  //16位
			}else{
				$sendMsg = pack('S', 0);
			}
			
			if (( $PositionCorrectVal=trim(input('PositionCorrectVal'))) !== '')
			{// 修正值
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $PositionCorrectVal))
				{
					echo '位置修正值只能是数字！';return;
				}
				$sendMsg .= pack('d', $PositionCorrectVal);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '位置修正指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 9) //镜盖操作
		{
			$length = 48 + 2;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=13);
			
			$openCover = trim(input('openCover'));
			if (!preg_match('/^[0-2]$/', $openCover))
			{
				return '镜盖操作参数超限！';
			}
			$sendMsg = pack('S', $openCover);     //unsigned short
			
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '镜盖指令：' .udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('command') == 10) //焦点切换镜
		{
			$length = 48 + 2;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=14);
			
			$setFocusType = input('setFocusType');
			if (!preg_match('/^\d{1,5}$/', $setFocusType))
			{
				echo '焦点切换镜只能是数字！';return;
			}
			$sendMsg = pack('S', $setFocusType); //unsigned short
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '焦点切换镜指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 11) //保存同步数据
		{
			$length = 48;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=16);
			
			//socket发送数据
			$sendMsg = $headInfo;
			echo '保存同步数据指令：' .udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('command') == 13) //属性设置
		{
			$length = 48;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=18);
			
			//socket发送数据
			$sendMsg = $headInfo;
			echo '属性设置指令：' .udpSend($sendMsg, $this->ip, $this->port);	
		}
		
		//至此，转台指令发送代码结束
    }

    //at60 发送ccd 指令 //////////////////////////////////////////
    public function at60CcdSendData ()
    {
        //判断权限和登录

		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
		
		//定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$sequence = Cookie::get('sequence');
			Cookie::set('sequence', $sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$sequence = 0;
		}
  
		if (!Request::instance()->param())
		{
			echo '提交数据失败！'; return;
		} 
		//60cm 望远镜
		$at  = 37; 
		 //CCD设备
		$device = 65;            
		$msg = 6; $magic = 439041101; $version = 1; 
		//头部后部数据
		$user = 1;  $plan = 0; 
		
		if (($ccdConnect=input('ccdConnect')) !== null) //ccd 连接指令
		{
			$length = 48 + 2;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=1);
			
			if (!preg_match('/^[1-2]$/', $ccdConnect))
			{
			   echo 'ccd连接指令无效!'; return; 
			}
		   
			$sendMsg = pack('S', $ccdConnect);  //unsigned short
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			if ($ccdConnect == 1)
			{
			   echo 'ccd连接指令：' .udpSend($sendMsg, $this->ip, $this->port); 
			}elseif ($ccdConnect == 2)
			{
				echo '断开ccd指令：' .udpSend($sendMsg, $this->ip, $this->port); 
			}
				
		}elseif (input('StopExpose') == 1)	//停止曝光
		{
			$length = 48 ;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=5);
			
			//socket发送数据        
			$sendMsg = $headInfo;
			echo '停止曝光指令：'. udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('AbortExpose') == 1)	//终止曝光
		{
			$length = 48 ;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=6);
			
			//socket发送数据        
			$sendMsg = $headInfo;
			echo '终止曝光指令：' . udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('command') == 1)	//设置制冷温度
		{
			$length = 48 +8;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=2);
			
			$temperature = input('temperature');
			if (!is_numeric($temperature) || $temperature >20 || $temperature < -80)
			{
				return '制冷温度参数超限！';
			}
			$sendMsg = pack('d', $temperature);     //double64
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '制冷温度指令：' .udpSend($sendMsg, $this->ip, $this->port);		
		}elseif (input('command') == 2) //设置曝光策略
		{
			$length = 48 + 264;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=3);
			
			$postData = input();
			
			if (($validFlag=trim(input('validFlag'))) !== '')    //数据有效标志位
			{
				if (!preg_match('/^\d{1,5}$/', $validFlag))
				{
					echo '数据有效标志位只能是数字！'; return;
				}
				$sendMsg = pack('d', $validFlag); //原来为Q格式
			}else{
				$sendMsg = pack('d', 0); //unsigned long long
			}
			
			if (($startTime=trim(input('startTime'))) !== '')      //起始时刻
			{
				if (!preg_match('/^\d{1,10}$/', $startTime))
				{
					echo '起始时刻只能是数字！'; return;
				}
				$sendMsg .= pack('I', $startTime);     
			}else{
				$sendMsg .= pack('I', 0);   //unsigned int
			}
			
			if (($duration=trim(input('duration'))) !== '')   //曝光时间
			{
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $duration))
				{
					echo '曝光时间只能是数字！'; return;
				}
				$sendMsg .= pack('d', $duration);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (($delay=trim(input('delay'))) !== '')   //延迟时间
			{
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $delay))
				{
					echo '延迟时间只能是数字！'; return;
				}
				$sendMsg .= pack('d', $delay);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if ($postData['objectName'] !== '')  //拍摄目标
			{
				if (preg_match('/[\x{4e00}-\x{9af5} ]/u', $postData['objectName']))
				{
					return '目标名称不能含汉字或空格！';
				}
				$sendMsg .= pack('a48', $postData['objectName']);  //objectName uint8-48
			}else{
				$sendMsg .= pack('a48', '0');
			}
			
			if ($postData['objectType'] !== '')    //拍摄目标类型
			{
				if (!preg_match('/^\d{1}$/', $postData['objectType']))
				{
					return '目标类型参数超限！';
				}
				$sendMsg .= pack('S', $postData['objectType']); 
			}else{
				$sendMsg .= pack('S', 0);  //unsigned short
			}
			
			//处理赤经数据			
			$objectRightAscension = $postData['objectRightAscension1'].':'.$postData['objectRightAscension2'].':'.$postData['objectRightAscension3'];
			
			if ($objectRightAscension !== '::')  
			{//拍摄目标赤经
				if (!preg_match('/^\d{1,2}$/', $postData['objectRightAscension1']) || $postData['objectRightAscension1'] > 24 || $postData['objectRightAscension1'] < 0)
				{
					return '曝光策略:赤经之小时参数超限!';
				}
				
				if (!preg_match('/^\d{1,2}$/', $postData['objectRightAscension2']) || $postData['objectRightAscension2'] > 59 || $postData['objectRightAscension2'] < 0)
				{
					return '曝光策略:赤经之分钟参数超限!';
				}
				
				if (!is_numeric($postData['objectRightAscension3']) || $postData['objectRightAscension3'] >= 60 || $postData['objectRightAscension3'] < 0)
				{
					return '曝光策略:赤经之秒参数超限!';
				}
				
				$objectRightAscension = time2Data($objectRightAscension);
				
				if ($objectRightAscension > 24 || $objectRightAscension < 0) //赤经
				{
					return '曝光策略:赤经参数超限!';
				}else{
					$sendMsg .= pack('d', $objectRightAscension);  //double64
				}
				
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			//处理赤纬数据
			$objectDeclination = $postData['objectDeclination1'].':'.$postData['objectDeclination2'].':'.$postData['objectDeclination3'];
			
			if ($objectDeclination !== '::')    
			{//当前拍摄目标赤纬
				if (!preg_match('/^\d{1,2}$/', $postData['objectDeclination1']) || $postData['objectDeclination1'] > 90 || $postData['objectDeclination1'] < -90)
				{
					return '曝光策略:赤纬之小时参数超限!';
				}
				
				if (!preg_match('/^\d{1,2}$/', $postData['objectDeclination2']) || $postData['objectDeclination2'] > 59 || $postData['objectDeclination2'] < 0)
				{
					return '曝光策略:赤纬之分钟参数超限!';
				}
				
				if (!is_numeric($postData['objectDeclination3']) || $postData['objectDeclination3'] >= 60 || $postData['objectDeclination3'] < 0)
				{
					return '曝光策略:赤纬之秒参数超限!';
				}

				$objectDeclination = time2Data($objectDeclination);
				if ($objectDeclination > 90 || $objectDeclination < -90)
				{ //赤纬
					return '曝光策略:赤纬参数超限!';
				}else{
					$sendMsg .= pack('d', $objectDeclination);//double64
				}
				
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (($objectEpoch=input('objectEpoch')) !== '')    //拍摄目标历元
			{
				if (!preg_match('/^\d{1}$/', $objectEpoch))
				{
					return '目标历元只能是数字！';
				}
				$sendMsg .= pack('S', $objectEpoch);   
			}else{
				$sendMsg .= pack('S', 0);   //unsigned short
			}
			
			if (($objectBand = trim(input('objectBand'))) !== '')      //拍摄波段
			{  
				if (!preg_match('/^[a-zA-Z0-9_-]{1,8}$/', $objectBand))
				{
					echo '当前拍摄波段只能是8位字母数字！'; return;
				}
				$sendMsg .= pack('a8', $objectBand);     //uint8-8
			}else{
				$sendMsg .= pack('a8', '0');
			}
			
			if (($objectFilter=input('objectFilter')) !== '')  //拍摄波段滤光片系统
			{
				if (!preg_match('/^\d{1,5}$/', $objectFilter))
				{
					echo '当前拍摄波段只能是数字！'; return;
				}
				$sendMsg .= pack('S', $objectFilter);     //uint16
			}else{
				$sendMsg .= pack('S', 0);
			}
			
			if (($isSaveImage=input('isSaveImage')) !== '')  //是否保存图像
			{
				if (!preg_match('/^\d{1,5}$/', $isSaveImage))
				{
					echo '是否保存图像只能是数字！'; return;
				}
				$sendMsg .= pack('S', $isSaveImage);     
			}else{
				$sendMsg .= pack('S', 0);   //uint16
			}
			
			if (($weatherGatherTime=trim(input('weatherGatherTime'))) !== '')   
			{//气象数据采集时间
				if (!preg_match('/^\d{1,10}$/', $weatherGatherTime))
				{
					echo '象数据采集时间只能是数字！'; return;
				}
				$sendMsg .= pack('I', $weatherGatherTime);     //uint32
			}else{
				$sendMsg .= pack('I', 0); //unsigned int
			}
			
			if (($temperature1=trim(input('temperature1'))) !== '')    //温度
			{
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $temperature1))
				{
					echo '温度只能是数字！'; return;
				}
				$sendMsg .= pack('d', $temperature1);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (($humidity=trim(input('humidity'))) !== '')    //湿度
			{
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $humidity))
				{
					echo '湿度只能是数字！'; return;
				}
				$sendMsg .= pack('d', $humidity);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (($windSpeed=trim(input('windSpeed'))) !== '')  //风速
			{
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $windSpeed))
				{
					echo '风速只能是数字！'; return;
				}
				$sendMsg .= pack('d', $windSpeed);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (($pressure=trim(input('pressure'))) !== '')      //气压
			{
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $pressure))
				{
					echo '气压只能是数字！'; return;
				}
				$sendMsg .= pack('d', $pressure);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (($skyGatherTime=trim(input('skyGatherTime'))) !== '')   //天气状态采集时间
			{
				if (!preg_match('/^\d{1,10}$/', $skyGatherTime))
				{
					echo '气象数据采集时间只能是数字！'; return;
				}
				$sendMsg .= pack('I', $skyGatherTime);     //uint32
			}else{
				$sendMsg .= pack('I', 0);   //unsigned int
			}
			
			if (($skyState=trim(input('skyState'))) !== '')  //天气状态
			{
				if (!preg_match('/^\d{1,5}$/', $skyState))
				{
					echo '天气状态只能是数字！'; return;
				}
				$sendMsg .= pack('S', $skyState);     
			}else{
				$sendMsg .= pack('S', 0);   //unsigned short
			}
			
			if (($clouds=trim(input('clouds'))) !== '')      //云量
			{
				if (!preg_match('/^\d{1,5}$/', $clouds))
				{
					echo '云量只能是数字！'; return;
				}
				$sendMsg .= pack('S', $clouds);  
			}else{
				$sendMsg .= pack('S', 0); //unsigned short
			}
			
			if (($seeingGatherTime=trim(input('seeingGatherTime'))) !== '')   //视宁度采集时间
			{
				if (!preg_match('/^\d{1,10}$/', $seeingGatherTime))
				{
					echo '视宁度采集时间只能是数字！'; return;
				}
				$sendMsg .= pack('I', $seeingGatherTime);     //uint32
			}else{
				$sendMsg .= pack('I', 0); //unsigned int
			}
			
			if (($seeing=trim(input('seeing'))) !== '')   //视宁度
			{
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $seeing))
				{
					echo '视宁度只能是数字！'; return;
				}
				$sendMsg .= pack('d', $seeing);     //double
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (input('dustGatherTime') !== '')      //粉尘采集时间
			{
				$dustGatherTime = trim(input('dustGatherTime'));
				if (!preg_match('/^\d{1,10}$/', $dustGatherTime))
				{
					echo '粉尘采集时间只能是数字！'; return;
				}
				$sendMsg .= pack('I', $dustGatherTime);     //uint32
			}else{
				$sendMsg .= pack('I', 0);
			}
			
			if (input('dust') !== '')      //粉尘
			{
				$dust = trim(input('dust'));
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $dust))
				{
					echo '粉尘数据只能是数字！'; return;
				}
				$sendMsg .= pack('d', $dust);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (input('AMS') !== '')      //AMS
			{
				$AMS = trim(input('AMS'));
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $AMS))
				{
					echo 'AMS数据只能是数字！'; return;
				}
				$sendMsg .= pack('d', $AMS);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (input('extinctionGatherTime') !== '')    //消光系数采集时间
			{
				$extinctionGatherTime = trim(input('extinctionGatherTime'));
				if (!preg_match('/^\d{1,10}$/', $extinctionGatherTime))
				{
					echo '消光系数采集时间只能是数字！'; return;
				}
				$sendMsg .= pack('I', $extinctionGatherTime);     
			}else{
				$sendMsg .= pack('I', 0); //unsigned int
			}
			
			if (input('rightAscension') !== '')    //赤经
			{
				$rightAscension = trim(input('rightAscension'));
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $rightAscension))
				{
					echo '赤经只能是数字！'; return;
				}
				$sendMsg .= pack('d', $rightAscension);     
			}else{
				$sendMsg .= pack('d', 0); //double64
			}
			
			if (input('declination') !== '')      //赤纬
			{
				$declination = trim(input('declination'));
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $declination))
				{
					echo '赤纬只能是数字！'; return;
				}
				$sendMsg .= pack('d', $declination);     
			}else{
				$sendMsg .= pack('d', 0); //double64
			}
			
			 if (input('band') !== '')      //波段
			{
				$band = trim(input('band'));
				if (!preg_match('/^[a-zA-Z0-9]{1,8}$/', $band))
				{
					echo '波段只能是数字！'; return;
				}
				$sendMsg .= pack('a8', $band);     //band  uint8-8
			}else{
				$sendMsg .= pack('a8', '0');
			}
			
			if (input('extinctionFactor1') !== '')      
			{//消光系数1
				$extinctionFactor1 = trim(input('extinctionFactor1'));
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $extinctionFactor1))
				{
					echo '消光系数1只能是数字！'; return;
				}
				$sendMsg .= pack('d', $extinctionFactor1);     
			}else{
				$sendMsg .= pack('d', 0); //double64
			}
			
			if (input('extinctionFactor2') !== '')      //消光系数2
			{
				 $extinctionFactor2 = trim(input('extinctionFactor2'));
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $extinctionFactor2))
				{
					echo '消光系数2只能是数字！'; return;
				}
				$sendMsg .= pack('d', $extinctionFactor2);     
			}else{
				$sendMsg .= pack('d', 0);//double64
			}
			
			if (input('extinctionFactor3') !== '')      //消光系数3
			{
				 $extinctionFactor3 = trim(input('extinctionFactor3'));
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $extinctionFactor3))
				{
					echo '消光系数3只能是数字！'; return;
				}
				$sendMsg .= pack('d', $extinctionFactor3);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (input('telescopeRightAscension') !== '')      //望远镜赤经
			{
				$telescopeRightAscension = trim(input('telescopeRightAscension'));
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $telescopeRightAscension))
				{
					echo '望远镜赤经只能是数字！'; return;
				}
				$sendMsg .= pack('d', $telescopeRightAscension);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (input('telescopeDeclination') !== '')      //望远镜赤纬
			{
				$telescopeDeclination = trim(input('telescopeDeclination'));
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $telescopeDeclination))
				{
					echo '望远镜赤纬只能是数字！'; return;
				}
				$sendMsg .= pack('d', $telescopeDeclination);     //double64
			}else{
				$sendMsg .= pack('d', 0);
			}
			
			if (input('focusLength') !== '')      //焦距
			{
				$focusLength = trim(input('focusLength'));
				if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $focusLength))
				{
					echo '焦距数据只能是数字！'; return;
				}
				$sendMsg .= pack('d', $focusLength);     //double64
			}else{
					$sendMsg .= pack('d', 0);
			}
			
			if (input('frameNum') !== '')      //帧数
			{
				$frameNum = trim(input('frameNum'));
				if (!preg_match('/^\d{1,10}$/', $frameNum))
				{
					echo '帧数只能是数字！'; return;
				}
				$sendMsg .= pack('I', $frameNum);     //uint32
			}else{
				$sendMsg .= pack('I', 0);
			}
			
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo '设置曝光策略指令：' .udpSend($sendMsg, $this->ip, $this->port);
		
		}elseif (input('command') == 3)	
		{//开始曝光
			$length = 48 +6;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=4);
			
			if (input('isReadFrameSeq') !== '')      //是否读取帧序号
			{
				$isReadFrameSeq = trim(input('isReadFrameSeq'));
				if (!preg_match('/^\d{1}$/', $isReadFrameSeq))
				{
					echo '是否读取帧序号只能是数字！'; return;
				}
				$sendMsg = pack('S', $isReadFrameSeq);    //unsigned short
			}else{
				$sendMsg = pack('S', 0);
			}
			
			if (input('frameSequence') !== '')      //帧序号
			{
				$frameSequence = trim(input('frameSequence'));
				if (!preg_match('/^\d{1,10}$/', $frameSequence))
				{
					echo '起始时间只能是数字！'; return;
				}
				$sendMsg .= pack('I', $frameSequence);    //unsigned int
			}else{
				$sendMsg .= pack('I', 0);
			}
			
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo '开始曝光指令：'. udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('command') == 4)
		{//设置增益
			$length = 48 +4;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=7);
			
			if (input('mode') !== '')      //增益模式
			{
				$mode = trim(input('mode'));
				if (!preg_match('/^\d{1,5}$/', $mode))
				{
					echo '增益模式只能是数字！'; return;
				}
				$sendMsg = pack('S', $mode);     //unsigned short
			}else{
				$sendMsg = pack('S', 0);
			}
			
			if (input('gear') !== '')      //增益档位
			{
				$gear = trim(input('gear'));
				if (!preg_match('/^\d{1,5}$/', $gear))
				{
					echo '增益模式只能是数字！'; return;
				}
				$sendMsg .= pack('S', $gear);     //unsigned short
			}else{
				$sendMsg .= pack('S', 0);
			}
			
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo '设置增益指令：' .udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('command') == 5)   //读出速度模式值
		{
			$length = 48 + 2;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=8);
			
		
			$ReadSpeedMode = trim(input('ReadSpeedMode'));
			if (!preg_match('/^\d{1,10}$/', $ReadSpeedMode))
			{
				echo '读出速度模式值只能是数字！'; return;
			}
			$sendMsg = pack('S', $ReadSpeedMode);     //unsigned short
							
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo '设置读出速度模式指令：' .udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('command') == 6) //转移速度模式值
		{
			$length = 48 + 2;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=9);
			
			$SetTransferSpeed = trim(input('SetTransferSpeed'));
			if (!preg_match('/^\d{1,10}$/', $SetTransferSpeed))
			{
				echo '转移速度模式值只能是数字！'; return;
			}
			$sendMsg = pack('S', $SetTransferSpeed);     //uint32
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo '转移速度模式指令：' .udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('command') == 7)     //设置BIN
		{
			$length = 48 +8;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=10);
			
			if (input('BinX') !== '')      //binx
			{
				$BinX = trim(input('BinX'));
				if (!preg_match('/^\d{1,10}$/', $BinX))
				{
					echo 'BinX值只能是数字！'; return;
				}
				$sendMsg = pack('I', $BinX);     //uint32
			}else{
				$sendMsg = pack('I', 0);
			}
			
			if (input('BinY') !== '')      //BinY
			{
				$BinY = trim(input('BinY'));
				if (!preg_match('/^\d{1,10}$/', $BinY))
				{
					echo 'BinY值只能是数字！'; return;
				}
				$sendMsg .= pack('I', $BinY);     //uint32
			}else{
				$sendMsg .= pack('I', 0);
			}
			
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo '设置BIN指令：' .udpSend($sendMsg, $this->ip, $this->port);	
		}elseif (input('command') == 8 )      //设置ROI 指令
		{
			$length = 48 + 16;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=11);
			
			if (input('startX') !== '')      //startX
			{
				$startX = trim(input('startX'));
				if (!preg_match('/^\d{1,10}$/', $startX))
				{
					echo 'startX值只能是数字！'; return;
				}
				$sendMsg = pack('I', $startX);     //unsigned int
			}else{
				$sendMsg = pack('I', 0);
			}
			
			if (input('startY') !== '')      //unsigned int
			{
				$startY = trim(input('startY'));
				if (!preg_match('/^\d{1,10}$/', $startY))
				{
					echo 'startY值只能是数字！'; return;
				}
				$sendMsg .= pack('I', $startY);     //unsigned int
			}else{
				$sendMsg .= pack('I', 0);
			}
			
			if (input('imageWidth') !== '')      //imageWidth
			{
				$imageWidth = trim(input('imageWidth'));
				if (!preg_match('/^\d{1,10}$/', $imageWidth))
				{
					echo 'imageWidth值只能是数字！'; return;
				}
				$sendMsg .= pack('I', $imageWidth);     //unsigned int
			}else{
				$sendMsg .= pack('I', 0);
			}
			
			if (input('imageHeight') !== '')      //imageWidth
			{
				$imageHeight = trim(input('imageHeight'));
				if (!preg_match('/^\d{1,10}$/', $imageHeight))
				{
					echo 'imageHeight值只能是数字！'; return;
				}
				$sendMsg .= pack('I', $imageHeight);     //unsigned int
			}else{
				$sendMsg .= pack('I', 0);
			}
			
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo '设置Roi指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 9)  //设置快门指令
		{
			$length = 48 + 2;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=12);
			
			$shutter = input('shutter');
			if (!preg_match('/^\d{1,10}$/', $shutter))
			{
				echo 'shutter值只能是数字！'; return;
			}
			$sendMsg = pack('S', $shutter);     //unsigned short
	
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo '设置快门指令：'. udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 10)     //设置帧转移
		{
			$length = 48 + 2;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=13);
			
			$isFullFrame = trim(input('isFullFrame'));
			if (!preg_match('/^\d{1,10}$/', $isFullFrame))
			{
				echo 'isFullFrame值只能是数字！'; return;
			}
			$sendMsg = pack('S', $isFullFrame);     //unsigned short
	
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo '设置帧转移指令：'. udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 11 )     //SetEM     指令
		{
			$length = 48 + 6;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=14);
			
			if (input('isEM') !== '')      //isEM
			{
				$isEM = trim(input('isEM'));
				if (!preg_match('/^\d{1,5}$/', $isEM))
				{
					echo 'isEM值只能是数字！'; return;
				}
				$sendMsg = pack('S', $isEM);     //unsigned short
			}else{
				$sendMsg = pack('S', 0);
			}
			
			if (input('eMValue') !== '')      //eMValue
			{
				$eMValue = trim(input('eMValue'));
				if (!preg_match('/^\d{1,10}$/', $eMValue))
				{
					echo 'eMValue值只能是数字！'; return;
				}
				$sendMsg .= pack('I', $eMValue);     //unsigned int
			}else{
				$sendMsg .= pack('I', 0);
			}
			
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo 'SetEM指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 12 )  //isNoiseFilter
		{
			$length = 48 + 2;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=15);
			
			$isNoiseFilter = trim(input('isNoiseFilter'));
			if (!preg_match('/^\d{1,5}$/', $isNoiseFilter))
			{
				echo 'isNoiseFilter值只能是数字！'; return;
			}
			$sendMsg = pack('S', $isNoiseFilter);     //unsigned short
				
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo 'CMOS noise filter指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 13 )     //SetBaseline 指令
		{
			$length = 48 + 6;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=16);
			
			if (input('isBaseline') !== '')      //isBaseline
			{
				$isBaseline = trim(input('isBaseline'));
				if (!preg_match('/^\d{1,5}$/', $isBaseline))
				{
					echo 'isBaseline值只能是数字！'; return;
				}
				$sendMsg = pack('S', $isBaseline);     //unsigned short
			}else{
				$sendMsg = pack('S', 0);
			}
			
			if (input('baselineValue') !== '')      //baselineValue
			{
				$baselineValue = trim(input('baselineValue'));
				if (!preg_match('/^\d{1,10}$/', $baselineValue))
				{
					echo 'baselineValue值只能是数字！'; return;
				}
				$sendMsg .= pack('I', $baselineValue);     //unsigned int
			}else{
				$sendMsg .= pack('I', 0);
			}
				
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo 'Baseline指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 14) //set over scan
		{
			$length = 48 + 2;      //该结构体总长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=17);
			
			$isOverScan = trim(input('isOverScan'));  //isOverScan
			if (!preg_match('/^\d{1,5}$/', $isOverScan))
			{
				echo 'isOverScan值只能是数字！'; return;
			}
			$sendMsg = pack('S', $isOverScan);     //unsigned short
			
			//socket发送数据        
			$sendMsg = $headInfo . $sendMsg;
			echo 'Over Scan指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}
		
		//至此，ccd指令发送代码结束        
    }
	
	//at60 发送调焦器 指令///////////////////////////////////////////
	public function at60FocusSendData ()
	{
		//判断权限和登录

		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
		//定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$sequence = Cookie::get('sequence');
			Cookie::set('sequence', $sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$sequence = 0;
		}
  
		if (!Request::instance()->param())
		{
			echo '提交数据失败！'; return;
		} 
		//60cm 望远镜
		$at  = 37; 
		 //调焦器设备
		$device = 69;            
		$msg = 6; $magic = 439041101; $version = 1; 
		//头部后部数据
		$user = 1;  $plan = 0;
		
		if (input('focusConnect') !== null)
		{//调焦器 连接指令
			$length = 48 + 2;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=1);
			
			$focusConnect = input('focusConnect');
			$sendMsg = pack('S', $focusConnect);  //unsigned short
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			if ($focusConnect == 1)
			{
				echo '调焦器连接指令：' .udpSend($sendMsg, $this->ip, $this->port);
			}elseif ($focusConnect == 2)
			{
				echo '调焦器断开指令：' .udpSend($sendMsg, $this->ip, $this->port);
			}
			
		}elseif (input('focusStop') == 1)	//调焦器 停止运动
		{
			$length = 48 ;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=4);
			
			//socket发送数据
			$sendMsg = $headInfo;
			echo '调焦器停止指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('findHome') == 1)  //找零
		{
			$length = 48 ;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=7);
			
			//socket发送数据
			$sendMsg = $headInfo;
			echo '调焦器找零指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 1)	//设置目标位置
		{
			$length = 48 + 8;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=2);
			$setPosition=trim(input('setPosition'));
			if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $setPosition))
			{
				echo '目标位置的值必须是数字！';return; 
			}
			$sendMsg = pack('d', $setPosition);    //double64
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '设置目标位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 2) //恒速转动
		{
			$length = 48 + 8;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=3);
			
			$speed = trim(input('speed'));
			if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $speed))
			{
				echo '恒速转动的值必须是数字！';return; 
			}
			$sendMsg = pack('d', $speed);    //double64
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '恒速转动指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 3) //温度补偿
		{
			$length = 48 + 2;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=5);
			
			$enable = trim(input('enable'));
			if (!preg_match('/^\d{1,5}$/', $enable))
			{
				echo '使能温度补偿的值必须是数字！';return; 
			}
			$sendMsg = pack('S', $enable);      //uint16
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '使能温度补偿指令：'. udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 4)   //使能温度补偿系数
		{
			$length = 48 + 8;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=6);
			
			$coefficient = trim(input('coefficient'));
			if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $coefficient))
			{
				echo '温度补偿系数的值必须是数字！';return; 
			}
			$sendMsg = pack('d', $coefficient);      //double64
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '温度补偿系数：' .udpSend($sendMsg, $this->ip, $this->port);
		}
		
		//调焦器指令发送 完毕
	}
	
	//随动圆顶 指令发送/////////////////////////////////////////////
	public function at60sDomeSendData ()
	{
		//判断权限和登录

		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
		
		//定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$sequence = Cookie::get('sequence');
			Cookie::set('sequence', $sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$sequence = 0;
		}
  
		if (!Request::instance()->param())
		{
			echo '提交数据失败！'; return;
		} 
		//60cm 望远镜
		$at  = 37; 
		 //slave dome设备
		$device = 67;            
		$msg = 6; $magic = 439041101; $version = 1; 
		//头部后部数据
		$user = 1;  $plan = 0;
		
		if (($sDomeConnect=input('sDomeConnect')) !== null)
		{//随动圆顶 连接指令
			$length = 48 +2;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=1);
			
			$sendMsg = pack('S', $sDomeConnect); //unsigned short
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			if ($sDomeConnect == 1)
			{
				echo '随动圆顶连接指令：' .udpSend($sendMsg, $this->ip, $this->port);	
			}elseif ($sDomeConnect == 2)
			{
				echo '随动圆顶断开指令：' .udpSend($sendMsg, $this->ip, $this->port);
			}
						
		}elseif (input('sDomeStop') !== null)	//停止运动
		{
			$length = 48;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=5);
			
			//socket发送数据
			$sendMsg = $headInfo;
			echo '随动圆顶停止运动指令:' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (($OpenScuttle=input('OpenScuttle')) !== null) //天窗
		{
			$length = 48 + 2;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=6);
				 
			if (!preg_match('/^\d{1,10}$/', $OpenScuttle))
			{
				echo '开关天窗值必须是数字！';return; 
			}
			$sendMsg = pack('S', $OpenScuttle); //unsinged short
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			if ($OpenScuttle == 1)
			{
				echo '打开天窗指令：' .udpSend($sendMsg, $this->ip, $this->port);
			}elseif ($OpenScuttle == 2)
			{
				echo '关闭天窗指令：' .udpSend($sendMsg, $this->ip, $this->port);
			}
			
		}elseif (input('command') == 1)
		{//目标方位
			$length = 48 + 8;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=2);
			
			$domePosition = trim(input('domePosition')); //double64
			if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $domePosition))
			{
				echo '目标方位值必须是数字！';return;
			}
			$sendMsg = pack('d', $domePosition);
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '目标方位指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 2)
		{//转动速度
			$length = 48 + 8;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=4);
			
			$RotateSpeed = trim(input('RotateSpeed'));    //double64
			if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $RotateSpeed))
			{
				echo '转动速度值必须是数字！';return; //double64
			}
			$sendMsg = pack('d', $RotateSpeed);
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '转动速度指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 3)
		{//风帘位置
			$length = 48 + 8;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=3);
			
			$shadePosition = trim(input('shadePosition'));
			if (!preg_match('/^-?\d+(\.\d{0,15})?$/', $shadePosition))
			{
				echo '风帘位置的值必须是数字！';return; //double64
			}
		
			$sendMsg = pack('d', $shadePosition);
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '风帘位置指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (input('command') == 4)
		{//风帘运动
			$length = 48 + 2;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=7);
			
			$shadeAction = trim(input('shadeAction'));    //unsigned short
			if (!preg_match('/^\d{1,10}$/', $shadeAction))
			{
				echo '风帘运动值必须是数字！';return; 
			}
			$sendMsg = pack('S', $shadeAction);
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '风帘运动指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}
		
		//随动圆顶 指令发送结束	
	}
	
	//全开圆顶 指令发送//////////////////////////////////////////
	public function at60fDomeSendData ()
	{
		//判断权限和登录

		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
        
		//定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$sequence = Cookie::get('sequence');
			Cookie::set('sequence', $sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$sequence = 0;
		}
  
		if (!Request::instance()->param())
		{
			echo '提交数据失败！';
			return;
		} 
		//望远镜
		$at  = 37; 
		 //望远镜子设备
		$device = 68;            
		$msg = 6; $magic = 439041101; $version = 1;
		//头部后部数据
		$user = 1;  $plan = 0;
		
		if (($fDomeConnect=input('fDomeConnect')) !== null)
		{//连接指令
			$length = 48 + 2;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=1);
			
			$sendMsg = pack('S', $fDomeConnect);  //unsigned short
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			if ($fDomeConnect == 1)
			{
				echo '全开圆顶连接指令：' .udpSend($sendMsg, $this->ip, $this->port);
			}elseif ($fDomeConnect == 2)
			{
				echo '全开圆顶断开指令：' .udpSend($sendMsg, $this->ip, $this->port);
			}
			
		}elseif (($openDome=input('openDome')) !== null)
		{//打开圆顶  dump($openDome);return;
			$length = 48 + 2;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=2);
			
			if (!preg_match('/^\d{1,10}$/', $openDome))
			{
				echo '打开圆顶的值必须是数字！';return; 
			}
			$sendMsg = pack('S', $openDome);    //unsigned short
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			if ($openDome == 0)
			{
				echo '全开圆顶关闭指令：'. udpSend($sendMsg, $this->ip, $this->port);
			}elseif ($openDome == 1)
			{
				echo '全开圆顶打开指令：'. udpSend($sendMsg, $this->ip, $this->port);
			}elseif ($openDome == 2)
			{
				echo '全开圆顶停止指令：'. udpSend($sendMsg, $this->ip, $this->port);
			}
			
		}
		
		//全开圆顶 指令发送结束
	}
	
	//滤光片 指令发送/////////////////////////////////////////////
	public function at60FilterSendData ()
	{
		//判断权限和登录

		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
		
		//定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$sequence = Cookie::get('sequence');
			Cookie::set('sequence', $sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$sequence = 0;
		}
  
		if (!Request::instance()->param())
		{
			echo '提交数据失败！';
			return;
		} 
		//望远镜
		$at  = 37; 
		 //望远镜子设备
		$device = 66;            
		$msg = 6; $magic = 439041101; $version = 1;
		//头部后部数据
		$user = 1;  $plan = 0;
		
		if (($filterConnect=input('filterConnect')) !== null)
		{//连接指令
			$length = 48 +2;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=1);

			$sendMsg = pack('S', $filterConnect ); //unsigned short
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			
			if ($filterConnect == 1)
			{
				echo '滤光片连接指令：' .udpSend($sendMsg, $this->ip, $this->port);
			}elseif ($filterConnect == 2)
			{
				echo '滤光片断开指令：' .udpSend($sendMsg, $this->ip, $this->port);
			}
			
		}elseif (input('filterFindHome') == 1)
		{//找零
			$length = 48;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=3);
			
			//socket发送数据
			$sendMsg = $headInfo;
			echo '滤光片找零指令：' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif (($filterPos=input('filterPos')) !== null)
		{//滤光片位置
			$length = 48 + 2;      //结构体长度
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);

			$headInfo .= packHead2 ($user,$plan,$at,$device,$sequence,$operation=2);
			
			if (!preg_match('/^\d{1,5}$/', $filterPos))
			{
				echo '滤光片位置必须是数字！';return;
			}
			$sendMsg = pack('S', $filterPos);  //unsigned short
			//socket发送数据
			$sendMsg = $headInfo . $sendMsg;
			echo '滤光片位置指令：'. udpSend($sendMsg, $this->ip, $this->port);
		}
			
			//滤光片 指令发送完毕
	}
	
	//观测计划 指令发送/////////////////////////////////////////////
    public function planTest()
    {
        //判断权限和登录

		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
		
		//定义全局$sequence 此变量在packHead()函数中要使用
            if (Cookie::has('sequence'))
            {
                $sequence = Cookie::get('sequence');
                Cookie::set('sequence', $sequence+1);
            }else{
                Cookie::set('sequence', 1);
                $sequence = 0;
            }
     
            //望远镜
            $at  = 37;
             //望远镜子设备
            $device = 66;           
            $msg = 8; $magic = 439041101; $version = 1;
            //头部后部数据
            $user = 1;  $plan = 0; $length =28 + 204;
           
            $headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);
            $sendMsg = pack('S', $at);
            $sendMsg .= pack('a48', '01'); //user
            $sendMsg .= pack('a48', '02'); //project
            $sendMsg .= pack('a48', 'star'); //target
            $sendMsg .= pack('I', 0); //type
            $sendMsg .= pack('d', 10.0); //rightAscension
            $sendMsg .= pack('d', 45.0); //declination
            $sendMsg .= pack('I', 1); //epoch
            $sendMsg .= pack('d', 5); //exposureTime
            $sendMsg .= pack('d', 1); //delayTime
            $sendMsg .= pack('I', 5); //exposureCount
            $sendMsg .= pack('a8', 'I'); //filter
            $sendMsg .= pack('S', 0); //gain
            $sendMsg .= pack('S', 1); //bin
            $sendMsg .= pack('S', 0); //readout
           
            //socket发送数据
            $sendMsg = $headInfo . $sendMsg;
           
            echo '观测计划：'. udpSend($sendMsg, $this->ip, $this->port);
			
			unset($sendMsg);
			
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);
            $sendMsg = pack('S', $at);
            $sendMsg .= pack('a48', '01'); //user
            $sendMsg .= pack('a48', '02'); //project
            $sendMsg .= pack('a48', 'star2'); //target
            $sendMsg .= pack('I', 0); //type
            $sendMsg .= pack('d', 10.2); //rightAscension
            $sendMsg .= pack('d', 45.0); //declination
            $sendMsg .= pack('I', 1); //epoch
            $sendMsg .= pack('d', 4); //exposureTime
            $sendMsg .= pack('d', 1); //delayTime
            $sendMsg .= pack('I', 3); //exposureCount
            $sendMsg .= pack('a8', 'B'); //filter
            $sendMsg .= pack('S', 1); //gain
            $sendMsg .= pack('S', 1); //bin
            $sendMsg .= pack('S', 1); //readout
           
            //socket发送数据
            $sendMsg = $headInfo . $sendMsg;
           
            echo '观测计划：'. udpSend($sendMsg, $this->ip, $this->port);
			
			unset($sendMsg);
			
			$headInfo = packHead($magic,$version,$msg,$length,$sequence,$at,$device);
            $sendMsg = pack('S', $at);
            $sendMsg .= pack('a48', '01'); //user
            $sendMsg .= pack('a48', '02'); //project
            $sendMsg .= pack('a48', 'star3'); //target
            $sendMsg .= pack('I', 0); //type
            $sendMsg .= pack('d', 10.4); //rightAscension
            $sendMsg .= pack('d', 45.0); //declination
            $sendMsg .= pack('I', 1); //epoch
            $sendMsg .= pack('d', 6); //exposureTime
            $sendMsg .= pack('d', 1); //delayTime
            $sendMsg .= pack('I', 4); //exposureCount
            $sendMsg .= pack('a8', 'U'); //filter
            $sendMsg .= pack('S', 2); //gain
            $sendMsg .= pack('S', 1); //bin
            $sendMsg .= pack('S', 2); //readout
           
            //socket发送数据
            $sendMsg = $headInfo . $sendMsg;
           
            echo '观测计划：'. udpSend($sendMsg, $this->ip, $this->port);
			
    }//观测计划 指令发送 结束///////////////////////////////////////
	
	
	//导入计划文件/////////////////////////////////////////////////
	public function importPlan ()
	{
		//判断权限和登录

		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
		
		// 获取表单上传文件 
		$file = request()->file('plan');
	
		//上传文件验证
		$result = $this->validate(
				['file' => $file],
				['file' => 'file|require|fileExt:txt|fileSize:4096000000|fileMime:text/plain'],
				['file.require' => '请选择上传文件',
				 'file.fileExt' => '文件后缀名必须为txt',
				 'file.fileSize' => '文件大小超出限制',
				 'file.fileMime' => '文件格式或文件内容不符合要求']);
		if (true !== $result)
		{
			return $result;
		}
		// 移动到框架应用根目录/public/uploads/ 目录下
		$dir = date('Y/m/d', time());
		$dir = str_replace('/', '', $dir);
		$name = time(). cookie('login');
		//文件存储存储路径
		$path = ROOT_PATH . 'public' . DS . 'uploads/'.$dir;
		$info = $file->move($path, $name.'.txt');

		if($info){
			//获取计划文件的字符编码
			$planData = file_get_contents($path.'/'.$name.'.txt');
			
			$encoding = mb_detect_encoding($planData, array('GB2312','GBK','UTF-16','UCS-2','UTF-8','BIG5','ASCII'));
			
			$fileData=array(); //存储计划的每行数据
			
			$file = fopen($path.'/'.$name.'.txt', 'r'); //只读方式打开文件
			
			while(!feof($file)) //输出文本中所有的行，直到文件结束为止。
			{
				$str = trim(fgets($file));   //fgets()从文件指针中读取一行
				if ($encoding != false)
				{
					$str = iconv($encoding, 'UTF-8', $str);

					  if ($str)
					  {
						  $fileData[] = $str;
					  }
				}else{
					$str = mb_convert_encoding ($str, 'UTF-8','Unicode');

					  if ($str)
					  {
						  $fileData[] = $str;
					  }
				}
			}
			fclose($file);
			array_filter($fileData);  //删除$fileData中等值为false的元素
			
			//遍历每行数据
			foreach ($fileData as $k => $data)
			{
				//解析每行计划的每个参数
				$row = explode('#', $data);
				if (count($row) !== 12)	//计划的参数  个数不对
				{
					return '第'.($k+1).'行计划参数的数量错误';
				}else{
					$plan["plan".$k]["target"] = trim($row[0]);
					$plan["plan".$k]["type"] = trim($row[1]);
					
					//处理赤经
					$rightAscension = explode (':', trim($row[2]));
					$plan["plan".$k]["rightAscension1"] = $rightAscension [0];
					$plan["plan".$k]["rightAscension2"] = $rightAscension [1];
					$plan["plan".$k]["rightAscension3"] = $rightAscension [2];
					
					//处理赤经
					$declination = explode (':', trim($row[3]));
					$plan["plan".$k]["declination1"] = $declination [0];
					$plan["plan".$k]["declination2"] = $declination [1];
					$plan["plan".$k]["declination3"] = $declination [2];
					
					$plan["plan".$k]["declination"] = trim($row[3]);
					$plan["plan".$k]["epoch"] = trim($row[4]);
					$plan["plan".$k]["exposureTime"] = trim($row[5]);
					$plan["plan".$k]["delayTime"] = trim($row[6]);
					$plan["plan".$k]["exposureCount"] = trim($row[7]);
					$plan["plan".$k]["filter"] = trim($row[8]);
					$plan["plan".$k]["gain"] = trim($row[9]);
					$plan["plan".$k]["bin"] = trim($row[10]);
					$plan["plan".$k]["readout"] = trim($row[11]);
					$plan["plan".$k]["id"] = $k+1;
				}

			}
			return json_encode($plan);
			
		}else{// 上传失败获取错误信息
			return $file->getError();
		}		
	}//导入计划文件 结束//////////////////////////////////////////////
	
	//获取计划数据 验证并发送计划数据///////////////////////////
	public function savePlan ()
	{
		//为了避免重新登录时 重新填写计划数据 忽略此项判断 仅验证权限一项
		/* if (!Session::has('login'))
		{
			return '为确保操作者为同一人，请再次登录！';
		} */

		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
		
		//获取计划数据
		if (!($planData = input()))
		{
			return '网络异常,请重新提交计划!';
		}
		
		//定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$sequence = Cookie::get('sequence');
			Cookie::set('sequence', $sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$sequence = 0;
		}
 
		//望远镜
		$at  = 37;
		 //望远镜子设备
		$device = 66;           
		$msg = 8; $magic = 439041101; $version = 1;
		//头部后部数据
		$user = 1;  $plan = 0; $length =28 + 208;
	   
		$headInfo = planPackHead($magic,$version,$msg,$length,$sequence,$at,$device);
		
		//halt($planData['planData'][1]['bin']);
		//验证计划数据
		$planNum = count($planData['planData']);
		//halt($planNum);
		for ($i = 0; $i < $planNum; $i ++) //循环提交上来的 每一条计划
		{
			//每条指令的tag
			$sendMsg = pack('I', $i+1);  //unsigned int 
			
			$sendMsg .= pack('S', $at);
            $sendMsg .= pack('a48', '01'); //user
            $sendMsg .= pack('a48', '02'); //project
			
			$target = trim($planData['planData'][$i]['target']);
			
			$sendMsg .= pack('a48', $target); 
		
			
			//验证目标类型
			$type = trim($planData['planData'][$i]['type']);
			if($type === '')//目标类型未填写
			{
				return '请选择第'. ($i+1) .'条计划:目标类型!'; 
			}else{ //目标类型 有值
				if (preg_match('/^[0-9]{1}$/', $type)) //数字类型
				{
					$sendMsg .= pack('I', $type); 
				}else{//直接为汉字类型数据
					if(!in_array($type, ['太阳','月亮','恒星','彗星',
						'行星', '卫星', '固定位置', '本底', '暗流', '平场']))
					{
						return '第'. ($i+1) .'条计划:目标类型有误!';
					}else{
						if($type == '恒星')
						{
							$sendMsg .= pack('I', 0); 
						}elseif($type == '太阳'){
							$sendMsg .= pack('I', 1); 
						}elseif($type == '月亮'){
							$sendMsg .= pack('I', 2); 
						}elseif($type == '彗星'){
							$sendMsg .= pack('I', 3); 
						}elseif($type == '行星'){
							$sendMsg .= pack('I', 4); 
						}elseif($type == '卫星'){
							$sendMsg .= pack('I', 5); 
						}elseif($type == '固定位置'){
							$sendMsg .= pack('I', 6); 
						}elseif($type == '本底'){
							$sendMsg .= pack('I', 7); 
						}elseif($type == '暗流'){
							$sendMsg .= pack('I', 8); 
						}elseif($type == '平场'){
							$sendMsg .= pack('I', 9); 
						}
					}
				}
			}
			
			//处理赤经数据
			$rightAscension = trim($planData['planData'][$i]['rightAscension1']).':'.trim($planData['planData'][$i]['rightAscension2']).':'.trim($planData['planData'][$i]['rightAscension3']);
			
			//验证 rightAscension
			if ($rightAscension !== '::')
			{//赤经：数字 0-24
				if (!is_numeric(str_replace(':', '', $rightAscension)))
				{
					return '第'. ($i+1) .'条计划:赤经数据有误!';
				}
				
				$rightAscension = time2Data($rightAscension);
				if ($rightAscension > 90 || $rightAscension < -90)
				{
					return '第'. ($i+1) .'条计划:赤经数据有误!';
				}
				$sendMsg .= pack('d', $rightAscension);
			}else{
				return '第'. ($i+1) .'条计划:赤经数据有误!';
			} 
			
			//处理赤纬数据
			$declination = trim($planData['planData'][$i]['declination1']).':'.trim($planData['planData'][$i]['declination2']).':'.trim($planData['planData'][$i]['declination3']);
			
			//验证 赤纬
			if ($declination !== '::')
			{
				if (!is_numeric(str_replace(':', '', $declination)))
				{
					return '第'. ($i+1) .'条计划:赤纬数据有误!';
				}
				
				$declination = time2Data($declination);
				if ($declination > 90 || $declination < -90)
				{
					return '第'. ($i+1) .'条计划:赤纬数据有误!';
				}
				$sendMsg .= pack('d', $declination);
			}else{
				return '第'. ($i+1) .'条计划:赤纬数据有误!';
			}
			
			//验证历元 
			$epoch = trim($planData['planData'][$i]['epoch']);
			if($epoch === '')//目标类型未填写
			{
				return '请选择第'. ($i+1) .'条计划:历元!'; 
			}else{ //历元 有值
				if (preg_match('/^[0-9]{1}$/', $epoch)) //数字类型
				{
					$sendMsg .= pack('I', $epoch); 
				}else{//直接为汉字类型数据
					if(!in_array($epoch, ['real','Real','j2000','J2000','B1950', 'b1950','j2050', 'J2050']))
					{
						return '第'. ($i+1) .'条计划:历元数据有误!';
					}else{
						if($epoch == 'real' || $epoch == 'Real')
						{
							$sendMsg .= pack('I', 0); 
						}elseif($epoch == 'j2000' || $epoch == 'J2000'){
							$sendMsg .= pack('I', 1); 
						}elseif($epoch == 'b1950' || $epoch == 'B1950'){
							$sendMsg .= pack('I', 2); 
						}elseif($epoch == 'j2050' || $epoch == 'J2050'){
							$sendMsg .= pack('I', 3); 
						}
					}
				}
			}
			
			//验证 曝光时间
			$exposureTime = trim($planData['planData'][$i]['exposureTime']);
			if($exposureTime === '')
			{
				return '请填写第'. ($i+1) .'条计划:曝光时间!'; 
			}else{
				if(true)//曝光时间最大值？
				{
					$sendMsg .= pack('d', $exposureTime); 
				}else{
					return '第'. ($i+1) .'条计划:曝光时间数据有误!'; 
				}
			}
			
			//验证 delayTime
			$delayTime = trim($planData['planData'][$i]['delayTime']);
			if($delayTime === '')
			{
				return '请填写第'. ($i+1) .'条计划:delayTime!'; 
			}else{
				if(true)//曝光时间最大值？
				{
					$sendMsg .= pack('d', $delayTime); 
				}else{
					return '第'. ($i+1) .'条计划:delayTime数据有误!'; 
				}
			}
			
			//验证 exposureCount
			$exposureCount = trim($planData['planData'][$i]['exposureCount']);
			if($exposureCount === '')
			{
				return '请填写第'. ($i+1) .'条计划:曝光数量!'; 
			}else{
				if(preg_match('/^[0-9]+$/', $exposureCount) && $exposureCount>0 && $exposureCount <= 100)//曝光时间最大值？
				{
					$sendMsg .= pack('I', $exposureCount); 
				}else{
					return '第'. ($i+1) .'条计划:曝光数量数据有误!'; 
				}
			}
			
			//验证 滤光片数据
			$filter = trim($planData['planData'][$i]['filter']);
			if($filter === '')//目标类型未填写
			{
				return '请选择第'. ($i+1) .'条计划:滤光片!'; 
			}else{ //filter有值
				if (preg_match('/^[0-9]{1}$/', $filter)) //数字类型
				{
					$sendMsg .= pack('a8', $filter); 
				}else{//直接为汉字类型数据
					if(!in_array($filter, ['U','V','B','R','I', 'u','b', 'v', 'r', 'i']))
					{
						return '第'. ($i+1) .'条计划:滤光片数据有误!';
					}else{
						if($filter == 'u' || $filter == 'U')
						{
							$sendMsg .= pack('a8', 'U'); 
						}elseif($filter == 'b' || $filter == 'B'){
							$sendMsg .= pack('a8', 'B'); 
						}elseif($filter == 'V' || $filter == 'v'){
							$sendMsg .= pack('a8', 'V'); 
						}elseif($filter == 'R' || $filter == 'r'){
							$sendMsg .= pack('a8', 'R'); 
						}elseif ($filter == 'i' || $filter == 'I'){
							$sendMsg .= pack('a8', 'I'); 
						}
					}
				}
			}
			
			//验证增益 
			$gain = trim($planData['planData'][$i]['gain']);
			if($gain === '')//目标类型未填写
			{
				return '请填写第'. ($i+1) .'条计划:增益值!'; 
			}else{
				if (preg_match('/^[0-9]{1}$/', $gain))
				{
					$sendMsg .= pack('S', $gain); 
				}else{
					return '第'. ($i+1) .'条计划:增益数据有误!';
				}
			}
			
			//验证Bin 
			$bin = trim($planData['planData'][$i]['bin']);
			if($bin === '')//目标类型未填写
			{
				return '请填写第'. ($i+1) .'条计划:增益值!'; 
			}else{
				if (preg_match('/^[0-9]{1}$/', $bin))
				{
					$sendMsg .= pack('S', $bin); 
				}else{
					return '第'. ($i+1) .'条计划:Bin数据有误!';
				}
			}
			
			//验证读出速度 
			$readout = trim($planData['planData'][$i]['readout']);
			if($readout === '')//目标类型未填写
			{
				return '请填写第'. ($i+1) .'条计划:读出速度!'; 
			}else{
				if (preg_match('/^[0-9]{1}$/', $readout))
				{
					$sendMsg .= pack('S', $readout); 
				}else{
					return '第'. ($i+1) .'条计划:读出速度数据有误!';
				}
			}
			
			//发送 计划数据
            $sendMsg = $headInfo . $sendMsg;
           
            udpSendPlan($sendMsg, $this->ip, $this->port); //无返回值	
		} //结束循环提交上来的计划
		
		return '观测计划发送完毕!';

	} //发送观测计划 结束/////////////////////////////////////////////
	
	//观测计划的 开始 停止 下一个 ////////////////////////////////
	public function at60PlanOption ()
	{
		//为了避免重新登录时 重新填写计划数据 忽略此项判断 仅验证权限一项
		/* if (!Session::has('login'))
		{
			return '为确保操作者为同一人，请再次登录！';
		} */

		/*if (!Session::has('role'))
		{
			return '您没有此权限！';
		}*/
		
		//获取提交数据
		if (!input())
		{
			return '网络异常,请重新提交计划!';
		}
		
		//定义全局$sequence 此变量在packHead()函数中要使用
		if (Cookie::has('sequence'))
		{
			$sequence = Cookie::get('sequence');
			Cookie::set('sequence', $sequence+1);
		}else{
			Cookie::set('sequence', 1);
			$sequence = 0;
		}
 
		//望远镜
		$at  = 37;
		 //望远镜子设备
		$device = 66;           
		$msg = 9; $magic = 439041101; $version = 1;
		//头部后部数据
		$user = 1;  $plan = 0; $length =28 + 16; //长度有变
	   
		$headInfo = planPackHead($magic,$version,$msg,$length,$sequence,$at,$device);
		
		$input = input();
		if($input['planOption'] == 'planStart')
		{
			$sendMsg = pack('I', 1);
			$sendMsg .= pack('I', $input['mode']);
			$sendMsg .= pack('I', $input['start']);
			$sendMsg .= pack('I', 0);
			
			$sendMsg = $headInfo . $sendMsg;
			echo '计划开始:' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif($input['planOption'] == 'planStop'){
			$sendMsg = pack('I', 2);
			$sendMsg .= pack('I', $input['mode']);
			$sendMsg .= pack('I', $input['start']);
			$sendMsg .= pack('I', 0);
			
			$sendMsg = $headInfo . $sendMsg;
			echo '计划停止:' .udpSend($sendMsg, $this->ip, $this->port);
		}elseif($input['planOption'] == 'planNext'){
			$sendMsg = pack('I', 3);
			$sendMsg .= pack('I', $input['mode']);
			$sendMsg .= pack('I', $input['start']);
			$sendMsg .= pack('I', 0);
			
			$sendMsg = $headInfo . $sendMsg;
			echo '下一条计划:' .udpSend($sendMsg, $this->ip, $this->port);
		}
		
		
	}
	//观测计划的 开始 停止 下一个 结束//////////////////////////////
}