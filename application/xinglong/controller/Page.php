<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Cache;
use think\Db;

class Page extends Base
{
    //根据at参数显示不同望远镜页面
    public function at_page($at)
    {
        /*
        *此方法要修改
        */
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

    //显示望远镜配置页面 /////////
    public function at_config()
    {
        //首先获取已添加的望远镜列表, 查字段id和atname
        $atList = Db::table('atlist')->field('id, atname')->select();
        if (!$atList)
        {//还未添加望远镜
            $this->error('请先添加望远镜!');
        }

        $vars['atList'] = $atList;

        return view('config', $vars);  //之前配置页面的模板文件是page/config-0.html
    }//望远镜配置页面 结束

    //ajax请求 判断19个动态增减的固定是否已添加够 并获取相应望远镜的配置数据/////////
    public function config()
    {
        //首先判断是否有权限执行
       /* if ($this->ajaxAuthErr == 1)
        {//无权执行
            return '您无权限执行此操作!';
        }*/

        /*如下开始
        *判断数据表'confoption'内19个动态增减的固定属性是否已全部添加，
         否则无法进行各望远镜的配置
        */
        $confOption = Db::table('confoption')->group('conf')->field('conf')->select();
        $confNum = count($confOption);  //动态增减的固定属性的数量
        
        $errMsg = '';  //错误提示
        if ( $confNum < 19 )    //不够19个时
        {//逐一判断缺少了哪个固定属性
            if ( !in_array(['conf' => 'focustype'], $confOption) )
            {//缺少了'焦点类型'
                $errMsg .= '固定属性还须添加：焦点类型!<br>';
            }

            if ( !in_array(['conf' => 'focusratio'], $confOption) )
            {//缺少了'焦距'
                $errMsg .= '固定属性还须添加：焦比!<br>';
            }
            if ( !in_array(['conf' => 'imageBits'], $confOption) )
            {//缺少了'图像位数'
                $errMsg .= '固定属性还须添加：图像位数!<br>';
            }
            if ( !in_array(['conf' => 'coolerMode'], $confOption) )
            {//缺少了'制冷方式'
                $errMsg .= '固定属性还须添加：制冷方式!<br>';
            }
            if ( !in_array(['conf' => 'readoutMode'], $confOption) )
            {//缺少了'读出模式'
                $errMsg .= '固定属性还须添加：读出模式!<br>';
            }
            if ( !in_array(['conf' => 'readoutSpeed'], $confOption) )
            {//缺少了'读出速度模式'
                $errMsg .= '固定属性还须添加：读出速度模式!<br>';
            }
            if ( !in_array(['conf' => 'transferSpeed'], $confOption) )
            {//缺少了'转移速度模式'
                $errMsg .= '固定属性还须添加：转移速度模式!<br>';
            }
            if ( !in_array(['conf' => 'gainmode'], $confOption) )
            {//缺少了'增益模式'
                $errMsg .= '固定属性还须添加：增益模式!<br>';
            }
            if ( !in_array(['conf' => 'gainNumber'], $confOption) )
            {//缺少了'增益档位'
                $errMsg .= '固定属性还须添加：增益档位!<br>';
            }
            if ( !in_array(['conf' => 'ShutterType'], $confOption) )
            {//缺少了'快门类型'
                $errMsg .= '固定属性还须添加：快门类型!<br>';
            }
            if ( !in_array(['conf' => 'ShutterMode'], $confOption) )
            {//缺少了'快门模式'
                $errMsg .= '固定属性还须添加：快门模式!<br>';
            }
            if ( !in_array(['conf' => 'BinArray'], $confOption) )
            {//缺少了'Bin'
                $errMsg .= '固定属性还须添加：Bin!<br>';
            }
            if ( !in_array(['conf' => 'InterfaceType'], $confOption) )
            {//缺少了'ccd接口类型'
                $errMsg .= '固定属性还须添加：ccd接口类型!<br>';
            }
            if ( !in_array(['conf' => 'ExposeTriggerMode'], $confOption) )
            {//缺少了'曝光触发模式'
                $errMsg .= '固定属性还须添加：曝光触发模式!<br>';
            }
            if ( !in_array(['conf' => 'FilterSystem'], $confOption) )
            {//缺少了'滤光片类型'
                $errMsg .= '固定属性还须添加：滤光片类型!<br>';
            }
            if ( !in_array(['conf' => 'FilterShape'], $confOption) )
            {//缺少了'滤光片形状'
                $errMsg .= '固定属性还须添加：滤光片形状!<br>';
            }
            if ( !in_array(['conf' => 'slaveDomeType'], $confOption) )
            {//缺少了'随动圆顶类型'
                $errMsg .= '固定属性还须添加：随动圆顶类型!<br>';
            }
            if ( !in_array(['conf' => 'openDomeType'], $confOption) )
            {//缺少了'全开圆顶类型'
                $errMsg .= '固定属性还须添加：全开圆顶类型!<br>';
            }
            if ( !in_array(['conf' => 'opticalStructure'], $confOption) )
            {//缺少了'导星镜焦点类型'
                $errMsg .= '固定属性还须添加：导星镜焦点类型!<br>';
            }
        }/*检查判断数据表'confoption'内19个动态增减的固定属性  结束*/
        //$errMsg = '固定属性还须添加<br>'.'固定属性还须添加：全开圆顶类型!<br>';
        if ($errMsg != '')
        {//还须添加固定属性
            return $errMsg;
        }else{//获取相应望远镜的配置数据，以json格式返回
            /*1、获取19个动态增减的固定属性数据*/
            $result['confOption'] = $this->get_19confOption ();
            
            
            /*1、获取19个动态增减的固定属性数据 结束*/
            $id = input('id'); //获取相应望远镜的id 在atlist表中此$id对应id字段,其他表中对应teleid字段
            /*查转台的配置数据*/
            $gimbal_data = Db::table('atlist')->where('id', $id)->find();

            if ( $gimbal_data )
            {
                $result['gimbal_data'] = $gimbal_data;
            }
            /*查转台的配置数据 结束*/
            //据此id去各自设备的固定属性表中获取数据
            
            //根据$id查询去相应目录中查询上传的说明文件

            //return json数据给前端
            return json_encode ($result);
        }//获取相应望远镜的配置数据，以json格式返回 结束
    }//ajax请求 判断19个动态增减的固定是否已添加够 并获取相应望远镜的配置数据 结束

    //显示望远镜列表 /////////
    public function atlist()
    {
        $res = Db::table('atlist')->select(); //获取望远镜列表中的望远镜数量
        if(!$res)
        {
            $vars['noAt'] = '无望远镜!';
        }else{
            $vars['atList'] = $res;
        }
        return view('atlist', $vars);
    }//显示望远镜列表 结束

    //显示添加望远镜页面 /////////
    public function at_add()
    {
        return view('atadd');
    }//显示添加望远镜页面 结束

    //执行添加望远镜 /////////
    public function at_doadd()
    {
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $postData = input();
        if (!$postData)
        {
            return '提交数据失败!';
        }

        /*验证望远镜添加表单的数据*/
        $errMsg = ''; //存储错误提示信息
        //验证望远镜id
        if ( !$this->check_atId( $postData['atid']) )
        {
            $errMsg .= '望远镜id格式错误!<br>';
        }

        //验证望远镜名
        if ( !$this->check_name( $postData['atname']) )
        {
            $errMsg .=  '望远镜名格式错误!<br>';
        }

        //验证望远镜观测站
        if ( !$this->check_address( $postData['address']) )
        {
            $errMsg .=  '望远镜所属观测站格式错误!<br>';
        }

        //验证望远镜观 经度
        if ( !$this->check_longitude( $postData['longitude']) )
        {
            $errMsg .=  '望远镜经度格式错误!<br>';
        }

        //验证望远镜观 纬度
        if ( !$this->check_latitude( $postData['latitude']) )
        {
            $errMsg .=  '望远镜纬度格式错误!<br>';
        }

        //验证望远镜观 海拔
        if ( !$this->check_altitude( $postData['altitude']) )
        {
            $errMsg .=  '望远镜海拔格式错误!<br>';
        }

        //验证望远镜观 口径
        if ( !$this->check_aperture( $postData['aperture']) )
        {
            $errMsg .= '望远镜口径格式错误!<br>';
        }

        if ($errMsg != '')
        {
            return $errMsg;
        }/*验证望远镜添加表单的数据 结束*/

        //查询新提交的望远镜id或望远镜名 是否在数据表中唯一
        $old = Db::table('atlist')->where('atid', $postData['atid'])->whereOr('atname', $postData['atname'])->find();
        
        if ($old)
        {
            return '望远镜Id或望远镜名重复,请重新填写!';
        }
        //执行数据添加
        $res = Db::table('atlist')->insert($postData);
        if ($res)
        {
            return '添加望远镜ok!';
        }else{
            return '添加望远镜失败!';
        }

    }//执行添加望远镜 结束

    //显示要编辑的望远镜数据  /////////
    public function at_edit($at)
    {   
        //查对应望远镜数据
        $res = Db::table('atlist')->where('id', $at)->find();
        if (!$res)
        {
           $this->error('读取数据失败!');
        }else{
            $vars['atData'] = $res;
            return view ('atedit', $vars);
        }

    }//显示要编辑的望远镜数据 结束

    //执行编辑望远镜数据  /////////
    public function at_doedit()
    {  
        //判断ajax 请求时 是否有权限
        // if ($this->ajaxAuthErr == 1)
        // {
        //     return '您无权执行此操作!';
        // }

        $postData = input();
        if (!$postData)
        {
            return '提交数据失败!';
        }

        /*验证望远镜添加表单的数据*/
        $errMsg = ''; //存储错误提示信息
        //验证望远镜id
        if ( !$this->check_atId( $postData['atid']) )
        {
            $errMsg .= '望远镜id格式错误!<br>';
        }

        //验证望远镜名
        if ( !$this->check_name( $postData['atname']) )
        {
            $errMsg .=  '望远镜名格式错误!<br>';
        }

        //验证望远镜观测站
        if ( !$this->check_address( $postData['address']) )
        {
            $errMsg .=  '望远镜所属观测站格式错误!<br>';
        }

        //验证望远镜观 经度
        if ( !$this->check_longitude( $postData['longitude']) )
        {
            $errMsg .=  '望远镜经度格式错误!<br>';
        }

        //验证望远镜观 纬度
        if ( !$this->check_latitude( $postData['latitude']) )
        {
            $errMsg .=  '望远镜纬度格式错误!<br>';
        }

        //验证望远镜观 海拔
        if ( !$this->check_altitude( $postData['altitude']) )
        {
            $errMsg .=  '望远镜海拔格式错误!<br>';
        }

        //验证望远镜观 口径
        if ( !$this->check_aperture( $postData['aperture']) )
        {
            $errMsg .= '望远镜口径格式错误!<br>';
        }

        if ($errMsg != '')
        {
            return $errMsg;
        }/*验证望远镜添加表单的数据 结束*/

        //执行更新
        $res = Db::table('atlist')->where('id', $postData['id'])->update($postData);
        if ($res)
        {
            return '编辑望远镜信息ok!';
        }else{
            return '编辑望远镜信息失败!';
        }

    }//执行编辑望远镜数据 结束

    //删除望远镜 
    public function at_delete ($atid)
    {
        //首先删除望远镜列表中相应数据
        $res = Db::table('atlist')->where('atid', $atid)->delete();
        if ($res)
        {
            $this->success ('删除成功!', '/atlist');
        }else{
            $this->error ('删除失败!');
        }
        //然后开始事务 去删除此atid关联的此望远镜其他数据
        /*
        Db::startTrans();
        
        //删除其他表相关数据
        if ($res && 其他表删除ok)
        {
            Db::commit();
        }else{
            Db::rollback();
        }
        */

    }//删除望远镜  结束

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

        //读取望远镜列表 将已添加的望远镜显示在下拉框中
        $atList = Db::table('atlist')->order('id', 'asc')->field('id, atname')->select();
        if ($atList)
        {
            $vars['atList'] = $atList;
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

    /*验证望远镜id格式*/
    protected function check_atId ($atId)
    {
        //合法格式：02000
        if ( strlen($atId) != 5 || !is_numeric($atId) || strpos($atId, '0') != 0 )
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜id格式 结束*/

    /*验证望远镜名格式*/
    protected function check_name ($name)
    {
        //合法格式：0.6m望远镜
        if ( !preg_match('/^\d(\d|\.)*m望远镜+$/', $name) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名格式 结束*/

    /*验证望远镜名格式*/
    protected function check_address ($address)
    {
        //合法格式：字符长度不低于3
        if ( strlen($address) < 3 )
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名格式 结束*/

    /*验证望远镜 经度*/
    protected function check_longitude ($longitude)
    {
        //合法格式：-180 ~ 180
        if ( !is_numeric($longitude) || $longitude > 180 || $longitude < -180)
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名 经度 结束*/

    /*验证望远镜 纬度*/
    protected function check_latitude ($latitude)
    {
        //合法格式：-90 ~ 90
        if ( !is_numeric($latitude) || $latitude > 90 || $latitude < -90)
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名 纬度 结束*/

    /*验证望远镜 海拔*/
    protected function check_altitude ($altitude)
    {
        //合法格式：-1000 ~ 1000
        if ( !is_numeric($altitude) || $altitude > 6000 || $altitude < -1000)
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名 海拔 结束*/

    /*验证望远镜 口径*/
    protected function check_aperture ($aperture)
    {
        //合法格式：216.0
        if ( !is_numeric($aperture) )
        {
            return false;
        }else{
            return true;
        }
    }/*验证望远镜名 口径 结束*/

    /*获取19个动态增减的固定属性 数据*/
    protected function get_19confOption ()
    {
        /*获取所有配置选项 */
        $confOption = Db::table('confoption')->field('conf, conf_val')->select();
        /*遍历配置选项数据 组装为：配置项=>[配置数据]*/
        foreach ($confOption as $v)
        {
            $res[$v['conf']][]= $v['conf_val'];
        }
        return $res;
    }/*获取19个动态增减的固定属性 数据 结束*/
}