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