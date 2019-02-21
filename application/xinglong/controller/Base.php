<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Session;
use think\Db;

//共用的控制器基类
class Base extends Controller
{
    protected $ajaxAuthErr = 0; //ajax请求无权限时的错误标识（提示未登录、无权限等不同情况，赋给不同的值）
    protected $userId = ''; //登录用户的id, 在被继承的控制器中赋值给$user
    protected $userName = ''; //登录用户的用户名

    protected function _initialize () //最先执行的方法
	{
        $this->get_atList();//查询望远镜列表，并进行模板赋值

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

        //然后 检查用户的权限 暂时不写

        //halt($this->userId);

        /* $route = $this->request->routeInfo()['route']; //即: 'xinglong/gimbal/sendCommand'
        $param = input(); //请求参数
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
            foreach ( $res as $v)
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
}