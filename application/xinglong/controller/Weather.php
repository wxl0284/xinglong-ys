<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Db;
use think\Cookie;

class Weather extends Controller
{
	//检测是否登录////////////////////////////////////////////////
    public function _initialize ()
    {
        //未登录
        if (!Cookie::has('login'))
        {
			/* if (Request::instance()->isAjax())
			{
				return '请完成登录后，再进行相关操作！';
			} */
            $request = Request::instance();
            Cookie::set('url', $request->url());
            $this->error('请完成登录后，再进行相关操作！', '/');
        }   
    }
	
	//显示更多云量图片
	public function clouds ()
	{
		return view('clouds');
	}
	
	//显示更多 气象信息
	public function weathermore ()
	{
		return view('weatherMore');
	}
}
