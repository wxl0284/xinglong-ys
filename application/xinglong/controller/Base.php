<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Session;
use think\Db;

//共用的控制器基类
class Base extends Controller
{
    protected $ajaxAuthErr = ''; //ajax请求无权限时的错误标识（提示未登录、无权限等不同情况，赋给不同的值）
    protected $userId = ''; //登录用户的id, 在被继承的控制器中赋值给$user
    protected $userName = ''; //登录用户的用户名
    protected $id_aperture = []; //存储各望远镜id及口径值，例如['3'=>'60cm']
    protected $input = []; //提交的参数

    protected function _initialize () //最先执行的方法
	{
        $this->get_atList();//查询望远镜列表，并进行模板赋值
        //dump(Session::has('login'));die();
        //检查是否已登录
        if ( !Session::has('login') )//未登录
        {
            if ( $this->request->isAjax() === false ) //非ajax请求
            {
                $this->error('请先登录再进行其他操作', '/');
            }else{//ajax请求
                $this->ajaxAuthErr = 'not_log';
            }
           
        }else{//已登录,获取用户的id
            $this->userId = Session::get('userId');
            $this->userName = Session::get('login'); //便于后面的方法中直接使用用户名，而不用再查数据库
        }//检查是否已登录 结束

        $this->input = input(); //获取提交的参数
        //halt($this->request->routeInfo()['route']); die();
        //然后 检查普通用户的权限
        if ( Session::get('role') !== 1 )
        {
            $route = $this->request->routeInfo()['route']; //即: 'xinglong/gimbal/sendCommand'
            
            $this->check_auth($route);
        }//检查用户的权限 结束

        /*$param = input(); //请求参数
        halt($param);

        $this->success('新增成功', 'At60/index');
        $this->redirect('At80/index');
        $aa = $this->request->isAjax();*/
    }//_initialize方法 结束

    /*查询望远镜列表，并进行模板赋值*/
    protected function get_atList()
    {
        //查询望远镜数据
        $res = Db::table('atlist')->order('id asc')->select();
        if ( $res )
        {
            //将id 与表中其他字段一一对应起来
            foreach ( $res as $v )
            {
                $at_id[$v['id']] = $v['atid']; //将id 和 望远镜唯一id对应起来
                $at_name[$v['id']] = $v['atname']; //将id 和 望远镜名称 对应起来
                $at_longitude[$v['id']] = $v['longitude']; //将id 和 经度 对应起来
                $at_latitude[$v['id']] = $v['latitude']; //将id 和 纬度 对应起来
                $at_aperture[$v['id']] = $v['aperture']; //将id 和 口径 对应起来
            }
         
            $this->assign([
                'atList' => $res,
                'at_id' => $at_id,
                'at_name' => $at_name,
                'at_longitude' => $at_longitude,
                'at_latitude' => $at_latitude,
                'at_aperture' => $at_aperture,
            ]);

            $this->id_aperture = $at_aperture; //存储方便在check_auth()中使用
        }
    }/*查询望远镜列表，并进行模板赋值 结束*/

