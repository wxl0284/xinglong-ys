/*60cm望远镜配置 js*/
 $(function () {
//页面加载完成后，默认所有选项为 未选中状态
	$('input').prop('checked', false);
	
//显示导航栏望远镜列表///////////////////////////////////// 
   var ul = $('#atListUl');
   $('#atList').hover(
        function (){
            ul.show();
        }, 
       function (){		
			ul.hide();
        } 
   );
   
    //各望远镜配置 js事件
   var configList = $('#atConfigList');
   $('#atConfig').hover(
        function (){
            configList.show();
        }, 
       function (){		
			configList.hide();
        } 
   );
//望远镜列表js代码结束///////////////////////////////////

//转台 ccd等子设备选中 js事件//////////////////////////////	
	$('#at60devs').on('click', 'input', function () {
		if ($(this).prop('checked')){
			//转台被选中 则显示转台的指令和属性选项
			var e = $(this).val();
			var div = '#' + e;
			var div = $(div);
			div.show();
			//控制滚动条 至新生产元素处
			$(document).scrollTop(div.offset().top);
		}else{
	//转台被取消选中 将转台下面所有指令和属性取消选中，并隐藏选项
			var e = $(this).val();
			var div = '#' + e;
			var div = $(div);
			div.find('input').prop('checked', false);
			div.hide();
		}
		
	});
//转台 ccd等子设备选中 js事件 结束/////////////////////////

//提交60cm望远镜配置 js事件 /////////////////////////////
	$('#at60ConfigBtn').click(function () {
		var configForm = document.getElementById('allOption');
		var formData = new FormData(configForm);
		//console.log(formData);
		$.ajax({
			url : '/xinglong/page_config/doAt60config',
			type : 'post',
			processData : false,
            contentType : false, 
			data : formData,
			success :function (info) {
				alert(info);
			},
			error : function () {
				alert('网络异常,请重新设置!')
			}
		});
	});
//提交60cm望远镜配置 js事件 结束/////////////////////////

//各选项询问框 js事件 /////////////////////////////
	//转台类型
	$('#atType').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['110px', '170px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#atTypeVal'),
			});
		}
	})
	
	//焦点类型
	$('#focustype').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['200px', '170px'],
			  content: $('#focustypeVal'),
			});
		}
	})
	
	//焦比值
	$('#focusratio').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['110px', '170px'],
			  content: $('#focusratioVal'),
			});
		}
	})
	
	//焦距
	$('#focuslength').click(function () {
		var focuslengthVal = $('#focuslengthVal');
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['110px', '170px'],
			  content: focuslengthVal,
			});				
		}
	})
	
	//轴1最大速度
	$('#maxAxis1Speed').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入轴1最大速度（°/s）", "0-30");
			
			while(!$.isNumeric(v) || v <= 0 || v > 30)
			{
				alert('轴1最大速度输入有误!');
				v = prompt("请输入轴1最大速度", "");
			}
			$('#maxAxis1SpeedVal').val(v);	
		}
	})
	
	//轴2最大速度
	$('#maxAxis2Speed').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入轴2最大速度（°/s）", "0-30");
			while(!$.isNumeric(v) || v <= 0 || v > 30)
			{
				alert('轴2最大速度输入有误!');
				v = prompt("请输入轴2最大速度", "");
			}
			
			$('#maxAxis2SpeedVal').val(v);			
		}
	})
	
	//轴3最大速度
	$('#maxAxis3Speed').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入轴3最大速度（°/s）", "0-30");
			
			while(!$.isNumeric(v) || v <= 0 || v > 30)
			{
				alert('轴3最大速度输入有误!');
				v = prompt("请输入轴3最大速度", "");
			}
			
			$('#maxAxis3SpeedVal').val(v);			
		}
	})
	
	//轴1最大加速度
	$('#maxAxis1Acceleration').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入轴1最大加速度（°/s）", "0-5");
			
			while(!$.isNumeric(v) || v <= 0 || v > 5)
			{
				alert('轴1最大加速度输入有误!');
				v = prompt("请输入轴1最大加速度（°/s）", "0-5");
			}
			
			$('#maxAxis1AccelerationVal').val(v);			
		}
	})
	
	//轴2最大加速度
	$('#maxAxis2Acceleration').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入轴2最大加速度（°/s）", "0-5");
			
			while(!$.isNumeric(v) || v <= 0 || v > 5)
			{
				alert('轴2最大加速度输入有误!');
				v = prompt("请输入轴2最大加速度（°/s）", "0-5");
			}
			
			$('#maxAxis2AccelerationVal').val(v);			
		}
	})
	
	//轴3最大加速度
	$('#maxAxis3Acceleration').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入轴3最大加速度（°/s）", "0-5");
			
			while(!$.isNumeric(v) || v <= 0 || v > 5)
			{
				alert('轴3最大加速度输入有误!');
				v = prompt("请输入轴3最大加速度（°/s）", "0-5");
			}
			
			$('#maxAxis3AccelerationVal').val(v);			
		}
	})
	
	//轴1复位位置
	$('#axis1ParkPosition').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入轴1复位位置（°）", "0-360");
			
			while(!$.isNumeric(v) || v < 0 || v > 360)
			{
				alert('轴1复位位置输入有误!');
				v = prompt("请输入轴1复位位置（°）", "0-360");
			}
			
			$('#axis1ParkPositionVal').val(v);			
		}
	})
	
	//轴2复位位置
	$('#axis2ParkPosition').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入轴2复位位置（°）", "0-90");
			
			while(!$.isNumeric(v) || v < 0 || v > 90)
			{
				alert('轴2复位位置输入有误!');
				v = prompt("请输入轴2复位位置（°）", "0-90");
			}
			
			$('#axis2ParkPositionVal').val(v);			
		}
	})
	
	//轴3复位位置
	$('#axis3ParkPosition').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入轴3复位位置（°）", "0-360");
			
			while(!$.isNumeric(v) || v < 0 || v > 360)
			{
				alert('轴3复位位置输入有误!');
				v = prompt("请输入轴3复位位置（°）", "0-360");
			}
			
			$('#axis3ParkPositionVal').val(v);			
		}
	})
	
	//俯仰最低值
	$('#minElevation').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入俯仰最低值（°）", ">10");
			
			while(!$.isNumeric(v) || v <= 10)
			{
				alert('俯仰最低值输入有误!');
				v = prompt("请输入俯仰最低值（°）", ">10");
			}
			
			$('#minElevationVal').val(v);			
		}
	})
	
	//温度传感器数目
	$('#numTemperatureSensor').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入温度传感器数目", "");
			var patn = /^\d+$/;
			
			while(!patn.test(v) || v < 0)
			{
				alert('温度传感器数目输入有误!');
				v = prompt("请输入温度传感器数目", "");
			}
			
			$('#numTemperatureSensorVal').val(v);			
		}
	})
	
	//湿度传感器数目
	$('#numHumiditySensor').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入湿度传感器数目", "");
			var patn = /^\d+$/;
			
			while(!patn.test(v) || v < 0)
			{
				alert('湿度传感器数目输入有误!');
				v = prompt("请输入湿度传感器数目", "");
			}
			
			$('#numHumiditySensorVal').val(v);			
		}
	})
	
	//如下为ccd之固定属性
	//探测器类型
	//转台类型
	$('#ccdType').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['150px', '120px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#ccdTypeVal'),
			});
		}
	})
	
	//x像素
	$('#xPixel').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入x像素值", "1024/2048");
			
			while(!$.isNumeric(v) || v < 0)
			{
				alert('x像素值输入有误!');
				v = prompt("请输入x像素值", "1024/2048");
			}
			
			$('#xPixelVal').val(v);
		}
	})
	
	//y像素
	$('#yPixel').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入y像素值", "1024/2048");
			
			while(!$.isNumeric(v) || v < 0)
			{
				alert('y像素值输入有误!');
				v = prompt("请输入y像素值", "1024/2048");
			}
			
			$('#yPixelVal').val(v);
		}
	})
	
	//x像元大小
	$('#xPixelSize').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入x像元值(um)", "");
			
			while(!$.isNumeric(v) || v < 0)
			{
				alert('x像元值(um)输入有误!');
				v = prompt("请输入x像元值(um)", "");
			}
			
			$('#xPixelSizeVal').val(v);
		}
	})
	
	//y像元大小
	$('#yPixelSize').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入y像元值(um)", "");
			
			while(!$.isNumeric(v) || v < 0)
			{
				alert('y像元值(um)输入有误!');
				v = prompt("请输入y像元值(um)", "");
			}
			
			$('#yPixelSizeVal').val(v);
		}
	})
	
	//传感器名称
	$('#sensorName').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入传感器名称", "芯片名");
			var patn = / /;
			while(patn.test(v))
			{
				alert('传感器名称输入有误!');
				v = prompt("请输入传感器名称", "");
			}
			
			$('#sensorNameVal').val(v);
		}
	})
	
	//图像位数
	$('#imageBits').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['136px', '120px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#imageBitsVal'),
			});
		}
	})
	
	//制冷方式
	$('#coolerMode').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['150px', '120px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#coolerModeVal'),
			});
		}
	})
	
	//最低制冷温度
	$('#lowCoolerT').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入最低制冷温度（℃）", "");
			while(!$.isNumeric(v))
			{
				alert('最低制冷温度输入有误!');
				v = prompt("请输入最低制冷温度（℃）", "");
			}
			
			$('#lowCoolerTVal').val(v);
		}
	})
	
	//最大曝光时间
	$('#maxExposureTime').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入最大曝光时间（S）", "");
			while(!$.isNumeric(v))
			{
				alert('最大曝光时间输入有误!');
				v = prompt("请输入最大曝光时间（S）", "");
			}
			
			$('#maxExposureTimeTVal').val(v);
		}
	})
	
	//最小曝光时间
	$('#minExposureTime').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入最小曝光时间（S）", "");
			while(!$.isNumeric(v) || v <= 0)
			{
				alert('最小曝光时间输入有误!');
				v = prompt("请输入最小曝光时间（S）", "");
			}
			
			$('#minExposureTimeVal').val(v);
		}
	})
	
	//曝光时间分辨率
	$('#exposureTimeRation').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入曝光时间分辨率（/微妙）", "");
			while(!$.isNumeric(v) || v <= 0)
			{
				alert('曝光时间分辨率输入有误!');
				v = prompt("请输入曝光时间分辨率（/微妙）", "");
			}
			
			$('#exposureTimeRationVal').val(v);
		}
	})
	
	//满阱电荷
	$('#fullWellDepth').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入满阱电荷（电子数）", "");
			while(!$.isNumeric(v) || v <= 0)
			{
				alert('满阱电荷输入有误!');
				v = prompt("请输入满阱电荷（电子数）", "");
			}
			
			$('#fullWellDepthVal').val(v);
		}
	})
	
	//读出模式5
	$('#readoutMode5').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['150px', '120px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#readoutMode5Val'),
			});
		}
	})
	
	//读出速度模式4
	$('#readoutSpeed').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['110px', '120px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#readoutSpeedVal'),
			});
		}
	})
	
	//转移速度模式4
	$('#transferSpeed').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['110px', '120px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#transferSpeedVal'),
			});
		}
	})
	
	//增益模式2
	$('#gainmode').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['160px', '120px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#gainmodeVal'),
			});
		}
	})
	
	//增益挡位4
	$('#gainNumber').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['118px', '130px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#gainNumberVal'),
			});
		}
	})
	
	//增益值
	$('#gainValueArray').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['158px', '130px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#gainValueArrayVal'),
			});
		}
	})
	
	//读出噪声值
	$('#readoutNoiseArray').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['118px', '130px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#readoutNoiseArrayVal'),
			});
		}
	})
	
	//快门类型
	$('#ShutterType').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['118px', '130px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#ShutterTypeVal'),
			});
		}
	})
	
	//快门模式
	$('#ccdShutterMode').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['126px', '130px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#ccdShutterModeVal'),
			});
		}
	})
	
	//bin值
	$('#binArray').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['126px', '130px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#binArrayVal'),
			});
		}
	})
	
	//接口类型
	$('#InterfaceType').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['126px', '130px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#InterfaceTypeVal'),
			});
		}
	})
	
	//曝光触发模式
	$('#ExposeTriggerMode').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['126px', '130px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#ExposeTriggerModeVal'),
			});
		}
	})
	
	//最大em值
	$('#EmMaxValue').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入最大EM值", "整数");
			var patn = /^\d{1,3}$/;
			while(!patn.test(v) || v <= 0)
			{
				alert('最大EM输入有误!');
				v = prompt("请输入最大EM", "整数");
			}
			
			$('#EmMaxValueVal').val(v);
		}
	})
	
	//最小em值
	$('#EmMinValue').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入最小EM值", "整数");
			var patn = /^\d{1,3}$/;
			while(!patn.test(v) || v <= 0)
			{
				alert('最小EM输入有误!');
				v = prompt("请输入最小EM", "整数");
			}
			
			$('#EmMinValueVal').val(v);
		}
	})
	
	//调焦器 固定属性配置
	//最大值
	$('#focusMaxValue').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入最大值（μm）", "");
			while(!$.isNumeric(v) || v <= 0)
			{
				alert('最大值输入有误!');
				v = prompt("请输入最大值（μm）", "");
			}
			
			$('#focusMaxValueVal').val(v);
		}
	})
	
	//最小值
	$('#focusMinValue').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入最小值（μm）", "");
			while(!$.isNumeric(v) || v <= 0)
			{
				alert('最小值输入有误!');
				v = prompt("请输入最小值（μm）", "");
			}
			
			$('#focusMinValueVal').val(v);
		}
	})
	
	//分辨率
	$('#focusIncrement').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入分辨率（μm）", "");
			while(!$.isNumeric(v) || v <= 0)
			{
				alert('分辨率输入有误!');
				v = prompt("请输入分辨率（μm）", "");
			}
			
			$('#focusIncrementVal').val(v);
		}
	})
	
	//最大速度
	$('#focusMaxSpeed').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入最大速度（μm/s）", "");
			while(!$.isNumeric(v) || v <= 0)
			{
				alert('最大速度输入有误!');
				v = prompt("请输入最大速度（μm/s）", "");
			}
			
			$('#focusMaxSpeedVal').val(v);
		}
	})
	
	//随动圆顶 固定属性配置
	//圆顶类型
	$('#DomeType').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['110px', '130px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#DomeTypeVal'),
			});
		}
	})
	
	//最大转动速度
	$('#sDomeMaxSpeed').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入最大转动速度（°/s）", "");
			while(!$.isNumeric(v) || v <= 0)
			{
				alert('最大转动速度输入有误!');
				v = prompt("请输入最大转动速度（°/s）", "");
			}
			
			$('#sDomeMaxSpeedVal').val(v);
		}
	})
	
	//滤光片固定属性 配置
	//插槽数目
	$('#numberOfFilter').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入插槽数目", "");
			while(!$.isNumeric(v) || v <= 0)
			{
				alert('插槽数目输入有误!');
				v = prompt("请输入插槽数目", "");
			}
			
			$('#numberOfFilterVal').val(v);
		}
	})
	
	//滤光片类型
	$('#FilterSystem').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['150px', '130px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#FilterSystemVal'),
			});
		}
	})
	
	//滤光片名称
	$('#FilterName').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入滤光片名称", "");
			v = $.trim(v);
			while(v.length === 0)
			{
				alert('滤光片名称不能为空!');
				v = prompt("请输入滤光片名称", "");
			}
			
			$('#FilterNameVal').val(v);
		}
	})
	
	//滤光片焦距偏差值
	$('#FilterFocusLengthCompensate').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入滤光片焦距偏差值（μm）", "");
			v = $.trim(v);
			while(v.length === 0)
			{
				alert('滤光片焦距偏差值输入有误!');
				v = prompt("请输入滤光片焦距偏差值（μm）", "");
			}
			
			$('#FilterFocusLengthCompensateVal').val(v);
		}
	})
	
	//插槽尺寸
	$('#FilterSize').click(function () {
		if ($(this).prop('checked'))
		{
			var v = prompt("请输入插槽尺寸（mm）", "[x,y,z]");
			v = $.trim(v);
			var patn = /^\[\d+,\d+,\d+\]$/;
			while(!patn.test(v))
			{
				alert('插槽尺寸输入有误!');
				v = prompt("请输入插槽尺寸（mm）", "[x,y,z]");
			}
			
			$('#FilterSizeVal').val(v);
		}
	})
	
	//形状
	$('#FilterShape').click(function () {
		if ($(this).prop('checked'))
		{
			layer.open({
			  type: 1,
			  title: false,
			  closeBtn: 2,
			  area: ['110px', '120px'],
			  //shadeClose: true,
			  //skin: 'yourclass',
			  content: $('#FilterShapeVal'),
			});
		}
	})
//各选项询问框 js事件 结束/////////////////////////
})