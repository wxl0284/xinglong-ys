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
    $head = pack('L', $magic);  //uint32 unsigned int

    $head .= pack('S', $version);  //uint16 unsigned short

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
    $head2 = pack('L', $user);   //uint32 unsigned int

    $head2 .= pack('L', $plan);   

    $head2 .= pack('S', $at); 

    $head2 .= pack('S', $device);   

    $head2 .= pack('L', $sequence);  

    $head2 .= pack('L', $operation);      //uint32

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
	$sec =  (($data - $hour - $min/60) * 360000);
    $sec = '' . round ($sec/100, 1);
    if ( strpos($sec, '.') ===false )
    {
        $sec .= '.0';
    }

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
	
	return $sign . $hour . ':' . $min . ':' . $sec;
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

/*********************以下为天文计算的函数**********************/
/*ymd(); 
由格林威治时间 获取年、月、日
*/
function ymd ()
{
	$ymd = gmdate('Y:m:d');
	$ymd = explode(':', $ymd);
	return $ymd;	
}		

/** 查看当前历元.
 * 根据格林威治时间 计算当前历元
 * return 当前历元
 */
function GetEpoch () //疑问:此函数未被调用
{
	$ymd = ymd();
	return floatval ($ymd[0] + ( ($ymd[1]- 1)*30.3 + $ymd[2] )/365);	
}

/*
  GetJD(); 计算当前时间的修正儒略日
*/
function GetJD ()
{
	$hms = gmdate('H:i:s');  //用格林威治标准时
	$hms = explode(':', $hms);
	
	list($usec, $sec) = explode(" ", microtime());
	$usec = (float)$usec;  //微秒数(单位：秒)
	
	$h = $hms[0] + $hms[1]/60 + ($hms[1] + $usec)/3600;
	$h = floatval ($h);
	$ymd = ymd();
	
	return ModifiedJulianDay($ymd[0], $ymd[1], $ymd[2], $h);
}


/*
  ModifiedJulianDay(); 返回修正儒略日
*/
function ModifiedJulianDay ($year, $mon, $day, $hour)
{
	$a = intval (10000*$year + 100*$mon + $day);
	
	if($mon <= 2)
	{
		$mon += 12;
		$year --;
	}
	
	if($a <= 15821004.1)
	{
		$b = intval (($year + 4716)/4 - 1179 - 2);
	}else{
		$b = intval ($year/400 - $year/100 + $year/4);
	}
	
	$a = intval (365*$year - 679004);			
	$c = intval ( 30.6001*($mon + 1) );
	
	$jDay = floatval ($a + $b + $c + $day + $hour/24);
	return $jDay;
}

/** sunTwilight() : 计算日出日落的时间.(晨光始 昏影终)
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
 }//sunTwilight() 结束////////////////////////////
 
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
	$latitude = config('latitude'); //获取兴隆纬度
	
	sunPosition($ra, $dec, $mjd);
	
	$cosha = (sin($ele/180*pi()) - sin($latitude/180*pi()) * sin($dec/180*pi())) / (cos($latitude/180*pi()) * cos($dec/180*pi()));
	
	$cosha = floatval ($cosha);
	
	if($cosha < -1)
	{
		return 1;
	}else if($cosha > 1)
	{
		return 2;
	}
	
	$ha = floatval ( (acos($cosha)*180/pi())/15 );	// 小时为单位
	
	$LtNoon = floatval ($ra - getGMST0($mjd));
	
	$from = reduceZeroMax($LtNoon - $ha, 24);
	$to   = reduceZeroMax($LtNoon + $ha, 24);
	
	return 0;
}

/*
* getGMST0() : 
*/

function getGMST0($mjd)
{
	$mjd0 = intval ($mjd -51543);
	$mjd0 -= 1.5;
	$t = floatval ($mjd0 / 36525);

	$gmst0 = reduceZeroMax(280.46061837 + 360.98564736629 * $mjd0 + 0.000388 * $t * $t, 360);
	
	return floatval ($gmst0 / 15);
}

/*
*reduceZeroMax() :
*/

function reduceZeroMax ($x, $maxV)
{
	return floatval ($x - floor($x/$maxV)*$maxV);
}

/*
*getPosSun() : 太阳位置计算
*/

function getPosSun(&$sun_azi, &$sun_ele)
{//此时只是定义 还未调用
	$ra = 0;
	$dec = 0;

	$lmst = GetLMST();
	sunPosition($ra, $dec, GetJD());
	Eq2AziEle($sun_azi, $sun_ele, $lmst - $ra, $dec);
}

/*
*GetLMST() 恒星时计算
*/