    /*check_auth ()： 检查普通用户的权限，如果没有某个权限则提示该用户无权操作
    *参数$route: 请求的路由地址 
    */
    protected function check_auth ($route)
    {
        //逐一对所有路由进行比对 检查（除了某些路由，如登录 退出及一些无关紧要的操作，例如查看首页、天气情况等）
        switch ($route)
        {
            case 'xinglong/page/front': //显示首页 不检查权限
                break;
            case 'xinglong/user/passwd': //显示修改密码页 不检查权限
                break;
            case 'xinglong/user/editPasswd': //执行修改密码 不检查权限
                break;
            case 'xinglong/page/weather': //显示天气详情页面 不检查权限
                break;
            case 'xinglong/page/ajax_get_weather': //ajax 每分钟请求环境信息 不检查权限
                break;
            case 'xinglong/page/at_page': //显示各望远镜操控页面

                if ( empty($this->id_aperture) )
                {
                    $this->error('未查到望远镜数据!');
                }elseif( count($this->id_aperture) > 0 )
                {
                    $current_apert = $this->id_aperture[$this->input['at']];
                   
                    if ( strpos(Session::get('look'), $current_apert) === false )
                    {//当前请求页面的望远镜口径值不在look字段中，即无权查看此望远镜
                        $this->error('您无权查看此望远镜!');
                    }  
                }
                break;
            case 'xinglong/user/index': //显示用户管理页面及查询所有用户数据
                $this->error('您无权执行此操作!'); break;
            case 'xinglong/user/add': //显示用户添加页面
                $this->error('您无权执行此操作!'); break;
            case 'xinglong/user/doadd': //执行 用户添加
                $this->ajaxAuthErr = 'no_auth'; break; //'no_auth' 代表无权限
            case 'xinglong/user/edit': //显示某用户编辑页面
                $this->error('您无权执行此操作!'); break;
            case 'xinglong/user/doedit': //执行 用户编辑
                $this->ajaxAuthErr = 'no_auth'; break;
            case 'xinglong/user/off': //禁用 用户
                $this->error('您无权执行此操作!'); break;
            case 'xinglong/user/on': //启用 用户
                $this->error('您无权执行此操作!'); break;
            case 'xinglong/weather/weatherDetail': //天气 详情 显示不同时间段环境信息折线图
                break;
            case 'xinglong/status/get_status': //ajax实时获取各子设备状态信息
                break;
            case 'xinglong/gimbal/sendCommand': //发送转台指令
                //判断是否有权限
                if ( !isset($this->input['at_aperture']) || strpos(Session::get('operate'), $this->input['at_aperture']) === false )
                {
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/ccd/sendCommand': //发送CCD指令
                //判断是否有权限
                if ( !isset($this->input['at_aperture']) || strpos(Session::get('operate'), $this->input['at_aperture']) === false )
                {
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/focus/sendCommand': //发送调焦器指令
                //判断是否有权限
                if ( !isset($this->input['at_aperture']) || strpos(Session::get('operate'), $this->input['at_aperture']) === false )
                {
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/slavedome/sendCommand': //发送随动圆顶指令
                //判断是否有权限
                if ( !isset($this->input['at_aperture']) || strpos(Session::get('operate'), $this->input['at_aperture']) === false )
                {
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/opendome/sendCommand': //发送全开圆顶指令
                //判断是否有权限
                if ( !isset($this->input['at_aperture']) || strpos(Session::get('operate'), $this->input['at_aperture']) === false )
                {
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/filter/sendCommand': //发送滤光片指令
                //判断是否有权限
                if ( !isset($this->input['at_aperture']) || strpos(Session::get('operate'), $this->input['at_aperture']) === false )
                {
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/plan/importPlan': //导入观测计划
                //判断是否有权限
                if ( !isset($this->input['aperture']) || strpos(Session::get('operate'), $this->input['aperture']) === false )
                {
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/plan/sendData': //观测计划之发送（提交）和开始执行
                //判断是否有权限
                if ( !isset($this->input['at_aperture']) || strpos(Session::get('operate'), $this->input['at_aperture']) === false )
                {
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/page/at_add': //显示添加望远镜页面 （仅管理员可以添加望远镜）
                $this->error('您无权执行此操作!'); break;
            case 'xinglong/page/atlist': //显示望远镜列表
                break;
            case 'xinglong/page/at_doadd': //执行 望远镜添加
                $this->ajaxAuthErr = 'no_auth'; break;
            case 'xinglong/page/at_edit': //显示 望远镜编辑页面
                $this->error('您无权执行此操作!'); break;
            case 'xinglong/page/at_doedit': //执行 望远镜编辑
                $this->ajaxAuthErr = 'no_auth'; break;
            case 'xinglong/page/at_delete': //删除 望远镜
                $this->error('您无权执行此操作!'); break;
            case 'xinglong/conf/index': //显示 需动态增减的固定属性配置页
                break;
            case 'xinglong/conf/conf_add': //添加 动态增减的固定属性
                if ( empty( Session::get('operate') ) )
                {//没有权限 操作任一望远镜
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/conf/get_conf': //查看 动态增减的固定属性
                break;
            case 'xinglong/conf/delete_conf': //删除 动态增减的固定属性
                if ( empty( Session::get('operate') ) )
                {//没有权限 操作任一望远镜
                    $this->ajaxAuthErr = 'no_auth';
                }    
                break;
            case 'xinglong/page/at_config': //显示望远镜配置页面 每个用户都可以看   
                break;
            case 'xinglong/page/config': //望远镜配置页面 选择望远镜下拉框 change事件 ajax   
                break; //每个用户都可以看
            case 'xinglong/atconfig/gimbal_config': //提交转台配置数据 ajax
                $current_apert = $this->id_aperture[$this->input['teleid']];

                if ( strpos(Session::get('operate'), $current_apert) === false )
                {//当配置的望远镜口径值不在operate字段中，即无权配置此望远镜
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/atconfig/ccd_config': //提交ccd配置数据 ajax
                $current_apert = $this->id_aperture[$this->input['teleid']];

                if ( strpos(Session::get('operate'), $current_apert) === false )
                {//当配置的望远镜口径值不在operate字段中，即无权配置此望远镜
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/atconfig/gainNoiseConfig': //提交ccd之增益值和读出噪声值 ajax
                $current_apert = $this->id_aperture[$this->input['teleid']];

                if ( strpos(Session::get('operate'), $current_apert) === false )
                {//当配置的望远镜口径值不在operate字段中，即无权配置此望远镜
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/atconfig/gainNoiseSort': //ajax 对ccd增益-读出噪声表格各列排序
                break;//似乎无此操作了
            case 'xinglong/atconfig/filter_config': //ajax 提交滤光片配置数据
                $current_apert = $this->id_aperture[$this->input['teleid']];

                if ( strpos(Session::get('operate'), $current_apert) === false )
                {//当配置的望远镜口径值不在operate字段中，即无权配置此望远镜
                    $this->ajaxAuthErr = 'no_auth';
                }   
                break;
            case 'xinglong/atconfig/slaveDome_config': //ajax 提交随动圆顶配置数据
                $current_apert = $this->id_aperture[$this->input['teleid']];

                if ( strpos(Session::get('operate'), $current_apert) === false )
                {//当配置的望远镜口径值不在operate字段中，即无权配置此望远镜
                    $this->ajaxAuthErr = 'no_auth';
                }   
                break;
            case 'xinglong/atconfig/oDome_config': //ajax 提交全开圆顶配置数据
                $current_apert = $this->id_aperture[$this->input['teleid']];

                if ( strpos(Session::get('operate'), $current_apert) === false )
                {//当配置的望远镜口径值不在operate字段中，即无权配置此望远镜
                    $this->ajaxAuthErr = 'no_auth';
                }   
                break;
            case 'xinglong/atconfig/focus_config': //ajax 提交调焦器配置数据
                $current_apert = $this->id_aperture[$this->input['teleid']];

                if ( strpos(Session::get('operate'), $current_apert) === false )
                {//当配置的望远镜口径值不在operate字段中，即无权配置此望远镜
                    $this->ajaxAuthErr = 'no_auth';
                }   
                break;
            case 'xinglong/atconfig/guideScope_config': //ajax 提交导星镜配置数据
                $current_apert = $this->id_aperture[$this->input['teleid']];

                if ( strpos(Session::get('operate'), $current_apert) === false )
                {//当配置的望远镜口径值不在operate字段中，即无权配置此望远镜
                    $this->ajaxAuthErr = 'no_auth';
                }   
                break;
            case 'xinglong/atconfig/downLoadFlie': //ajax 下载配置说明文件
                break;//都有权限
            case 'xinglong/too/ToO': //显示 提交协同观测页面
                $this->error('您无权执行此操作!'); break;
            case 'xinglong/too/ToO_1': //显示 提交ToO计划页面
                $this->error('您无权执行此操作!'); break;
            case 'xinglong/too/send_ToO_plan': //提交 提交协同观测计划 ajax
                $this->ajaxAuthErr = 'no_auth'; break;//只有管理员有权限
            case 'xinglong/too/send_ToO_1_plan': //提交 提交ToO观测计划 ajax
                $this->ajaxAuthErr = 'no_auth'; break;//只有管理员有权限
            case 'xinglong/status/changeimport': //点击导入 把plancooper中import字段或giveup字段变为1 ajax
                if ( !isset($this->input['at_aperture']) || strpos(Session::get('operate'), $this->input['at_aperture']) === false )
                {
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/status/changetooimport': //点击导入 把plantoo中import字段或giveup字段变为1 ajax
                if ( !isset($this->input['at_aperture']) || strpos(Session::get('operate'), $this->input['at_aperture']) === false )
                {
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/image/get_image': //望远镜页面 显示观测图像 ajax
                if ( !isset($this->input['at_aperture']) || ( strpos(Session::get('look'), $this->input['at_aperture']) === false && strpos(Session::get('operate'), $this->input['at_aperture']) === false) )
                {//既不能查看也不能操作此望远镜时
                    $this->ajaxAuthErr = 'no_auth';
                }    
                break;
            case 'xinglong/page/more_cloud_pic': //查看更多云量相机图片
                break;
            case 'xinglong/page/ajax_get_cloud_pic': //ajax 获取云量图像
                break;
            case 'xinglong/page/whole_day_pic': //显示 全天观测图片
                if ( !isset($this->input['aperture']) || ( strpos(Session::get('look'), $this->input['aperture']) === false && strpos(Session::get('operate'), $this->input['aperture']) === false) )
                {//既不能查看也不能操作此望远镜时
                    $this->error('您无权执行此操作!');
                }
                break;
            case 'xinglong/page/down_multi_fit': //ajax 全天观测图片 下载多个图片
                if ( !isset($this->input['aperture']) || ( strpos(Session::get('look'), $this->input['aperture']) === false && strpos(Session::get('operate'), $this->input['aperture']) === false) )
                {//既不能查看也不能操作此望远镜时
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            case 'xinglong/page/down_fits_pic': //ajax 下载当前一个fits图片
                if ( !isset($this->input['aperture']) || ( strpos(Session::get('look'), $this->input['aperture']) === false && strpos(Session::get('operate'), $this->input['aperture']) === false) )
                {//既不能查看也不能操作此望远镜时
                    $this->ajaxAuthErr = 'no_auth';
                }
                break;
            default:
                if ( $this->request->isAjax() === true )
                {//如果ajax
                    return '您无权限进行此操作!'; die();
                }else{//不是ajax
                    $this->error('您请求的URL无效!');
                }
                break;
        }//switch 结束
    }//check_auth () 方法结束
}