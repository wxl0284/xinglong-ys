<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Db;
use think\Session;
use think\Request;
use think\Cache;
use think\Cookie;

class Control extends Controller
{
    //显示登陆页面////////////////////////////////////////////////
    public function index()
    {
       return view('login');
    }
    
    //登录的方法/////////////////////////////////////////////////////////
    public function dologin ()
    {
        $username = trim(input('username'));
        $password = trim(input('password'));
        
        //验证数据 validate方法
        $result	= $this->validate(
            [
                '用户名' =>	$username,
                '__token__'	=> input('__token__'),
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
		$userData = Db::query('SELECT * FROM user WHERE username=?',[$username]);
		
		if (!$userData) //用户名不存在
        {
            $this->error('用户名或密码错误!');
        }

        //判断密码
        if(md5($password) != $userData[0]['password'])
        {
            $this->error('用户名或密码错误!');
        }
		
		//判断用户状态
        if(1 != $userData[0]['status'])
        {
            $this->error('此用户已被禁用!');
        }

        //登录成功，写入Cookie, 跳转至主页面
        Cookie::set('login', $userData[0]['username']);
        Session::set('login', $userData[0]['username']);
        Cookie::set('role', $userData[0]['role']);
        //session中已有之前的url,则跳转回此url
        if (Cookie::has('url'))
        {
            $this->redirect(Cookie::get('url'));
        }else{
            $this->redirect('/xinglong/control/front'); //去首页
        }
        
    }
    
    //显示首页//////////////////////////////////////////////////
    public function front ()
    {
		//未登录
        if (!Cookie::has('login'))
        {
            $request = Request::instance();
            Cookie::set('url', $request->url());
            $this->error('请完成登录后，再进行相关操作！', '/');
        }
        
        //获取天气预报数据////////////////////////////////////////
        if ($weathStr = Cache::get('weather'))
        {
            //获取当天预报数据的html,$match[0]
            preg_match('/今天[\s\S]*今天/', $weathStr, $match);
            
            if (strpos($match[0],'colspan="2"')) //是晚上
            {
                $night = 1;
                preg_match_all('/> ?[^a-zA-Z\s]{2,} ?</', $match[0], $matchs);
                //获取天气图标
                preg_match('/http:[\S]*white[\S]{5,15}png/', $match[0],$matchImg);
                
                //晚上的天气图标
                $nightPic = $matchImg[0];        
                //天气状况
                $weatherNight = str_replace(['<','>'], '', $matchs[0][2]);
                //温度
                $tmpNight = str_replace(['<','>'], '', $matchs[0][3]);
                //风向
                $windNight = str_replace(['<','>'], '', $matchs[0][4]);
                //风力
                $windPowerNight = str_replace(['<','>'], '', $matchs[0][5]);
                
            }else{//白天
                $day = 1;
                preg_match_all('/> ?[^a-zA-Z\s]{2,} ?</', $match[0], $matchs);
                //获取天气图标
                preg_match_all('/http:[\S]*white[\S]{5,15}png/', $match[0],$matchImg);
               
                $dayPic = $matchImg[0][0];      //白天的气象图标
                $nightPic = $matchImg[0][1];    //晚上的气象图标
                
                //天气状况
                $weatherDay = str_replace(['<','>'], '', $matchs[0][2]);
                $weatherNight = str_replace(['<','>'], '', $matchs[0][3]);
                //温度
                $tmpDay = str_replace(['<','>'], '', $matchs[0][4]);
                $tmpNight = str_replace(['<','>'], '', $matchs[0][5]);
                //风向
                $windDay = str_replace(['<','>'], '', $matchs[0][6]);
                $windNight = str_replace(['<','>'], '', $matchs[0][7]);
                //风力
                $windPowerDay = str_replace(['<','>'], '', $matchs[0][8]);
                $windPowerNight = str_replace(['<','>'], '', $matchs[0][9]);
            }
        }else{//缓存获取失败，从网络抓取
            $weathStr = file_get_contents('http://www.nmc.cn/publish/forecast/AHE/xinglong.html');
        
            if ($weathStr)
            {//获取成功，写入缓存
                Cache::set('weather', $weathStr, 3600);
				
				//获取当天预报数据的html,$match[0]
				preg_match('/今天[\s\S]*今天/', $weathStr, $match);
				
				if (strpos($match[0],'colspan="2"')) //是晚上
				{
					$night = 1;
					preg_match_all('/> ?[^a-zA-Z\s]{2,} ?</', $match[0], $matchs);
					//获取天气图标
					preg_match('/http:[\S]*white[\S]{5,15}png/', $match[0],$matchImg);
					
					//晚上的天气图标
					$nightPic = $matchImg[0];
					//天气状况
					$weatherNight = str_replace(['<','>'], '', $matchs[0][2]);
					//温度
					$tmpNight = str_replace(['<','>'], '', $matchs[0][3]);
					//风向
					$windNight = str_replace(['<','>'], '', $matchs[0][4]);
					//风力
					$windPowerNight = str_replace(['<','>'], '', $matchs[0][5]);
					
				}else{//白天
					$day = 1;
					preg_match_all('/> ?[^a-zA-Z\s]{2,} ?</', $match[0], $matchs);
					//获取天气图标
					preg_match_all('/http:[\S]*white[\S]{5,15}png/', $match[0],$matchImg);
				   
					$dayPic = $matchImg[0][0];      //白天的气象图标
					$nightPic = $matchImg[0][1];    //晚上的气象图标
					//天气状况
					$weatherDay = str_replace(['<','>'], '', $matchs[0][2]);
					$weatherNight = str_replace(['<','>'], '', $matchs[0][3]);
					//温度
					$tmpDay = str_replace(['<','>'], '', $matchs[0][4]);
					$tmpNight = str_replace(['<','>'], '', $matchs[0][5]);
					//风向
					$windDay = str_replace(['<','>'], '', $matchs[0][6]);
					$windNight = str_replace(['<','>'], '', $matchs[0][7]);
					//风力
					$windPowerDay = str_replace(['<','>'], '', $matchs[0][8]);
					$windPowerNight = str_replace(['<','>'], '', $matchs[0][9]);
				}
            }else{//从网络抓取 失败
                $weatherError = '网络异常，暂未获取天气预报!';
            }
            
        }//天气预报获取ok////////////////////////////////////////
		
        //白天 天气数据模板赋值
        if (isset($day))
        {
            $this->assign([
                'day' => $day,
                'dayPic' => $dayPic,            
                'nightPic' => $nightPic,         
                'weatherDay' => $weatherDay,        
                'weatherNight' => $weatherNight,      
                'tmpDay' => $tmpDay,   
                'tmpNight' => $tmpNight,  
                'windDay' => $windDay, 
                'windNight' => $windNight,
                'windPowerDay' => $windPowerDay,
                'windPowerNight' => $windPowerNight,
            ]);
        }
        
        //夜晚天气数据模板赋值
        if (isset($night))
        {
            $this->assign([
                'night' => $night,                
                'nightPic' => $nightPic,                
                'weatherNight' => $weatherNight,                
                'tmpNight' => $tmpNight,
                'windNight' => $windNight,
                'windPowerNight' => $windPowerNight,               
            ]);
        }
        //云图错误
        if (isset($wxytError))
        {
           $this->assign('wxytError', $wxytError);
        }
        
        //天气获取错误
        if (isset($weatherError))
        {
           $this->assign('weatherError', $weatherError);
        }
        //卫星云图赋值
        if (isset($cloudPic))
        {
           $this->assign('cloudPic', $cloudPic); 
        }
		
        return view('front');
    }
	
	//更多气象信息 页面 ////////////////////////////////////////////
    public function weatherMore ()
    {
		//未登录////////////////////////////////////////////////
        if (!Cookie::has('login'))
        {
            $request = Request::instance();
            Cookie::set('url', $request->url());
            $this->error('请完成登录后，再进行相关操作！', '/');
        }
		
		//获取卫星云图图片///////////////////////////////////////////// 
        if ($wxyt = Cache::get('wxyt'))    //缓存有效
        {
            preg_match('/<img id="imgpath"([\s\S]){50,260}不存在！\'">/', $wxyt, $match);
            preg_match('/src="http:\/\/([\s\S]){50,200}\d+"/', $match[0], $match1);
            $cloudPic = $match1[0];
        }else{//从网络抓取数据
            $a = file_get_contents('http://www.nmc.cn/publish/satellite/fy2.htm');
            //写入缓存
            if ($a)
            {//抓取成功
                Cache::set('wxyt', $a, 3600);
				
				$wxyt = Cache::get('wxyt');
				preg_match('/<img id="imgpath"([\s\S]){50,260}不存在！\'">/', $wxyt, $match);
				preg_match('/src="http:\/\/([\s\S]){50,200}\d+"/', $match[0], $match1);
				$cloudPic = $match1[0];
            }else{//抓取失败
                $wxytError = '网络异常，暂未获取卫星云图!';
				$cloudPic  = null;
            }
              
        } //气象云图获取ok//////////////////////////////////////////
		
		//获取其他数据信息///////////////////////////////////////////
		
		//云图错误
        if (isset($wxytError))
        {
           $this->assign('wxytError', $wxytError);
        }
        
        //卫星云图赋值
        if (isset($cloudPic))
        {
           $this->assign('cloudPic', $cloudPic); 
        }

		return view('weather');
    }
    
    //退出  ////////////////////////////////////////////////
    public function logout ()
    {
        //清空session 和cookie
        Session::clear();
        Cookie::clear();
        //返回首页
         return view('login');
    }

}
