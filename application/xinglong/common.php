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

//以下为太阳、月亮等位置计算函数////////////////////////////////////
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

/*
 * sunTimeOfEle(): 太阳在某个高度时的本地时
 * 参数:
 *   $from      -- 与ele对应的,还未到达正午的本时间,单位:小时
 *   $to        -- 与ele对应的,已经过了正午的本地时间:单位:小时
 *   $mjd       -- 修正儒略日
 *   $ele       -- 太阳高度角
 * 返回:
 *    0        -- 位于可计算范围内,完成后from,to保存正确的对应时间
 *   +1        -- 极夜
 *   +2        -- 极昼
 *   -1        -- 错误
 *   ? m_dLatitude  变量信息
*/

function sunTimeOfEle (&$from, &$to, $mjd, $ele)
{
	if($ele > 90 || $ele < -90) {// 高度角超过[-90,90]范围
		return -1;
	}
	
	$ra = 0; $dec = 0;  // 太阳的赤道坐标
	$cosha = 0;
	$ha = 0;
	$LtNoon = 0;	// 正午时间
	
	sunPosition($ra, $dec, $mjd);
	$cosha = ((sin($ele) - sin(m_dLatitude) * sin($dec))) / (cos(m_dLatitude) * cos($dec));
	
	if($cosha < -1)
	{
		return 1;
	}else if($cosha > 1)
	{
		return 2;
	}
	
	$ha = acos($cosha)/15;	// 小时为单位
	$LtNoon = $ra - GetGMST0($mjd); //GetGMST0 : 缺失的函数
	
	$from = ReduceZeroMax($LtNoon - $ha, 24);
	$to   = ReduceZeroMax($LtNoon + $ha, 24);
	
	return 0;
}

/** sunTwilight() : 计算日出日落的时间.
 * \param[out] sunrise 太阳升起的时间, 单位: 小时
 * \param[out] sunset 太阳落山的时间, 单位: 小时
 * \param[in] mjd 修正儒略日
 * \param[in] type 查看何种类型的日出/日落时间. 
 *   1 -- 太阳中心到达地平线. 
 *   2 -- 太阳上沿到达地平线. 
 *   3 -- 太阳中心到达地平线,加入了大气折射修正.
 *   4 -- 太阳上沿到达地平线,加入了大气折射修正. 
 *   5 -- 通用晨昏时. 
 *   6 -- 海上晨昏时. 
 *   7 -- 业余天文观测晨昏时. 
 *   8 -- 专业天文观测晨昏时. 
 * \return 返回值分为四种情况:
 *  0 : 位于可计算范围内, 函数返回后, from和to保存正确的对应时间;
 * +1 : 极夜. 
 * +2 : 极昼. 
 * -1 : 错误
 */
 
 function sunTwilight (&$sunRise, &$sunSet, $mjd, $type)
 {
	$h = 0;

	switch($type)
	{
	case 1:	// 太阳中心到达地平线
		$h = 0;
		break;
	case 2:	// 太阳上沿到达地平线
		$h = -0.25;
		break;
	case 3:	// 太阳中心到达地平线,加入大气折射修正
		$h = -0.583;
		break;
	case 4:	// 太阳上沿到达地平线,加入大气折射修正
		$h = -0.833;
		break;
	case 5:	// 通用
		$h = -6;
		break;
	case 6:	// 海上
		$h = -12;
		break;
	case 7:	// 业余
		$h = -15;
		break;
	case 8:	// 专业
		$h = -18;
		break;
	default:
		return -1;	// 未定义类型
	}
	
	return sunTimeOfEle($sunRise, $sunSet, $mjd, $h);
 }
 
/*
*reduceZeroMax() :
*/

function reduceZeroMax ($x, $maxV)
{
	return $x - floor($x/$maxV)*$maxV;
}

/*
*localMeanSiderialTime() :
*/

function localMeanSiderialTime ($mjd)
{
	$mjd0 = 0;
	$t = 0;
	$ut = 0;		// 世界时
	$gmst = 0;	// 格林威治平恒星时
	$lmst = 0;	// 本地平恒星时
	
	$mjd0 = (int) $mjd;
	$ut = ($mjd - $mjd0)*24;
	$t = ($mjd0 - 51544.5)/36525;	// 儒略纪元
	$gmst = 6.697374558 + 1.0027379093 * $ut + (8640184.812866 + (0.093104 - 0.0000062 * $t) * $t) * $t / 3600;
	$lmst = $gmst + m_dLongitude / 15;
	
	return reduceZeroMax($lmst, 24);
}

/*
*sunPosition() : 太阳相对地心的位置
*/

function sunPosition (&$ra, &$dec, $mjd)
{
	$E = sunEccentricAnomaly($mjd); //sunEccentricAnomaly 函数 ？
	$e = sunEccentricity($mjd); //sunEccentricity 函数 ?
	$w = perihelion($mjd); //perihelion 函数 ?
	$eo = eclipticObliquity($mjd); //eclipticObliquity 函数 ?
	$x = 0; $y = 0; $z = 0;
	$r = 0;
	$v = 0;
	$lon = 0;
	$xequat = 0; $yequat = 0; $zequat = 0;

	$x = cos($E/180*pi()) - $e;
	$y = sin($E/180*pi()) * sqrt(1 - $e * $e);

	$r = sqrt($x * $x + $y * $y);
	$v = atan2($y, $x);
	$lon = reduceAngle($v + $w); //reduceAngle 函数 ?
 
	$x = $r * cos($lon/180*pi());
	$y = $r * sin($lon/180*pi());

	$xequat = $x;
	$yequat = $y * cos($eo/180*pi()) + $z * sin($eo/180*pi(); //$z=0 ?
	$zequat = $y * sin($eo/180*pi()) + $z * cos($eo/180*pi()); //$z=0 ?

	rect2Sphere($r, $ra, $dec, $xequat, $yequat, $zequat); //rect2Sphere 函数 ？
	$ra /= 15;
}

/*
*eq2AziEle() : 赤经赤纬转方位俯仰
*/

function eq2AziEle (&$azi, &$ele, $ha, $dec)
{
	$q =0;
	$cosz = 0;
	$sina = 0;
	$cosa = 0;

	$ha *= 15;	// 时角从小时转换为角度
	// 计算俯仰角
	$cosz = sin(m_dLatitude/180*pi()) * sin($dec/180*pi()) + cos(m_dLatitude/180*pi()) * cos($dec/180*pi()) * cos($ha/180*pi());
	$q = 1 - $cosz * $cosz;
	$ele = atan2($cosz, sqrt($q));

	// 计算方位角
	$sina = cos($dec/180*pi()) * sin($ha/180*pi());
	$cosa = sin(m_dLatitude/180*pi()) * cos($dec/180*pi()) * cos($ha/180*pi()) - cos(m_dLatitude/180*pi()) * sin($dec/180*pi());
	$azi = atan2($sina, $cosa);
	// 转换为北零点 azi += 180;
	$azi = reduceAngle($azi);
}
//太阳、月亮等位置计算函数 结束/////////////////////////