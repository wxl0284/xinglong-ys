<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;
use think\Cookie;

class Result extends Controller
{
	//检测是否登录////////////////////////////////////////////////
    public function _initialize ()
    {
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
	
	//显示观测结果页面///////////////////////////////////////////
	public function index ()
	{
		return view();
	}

}
