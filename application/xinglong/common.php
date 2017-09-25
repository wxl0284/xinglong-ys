<?php
/*packHead()函数功能：对结构体头部信息打包
$at: 望远镜
$device：望远镜子设备
$sequence:
$length:消息长度
$msg:指令编号
$version:版本
$magic: 
return $head; pack完成的二进制数据
*/

function packHead ($magic=0,$version=0,$msg=0,$length=0,$sequence=0,$at=0,$device=0)
{
    $head = pack('L', $magic);  //uint32

    $head .= pack('S', $version);  //uint16

    $head .= pack('S', $msg);       //uint16

    $head .= pack('L', $length);        //uint32

    $head .= pack('L', $sequence);      //uint32

    $tv_sec = time();
    $head .= pack('L', $tv_sec);        //uint32

    $tv_usec = substr(microtime(), 2, 8);
    $head .= pack('L', $tv_usec);        //uint32  精确到微妙

    $head .= pack('S', $at);        //uint16

    $head .= pack('S', $device);    //uint16
    return $head;
}
///////////////////////////////////////////////////////////
/*packHead2()函数功能：对结构体头部信息第2部分打包
$user: 操作者
$plan：观测计划
$at: 望远镜
$device: 望远镜子设备
$sequence2: 
$operation: 操作编号
return $head2; pack完成的二进制数据
*/

function packHead2 ($user=1,$plan=0,$at=0,$device=0,$sequence=0,$operation=0)
{
    $head2 = pack('I', $user);   

    $head2 .= pack('I', $plan);   

    $head2 .= pack('S', $at); 

    $head2 .= pack('S', $device);   

    $head2 .= pack('I', $sequence);  

    $head2 .= pack('I', $operation);      //uint32

    return $head2;
}///////////////////////////////////////////////////////


/*planPackHead()函数功能：对结构体头部信息打包
$at: 望远镜
$device：望远镜子设备
$sequence:
$length:消息长度
$msg:指令编号
$version:版本
$magic: 
return $head; pack完成的二进制数据
*/

function planPackHead ($magic=0,$version=0,$msg=0,$length=0,$sequence=0,$at=0,$device=0)
{
	$head = pack('L', $magic);  //uint32

    $head .= pack('S', $version);  //uint16

    $head .= pack('S', $msg);       //uint16

    $head .= pack('L', $length);        //uint32

    $head .= pack('L', $sequence);      //uint32

    $tv_sec = time();
    $head .= pack('L', $tv_sec);        //uint32

    $tv_usec = substr(microtime(), 2, 8);
    $head .= pack('L', $tv_usec);        //uint32  精确到微妙

    $head .= pack('S', $at);        //uint16

    $head .= pack('S', $device);    //uint16
	 
    return $head;
}///////////////////////////////////////////////////////

///////////////////////////////////////////////////
//udpSend(): udp协议下socket发送数据到后面控制端
//$sendMsg: 要发送的字串
//$ip : 目的端ip
//$port: 目的端程序端口
function udpSend ($sendMsg = '', $ip, $port)
{ 
  $handle = stream_socket_client("udp://{$ip}:{$port}", $errno, $errstr); 

  if( !$handle ){
   return "ERROR: {$errno} - {$errstr}\n"; 
  } 


  $res = fwrite($handle, $sendMsg);
  
   fclose($handle); 

  if($res)
  {
    return '发送成功!';
  }else{
    return '发送失败!';
  } 
  
} 

///////////////////////////////////////////////////
//udpSendPlan(): udp协议下socket发送数据到后面控制端
//$sendMsg: 要发送的字串
//$ip : 目的端ip
//$port: 目的端程序端口
//循环发送每条计划，故函数中不能return

function udpSendPlan ($sendMsg = '', $ip, $port)
{ 
  $handle = stream_socket_client("udp://{$ip}:{$port}", $errno, $errstr); 

  if( !$handle ){
   return "ERROR: {$errno} - {$errstr}\n"; 
  } 


  $res = fwrite($handle, $sendMsg);
  
   fclose($handle); 
  
}

//数据库的时角/恒星时/赤经/赤纬 转为时间 的函数///////////////////
/*
* data2Time() 
* param:$data :数据库读出的数据
* return：00:01:02.3 (即00时1分2秒点3)
*/
function data2Time ($data)
{
	if ($data >= 0)
	{
		$sign = '+';
	}else{
		$sign = '-';
		$data = -1 * $data;
	}
	
	$hour = (int) $data;
	$min =  (int) (($data - $hour) * 60);
	$sec =  (int) (($data - $hour - $min/60) * 36000);
	
	$sec = $sec/10;
	
	if($hour < 10)
	{
		$hour = '0' . $hour;
	}
	
	if($min < 10)
	{
		$min = '0' . $min;
	}
	
	if($sec < 10)
	{
		$sec = '0' . $sec;
	}
	
	return $res = $sign . $hour . ':' . $min . ':' . $sec;
}

//数据库的时角/恒星时/赤经/赤纬 转为时间 的函数///////////////////

//时间 转为/赤经/赤纬(浮点数) 的函数///////////////////
/*
* time2Data() 
* param:$data :用户输入（-12:12:12.33）
* return：-6.66666 
*/
function time2Data ($data)
{
	//如果为0:0:0.0 返回0
	if(str_replace(':', '', $data) == 0)
	{
		return 0;
	}

	$hms = explode(':', $data);
	if(strpos($data, '-') !== false)
	{
		$hour = abs($hms[0]);
		$min = floatval ($hms[1]/60);
		$sec = floatval ($hms[2]/3600);

		$res = $hour + $min + $sec;
		return floatval($res * -1);
	}else{
		$hour = abs($hms[0]);
		$min = $hms[1]/60;
		$sec = $hms[2]/3600;

		$res = $hour + $min + $sec;
		return floatval ($res);
	}
}

//时间 转为/赤经/赤纬(浮点数) 的函数///////////////////