function GetLMST()
{
	return localMeanSiderialTime(GetJD());
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
	$longitude = config('longitude'); //获取经度
	
	$mjd0 = (int) $mjd;
	$ut = floatval (($mjd - $mjd0)*24);
	$t = floatval (($mjd0 - 51544.5)/36525);	// 儒略纪元
	$gmst = 6.697374558 + 1.0027379093 * $ut + (8640184.812866 + (0.093104 - 0.0000062 * $t) * $t) * $t / 3600;
	
	$lmst = floatval ($gmst + $longitude / 15);
	
	return reduceZeroMax($lmst, 24);
}


/*
*sunPosition() : 太阳相对地心的位置
*/

function sunPosition (&$ra, &$dec, $mjd)
{
	$E = SunEccentricAnomaly($mjd);
	$e = SunEccentricity($mjd);
	$w = Perihelion($mjd);
	$eo = EclipticObliquity($mjd);
	$x = 0; $y = 0; $z = 0;
	$r = 0;
	$v = 0;
	$lon = 0;
	$xequat = 0; $yequat = 0; $zequat = 0;

	$x = floatval (cos($E/180*pi()) - $e);
	
	$y = floatval (sin($E/180*pi()) * sqrt(1 - $e * $e));

	$r = floatval (sqrt($x * $x + $y * $y));
	
	$v = floatval (atan2($y, $x)*180/pi());
	
	$lon = floatval (ReduceAngle($v + $w));
 
	$x = floatval ($r * cos($lon/180*pi()));
	
	$y = floatval ($r * sin($lon/180*pi()));

	$xequat = $x;
	$yequat = $y * cos($eo/180*pi()) + $z * sin($eo/180*pi()); //$z=0 ?
	
	$yequat = floatval ($yequat);
	
	$zequat = $y * sin($eo/180*pi()) + $z * cos($eo/180*pi()); //$z=0 ?
	
	$zequat = floatval ($zequat);

	rect2Sphere($r, $ra, $dec, $xequat, $yequat, $zequat);
	
	$ra /= 15;
	$ra = floatval ($ra);
}

/*
*SunEccentricAnomaly()
*/

function SunEccentricAnomaly($mjd)
{
	$e = SunEccentricity($mjd);
	$M = SunMeanAnomaly($mjd);

	$sea = $M + (180/pi()) * $e * sin($M/180*pi()) * (1 + $e * cos($M/180*pi()));

	return floatval ($sea);
}

/*
*SunEccentricity()
*/

function SunEccentricity($mjd)
{
	$mjd0 = (int) ($mjd - 51543);
	return floatval (0.016709 - $mjd0 * 1.151E-9);
}

/*
*SunMeanAnomaly() 
*/

function SunMeanAnomaly($mjd)
{
	$mjd0 = (int) ($mjd - 51543);
	
	$sma = floatval (356.0470 + 0.9856002585 * $mjd0);
	
	return ReduceAngle($sma);
}

/*
*Perihelion()
*/

function Perihelion($mjd)
{
	$mjd0 = (int) ($mjd - 51543);
	return floatval (282.9404 + $mjd0 * 4.70935E-5);
}

/*
*EclipticObliquity()
*/

function EclipticObliquity($mjd)
{
	$mjd0 = (int) ($mjd - 51543);
	return floatval (23.4393 - $mjd0 * 3.563E-7);
}

/*
*Eq2AziEle() 赤经赤纬转方位俯仰
*/

function Eq2AziEle(&$azi, &$ele, $ha, $dec)
{
	$ha *= 15;	// 时角从小时转换为角度
	$ha = floatval ($ha);
	$latitude = config('latitude');
	
	// 计算俯仰角
	$cosz = sin($latitude/180*pi()) * sin($dec/180*pi()) + cos($latitude/180*pi()) * cos($dec/180*pi()) * cos($ha/180*pi());
	
	$cosz  = floatval ($cosz);
	
	$q = floatval (1 - $cosz * $cosz);
	
	$ele = atan2($cosz, sqrt($q)) *180/pi();
	
	$ele = floatval ($ele);

	// 计算方位角
	$sina = cos($dec/180*pi()) * sin($ha/180*pi());
		
	$sina = floatval ($sina);
	
	$cosa = sin($latitude/180*pi()) * cos($dec/180*pi()) * cos($ha/180*pi()) - cos($latitude/180*pi()) * sin($dec/180*pi());
	
	$azi = atan2($sina, $cosa) *180/pi();
	
	$azi = floatval ($azi);

	$azi = ReduceAngle($azi);
}


/*
*getPosMoon() 月亮位置计算
*/

