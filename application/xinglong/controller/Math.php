<?php
namespace app\xinglong\controller;
/*
此类用来计算恒星时、太阳、月亮等位置数据
*/

class Math
{
	//计算恒星时 太阳 月亮等位置数据/////////////////////////////////
	public function  compute ()
	{
		$mjd = GetJD();  //修正儒略日
		 
		$sunRise = 0; //晨光始
		$sunSet = 0; //昏影终
		 
		sunTwilight ($sunRise, $sunSet, $mjd, 8);
		 
		//将晨光始 昏影终 存于数组中  返回修正儒略日
		$result['sunRise'] = $sunRise;
		$result['sunSet'] = $sunSet;
		
		$sun_azi = 0; $sun_ele = 0;  //太阳位置
		$moon_azi = 0; $moon_ele = 0;  //月亮位置
		
		//调用太阳 月亮位置计算的函数
		getPosMoon($moon_azi, $moon_ele);
		getPosSun($sun_azi, $sun_ele);
		
		//将计算结果返回 数组形式
		$result['moon_azi'] = $moon_azi;
		$result['moon_ele'] = $moon_ele;
		$result['sun_azi'] = $sun_azi;
		$result['sun_ele'] = $sun_ele;
	
		dump($result);
		
	}//计算恒星时 太阳、月亮等位置数据 结束/////////////////////
	
	
	public function f ()
	{
		$res = data2Time (5.5806619387891);
		return $res;
	}
}//class Math 结束 ///////////////////////////////////////




//下面为原始函数的代码  上面为重写的函数代码
//以下为太阳、月亮等位置计算函数////////////////////////////////////
//date_default_timezone_set('PRC'); //设置时区
/*
  ymd(); 获取年、月、日

function ymd ()
{
	$ymd = gmdate('Y:m:d');
	$ymd = explode(':', $ymd);
	return $ymd;	
}

/*
  epoch(); 得到历元

function epoch ()
{
	$ymd = ymd();
	return $ymd[0] + (($ymd[1]- 1)*30.3 + $ymd[2])/365;	
}

/*
  ModifiedJulianDay(); 返回修正儒略日
*/
/* function ModifiedJulianDay ($year, $mon, $day, $hour)
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
	$jDay = $a + $b + $c + $day + $hour/24;
	return $jDay;
} */

