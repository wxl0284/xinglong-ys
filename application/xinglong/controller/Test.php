<?php
namespace app\xinglong\controller;

use think\Controller;
use app\xinglong\model\User;
use think\Db;
use think\Cache;
use think\Request;
use app\xinglong\model\At60config;

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
		return view('table/a');
	}
	
	public function index2 ()
	{
		return $this->index1();
	}
	
	public function test ()
	{
		/* $a['aa'] = 'aaaa';
		$a['bb'] = 'bbbb';
		Cache::set('name', $a);
		return view('table/a'); */
		$At60config = new At60config;
		$configData = $At60config->all();
		//halt($configData[0]);
		if (empty($configData))
		{
			return '请先配置60CM望远镜!';
		}else{
			$At60config->save([
				'attype' => '2',
				'filtercanfindhome' => '6'
			],['pk_at60config' => $configData[0]['pk_at60config']]);
		}
	}
	
	public function valid ()
	{
		$data = Cache::get('name');
		halt($data);
	/* 	$result	= $this->validate(
            [
                '用户名' =>	$a['n'],
            ],
            
            [
                '用户名' =>	'alphaDash',
        ]); */
        if (!preg_match('/^[0-2]$/', $a['n']))
        { 
            return 'err';
        }else{
			return 'ok';
		}
	}
}