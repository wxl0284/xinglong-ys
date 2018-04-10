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
//显示望远镜列表 路由
Route::get('atlist$', 'xinglong/page/atlist');
//执行添加望远镜 路由
Route::post('at_doadd$', 'xinglong/page/at_doadd');
//显示望远镜配置页面 路由
Route::get('atconfig$', 'xinglong/page/at_config');
//望远镜配置页面'望远镜选择下拉选择框'的ajax请求判断19个固定属性是否已添加 路由 
Route::post('conf_num$', 'xinglong/page/conf_num');
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
//发送随动圆顶指令 路由
Route::post('slavedome$', 'xinglong/slavedome/sendCommand');
//发送全开圆顶指令 路由
Route::post('opendome$', 'xinglong/opendome/sendCommand');
//发送滤光片指令 路由
Route::post('filter$', 'xinglong/filter/sendCommand');
//导入观测计划 路由
Route::post('importplan$', 'xinglong/plan/importPlan');
//观测计划之发送和开始执行 路由
Route::post('plan$', 'xinglong/plan/sendData');
//显示望远镜编辑页面 路由
Route::get('atedit/:at$', 'xinglong/page/at_edit', [], ['at'=>'\d{5}']);
//执行望远镜编辑 路由
Route::post('at_doedit$', 'xinglong/page/at_doedit');
//删除望远镜 路由
Route::get('at_delete/:atid$', 'xinglong/page/at_delete');
//显示需动态增减的固定属性配置入口页 路由
Route::get('conf_option$', 'xinglong/conf/index');
//添加：动态增减的固定属性 路由
Route::post('conf_option_add$', 'xinglong/conf/conf_add');
//获取：动态增减的固定属性列表 路由
Route::post('get_conf$', 'xinglong/conf/get_conf');
//删除：动态增减的固定属性列表 路由
Route::post('del_conf$', 'xinglong/conf/delete_conf');

/*配置各望远镜固定属性 路由*/
//显示配置页面 路由
Route::get('config_page$', 'xinglong/atconfig/config_page');




//显示不同的望远镜
Route::get('test2/:at$', 'test/test/test2');
//显示配置页面
Route::get('test1$', 'test/test/test1');
/*配置各望远镜固定属性 结束*/

/*如下为望远镜配置的示例 路由*/
//测试首页 
Route::get('test$', 'test/test/test');
//显示不同的望远镜
Route::get('test2/:at$', 'test/test/test2');
//显示配置页面
Route::get('test1$', 'test/test/test1');

/*如下为望远镜配置的示例 结束*/