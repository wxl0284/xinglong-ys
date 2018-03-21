<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

//登陆页 路由
Route::get('/$', 'xinglong/login/index');
//执行登陆 路由
Route::post('login$', 'xinglong/login/dologin');
//退出 路由
Route::get('logout$', 'xinglong/login/logout');
//首页 路由
Route::get('front$', 'xinglong/page/front');
//用户管理 路由
Route::any('user$', 'xinglong/user/index');
//显示用户添加页面 路由
Route::get('user/add$', 'xinglong/user/add');
//添加用户 路由
Route::post('user/doadd$', 'xinglong/user/doadd');
//显示用户编辑页面 路由
Route::get('user/edit/:id$', 'xinglong/user/edit', [], ['id'=>'\d+']);