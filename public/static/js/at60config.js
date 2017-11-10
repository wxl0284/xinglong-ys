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
//各选项询问框 js事件 结束/////////////////////////
})