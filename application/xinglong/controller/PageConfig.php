<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Cache;
use think\Session;
use think\Request;
use think\Cookie;
use think\Db;
use app\xinglong\model\At60config;
//use think\Config;


class PageConfig extends Controller
{
	//检测是否登录////////////////////////////////////////////////
    public function _initialize ()
    {		
		//未登录
        if (!Session::has('login'))
        {
			if (Request::instance()->isAjax())
			{
				return '请完成登录后，再进行相关操作！';
			}
            $request = Request::instance();
            Cookie::set('url', $request->url(true));
            $this->error('请完成登录后，再进行相关操作！', '/');
        }   
    }
	
	//显示60cm望远镜配置选择页
	public function at60config ()
	{
		return view('pageConfig/at60config');
	}
	
	//根据用户提交的选项，进行60cm望远镜
	public function doAt60config ()
	{
		//未选中任何选项
		$option = input();

		if(empty($option['viewOpt'])) {
			return '未做任何选择配置!';
		}
		
		//将提交的数据，组装为一个数组
		//转台之配置
		if (in_array('atType', $option['viewOpt']))
		{//转台：类型被选中
			$data['attype'] = '1';
			$data['attypeval'] = $option['atTypeVal'];
		}else{//转台：类型未被选中
			$data['attype'] = '0';
		}
		
		//焦点类型
		if (in_array('focustype', $option['viewOpt']))
		{
			$data['focustype'] = '1';
			$data['focustypeval'] = $option['focustypeVal'];
		}else{
			$data['focustype'] = '0';
		}
		
		//焦比
		if (in_array('focusratio', $option['viewOpt']))
		{
			$data['focusratio'] = '1';
			$data['focusratioval'] = $option['focusratioVal'];
		}else{
			$data['focusratio'] = '0';
		}
		
		//焦距
		if (in_array('focuslength', $option['viewOpt']))
		{
			$data['focuslength'] = '1';
			$data['focuslengthval'] = $option['focuslengthVal'];
		}else{
			$data['focuslength'] = '0';
		}
		
		//轴1最大速度
		if (in_array('maxAxis1Speed', $option['viewOpt']))
		{
			$data['maxaxis1speed'] = '1';
			$data['maxaxis1speedval'] = $option['maxAxis1SpeedVal'];
		}else{
			$data['maxaxis1speed'] = '0';
		}
		
		//轴2最大速度
		if (in_array('maxAxis2Speed', $option['viewOpt']))
		{
			$data['maxaxis2speed'] = '1';
			$data['maxaxis2speedval'] = $option['maxAxis2SpeedVal'];
		}else{
			$data['maxaxis2speed'] = '0';
		}
		
		//轴3最大速度
		if (in_array('maxAxis3Speed', $option['viewOpt']))
		{
			$data['maxaxis3speed'] = '1';
			$data['maxaxis3speedval'] = $option['maxAxis3SpeedVal'];
		}else{
			$data['maxaxis3speed'] = '0';
		}
		
		//轴1最大加速度
		if (in_array('maxAxis1Acceleration', $option['viewOpt']))
		{
			$data['maxaxis1acceleration'] = '1';
			$data['maxaxis1accelerationval'] = $option['maxAxis1AccelerationVal'];
		}else{
			$data['maxaxis1acceleration'] = '0';
		}
		
		//轴2最大加速度
		if (in_array('maxAxis2Acceleration', $option['viewOpt']))
		{
			$data['maxaxis2acceleration'] = '1';
			$data['maxaxis2accelerationval'] = $option['maxAxis2AccelerationVal'];
		}else{
			$data['maxaxis2acceleration'] = '0';
		}
		
		//轴3最大加速度
		if (in_array('maxAxis3Acceleration', $option['viewOpt']))
		{
			$data['maxaxis3acceleration'] = '1';
			$data['maxaxis3accelerationval'] = $option['maxAxis3AccelerationVal'];
		}else{
			$data['maxaxis3acceleration'] = '0';
		}
		
		//轴1复位位置
		if (in_array('axis1ParkPosition', $option['viewOpt']))
		{
			$data['axis1parkposition'] = '1';
			$data['axis1parkpositionval'] = $option['axis1ParkPositionVal'];
		}else{
			$data['axis1parkposition'] = '0';
		}
		
		//轴2复位位置
		if (in_array('axis2ParkPosition', $option['viewOpt']))
		{
			$data['axis2parkposition'] = '1';
			$data['axis2parkpositionval'] = $option['axis2ParkPositionVal'];
		}else{
			$data['axis2parkposition'] = '0';
		}
		
		//轴3复位位置
		if (in_array('axis3ParkPosition', $option['viewOpt']))
		{
			$data['axis3parkposition'] = '1';
			$data['axis3parkpositionval'] = $option['axis3ParkPositionVal'];
		}else{
			$data['axis3parkposition'] = '0';
		}
		
		//是否有第3轴
		if (in_array('haveAxis3', $option['viewOpt']))
		{
			$data['haveaxis3'] = '1';
		}else{
			$data['haveaxis3'] = '0';
		}
		
		//是否有镜盖（轴5）
		if (in_array('haveAxis5', $option['viewOpt']))
		{
			$data['haveaxis5'] = '1';
		}else{
			$data['haveaxis5'] = '0';
		}
		
		//俯仰最低值
		if (in_array('minElevation', $option['viewOpt']))
		{
			$data['minelevation'] = '1';
			$data['minelevationval'] = $option['minElevationVal'];
		}else{
			$data['minelevation'] = '0';
		}
		
		//温度传感器数目
		if (in_array('numTemperatureSensor', $option['viewOpt']))
		{
			$data['numtemperaturesensor'] = '1';
			$data['numtemperaturesensorval'] = $option['numTemperatureSensorVal'];
		}else{
			$data['numtemperaturesensor'] = '0';
		}
		
		//湿度传感器数目
		if (in_array('numHumiditySensor', $option['viewOpt']))
		{
			$data['numhumiditysensor'] = '1';
			$data['numhumiditysensorval'] = $option['numHumiditySensorVal'];
		}else{
			$data['numhumiditysensor'] = '0';
		}
		
		//支持连接指令
		if (in_array('gimbalCanConnect', $option['viewOpt']))
		{
			$data['gimbalcanconnect'] = '1';
		}else{
			$data['gimbalcanconnect'] = '0';
		}
		
		//支持找零
		if (in_array('canFindHome', $option['viewOpt']))
		{
			$data['canfindhome'] = '1';
		}else{
			$data['canfindhome'] = '0';
		}
		
		//支持跟踪恒星
		if (in_array('canTrackStar', $option['viewOpt']))
		{
			$data['cantrackstar'] = '1';
		}else{
			$data['cantrackstar'] = '0';
		}
		
		//支持设置目标名称
		if (in_array('canSetObjectName', $option['viewOpt']))
		{
			$data['cansetobjectname'] = '1';
		}else{
			$data['cansetobjectname'] = '0';
		}
		
		//支持指向固定位置
		if (in_array('canSlewAzEl', $option['viewOpt']))
		{
			$data['canslewazel'] = '1';
		}else{
			$data['canslewazel'] = '0';
		}
		
		//支持轴3指向固定位置
		if (in_array('canSlewDerotator', $option['viewOpt']))
		{
			$data['canslewderotator'] = '1';
		}else{
			$data['canslewderotator'] = '0';
		}
		
		//支持设置轴3工作模式
		if (in_array('canConfigDerotator', $option['viewOpt']))
		{
			$data['canconfigderotator'] = '1';
		}else{
			$data['canconfigderotator'] = '0';
		}
		
		//支持停止指令
		if (in_array('canStop', $option['viewOpt']))
		{
			$data['canstop'] = '1';
		}else{
			$data['canstop'] = '0';
		}
		
		//支持设置跟踪速度
		if (in_array('canSetTrackSpeed', $option['viewOpt']))
		{
			$data['cansettrackspeed'] = '1';
		}else{
			$data['cansettrackspeed'] = '0';
		}
		
		//支持复位
		if (in_array('canPark', $option['viewOpt']))
		{
			$data['canpark'] = '1';
		}else{
			$data['canpark'] = '0';
		}
		
		//支持恒速运动
		if (in_array('canFixedMove', $option['viewOpt']))
		{
			$data['canfixedmove'] = '1';
		}else{
			$data['canfixedmove'] = '0';
		}
		
		//支持位置修正
		if (in_array('canPositionCorrect', $option['viewOpt']))
		{
			$data['canpositioncorrect'] = '1';
		}else{
			$data['canpositioncorrect'] = '0';
		}
		
		//支持镜盖操作
		if (in_array('canCoverOperation', $option['viewOpt']))
		{
			$data['cancoveroperation'] = '1';
		}else{
			$data['cancoveroperation'] = '0';
		}
		
		//支持焦点切换镜操作
		if (in_array('canFocusOperation', $option['viewOpt']))
		{
			$data['canfocusoperation'] = '1';
		}else{
			$data['canfocusoperation'] = '0';
		}
		
		//支持急停
		if (in_array('canEmergencyStop', $option['viewOpt']))
		{
			$data['canemergencystop'] = '1';
		}else{
			$data['canemergencystop'] = '0';
		}
		
		//支持保存同步数据
		if (in_array('canSaveSyncData', $option['viewOpt']))
		{
			$data['cansavesyncdata'] = '1';
		}else{
			$data['cansavesyncdata'] = '0';
		}
		
		//支持跟踪卫星
		if (in_array('canTrackSatellite', $option['viewOpt']))
		{
			$data['cantracksatellite'] = '1';
		}else{
			$data['cantracksatellite'] = '0';
		}	
	/***********转台之配置 结束***********/
	
	/***********CCD之配置 开始***********/
		//探测器类型
		if (in_array('ccdType', $option['viewOpt']))
		{
			$data['ccdtype'] = '1';
			$data['ccdtypeval'] = $option['ccdTypeVal'];
		}else{
			$data['ccdtype'] = '0';
		}
		
		//x像素
		if (in_array('xPixel', $option['viewOpt']))
		{
			$data['xpixel'] = '1';
			$data['xpixelval'] = $option['xPixelVal'];
		}else{
			$data['xpixel'] = '0';
		}
		
		//y像素
		if (in_array('yPixel', $option['viewOpt']))
		{
			$data['ypixel'] = '1';
			$data['ypixelval'] = $option['yPixelVal'];
		}else{
			$data['ypixel'] = '0';
		}
		
		//x像元大小
		if (in_array('xPixelSize', $option['viewOpt']))
		{
			$data['xpixelsize'] = '1';
			$data['xpixelsizeval'] = $option['xPixelSizeVal'];
		}else{
			$data['xpixelsize'] = '0';
		}
		
		//y像元大小
		if (in_array('yPixelSize', $option['viewOpt']))
		{
			$data['ypixelsize'] = '1';
			$data['ypixelsizeval'] = $option['yPixelSizeVal'];
		}else{
			$data['ypixelsize'] = '0';
		}
		
		//传感器名称
		if (in_array('sensorName', $option['viewOpt']))
		{
			$data['sensorname'] = '1';
			$data['sensornameval'] = $option['sensorNameVal'];
		}else{
			$data['sensorname'] = '0';
		}
		
		//图像位数
		if (in_array('imageBits', $option['viewOpt']))
		{
			$data['imagebits'] = '1';
			$data['imagevitsval'] = $option['imageBitsVal'];
		}else{
			$data['imagebits'] = '0';
		}
		
		//制冷方式
		if (in_array('coolerMode', $option['viewOpt']))
		{
			$data['coolermode'] = '1';
			$data['coolermodeval'] = $option['coolerModeVal'];
		}else{
			$data['coolermode'] = '0';
		}
		
		//最低制冷温度
		if (in_array('lowCoolerT', $option['viewOpt']))
		{
			$data['lowcoolert'] = '1';
			$data['lowcoolertval'] = $option['lowCoolerTVal'];
		}else{
			$data['lowcoolert'] = '0';
		}
		
		//最大曝光时间
		if (in_array('maxExposureTime', $option['viewOpt']))
		{
			$data['maxexposuretime'] = '1';
			$data['maxexposuretimetval'] = $option['maxExposureTimeTVal'];
		}else{
			$data['maxexposuretime'] = '0';
		}
		
		//最小曝光时间
		if (in_array('minExposureTime', $option['viewOpt']))
		{
			$data['minexposuretime'] = '1';
			$data['minexposuretimeval'] = $option['minExposureTimeVal'];
		}else{
			$data['minexposuretime'] = '0';
		}
		
		//曝光时间分辨率
		if (in_array('exposureTimeRation', $option['viewOpt']))
		{
			$data['exposuretimeration'] = '1';
			$data['exposuretimerationval'] = $option['exposureTimeRationVal'];
		}else{
			$data['exposuretimeration'] = '0';
		}
		
		//满阱电荷
		if (in_array('fullWellDepth', $option['viewOpt']))
		{
			$data['fullwelldepth'] = '1';
			$data['fullwelldepthval'] = $option['fullWellDepthVal'];
		}else{
			$data['fullwelldepth'] = '0';
		}
		
		//读出模式5
		if (in_array('readoutMode5', $option['viewOpt']))
		{
			$data['readoutmode5'] = '1';
			$data['readoutmode5val'] = $option['readoutMode5Val'];
		}else{
			$data['readoutmode5'] = '0';
		}
		
		//读出速度模式4
		if (in_array('readoutSpeed', $option['viewOpt']))
		{
			$data['readoutspeed'] = '1';
			$data['readoutspeedval'] = $option['readoutSpeedVal'];
		}else{
			$data['readoutspeed'] = '0';
		}
	/***********CCD之配置 结束***********/
		$At60config = new At60config;
		$configData = $At60config->all();
		
		if (empty($configData))
		{	//首次配置，进行数据新增
			$res = $At60config->save($data);
			if ($res)
			{
				return '60CM望远镜配置成功!';
			}else{
				return '60CM望远镜配置失败!';
			}
		}else{//已有配置，进行数据更新
			$res = $At60config->save($data,['pk_at60config' => $configData[0]['pk_at60config']]);
			if ($res)
			{
				return '60CM望远镜配置成功!';
			}else{
				return '60CM望远镜配置失败!';
			}
		}
		
		/* if (!in_array('siderealTime_1', $option['viewOpt']))
		{
			$cachePage['siderealTime_1'] = '';
		}else{
			
			$cachePage['siderealTime_1'] = "<li class='li fl'>当前恒星时:<span id='siderealTime_1'></span></li>";
			$cachePage['data'] = 1;
		}
		
		if (!in_array('hourAngle', $option['viewOpt']))
		{
			$cachePage['hourAngle'] = '';
		}else{
			
			$cachePage['hourAngle'] = "<li class='li fl'>当前时角:<span id='hourAngle_1'></span></li>";
			$cachePage['data'] = 1;
		}
		
		//将60cm望远镜页面的html 写入缓存
		$ok = Cache::set('at60Page', $cachePage);
		
		if ($ok)
		{//页面缓存成功
			return '页面设置成功!';
		}else{
			return '页面设置失败!';
		} */
	
	}//保存60CM配置数据 结束////////////////////////////////
	
	//显示80cm望远镜配置选择页
	public function at80config ()
	{
		return view('pageConfig/at80config');
	}
}