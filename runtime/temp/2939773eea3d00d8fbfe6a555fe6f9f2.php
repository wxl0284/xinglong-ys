<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:73:"D:\xampp\htdocs\demo\public/../application/xinglong\view\at60\at60_1.html";i:1505573128;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <title>60CM望远镜</title>
     <link rel="stylesheet" type="text/css" href="/static/css/common-1.css" />
     <link rel="stylesheet" type="text/css" href="/static/css/at60_1.css" />
     <link rel="stylesheet" type="text/css" href="/static/plugin/easyui-1.3.5/themes/default/easyui.css" />
     <link rel="stylesheet" type="text/css" href="/static/plugin/easyui-1.3.5/themes/icon.css" />
     <script type="text/javascript" src="/static/plugin/easyui-1.3.5/jquery.min.js"></script>
     <script type="text/javascript" src="/static/plugin/easyui-1.3.5/jquery.easyui.min.js"></script>
     <script type="text/javascript" src="/static/plugin/easyui-1.3.5/locale/easyui-lang-zh_CN.js"></script>
     <script type="text/javascript" src="/static/js/at60.js"></script>
	 <script type="text/javascript" src="/static/plugin/easyui-1.3.5/datagrid-dnd.js"></script>
</head>
<body>
	<header class='pos_r'>
	   <div class='pos_r'>
	      <img class='pos_a' src='/static/images-0/logo1.png'/>
		  <div id='atList' class='pos_a'>
				望远镜列表
				<ul id='atListUl' class='center displayNo'>
					<li id='at60'>60CM</li>
					<hr>
					<li id='at80'>80CM</li>
					<hr>
					<li>85CM</li>
					<hr>
					<li>85CM</li>
				</ul>
		   </div>
		  <!-- <a href="<?php echo url('/xinglong/result'); ?>" class='pos_r' id='res'>观测结果</a> -->
		   <p class='pos_a'>
				<a href="<?php echo url('xinglong/control/front'); ?>">首页&nbsp;&nbsp;&nbsp;&nbsp;
				</a>
				<?php if(\think\Request::instance()->cookie('role') == 1): ?>
				<a href='/xinglong/user'>用户管理&nbsp;&nbsp;&nbsp;&nbsp;</a>
				<?php endif; ?>
				<a href='/xinglong/user/passwd'>修改密码&nbsp;&nbsp;&nbsp;&nbsp;
				</a>
				<span>欢迎!&nbsp;&nbsp;<?php echo (\think\Request::instance()->cookie('login')) ? \think\Request::instance()->cookie('login') :  ''; ?>&nbsp;
				</span>
		   </p>
		   <div class='pos_a'><a href='/xinglong/control/logout'>&nbsp;&nbsp;&nbsp;退出&nbsp;&nbsp;&nbsp;</a></div> 
	    </div>
	</header>
    <div id='all'>
        <div class='pos_r'><!--重要信息区域 开始-->
			<div id='atPic'>
                <img src='/static/images-1/location_1.png'/>
                <img src='/static/images-0/location.png'/>
				<a class='pos_a border1 more inline center' id='takeOverBtn'>接管</a>
            </div>
			<div id='panel' class='displayNo pos_a'><!--接管弹窗-->
			   <div id='takeOverPanel'>
			    <table class='center_mrg'>
					<tr class='center'><td colspan='2' style='color:red'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;望远镜未占用</td>
					</tr>
					<tr class='text_r'>
					  <td>权限</td>
					  <td><select name="role" style="width:150px">
							<option value="role1">抢占</option>
							<option value="role2">禁止抢占</option>
						  </select>
					  </td>
					</tr>
					<tr class='text_r'>
					  <td>接管起始时间</td>
					  <td><input class="easyui-datetimebox" editable=false style="width:150px">
					  </td>
					</tr>
					<tr class='text_r'>
					  <td>接管结束时间</td>
					  <td><input class="easyui-datetimebox" editable=false style="width:150px">
					  </td>
					</tr>
					<tr class='center'>
					  <td colspan='2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='border1 more inline center' id='doTakeOver'>接管望远镜</a>&nbsp;&nbsp;&nbsp;&nbsp;
					  <a class='border1 more inline center' id='noTakeOver'>放弃接管</a></td>
					</tr>
			    </table>
			 </div>
		   </div><!--接管弹窗 结束-->
			<div id='infos' class='border pos_a'>
				<table id='time' class='center'>
					<tr>
						<td>日期</td>
						<td>北京时</td>
						<td>历元</td>
						<td>恒星时</td>
						<td>晨光始</td>
						<td>昏影终</td>
					</tr>
					<tr class='tdC'>
						<td id='date'></td>
						<td id='bjTime'></td>
						<td>J2000</td>
						<td id='siderealTime'></td>
						<td>2017.7.11</td>
						<td>2017.7.11</td>
					</tr>
				</table>
				<br>
				<hr style='width:98%;' class='center_mrg'><br>
				<table id='time_1' class='center_mrg '>
					<tr>
					  <td>当前状态:<span id='curstatus' style='color:red;'></span>
					  </td>
					  <td>跟踪误差:<span id='trackError'></span>
					  </td>
					  <td>当前时角:<span id='hourAngle'></span>
					  </td>
                    </tr>
                    <tr>
					  <td>镜盖状态:<span id='coverStatus'></span>
					  </td>
					  <td>当前赤经:<span id='rightAscension'></span>
					  </td>
					  <td>当前赤纬:<span id='declination'></span>
					  </td>
                    </tr>
                    <tr>
					  <td>目标名:<span id='trackObjectName'></span>
					  </td>
					  <td>目标类型:<span id='trackType'></span>
					  </td>
					  <td>目标赤经:<span id='targetRightAscension'></span>
					  </td>
                    </tr>
                    <tr>
					  <td>目标赤纬:<span id='targetDeclination'></span>
					  </td>
					  <td>当前方位:<span id='azmiuth'></span>
					  </td>
					  <td>当前俯仰:<span id='elevation'></span>
					  </td>
                    </tr>
					<tr>
					  <td>赤经速度:<span id='RightAscensionSpeed'></span>
					  </td>
					  <td>赤纬速度:<span id='declinationSpeed'></span>
					  </td>
					  <td>轴1跟踪误差:<span id='axis1TrackError'></span>
					  </td>
					  <!-- <td>当前消旋位置:<span id='derotatorPositon'></span>
					  </td> -->
                    </tr>
					<tr>
					  <!-- <td>目标消旋位置:<span id='targetDerotatorPosition'></span>
					  </td> -->
					  <td>轴2跟踪误差:<span id='axis2TrackError'></span>
					  </td> 
					</tr>
					<!-- <tr>
					  <td>轴3跟踪误差:<span id='axis3TrackError'></span>
					  </td>
					</tr>			 -->
				</table>
			</div>
		</div><!--重要信息区域 结束-->
        <div id='devsInfo' class='pos_r center'>
            <div class='childDev'>
               <img src='/static/images-1/focus.png' width='100px;'/>
				<br />调焦位置
            </div>
            <div class='childDev'>
                <img src='/static/images-1/filter.png' width='100px;'/>
				<br />滤光片位置
            </div>
            <div class='childDev'>
                <img src='/static/images-1/ccdTemp.png' width='100px;' /><br />CCD温度
            </div>
            <div class='childDev'>
                <img src='/static/images-1/ccdExpo.png' width='100px;' /><br />CCD曝光率(%)
            </div>
            <div class='childDev'>
				<img src='/static/images-1/ccdExpo.png' width='100px;' />
                <br />设备状态信息
            </div>
			<img class='center_mrg' src='/static/images-1/line.png' width='970px;' style='display:block;'/>
			<div class='pos_a' style='left:47px;top:168px;width:150px;'>
				<!-- <img src='/static/images-1/zt.png' > -->
				<img id='gimbPic' src='/static/images-1/ok.jpg' >
				<p id='gimbStatus'></p>
			</div>
			<div class='pos_a' style='left:287px;top:168px;width:150px;'>
				<!-- <img src='/static/images-1/ccd.png' > -->
				<img src='/static/images-1/ok.jpg' >
				<p id='ccdStatus'></p>
			</div>
			<div class='pos_a' style='left:526px;top:168px;width:150px;'>
				<!-- <img src='/static/images-1/tjq.png' > -->
				<img src='/static/images-1/ok.jpg' ><p id='focusStatus' style="color:#ccc;"></p>
			</div>
			<div class='pos_a' style='left:772px;top:168px;width:150px;'>
				<!-- <img src='/static/images-1/yd.png' > -->
				<img src='/static/images-1/ok.jpg' ><p id='domeStatus'></p>
			</div>
			<div class='pos_a' style='left:1021px;top:168px;width:128px;'>
				<!-- <img src='/static/images-1/lgp.png' > -->
				<img src='/static/images-1/ok.jpg' ><p id='filterStatus'></p>
			</div>
        </div><!--重要信息区域 结束-->
		<br ><br>
        <div id='devsNav'><!--望远镜子设备导航:js用code/public/static/js/dev1.js-->
            <table>
                <tr>
                  <td><a name='planInfo'>观测计划</a></td><td><a name='at60Res'>观测图像</a></td><td><a name='gimbalInfo' class='borderBtm'>转台</a></td><td><a name='ccdInfo'>CCD</a></td><td><a name='focusInfo'>调焦器</a></td><td><a name='domeInfo'>圆顶</a></td><td><a name='filterInfo'>滤光片</a></td><td><a>数据处理</a></td><td><a>光谱终端</a></td><td><a>导星</a></td>
                </tr>
            </table>
			<hr style='height:3px;border:none;border-top:3px solid #ccc;'/>
        </div><!--望远镜子设备导航结束-->
          <div id='gimbalInfo' class='display'><!--转台指令和属性-->
             <nav class='center'><!--转台指令 导航-->
			    <span name='trackStar'>跟踪恒星</span>	<span name='targetName'>设置目标名称</span>
				<!--<span name='poitFix'>指向固定位置</span>-->  <!-- <span name='slewDerotator'>轴3指向固定位置</span> -->
				<!-- <span name='axis3Mode'>轴3工作模式</span>-->  <span name='speedX'>速度修正</span> 
				<span name='speedF'>恒速运动</span>  <span name='positionX'>位置修正</span>
				<span name='coverOp'>镜盖操作</span>  <!--<span name='focusOp'>焦点切换镜</span>-->
				<span name='saveData'>保存同步数据</span>  <!--<span name='trackS'>跟踪恒星</span>	<span name='PropCfg'>属性设置</span>-->
			 </nav>
			 <div class='border2'><!--指令和可变属性-->
				<div id='btnsGimbal' class='center_mrg center BtnsDiv'>
					<input id='gimbalConnect' class='border1 btnCommand' type='button' value='连接望远镜' />
					<input id='gimbalDisConnect' class='border1 btnCommand' type='button' value='断开望远镜' />
                   <input id='gimbalFindhome' class='border1 btnCommand' type='button' value='找  零' style='color:#ccc;border-color:#ccc;cursor:not-allowed;' disabled='disabled'/>
                   <input id='gimbalPark' class='border1 btnCommand' type='button' value='复 位' />              
                   <input id='gimbalStop' class='border1 btnCommand' type='button' value='停 止' />				   
                   <input id='gimbalEmergenceStop' class='border1 btnCommand' type='button' value='急 停' />
				</div><br>
				<hr style='width:98%;' class='center_mrg'><br>
				<form id='at60Gimbal' class='center command'>
					<div id='trackStar' class="pos_r">
					   <input type='radio' name='command' value='1' />
					   赤经：<input id='inputIn1' type="text" name='rightAscension' />
					   赤纬：<input id='inputIn2' type="text" name='declination' />
					  历元：<select name="epoch" style='width:69px;'>
						       <option value="0">real</option>
							   <option value="1">J2000</option>
							   <option value="2">B1950</option>
							   <option value="3">J2050</option>
						    </select>
					  跟踪类型：
					   <select name="speed" style='width:66px;'>
						   <option value="0">恒星</option>
						   <option value="1">太阳</option>
						   <option value="2">月亮</option>
						   <option value="3">彗星</option>
						</select>
						<!--上面大输入框的内嵌小输入框-->
						<span id='inputIn1_1' style='display:block;left:290px;top:0px;' class="pos_a input_in">
						  <input id='inputIn1_1_1' type="text" class='keyup'>
						  :<input type="text" class='keyup'>
						  :<input id='inputIn1_1_3' type="text" style="width:38px;">
						</span>
						<span id='inputIn2_1' style='display:block;left:510px;top:0px;' class="pos_a input_in">
							<input id='inputIn2_1_1' type="text" class='keyup'>
							:<input type="text" class='keyup'>
							:<input id='inputIn2_1_3' type="text" style="width:38px;">
						</span>
						<!--上面大输入框的内嵌小输入框-->
					</div>
					<div id='targetName'>
					   <input type='radio' name='command' value='2' />
					   目标名称：<input type="text" name='objectName' />
					  目标类型：<select name="objectType" style='width:81px;'>
						           <option value="0">恒星</option>
								   <option value="1">太阳</option>
								   <option value="2">月亮</option>
								   <option value="3">彗星</option>
								   <option value="4">行星</option>
								   <option value="5">卫星</option>
								   <option value="6">固定位置</option>
						    </select>
					  </div>
					<div id='poitFix'>
						   <input type='radio' name='command' value='3' />
						   方位：<input type="text" name='azimuth' />
						   俯仰：<input type="text" name='elevation' />
					</div>
					<div id='slewDerotator'>
					   <input type='radio' name='command' value='4' />轴3指向固定位置：
					   <input type="text" name='slewDerotator' />
					</div>
					<div id='axis3Mode'>
					   <input type='radio' name='command' value='5' />
					   模式：<select id='at60Axis3' name="mode" style='width:129px;'> 
						           <option value="0">不消旋</option>
								   <option value="1">偏差消旋</option>
								   <option value="2">始终指向同一角度</option>
						    </select>
					   <span class='displayNo'>起偏角：<input type="text" name='polarizingAngle' /></span>
					</div>
					<div id='speedX'>
                           <input type='radio' name='command' value='6' />
                           轴：<input type="text" name='axis' />
                           速度：<input type="text" name='correction' />
					</div>
					<div id='speedF'>
                           <input type='radio' name='command' value='7' />
                           轴：<input type="text" name='FixedMoveAxis' />
                           速度：<input type="text" name='FixedMoveSpeed' />
					</div>
					<div id='positionX'>
                           <input type='radio' name='command' value='8' />
                           轴：<input type="text" name='PositionCorrectAxis' />
                           修正值：<input type="text" name='PositionCorrectVal' />
					</div>
					<div id='coverOp'>
                           <input type='radio' name='command' value='9' />镜盖操作：
						   <select name="openCover" style='width:56px;'>
						           <option value="1">开</option>
								   <option value="2">关</option>
								   <option value="0">停</option>
						    </select>                        
					</div>
					<div id='focusOp'>
                           <input type='radio' name='command' value='10' />焦点切换镜：
						   <select name="setFocusType" style='width:56px;'>
						        <option value="1">1</option>
							    <option value="2">2</option>
							    <option value="3">3</option>
							    <option value="-1">停</option>
						    </select>
                          
					</div>
					<div id='saveData'>
                           <input type='radio' name='command' value='11' />保存同步数据：
						   <input type="text" name='saveSyncData' />
					</div>
					<div id='trackS'>
                           <input type='radio' name='command' value='12' />跟踪恒星：
						   <input type="text" name='trackStar' />
					</div>
					<div id='PropCfg'>
                           <input type='radio' name='command' value='13' />属性设置：
						   <input type="text" name='configProp' />
					</div>
					<br><input id='gimbalSbmt' class='border submitBtn' type="button" value="确认提交指令">
				</form>
			  <br><hr style='width:98%;' class='center_mrg'><br>
			  <div><!--转台可变属性-->
                <span class='propT'>转台可变属性:</span><br><br>
                <ul class='properUl'>
                    <li class='li fl'>时间戳:<span id='stamp'></span></li>
                    <li class='li fl'>当前恒星时:<span id='siderealTime_1'></span></li>
                    <li class='li fl'>当前时角:<span id='hourAngle_1'></span></li>
                    <li class='li fl'>当前赤经:<span id='rightAscension_1'></span></li>
                    <li class='li fl'>当前赤纬:<span id='declination_1'></span></li>
                    <li class='li fl'>当前J2000赤经:<span id='J2000RightAscension'></span></li>
                    <li class='li fl'>当前J2000赤纬:<span id='J2000Declination'></span> </li>
                    <li class='li fl'>当前方位:<span id='azmiuth_1'></span></li>
                    <li class='li fl'>当前俯仰:<span id='elevation_1'></span></li>
                    <!-- <li class='li fl'>当前消旋位置:<span id='derotatorPositon_1'></span></li> -->
                    <li class='li fl'>目标赤经:<span id='targetRightAscension_1'></span></li>
                    <li class='li fl'>目标赤纬:<span id='targetDeclination_1'></span></li>
                    <li class='li fl'>目标J2000赤经:<span id='targetJ2000RightAscension'></span></li>
                    <li class='li fl'> 目标J2000赤纬:<span id='targetJ2000Declination'></span></li>
                    <!-- <li class='li fl'>目标消旋位置:<span id='targetDerotatorPosition_1'></span></li> -->
                </ul><div class='clear'></div>
             </div><!--转台 可变属性结束-->
		  </div><!--指令和可变属性 结束-->
		 <div class='border2'><!--转台固定属性-->
			<br><span class='propT'>转台固定属性:</span><br><br>
                <ul class='properUl'>
                    <li class='li fl'>经度:<span>12345678</span></li>
                    <li class='li fl'>  纬度:<span>12345678</span></li>
                    <li class='li fl'>  口径:<span>12345678</span> </li>
                    <li class='li fl'> 类型:<span>12345678</span></li>
                    <li class='li fl'> 焦点类型:<span>12345678</span></li>
                    <li class='li fl'>焦比:<span>12345678</span></li>
                    <li class='li fl'>焦距:<span>12345678</span></li>
                    <li class='li fl'>轴1最大速度:<span>12345678</span></li>
                    <li class='li fl'> 轴2最大速度:<span>12345678</span></li>
                    <li class='li fl'> 轴3最大速度:<span>12345678</span></li>
					<li class='li fl'>轴1最大加速度:<span>12345678</span></li>
                    <li class='li fl'> 轴2最大加速度:<span>12345678</span></li>
                    <li class='li fl'> 轴3最大加速度:<span>12345678</span></li>
                    <li class='li fl'> 轴1复位位置:<span>12345678</span></li>
                    <li class='li fl'> 轴2复位位置:<span>12345678</span></li>
                    <li class='li fl'> 轴3复位位置:<span>12345678</span></li>
                    <li class='li fl'> 是否有第3轴:<span>12345678</span></li>
                    <li class='li fl'> 是否有镜盖:<span>12345678</span></li>
                    <li class='li fl'> 是否有镜盖:<span>12345678</span></li>
                    <li class='li fl'> 恒速运动:<span>12345678</span></li>
                </ul><div class='clear'></div>
		 </div><!--固定属性 结束-->
       </div><!--转台 结束-->
	   <div id='ccdInfo' class='displayNo'><!--ccd 指令和属性-->
			<nav class='center'><!--ccd指令 导航-->
			    <span name='coolTemp'>制冷温度</span>	<span name='exposStratic'>曝光策略</span>
				<span name='startExp'>开始曝光</span>  <span name='setGear'>设置增益</span>
				<span name='readOut'>读出速度模式</span>  <span name='transSpeed'>设置转移速度</span>
				<span name='setBin'>设置Bin</span>  <span name='setRoi'>设置Roi</span>
				<span name='setShutter'>设置快门</span>  <span name='setFrame'>设置帧转移</span>
				<span name='setEm'>设置SetEM</span>  <span name='setCmos'>CMOS noise</span>	<span name='setBase'>Base line</span> <span name='setOverScan'>Over Scan</span>
			</nav>
			<div class='border2'><!--ccd 指令和可变属性-->
				<div id='btnsCCD' class='center_mrg center BtnsDiv'>
					<input id='ccdConnect' class='border1 btnCommand' type='button' value='连接CCD' />
					<input id='ccdDisConnect' class='border1 btnCommand' type='button' value='断开CCD' />
					<input id='ccdStopExpose' class='border1 btnCommand' type='button' value='停止曝光' />
					<input id='ccdAbortExpose' class='border1 btnCommand' type='button' value='终止曝光' />
				</div><br>
				<hr style='width:98%;' class='center_mrg'><br>
				<form id='at60Ccd' class='center command'>
					<div id='coolTemp'>
					   <input type='radio' name='command' value='1' />设置制冷温度：
					   <input type="text" name='temperature' placeholder=''/>
					</div>
					<div id='exposStratic' class="pos_r"><!--ccd 曝光策略开始-->
					   <input type='radio' name='command' value='2' />
					   <span>数据有效标志位：<input type="text" name='validFlag' placeholder='整数'/></span>
					   <span>起始时刻：<input type="text" name='startTime' placeholder='整数' /></span>
					   <span>曝光时间：<input type="text" name='duration' placeholder='整数' /></span>
					   <span>延迟时间：<input type="text" name='delay' placeholder='整数' /></span>
					   <span>拍摄目标名称：<input type="text" name='objectName' placeholder='数字字母' /></span>
					   <span>拍摄目标类型：<select name="objectType" style='width:75px;'>
										   <option value="0">恒星</option>
										   <option value="2">月亮</option>
										   <option value="3">彗星</option>
										   <option value="4">行星</option>
										   <option value="5">卫星</option>
										   <option value="6">固定位置</option>
										   <option value="7">本底</option>
										   <option value="8">暗流</option>
										   <option value="9">平场</option>
									 </select></span>
						<span>拍摄目标赤经：<input id='inputIn3' type="text" name='objectRightAscension' /></span>
						<span>拍摄目标赤纬：<input id='inputIn4' type="text" name='objectDeclination' /></span>
						<span>拍摄目标历元：<select name="objectEpoch" style='width:69px;'>
									   <option value="0">real</option>
									   <option value="1">J2000</option>
									   <option value="2">B1950</option>
									   <option value="3">J2050</option>
									</select></span>
						<span>当前拍摄波段：<input type="text" name='objectBand' placeholder='数字字母' /></span>
						<span>拍摄波段滤光片系统：<select name="objectFilter" style='width:110px;'>
									   <option value="0">Johnshon-Bessel</option>
									   <option value="1">Sloan</option>
									   <option value="2">Strömgrem</option>
									   <option value="3">其它</option>
									</select></span>
						<span>是否保存图像：<select name="isSaveImage" style='width:69px;'>
									   <option value="1">是</option>
									   <option value="2">否</option>
									</select></span>
						<span>气象数据采集时间：<input type="text" name='weatherGatherTime' placeholder='整数' /></span>
						<span>温度：<input type="text" name='temperature1' placeholder='小数' /></span>
						<span>湿度：<input type="text" name='humidity' placeholder='小数' /></span>
						<span>风速：<input type="text" name='windSpeed' placeholder='小数' /></span>
						<span>气压：<input type="text" name='pressure' placeholder='小数' /></span>
						<span>天气状态采集时间：<input type="text" name='skyGatherTime' placeholder='整数' /></span>
						<span>天气状态：<input type="text" name='skyState' placeholder='整数' /></span>
						<span>云量：<input type="text"  name='clouds' placeholder='整数' /></span>
						<span>视宁度采集时间：<input type="text" name='seeingGatherTime' placeholder='整数' /></span>
						<span>视宁度：<input type="text" name='seeing' placeholder='小数' /></span>
						<span>粉尘采集时间：<input type="text" name='dustGatherTime' placeholder='小数' /></span>
						<span>粉尘：<input type="text" name='dust' placeholder='小数' /></span>
						<span>AMS：<input type="text" name='AMS' placeholder='小数' /></span>
						<span>消光系数采集时间：<input type="text" name='extinctionGatherTime' placeholder='整数' /></span>
						<span>赤经：<input type="text" name='rightAscension' placeholder='小数' /></span>
						<span>赤纬：<input type="text" name='declination' placeholder='小数' /></span>
						<span>波段：<input type="text" name='band' placeholder='字母数字' /></span>
						<span>消光系数1：<input type="text" name='extinctionFactor1' placeholder='小数' /></span>
						<span>消光系数2：<input type="text" name='extinctionFactor2' placeholder='小数' /></span>
						<span>消光系数3：<input type="text" name='extinctionFactor3' placeholder='小数' /></span>
						<span>望远镜赤经：<input type="text" name='telescopeRightAscension' placeholder='小数' /></span>
						<span>望远镜赤纬：<input type="text" name='telescopeDeclination' placeholder='小数' /></span>
						<span>焦距：<input type="text" name='focusLength' placeholder='小数' /></span>
						<span>帧数：<input type="text" name='frameNum' placeholder='整数' /></span>
						<!--上面大输入框的内嵌小输入框-->
						<span id='inputIn3_1' style='display:block;left:90px;top:64px;' class="pos_a input_in">
							<input id='inputIn3_1_1' type="text" class='keyup'>
							:<input type="text" class='keyup'>
							:<input id='inputIn3_1_3' type="text" style="width:38px;">
						</span>
						<span id='inputIn4_1' style='display:block;left:403px;top:64px;' class="pos_a input_in">
							<input id='inputIn4_1_1' type="text" class='keyup'>
							:<input type="text" class='keyup'>
							:<input id='inputIn4_1_3' type="text" style="width:38px;">
						</span>
						<!--上面大输入框的内嵌小输入框-->	
					  </div><!--ccd 曝光策略结束-->
					  <div id='startExp'>
					   <input type='radio' name='command' value='3' />
					   是否读取帧序号：<input type="text" name='isReadFrameSeq' />
					   帧序号：<input type="text" name='frameSequence' />
					  </div>
					  <div id='setGear'>
					   <input type='radio' name='command' value='4' />
					   增益模式：<input type="text" name='mode' />
					   增益档位：<input type="text" name='gear' />
					  </div>
					  <div id='readOut'>
					   <input type='radio' name='command' value='5' />读出速度模式：
					   <input type="text" name='ReadSpeedMode' placeholder='整数' />		   
					  </div>
					  <div id='transSpeed'>
                           <input type='radio' name='command' value='6' />转移速度值：
						   <input type="text" name='SetTransferSpeed' placeholder='整数' />	
					  </div>
					  <div id='setBin'>
                           <input type='radio' name='command' value='7' />
                           BinX：<input type="text" name='BinX' />
                           BinY：<input type="text" name='BinY' />
					  </div>
					  <div id='setRoi'>
                           <input type='radio' name='command' value='8' />
                           startX：<input type="text" name='startX' />
                           startY：<input type="text" name='startY' />
                           ImageW：<input type="text" name='imageWidth' />
                           imageHeight：<input type="text" name='imageHeight' />
					  </div>
					  <div id='setShutter'>
                           <input type='radio' name='command' value='9' />设置快门：
						   <select name="shutter" style='width:118px;'>
						           <option value="0">GlobalShutter</option>
								   <option value="1">RollingShutter</option>
						    </select>
					  </div>
					  <div id='setFrame'>
                           <input type='radio' name='command' value='10' />帧转移：
						   <input type="text" name='isFullFrame' />                          
					  </div>
					  <div id='setEm'>
                           <input type='radio' name='command' value='11' />
						   isEM<input type="text" name='isEM' />					   
						  eMValue<input type="text" name='eMValue' />						   
					  </div>
					  <div id='setCmos'>
                           <input type='radio' name='command' value='12' />
						   CMOS noise filter：<input type="text" name='isNoiseFilter' />
					  </div>
					  <div id='setBase'>
                           <input type='radio' name='command' value='13' />
						   isBaseline：<input type="text" name='isBaseline' />
						  baselineValue：<input type="text" name='baselineValue' />
					  </div>
					  <div id='setOverScan'>
                           <input type='radio' name='command' value='14' />
						   Over Scan：<input type="text" name='isOverScan' />
					  </div>
					<br>
					<input id='ccdSbmt' class='border submitBtn' type="button" value="确认提交指令">
				</form>
			  <br><hr style='width:98%;' class='center_mrg'><br>
			  <div><!--CCD可变属性-->
                <span class='propT'>CCD可变属性:</span><br><br>
				 <ul class='properUl'>
                    <li class='li fl'>当前状态:<span id='ccdStatus_1'></span></li>
                    <li class='li fl'>Base line值:<span id='baseLine'></span></li>
                    <li class='li fl'>读出模式:<span id='readMode'></span></li>
                    <li class='li fl'>当前拍摄波段:<span id='ObserveBand'></span></li>
                    <li class='li fl'>拍摄目标赤经:<span id='TargetRightAscension'></span></li>
                    <li class='li fl'>拍摄目标赤维:<span id='TargetDeclination'></span></li>
                </ul>
               <div class='clear'></div>
             </div><!--CCD 可变属性结束-->
		  </div><!--CCD 指令和可变属性 结束-->
		 <div class='border2'><!--ccd 固定属性-->
			<br><span class='propT'>CCD固定属性:</span><br><br>
			<ul class='properUl'>
				<li class='li fl'>探测器类型:<span>12345678</span></li>
				<li class='li fl'>传感器名称:<span>12345678</span></li>
				<li class='li fl'>图像位数:<span>12345678</span></li>
				<li class='li fl'>制冷方式:<span>12345678</span></li>
				<li class='li fl'>最低制冷温度:<span>12345678</span></li>
				<li class='li fl'>最大曝光时间:<span>12345678</span></li>
				<li class='li fl'>最小曝光时间:<span>12345678</span></li>
				<li class='li fl'>增益模式:<span>12345678</span></li>
				<li class='li fl'>读出模式:<span>12345678</span></li>
				<li class='li fl'>快门类型:<span>12345678</span></li>
				<li class='li fl'>快门模式:<span>12345678</span></li>
             </ul>
           <div class='clear'></div>
		 </div><!--ccd固定属性 结束-->
	   </div><!--ccd 结束-->
	   <div id='focusInfo' class='displayNo'><!--调焦器指令和属性 -->
			<nav class='center'><!--调焦器指令 导航-->
			    <span name='targetPos'>设置目标位置</span>&nbsp;&nbsp;&nbsp;&nbsp;
				<span name='fixedSpeed'>设置恒速转动</span>&nbsp;&nbsp;&nbsp;&nbsp;
				<span name='temperaEnable'>使能温度补偿</span>&nbsp;&nbsp;&nbsp;&nbsp;
				<span name='temperaCoeficnt'>温度补偿系数</span>
			</nav>
			<div class='border2'><!--调焦器 指令和可变属性-->
				<div id='btnsFocus' class='center_mrg center BtnsDiv'>
					&nbsp;&nbsp;&nbsp;&nbsp;<input id='focusConnect' class='border1 btnCommand' type='button' value='连接调焦器' />
					<input id='focusDisConnect' class='border1 btnCommand' type='button' value='断开调焦器' />
					<input id='focusFindHome' class='border1 btnCommand' type='button' value='找 零' />
					<input id='focusStop' class='border1 btnCommand' type='button' value='停止运动' />
				</div><br>
				<hr style='width:98%;' class='center_mrg'><br>
				<form id='at60Focus' class='center command'>
					<div id='targetPos'>
					   <input type='radio' name='command' value='1' />目标位置：
					   <input type="text" name='setPosition' placeholder=''/>
					</div>
					<div id='fixedSpeed'>
					   <input type='radio' name='command' value='2' />设置恒速转动：
					  <input type="text" name='speed' placeholder=''/>
					</div>
					<div id='temperaEnable'>
					   <input type='radio' name='command' value='3' />使能温度补偿：
					   <input type="text" name='enable' />
					</div>
					<div id='temperaCoeficnt'>
					   <input type='radio' name='command' value='4' />温度补偿系数：
					   <input type="text" name='coefficient' />
					  </div>
					<br><input id='focusSbmt' class='border submitBtn' type="button" value="确认提交指令">
				</form>
				<br><hr style='width:98%;' class='center_mrg'><br>
			<div><!--调焦器 可变属性-->
                <span class='propT'>调焦器可变属性:</span><br><br>
				 <ul class='properUl'>
                   <li class='li fl'>当前位置:&nbsp;&nbsp;<span id='curPos'></span></li>
                   <li class='li fl'>目标位置:&nbsp;&nbsp;<span id='targetPosition'></span></li>
                   <li class='li fl'>找零状态:&nbsp;&nbsp;<span id='focusIsHomed'></span></li>
                   <li class='li fl'>是否进行温度补偿:&nbsp;&nbsp;<span id='compens'></span></li>
                   <li class='li fl'>温度补偿系数:&nbsp;&nbsp;<span id='compensX'></span></li>
                </ul>
               <div class='clear'></div>
             </div><!--调焦器 可变属性结束-->
		 </div><!--调焦器 指令和可变属性 结束-->
		 <div class='border2'><!--调焦器 固定属性-->
			<br><span class='propT'>调焦器固定属性:</span><br><br>
			<ul class='properUl'>
			   <li class='li fl'>分辨率:&nbsp;&nbsp;<span>123456</span></li>          
			   <li class='li fl'>最大值:&nbsp;&nbsp;<span>123456</span></li>    
			   <li class='li fl'>最小值:&nbsp;&nbsp;<span>123456</span></li>    
			   <li class='li fl'>是否可找零:&nbsp;&nbsp;<span>12345</span></li>           
			   <li class='li fl'>可否温度补偿:&nbsp;&nbsp;<span>12345678</span></li>      
			   <li class='li fl'>最大速度:&nbsp;&nbsp;<span>12345678</span></li>          
			   <li class='li fl'>可否找零:&nbsp;&nbsp;<span>12345678</span></li>   
             </ul>
           <div class='clear'></div>
		 </div><!--调焦器 固定属性 结束-->
	   </div><!--调焦器 结束-->
	   <div id='domeInfo' class='displayNo'><!--圆顶 指令和属性-->
			<nav class='center'><!--圆顶指令 导航-->
			    <span name='setPos'>设置目标方位</span>&nbsp;&nbsp;&nbsp;&nbsp;
				<span name='rotateSpd'>设置转动速度</span>&nbsp;&nbsp;&nbsp;&nbsp;
				<span name='shadePos'>设置风帘位置</span>&nbsp;&nbsp;&nbsp;&nbsp;
				<span name='shadeAct'>控制风帘运动</span>
			</nav>
			<div class='border2'><!--圆顶 指令和可变属性-->
				<div id='btnsSlaveD' class='center_mrg center BtnsDiv'>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id='sDomeConnect' class='border1 btnCommand' type='button' value='连接圆顶' />
				   <input id='sDomeDisConnect' class='border1 btnCommand' type='button' value='断开圆顶' />
				   <input id='sDomeStop' class='border1 btnCommand' type='button' value='停止运动' />
				   <input id='sDomeScuttle' class='border1 btnCommand' type='button' value='打开天窗' />
				   <input id='sDomeScuttleClose' class='border1 btnCommand' type='button' value='关闭天窗' />
				</div><br>
				<hr style='width:98%;' class='center_mrg'><br>
				<form id='at60Dome' class='center command'>
					<div id='setPos'>
					   <input type='radio' name='command' value='1' />设置目标方位:
					   <input type="text" name='domePosition' placeholder='0-360'/>
					</div>
					<div id='rotateSpd'>
					    <input type='radio' name='command' value='2' />设置转动速度：
						<input type="text" name='RotateSpeed' placeholder='数字'/>
					</div>
					<div id='shadePos'>
					   <input type='radio' name='command' value='3' />设置风帘位置：
					   <input type="text" name='shadePosition' placeholder='0-90' />
					</div>
					<div id='shadeAct'>
					   <input type='radio' name='command' value='4' />控制风帘运动：
					   <select name="shadeAction" style='width:90px;'>
						   <option value="1">向上</option>
						   <option value="2">向下</option>
						   <option value="3">停止</option>
						 </select>
					  </div>
					<br><input id='domeSbmt' class='border submitBtn' type="button" value="确认提交指令">
				</form>
				<br><hr style='width:98%;' class='center_mrg'><br>
			<div><!--圆顶 可变属性-->
                <span class='propT'>圆顶可变属性:</span><br><br>
				 <ul class='properUl'>
                    <li class='li fl'>当前状态:&nbsp;&nbsp;<span id='domeStatus_1'></span></li>
				   <li class='li fl'>天窗状态:&nbsp;&nbsp;<span id='scuttle'></span></li>
                   <li class='li fl'>风帘状态:&nbsp;&nbsp;<span id='shadeStatus'></span></li>
                   <li class='li fl'>错误标识:&nbsp;&nbsp;<span id='errorStr'></span></li>
                </ul>
               <div class='clear'></div>
             </div><!--圆顶 可变属性结束-->
		 </div><!--圆顶 指令和可变属性 结束-->
		<div class='border2'><!--圆顶 固定属性-->
			<br><span class='propT'>圆顶固定属性:</span><br><br>
			<ul class='properUl'>
			   <li class='li fl'>类型:&nbsp;&nbsp;<span>123456</span></li>
                   <li class='li fl'>是否具备风帘:&nbsp;&nbsp;<span>123456</span></li>
                   <li class='li fl'>最大转动速度:&nbsp;&nbsp;<span>123456</span></li>
                   <li class='li fl'>控制风帘运动:&nbsp;&nbsp;<span>123456</span></li>
                   <li class='li fl'>可否连接:&nbsp;&nbsp;<span>123456</span></li>
             </ul>
           <div class='clear'></div>
		 </div><!--圆顶 固定属性 结束-->
	   </div><!--圆顶 结束-->
	   <div id='filterInfo' class='displayNo'><!--滤光片 指令和属性-->
			<div class='border2'><!--滤光片 指令和可变属性-->
				<div id='btnsFilter' class='center_mrg center BtnsDiv' style='width:850px;'>
					<input id='filterConnect' class='border1 btnCommand' type='button' value='连接滤光片' />&nbsp;&nbsp;&nbsp;&nbsp;
					<input id='filterDisConnect' class='border1 btnCommand' type='button' value='断开滤光片' />&nbsp;&nbsp;&nbsp;&nbsp;
					<input id='filterFindHome' class='border1 btnCommand' type='button' value='找 零' />&nbsp;&nbsp;&nbsp;&nbsp;
					滤光片位置：<select id='filterPosi' class='border1 btnCommand' name="filterPos" style='width:90px;color:#000;border:1px solid #000;'>
								<option value="">请选择</option>
							   <option value="0">0</option>
							   <option value="1">1</option>
							   <option value="2">2</option>
							   <option value="3">3</option>
						 </select>&nbsp;&nbsp;
					<input id='filterPosBtn' class='border submitBtn' type='button' value='提交滤光片位置' />
				</div><br>
			<hr style='width:98%;' class='center_mrg'><br>
			<div><!--滤光片 可变属性-->
                <span class='propT'>滤光片可变属性:</span><br><br>
				 <ul class='properUl'>
                   <li class='li fl'>是否找零:&nbsp;&nbsp;<span id='filterIsHomed'></span></li>
                   <li class='li fl'>当前状态:&nbsp;&nbsp;<span id='filterStatus_1'></span></li>
                   <li class='li fl'>错误标识:&nbsp;&nbsp;<span id='filterErrStr'></span></li>
                </ul>
               <div class='clear'></div>
             </div><!--滤光片 可变属性结束-->
		 </div><!--滤光片 指令和可变属性 结束-->
		<div class='border2'><!--滤光片 固定属性-->
			<br><span class='propT'>滤光片固定属性:</span><br><br>
			<ul class='properUl'>
			    <li class='li fl'>插槽数目:&nbsp;&nbsp;<span>123456</span></li>
			   <li class='li fl'>滤光片类型:&nbsp;&nbsp;<span>123456</span></li>
			   <li class='li fl'>滤光片焦距偏差值:&nbsp;&nbsp;<span>123456</span></li>
			   <li class='li fl'>滤光片焦距偏差值:&nbsp;&nbsp;<span>123456</span></li>
			   <li class='li fl'>可否设置滤光片位置:&nbsp;&nbsp;<span>123456</span></li>
             </ul>
           <div class='clear'></div>
		 </div><!--滤光片 固定属性 结束-->
	   </div><!--滤光片 结束-->
	   <div id='planInfo' class='displayNo pos_r'><!--观测计划表格 开始-->
			 <table id="dg" title="计划表格" >  </table>
			<div id="toolbar">
			<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-more" plain="true" onclick="importPlan()">导入计划</a>
			<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="addPlan()">添加计划</a>
			<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="savePlan()">保存</a>
			<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editPlan()">编辑</a>
			<!-- <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="submitPlan()">提交计划</a> -->
           </div>
		   <!--观测计划的模式按钮-->
		   <div id='planModes' class='pos_a'>
			  <div id='modeSpan'>执行模式
				  <div id='modeVal' class='border displayNo'>
					Single：<input type='radio' name='planMode' value='1' >
					SingleLoop：<input type='radio' name='planMode' value='2' >
					Sequence：<input type='radio' name='planMode' value='3' >
					SequenceLoop：<input type='radio' name='planMode' value='4' >
				  </div>
			  </div>
		      
				<div id='planExe' class='pos_a'>
					<button id='planStart'>开始</button>
					<button id='planStop'>停止</button>
					<!-- <button id='planNext'>下一个</button> -->
				</div>
				
		   </div>
		   
		   <!--观测计划的模式按钮 结束-->
		   <form id='imptPlan' style='display:none;' enctype='multipart/form-data'>
				<input type='file' name='plan' />
		   </form><!--计划文件上传表单-->
		</div><!--观测计划表格 结束-->
		<script type="text/javascript" src="/static/js/plan_datagrid.js"></script>
		<br>
		<div id='at60Res' class='displayNo center_mrg' style='width:92%;'><!--观测图像 开始-->
			<div id='bigPic' class='pos_r'>
				<img src='/static/images-1/pic_Res.jpg' style='width:100%;'>
				<button class='pos_a'>最新图像</button>
			</div>
			<div id='smallPic' class='pos_r'>
				<img id='pre' src='/static/images-1/left.jpg'>
				<img class='mrgR' src='/static/images-1/picSmall.jpg'>
				<img class='mrgR' src='/static/images-1/picSmall.jpg'>
				<img class='mrgR' src='/static/images-1/picSmall.jpg'>
				<img class='mrgR' src='/static/images-1/picSmall.jpg'>
				<img id='next' class='pos_a' src='/static/images-1/right.jpg'>
			</div>
		</div><!--观测图像 结束-->
    </div><!-- id='all' 结束 --><br>
	<footer class='center clear'>
		<span>中科院国家天文台兴隆观测基地</span>
	</footer>
</body>
<div style='position:fixed;bottom:36px;right:2px;width:18px' class='border'>观测计划在执行第<span id='planNum' style='color:blue;'></span>条</div>
</html>
<!-- <span>全开圆顶操作指令:</span><br />
               <input id='fDomeConnect' type='button' value='连接圆顶' />
			   打开圆顶：<select name="openDome" style='width:90px;'>
										<option value="">请选择</option>
									   <option value="0">关闭</option>
									   <option value="1">打开</option>
									   <option value="2">停止运动</option>
</select> -->