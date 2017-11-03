<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Session;
use think\Request;
use think\Cookie;
use think\Db;
use think\Config;

//80cm号望远镜控制器
class At80 extends Controller
{
	public $ip = '';  //socket通信 ip
	public $port = '';  //socket通信 port
    //检测是否登录////////////////////////////////////////////////
    public function _initialize ()
    {
		//初始化soket的ip和端口
        $this->ip = Config::get('ip');
        $this->port = Config::get('port');
		
		//未登录
        if (!Session::has('login'))
        {
			if (Request::instance()->isAjax())
			{
				return '请完成登录后，再进行相关操作！';
			}
            $request = Request::instance();
            Cookie::set('url', $request->url(true));
            $this->error('请完成登录后，再进行相关操作！', '/');
        }   
    }
	
	//显示80cm望远控制镜页面////////////////////////////////////////
    public function index ()
    {
		//return view('at60');
		return view('at80');
    }
}