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
        }elseif ( $postData['conf'] == 'readoutMode' )   //验证 读出模式
         {
             if ( !$this->check_readoutMode($postData['conf_val']) )
             {
                 return '读出模式格式错误';
             }
         }elseif ( $postData['conf'] == 'transferSpeed' )   //验证 转移速度模式
         {
             if ( !$this->check_transferSpeed($postData['conf_val']) )
             {
                 return '转移速度模式格式错误';
             }
         }elseif ( $postData['conf'] == 'gainmode' )   //验证 增益模式
         {
             if ( !$this->check_gainmode($postData['conf_val']) )
             {
                 return '增益模式格式错误';
             }
         }elseif ( $postData['conf'] == 'ShutterType' )   //验证 快门类型
         {
             if ( !$this->check_ShutterType($postData['conf_val']) )
             {
                 return '快门类型格式错误';
             }
         }elseif ( $postData['conf'] == 'ShutterMode' )   //验证 快门模式
         {
             if ( !$this->check_ShutterMode($postData['conf_val']) )
             {
                 return '快门模式格式错误';
             }
         }elseif ( $postData['conf'] == 'InterfaceType' )   //验证 ccd接口类型
         {
             if ( !$this->check_InterfaceType($postData['conf_val']) )
             {
                 return 'ccd接口类型格式错误';
             }
         }elseif ( $postData['conf'] == 'ExposeTriggerMode' )   //验证 曝光触发模式
         {
             if ( !$this->check_ExposeTriggerMode($postData['conf_val']) )
             {
                 return '曝光触发模式格式错误';
             }
         }elseif ( $postData['conf'] == 'FilterSystem' )   //验证 滤光片类型
         {
             if ( !$this->check_FilterSystem($postData['conf_val']) )
             {
                 return '滤光片类型格式错误';
             }
         }elseif ( $postData['conf'] == 'FilterShape' )   //验证 滤光片形状
         {
             if ( !$this->check_FilterShape($postData['conf_val']) )
             {
                 return '滤光片形状格式错误';
             }
         }elseif ( $postData['conf'] == 'slaveDomeType' || $postData['conf'] == 'openDomeType' || $postData['conf'] == 'opticalStructure')   //验证 随动圆顶类型
         {
             if ( !$this->check_slaveDomeType($postData['conf_val']) )
             {
                 return '随动圆顶类型格式错误';
             }
         }elseif ( $postData['conf'] == 'openDomeType' )   //验证 全开圆顶类型
         {
             if ( !$this->check_slaveDomeType($postData['conf_val']) )
             {
                 return '全开圆顶类型格式错误';
             }
         }elseif ( $postData['conf'] == 'opticalStructure' )   //验证 导星镜焦点类型
         {
             if ( !$this->check_slaveDomeType($postData['conf_val']) )
             {
                 return '导星镜焦点类型格式错误';
             }
         }/*验证表单数据 结束*/
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
        }else if ($postConf == 'imageBits'){
            $conf = '图像位数';
        }else if ($postConf == 'coolerMode'){
            $conf = '制冷方式';
        }else if ($postConf == 'readoutMode'){
            $conf = '读出模式';
        }else if ($postConf == 'gainmode'){
            $conf = '增益模式';
        }else if ($postConf == 'ShutterType'){
            $conf = '快门类型';
        }else if ($postConf == 'ShutterMode'){
            $conf = '快门模式';
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

    /*验证 读出模式*/
    protected function check_readoutMode ($val)
    {
        //if ( !preg_match('/^支持[\x{4e00}-\x{9af5}]+读出$/u', $val) )
        if ( strlen($val) < 1 )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 读出模式 结束*/

    /*验证 增益模式*/
     protected function check_gainmode ($val)
     {
         if ( !preg_match('/^\w+ \w+$/', $val) )
         {
             return false;
         }else{
             return true;
         }
     }/*验证 增益模式 结束*/

    /*验证 快门类型*/
    protected function check_ShutterType ($val)
    {
        if ( !preg_match('/^[\x{4e00}-\x{9af5}]+快门$/u', $val) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 快门类型 结束*/

    /*验证 快门模式*/
    protected function check_ShutterMode ($val)
    {
        //if ( !preg_match('/^支持\w+$/', $val) )
        if ( strlen($val) < 1 )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 快门模式 结束*/

    /*验证 ccd接口类型*/
    protected function check_InterfaceType ($val)
    {
        //if ( !preg_match('/^支持\w+(\w|.)*$/', $val) )
        if ( strlen($val) < 1 )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 ccd接口类型 结束*/

    /*验证 曝光触发模式*/
    protected function check_ExposeTriggerMode ($val)
    {
        if ( !preg_match('/^支持\S+触发$/', $val) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 曝光触发模式 结束*/

    /*验证 滤光片类型*/
    protected function check_FilterSystem ($val)
    {
        if ( !preg_match('/^\S(\S| )*$/', $val) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 滤光片类型 结束*/

    /*验证 滤光片形状*/
    protected function check_FilterShape ($val)
    {
        if ( !preg_match('/^[\x{4e00}-\x{9af5}]+形$/u', $val) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证 滤光片形状 结束*/

    /*验证随动及全开圆顶类型，导星镜焦点类型*/
    function check_slaveDomeType ($val)
    {
        //输入格式 圆形
        if ( !preg_match('/^.+$/', $val) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证随动及全开圆顶类型，导星镜焦点类型 结束*/
}