<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Db;

class Weather extends Controller
{
	//显示更多云量图片
	public function clouds ()
	{
		return view('clouds');
	}
}
