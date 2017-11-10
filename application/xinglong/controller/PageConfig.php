<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Cache;
use think\Session;
use think\Request;
use think\Cookie;
use think\Db;
//use think\Config;


class PageConfig extends Controller
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
	
	//显示60cm望远镜配置选择页
	public function at60config ()
	{
		return view('pageConfig/at60config');
	}
	
	//根据用户提交的选项，进行60cm望远镜
	public function doAt60config ()
	{
		//未选中任何选项
		$option = input();
		halt($option);
		if(empty($option['viewOpt'])) {
			return '未选中任何选项!';
		}
		
		if (!in_array('siderealTime_1', $option['viewOpt']))
		{
			$cachePage['siderealTime_1'] = '';
		}else{
			
			$cachePage['siderealTime_1'] = "<li class='li fl'>当前恒星时:<span id='siderealTime_1'></span></li>";
			$cachePage['data'] = 1;
		}
		
		if (!in_array('hourAngle', $option['viewOpt']))
		{
			$cachePage['hourAngle'] = '';
		}else{
			
			$cachePage['hourAngle'] = "<li class='li fl'>当前时角:<span id='hourAngle_1'></span></li>";
			$cachePage['data'] = 1;
		}
		
		//将60cm望远镜页面的html 写入缓存
		$ok = Cache::set('at60Page', $cachePage);
		
		if ($ok)
		{//页面缓存成功
			return '页面设置成功!';
		}else{
			return '页面设置失败!';
		}
	
	}
	
	//显示80cm望远镜配置选择页
	public function at80config ()
	{
		return view('pageConfig/at80config');
	}
}