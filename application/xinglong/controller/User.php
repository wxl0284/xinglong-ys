<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Db;
use think\Session;

//用户管理的控制器
class User extends Base
{
    //用户管理首页
	public function index ()
	{
		if($keyword = input('keyword'))
		{
			
		}else{
			$keyword = '';
		}
		
		$userList =Db::table('atccsuser')->where('username', 'like', '%'.$keyword.'%')->order('id desc')->paginate(8, false, ['query' => ['keyword' => $keyword]]);
		$vars['userList'] = $userList;
		return view('userIndex', $vars);
    }//用户管理首页 结束
    
    //显示 添加用户页面
	public function add ()
	{
		return view('add');
    }//显示 添加用户页面 结束
    
    //添加用户////////////////////////////////////////////////////
	public function doadd ()
	{
		//权限的判断
		if (Session::get('role') != 1)
		{
			 return '您无权添加用户';
		}
		
		$inputData = input(); //获取表单数据
		$username = $inputData['username'];
		$passwd = $inputData['passwd'];
		$rePasswd = $inputData['rePasswd'];
		$role = $inputData['role'];
		//验证数据
		$result	= $this->validate(
            [
                '用户名' =>	$username,
                '密码' => $passwd,
                '确认密码' => $rePasswd ,],
            [
                //'用户名' =>	'require|max:12|min:6|regex:/^\w{5,10}$/',
                '用户名' =>	'require|max:12|min:6|alphaDash',
                '密码' => 'require|min:6|max:12|alphaDash',
                '确认密码' => 'require|confirm:密码',],
			['确认密码'=>['confirm'=>'两次密码不一致','require'=>'确认密码不能为空'],
			 '用户名'=>['alphaDash'=>'密码只能是数字字母_及-','min'=>'用户名长度最少须6位','max'=>'用户名长度最多12位','require'=>'用户名不能为空'],
			 '密码'=>['min'=>'密码长度最少须6位','max'=>'密码长度最多12位','require'=>'密码不能为空','alphaDash'=>'密码只能是数字字母_及-'],]);
		
        if(true	!==	$result)    //验证失败 输出错误信息
        { 
            return $result;
        }
		
		//添加用户
		$userData = Db::query('SELECT * FROM atccsuser WHERE username=?',[$username]);
		if ($userData) 
		{
			return '此用户名已存在，请重新填写用户名!';
		}
		
		//新增用户数据
		if ($role == 2)
		{
			$des = '普通用户';
		}elseif ($role == 3)
		{
			$des = '操作员';
		}elseif ($role ==4)
		{
			$des = '科学家';
		}
		$data = ['username' => $username, 'password' => md5($passwd), 'role' => $role,
				 'status' => 1, 'description' => $des];
		$user = Db::table('atccsuser')->insert($data);
		if($user)
		{
			return '添加用户成功!';
		}else{
			return '添加用户失败!';
		}
    }//添加用户  结束/////////////////////
    
    //显示编辑用户页面 ////////////////////////////
	public function edit ($id)
	{
		//权限的判断	
		if (Session::get('role') != 1)
		{
			 $this->error('您无权编辑用户！', '/');
		}
		
		$userData = Db::table('atccsuser')->where('id', $id)->find();
		if ($userData)
		{
            $vars['userData'] = $userData;
		}else{
			$this->error('网络异常，请再次点击编辑!');
		}
		
		return view('edit',$vars);
	}//显示编辑用户页面 结束
}