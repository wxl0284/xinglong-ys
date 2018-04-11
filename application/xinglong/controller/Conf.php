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

        /*验证表单数据*/
        if ( $postData['conf'] == 'focustype' ) //验证 焦点类型
        {
            if ( !$this->check_focustype($postData['conf_val']) )
            {
                return '焦点类型格式错误';
            }
        }elseif ( $postData['conf'] == 'focusratio' )    //验证 焦比
        {
            if ( !$this->check_focusratio($postData['conf_val']) )
            {
                return '焦比格式错误';
            }
        }elseif ( $postData['conf'] == 'imageBits' ) //验证 图像位数
        {
            if ( !$this->check_imageBits($postData['conf_val']) )
            {
                return '图像位数格式错误';
            }
        }elseif ( $postData['conf'] == 'coolerMode' )//验证 制冷方式
        {
            if ( !$this->check_coolerMode($postData['conf_val']) )
            {
                return '制冷方式格式错误';
            }
        }elseif ( $postData['conf'] == 'coolerMode' )   //验证 读出模式
         {
             if ( !$this->check_coolerMode($postData['conf_val']) )
             {
                 return '制冷方式格式错误';
             }
         }  //4.11 doing
        /*验证表单数据 结束*/
        //判断提交的固定属性值是否已存在
        $res = Db::table('confoption')->where('conf', $postData['conf'])->column('conf_val');
        if( in_array($postData['conf_val'], $res) )
        {
            return '已添加此固定属性值!';
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
        //halt($res);
        if ($res)
        {
            return json ($res);
        }else{
            return 0; //必须return回一个数据，否则ajax的success方法接收不到数据
        }
        //return 'ni hao';
    }//获取 配置的各固定属性 结束

    //删除不需要的 配置的各固定属性
    public function delete_conf ()
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $id = input('id');
        $res = Db::table('confoption')->delete($id);
        if ($res)
        {
            return '删除成功!'; 
        }else{
            return '删除失败';
        }
    }
    //删除不需要的 配置的各固定属性 结束

    //根据表单数据 判断是哪个固定属性
    private function which_conf ($postConf)
    {
        if ( $postConf == 'focustype')
        {
            $conf = '望远镜焦点类型';
        }else if ($postConf == 'focusratio'){
            $conf = '焦比';
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

    /*验证 焦点类型*/
    protected function check_focustype ($val)
    {
        if ( !preg_match('/^\[\w+ \w+\]$/', $val) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 焦点类型 结束*/

    /*验证 焦比*/
    protected function check_focusratio ($val)
    {
        if ( !preg_match('/^\[[0-9.]+ [0-9.]+\]$/', $val) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 焦比 结束*/

    /*验证 图像位数*/
    protected function check_imageBits ($val)
    {
        if ( !preg_match('/^[1-9]+$/', $val) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 图像位数 结束*/

    /*验证 制冷方式*/
    protected function check_coolerMode ($val)
    {
        if ( !preg_match('/^[\x{4e00}-\x{9af5}-]+$/u', $val) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 制冷方式 结束*/


}