function getPosMoon(&$moon_azi, &$moon_ele)
{//此时只是定义 还未调用
	$ra = 0; $dec = 0;
	$lmst = 0;

	MoonTopocentricPos($ra, $dec, GetJD());
	$lmst = GetLMST();
	Eq2AziEle($moon_azi, $moon_ele, $lmst - $ra, $dec);
}

/**-----------------------------------------------------------
 * MoonTopocentricPos()
 * 说明:
 *   月亮在某日的位置:相对地表
 * 参数:
 *   $ra       -- 赤经,单位:小时
 *   $dec      -- 赤纬,单位:度
 *   $mjd      -- 通过ModifiedJulianDay计算得到
 *--------------------------------------------------------------*/
function MoonTopocentricPos(&$ra, &$dec, $mjd)
{
	$latitude = config('latitude');
	// 地心纬度
	$gclat = $latitude - 0.1924 * sin(2.0 * $latitude/180*pi());

	// 地表到地心距离
	$rho = 0.99883 + 0.00167 * cos(2.0 * $latitude/180*pi());

	$ha = 0;		// 时角
	$r = 0;		// 月球到地心距离
		
	$lmst = localMeanSiderialTime($mjd);

	MoonPosition($r, $ra, $dec, $mjd);
	
	$ha = floatval ($lmst - $ra);
	
	$ha = ReduceAngle($ha * 15);
	
	$mpar = asin(1.0 / $r) * 180/pi(); // 视差
	
	$g = atan2(tan($gclat/180*pi()), cos($ha/180*pi())) * 180/pi();
	
	$ra = $ra - $mpar * $rho * cos($gclat/180*pi()) * sin($ha/180*pi()) / cos($dec/180*pi()) / 15.0;
	
	if(abs($g) >= 0.0001)
	{
		$dec = $dec - $mpar * $rho * sin($gclat/180*pi()) * sin(($g-$dec)/180*pi()) / sin($g/180*pi());

		$dec = floatval ($dec);
	}else{
		$dec = $dec + $mpar * $rho * sin($gclat/180*pi()) * sin($dec/180*pi()) * cos($ha/180*pi());

		$dec = floatval ($dec);
	}
		
}

/*
*MoonPosition() 
*/

