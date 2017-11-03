<?php
namespace app\xinglong\controller;

use think\Controller;
use app\xinglong\model\User;
use think\Cache;

class Test extends Controller
{
	public function index ()
	{
		/*  $mjd = GetJD(); //获取修正儒略日
		
		$sunRise = 0; $sunSet = 0;
		
		sunTwilight($sunRise, $sunSet, $mjd, 8);
		
		echo $sunRise. '|' .$sunSet;
		//echo config('latitude'); 
		//return data2Time ('-11.386761111111');
		//return time2Data ('-11:23:12.34'); */
		//$userData = User::where('id','>',10)->column('username','password');
		//print_r($userData);
		$cacheStr['a'] = "aaa";
		$cacheStr['b'] = "bbb";
		$cacheStr['data'] = 1;
		Cache::set('name', $cacheStr);
		
		
	}
	
	public function index1 ()
	{
		if (Cache::get('name')) {
			$res = Cache::get('name');
			return $res['data'];
		}else{
			return 'no cache';
		}
	}
}