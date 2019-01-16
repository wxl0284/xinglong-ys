<?php
namespace app\xl\controller;

use think\Controller;
use app\xinglong\model\User;
use think\Db;
use think\Cache;
use think\Request;
use app\xinglong\model\At60config;
use \ZipArchive;

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

	public function index (Request $request)
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
		$vars['n'] = ['j' => 'kk', 'jj'=>null];
	    return view('table/a', $vars);
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
		//$arr = ['name' => 'xxxx'];
		//$res = Db::table('ccdconf')->where('teleid', 16)->field('gain_noise')->find();
		//halt($res);
		
		//$temp = $res['gain_noise'];
		
		//$a = data2Time(17.416638888889);
		
		//$dir = ROOT_PATH . 'public' . DS . 'cloudsc/';

		//$res = scandir ( $dir = ROOT_PATH . 'public' . DS . 'cloudsd/' );
		//halt($res);

		/*try{
			$res = scandir ( $dir = ROOT_PATH . 'public' . DS . 'cloudsd/' );
		}catch(\Exception $e){
			$err = 'hhh';
		}*/

		/*如下是测试php-zip扩展 进行多文件压缩
		$zip = new \ZipArchive;

		$res = $zip->open('test.zip', ZipArchive::CREATE);

		if ($res === TRUE)
		{
			$zip->addFile('11.png'); //添加文件
			$zip->addFile('vv.docx'); //继续添加文件
    		$zip->close();
		} else {
			echo 'failed';
		}
		/*如下是测试php-zip扩展 进行多文件压缩 结束*/

		$default_bin_read_gain = Db::table('ccdconf')->where('teleid', 25)->where('ccdno', 1)->field('default_bin, default_readout, default_gain')->find();
		dump($default_bin_read_gain);
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