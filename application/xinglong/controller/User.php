<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;
use think\Cookie;

class User extends Controller
{
	//检测是否登录////////////////////////////////////////////////
    public function _initialize ()
    {
        //未登录
        if (!Cookie::has('login'))
        {
			if (Request::instance()->isAjax())
			{
				return '请完成登录后，再进行相关操作！';
			}
            $request = Request::instance();
            Cookie::set('url', $request->url());
            $this->error('请完成登录后，再进行相关操作！', '/');
        }   
    }
	
	//用户首页/////////////////////////////////////////////////
	public function index ()
	{
		//权限判断
		if (Cookie::get('role') != 1)
		{
			$this->error('您无权查看用户信息!');
		}
		
		if($keyword = trim(input('keyword')))
		{
			
		}else{
			$keyword = '';
		}
		
		$userList =Db::table('user')->where('username', 'like', '%'.$keyword.'%')->order('id desc')->paginate(8, false, ['query' => ['keyword' => $keyword]]);
		$this->assign('userList', $userList);
		return view('userIndex');
	}
	
	//显示 添加用户页面///////////////////////////////////////
	public function add ()
	{
		return view('add');
	}
	
	//添加用户////////////////////////////////////////////////////
	public function doadd ()
	{
		//权限和是否登录的判断
		if (!Session::has('login'))
		{
			return '为确保操作者为同一人，请再次登录！';
		}
	
		if (Cookie::get('role') != 1)
		{
			 return '您无权添加用户';
		}
		
		$inputData = input(); //获取表单数据
		$username = trim($inputData['username']);
		$passwd = trim($inputData['passwd']);
		$rePasswd = trim($inputData['rePasswd']);
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
		$userData = Db::query('SELECT * FROM user WHERE username=?',[$username]);
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
		$user = Db::table('user')->insert($data);
		if($user)
		{
			return '添加用户成功!';
		}else{
			return '添加用户失败!';
		}
	}
	
	//编辑用户 页面///////////////////////////////////////////////////
	public function edit ()
	{
		//权限和是否登录的判断
		if (!Session::has('login'))
		{
			Cookie::set('url', $this->request->url());
			$this->error('为确保操作者为同一人，请再次登录！', '/');
		}
	
		if (Cookie::get('role') != 1)
		{
			 $this->error('您无权编辑用户！', '/');
		}
		
		//halt(input('id'));
		$id = input('id');
		$userData = Db::table('user')->where('id', $id)->find();
		if ($userData)
		{
			$this->assign('userData', $userData);
		}else{
			$this->error('网络异常，请再次点击编辑!');
		}
		
		return view('edit');
	}
	
	//编辑用户 /////////////////////////////////////////////////////
	public function doEdit ()
	{
		//权限和是否登录的判断
		if (!Session::has('login'))
		{
			$this->error('为确保操作者为同一人，请再次登录！', '/');
		}
	
		if (Cookie::get('role') != 1)
		{
			 $this->error('您无权编辑用户！', '/');
		}
		
		$inputData = input(); //获取表单数据
		$id = $inputData['id'];
		$username = trim($inputData['username']);
		$passwd = trim($inputData['passwd']);
		if (!input('?post.role'))
		{
			$role = 1; //若是管理员 修改自己信息 role默认为1
			$des = '管理员';
		}else{
			$role = trim($inputData['role']);
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
		}
		
		//验证数据
		$result	= $this->validate(
            [
                '用户名' =>	$username,
            ],
            [
                '用户名' =>	'require|max:12|min:6|alphaDash',
			],
		    ['用户名'=>['alphaDash'=>'用户名只能数字字母_及-','min'=>'用户名长度最少须6位','max'=>'用户名长度最多12位','require'=>'用户名不能为空'],]);
		
        if(true	!==	$result)    //验证失败 输出错误信息
        { 
            $this->error($result);
        }
		
		//验证密码
		if($passwd) //有提交的密码
		{
			if (!preg_match('/[\w-]{6,10}/', $passwd))
			{
				$this->error('密码须是6-12为数字字母_及-!');
			}else{
				$passwd = md5($passwd);
			}
		}else{//没有密码提交 用原来的密码
			$passwd = Db::table('user')->where('id', $id)->value('password');
			if(!$passwd)
			{
				$this->error('网络异常，请重新提交数据!');
			}
		}
		
		//更新数据
		$res = Db::table('user')->where('id', $id)
			->update(['username'=>$username, 'password'=>$passwd, 'role'=>$role, 'description'=>$des,]);
		if ($res)
		{
			$this->success('更新用户信息成功!', '/xinglong/user');
		}else{
			$this->error('更新用户信息失败!');
		}
	}
	
