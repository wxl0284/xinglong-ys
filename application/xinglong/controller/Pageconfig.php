<?php
public function doAt60config () /*此为写入配置的代码*/
	{
		$option = input();	//获取配置信息
		$atNo = $option['atNo'] ; //获取配置的望远镜序号
		//halt($option['gimbal']);
		if ($atNo=== '0')
		{
			return '请选择您要配置的望远镜';
		}
		//halt($option);
		
		$fileName = $atNo.'conf'.'.txt';  //60的配置写入60conf.txt文件
		if (file_exists($fileName))
		{//已配置过，则更新配置
			$jsonData = file_get_contents($fileName);
			$cofigArr = json_decode($jsonData, true); //旧配置
			$option = array_merge($cofigArr, $option);	//将提交的配置与旧配置合并
		}
		$option = json_encode($option);	//将配置信息数组转为json字串
		$res = file_put_contents ($fileName, $option);
		if ($res > 1)
		{//写入文件成功
			return $atNo . '望远镜配置ok!';
		}
		//将json数据存入文件
    }
    


    /*如下为读取配置的代码*/
    //显示60cm望远控制镜页面////////////////////////////////////////
    public function index ()
    {
		//判断60cm望远镜是否已配置，否则先配置并存入缓存
		/* $at60Page = Cache::get('at60Page');
		
		if(!isset($at60Page['data']))
		{
			$this->redirect('/xinglong/page_config/at60config');
		}else{
			$this->assign([
                'cacheStr' => $at60Page,
            ]);
		} */

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
		//读取60cm望远镜配置 60conf.txt要用变量组成
		if (!file_exists('60conf.txt') || !file_get_contents('60conf.txt'))
		{
			$this->error('读取60页面配置数据失败!', 'xinglong/page_config/at60config'); //后期将60conf动态获取
		}
		//获取配置数据成功
		$res = file_get_contents('60conf.txt'); //后期将60conf动态获取
		$confData = json_decode($res, true);
		//halt($confData['focuscanfindhome']);
		$vars['configData'] = $confData;
		return view('at60-m', $vars);  		
    }