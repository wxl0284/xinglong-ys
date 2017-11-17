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
			$data['imagebitsval'] = $option['imageBitsVal'];
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
		
		//转移速度模式4
		if (in_array('transferSpeed', $option['viewOpt']))
		{
			$data['transferspeed'] = '1';
			$data['transferspeedval'] = $option['transferSpeedVal'];
		}else{
			$data['transferspeed'] = '0';
		}
		
		//增益模式2
		if (in_array('gainmode', $option['viewOpt']))
		{
			$data['gainmode'] = '1';
			$data['gainmodeval'] = $option['gainmodeVal'];
		}else{
			$data['gainmode'] = '0';
		}
		
		//增益档位4
		if (in_array('gainNumber', $option['viewOpt']))
		{
			$data['gainnumber'] = '1';
			$data['gainnumberval'] = $option['gainNumberVal'];
		}else{
			$data['gainnumber'] = '0';
		}
		
		//增益值
		if (in_array('gainValueArray', $option['viewOpt']))
		{
			$data['gainvaluearray'] = '1';
			$data['gainvaluearrayval'] = $option['gainValueArrayVal'];
		}else{
			$data['gainvaluearray'] = '0';
		}
		
		//读出噪声值
		if (in_array('readoutNoiseArray', $option['viewOpt']))
		{
			$data['readoutnoisearray'] = '1';
			$data['readoutnoisearrayval'] = $option['readoutNoiseArrayVal'];
		}else{
			$data['readoutnoisearray'] = '0';
		}
		
		//快门类型
		if (in_array('ShutterType', $option['viewOpt']))
		{
			$data['shuttertype'] = '1';
			$data['shuttertypeval'] = $option['ShutterTypeVal'];
		}else{
			$data['shuttertype'] = '0';
		}
		
		//快门模式
		if (in_array('ccdShutterMode', $option['viewOpt']))
		{
			$data['ccdshuttermode'] = '1';
			$data['ccdshuttermodeval'] = $option['ccdShutterModeVal'];
		}else{
			$data['ccdshuttermode'] = '0';
		}
		
		//是否支持帧转移
		if (in_array('IsSupportFullFrame', $option['viewOpt']))
		{
			$data['issupportfullframe'] = '1';
		}else{
			$data['issupportfullframe'] = '0';
		}
		
		//是否支持EM
		if (in_array('IsSupportEM', $option['viewOpt']))
		{
			$data['issupportem'] = '1';
		}else{
			$data['issupportem'] = '0';
		}
		
		//支持CMOS noise filter
		if (in_array('IsSupportsCmosNoiseFilter', $option['viewOpt']))
		{
			$data['issupportscmosnoisefilter'] = '1';
		}else{
			$data['issupportscmosnoisefilter'] = '0';
		}
		
		//支持base line
		if (in_array('IsSupportBaseLine', $option['viewOpt']))
		{
			$data['issupportbaseline'] = '1';
		}else{
			$data['issupportbaseline'] = '0';
		}
		
		//支持Over scan
		if (in_array('IsSupportOverScan', $option['viewOpt']))
		{
			$data['issupportoverscan'] = '1';
		}else{
			$data['issupportoverscan'] = '0';
		}
		
		//BIN值
		if (in_array('binArray', $option['viewOpt']))
		{
			$data['binarray'] = '1';
			$data['binarrayval'] = $option['binArrayVal'];
		}else{
			$data['binarray'] = '0';
		}
		
		//支持开窗
		if (in_array('IsSupportROI', $option['viewOpt']))
		{
			$data['issupportroi'] = '1';
		}else{
			$data['issupportroi'] = '0';
		}
		
		//接口类型
		if (in_array('InterfaceType', $option['viewOpt']))
		{
			$data['interfacetype'] = '1';
			$data['interfacetypeval'] = $option['InterfaceTypeVal'];
		}else{
			$data['interfacetype'] = '0';
		}
		
		//曝光触发模式
		if (in_array('ExposeTriggerMode', $option['viewOpt']))
		{
			$data['exposetriggermode'] = '1';
			$data['exposetriggermodeval'] = $option['ExposeTriggerModeVal'];
		}else{
			$data['exposetriggermode'] = '0';
		}
		
		//最大EM
		if (in_array('EmMaxValue', $option['viewOpt']))
		{
			$data['emmaxvalue'] = '1';
			$data['emmaxvalueval'] = $option['EmMaxValueVal'];
		}else{
			$data['emmaxvalue'] = '0';
		}
		
		//最小EM
		if (in_array('EmMinValue', $option['viewOpt']))
		{
			$data['emminvalue'] = '1';
			$data['emminvalueval'] = $option['EmMinValueVal'];
		}else{
			$data['emminvalue'] = '0';
		}
		
		//支持连接
		if (in_array('ccdCanConnect', $option['viewOpt']))
		{
			$data['ccdcanconnect'] = '1';
		}else{
			$data['ccdcanconnect'] = '0';
		}
		
		//支持设置制冷温度
		if (in_array('canSetCoolerT', $option['viewOpt']))
		{
			$data['cansetcoolert'] = '1';
		}else{
			$data['cansetcoolert'] = '0';
		}
		
		//支持设置曝光策略
		if (in_array('canSetExposureParam', $option['viewOpt']))
		{
			$data['cansetexposureparam'] = '1';
		}else{
			$data['cansetexposureparam'] = '0';
		}
		
		//支持开始曝光指令
		if (in_array('canStartExposure', $option['viewOpt']))
		{
			$data['canstartexposure'] = '1';
		}else{
			$data['canstartexposure'] = '0';
		}
		
		//支持停止曝光指令
		if (in_array('canStopExposure', $option['viewOpt']))
		{
			$data['canstopexposure'] = '1';
		}else{
			$data['canstopexposure'] = '0';
		}
		
		//支持终止曝光指令
		if (in_array('canAbortExposure', $option['viewOpt']))
		{
			$data['canabortexposure'] = '1';
		}else{
			$data['canabortexposure'] = '0';
		}
		
		//支持设置增益
		if (in_array('canSetGain', $option['viewOpt']))
		{
			$data['cansetgain'] = '1';
		}else{
			$data['cansetgain'] = '0';
		}
		
		//可设置读出速度模式值
		if (in_array('canSetReadoutSpeedMode', $option['viewOpt']))
		{
			$data['cansetreadoutspeedmode'] = '1';
		}else{
			$data['cansetreadoutspeedmode'] = '0';
		}
		
		//可设置转移速度模式值
		if (in_array('canSetTransferSpeedMode', $option['viewOpt']))
		{
			$data['cansettransferspeedmode'] = '1';
		}else{
			$data['cansettransferspeedmode'] = '0';
		}
		
		//支持设置BIN
		if (in_array('canSetBin', $option['viewOpt']))
		{
			$data['cansetbin'] = '1';
		}else{
			$data['cansetbin'] = '0';
		}
		
		//支持设置ROI
		if (in_array('canSetROI', $option['viewOpt']))
		{
			$data['cansetroi'] = '1';
		}else{
			$data['cansetroi'] = '0';
		}
		
		//支持设置快门
		if (in_array('canSetShutter', $option['viewOpt']))
		{
			$data['cansetshutter'] = '1';
		}else{
			$data['cansetshutter'] = '0';
		}
		
		//支持设置帧转移
		if (in_array('canSetFullFrame', $option['viewOpt']))
		{
			$data['cansetfullframe'] = '1';
		}else{
			$data['cansetfullframe'] = '0';
		}
		
		//支持设置EM
		if (in_array('canSetEM', $option['viewOpt']))
		{
			$data['cansetem'] = '1';
		}else{
			$data['cansetem'] = '0';
		}
		
		//支持CMOS noise filter
		if (in_array('canNoiseFilter', $option['viewOpt']))
		{
			$data['cannoisefilter'] = '1';
		}else{
			$data['cannoisefilter'] = '0';
		}
		
		//支持设置base line
		if (in_array('canSetBaseline', $option['viewOpt']))
		{
			$data['cansetbaseline'] = '1';
		}else{
			$data['cansetbaseline'] = '0';
		}
		
		//支持设置base line
		if (in_array('canSetBaseline', $option['viewOpt']))
		{
			$data['cansetbaseline'] = '1';
		}else{
			$data['cansetbaseline'] = '0';
		}
		
		//支持设置over scan
		if (in_array('canSetOverScan', $option['viewOpt']))
		{
			$data['cansetoverscan'] = '1';
		}else{
			$data['cansetoverscan'] = '0';
		}
	/***********CCD之配置 结束***********/
	
	/***********调校器之配置 开始***********/
		//最大值
		if (in_array('focusMaxValue', $option['viewOpt']))
		{
			$data['focusmaxvalue'] = '1';
			$data['focusmaxvalueval'] = $option['focusMaxValueVal'];
		}else{
			$data['focusmaxvalue'] = '0';
		}
		
		//最小值
		if (in_array('focusMinValue', $option['viewOpt']))
		{
			$data['focusminvalue'] = '1';
			$data['focusminvalueval'] = $option['focusMinValueVal'];
		}else{
			$data['focusminvalue'] = '0';
		}
		
		//分辨率
		if (in_array('focusIncrement', $option['viewOpt']))
		{
			$data['focusincrement'] = '1';
			$data['focusincrementval'] = $option['focusIncrementVal'];
		}else{
			$data['focusincrement'] = '0';
		}
		
		//支持找零
		if (in_array('focusCanFindHome', $option['viewOpt']))
		{
			$data['focuscanfindhome'] = '1';
		}else{
			$data['focuscanfindhome'] = '0';
		}
		
		//支持温度补偿
		if (in_array('canTempertureCompensate', $option['viewOpt']))
		{
			$data['cantemperturecompensate'] = '1';
		}else{
			$data['cantemperturecompensate'] = '0';
		}
		
		//最大速度
		if (in_array('focusMaxSpeed', $option['viewOpt']))
		{
			$data['focusmaxspeed'] = '1';
		}else{
			$data['focusmaxspeed'] = '0';
		}
		
		//支持连接指令
		if (in_array('focusCanConnect', $option['viewOpt']))
		{
			$data['focuscanconnect'] = '1';
		}else{
			$data['focuscanconnect'] = '0';
		}
		
		//支持连接指令
		if (in_array('focusCanConnect', $option['viewOpt']))
		{
			$data['focuscanconnect'] = '1';
		}else{
			$data['focuscanconnect'] = '0';
		}
		
		//支持设置目标位置
		if (in_array('focusSetPosition', $option['viewOpt']))
		{
			$data['focussetposition'] = '1';
		}else{
			$data['focussetposition'] = '0';
		}
		
		//支持设置恒速运动
		if (in_array('focusCanSetSpeed', $option['viewOpt']))
		{
			$data['focuscansetspeed'] = '1';
		}else{
			$data['focuscansetspeed'] = '0';
		}
		
		//支持停止运动指令
		if (in_array('focusCanStop', $option['viewOpt']))
		{
			$data['focuscanstop'] = '1';
		}else{
			$data['focuscanstop'] = '0';
		}
		
		//支持使能温度补偿
		if (in_array('canEnableTempertureCompensate', $option['viewOpt']))
		{
			$data['canenabletemperturecompensate'] = '1';
		}else{
			$data['canenabletemperturecompensate'] = '0';
		}
		
		//支持设置温度补偿系数
		if (in_array('canSetTempertCompensatecoefficient', $option['viewOpt']))
		{
			$data['cansettempertcompensatecoefficient'] = '1';
		}else{
			$data['cansettempertcompensatecoefficient'] = '0';
		}
	/***********调校器之配置 结束***************/
	/***********随动圆顶之配置 开始**************/
		//圆顶类型
		if (in_array('DomeType', $option['viewOpt']))
		{
			$data['dometype'] = '1';
			$data['dometypeval'] = $option['DomeTypeVal'];
		}else{
			$data['dometype'] = '0';
		}
		
		//是否具备风帘
		if (in_array('HasShade', $option['viewOpt']))
		{
			$data['hasshade'] = '1';
		}else{
			$data['hasshade'] = '0';
		}
		
		//最大转动速度
		if (in_array('sDomeMaxSpeed', $option['viewOpt']))
		{
			$data['sdomemaxspeed'] = '1';
			$data['sdomemaxspeedval'] = $option['sDomeMaxSpeedVal'];
		}else{
			$data['sdomemaxspeed'] = '0';
		}
		
		//支持设置目标方位
		if (in_array('canSetDomePositin', $option['viewOpt']))
		{
			$data['cansetdomepositin'] = '1';
		}else{
			$data['cansetdomepositin'] = '0';
		}
		
		//支持设置风帘位置
		if (in_array('canSetShadePosition', $option['viewOpt']))
		{
			$data['cansetshadeposition'] = '1';
		}else{
			$data['cansetshadeposition'] = '0';
		}
		
		//支持设置转动速度
		if (in_array('canSetRotateSpeed', $option['viewOpt']))
		{
			$data['cansetshadeposition'] = '1';
		}else{
			$data['cansetshadeposition'] = '0';
		}
		
		//支持停止运动指令
		if (in_array('sDomeCanStop', $option['viewOpt']))
		{
			$data['sdomecanstop'] = '1';
		}else{
			$data['sdomecanstop'] = '0';
		}
		
		//支持打开天窗指令
		if (in_array('canOpenShutter', $option['viewOpt']))
		{
			$data['canopenshutter'] = '1';
		}else{
			$data['canopenshutter'] = '0';
		}
		
		//支持控制风帘运动
		if (in_array('canSetShadeSpeed', $option['viewOpt']))
		{
			$data['cansetshadespeed'] = '1';
		}else{
			$data['cansetshadespeed'] = '0';
		}
		
		//支持连接指令
		if (in_array('sDomeCanConnect', $option['viewOpt']))
		{
			$data['sdomecanconnect'] = '1';
		}else{
			$data['sdomecanconnect'] = '0';
		}
	/***********随动圆顶之配置 结束*************/
	
	/***********滤光片之配置 结束*************/
		//插槽数目
		if (in_array('numberOfFilter', $option['viewOpt']))
		{
			$data['numberoffilter'] = '1';
			$data['numberoffilterval'] = $option['numberOfFilterVal'];
		}else{
			$data['numberoffilter'] = '0';
		}
		
		//滤光片类型
		if (in_array('FilterSystem', $option['viewOpt']))
		{
			$data['filtersystem'] = '1';
			$data['filtersystemval'] = $option['FilterSystemVal'];
		}else{
			$data['filtersystem'] = '0';
		}
		
		//滤光片名称
		if (in_array('FilterName', $option['viewOpt']))
		{
			$data['filtername'] = '1';
			$data['filternameval'] = $option['FilterNameVal'];
		}else{
			$data['filtername'] = '0';
		}
		
		//滤光片焦距偏差值
		if (in_array('FilterFocusLengthCompensate', $option['viewOpt']))
		{
			$data['filterfocuslengthcompensate'] = '1';
			$data['filterfocuslengthcompensateval'] = $option['FilterFocusLengthCompensateVal'];
		}else{
			$data['filterfocuslengthcompensate'] = '0';
		}
		
		//插槽尺寸
		if (in_array('FilterSize', $option['viewOpt']))
		{
			$data['filtersize'] = '1';
			$data['filtersizeval'] = $option['FilterSizeVal'];
		}else{
			$data['filterfocuslengthcompensate'] = '0';
		}
		
		//形状
		if (in_array('FilterShape', $option['viewOpt']))
		{
			$data['filtershape'] = '1';
			$data['filtershapeval'] = $option['FilterShapeVal'];
		}else{
			$data['filtershape'] = '0';
		}
		
		//支持设置滤光片位置
		if (in_array('canSetFilterPosition', $option['viewOpt']))
		{
			$data['cansetfilterposition'] = '1';
		}else{
			$data['cansetfilterposition'] = '0';
		}
		
		//支持连接指令
		if (in_array('filterCanConnect', $option['viewOpt']))
		{
			$data['filtercanconnect'] = '1';
		}else{
			$data['filtercanconnect'] = '0';
		}
	/***********滤光片之配置 结束*************/
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