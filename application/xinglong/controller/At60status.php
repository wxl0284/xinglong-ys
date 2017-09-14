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
		//时间信息
		$time = explode('#', date('Y.m.d#H:i:s', time()));
		$status['date'] = $time[0];
		$status['time'] = $time[1];
		//读取转台状态数据///////////////////////////////////////////
		$gimbalStatus = Db::table('at60gimbalstatus')->order('id desc')->find();
		/* if ($gimbalStatus['error'] === '0')
		{
			$gimbalError = '转台状态正常';
		}else{
			$gimbalError = '其他情况';
		}
		$status['gimbalStatus'] = $gimbalError; */
		
		//60cm望远镜 当前状态信息/////////////////////////////
		if ($gimbalStatus['curstatus'] == 1)
		{
			$status['curstatus'] = '离线';
		}elseif ($gimbalStatus['curstatus'] == 2)
		{
			$status['curstatus'] = '连接中';
		}elseif ($gimbalStatus['curstatus'] == 3)
		{
			$status['curstatus'] = '断开中';
		}elseif ($gimbalStatus['curstatus'] == 4)
		{
			$status['curstatus'] = '未找零';
		}elseif ($gimbalStatus['curstatus'] == 5)
		{
			$status['curstatus'] = '找零中';
		}elseif ($gimbalStatus['curstatus'] == 6)
		{
			$status['curstatus'] = '停止中';
		}elseif ($gimbalStatus['curstatus'] == 7)
		{
			$status['curstatus'] = '停止';
		}elseif ($gimbalStatus['curstatus'] == 8)
		{
			$status['curstatus'] = '急停中';
		}elseif ($gimbalStatus['curstatus'] == 9)
		{
			$status['curstatus'] = '急停';
		}elseif ($gimbalStatus['curstatus'] == 10)
		{
			$status['curstatus'] = '复位中';
		}elseif ($gimbalStatus['curstatus'] == 11)
		{
			$status['curstatus'] = '复位';
		}elseif ($gimbalStatus['curstatus'] == 12)
		{
			$status['curstatus'] = '等待恒速跟踪';
		}elseif ($gimbalStatus['curstatus'] == 13)
		{
			$status['curstatus'] = '恒速跟踪';
		}elseif ($gimbalStatus['curstatus'] == 14)
		{
			$status['curstatus'] = '速度修正中';
		}elseif ($gimbalStatus['curstatus'] == 15)
		{
			$status['curstatus'] = '位置修正中'; //？存疑
		}elseif ($gimbalStatus['curstatus'] == 16)
		{
			$status['curstatus'] = '等待变速跟踪';
		}elseif ($gimbalStatus['curstatus'] == 17)
		{
			$status['curstatus'] = '变速跟踪中';
		}elseif ($gimbalStatus['curstatus'] == 18)
		{
			$status['curstatus'] = '指向中';
		}elseif ($gimbalStatus['curstatus'] == 19)
		{
			$status['curstatus'] = '指向到位';
		}elseif ($gimbalStatus['curstatus'] == 20)
		{
			$status['curstatus'] = '等待恒速';
		}elseif ($gimbalStatus['curstatus'] == 21)
		{
			$status['curstatus'] = '恒速运动';
		}elseif ($gimbalStatus['curstatus'] == 22)
		{
			$status['curstatus'] = '异常';
		}
		//当前状态结束///////////////////////////////////////
		
		$status['trackError'] = 2.11; //位置信息：跟踪误差（？咋获取）
		//位置信息：当前时角
		$status['hourAngle'] = round($gimbalStatus['hourAngle'], 5);
		//获取镜盖 开/关 情况
		if ($gimbalStatus['coverIndex'] === 0)
		{
			$status['coverStatus'] = '关闭'; //位置信息：镜盖状态
		}elseif ($gimbalStatus['coverIndex'] === 1){
			$status['coverStatus'] = '打开';
		}
		
		$status['rightAscension'] = round($gimbalStatus['rightAscension'], 5); //当前赤经
		$status['declination'] = round($gimbalStatus['declination'], 5); //当前赤纬
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
		
		$status['targetRightAscension'] = round($gimbalStatus['targetRightAscension'], 5); //目标赤经
		$status['targetDeclination'] = round($gimbalStatus['targetDeclination'],5); //目标赤纬
		$status['azmiuth'] = round($gimbalStatus['azmiuth'],5); //当前方位
		$status['elevation'] = round($gimbalStatus['elevation'],5); //当前俯仰
		$status['RightAscensionSpeed'] = 12.3; //赤经速度 ?咋获取
		$status['declinationSpeed'] = 12.5; //赤纬速度 ?咋获取
		$status['derotatorPositon'] = $gimbalStatus['derotatorPositon']; //当前消旋位置
		$status['targetDerotatorPosition'] = $gimbalStatus['targetDerotatorPosition']; //目标消旋位置
		$status['axis1TrackError'] = $gimbalStatus['axis1TrackError']; //轴1跟踪误差
		$status['axis2TrackError'] = $gimbalStatus['axis2TrackError']; //轴2跟踪误差
		$status['axis3TrackError'] = $gimbalStatus['axis3TrackError']; //轴3跟踪误差
		//当前恒星时
		$status['siderealTime'] = round($gimbalStatus['siderealTime'],5);
		//接下来：转台可变属性
		$status['timeStamp'] = time(); //时间戳
		//j2000赤经
		$status['J2000RightAscension'] = round($gimbalStatus['J2000RightAscension'], 5);
		//j2000赤纬
		$status['J2000Declination'] = round($gimbalStatus['J2000Declination'], 5);
		//目标j2000赤经
		$status['targetJ2000RightAscension'] = round($gimbalStatus['targetJ2000RightAscension'], 5);
		//目标j2000赤纬
		$status['targetJ2000Declination'] = round($gimbalStatus['targetJ2000Declination'], 5);
		//转台 数据结束
		
		//读取ccd状态数据///////////////////////////////////////////
		$ccdStatus = Db::table('at60ccdstatus')->order('id desc')->find();
		/* if ($ccdStatus['error'] === '0')
		{
			$ccdError = 'CCD状态正常';
		}else{
			$ccdError = '其他情况';
		}
		$status['ccdStatus'] = $ccdError; */
		
		//ccd可变属性：当前状态信息/////////////////////////////////
		if ($ccdStatus['curstatus'] == 1)
		{
			$status['ccdCurStatus'] = '离线';
		}elseif ($ccdStatus['curstatus'] == 2)
		{
			$status['ccdCurStatus'] = '连接中';
		}elseif ($ccdStatus['curstatus'] == 3)
		{
			$status['ccdCurStatus'] = '断开中';
		}elseif ($ccdStatus['curstatus'] == 4)
		{
			$status['ccdCurStatus'] = '等待曝光';
		}elseif ($ccdStatus['curstatus'] == 5)
		{
			$status['ccdCurStatus'] = '曝光中';
		}elseif ($ccdStatus['curstatus'] == 6)
		{
			$status['ccdCurStatus'] = '读出中';
		}elseif ($ccdStatus['curstatus'] == 7)
		{
			$status['ccdCurStatus'] = '图像存储中';
		}elseif ($ccdStatus['curstatus'] == 7)
		{
			$status['ccdCurStatus'] = '图像存储中';
		}elseif ($ccdStatus['curstatus'] == 8)
		{
			$status['ccdCurStatus'] = '终止中';
		}elseif ($ccdStatus['curstatus'] == 9)
		{
			$status['ccdCurStatus'] = '空闲';
		}
 
		$status['ccdBaseline'] = $ccdStatus['baseline'];  //ccd可变属性：baseline值
		$status['ccdReadOutMode'] = $ccdStatus['readMode'];  //ccd可变属性：读出模式
		$status['ccdObserveBand'] = $ccdStatus['band'];  //ccd可变属性：当前拍摄波段
		$status['ccdJ2000RightAscension'] = round($ccdStatus['J2000RightAscension'], 5);  //ccd可变属性：当前拍摄目标赤经
		$status['ccdJ2000Declination'] = round($ccdStatus['J2000Declination'], 5);  //ccd可变属性：当前拍摄目标赤纬
		//ccd 数据结束///////////////////////////////////////////////
		
		//读取调焦器状态数据///////////////////////////////////////////
		$focusStatus = Db::table('at60focusstatus')->order('id desc')->find();
		/* if ($focusStatus['error'] === '0')
		{
			$focusError = '调焦器状态正常';
		}else{
			$focusError = '其他情况';
		}
		$status['focusStatus'] = $focusError;  //是否正常 */
		//调焦器当前状态//////////////////////////////////////////////
		if ($focusStatus['curstatus'] == 1)
		{
			$status['focusCurStatus'] = '离线';
		}elseif($focusStatus['curstatus'] == 2)
		{
			$status['focusCurStatus'] = '连接中';
		}elseif($focusStatus['curstatus'] == 3)
		{
			$status['focusCurStatus'] = '断开中';
		}elseif($focusStatus['curstatus'] == 4)
		{
			$status['focusCurStatus'] = '停止中';
		}elseif($focusStatus['curstatus'] == 5)
		{
			$status['focusCurStatus'] = '已停止';
		}elseif($focusStatus['curstatus'] == 6)
		{
			$status['focusCurStatus'] = '转动中';
		}elseif($focusStatus['curstatus'] == 7)
		{
			$status['focusCurStatus'] = '转动到位';
		}elseif($focusStatus['curstatus'] == 8)
		{
			$status['focusCurStatus'] = '未找零';
		}elseif($focusStatus['curstatus'] == 9)
		{
			$status['focusCurStatus'] = '找零中';
		}elseif($focusStatus['curstatus'] == 10)
		{
			$status['focusCurStatus'] = '急停中';
		}elseif($focusStatus['curstatus'] == 11)
		{
			$status['focusCurStatus'] = '急停';
		}
		//调焦器当前状态 结束/////////////////////////////////////////
		
		$status['focusPosition'] = $focusStatus['position'];  //当前位置
		$status['focusTargetPos'] = $focusStatus['targetPosition'];  //目标位置
		$status['focusIsHomed'] = $focusStatus['isHomed'];  //找零状态
		$status['focusIsTCompensation'] = $focusStatus['isTCompensation'];  //是否进行温度补偿
		$status['focusTCompenensation'] = $focusStatus['TCompenensation']; //温度补偿系数
		//调焦器 状态数据结束/////////////////////////////////////////////
		
		//读取圆顶状态数据///////////////////////////////////////////
		$slaveDomeStatus = Db::table('at60slavedomestatus')->order('id desc')->find();
		/* if ($slaveDomeStatus['error'] === '0')
		{
			$slaveDomeError = '圆顶状态正常';
		}else{
			$slaveDomeError = '其他情况';
		}
		$status['slaveDomeStatus'] = $slaveDomeError; //圆顶：是否正常 */
		//圆顶当前状态////////////////////////////////////////////////
		if($slaveDomeStatus['curstatus'] == 1)
		{
			$status['slaveDomeCurstatus'] = '离线';
		}elseif($slaveDomeStatus['curstatus'] == 2)
		{
			$status['slaveDomeCurstatus'] = '连接中';
		}elseif($slaveDomeStatus['curstatus'] == 3)
		{
			$status['slaveDomeCurstatus'] = '断开中';
		}elseif($slaveDomeStatus['curstatus'] == 4)
		{
			$status['slaveDomeCurstatus'] = '停止中';
		}elseif($slaveDomeStatus['curstatus'] == 5)
		{
			$status['slaveDomeCurstatus'] = '已停止';
		}elseif($slaveDomeStatus['curstatus'] == 6)
		{
			$status['slaveDomeCurstatus'] = '复位中';
		}elseif($slaveDomeStatus['curstatus'] == 7)
		{
			$status['slaveDomeCurstatus'] = '复位';
		}elseif($slaveDomeStatus['curstatus'] == 8)
		{
			$status['slaveDomeCurstatus'] = '指向中';
		}elseif($slaveDomeStatus['curstatus'] == 9)
		{
			$status['slaveDomeCurstatus'] = '指向到位';
		}elseif($slaveDomeStatus['curstatus'] == 10)
		{
			$status['slaveDomeCurstatus'] = '等待随动';
		}elseif($slaveDomeStatus['curstatus'] == 11)
		{
			$status['slaveDomeCurstatus'] = '随动中';
		}elseif($slaveDomeStatus['curstatus'] == 12)
		{
			$status['slaveDomeCurstatus'] = '异常';
		}
		//圆顶当前状态结束/////////////////////////////////////////////
		
		$status['slaveDomeScuttleStatus'] = $slaveDomeStatus['scuttleStatus']; //天窗状态
		$status['slaveDomeShadeStatus'] = $slaveDomeStatus['shadeStatus']; //风帘状态
		$status['slaveDomeErrorStatus'] = $slaveDomeStatus['errorString']; //错误标识
		//圆顶状态数据 结束/////////////////////////////////////////////
		
		//读取滤光片状态数据///////////////////////////////////////////
		$filterStatus = Db::table('at60filterstatus')->order('id desc')->find();
		/* if ($filterStatus['error'] === '0')
		{
			$filterError = '滤光片状态正常';
		}else{
			$filterError = '其他情况';
		}
		$status['filterStatus'] = $filterError; //滤光片 是否正常 */
		
		//滤光片当前状态///////////////////////////////////////////
		if($filterStatus['curstatus'] == 1)
		{
			$status['filterCurstatus'] = '离线';
		}elseif($filterStatus['curstatus'] == 2)
		{
			$status['filterCurstatus'] = '连接中';
		}elseif($filterStatus['curstatus'] == 3)
		{
			$status['filterCurstatus'] = '断开中';
		}elseif($filterStatus['curstatus'] == 4)
		{
			$status['filterCurstatus'] = '停止中';
		}elseif($filterStatus['curstatus'] == 5)
		{
			$status['filterCurstatus'] = '已停止';
		}elseif($filterStatus['curstatus'] == 6)
		{
			$status['filterCurstatus'] = '转动中';
		}elseif($filterStatus['curstatus'] == 7)
		{
			$status['filterCurstatus'] = '转动到位';
		}elseif($filterStatus['curstatus'] == 8)
		{
			$status['filterCurstatus'] = '未找零';
		}elseif($filterStatus['curstatus'] == 9)
		{
			$status['filterCurstatus'] = '找零中';
		}elseif($filterStatus['curstatus'] == 10)
		{
			$status['filterCurstatus'] = '急停中';
		}elseif($filterStatus['curstatus'] == 11)
		{
			$status['filterCurstatus'] = '急停';
		}
		//滤光片当前状态 结束 /////////////////////////////////////
		$status['filterIsHomed'] = $filterStatus['isHomed']; //是否找零
		$status['filterErrorStatus'] = $filterStatus['errorString']; //错误标识
		
		return json_encode($status);
	}
}
