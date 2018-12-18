<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Session;
use think\Db;

//共用的控制器基类
class Base extends Controller
{
    //定义ajax请求无权限时的错误标识
    protected $ajaxAuthErr = 0; //ajax请求时 （提示未登录、无权限等不同情况，赋给不同的值）

    //_initialize方法进行登陆及权限的验证,暂时不写
    protected function _initialize ()
	{
        //查询望远镜列表，并进行模板赋值
        // $this->get_atList();

        // $route = $this->request->routeInfo()['route']; //即: 'xinglong/gimbal/sendCommand'
        // $param = input(); //请求参数
        // halt($param);

    //    $m = $this->request->module(); //模块
    //    $c = $this->request->controller();  //控制器
    //    $a = $this->request->action(); //方法
    //    halt($m . '/' . $c . '/' . $a);
        //$this->success('新增成功', 'At60/index');
        //$this->redirect('At80/index');
        // $aa = $this->request->isAjax();
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