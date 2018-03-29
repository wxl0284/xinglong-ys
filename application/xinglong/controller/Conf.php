<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
// use think\Cache;
// use think\Session;
use think\Db;

class Conf extends Base
{
    //显示要配置的各固定属性入口页
    public function index ()
    {
        return view('conf_index');
    }//显示要配置的各固定属性入口页 结束

    //添加 配置的各固定属性
    public function conf_add ()
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }
        $postData = input();
        if (!$postData['conf_val'])
        {
            return '数据提交失败,请重新提交!';
        }

        //判断要入库的是哪个固定属性
        if ( $postData['conf'] == 'focustype')
        {
            $conf = '望远镜焦点类型';
        }else if ($postData['conf'] == ''){
            $conf = '';
        }

        //halt($postData);
        $res = Db::table('confoption')->insert($postData);
        if ($res)
        {
            return $conf . '新增ok!';
        }else{
            return $conf . '新增失败!';
        }
       
    }//添加 配置的各固定属性 结束
}