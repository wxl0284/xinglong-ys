<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"D:\xampp\htdocs\demo\public/../application/xinglong\view\pageConfig\at80config.html";i:1509686876;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <title>配置80CM望远镜</title>
     <link rel="stylesheet" type="text/css" href="/static/css/common-1.css" />
     <link rel="stylesheet" type="text/css" href="/static/css/at60config.css" />
     <script type="text/javascript" src="/static/plugin/easyui-1.3.5/jquery.min.js"></script>
     <script type="text/javascript" src="/static/js/at80config.js"></script>
</head>
<body>
	<header class='pos_r'>
	   <div class='pos_r'>
	      <img class='pos_a' src='/static/images-0/logo1.png'/>
		  <div id='atList' class='pos_a'>
				望远镜列表
				<ul id='atListUl' class='center displayNo'>
					<li><a href='<?php echo url("/xinglong/at60"); ?>' target='_blank'>60CM</a></li>
					<hr>
					<li><a href='<?php echo url("/xinglong/at80"); ?>' target='_blank'>80CM</a></li>
					<hr>
					<li>85CM</li>
					<hr>
					<li>85CM</li>
				</ul>
		   </div>
		   <div id='atConfig' class='pos_a'>
				配置望远镜
				<ul id='atConfigList' class='center displayNo'>
					<li><a href='<?php echo url("/xinglong/page_config/at60config"); ?>' target='_blank'>配置60CM</a></li>
					<hr>
					<li><a href='<?php echo url("/xinglong/page_config/at80config"); ?>' target='_blank'>配置80CM</a></li>
					<hr>
					<li>配置85CM</li>
				</ul>
		   </div>
		  <!-- <a href="<?php echo url('/xinglong/result'); ?>" class='pos_r' id='res'>观测结果</a> -->
		   <p class='pos_a'>
				<a href="<?php echo url('xinglong/control/front'); ?>">首页&nbsp;&nbsp;&nbsp;&nbsp;
				</a>
				<?php if(\think\Session::get('role') == 1): ?>
				<a href='/xinglong/user'>用户管理&nbsp;&nbsp;&nbsp;&nbsp;</a>
				<?php endif; ?>
				<a href='/xinglong/user/passwd'>修改密码&nbsp;&nbsp;&nbsp;&nbsp;
				</a>
				<span>欢迎!&nbsp;&nbsp;<?php echo (\think\Session::get('login')) ? \think\Session::get('login') :  ''; ?>&nbsp;
				</span>
		   </p>
		   <div class='pos_a'><a href='/xinglong/control/logout'>&nbsp;&nbsp;&nbsp;退出&nbsp;&nbsp;&nbsp;</a></div> 
	    </div>
	</header>
    <div id='all'>
        <h3 class='center'>配置60CM望远镜(页面内容还是60cm的)</h3>
		<br>
		<form id='at60devs' class='center_mrg center'><!--60cm子设备-->
			转台<input type='checkbox' name='devs[]' value='gimbal'>
			CCD<input type='checkbox' name='devs[]' value='ccd'>
			调焦器<input type='checkbox' name='devs[]' value='focus'>
			随动圆顶<input type='checkbox' name='devs[]' value='slaveDome'>
			滤光片<input type='checkbox' name='devs[]' value='filter'>
			全开圆顶<input type='checkbox' name='devs[]' value='openDome'>
			数据处理<input type='checkbox' name='devs[]' value='dataHandle'>
		</form><!--60cm子设备 结束-->
		<br>
		<hr>
		<form id='allOption'><!--60cm各子设备 指令属性选项-->
			<div id='gimbal' class='displayNo border mrgTop'>
			  <span class='optionTitle'>转台指令</span><br>
				<span class='optionLeng inline'>连接望远镜<input type='checkbox' name='viewOpt[]' value='connectAt'></span>
				<span class='optionLeng inline'>断开望远镜<input type='checkbox' name='viewOpt[]' value='disconnect'></span>
				<span class='optionLeng inline'>找零<input type='checkbox' name='viewOpt[]' value='gimbalFindHome'></span>
				<span class='optionLeng inline'>复位<input type='checkbox' name='viewOpt[]' value='gimbalPark'></span>
				<span class='optionLeng inline'>停止<input type='checkbox' name='viewOpt[]' value='gimbalStop'></span>
				<span class='optionLeng inline'>急停<input type='checkbox' name='viewOpt[]' value='gimbalEmergStop'></span>
				<span class='optionLeng inline'>跟踪恒星<input type='checkbox' name='viewOpt[]' value='trackStar'></span>
				<span class='optionLeng inline'>设置目标名称<input type='checkbox' name='viewOpt[]' value='setObjecName'></span>
				<span class='optionLeng inline'>速度修正<input type='checkbox' name='viewOpt[]' value='speedM'></span>
				<span class='optionLeng inline'>恒速运动<input type='checkbox' name='viewOpt[]' value='speedE'></span>
				<span class='optionLeng inline'>位置修正<input type='checkbox' name='viewOpt[]' value='positionM'></span>
				<span class='optionLeng inline'>镜盖操作<input type='checkbox' name='viewOpt[]' value='coverOpr'></span>
				<span class='optionLeng inline'>保存同步数据<input type='checkbox' name='viewOpt[]' value='saveData'></span>
				<br><span class='optionTitle'>转台可变属性</span>
				<br>
				<span class='optionLeng inline'>时间戳<input type='checkbox' name='viewOpt[]' value='timeStamp'></span>
				<span class='optionLeng inline'>当前恒星时<input type='checkbox' name='viewOpt[]' value='siderealTime_1'></span>
				<span class='optionLeng inline'>当前时角<input type='checkbox' name='viewOpt[]' value='hourAngle'></span>
				<span class='optionLeng inline'>当前赤经<input type='checkbox' name='viewOpt[]' value='rightAscension'></span>
				<span class='optionLeng inline'>当前赤纬<input type='checkbox' name='viewOpt[]' value='declination'></span>
				<span class='optionLeng inline'>当前J2000赤经<input type='checkbox' name='viewOpt[]' value='J2000RightAscension'></span>
				<span class='optionLeng inline'>当前J2000赤纬<input type='checkbox' name='viewOpt[]' value='J2000Declination'></span>
				<span class='optionLeng inline'>当前方位<input type='checkbox' name='viewOpt[]' value='azimuth'></span>
				<span class='optionLeng inline'>当前俯仰<input type='checkbox' name='viewOpt[]' value='elevation'></span>
				<span class='optionLeng inline'>当前消旋位置<input type='checkbox' name='viewOpt[]' value='derotatorPosition'></span>
				<span class='optionLeng inline'>目标赤经<input type='checkbox' name='viewOpt[]' value='targetRightAscension'></span>
				<span class='optionLeng inline'>目标赤纬<input type='checkbox' name='viewOpt[]' value='targetDeclination'></span>
				<span class='optionLeng inline'>目标J2000赤经<input type='checkbox' name='viewOpt[]' value='targetJ2000RightAscension'></span>
				<span class='optionLeng inline'>目标J2000赤纬<input type='checkbox' name='viewOpt[]' value='targetJ2000Declination'></span>
				<span class='optionLeng inline'>目标方位<input type='checkbox' name='viewOpt[]' value='targetAzmiuth'></span>
				<span class='optionLeng inline'>目标俯仰<input type='checkbox' name='viewOpt[]' value='targetElevation'></span>
				<span class='optionLeng inline'>目标消旋位置<input type='checkbox' name='viewOpt[]' value='targetDerotatorPosition'></span>
				<span class='optionLeng inline'>轴1跟踪误差<input type='checkbox' name='viewOpt[]' value='axis1TrackError'></span>
				<span class='optionLeng inline'>轴2跟踪误差<input type='checkbox' name='viewOpt[]' value='axis2TrackError'></span>
				<span class='optionLeng inline'>轴3跟踪误差<input type='checkbox' name='viewOpt[]' value='axis2TrackError'></span>
				<span class='optionLeng inline'>焦点类型索引位置<input type='checkbox' name='viewOpt[]' value='focusTypeIndex'></span>
				<span class='optionLeng inline'>焦点切换镜角度位置<input type='checkbox' name='viewOpt[]' value='axis4Angle'></span>
				<span class='optionLeng inline'>镜盖索引位置<input type='checkbox' name='viewOpt[]' value='coverIndex'></span>
				<span class='optionLeng inline'>镜盖位置<input type='checkbox' name='viewOpt[]' value='coverPosition'></span>
				<span class='optionLeng inline'>焦点类型目标索引位置<input type='checkbox' name='viewOpt[]' value='targetFocusTypeIndex'></span>
				<span class='optionLeng inline'>焦点切换镜目标角度位置<input type='checkbox' name='viewOpt[]' value='targetAxis4Angle'></span>
				<span class='optionLeng inline'>镜盖目标索引位置<input type='checkbox' name='viewOpt[]' value='targetIndexOfCover'></span>
				<span class='optionLeng inline'>轴1速度<input type='checkbox' name='viewOpt[]' value='axis1Speed'></span>
				<span class='optionLeng inline'>轴2速度<input type='checkbox' name='viewOpt[]' value='axis2Speed'></span>
				<span class='optionLeng inline'>轴3速度<input type='checkbox' name='viewOpt[]' value='axis3Speed'></span>
				<span class='optionLeng inline'>跟踪目标类型<input type='checkbox' name='viewOpt[]' value='trackType'></span>
				<span class='optionLeng inline'>目标名称<input type='checkbox' name='viewOpt[]' value='trackObjectName'></span>
				<span class='optionLeng inline'>大气折射修正值<input type='checkbox' name='viewOpt[]' value='refractionCorrection'></span>
				<span class='optionLeng inline'>轴1编码器位置<input type='checkbox' name='viewOpt[]' value='axis1Encoder'></span>
				<span class='optionLeng inline'>轴2编码器位置<input type='checkbox' name='viewOpt[]' value='axis1Encoder'></span>
				<span class='optionLeng inline'>轴3编码器位置<input type='checkbox' name='viewOpt[]' value='axis1Encoder'></span>
				<span class='optionLeng inline'>轴3工作模式<input type='checkbox' name='viewOpt[]' value='axis3Mode'></span>
				<span class='optionLeng inline'>轴1指向模型修正值<input type='checkbox' name='viewOpt[]' value='axis1PMCorrection'></span>
				<span class='optionLeng inline'>轴2指向模型修正值<input type='checkbox' name='viewOpt[]' value='axis2PMCorrection'></span>
				<span class='optionLeng inline'>轴3指向模型修正值<input type='checkbox' name='viewOpt[]' value='axis3PMCorrection'></span>
				<span class='optionLeng inline'>轴1人工修正值<input type='checkbox' name='viewOpt[]' value='axis1ManualCorrection'></span>
				<span class='optionLeng inline'>轴2人工修正值<input type='checkbox' name='viewOpt[]' value='axis2ManualCorrection'></span>
				<span class='optionLeng inline'>轴3人工修正值<input type='checkbox' name='viewOpt[]' value='axis3ManualCorrection'></span>
				<span class='optionLeng inline'>盘向<input type='checkbox' name='viewOpt[]' value='pieSide'></span>
				<span class='optionLeng inline'>目标盘向<input type='checkbox' name='viewOpt[]' value='targetPieSide'></span>
				<span class='optionLeng inline'>轴1是否找零<input type='checkbox' name='viewOpt[]' value='isAxis1FindHome'></span>
				<span class='optionLeng inline'>轴2是否找零<input type='checkbox' name='viewOpt[]' value='isAxis2FindHome'></span>
				<span class='optionLeng inline'>轴3是否找零<input type='checkbox' name='viewOpt[]' value='isAxis3FindHome'></span>
				<span class='optionLeng inline'>温度<input type='checkbox' name='viewOpt[]' value='temperature'></span>
				<span class='optionLeng inline'>湿度<input type='checkbox' name='viewOpt[]' value='humidity'></span>
				<span class='optionLeng inline'>当前状态<input type='checkbox' name='viewOpt[]' value='gimbalCurStatus'></span>
				<span class='optionLeng inline'>历史状态<input type='checkbox' name='viewOpt[]' value='gimbalLastStatus'></span>
				<span class='optionLeng inline'>错误标识<input type='checkbox' name='viewOpt[]' value='gimbalErrorStatus'></span>
				<span class='optionLeng inline'>警告状态<input type='checkbox' name='viewOpt[]' value='WarningType'></span>
				<span class='optionLeng inline'>错误状态<input type='checkbox' name='viewOpt[]' value='ErrorType'></span>
				<br><span class='optionTitle'>转台固定属性</span>
				<br>
				<span class='optionLeng inline'>类型<input type='checkbox' name='viewOpt[]' value='atType'></span>
				<span class='optionLeng inline'>焦点类型<input type='checkbox' name='viewOpt[]' value='focustype'></span>
				<span class='optionLeng inline'>焦比<input type='checkbox' name='viewOpt[]' value='focusratio'></span>
				<span class='optionLeng inline'>焦距<input type='checkbox' name='viewOpt[]' value='focuslength'></span>
				<span class='optionLeng inline'>轴1最大速度<input type='checkbox' name='viewOpt[]' value='maxAxis1Speed'></span>
				<span class='optionLeng inline'>轴2最大速度<input type='checkbox' name='viewOpt[]' value='maxAxis2Speed'></span>
				<span class='optionLeng inline'>轴3最大速度<input type='checkbox' name='viewOpt[]' value='轴3最大速度'></span>
				<span class='optionLeng inline'>轴1最大加速度<input type='checkbox' name='viewOpt[]' value='maxAxis1Acceleration'></span>
				<span class='optionLeng inline'>轴2最大加速度<input type='checkbox' name='viewOpt[]' value='maxAxis2Acceleration'></span>
				<span class='optionLeng inline'>轴3最大加速度<input type='checkbox' name='viewOpt[]' value='maxAxis3Acceleration'></span>
				<span class='optionLeng inline'>轴1复位位置<input type='checkbox' name='viewOpt[]' value='axis1ParkPosition'></span>
				<span class='optionLeng inline'>轴2复位位置<input type='checkbox' name='viewOpt[]' value='axis2ParkPosition'></span>
				<span class='optionLeng inline'>轴3复位位置<input type='checkbox' name='viewOpt[]' value='axis3ParkPosition'></span>
				<span class='optionLeng inline'>是否有第3轴<input type='checkbox' name='viewOpt[]' value='haveAxis3'></span>
				<span class='optionLeng inline'>是否有镜盖（轴5）<input type='checkbox' name='viewOpt[]' value='haveAxis5'></span>
				<span class='optionLeng inline'>俯仰最低值<input type='checkbox' name='viewOpt[]' value='minElevation'></span>
				<span class='optionLeng inline'>温度传感器数目<input type='checkbox' name='viewOpt[]' value='numTemperatureSensor'></span>
				<span class='optionLeng inline'>湿度传感器数目<input type='checkbox' name='viewOpt[]' value='numHumiditySensor'></span>
				<span class='optionLeng inline'>连接<input type='checkbox' name='viewOpt[]' value='gimbalCanConnect'></span>
				<span class='optionLeng inline'>找零<input type='checkbox' name='viewOpt[]' value='canFindHome'></span>
				<span class='optionLeng inline'>跟踪恒星<input type='checkbox' name='viewOpt[]' value='canTrackStar'></span>
				<span class='optionLeng inline'>设置目标名称<input type='checkbox' name='viewOpt[]' value='canSetObjectName'></span>
				<span class='optionLeng inline'>指向固定位置<input type='checkbox' name='viewOpt[]' value='canSlewAzEl'></span>
				<span class='optionLeng inline'>轴3指向固定位置<input type='checkbox' name='viewOpt[]' value='canSlewDerotator'></span>
				<span class='optionLeng inline'>设置轴3工作模式<input type='checkbox' name='viewOpt[]' value='canConfigDerotator'></span>
				<span class='optionLeng inline'>停止<input type='checkbox' name='viewOpt[]' value='canStop'></span>
				<span class='optionLeng inline'>设置跟踪速度<input type='checkbox' name='viewOpt[]' value='canSetTrackSpeed'></span>
				<span class='optionLeng inline'>复位<input type='checkbox' name='viewOpt[]' value='canPark'></span>
				<span class='optionLeng inline'>恒速运动<input type='checkbox' name='viewOpt[]' value='canFixedMove'></span>
				<span class='optionLeng inline'>位置修正<input type='checkbox' name='viewOpt[]' value='canPositionCorrect'></span>
				<span class='optionLeng inline'>镜盖操作<input type='checkbox' name='viewOpt[]' value='canCoverOperation'></span>
				<span class='optionLeng inline'>焦点切换镜操作<input type='checkbox' name='viewOpt[]' value='canFocusOperation'></span>
				<span class='optionLeng inline'>急停<input type='checkbox' name='viewOpt[]' value='canEmergencyStop'></span>
				<span class='optionLeng inline'>保存同步数据<input type='checkbox' name='viewOpt[]' value='canSaveSyncData'></span>
				<span class='optionLeng inline'>跟踪卫星<input type='checkbox' name='viewOpt[]' value='canTrackSatellite'></span>
			</div><!--转台的选项 结束-->
			<div id='ccd' class='displayNo border mrgTop'><!--ccd选项-->
				<span class='optionTitle'>CCD指令</span><br>
				<span class='optionLeng inline'>连接CCD<input type='checkbox' name='viewOpt[]' value='ccdConnect'></span>
				<span class='optionLeng inline'>断开CCD<input type='checkbox' name='viewOpt[]' value='ccdDisConnect'></span>
				<span class='optionLeng inline'>停止曝光<input type='checkbox' name='viewOpt[]' value='ccdStopExpose'></span>
				<span class='optionLeng inline'>终止曝光<input type='checkbox' name='viewOpt[]' value='ccdAbortExpose'></span>
				<span class='optionLeng inline'>制冷温度<input type='checkbox' name='viewOpt[]' value='coolTemp'></span>
				<span class='optionLeng inline'>曝光策略<input type='checkbox' name='viewOpt[]' value='exposStratic'></span>
				<span class='optionLeng inline'>开始曝光<input type='checkbox' name='viewOpt[]' value='startExp'></span>
				<span class='optionLeng inline'>设置增益<input type='checkbox' name='viewOpt[]' value='setGear'></span>
				<span class='optionLeng inline'>读出速度模式<input type='checkbox' name='viewOpt[]' value='readOut'></span>
				<span class='optionLeng inline'>设置转移速度<input type='checkbox' name='viewOpt[]' value='transSpeed'></span>
				<span class='optionLeng inline'>设置Bin<input type='checkbox' name='viewOpt[]' value='setBin'></span>
				<span class='optionLeng inline'>设置Roi<input type='checkbox' name='viewOpt[]' value='setRoi'></span>
				<span class='optionLeng inline'>设置快门 <input type='checkbox' name='viewOpt[]' value='setShutter'></span>
				<span class='optionLeng inline'>设置帧转移<input type='checkbox' name='viewOpt[]' value='setFrame'></span>
				<span class='optionLeng inline'>设置SetEM<input type='checkbox' name='viewOpt[]' value='setEm'></span>
				<span class='optionLeng inline'>CMOS noise<input type='checkbox' name='viewOpt[]' value='setCmos'></span>
				<span class='optionLeng inline'>Base line<input type='checkbox' name='viewOpt[]' value='setBase'></span>
				<span class='optionLeng inline'>Over Scan<input type='checkbox' name='viewOpt[]' value='setOverScan'></span>
				<span class='optionTitle'>CCD可变属性</span><br>
				<span class='optionLeng inline'>时间戳<input type='checkbox' name='viewOpt[]' value='ccdIimeStamp'></span>
				<span class='optionLeng inline'>当前拍摄目标名称<input type='checkbox' name='viewOpt[]' value='curObserveName'></span>
				<span class='optionLeng inline'>当前拍摄目标类型<input type='checkbox' name='viewOpt[]' value='curObserveType'></span>
				<span class='optionLeng inline'>当前拍摄目标赤经<input type='checkbox' name='viewOpt[]' value='curObserveJ2000RightAscension'></span>
				<span class='optionLeng inline'>当前拍摄目标赤纬<input type='checkbox' name='viewOpt[]' value='curObserveJ2000Declination'></span>
				<span class='optionLeng inline'>当前拍摄目标历元<input type='checkbox' name='viewOpt[]' value='curObserveEpoch'></span>
				<span class='optionLeng inline'>当前拍摄波段<input type='checkbox' name='viewOpt[]' value='curObserveBand'></span>
				<span class='optionLeng inline'>当前状态<input type='checkbox' name='viewOpt[]' value='ccdCurStatus'></span>
				<span class='optionLeng inline'>历史状态<input type='checkbox' name='viewOpt[]' value='ccdLastStatus'></span>
				<span class='optionLeng inline'>错误标识<input type='checkbox' name='viewOpt[]' value='ccdErrorStatus'></span>
				<span class='optionLeng inline'>警告状态<input type='checkbox' name='viewOpt[]' value='ccdWarningType'></span>
				<span class='optionLeng inline'>错误状态<input type='checkbox' name='viewOpt[]' value='ccdErrorType'></span>
				<span class='optionLeng inline'>BinX<input type='checkbox' name='viewOpt[]' value='ccdBinX'></span>
				<span class='optionLeng inline'>BinY<input type='checkbox' name='viewOpt[]' value='ccdBinY'></span>
				<span class='optionLeng inline'>startX<input type='checkbox' name='viewOpt[]' value='ccdStartX'></span>
				<span class='optionLeng inline'>startY<input type='checkbox' name='viewOpt[]' value='ccdStartY'></span>
				<span class='optionLeng inline'>图像宽度<input type='checkbox' name='viewOpt[]' value='imageWidth'></span>
				<span class='optionLeng inline'>图像高度<input type='checkbox' name='viewOpt[]' value='imageHeight'></span>
				<span class='optionLeng inline'>曝光时间<input type='checkbox' name='viewOpt[]' value='exposureTime'></span>
				<span class='optionLeng inline'>延迟时间<input type='checkbox' name='viewOpt[]' value='delayTime'></span>
				<span class='optionLeng inline'>帧序号<input type='checkbox' name='viewOpt[]' value='frameSequence'></span>
				<span class='optionLeng inline'>总拍摄帧数<input type='checkbox' name='viewOpt[]' value='frameTotal'></span>
				<span class='optionLeng inline'>读出速度模式索引<input type='checkbox' name='viewOpt[]' value='indexofReadoutMode'></span>
				<span class='optionLeng inline'>转移速度模式索引<input type='checkbox' name='viewOpt[]' value='indexofTransferSpeedMode'></span>
				<span class='optionLeng inline'>增益模式索引<input type='checkbox' name='viewOpt[]' value='indexofGainMode'></span>
				<span class='optionLeng inline'>增益档位索引<input type='checkbox' name='viewOpt[]' value='indexofGain'></span>
				<span class='optionLeng inline'>当前制冷温度<input type='checkbox' name='viewOpt[]' value='curCoolerT'></span>
				<span class='optionLeng inline'>制冷温度设置值<input type='checkbox' name='viewOpt[]' value='targetCoolerT'></span>
				<span class='optionLeng inline'>上次曝光名称<input type='checkbox' name='viewOpt[]' value='lastTargetName'></span>
				<span class='optionLeng inline'>上次曝光名称<input type='checkbox' name='viewOpt[]' value='lastTargetName'></span>
				<span class='optionLeng inline'>上次曝光开始时刻<input type='checkbox' name='viewOpt[]' value='lastExposeStartTime'></span>
				<span class='optionLeng inline'>上次曝光时间<input type='checkbox' name='viewOpt[]' value='lastExposeDuration'></span>
				<span class='optionLeng inline'>读出模式<input type='checkbox' name='viewOpt[]' value='readoutMode'></span>
				<span class='optionLeng inline'>图像存储位数值<input type='checkbox' name='viewOpt[]' value='bytesofPixel'></span>
				<span class='optionLeng inline'>曝光完成百分比<input type='checkbox' name='viewOpt[]' value='exposurePercent'></span>
				<span class='optionLeng inline'>曝光触发模式值（索引）<input type='checkbox' name='viewOpt[]' value='exposeTriggerMode'></span>
				<span class='optionLeng inline'>风扇状态（索引）<input type='checkbox' name='viewOpt[]' value='fanOn'></span>
				<span class='optionLeng inline'>快门模式值（索引）<input type='checkbox' name='viewOpt[]' value='ShutterMode'></span>
				<span class='optionLeng inline'>帧转移<input type='checkbox' name='viewOpt[]' value='isFullFrame'></span>
				<span class='optionLeng inline'>EM<input type='checkbox' name='viewOpt[]' value='isEM'></span>
				<span class='optionLeng inline'>EM值<input type='checkbox' name='viewOpt[]' value='valueOfEM'></span>
				<span class='optionLeng inline'>CMOS noise filter<input type='checkbox' name='viewOpt[]' value='isNoiseFilter'></span>
				<span class='optionLeng inline'>base line<input type='checkbox' name='viewOpt[]' value='isBaseline'></span>
				<span class='optionLeng inline'>Base line值<input type='checkbox' name='viewOpt[]' value='vlaueOfBaseline'></span>
				<span class='optionLeng inline'>像元比例尺X<input type='checkbox' name='viewOpt[]' value='pixelScaleX'></span>
				<span class='optionLeng inline'>像元比例尺Y<input type='checkbox' name='viewOpt[]' value='pixelScaleY'></span>
				<br><span class='optionTitle'>CCD固定属性</span><br>
				<span class='optionLeng inline'>探测器类型<input type='checkbox' name='viewOpt[]' value='ccdType'></span>
				<span class='optionLeng inline'>x像素<input type='checkbox' name='viewOpt[]' value='xPixel'></span>
				<span class='optionLeng inline'>y像素<input type='checkbox' name='viewOpt[]' value='yPixel'></span>
				<span class='optionLeng inline'>x像元大小<input type='checkbox' name='viewOpt[]' value='xPixelSize'></span>
				<span class='optionLeng inline'>y像元大小<input type='checkbox' name='viewOpt[]' value='yPixelSize'></span>
				<span class='optionLeng inline'>y像元大小<input type='checkbox' name='viewOpt[]' value='yPixelSize'></span>
				<span class='optionLeng inline'>传感器名称<input type='checkbox' name='viewOpt[]' value='sensorName'></span>
				<span class='optionLeng inline'>图像位数<input type='checkbox' name='viewOpt[]' value='imageBits'></span>
				<span class='optionLeng inline'>制冷方式<input type='checkbox' name='viewOpt[]' value='coolerMode'></span>
				<span class='optionLeng inline'>制冷方式<input type='checkbox' name='viewOpt[]' value='coolerMode'></span>
				<span class='optionLeng inline'>最低制冷温度<input type='checkbox' name='viewOpt[]' value='lowCoolerT'></span>
				<span class='optionLeng inline'>最大曝光时间<input type='checkbox' name='viewOpt[]' value='maxExposureTime'></span>
				<span class='optionLeng inline'>最小曝光时间<input type='checkbox' name='viewOpt[]' value='minExposureTime'></span>
				<span class='optionLeng inline'>曝光时间分辨率<input type='checkbox' name='viewOpt[]' value='exposureTimeRation'></span>
				<span class='optionLeng inline'>满阱电荷<input type='checkbox' name='viewOpt[]' value='fullWellDepth'></span>
				<span class='optionLeng inline'>读出模式5<input type='checkbox' name='viewOpt[]' value='readoutMode5'></span>
				<span class='optionLeng inline'>读出速度模式4<input type='checkbox' name='viewOpt[]' value='readoutSpeed'></span>
				<span class='optionLeng inline'>转移速度模式4<input type='checkbox' name='viewOpt[]' value='transferSpeed'></span>
				<span class='optionLeng inline'>增益模式2<input type='checkbox' name='viewOpt[]' value='gainmode'></span>
				<span class='optionLeng inline'>增益档位4<input type='checkbox' name='viewOpt[]' value='gainNumber'></span>
				<span class='optionLeng inline'>增益值<input type='checkbox' name='viewOpt[]' value='gainValueArray'></span>
				<span class='optionLeng inline'>读出噪声值<input type='checkbox' name='viewOpt[]' value='readoutNoiseArray'></span>
				<span class='optionLeng inline'>快门类型<input type='checkbox' name='viewOpt[]' value='ShutterType'></span>
				<span class='optionLeng inline'>快门模式<input type='checkbox' name='viewOpt[]' value='ccdShutterMode'></span>
				<span class='optionLeng inline'>是否支持帧转移<input type='checkbox' name='viewOpt[]' value='IsSupportFullFrame'></span>
				<span class='optionLeng inline'>是否支持EM<input type='checkbox' name='viewOpt[]' value='IsSupportEM'></span>
				<span class='optionLeng inline'>支持CMOS noise filter<input type='checkbox' name='viewOpt[]' value='IsSupportsCmosNoiseFilter'></span>
				<span class='optionLeng inline'>支持 base line<input type='checkbox' name='viewOpt[]' value='IsSupportBaseLine'></span>
				<span class='optionLeng inline'>支持Over scan<input type='checkbox' name='viewOpt[]' value='IsSupportOverScan'></span>
				<span class='optionLeng inline'>BIN<input type='checkbox' name='viewOpt[]' value='binArray'></span>
				<span class='optionLeng inline'>支持开窗<input type='checkbox' name='viewOpt[]' value='IsSupportROI'></span>
				<span class='optionLeng inline'>接口类型<input type='checkbox' name='viewOpt[]' value='InterfaceType'></span>
				<span class='optionLeng inline'>曝光触发模式<input type='checkbox' name='viewOpt[]' value='ExposeTriggerMode'></span>
				<span class='optionLeng inline'>最大EM<input type='checkbox' name='viewOpt[]' value='EmMaxValue'></span>
				<span class='optionLeng inline'>最小EM<input type='checkbox' name='viewOpt[]' value='EmMinValue'></span>
				<span class='optionLeng inline'>最小EM<input type='checkbox' name='viewOpt[]' value='EmMinValue'></span>
				<span class='optionLeng inline'>连接<input type='checkbox' name='viewOpt[]' value='ccdCanConnect'></span>
				<span class='optionLeng inline'>设置制冷温度<input type='checkbox' name='viewOpt[]' value='canSetCoolerT'></span>
				<span class='optionLeng inline'>设置曝光策略<input type='checkbox' name='viewOpt[]' value='canSetExposureParam'></span>
				<span class='optionLeng inline'>开始曝光<input type='checkbox' name='viewOpt[]' value='canStartExposure'></span>
				<span class='optionLeng inline'>停止曝光<input type='checkbox' name='viewOpt[]' value='canStopExposure'></span>
				<span class='optionLeng inline'>终止曝光<input type='checkbox' name='viewOpt[]' value='canAbortExposure'></span>
				<span class='optionLeng inline'>设置增益<input type='checkbox' name='viewOpt[]' value='canSetGain'></span>
				<span class='optionLeng inline'>设置读出速度模式值<input type='checkbox' name='viewOpt[]' value='canSetReadoutSpeedMode'></span>
				<span class='optionLeng inline'>设置转移速度模式值<input type='checkbox' name='viewOpt[]' value='canSetTransferSpeedMode'></span>
				<span class='optionLeng inline'>设置BIN<input type='checkbox' name='viewOpt[]' value='canSetBin'></span>
				<span class='optionLeng inline'>设置ROI<input type='checkbox' name='viewOpt[]' value='canSetROI'></span>
				<span class='optionLeng inline'>设置快门<input type='checkbox' name='viewOpt[]' value='canSetShutter'></span>
				<span class='optionLeng inline'>设置帧转移<input type='checkbox' name='viewOpt[]' value='canSetFullFrame'></span>
				<span class='optionLeng inline'>设置EM<input type='checkbox' name='viewOpt[]' value='canSetEM'></span>
				<span class='optionLeng inline'>设置CMOS noise filter<input type='checkbox' name='viewOpt[]' value='canNoiseFilter'></span>
				<span class='optionLeng inline'>设置CMOS noise filter<input type='checkbox' name='viewOpt[]' value='canNoiseFilter'></span>
				<span class='optionLeng inline'>设置base line<input type='checkbox' name='viewOpt[]' value='canNoiseFilter'></span>
				<span class='optionLeng inline'>设置over scan<input type='checkbox' name='viewOpt[]' value='canSetOverScan'></span>
			</div><!--ccd选项 结束-->
			<div id='focus' class='displayNo border mrgTop'>
				<span class='optionTitle'>调焦器指令</span><br>
				<span class='optionLeng inline'>连接调焦器<input type='checkbox' name='viewOpt[]' value='focusConnect'></span>
				<span class='optionLeng inline'>断开调焦器<input type='checkbox' name='viewOpt[]' value='focusDisConnect'></span>
				<span class='optionLeng inline'>找零<input type='checkbox' name='viewOpt[]' value='focusFindhome'></span>
				<span class='optionLeng inline'>停止运动<input type='checkbox' name='viewOpt[]' value='focusStop'></span>
				<span class='optionLeng inline'>设置目标位置<input type='checkbox' name='viewOpt[]' value='focusTargetPos'></span>
				<span class='optionLeng inline'>设置恒速转动<input type='checkbox' name='viewOpt[]' value='focusFixedSpeed'></span>
				<span class='optionLeng inline'>使能温度补偿 <input type='checkbox' name='viewOpt[]' value='temperaEnable'></span>
				<span class='optionLeng inline'>温度补偿系数<input type='checkbox' name='viewOpt[]' value='temperaCoeficnt'></span>
				<br><span class='optionTitle'>调焦器可变属性</span><br>
				<span class='optionLeng inline'>时间戳<input type='checkbox' name='viewOpt[]' value='focusTimeStamp'></span>
				<span class='optionLeng inline'>当前状态<input type='checkbox' name='viewOpt[]' value='focusCurStatus'></span>
				<span class='optionLeng inline'>当前位置<input type='checkbox' name='viewOpt[]' value='focusCurPosition'></span>
				<span class='optionLeng inline'>目标位置<input type='checkbox' name='viewOpt[]' value='focusTargetPosition'></span>
				<span class='optionLeng inline'>找零状态<input type='checkbox' name='viewOpt[]' value='focusBeHomed'></span>
				<span class='optionLeng inline'>当前环境温度<input type='checkbox' name='viewOpt[]' value='focusTemperature'></span>
				<span class='optionLeng inline'>是否进行温度补偿<input type='checkbox' name='viewOpt[]' value='isTemperatureCompensate'></span>
				<span class='optionLeng inline'>温度补偿系数<input type='checkbox' name='viewOpt[]' value='tempertureCompensatecoefficient'></span>
				<span class='optionLeng inline'>错误标识<input type='checkbox' name='viewOpt[]' value='focusErrorStatus'></span>
				<br><span class='optionTitle'>调焦器固定属性</span><br>
				<span class='optionLeng inline'>最大值<input type='checkbox' name='viewOpt[]' value='focusMaxValue'></span>
				<span class='optionLeng inline'>最小值<input type='checkbox' name='viewOpt[]' value='focusMinValue'></span>
				<span class='optionLeng inline'>分辨率<input type='checkbox' name='viewOpt[]' value='focusIncrement'></span>
				<span class='optionLeng inline'>是否可找零<input type='checkbox' name='viewOpt[]' value='focusCanFindHome'></span>
				<span class='optionLeng inline'>可否温度补偿<input type='checkbox' name='viewOpt[]' value='canTempertureCompensate'></span>
				<span class='optionLeng inline'>最大速度<input type='checkbox' name='viewOpt[]' value='focusMaxSpeed'></span>
				<span class='optionLeng inline'>可否连接<input type='checkbox' name='viewOpt[]' value='focusCanConnect'></span>
				<span class='optionLeng inline'>可否设置目标位置<input type='checkbox' name='viewOpt[]' value='focusSetPosition'></span>
				<span class='optionLeng inline'>可否设置恒速运动<input type='checkbox' name='viewOpt[]' value='focusCanSetSpeed'></span>
				<span class='optionLeng inline'>可否设置恒速运动<input type='checkbox' name='viewOpt[]' value='focusCanSetSpeed'></span>
				<span class='optionLeng inline'>可否停止运动<input type='checkbox' name='viewOpt[]' value='focusCanStop'></span>
				<span class='optionLeng inline'>使能温度补偿<input type='checkbox' name='viewOpt[]' value='canEnableTempertureCompensate'></span>
				<span class='optionLeng inline'>可否设置温度补偿系数<input type='checkbox' name='viewOpt[]' value='canSetTempertCompensatecoefficient'></span>
				<span class='optionLeng inline'>可否找零<input type='checkbox' name='viewOpt[]' value='focusCanFindHome'></span>
			</div><!--调焦器选项 结束-->
			<div id='slaveDome' class='displayNo border mrgTop'>
				<span class='optionTitle'>随动圆顶指令</span><br>
				<span class='optionLeng inline'>连接圆顶<input type='checkbox' name='viewOpt[]' value='sDomeConnect'></span>
				<span class='optionLeng inline'>断开圆顶<input type='checkbox' name='viewOpt[]' value='sDomeDisConnect'></span>
				<span class='optionLeng inline'>停止运动<input type='checkbox' name='viewOpt[]' value='sDomeStop'></span>
				<span class='optionLeng inline'>打开天窗<input type='checkbox' name='viewOpt[]' value='sDomeScuttle'></span>
				<span class='optionLeng inline'>关闭天窗<input type='checkbox' name='viewOpt[]' value='sDomeScuttleClose'></span>
				<span class='optionLeng inline'>设置目标方位<input type='checkbox' name='viewOpt[]' value='sDomeSetPos'></span>
				<span class='optionLeng inline'>设置转动速度 <input type='checkbox' name='viewOpt[]' value='sDomeRotateSpeed'></span>
				<span class='optionLeng inline'>设置风帘位置 <input type='checkbox' name='viewOpt[]' value='sDomeShadePos'></span>
				<span class='optionLeng inline'>控制风帘运动 <input type='checkbox' name='viewOpt[]' value='sDomeShadeAct'></span>
				<br><span class='optionTitle'>随动圆顶可变属性</span><br>
				<span class='optionLeng inline'>当前状态<input type='checkbox' name='viewOpt[]' value='sDomeCurStatus'></span>
				<span class='optionLeng inline'>天窗状态<input type='checkbox' name='viewOpt[]' value='sDomeShutterStatus'></span>
				<span class='optionLeng inline'>风帘状态<input type='checkbox' name='viewOpt[]' value='sDomeShadeStatus'></span>
				<span class='optionLeng inline'>当前方位<input type='checkbox' name='viewOpt[]' value='curDomePosition'></span>
				<span class='optionLeng inline'>天窗打开百分比<input type='checkbox' name='viewOpt[]' value='curShutterPosition'></span>
				<span class='optionLeng inline'>风帘位置<input type='checkbox' name='viewOpt[]' value='curShadePosition'></span>
				<span class='optionLeng inline'>目标方位<input type='checkbox' name='viewOpt[]' value='targetDomePosition'></span>
				<span class='optionLeng inline'>目标天窗状态<input type='checkbox' name='viewOpt[]' value='targetShutterPosition'></span>
				<span class='optionLeng inline'>目标风帘位置<input type='checkbox' name='viewOpt[]' value='targetShadePosition'></span>
				<span class='optionLeng inline'>错误标识<input type='checkbox' name='viewOpt[]' value='sDomeErrorStatus'></span>
				<br><span class='optionTitle'>随动圆顶固定属性</span><br>
				<span class='optionLeng inline'>是否具备风帘<input type='checkbox' name='viewOpt[]' value='HasShade'></span>
				<span class='optionLeng inline'>最大转动速度<input type='checkbox' name='viewOpt[]' value='sDomeMaxSpeed'></span>
				<span class='optionLeng inline'>可否设置目标方位<input type='checkbox' name='viewOpt[]' value='canSetDomePositin'></span>
				<span class='optionLeng inline'>可否设置风帘位置<input type='checkbox' name='viewOpt[]' value='canSetShadePosition'></span>
				<span class='optionLeng inline'>可否设置转动速度<input type='checkbox' name='viewOpt[]' value='canSetRotateSpeed'></span>
				<span class='optionLeng inline'>可否停止运动<input type='checkbox' name='viewOpt[]' value='sDomeCanStop'></span>
				<span class='optionLeng inline'>可否打开天窗<input type='checkbox' name='viewOpt[]' value='canOpenShutter'></span>
				<span class='optionLeng inline'>可否控制风帘运动<input type='checkbox' name='viewOpt[]' value='canSetShadeSpeed'></span>
				<span class='optionLeng inline'>可否连接<input type='checkbox' name='viewOpt[]' value='sDomeCanConnect'></span>
			</div><!--随动圆顶 选项 结束-->
			<div id='filter' class='displayNo border mrgTop'>
				<span class='optionTitle'>滤光片指令</span><br>
				<span class='optionLeng inline'>连接<input type='checkbox' name='viewOpt[]' value='filterConnect'></span>
				<span class='optionLeng inline'>断开<input type='checkbox' name='viewOpt[]' value='filterDisConnect'></span>
				<span class='optionLeng inline'>找零<input type='checkbox' name='viewOpt[]' value='filterFindHome'></span>
				<span class='optionLeng inline'>设置滤光片位置<input type='checkbox' name='viewOpt[]' value='setFilterPosition'></span>
				<br><span class='optionTitle'>滤光片可变属性</span><br>
				<span class='optionLeng inline'>当前状态<input type='checkbox' name='viewOpt[]' value='filterCurStatus'></span>
				<span class='optionLeng inline'>时间戳<input type='checkbox' name='viewOpt[]' value='filterTimeStamp'></span>
				<span class='optionLeng inline'>当前插槽位置索引<input type='checkbox' name='viewOpt[]' value='curFilterPosition'></span>
				<span class='optionLeng inline'>目标插槽位置<input type='checkbox' name='viewOpt[]' value='targetFilterPosition'></span>
				<span class='optionLeng inline'>可否找零<input type='checkbox' name='viewOpt[]' value='filterBeHomed'></span>
				<span class='optionLeng inline'>错误标识<input type='checkbox' name='viewOpt[]' value='filterErrorStatus'></span>
				<span class='optionLeng inline'>错误类型<input type='checkbox' name='viewOpt[]' value='filterErrorType'></span>
				<br><span class='optionTitle'>滤光片固定属性</span><br>
				<span class='optionLeng inline'>插槽数目<input type='checkbox' name='viewOpt[]' value='numberOfFilter'></span>
				<span class='optionLeng inline'>滤光片类型<input type='checkbox' name='viewOpt[]' value='FilterSystem'></span>
				<span class='optionLeng inline'>滤光片名称<input type='checkbox' name='viewOpt[]' value='FilterName'></span>
				<span class='optionLeng inline'>滤光片焦距偏差值<input type='checkbox' name='viewOpt[]' value='FilterFocusLengthCompensate'></span>
				<span class='optionLeng inline'>形状<input type='checkbox' name='viewOpt[]' value='FilterShape'></span>
				<span class='optionLeng inline'>可否设置滤光片位置<input type='checkbox' name='viewOpt[]' value='canSetFilterPosition'></span>
				<span class='optionLeng inline'>支持连接操作<input type='checkbox' name='viewOpt[]' value='filterCanConnect'></span>
				<span class='optionLeng inline'>支持找零操作<input type='checkbox' name='viewOpt[]' value='filterCanFindHome'></span>
			</div>
		</form><!--60cm各子设备 指令属性选项结束 -->
		<br>
		<div class='center' style='margin-bottom:30px;'>
			<button id='at60ConfigBtn' style='font-size:18px;'>提交设置</button>
		</div>
    </div><!-- id='all' 结束 --><br>
	<footer class='center clear'>
		<span>中科院国家天文台兴隆观测基地</span>
	</footer>
</body>
</html>