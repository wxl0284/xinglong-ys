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
		//halt(input());
		//根据当前页码 显示用户的编号
		if ( isset($this->input['page']) ) //提交参数中有页码
		{
			$cur_page = $this->input['page']; //当前页码
		}else{//无页码
			$cur_page = 1;
		}
		
		$num_per_page = 10; //每页显示的条数

		$userList = Db::table('atccsuser')->where('username', 'like', '%'.$keyword.'%')->order('id asc')->paginate($num_per_page, false, ['query' => ['keyword' => $keyword]]);
		$user_num = count($userList);
		
		//查询atlist表 获取各口径对应的望远镜名称
		$apertureData = Db::table('atlist')->column('atname', 'aperture'); //以aperture为索引, 如['60cm'=>'60cm望远镜', '80cm'=>'80cm望远镜',]
		
		if ( $user_num > 0 )
		{
			$userList->each(function ($item, $key) use ($apertureData, $cur_page, $num_per_page)
			{
				if ( $apertureData ) //有望远镜口径数据
				{
					if ( $item['role'] == '1' )//是管理员 可以查看和操作所有望远镜
					{
						$item['look'] = '全部';
						$item['operate'] = '全部';
					}else{//普通用户

						$temp = '';
						$temp1 = '';

						foreach ( $apertureData as $kk => $vv ) //$kk表示口径值
						{
							//先处理此用户可查看的望远镜
							
							if ( !empty( $item['look'] ) )
							{
								if ( strpos( $item['look'], $kk ) !== false ) //atlist中的某口径值在$v['look']
								{
									$temp .= $vv . ',';
								} 
							}else{
								$temp = '无';
							}
							
							
							
							//再处理此用户可操作的望远镜

							if ( !empty( $item['operate'] ) )
							{
								if ( strpos( $item['operate'], $kk ) !== false ) //atlist中的某口径值在$v['operate']
								{
									$temp1 .= $vv . ',';
								} 
							}else{
								$temp1 = '无';
							}
							
						}//foreach 口径数据结束

						$item['look'] = $temp;

						if ( $item['look'] !== '无' )
						{
							$item['look'] = rtrim( $item['look'], ','); //删除最后一个','
						}

						$item['operate'] = $temp1;

						if ( $item['operate'] !== '无' )
						{
							$item['operate'] = rtrim( $item['operate'], ','); //删除最后一个','
						}

					} // else 普通用户 结束
				}else{//无望远镜口径数据
					$item['look'] = '暂无望远镜数据';
					$item['operate'] = '暂无望远镜数据';
				}

				$item['num'] = $num_per_page * ( $cur_page - 1) + $key + 1;

				return $item;
			});//循环每条记录 结束
		}	
		
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

		//判断是否有权限
		if ($this->ajaxAuthErr == 'no_auth')
		{
			return '您无权进行此操作!';
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
    
    //显示编辑用户页面 ////////////////////
	public function edit ($id)
	{
		$userData = Db::table('atccsuser')->where('id', $id)->find();
		
		//从atlist表中查望远镜口径数据
		$aperture = Db::table('atlist')->column('atname', 'aperture'); //以aperture为索引, 如['60cm'=>'60cm望远镜', '80cm'=>'80cm望远镜',]
		
		if ($userData)
		{
			/*检查此用户 可查看或操作的望远镜口径数据（如果用户可操作60cm和80cm望远镜，而此时atlist表中仅有60cm口径，
			则只在页面显示可操作60cm望远镜，编辑用户数据提交后，用户表中operate字段就没有80cm数据了），若从atlist表中未
			查到望远镜口径数据则提示用户先添加望远镜（可以暂不配置望远镜固定属性）*/
			if ( $aperture )
			{
				/*foreach ($aperture as $k => $v)
				{
					//如果某口径值在$userData的look和operate中，则在页面中显示可以查看和操作的望远镜

				}*/

				//页面赋值
				$vars['userData'] = $userData;
				$vars['aperture'] = $aperture;

				return view('edit',$vars);
			}else{
				$this->error('请先添加望远镜（可暂不配置固定属性），再编辑用户!');
			}

		}else{//未查到此用户数据
			$this->error('网络异常, 未查到该用户数据!');
		}
    }//显示编辑用户页面 结束
    
    //编辑用户 ///////////////////////////////
	public function doedit ()
	{//halt($this->input);die();
		//首先判断是否已登录
		if ($this->ajaxAuthErr == 'not_log')
		{
			return '请先登录再进行操作!';
		}

		//判断是否有权限
		if ($this->ajaxAuthErr == 'no_auth')
		{
			return '您无权进行此操作!';
		}

		$inputData = $this->input; //接收提交数据
		/*$id = $inputData['id'];
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
		}*/

		/*验证数据*/
		$errMsg = ''; //记录错误提示

		if ( !isset($inputData['username']) || !preg_match('/^[\w-]{6,12}$/', $inputData['username']) )
		{//验证用户名
			$errMsg .= '用户名须为6-12位字母数字_或-!<br>';
		}

		if ( !isset($inputData['password']) || ( !empty($inputData['password']) && !preg_match('/^[\w-]{6,12}$/', $inputData['password'])) )
		{//验证密码
			$errMsg .= '密码须为6-12位字母数字_或-!<br>';
		}

		//如果不是管理员 检查该用户能查看的望远镜是否有选择（若无选择，则提示错误）
		if ( !isset($inputData['role']) )
		{//未收到role这个数据
			$errMsg .= '提交数据有误!<br>';
		}else{
			if ( $inputData['role'] == '2' ) //此用户是普通用户
			{//则验证是否有 可查看的望远镜口径值被提交上来
				$apertures = ['50cm', '60cm', '80cm', '85cm', '100cm', '126cm', '216cm']; //全部可选的口径值

				if ( !isset($inputData['look']) )
				{//验证可查看的望远镜口径

					$errMsg .= '未收到可查看的望远镜口径!<br>';
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

				//如果有可操作望远镜口径被提交上来 则验证
				if ( isset($inputData['operate']) && is_array($inputData['operate']) )
				{
					foreach ( $inputData['operate'] as $vv ) 
					{  
						if ( in_array($vv, $apertures, true) )
						{
							continue;
						}else{
							$errMsg .= '可操作的望远镜口径数据有误!<br>'; break;
						}
					}
				}
			}//验证普通用户 结束
		}

		//验证用户名唯一
		if ( !isset($inputData['id']) || !is_string($inputData['id']) )
		{
			$errMsg .= '提交的数据有误!<br>';
		}else{
			//检查此用户名是否重复
			$user = Db::table('atccsuser')->where('id', '<>', $inputData['id'])->column('username');
			//halt($user);die();
			if ( $user && in_array($inputData['username'], $user) )
			{
				$errMsg .= '用户名重复!';
			}
		}

		/*验证数据 结束*/
		if ( $errMsg !== '' )
		{
			return $errMsg;
		}

		//接下来获取并处理数据 进行update
		if ( isset($this->input['look']) ) //可查看的望远镜口径
		{
			$this->input['look'] = implode(',', $this->input['look']);
		}else{
			$this->input['look'] = null;
		}

		if ( isset($this->input['operate']) ) //可操作的望远镜口径
		{
			$this->input['operate'] = implode(',', $this->input['operate']);
		}else{
			$this->input['operate'] = null;
		}

		if ( $this->input['password'] ) //提交了新密码时
		{
			$this->input['password'] = md5($this->input['password']);
		}else{//未提交新密码 则使用原密码
			$this->input['password'] = Db::table('atccsuser')->where('id', $this->input['id'])->value('password');
		}
		
		//更新数据
		$res = Db::table('atccsuser')->where('id', $this->input['id'])
			->update(
				['username' => $this->input['username'],
				'password' => $this->input['password'],
				'role' => $this->input['role'],
				'look' => $this->input['look'],
				'operate' => $this->input['operate'],
				]);
	
		if ($res)
		{
			return '编辑用户成功!';
		}else{
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