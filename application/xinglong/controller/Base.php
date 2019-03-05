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

        //然后 检查用户的权限
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
            case 'xinglong/page/at_page': //显示各望远镜操控页面
                $this->error('haha');
                break;
            // default:
            //     //如果ajax
            //     if ( $this->request->isAjax() === true )
            //     {
            //         return '您无权限进行此操作!'; die();
            //     }else{
            //         $this->error('您无权限进行此操作');
            //     }
            //     break;
        }
    }//check_auth () 方法结束
}