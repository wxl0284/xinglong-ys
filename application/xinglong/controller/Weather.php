<?php
namespace app\xinglong\controller;

use app\xinglong\controller\Base;
use think\Db;
use think\Cookie;
use think\Request;
use think\Session;

class Weather extends Base
{	
	//显示更多云量图片////////////////////////////////////////
	public function clouds ()
	{
		return view('clouds');
	}///////////////////////////////////////////////////////////
	
	//显示更多 气象信息////////////////////////////////////////////
	public function weatherDetail ($hour=2)
	{	
		//获取环境数据
		$time_leng = $hour*60; //获取多长时间间隔的数据
		$data = Db::table('wsrealtimedata')->order('id', 'desc')->limit($time_leng)->select();
		//halt($data);
		$num = count ($data);
		$i = $num - 1;
		for ($i; $i >= 0; $i--)
		{
			$time_point[] = date('G:i', $data[$i]['sec']); //分钟 横坐标
			$tempaData[] = $data[$i]['temperature']; //温度
			$windData2[] = $data[$i]['windspeed2']; //2分钟平均风速
			$windData10[] = $data[$i]['windspeed10']; //10分钟平均风速
			$humiData[] = $data[$i]['humidity']; //湿度
			$dewPointData[] = $data[$i]['dewpoint']; //露点温度
		}

		$time_point = json_encode($time_point);
		$tempaData = json_encode($tempaData);
		$windData2 = json_encode($windData2);
		$windData10 = json_encode($windData10);
		$dewPointData = json_encode($dewPointData);
		$humiData = json_encode($humiData);

		$this->assign([
			'time_point' => $time_point,
			'tempaData' => $tempaData,
			'windData2' => $windData2,
			'windData10' => $windData10,
			//'tmpData' => $tmpData,
			'dewPointData' => $dewPointData,
			'humiData' => $humiData,
			//'nightLight' => $nightLight,
			//'seeing' => $seeing,
			//'dust' => $dust,
		]);
		
		return view('weatherDetail');
	
	}/////////////////////////////////////////
	
}