/*
  GetJD(); 计算当前设置时间的修正儒略日

function GetJD ()
{
	$hms = gmdate('H:i:s');  //用格林威治标准时
	$hms = explode(':', $hms);
	$mSec = substr(microtime(), 2, 8); //疑问 $mSec 毫秒 ？
	
	$h = $hms[0] + $hms[1]/60 + ($hms[2] + $mSec/1000)/3600;
	$ymd = ymd();
	//echo $h;return;
	return ModifiedJulianDay($ymd[0], $ymd[1], $ymd[2], $h);	
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


function sunTimeOfEle (&$from, &$to, $mjd, $ele)
{
	if($ele > 90 || $ele < -90) {// 高度角超过[-90,90]范围
		return -1;
	}
	
	$ra = 0; $dec = 0;  // 太阳的赤道坐标
	$cosha = 0;
	$ha = 0;
	$LtNoon = 0;	// 正午时间
	$latitude = config('latitude'); //获取维度
	
	sunPosition($ra, $dec, $mjd);
	//echo $mjd;return;
	$cosha = (sin($ele/180*pi()) - sin($latitude/180*pi()) * sin($dec/180*pi())) / (cos($latitude/180*pi()) * cos($dec/180*pi()));
	//echo $cosha;return;
	if($cosha < -1)
	{
		return 1;
	}else if($cosha > 1)
	{
		return 2;
	}
	
	$ha = (acos($cosha)*180/pi())/15;	// 小时为单位
	$LtNoon = $ra - getGMST0($mjd);
	
	$from = reduceZeroMax($LtNoon - $ha, 24);
	$to   = reduceZeroMax($LtNoon + $ha, 24);
	
	return 0;
}

/*
*getGMST0() : 


function getGMST0($mjd)
{
	$mjd0 = (int) ($mjd -51543);
	$mjd0 -= 1.5;
	$t = $mjd0 / 36525;
	$gmst0 = 0;

	$gmst0 = reduceZeroMax(280.46061837 + 360.98564736629 * $mjd0 + 0.000388 * $t * $t, 360.0);
	return $gmst0 / 15;
}
 
/*
*reduceZeroMax() :


function reduceZeroMax ($x, $maxV)
{
	return $x - floor($x/$maxV)*$maxV;
}

/*
*getPosSun() : 太阳位置计算


function getPosSun(&$azi, &$ele)
{
	$ra = 0;
	$dec = 0;
	$lmst =  0;

	$lmst = GetLMST();
	sunPosition($ra, $dec, GetJD());
	Eq2AziEle($azi, $ele, $lmst - $ra, $dec);
}

/*
*GetLMST() 恒星时计算


function GetLMST()
{
	return localMeanSiderialTime(GetJD());
}

/*
*localMeanSiderialTime() :


function localMeanSiderialTime ($mjd)
{
	$mjd0 = 0;
	$t = 0;
	$ut = 0;		// 世界时
	$gmst = 0;	// 格林威治平恒星时
	$lmst = 0;	// 本地平恒星时
	$longitude = config('longitude'); //获取经度
	
	$mjd0 = (int) $mjd;
	$ut = ($mjd - $mjd0)*24;
	$t = ($mjd0 - 51544.5)/36525;	// 儒略纪元
	$gmst = 6.697374558 + 1.0027379093 * $ut + (8640184.812866 + (0.093104 - 0.0000062 * $t) * $t) * $t / 3600;
	
	$lmst = $gmst + $longitude / 15;
	
	return reduceZeroMax($lmst, 24);
}

/*
*sunPosition() : 太阳相对地心的位置


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

	$x = cos($E/180*pi()) - $e;
	$y = sin($E/180*pi()) * sqrt(1 - $e * $e);

	$r = sqrt($x * $x + $y * $y);
	$v = atan2($y, $x)*180/pi();
	$lon = ReduceAngle($v + $w);
 
	$x = $r * cos($lon/180*pi());
	$y = $r * sin($lon/180*pi());

	$xequat = $x;
	$yequat = $y * cos($eo/180*pi()) + $z * sin($eo/180*pi()); //$z=0 ?
	$zequat = $y * sin($eo/180*pi()) + $z * cos($eo/180*pi()); //$z=0 ?

	rect2Sphere($r, $ra, $dec, $xequat, $yequat, $zequat); //rect2Sphere 函数 ？
	$ra /= 15;
}

/*
*SunEccentricAnomaly()


function SunEccentricAnomaly($mjd)
{
	$e = SunEccentricity($mjd);
	$M = SunMeanAnomaly($mjd);

	$sea = $M + (180/pi()) * $e * sin($M/180*pi()) * (1 + $e * cos($M/180*pi()));

	return $sea;
}

/*
*SunEccentricity()


function SunEccentricity($mjd)
{
	$mjd0 = (int) ($mjd - 51543);
	return 0.016709 - $mjd0 * 1.151E-9;
	
}

/*
*SunMeanAnomaly() 


function SunMeanAnomaly($mjd)
{
	$mjd0 = (int) ($mjd - 51543);
	$sma = 356.0470 + 0.9856002585 * $mjd0;
	return ReduceAngle($sma);
}

/*
*Perihelion()


function Perihelion($mjd)
{
	$mjd0 = (int) ($mjd - 51543);
	return 282.9404 + $mjd0 * 4.70935E-5;
}

/*
*EclipticObliquity()


function EclipticObliquity($mjd)
{
	$mjd0 = (int) ($mjd - 51543);
	return 23.4393 - $mjd0 * 3.563E-7;
}


/*
*GetPosMoon() 月亮位置计算


function GetPosMoon(&$azi, &$ele)
{
	$ra = 0; $dec = 0;
	$lmst = 0;

	MoonTopocentricPos($ra, $dec, GetJD());
	$lmst = GetLMST();
	Eq2AziEle($azi, $ele, $lmst - $ra, $dec);
}

/**----------------------------------------------------------------------------
 * MoonTopocentricPos()
 * 说明:
 *   月亮在某日的位置:相对地表
 * 参数:
 *   $ra       -- 赤经,单位:小时
 *   $dec      -- 赤纬,单位:度
 *   $mjd      -- 通过ModifiedJulianDay计算得到
 *---------------------------------------------------------------------------
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
	$ha = $lmst - $ra;
	$ha = ReduceAngle($ha * 15);
	$mpar = asin(1.0 / $r) * 180/pi(); // 视差
	$g = atan2(tan($gclat/180*pi()), cos($ha/180*pi())) * 180/pi();
	$ra = $ra - $mpar * $rho * cos($gclat/180*pi()) * sin($ha/180*pi()) / cos($dec/180*pi()) / 15.0;
	if(abs($g) >= 0.0001)
	{
		$dec = $dec - $mpar * $rho * sin($gclat/180*pi()) * sin(($g-$dec)/180*pi()) / sin($g/180*pi());
	}else{
		$dec = $dec + $mpar * $rho * sin($gclat/180*pi()) * sin($dec/180*pi()) * cos($ha/180*pi());
	}
		
}

/*
*MoonPosition() 


function MoonPosition(&$r, &$ra, &$dec, $mjd)
{
	$mjd0 = $mjd - 51543;
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
	$longitude = 0; $latitude = 0;
	// $x.$y,z-equat,赤道坐标
	$xequat = 0; $xequat = 0; $zequat = 0;
	// 修正参数
	$Ls = SunMeanLongitude($mjd);	// 太阳平黄经
	$Lm = ReduceAngle($N + $w + $M);	// 月亮平黄经
	$Ms = SunMeanAnomaly($mjd);	// 太阳平近点角
	$D = ReduceAngle($Lm - $Ls);	// 月亮平均延伸量
	$F = ReduceAngle($Lm - $N);	// 月亮升交角距

	while(abs($E0 - $E1) > 0.005) {
		$E0 = $E1;
		$E1 = ReduceAngle($E0 - ($E0 - 180/pi() * $e * sin($E0/180*pi()) - $M) / (1 - $e * cos($E0/180*pi())));
	}
	$E0 = $E1;

	// 计算月轨位置
	$x = $a * (cos($E0/180*pi()) - $e);
	$y = $a * sqrt(1 - $e * $e) * sin($E0/180*pi());
	$r = sqrt($x * $x + $y * $y);
	$v = ReduceAngle(atan2($y, $x) * 180/pi());
	// 转换为黄道位置
	$xeclip = $r * (cos($N/180*pi()) * cos(($v + $w)/180*pi()) - sin($N/180*pi()) * sin(($v + $w)/180*pi()) * cos($i/180*pi()));
	
	$yeclip = $r * (sin($N/180*pi()) * cos(($v + $w)/180*pi()) + cos($N/180*pi()) * sin(($v + $w)/180*pi()) * cos($i/180*pi()));
	
	$zeclip = $r * sin(($v + $w)/180*pi()) * sin($i/180*pi());

	Rect2Sphere($r, $longitude, $latitude, $xeclip, $yeclip, $zeclip);

	// 计算扰动
	$longitude = $longitude - 1.274 * sin(($M-2*$D)/180*pi()) + 0.658 * sin(2*$D/180*pi())
		- 0.186 * sin($Ms/180*pi()) - 0.059 * sin((2*($M-$D))/180*pi()) - 0.057 * sin(($M - 2*$D + $Ms)/180*pi())
		+ 0.053 * sin(($M + 2*$D)/180*pi()) + 0.046 * sin((2*$D - $Ms)/180*pi()) + 0.041 * sin(($M - $Ms)/180*pi())
		- 0.035 * sin($D/180*pi()) - 0.031 * sin(($M + $Ms)/180*pi()) - 0.015 * sind(2*($F - $D)/180*pi())
		+ 0.011 * sind(($M - 4*$D)/180*pi());
		
	$latitude = $latitude - 0.173 * sind(($F - 2*$D)/180*pi()) - 0.055 * sin(($M - $F - 2*$D)/180*pi())
		-0.046 * sin(($M + $F - 2.0 * $D)/180*pi()) + 0.033 * sin(($F + 2*$D)/180*pi()) + 0.017 * sin((2*$M + $F)/180*pi());
		
	$r = $r - 0.58 * cos(($M - 2*$D)/180*pi()) - 0.46*cos(2*$D/180*pi());
	
	$longitude = ReduceAngle($longitude);

	// 转换为赤道坐标
	Sphere2Rect($xeclip, $yeclip, $zeclip, $r, $longitude, $latitude);

	$xequat = $xeclip;
	$xequat = $yeclip * cos($eo/180*pi()) - $zeclip*sin($eo/180*pi());
	$zequat = $yeclip * sin($eo/180*pi()) + $zeclip*cos($eo/180*pi());

	Rect2Sphere($r, $ra, $dec, $xequat, $yequat, $zequat);
	$ra /= 15;
}

/*
*SunMeanLongitude()


function SunMeanLongitude($mjd)
{
	$sml = Perihelion($mjd) + SunMeanAnomaly($mjd);
	return ReduceAngle($sml);
}

/*
*ReduceAngle()


function ReduceAngle($x)
{
	return reduceZeroMax($x, 360);
}

/*
*Rect2Sphere()


function Rect2Sphere(&$r, &$alpha, &$theta, $x, $y, $z)
{
	$r = sqrt($x * $x + $y * $y + $z * $z);
	if($x == 0 && $y == 0)
	{
		$alpha = 0;
	}else{
		$alpha = ReduceAngle(atan2($y, $x) * 180/pi());
		$theta = atan2($z, sqrt($x * $x + $y * $y)) * 180/pi();
	}
		
}

/*
*Sphere2Rect()


function Sphere2Rect(&$x, &$y, &$z, $r, $alpha, $theta)
{
	$x = $r * cos($alpha/180*pi()) * cos($theta/180*pi());
	$y = $r * sin($alpha/180*pi()) * cos($theta/180*pi());
	$z = $r * sin($theta/180*pi());
}

/*
*Eq2AziEle() 赤经赤纬转方位俯仰


function Eq2AziEle(&$azi, &$ele, $ha, $dec)
{
	$ha *= 15;	// 时角从小时转换为角度
	$latitude = config('latitude');
	
	// 计算俯仰角
	$cosz = sin($latitude/180*pi()) * sin($dec/180*pi()) + cos($latitude/180*pi()) * cos($dec/180*pi()) * cos($ha/180*pi());
	
	$q = 1 - $cosz * $cosz;
	
	$ele = atan2($cosz, sqrt($q)) *180/pi();

	// 计算方位角
	$sina = cos($dec/180*pi()) * sin($ha/180*pi());
	
	$cosa = sin($latitude/180*pi()) * cos($dec/180*pi()) * cos($ha/180*pi()) - cos($latitude/180*pi()) * sin($dec/180*pi());
	
	$azi = atan2($sina, $cosa) *180/pi();
	// 转换为北零点 $azi += 180;
	$azi = ReduceAngle($azi);
}

//接下来，该绘图了*/