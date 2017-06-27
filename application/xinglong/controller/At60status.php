<?php
namespace app\xinglong\controller;

use think\Controller;
use think\Db;

/*本控制器用来获取60cm望远镜的各种状态信息
*/
class At60status extends Controller
{
    //获取各设备数据
	public function devsStatus () 
	{
		$status = [];	//存放各种状态信息的数组
		//读取转台状态数据///////////////////////////////////////////
		$gimbalStatus = Db::table('at60gimbalstatus')->order('id desc')->find();
		if ($gimbalStatus['error'] === '0')
		{
			$gimbalError = '转台状态正常';
		}else{
			$gimbalError = '其他情况';
		}
		$status['gimbalStatus'] = $gimbalError;
		$status['curstatus'] = $gimbalStatus['curstatus']; //60cm望远镜 当前状态信息
		$status['trackError'] = 2.11; //位置信息：跟踪误差
		$status['hourAngle'] = $gimbalStatus['hourAngle']; //位置信息：当前时角
		//获取镜盖 开/关 情况
		if ($gimbalStatus['coverIndex'] === 0)
		{
			$status['coverStatus'] = '关闭'; //位置信息：镜盖状态
		}elseif ($gimbalStatus['coverIndex'] === 1){
			$status['coverStatus'] = '打开';
		}
		
		$status['rightAscension'] = $gimbalStatus['rightAscension']; //当前赤经
		$status['declination'] = $gimbalStatus['declination']; //当前赤纬
		$status['trackObjectName'] = $gimbalStatus['trackObjectName']; //目标名
		if ($gimbalStatus['trackType'] === 0)	//获取跟踪目标类型
		{
			$status['trackType'] = '恒星';
		}elseif ($gimbalStatus['trackType'] === 1){
			$status['trackType'] = '太阳';
		}elseif ($gimbalStatus['trackType'] === 2){
			$status['trackType'] = '月亮';
		}elseif ($gimbalStatus['trackType'] === 3){
			$status['trackType'] = '彗星';
		}elseif ($gimbalStatus['trackType'] === 4){
			$status['trackType'] = '行星';
		}elseif ($gimbalStatus['trackType'] === 5){
			$status['trackType'] = '卫星';
		}elseif ($gimbalStatus['trackType'] === 6){
			$status['trackType'] = '固定位置';
		}
		
		$status['targetRightAscension'] = $gimbalStatus['targetRightAscension']; //目标赤经
		$status['targetDeclination'] = $gimbalStatus['targetDeclination']; //目标赤纬
		$status['azmiuth'] = $gimbalStatus['azmiuth']; //当前方位
		$status['elevation'] = $gimbalStatus['elevation']; //当前俯仰
		$status['RightAscensionSpeed'] = 12.3; //赤经速度
		$status['declinationSpeed'] = 12.5; //赤纬速度
		$status['derotatorPositon'] = $gimbalStatus['derotatorPositon']; //当前消旋位置
		$status['targetDerotatorPosition'] = $gimbalStatus['targetDerotatorPosition']; //目标消旋位置
		$status['axis1TrackError'] = $gimbalStatus['axis1TrackError']; //轴1跟踪误差
		$status['axis2TrackError'] = $gimbalStatus['axis2TrackError']; //轴2跟踪误差
		$status['axis3TrackError'] = $gimbalStatus['axis3TrackError']; //轴3跟踪误差
		
		//读取ccd状态数据///////////////////////////////////////////
		$ccdStatus = Db::table('at60ccdstatus')->order('id desc')->find();
		if ($ccdStatus['error'] === '0')
		{
			$ccdError = 'CCD状态正常';
		}else{
			$ccdError = '其他情况';
		}
		$status['ccdStatus'] = $ccdError;
		$status['ccdCurStatus'] = $ccdStatus['curstatus'];  //ccd可变属性：当前状态
		$status['ccdBaseline'] = $ccdStatus['baseline'];  //ccd可变属性：baseline值
		$status['ccdReadOutMode'] = $ccdStatus['readMode'];  //ccd可变属性：读出模式
		$status['ccdObserveBand'] = $ccdStatus['band'];  //ccd可变属性：当前拍摄波段
		$status['J2000RightAscension'] = $ccdStatus['J2000RightAscension'];  //ccd可变属性：当前拍摄目标赤经
		$status['J2000Declination'] = $ccdStatus['J2000Declination'];  //ccd可变属性：当前拍摄目标赤纬
		
		
		//读取调焦器状态数据///////////////////////////////////////////
		$focusStatus = Db::table('at60focusstatus')->order('id desc')->find();
		if ($focusStatus['error'] === '0')
		{
			$focusError = '调焦器状态正常';
		}else{
			$focusError = '其他情况';
		}
		$status['focusStatus'] = $focusError;  //是否正常
		$status['focusPosition'] = $focusStatus['position'];  //当前位置
		$status['focusIsHomed'] = $focusStatus['isHomed'];  //找零状态
		$status['focusIsTCompensation'] = $focusStatus['isTCompensation'];  //是否进行温度补偿
		$status['focusTCompenensation'] = $focusStatus['TCompenensation'];  //温度补偿系数
		
		//读取圆顶状态数据///////////////////////////////////////////
		$slaveDomeStatus = Db::table('at60slavedomestatus')->order('id desc')->find();
		if ($slaveDomeStatus['error'] === '0')
		{
			$slaveDomeError = '圆顶状态正常';
		}else{
			$slaveDomeError = '其他情况';
		}
		$status['slaveDomeStatus'] = $slaveDomeError; //圆顶：是否正常
		$status['slaveDomeCurstatus'] = $slaveDomeStatus['curstatus']; //当前状态
		$status['slaveDomeScuttleStatus'] = $slaveDomeStatus['scuttleStatus']; //天窗状态
		$status['slaveDomeShadeStatus'] = $slaveDomeStatus['shadeStatus']; //风帘状态
		$status['slaveDomeErrorStatus'] = $slaveDomeStatus['errorString']; //错误标识
		
		//读取滤光片状态数据///////////////////////////////////////////
		$filterStatus = Db::table('at60filterstatus')->order('id desc')->find();
		if ($filterStatus['error'] === '0')
		{
			$filterError = '滤光片状态正常';
		}else{
			$filterError = '其他情况';
		}
		$status['filterStatus'] = $filterError; //滤光片 是否正常
		$status['filterIsHomed'] = $filterStatus['isHomed']; //是否找零
		$status['filterCurstatus'] = $filterStatus['curstatus']; //当前状态
		$status['filterErrorStatus'] = $filterStatus['errorString']; //错误标识
		
		return json_encode($status);
	}
}
