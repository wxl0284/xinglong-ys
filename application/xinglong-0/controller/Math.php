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
		$sun_azi = 0; $sun_ele = 0;  //太阳位置
		$moon_azi = 0; $moon_ele = 0;  //月亮位置
		
		//调用太阳 月亮位置计算的函数
		getPosMoon($moon_azi, $moon_ele);
		getPosSun($sun_azi, $sun_ele);
		
		//将计算结果返回 数组形式
		$result['moon_azi'] = round($moon_azi, 6);
		$result['moon_ele'] = round($moon_ele, 6);
		$result['sun_azi'] = round($sun_azi, 6);
		$result['sun_ele'] = round($sun_ele, 6);
	
		dump($result);
		
	}//计算恒星时 太阳、月亮等位置数据 结束/////////////////////
	
	
	public function f ()
	{
		$hms = data2Time(5.762449);
		echo $hms;
	}
}//class Math 结束 ///////////////////////////////////////