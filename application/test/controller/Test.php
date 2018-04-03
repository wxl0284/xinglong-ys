<?php
namespace app\test\controller;

use think\Controller;
use think\Cache;
use think\Session;
use think\Db;

class Test extends Controller
{
    public function test ()
    {
        //读取望远镜列表 将已添加的望远镜显示在下拉框中
        $atList = Db::table('atlist')->order('id', 'asc')->field('id, atname')->select();
        if ($atList)
        {
            $vars['atList'] = $atList;
        }else{
            $vars['noAt'] = 1;
        }
        return view('test',$vars);
    }

    public function test1 ()
    {
        return view('test1');
    }

    public function at60 ()
    {
        return view('at60');
    }
}