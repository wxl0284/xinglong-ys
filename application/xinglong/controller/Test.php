<?php
namespace app\xinglong\controller;

use think\Controller;

class Test extends Controller
{
	public function demo ()
	{
		$mjd = GetJD(); //获取修正儒略日
		
		$sunRise = 0; $sunSet = 0;
		
		sunTwilight($sunRise, $sunSet, $mjd, 8);
		
		echo $sunRise. '|' .$sunSet;
		//echo config('latitude');
		
	}
}