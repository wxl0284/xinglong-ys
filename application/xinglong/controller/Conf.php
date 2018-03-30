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
        $conf = $this->which_conf ($postData['conf']);
        //halt($postData);
        $res = Db::table('confoption')->insert($postData);
        if ($res)
        {
            return $conf . '新增ok!';
        }else{
            return $conf . '新增失败!';
        }
       
    }//添加 配置的各固定属性 结束

    //获取 配置的各固定属性
    public function get_conf ()
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }
        $postData = input(); //获取表单数据  
        //读取固定属性的值
        $res = Db::table('confoption')->where('conf', $postData['conf'])->select();

        if ($res)
        {
            return json ($res);
        }
        //return 'ni hao';
    }//获取 配置的各固定属性 结束

    //根据表单数据 判断是哪个固定属性
    private function which_conf ($postConf)
    {
        if ( $postConf == 'focustype')
        {
            $conf = '望远镜焦点类型';
        }else if ($postConf == 'focusratio'){
            $conf = '焦距';
        }else if ($postConf == 'imageBits'){
            $conf = '图像位数';
        }else if ($postConf == 'coolerMode'){
            $conf = '制冷方式';
        }else if ($postConf == 'readoutMode'){
            $conf = '读出模式';
        }else if ($postConf == 'readoutSpeed'){
            $conf = '读出速度模式';
        }else if ($postConf == 'transferSpeed'){
            $conf = '转移速度模式';
        }else if ($postConf == 'gainmode'){
            $conf = '增益模式';
        }else if ($postConf == 'gainNumber'){
            $conf = '增益档位';
        }else if ($postConf == 'ShutterType'){
            $conf = '快门类型';
        }else if ($postConf == 'ShutterMode'){
            $conf = '快门模式';
        }else if ($postConf == 'BinArray'){
            $conf = 'BIN';
        }else if ($postConf == 'InterfaceType'){
            $conf = 'ccd接口类型';
        }else if ($postConf == 'ExposeTriggerMode'){
            $conf = '曝光触发模式';
        }else if ($postConf == 'FilterSystem'){
            $conf = '滤光片类型';
        }else if ($postConf == 'FilterShape'){
            $conf = '滤光片形状';
        }else if ($postConf == 'slaveDomeType'){
            $conf = '随动圆顶类型';
        }else if ($postConf == 'openDomeType'){
            $conf = '全开圆顶类型';
        }else if ($postConf == 'opticalStructure'){
            $conf = '导星镜焦点类型';
        }

        return $conf;
    }//根据表单数据 判断是哪个固定属性 结束
}