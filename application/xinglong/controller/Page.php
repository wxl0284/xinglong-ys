<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cache;
//use think\Session;
//use think\Db;

class Page extends Base
{
    //显示首页
    public function front ()
    {
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
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, 'http://www.nmc.cn/publish/forecast/AHE/xinglong.html');
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); //返回字符串数据
			curl_setopt($ch, CURLOPT_FAILONERROR, 1); //出错时停止
			$weathStr = curl_exec($ch); //将远程数据存入变量

			if( curl_errno($ch) )
			{
				$weatherError = '网络异常，暂未获取天气预报,检查您的网络设置!';
			}
			curl_close($ch);
			
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
            }
            
        }//天气预报获取ok////////////////////////////////////////
		
        //白天 天气数据模板赋值
        if (isset($day))
        {
           $vars = [
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
            ];
        }
        
        //夜晚天气数据模板赋值
        if (isset($night))
        {
            $vars = [
                'night' => $night,                
                'nightPic' => $nightPic,                
                'weatherNight' => $weatherNight,                
                'tmpNight' => $tmpNight,
                'windNight' => $windNight,
                'windPowerNight' => $windPowerNight,               
            ];
        }
        //云图错误
        if (isset($wxytError))
        {
           $vars['wxytError'] = $wxytError;
        }
        
        //天气获取错误
        if (isset($weatherError))
        {
           $vars['weatherError'] = $weatherError;
        }
      		
        return view('front', $vars);
    }//显示首页 结束
}