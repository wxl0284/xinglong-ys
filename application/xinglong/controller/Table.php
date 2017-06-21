<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Db;
//use think\Session;
use think\Request;
//use think\Cache;

class Table extends Controller
{
	protected static $planData;
    //测试easy ui的表格
	public function index () 
	{
		self::$planData = 'asdf';
		return view('table');
	}
	
	public function info ()
	{
		return 123;
	}
}
