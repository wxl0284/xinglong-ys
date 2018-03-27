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
//望远镜操控页面 路由
Route::get('atpage/:at$', 'xinglong/page/at_page', [], ['at'=>'\d{2}']);
//显示添加望远镜 路由
Route::get('atadd$', 'xinglong/page/at_add');
//执行添加望远镜 路由
Route::post('at_doadd$', 'xinglong/page/at_doadd');
//显示望远镜配置页面 路由
Route::get('atconfig$', 'xinglong/page/at_config');
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
//进行用户编辑 路由
Route::post('user/doEdit$', 'xinglong/user/doedit');
//禁用用户 路由
Route::get('user/off/:id$', 'xinglong/user/off');
//启用用户 路由
Route::get('user/on/:id$', 'xinglong/user/on');
//显示修改密码页面 路由
Route::get('user/passwd$', 'xinglong/user/passwd');
//执行修改密码 路由
Route::post('user/edit_passwd$', 'xinglong/user/editPasswd');
//天气 显示详情 路由
Route::get('weather$', 'xinglong/page/weather');
//发送转台指令 路由
Route::post('gimbal$', 'xinglong/gimbal/sendCommand');
//发送CCD指令 路由
Route::post('ccd$', 'xinglong/ccd/sendCommand');
//发送调焦器指令 路由
Route::post('focus$', 'xinglong/focus/sendCommand');