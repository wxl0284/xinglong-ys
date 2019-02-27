<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Db;
use think\Session;
//用户管理的控制器
class User extends Base
{
	public function index () //用户管理首页
	{
		if( isset($this->input['keyword']) )
		{
			$keyword = $this->input['keyword'];
		}else{
			$keyword = '';
		}
		
		$userList = Db::table('atccsuser')->where('username', 'like', '%'.$keyword.'%')->order('id asc')->paginate(10, false, ['query' => ['keyword' => $keyword]]);
		$user_num = count($userList);
		halt($userList);
		//查询atlist表 获取各口径对应的望远镜名称
		$apertureData = Db::table('atlist')->column('atname', 'aperture'); //以aperture为索引, 如['60cm'=>'60cm望远镜', '80cm'=>'80cm望远镜',]
		
		if ( $user_num > 0 )
		{
			if ( $apertureData )
			{//已添加过望远镜数据信息
				foreach ( $userList as $k => $v )
				{
					if ( $v['role'] == '1' )//是管理员 可以查看和操作所有望远镜
					{
						//$userList[$k]['look'] = '全部';
						//$userList[$k]['operate'] = '全部';
					}else{//普通用户
						foreach ( $apertureData as $kk => $vv ) //$kk表示口径值
						{
							//先处理此用户可查看的望远镜
							$temp = '';

							if ( !empty( $vv['look'] ) )
							{
								if ( strpos( $vv['look'], $kk ) !== false ) //atlist中的某口径值在$v['look']
								{
									$temp .= $vv . ',';
								} 
							}else{
								$temp = '无';
							}

							$vv['look'] = $temp;

							if ( $vv['look'] !== '无' )
							{
								$vv['look'] = rtrim( $vv['look'], ','); //删除最后一个','
							}

							//再处理此用户可操作的望远镜
							$temp = '';

							if ( !empty( $vv['operate'] ) )
							{
								if ( strpos( $vv['operate'], $kk ) !== false ) //atlist中的某口径值在$v['operate']
								{
									$temp .= $vv . ',';
								} 
							}else{
								$temp = '无';
							}

							$vv['operate'] = $temp;

							if ( $vv['operate'] !== '无' )
							{
								$vv['operate'] = rtrim( $vv['operate'], ','); //删除最后一个','
							}
						}//foreach 口径数据结束
					}
				}//foreach 用户 结束
			}else{
				$v['look'] = '暂无望远镜数据';
				$v['operate'] = '暂无望远镜数据';
			}
		}
		//查询atlist表 获取各口径对应的望远镜名称结束
		//halt($userList);
		if ( $user_num > 0 )
		{
			$vars['userList'] = $userList;
		}else{
			$vars['notice'] = '未查询到用户数据!';
		}
		
		return view('userIndex', $vars);
    }//用户管理首页 结束
    
    //显示 添加用户页面
	public function add ()
	{
		//检查atlist表中是否已有望远镜 若无望远镜 则提示添加望远镜
		$at =Db::table('atlist')->field('aperture, atname')->select();
		
		if( !$at )
		{
			$this->error('请您先添加望远镜，再添加用户!');
		}
		$vars['at'] = $at; 
		return view('add', $vars); //前端赋值 并显示页面
    }//显示 添加用户页面 结束
    
    //ajax 添加用户////////////////////
	public function doadd ()
	{
		//首先判断是否已登录
		if ($this->ajaxAuthErr == 'not_log')
		{
			return '请先登录再进行操作!';
		}

		//权限的判断
		if (Session::get('role') != 1)
		{
			return '您无权添加用户';
		}
		
		$inputData = $this->input; //接收提交数据
		//halt($inputData);die();

		$errMsg = ''; //记录验证错误提示
		
		if ( !isset($inputData['username']) || !preg_match('/^[\w-]{6,12}$/', $inputData['username']) )
		{//验证用户名
			$errMsg .= '用户名须为6-12位字母数字_或-!<br>';
		}
		//return $errMsg; die();
		if ( !isset($inputData['passwd']) || !preg_match('/^[\w-]{6,12}$/', $inputData['passwd']) )
		{//验证密码
			$errMsg .= '密码须为6-12位字母数字_或-!<br>';
		}

		if ( !isset($inputData['rePasswd']) || $inputData['rePasswd'] !== $inputData['passwd'] )
		{//验证确认密码
			$errMsg .= '密码两次输入不一致!<br>';
		}

		$apertures = ['50cm', '60cm', '80cm', '85cm', '100cm', '126cm', '216cm']; //全部可选的口径值
		
		if ( !isset($inputData['look']) )
		{//验证可查看的望远镜口径

			$errMsg .= '可查看的望远镜口径数据有误!<br>';
		}elseif( is_array($inputData['look']) )
		{
			foreach ( $inputData['look'] as $v ) 
			{  
				if ( in_array($v, $apertures, true) )
				{
					continue;
				}else{
					$errMsg .= '可查看的望远镜口径数据有误!<br>'; break;
				}
			}
		}

		if( isset($inputData['operate']) && is_array($inputData['operate']) )
		{
			foreach ( $inputData['operate'] as $v ) 
			{  
				if ( in_array($v, $apertures, true) )
				{
					continue;
				}else{
					$errMsg .= '可操作的望远镜口径数据有误!<br>'; break;
				}
			}
		}

		//检查用户名
		$userData = Db::query( 'SELECT * FROM atccsuser WHERE username=?', [ $inputData['username'] ] );
		
		if ($userData) 
		{
			$errMsg .= '用户名已存在，请重新填写用户名!';
		}

		if ( $errMsg !== '' )
		{
			return $errMsg;
		}//验证表单数据结束

		//接下来 将look和operate拼接为字符串
		$inputData['look'] = implode(',', $inputData['look']);

		if( isset($inputData['operate']) && is_array($inputData['operate']) )
		{
			$inputData['operate'] = implode(',', $inputData['operate']);
		}else{
			$inputData['operate'] = '';
		}//将look和operate拼接为字符串 结束

		//$role = $inputData['role'];
		
		//验证数据
		/*$result	= $this->validate(
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
		}*/

		$data = ['username' => $inputData['username'], 'password' => md5($inputData['passwd']), 'role' => '2',
				 'status' => 1, 'description' => '普通用户', 'look' => $inputData['look'], 'operate' => $inputData['operate'] ];

		$user = Db::table('atccsuser')->insert($data);
		if($user)
		{
			return '添加用户成功!';
		}else{
			return '添加用户失败, 请重新提交!';
		}
    }//添加用户  结束/////////////////////
    
