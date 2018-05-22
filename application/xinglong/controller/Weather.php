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
	public function weatherDetail ()
	{	
		$tempaData = json_encode([12,15,18,21,25,27,29,32]); //气温数据
		$windData2 = json_encode([0.8,1.1,0.5,0.6,0.7,0.9,0.7,0.8]); //2分钟平均风速
		$windData10 = json_encode([0.9,0.8,0.7,0.9,0.85,0.95,0.8,1.0]); //10分钟平均风速
		$tmpData = json_encode([12,15,18,21,25,27,29,32]); //温度/露点/湿度图-温度
		$dewPointData = json_encode([-2.2,-1.8,0.2,-1.6,-2.0,-2.5,0.3,-1.7]); //温度/露点/湿度图-露点
		$humiData = json_encode([18,18.7,19.2,18.2,17.7,20,21,18.7]); //温度/露点/湿度图-湿度
		$nightLight = json_encode([18,18.7,19.2,18.2,17.7,20,21,18.7]); //夜天光
		$seeing = json_encode([18,18.7,19.2,18.2,17.7,20,21,18.7]); //视宁度
		$dust = json_encode([18,18.7,19.2,18.2,17.7,20,21,18.7]); //粉尘

		$this->assign([
			'tempaData' => $tempaData,
			'windData2' => $windData2,
			'windData10' => $windData10,
			'tmpData' => $tmpData,
			'dewPointData' => $dewPointData,
			'humiData' => $humiData,
			'nightLight' => $nightLight,
			'seeing' => $seeing,
			'dust' => $dust,
		]);
		
		return view('weatherDetail');
	}/////////////////////////////////////////////////////////////
	
}
