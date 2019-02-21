<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Session;
use think\Db;
use think\Cookie;

//共用的控制器基类
class Login extends Controller
{    
    //显示登陆页面
    public function index ()
    {
        
		if ( Session::has('login') ) //如果已登录，直接去首页
		{
			$this->redirect('/front');
		}else {//未登录
			return view('login');
		}
    }//显示登陆页面 结束


     //进行登陆
    public function dologin ()
    {
        $postData = input(); //获取表单数据
        if (!$postData)
        {
            $this->error('提交数据有误！');
        }
        $username = $postData['username'];
        $password = $postData['password'];
       
        //验证数据 validate方法
        $result	= $this->validate(
            [
                '用户名' =>	$username,
                '__token__'	=> $postData['__token__'],
                '密码' => $password,
            ],
            
            [
                '用户名' =>	'require|max:12|min:6',
                '__token__'	=>	'token',
                '密码' => 'require|min:6|max:12',
        ]);
        if(true	!==	$result)    //验证失败 输出错误信息
        { 
            return $this->error($result);
        }
        
        //判断登录用户名和密码有效性 用预处理 防止sql注入
        $err = ''; //错误标记
		$userData = Db::query('SELECT * FROM atccsuser WHERE username=?',[$username]);
		
		if (!$userData) //用户名不存在
        {
            $err .= '用户不存在!';
        }

        if( $userData && md5($password) != $userData[0]['password'] ) //判断密码
        {
            $err .= '密码错误!';
        }

        if ($err !== '') $this->error('用户名或密码错误!');
        
        if(1 != $userData[0]['status'])//判断用户状态
        {
            $this->error('此用户已被禁用!');
        }

        //登录成功，写入session, 跳转至主页面
        Session::set('login', $userData[0]['username']);
        Session::set('role', $userData[0]['role']);
        Session::set('userId', $userData[0]['id']);
        //cookie中已有之前的url,则跳转回此url
        
        $this->redirect('/front'); //去首页
    }//进行登陆 结束

    //退出的方法 ////////////////////
    public function logout ()
    {
        //清空session 和cookie
        Session::delete('login');
        Session::delete('role');
        Session::delete('userId');
        Session::delete('sunRise');
        Session::delete('sunSet');
        //Cookie::delete('url');
        Cookie::delete('sequence');
        //返回首页
        return view('login');
    }//退出的方法  结束
}