<?php
/*此控制器：对各望远镜的固定属性进行配置*/
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Db;

class Atconfig extends Base
{
    /*获取转台配置项表单 存入表atlist中*/
    public function gimbal_config()
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        //$postData = input('maxAxis3Speed');
        //$file = request()->file('instruction');
        //dump($postData);
        //dump($file);
        // $postData = input();

        // if ( !isset($postData['maxAxis3Speed']) )
        // {
        //     $postData['maxAxis3Speed'] = null;
        // }
        // dump($postData['maxAxis3Speed']);

        //处理表单数据，如果表单中：无某项数据或某项数据为空字符串，则将$postData中某项数据置为NULL
        // if ( $postData['ip'] === '')
        // {
        //     $postData['ip'] = null;
        // }
        $postData = input();
        $res = Db::table('atlist')->where('id', 1)->update($postData);
        //处理表单数据，如果表单中：无某项数据或某项数据为空字符串，则将$postData中某项数据置为NULL 结束
    }/*获取转台配置项表单 存入表atlist中 结束*/
}