	//禁用  用户
	public function off ()
	{
		//权限和是否登录的判断
		if (!Session::has('login'))
		{
			$this->error('为确保操作者为同一人，请再次登录！', '/');
		}
	
		if (Cookie::get('role') != 1)
		{
			 $this->error('您无权编辑用户！', '/');
		}
		
		if($id = input('id'))
		{
			$res = Db::table('user')->where('id', $id)
				 ->update(['status' => 2]);
			if($res)
			{
				$this->success('操作成功!');
			}else{
				$this->error('操作失败!请重新执行操作!');
			}
		}else{
			$this->error('网络异常，请重新操作!');
		}
		
	}
	
		//禁用  用户
	public function on ()
	{
		//权限和是否登录的判断
		if (!Session::has('login'))
		{
			$this->error('为确保操作者为同一人，请再次登录！', '/');
		}
	
		if (Cookie::get('role') != 1)
		{
			 $this->error('您无权编辑用户！', '/');
		}
		
		if($id = input('id'))
		{
			$res = Db::table('user')->where('id', $id)
				 ->update(['status' => 1]);
			if($res)
			{
				$this->success('操作成功!');
			}else{
				$this->error('操作失败!请重新执行操作!');
			}
		}else{
			$this->error('网络异常，请重新操作!');
		}
	}
	
	//显示修改密码页面/////////////////////////////////////////
	public function passwd ()
	{
		return view('editPasswd');
	}
	
	//修改密码//////////////////////////////////////////////////////
	public function editPaswd ()
	{
		if(!Cookie::has('login'))
		{
			$this->error('请登录后再进行此操作!', '/');
		}
		//halt(input());
		$inputData = input(); //获取表单数据
		$username = $inputData['username'];
		$passwd = trim($inputData['passwd']);
		$passwdNew = trim($inputData['passwdNew']);
		$RepasswdNew = trim($inputData['RepasswdNew']);
		//验证数据
		$result	= $this->validate(
            [
                '原密码' =>	$passwd,
                '新密码' => $passwdNew,
                '确认密码' => $RepasswdNew ,
            ],
            
            [
                '原密码' =>	'require|max:12|min:6|alphaDash',
                '新密码' => 'require|min:6|max:12|alphaDash',
                '确认密码' => 'require|confirm:新密码',
        ],
		['原密码'=>['alphaDash'=>'原密码只能数字字母_及-','min'=>'原密码长度最少须6位','max'=>'原密码长度最多12位','require'=>'原密码不能为空'],
		 '新密码'=>['alphaDash'=>'新密码只能数字字母_及-','min'=>'新密码长度最少须6位','max'=>'原密码长度最多12位','require'=>'新密码不能为空'],
		 '确认密码'=>['require'=>'确认密码不能为空','confirm'=>'两次密码不一致'],
		]);
		
        if(true	!==	$result)    //验证失败 输出错误信息
        { 
            $this->error($result);
        }
		
		$user = Db::table('user');
		//根据cookie中的用户名 查原来密码
		$oldPaswd = Db::table('user')->where('username', $username)->value('password');
	
		if($oldPaswd)
		{
			if(md5($passwd) !== $oldPaswd)
			{
				$this->error('您输入的原密码错误!');
			}else{
				$res = $user->where('username', $username)
				     ->update(['password'=>md5($passwdNew)]);
				if ($res)
				{
					$this->success('密码更改成功!', '/xinglong/control/front');
				}else{
					$this->error('密码更改失败!请重新更改。');
				}
			}			
			
		}else{
			$this->error('网络异常，请重新提交!');
		}
	}
	
}
