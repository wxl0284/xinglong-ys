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
Route::get('/', 'xinglong/control/index');
//登陆 路由
Route::post('login', 'xinglong/control/dologin');
//首页 路由
Route::get('front', 'xinglong/control/front');
//退出 路由
Route::get('logout', 'xinglong/control/logout');
//用户管理 路由
Route::any('user', 'xinglong/user/index');