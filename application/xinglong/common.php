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
}


///////////////////////////////////////////////////
//udpSend(): udp协议下socket发送数据到后面控制端
//$sendMsg: 要发送的字串
//$ip : 目的端ip
//$port: 目的端程序端口
function udpSend ($sendMsg = '', $ip='192.168.160.154', $port = '4747')
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

function udpSendPlan ($sendMsg = '', $ip='192.168.160.154', $port = '4747')
{ 
  $handle = stream_socket_client("udp://{$ip}:{$port}", $errno, $errstr); 

  if( !$handle ){
   return "ERROR: {$errno} - {$errstr}\n"; 
  } 


  $res = fwrite($handle, $sendMsg);
  
   fclose($handle); 
  
}

//以下为太阳、月亮等位置计算函数
date_default_timezone_set('PRC'); //设置时区
/*
  ymd(); 获取年、月、日
*/
function ymd ()
{
	$ymd = date('Y:m:d');
	$ymd = explode(':', $ymd);
	return $ymd;	
}

/*
  epoch(); 得到历元
*/
function epoch ()
{
	$ymd = ymd();
	return $ymd[0] + (($ymd[1]- 1)*30.3 + $ymd[2])/365;	
}

/*
  ModifiedJulianDay(); 返回修正儒略日
*/
function ModifiedJulianDay ($year, $mon, $day, $hour)
{
	$a = 10000*$year + 100*$mon + $day;
	if($mon <= 2)
	{
		$mon += 12;
		$year --;
	}
	
	if($a <= 15821004.1)
	{
		$b = ($year + 4716)/4 - 1179 - 2;
	}else{
		$b = $year/400 - $year/100 + $year/4;
	}
	
	$a = 365*$year - 679004;
	$c = 30.6001*($mon + 1);
	$jDay = $a + $b + $c + $hour/24;
	return $jDay;
}

/*
  getJDay(); 获取儒略日
*/
function getJDay ()
{
	$hms = date('H:i:s');
	$hms = explode(':', $hms);
	$mSec = substr(microtime(), 2, 8);
	$h = $hms[0] + $hms[1]/60 + ($hms[2] + $mSec/1000)/3600;
	$ymd = ymd();
	return ModifiedJulianDay($ymd[0], $ymd[1], $ymd[2], $h);
}

//太阳、月亮等位置计算函数 结束