<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Session;
use think\Db;

//共用的控制器基类
class Base extends Controller
{
    //定义ajax请求无权限时的错误标识
    protected $ajaxAuthErr = 0;

    //_initialize方法进行登陆及权限的验证,暂时不写
    protected function _initialize ()
	{
        //查询望远镜列表，并进行模板赋值
        $this->get_atList();



        //$this->request->routeInfo()['route']; //获取路由
        // echo $this->request->controller();
        // echo $this->request->action();
        //$this->success('新增成功', 'At60/index');
        //$this->redirect('At80/index');
        // $aa = $this->request->isAjax();
    }//_initialize方法 结束

    /*查询望远镜列表，并进行模板赋值*/
    protected function get_atList()
    {
        //查询望远镜数据
        $res = Db::table('atlist')->field('id, atname')->select();
        if ( $res )
        {
            $this->assign('atList', $res);
        }
    }
    /*查询望远镜列表，并进行模板赋值 结束*/
}