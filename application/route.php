<?php
use think\Route;

//登陆页 路由
Route::get('/$', 'xinglong/login/index');
//执行登陆 路由
Route::post('login$', 'xinglong/login/dologin');
//退出 路由
Route::get('logout$', 'xinglong/login/logout');
//显示各望远镜操控页面 路由
Route::get('atpage/:at$', 'xinglong/page/at_page', [], ['at'=>'\d{1,}']);
//首页 路由
Route::get('front$', 'xinglong/page/front');
//用户管理及用户查询 路由
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
//ajax 每分钟请求环境信息 显示详情 路由
Route::post('get_weater$', 'xinglong/page/ajax_get_weather');
//天气 显示详情 显示不同时间段环境信息折线图 路由
Route::get('weather_info/:hour$', 'xinglong/weather/weatherDetail');
//ajax实时获取各子设备状态信息 路由
Route::post('get_status$', 'xinglong/status/get_status');
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
//显示添加望远镜页面 路由
Route::get('atadd$', 'xinglong/page/at_add');
//显示望远镜列表 路由
Route::get('atlist$', 'xinglong/page/atlist');
//执行添加望远镜 路由
Route::post('at_doadd$', 'xinglong/page/at_doadd');
//显示望远镜编辑页面 路由
Route::get('atedit/:at$', 'xinglong/page/at_edit', [], ['at'=>'\d{1,}']);
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
//显示望远镜配置页面 路由
Route::get('atconfig$', 'xinglong/page/at_config');
//望远镜配置页面'望远镜选择下拉选择框'的ajax请求判断16个固定属性并获取配置的json数据 路由 
Route::post('config$', 'xinglong/page/config');
//ajax提交转台配置数据 路由
Route::post('gimbal_config$', 'xinglong/atconfig/gimbal_config');
//ajax提交ccd配置数据 路由
Route::post('ccd_config$', 'xinglong/atconfig/ccd_config');
//ajax提交ccd之增益值和读出噪声值配置数据 路由
Route::post('gain_noise$', 'xinglong/atconfig/gainNoiseConfig');
//ajax 对ccd增益-读出噪声表格各列排序 路由
Route::post('gainNoiseSort$', 'xinglong/atconfig/gainNoiseSort');
//ajax提交滤光片配置数据 路由
Route::post('filter_config$', 'xinglong/atconfig/filter_config');
//ajax提交随动圆顶配置数据 路由
Route::post('slaveDome_config$', 'xinglong/atconfig/slaveDome_config');
//ajax提交全开圆顶配置数据 路由
Route::post('oDome_config$', 'xinglong/atconfig/oDome_config');
//ajax提交调焦器配置数据 路由
Route::post('focus_config$', 'xinglong/atconfig/focus_config');
//ajax提交导星望远镜配置数据 路由
Route::post('guideScope_config$', 'xinglong/atconfig/guideScope_config');
//属性配置页面 下载说明文件 路由
Route::get('download/:dir/:filename$', 'xinglong/atconfig/downLoadFlie');
/*配置各望远镜固定属性 结束*/
//计划之 显示协同观测页面 ToO 路由
Route::get('ToO$', 'xinglong/Too/ToO');
//计划之 显示ToO计划页面 路由
Route::get('ToO_1$', 'xinglong/Too/ToO_1');
//页面 提交协同观测 ToO 路由
Route::post('ToO_plan$', 'xinglong/Too/send_ToO_plan');
//页面 提交ToO观测 ToO 路由
Route::post('ToO_1_plan$', 'xinglong/Too/send_ToO_1_plan');
//页面 提交协同观测 ToO 之开始或停止 路由
Route::post('ToO_start_stop$', 'xinglong/Too/start_stop_ToO');
//ajax 将协同计划表中import字段或giveup字段变为1 路由
Route::post('change_plancooper_import$', 'xinglong/Status/change_cooper_import');
//ajax 将ToO计划表中import字段变为1 路由
Route::post('change_plantoo_import$', 'xinglong/Status/change_too_import');
//ajax 获取观测图像 路由
Route::post('get_image$', 'xinglong/Image/get_image');
/*/ajax 观测图像 向右按钮 路由
Route::post('next4$', 'xinglong/Image/next4_image');
/ajax 观测图像 向左按钮 路由
Route::post('pre4$', 'xinglong/Image/pre4_image');*/

//ajax 查看更多云量相机图片 路由
Route::get('/weather/clouds', 'xinglong/Page/more_cloud_pic');
//ajax 获云量图像 路由
Route::post('get_cloud_pic$', 'xinglong/Page/ajax_get_cloud_pic');
//显示 全天观测图片 路由
Route::get('/whole_day_pic/:aperture$', 'xinglong/Page/whole_day_pic');
//ajax 全天观测图片 下载多个图片 路由
Route::post('/down_multi_fit$', 'xinglong/Page/down_multi_fit');
//ajax 下载当前一个fits图片 路由
Route::post('/down_fits_pic$', 'xinglong/page/down_fits_pic');

/*如下为望远镜配置的示例 路由*/
//测试首页 
Route::get('test$', 'xl/test/test');
/*如下为望远镜配置的示例 结束*/