<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cache;
use think\Session;
//use think\Db;

class Page extends Base
{
    //根据at参数显示不同望远镜页面
    public function at_page($at)
    {
        $confFile = $at . 'conf.txt'; //文件位60conf.txt

        if (!file_exists($confFile) || !file_get_contents($confFile))
        {
            $this->error('请先配置相应望远镜!', '/atconfig');
        }else{
            //计算晨光始、昏影终
            $mjd = GetJD();  //修正儒略日
            
            $sunRise = 0; //晨光始
            $sunSet = 0; //昏影终
            
            sunTwilight ($sunRise, $sunSet, $mjd, 8);
            //halt(data2Time ($sunRise));
            $sunRise = substr(data2Time ($sunRise), 1, 8);
            $sunSet = substr(data2Time ($sunSet), 1, 8);

            //将晨光始 昏影终 存入session
            Session::set('sunRise', $sunRise);
            Session::set('sunSet', $sunSet);
            //读取60cm望远镜配置
            $res = file_get_contents($confFile);
            if (!$res)
            {
                $this->error("读取{$at}页面配置数据失败!");
            }
            //获取配置数据成功
            $confData = json_decode($res, true);
            //halt($confData['focuscanfindhome']);
            $vars['configData'] = $confData;
            return view('atpage', $vars); 	
        }
    }//根据at参数显示不同望远镜页面 结束

    //望远镜配置页面 /////////
    public function at_config()
    {
        return view('config');
    }//望远镜配置页面 结束

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

    //显示天气详情页
    public function weather ()
    {		
		//获取卫星云图图片///////////////////////////////////////////// 
        if ($wxyt = Cache::get('wxyt'))    //缓存有效
        {
            preg_match('/<img id="imgpath"([\s\S]){50,260}不存在！\'">/', $wxyt, $match);
            preg_match('/src="http:\/\/([\s\S]){50,200}\d+"/', $match[0], $match1);
            $cloudPic = $match1[0];
        }else{//从网络抓取数据            
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, 'http://www.nmc.cn/publish/satellite/fy2.htm');
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); //返回字符串数据
			curl_setopt($ch, CURLOPT_FAILONERROR, 1); //出错时停止
			$cloudPicStr = curl_exec($ch); //将远程数据存入变量

			if( curl_errno($ch) )
			{
				$wxytError = '网络异常，暂未获取卫星云图,检查您的网络设置!';
				$cloudPic  = null;
			}
			curl_close($ch);
			
            if ($cloudPicStr)
            {//抓取成功
                Cache::set('wxyt', $cloudPicStr, 3600); //写入缓存
				
				$wxyt = Cache::get('wxyt');
				preg_match('/<img id="imgpath"([\s\S]){50,260}不存在！\'">/', $wxyt, $match);
				preg_match('/src="http:\/\/([\s\S]){50,200}\d+"/', $match[0], $match1);
				$cloudPic = $match1[0];
            }
              
        } //气象云图获取ok//////////////////////////////////////////
		
		//云图错误
        if (isset($wxytError))
        {
           $vars['wxytError'] = $wxytError;
        }
        
        //卫星云图赋值
        if (isset($cloudPic))
        {
           $vars['cloudPic'] = $cloudPic;
        }

		return view('weather', $vars);
    }//显示天气详情页 结束///
}