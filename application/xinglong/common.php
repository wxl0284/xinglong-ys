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

function packHead ($magic,$version,$msg,$length,$sequence,$at,$device)
{
    if (isset($magic))
    {
        $magic = $magic;
    }else{
        $magic = 0;
    }
    $head = pack('L', $magic);  //uint32

    if (isset($version))
    {
        $version = $version;
    }else{
        $version = 0;
    }
    $head .= pack('S', $version);  //uint16


    if (isset($msg))
    {
        $msg = $msg;
    }else{
        $msg = 0;
    }
    $head .= pack('S', $msg);       //uint16

    if (isset($length))
    {
        $length = $length;      //要动态获取
    }else{
        $length = 0;
    }
    $head .= pack('L', $length);        //uint32

    if (isset($sequence))
    {
        $sequence = $sequence;
    }else{
        $sequence = 0;
    }
    $head .= pack('L', $sequence);      //uint32

    $tv_sec = time();
    $head .= pack('L', $tv_sec);        //uint32

    $tv_usec = substr(microtime(), 2, 8);
    $head .= pack('L', $tv_usec);        //uint32  精确到微妙


    if (isset($at))
    {
        $at = $at;
    }else{
        $at = 0;
    }
    $head .= pack('S', $at);        //uint16

     if (isset($device))
    {
        $device = $device;
    }else{
        $device = 0;
    }
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

function packHead2 ($user,$plan,$at,$device,$sequence,$operation)
{
    if (isset($user))
    {
        $user = $user;
    }else{
        $user = 0;
    }
    $head2 = pack('I', $user);   

    if (isset($plan))
    {
        $plan = $plan;
    }else{
        $plan = 0;
    }
    $head2 .= pack('I', $plan);   

    if (isset($at))
    {
        $at = $at;
    }else{
        $at = 0;
    }
    $head2 .= pack('S', $at); 

    if (isset($device))
    {
        $device = $device;
    }else{
        $device = 0;
    }
    $head2 .= pack('S', $device);   

    if (isset($sequence))
    {
        $sequence = $sequence;
    }else{
        $sequence = 0;
    }
    $head2 .= pack('I', $sequence);  

    if (isset($operation))
    {
        $operation = $operation;
    }else{
        $operation = 0;
    }
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
