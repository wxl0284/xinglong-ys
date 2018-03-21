<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Session;

//共用的控制器基类
class Base extends Controller
{
    //定义ajax请求无权限时的错误标识
    protected $ajaxAuthErr = 0;

    //_initialize方法进行登陆及权限的验证,暂时不写
    protected function _initialize ()
	{
        // echo $this->request->controller();
        // echo $this->request->action();
        //$this->success('新增成功', 'At60/index');
        //$this->redirect('At80/index');
        // $aa = $this->request->isAjax();
    }//_initialize方法 结束
    
}