function MoonPosition(&$r, &$ra, &$dec, $mjd)
{
	$mjd0 = floatval ($mjd - 51543);
	// 月亮轨道根数
	$N = ReduceAngle(125.1228 - 0.0529538083 * $mjd0);
	
	$i = 5.1454;	// 倾度
	$w = ReduceAngle(318.0634 + 0.1643573223 * $mjd0); // 近地点

	$a = 60.2666;	// 平均距离
	$e = 0.054900;	// 离心率
	
	$M = ReduceAngle(115.3654 + 13.0649929509 * $mjd0);	// 平近点角
	
	$eo = EclipticObliquity($mjd);
	// E0,E1:偏近点角
	$E0 = ReduceAngle($M + 180/pi() * $e * sin($M/180*pi()) * (1 + $e * cos($M/180*pi())));
	
	$E1 = ReduceAngle($E0 - ($E0 - 180/pi() * $e * sin($E0/180*pi()) - $M) / (1 - $e * cos($E0/180*pi())));
	
	// $x,$y,$r,$v:月轨坐标
	$x = 0; $y = 0; $v = 0;
	
	// $x,$y,z-eclip,$longitude,latitude,黄道坐标
	$xeclip = 0; $yeclip = 0; $zeclip = 0;
	
	$longitude = config('longitude'); 
	$latitude = config('latitude');
	
	// $x.$y,z-equat,赤道坐标
	$xequat = 0; $xequat = 0; $zequat = 0;
	
	// 修正参数
	$Ls = SunMeanLongitude($mjd);	// 太阳平黄经
	
	$Lm = ReduceAngle($N + $w + $M);	// 月亮平黄经
	
	$Ms = SunMeanAnomaly($mjd);	// 太阳平近点角
	
	$D = ReduceAngle($Lm - $Ls);	// 月亮平均延伸量
	
	$F = ReduceAngle($Lm - $N);	// 月亮升交角距

	while(abs($E0 - $E1) > 0.005) 
	{
		$E0 = $E1;
		
		$E1 = ReduceAngle($E0 - ($E0 - 180/pi() * $e * sin($E0/180*pi()) - $M) / (1 - $e * cos($E0/180*pi())));
		
		$E1 = floatval ($E1);
	}
	
	$E0 = $E1;

	// 计算月轨位置
	$x = $a * (cos($E0/180*pi()) - $e);
	$x = floatval ($x);
	
	$y = $a * sqrt(1 - $e * $e) * sin($E0/180*pi());
	$y = floatval ($y);
	
	$r = floatval (sqrt($x * $x + $y * $y));
	
	$v = ReduceAngle(atan2($y, $x) * 180/pi());
	
	// 转换为黄道位置
	$xeclip = $r * (cos($N/180*pi()) * cos(($v + $w)/180*pi()) - sin($N/180*pi()) * sin(($v + $w)/180*pi()) * cos($i/180*pi()));
	
	$xeclip = floatval ($xeclip);
	
	$yeclip = $r * (sin($N/180*pi()) * cos(($v + $w)/180*pi()) + cos($N/180*pi()) * sin(($v + $w)/180*pi()) * cos($i/180*pi()));

	$yeclip = floatval ($yeclip);
	
	$zeclip = $r * sin(($v + $w)/180*pi()) * sin($i/180*pi());

	$zeclip = floatval ($zeclip);

	Rect2Sphere($r, $longitude, $latitude, $xeclip, $yeclip, $zeclip);

	// 计算扰动
	$longitude = $longitude - 1.274 * sin(($M-2*$D)/180*pi()) + 0.658 * sin(2*$D/180*pi())
		- 0.186 * sin($Ms/180*pi()) - 0.059 * sin((2*($M-$D))/180*pi()) - 0.057 * sin(($M - 2*$D + $Ms)/180*pi())
		+ 0.053 * sin(($M + 2*$D)/180*pi()) + 0.046 * sin((2*$D - $Ms)/180*pi()) + 0.041 * sin(($M - $Ms)/180*pi())
		- 0.035 * sin($D/180*pi()) - 0.031 * sin(($M + $Ms)/180*pi()) - 0.015 * sin(2*($F - $D)/180*pi())
		+ 0.011 * sin(($M - 4*$D)/180*pi());
		
	$longitude = floatval ($longitude);
		
	$latitude = $latitude - 0.173 * sin(($F - 2*$D)/180*pi()) - 0.055 * sin(($M - $F - 2*$D)/180*pi())
		-0.046 * sin(($M + $F - 2.0 * $D)/180*pi()) + 0.033 * sin(($F + 2*$D)/180*pi()) + 0.017 * sin((2*$M + $F)/180*pi());
		
	$latitude = floatval ($latitude);
		
	$r = $r - 0.58 * cos(($M - 2*$D)/180*pi()) - 0.46*cos(2*$D/180*pi());
	
	$r = floatval ($r);
	
	$longitude = ReduceAngle($longitude);

	// 转换为赤道坐标
	Sphere2Rect($xeclip, $yeclip, $zeclip, $r, $longitude, $latitude);

	$xequat = floatval ($xeclip);
	
	$yequat = $yeclip * cos($eo/180*pi()) - $zeclip*sin($eo/180*pi());

	$yequat = floatval ($yequat);
	
	$zequat = $yeclip * sin($eo/180*pi()) + $zeclip*cos($eo/180*pi());
	
	$zequat = floatval ($zequat);

	Rect2Sphere($r, $ra, $dec, $xequat, $yequat, $zequat);

	$ra /= 15;
	$ra  = floatval ($ra);
}//MoonPosition() 结束//////////////////////////////////

/*
*SunMeanLongitude()
*/

function SunMeanLongitude($mjd)
{
	$sml = Perihelion($mjd) + SunMeanAnomaly($mjd);
	return ReduceAngle($sml);
}

/*
*ReduceAngle()
*/

function ReduceAngle($x)
{
	return reduceZeroMax($x, 360);
}

/*
*Rect2Sphere()
*/

function Rect2Sphere(&$r, &$alpha, &$theta, $x, $y, $z)
{
	$r = sqrt($x * $x + $y * $y + $z * $z);
	
	$r = floatval ($r);
	
	if($x == 0 && $y == 0)
	{
		$alpha = 0;
	}else{
		$alpha = ReduceAngle(atan2($y, $x) * 180/pi());
		
		$theta = atan2($z, sqrt($x * $x + $y * $y)) * 180/pi();
		
		$theta = floatval ($theta);
	}
		
}

/*
*Sphere2Rect()
*/

function Sphere2Rect(&$x, &$y, &$z, $r, $alpha, $theta)
{
	$x = $r * cos($alpha/180*pi()) * cos($theta/180*pi());
	
	$x = floatval ($x);
	
	$y = $r * sin($alpha/180*pi()) * cos($theta/180*pi());
	
	$y = floatval ($y);
	
	$z = $r * sin($theta/180*pi());
	
	$z = floatval ($z);
}
/*********************天文计算的函数结束**********************/
