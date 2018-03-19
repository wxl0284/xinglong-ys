<?php
namespace app\xinglong\controller;

use think\Controller;
use app\xinglong\model\User;
use think\Db;
use think\Cache;
use think\Request;
use app\xinglong\model\At60config;

class Test extends Controller
{
	protected $err = 0;
	protected function _initialize ()
	{
		// echo $this->request->controller();
		// echo $this->request->action();
		//$this->success('新增成功', 'At60/index');
		//$this->redirect('At80/index');
		// $aa = $this->request->isAjax();
		// halt($aa);
		// $input = input();
		// if ($input['a'] == 2)
		// {
		// 	if($this->request->isAjax())
		// 	{
		// 		//echo '您无权限';exit();
		// 		if ($input['b'] == 2)
		// 		{
		// 			$this->err = 1;
		// 		}
		// 		//$this->err = 1;
				
		// 	}else{
		// 		$this->redirect('At80/index');
		// 	}
		// 	//$this->error('无权限');
		// }
	}

	public function index ($a)
	{
		/*  $mjd = GetJD(); //获取修正儒略日
		
		$sunRise = 0; $sunSet = 0;
		
		sunTwilight($sunRise, $sunSet, $mjd, 8);
		
		echo $sunRise. '|' .$sunSet;
		//echo config('latitude'); 
		//return data2Time ('-11.386761111111');
		//return time2Data ('-11:23:12.34'); */
		//$userData = User::where('id','>',10)->column('username','password');
		//print_r($userData);
		// $cacheStr['a'] = "aaa";
		// $cacheStr['b'] = "bbb";
		// $cacheStr['data'] = 1;
		// Cache::set('name', $cacheStr);
		//return url('a/b/c');
		//return url('aa/bb/cc/vv/mm', 'v1=gg&v2=mm', true, true);
		//$vars = ['mmm'=>5888];
		// $vars['n'] = ['j' => 'kk', 'jj'=>null];
		// return view('table/a', $vars);
		file_put_contents('2.txt',$a);
		$a = file_get_contents('2.txt');
		dump($a);
		//var_dump($request->routeInfo());
	}
	
	public function index1 ($a='mm')
	{
		//dump($_SERVER["DOCUMENT_ROOT"] . $_SERVER["REQUEST_URI"]);
		if ($this->err == 1)
		{
			return '无权限';
			//$this->error('无权限');
		}
		//return json(['aa'=>22, 'bb'=>33]);
		return json('b:3');
	}
	
	public function test ()
	{
		/* $a['aa'] = 'aaaa';
		$a['bb'] = 'bbbb';
		Cache::set('name', $a);
		return view('table/a'); */
		$At60config = new At60config;
		$configData = $At60config->all();
		//halt($configData[0]);
		if (empty($configData))
		{
			return '请先配置60CM望远镜!';
		}else{
			$At60config->save([
				'attype' => '2',
				'filtercanfindhome' => '6'
			],['pk_at60config' => $configData[0]['pk_at60config']]);
		}
	}
	
	public function valid ()
	{
		$data = Cache::get('name');
		halt('cc');
	/* 	$result	= $this->validate(
            [
                '用户名' =>	$a['n'],
            ],
            
            [
                '用户名' =>	'alphaDash',
        ]); */
        if (!preg_match('/^[0-2]$/', $a['n']))
        { 
            return 'err';
        }else{
			return 'ok';
		}
	}
}