    //显示编辑用户页面 ////////////////////////////
	public function edit ($id)
	{
		$userData = Db::table('atccsuser')->where('id', $id)->find();
		if ($userData)
		{
            $vars['userData'] = $userData;
		}else{
			$this->error('网络异常，请再次点击编辑!');
		}
		
		return view('edit',$vars);
    }//显示编辑用户页面 结束
    
    //编辑用户 /////////////////////////////////////////////////////
	public function doEdit ()
	{
		//首先判断是否已登录
		if ($this->ajaxAuthErr == 'not_log')
		{
			return '请先登录再进行操作!';
		}

		$inputData = $this->input; //接收提交数据
		$id = $inputData['id'];
		$username = $inputData['username'];
		$passwd = $inputData['password'];
		$role = $inputData['role'];

		if ($role == 2)
		{
			$des = '普通用户';
		}elseif ($role == 3)
		{
			$des = '操作员';
		}elseif ($role == 4)
		{
			$des = '科学家';
		}elseif ($role == 1)
		{
			$des = '科学家';
		}

		//验证用户名唯一
		$user = Db::table('atccsuser')->where('id', $id)->value('username');
		if(!$user)
		{
			return '网络异常，请重新提交数据!';
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
            return $result;
        }
		
		//验证密码
		if($passwd) //有提交的密码
		{
			if (!preg_match('/[\w-]{6,12}/', $passwd))
			{
				return '密码须是6-12位数字字母_及-!';
			}else{
				$passwd = md5($passwd);
			}
		}else{//没有密码提交 用原来的密码
			$passwd = Db::table('atccsuser')->where('id', $id)->value('password');
			if(!$passwd)
			{
				return '网络异常，请重新提交数据!';
			}
		}
		
		//更新数据
		$res = Db::table('atccsuser')->where('id', $id)
			->update(['username'=>$username, 'password'=>$passwd, 'role'=>$role, 'description'=>$des,]);
		//halt($res);
		if ($res)
		{
			//$this->success('编辑用户成功!', '/xinglong/user');
			return '编辑用户成功!';
		}else{
			//$this->error('编辑用户失败!');
			return '编辑用户失败,请重新操作!';
		}
    }//用户编辑 结束///////////////////
    
    //禁用  用户
	public function off ()
	{	
		if($id = $this->input['id'])
		{
			$res = Db::table('atccsuser')->where('id', $id)
				 ->update(['status' => 2]);
			if($res)
			{
				$this->success('禁用成功!');
			}else{
				$this->error('禁用成功!请重新操作!');
			}
		}else{
			$this->error('网络异常，请重新操作!');
		}
    }//禁用用户  结束/////////////////
    
    //启用  用户////////////////
	public function on ()
	{		
		if($id = $this->input['id'])
		{
			$res = Db::table('atccsuser')->where('id', $id)
				 ->update(['status' => 1]);
			if($res)
			{
				$this->success('启用成功!');
			}else{
				$this->error('启用失败!请重新操作!');
			}
		}else{
			$this->error('网络异常，请重新操作!');
		}
    }//启用  用户 结束////////////
    
    //显示修改密码页面  /////////////////////////////
	public function passwd ()
	{
		return view('editPasswd');
    }
    
    //ajax 执行密码修改  //////////////////
	public function editPasswd ()
	{
		//首先判断是否已登录
		if ($this->ajaxAuthErr == 'not_log')
		{
			return '请先登录再进行操作!';
		}

		$inputData = $this->input; //获取表单数据
		$username = $inputData['username'];
		$passwd = $inputData['passwd'];
		$passwdNew = $inputData['passwdNew'];
		$RepasswdNew = $inputData['RepasswdNew'];
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
            return $result;
        }
		
		$user = Db::table('atccsuser');
		//根据session中的用户名 查原来密码
		$oldPaswd = Db::table('atccsuser')->where('username', $username)->value('password');
	
		if($oldPaswd)
		{
			if(md5($passwd) !== $oldPaswd)
			{
				return '您输入的原密码错误!';
			}else{
				$res = $user->where('username', $username)
				     ->update(['password'=>md5($passwdNew)]);
				if ($res)
				{
					return '密码更改成功!';
				}else{
					return '密码更改失败!请重新更改!';
				}
			}
		}else{
			return '网络异常，请重新提交!';
		}
	}//修改密码 结束
}