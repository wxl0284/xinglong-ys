/* 
* 设备1js  at60
*/

 $(function () {
 	//ajax 实时更新60cm望远镜子设备状态数据///////////////////////	
	var err = 0;  //控制错误时，弹窗数量
	function getStatus()
	{
		$.ajax({
			type : 'post',
            url : '/xinglong/at60status/devsStatus',           
            success:  function (info) {
				err = 0; //将err变量重置为0
                var info = eval( '(' + info +')' );
				$('#date').html(info.date);
				$('#bjTime').html(info.time);
				$('#siderealTime').html(info.siderealTime);
				$('#curstatus').html(info.curstatus); //转台状态
				$('#trackError').html(info.trackError);
				$('#hourAngle').html(info.hourAngle);
				$('#coverStatus').html(info.coverStatus);
				$('#rightAscension').html(info.rightAscension);
				$('#declination').html(info.declination);
				$('#trackObjectName').html(info.trackObjectName);
				$('#trackType').html(info.trackType);
				$('#targetRightAscension').html(info.targetRightAscension);
				$('#targetDeclination').html(info.targetDeclination);
				$('#azmiuth').html(info.azmiuth);//当前方位
				$('#elevation').html(info.elevation);//当前俯仰
				$('#RightAscensionSpeed').html(info.RightAscensionSpeed);
				$('#declinationSpeed').html(info.declinationSpeed);
				$('#derotatorPositon').html(info.derotatorPositon);
				$('#targetDerotatorPosition').html(info.targetDerotatorPosition);
				$('#axis1TrackError').html(info.axis1TrackError);
				$('#axis2TrackError').html(info.axis2TrackError);
				$('#axis3TrackError').html(info.axis3TrackError);
				//接下来为gimbal可变属性
				$('#stamp').html(info.timeStamp); //时间戳
				$('#siderealTime_1').html(info.siderealTime); //恒星时
				$('#hourAngle_1').html(info.hourAngle); //时角
				$('#rightAscension_1').html(info.rightAscension); //赤经
				$('#declination_1').html(info.declination); //赤纬
				//J2000赤经
				$('#J2000RightAscension').html(info.J2000RightAscension);
				//j2000赤纬
				$('#J2000Declination').html(info.J2000Declination);
				$('#azmiuth_1').html(info.azmiuth);//当前方位
				$('#elevation_1').html(info.elevation);//当前俯仰
				//当前消旋位置
				$('#derotatorPositon_1').html(info.derotatorPositon);
				//目标赤经
				$('#targetRightAscension_1').html(info.targetRightAscension);
				//目标赤纬
				$('#targetDeclination_1').html(info.targetDeclination);
				//目标j2000赤经
				$('#targetJ2000RightAscension').html(info.targetJ2000RightAscension);
				//目标j2000赤纬
				$('#targetJ2000Declination').html(info.targetJ2000Declination);
				//目标消旋位置 targetDerotatorPosition
				$('#targetDerotatorPosition_1').html(info.targetDerotatorPosition);
				//gimbal可变属性 结束////////////////////////////
				
				//60cm各子设备状态//////////////////////////////////
				//转台状态////////////////////////////
				if (info.curstatus == '异常')
				{
					$('#gimbPic').attr('src', '/static/images-1/error.jpg');
				}else{
					$('#gimbPic').attr('src', '/static/images-1/ok.jpg');
				}
				$('#gimbStatus').html('转台:' + info.curstatus);
				//转台状态结束/////////////////////////////
				
				//ccd状态/////////////////////////////////////
				/* if (info.curstatus == '异常')
				{
					$('#gimbPic').attr('src', '/static/images-1/error.jpg');
				} */
				$('#ccdStatus').html('CCD:' + info.ccdCurStatus);
				//如下为ccd可变属性
				$('#ccdStatus_1').html(info.ccdCurStatus);
				$('#baseLine').html(info.ccdBaseline);
				$('#readMode').html(info.ccdReadOutMode);
				$('#ObserveBand').html(info.ccdObserveBand);
				$('#TargetRightAscension').html(info.ccdJ2000RightAscension);
				$('#TargetDeclination').html(info.ccdJ2000Declination);				
				//ccd 状态结束/////////////////////////////////
				$('#focusStatus').html('调焦器:' + info.focusCurStatus);
				//如下为 调焦器可变属性/////////////////////////////////
				$('#curPos').html(info.focusPosition);
				$('#targetPosition').html(info.focusTargetPos);
				//找零状态
				$('#focusIsHomed').html(info.focusIsHomed);
				//是否温度补偿
				$('#compens').html(info.focusIsTCompensation);
				//温度补偿系数
				$('#compensX').html(info.focusTCompenensation);
				//调焦器 状态///////////////////////////////////////////////
				//调焦器 状态 结束//////////////////////////////////////////
				
				//圆顶状态//////////////////////////////////////////////////
				/* if (info.curstatus == '异常')
				{
					$('#gimbPic').attr('src', '/static/images-1/error.jpg');
				}else{
					$('#gimbPic').attr('src', '/static/images-1/ok.jpg');
				} */
				$('#domeStatus').html('圆顶:' + info.slaveDomeCurstatus);
				$('#domeStatus_1').html(info.slaveDomeCurstatus);
				$('#scuttle').html(info.slaveDomeScuttleStatus);//天窗状态
				$('#shadeStatus').html(info.slaveDomeShadeStatus);//风帘状态
				$('#errorStr').html(info.slaveDomeErrorStatus);//错误标识
				//圆顶状态 结束/////////////////////////////////////////////
				/* if (info.curstatus == '异常')
				{
					$('#gimbPic').attr('src', '/static/images-1/error.jpg');
				}else{
					$('#gimbPic').attr('src', '/static/images-1/ok.jpg');
				} */
				$('#filterStatus').html('滤光片:' + info.filterCurstatus);
				$('#filterStatus_1').html(info.filterCurstatus);
				$('#filterIsHomed').html(info.filterIsHomed);
				$('#filterErrStr').html(info.filterErrorStatus);
				
				//滤光片状态////////////////////////////////////////////////
				
				//滤光片状态 结束///////////////////////////////////////////
				//60cm各子设备状态结束//////////////////////////////
            },
			error: function (){
				err ++;
				if(err <= 1)
				{
					alert('网络异常,设备实时数据无法获取!');
				}
				
			},
		});
	}
	setInterval (getStatus, 1800);
	/////////////////////////////////////////////////////////
	//显示导航栏望远镜列表   
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
	
	//接管 弹窗代码////////////////////////////////////////////////
	$('#takeOverBtn').click(function () {
		$('#panel').removeClass('displayNo');
		$('#takeOverPanel').window({
			title : '接管望远镜',
			width : 410,
			height : 300,
			collapsible : false,
			minimizable : false,
			maximizable : false,
			
		});
	});
	//接管 弹窗代码 结束////////////////////////////////////////////////
	
    //望远镜 子设备导航栏 js事件 ////////////////////////////
    $('#devsNav table').on('click', 'a', function (){
        var btm = $('#devsNav a.borderBtm').not($(this));  //排除自己被多次点击时的情况
        var dev = $(this).attr('name');
        var display = $('div.display');
        
        $(this).addClass('borderBtm');
		btm.removeClass('borderBtm');
        //将对应子设备的信息显示出来
        dev = '#' + dev;
        dev = $(dev);
        display.removeClass('display');
        display.addClass('displayNo');
        dev.removeClass('displayNo');
        dev.addClass('display');
    }); ///////////////////////////////////////////////////////
    
    /*//接管望远镜按钮 js事件 ////////////////////////////////////////////
     $('#takeOver').click(function () {
       //var e = $(this);
   
       var t = $('.easyui-datetimespinner');//获取时间输入框
       var v1 = t.first().val();
       var v2 = t.last().val();
       
       if (!v1 || !v2)
       {
           alert('请正确输入:起始/结束时间！');return;
       }
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60TakeOver',
            data : {takeOver:1,},             
            success:  function (info) {
               alert(info);
            },
            error:  function () {
               alert('网络异常,请再次点击：接管按钮!');
            },
        });
     
    });/////////////////////////////////////////////////////////////*/
	
	//每个设备 指令导航栏 nav下span 点击事件/////////////////////////
	var navSpan = $('nav span');
	var divs = $('form div');//获取页面指令表单中所有div
	$('nav').on('click', 'span', function() {
		$(this).addClass('active');
		navSpan.not($(this)).removeClass('active');
		
		//显示或隐藏相应指令 
		var divID = $(this).attr('name');
		divID = '#' + divID;
		commandDiv = $(divID);
		divs.not(commandDiv).hide();//隐藏其他相应指令
		commandDiv.show();//显示相应指令		
		commandDiv.click();//同时执行此指令的点击事件		
	});//每个设备 指令导航栏 nav下span 点击事件  结束/////////////////////////
	
	//转台 连接按钮js事件/////////////////////////////////
    $('#gimbalConnect').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {connect:1},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
              error:  function () {
               alert('网络异常,请再次连接！');
            },
        });
    }); 
	
	//转台 断开连接按钮 js事件/////////////////////////////////
    $('#gimbalDisConnect').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {connect:2},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
              error:  function () {
               alert('网络异常,请再次断开连接！');
            },
        });
    }); 
	
	//转台 找零按钮js事件//////////////////////////////////
    $('#gimbalFindhome').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {findHome:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击：找零按钮!');
            },
        });
    });
    
	//转台 复位按钮 js事件////////////////////////////////////
    $('#gimbalPark').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {park:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次复位!');
            },
        });
    }); 
	 
	//转台 停止按钮 js事件///////////////////////////////////
    $('#gimbalStop').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {stop:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击：停止按钮!');
            },
        });
    });  
	
	//转台 急停按钮 js事件/////////////////////////////////////
    $('#gimbalEmergenceStop').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {EmergenceStop:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击：急停按钮!');
            },
        });
    });/////////////////////////////////////////// 
	
	//转台 带参数指令  js事件////////////////////////////////////////////////
	var gimbalForm = $('#at60Gimbal');
    var gimbalSelect = gimbalForm.find('div');

    gimbalSelect.click(function () {
         $(this).find('input:radio').prop('checked', true);
         var notcheck = gimbalSelect.not($(this));
         notcheck.addClass('notCheck');
         notcheck.find('input[type="text"]').val('');
         $(this).removeClass('notCheck');
         
     });
	 
    //转台 轴3工作模式 仅对模式2有效 js事件////////////////////////////
	$('#at60Axis3').change(function (){
        var e = $(this).next('span');
       if ($(this).val() == 2)
       {
          e.removeClass('displayNo');
           
       }else{
           e.addClass('displayNo');
       }
    });
    
    //验证转台 表单指令数据///////////////////////////////////////////
	function checkGimbal ()
	{
		var msg = ''; //定义错误提示
		var formElemt = $('#at60Gimbal');
		if (formElemt.find("input[value='1']").prop('checked')) //验证跟踪恒星 指令数据
		{
			var rightAscension = formElemt.find('input[name="rightAscension"]').val();
			var declination = formElemt.find('input[name="declination"]').val();
			var epoch = formElemt.find('select[name="epoch"]').val();
			var speed = formElemt.find('select[name="speed"]').val();
			//定义各指令数据格式
			var reg = new RegExp("^[a-zA-Z0-9_]{6,10}$");
			
			/* if (数据1未通过) //验证未通过
			{
				msg += '提示信息1\n';
			}
			
			if (数据2未通过){
				msg += '提示信息2\n';
			}
			.....
			if (msg != '')  //有数据错误
			{
				alert(msg);	return false;
			}else{//数据无误
				return true;
			}
			*/
		}else if(formElemt.find("input[value='2']").prop('checked')){//验证目标名称 
			var objectName = formElemt.find('input[name="objectName"]').val();//目标名称
			var objectType = formElemt.find('select[name="objectType"]').val();//目标类型
			//定义各指令数据格式
			var reg = new RegExp("^[a-zA-Z0-9_]{6,10}$");
			/* if (数据1未通过) //验证未通过
			{
				msg += '提示信息1\n';
			}
			
			if (数据2未通过){
				msg += '提示信息2\n';
			}
			.....
			if (msg != '')  //有数据错误
			{
				alert(msg);	return false;
			}else{//数据无误
				return true;
			}
			*/
		}else if(formElemt.find("input[value='3']").prop('checked')){//指向固定位置 
			var azimuth = formElemt.find('input[name="azimuth"]').val();//方位
			var elevation = formElemt.find('input[name="elevation"]').val();//俯仰
			//定义各指令数据格式
			var reg = new RegExp("^[a-zA-Z0-9_]{6,10}$");
			/* if (数据1未通过) //验证未通过
			{
				msg += '提示信息1\n';
			}
			
			if (数据2未通过){
				msg += '提示信息2\n';
			}
			.....
			if (msg != '')  //有数据错误
			{
				alert(msg);	return false;
			}else{//数据无误
				return true;
			}
			*/
		}else if (formElemt.find("input[value='4']").prop('checked')){//轴3指向固定位置
			var slewDerotator = formElemt.find('input[name="slewDerotator"]').val();//轴3指向固定位置
			//if ()......
		}else if (formElemt.find("input[value='5']").prop('checked')){//轴3工作模式
			var axis3Mode = formElemt.find('select[name="mode"]').val();//模式
			var polarizingAngle = formElemt.find('input[name="polarizingAngle"]').val();//起偏角
			//if ()......
		}else if (formElemt.find("input[value='6']").prop('checked')){//速度修正
			var axis = formElemt.find('select[name="axis"]').val();//轴
			var correction = formElemt.find('input[name="correction"]').val();//修正值
			//if ()......
		}else if (formElemt.find("input[value='7']").prop('checked')){//恒速运动
			var FixedMoveAxis = formElemt.find('input[name="FixedMoveAxis"]').val();//轴
			var FixedMoveSpeed = formElemt.find('input[name="FixedMoveSpeed"]').val();//速度
			//if ()......
		}else if (formElemt.find("input[value='8']").prop('checked')){//位置修正
			var PositionCorrectAxis = formElemt.find('input[name="PositionCorrectAxis"]').val();//轴
			var PositionCorrectVal = formElemt.find('input[name="PositionCorrectVal"]').val();//速度
			//if ()......
		}else if (formElemt.find("input[value='9']").prop('checked')){//镜盖操作
			var openCover = formElemt.find('select[name="openCover"]').val();//
			//if ()......
		}else if (formElemt.find("input[value='10']").prop('checked')){//焦点切换镜
			var setFocusType = formElemt.find('select[name="setFocusType"]').val();
			//if ()......
		}else if (formElemt.find("input[value='11']").prop('checked')){//保存同步数据
			var saveSyncData = formElemt.find('input[name="saveSyncData"]').val();
			//if ()......
		}else if (formElemt.find("input[value='13']").prop('checked')){//属性设置
			var configProp = formElemt.find('input[name="configProp"]').val();
			//if ()......
		}
	}	//转台指令 验证函数结束
	
    //转台 带参数指令 表单提交//////////////////////////////////
    $('#gimbalSbmt').click(function () {
        var form = $('#at60Gimbal');    //获取转台表单元素
        var formData = new FormData(form[0]);  //将jquery对象转为js-dom对象
		if(true)	//验证数据 通过后执行ajax  checkGimbal()
		{
			//执行ajax
			$.ajax ({
              type: 'post',
              url : '/xinglong/at60/at60GimbalSendData',
              data : formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
                alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
              },
              error:  function () {
               alert('网络异常,请重新提交');
              },
        	});
		}
        
     
    });
    
   //转台 表单提交按钮 hover //////////////////////////////////
   $('#gimbalSbmt').hover(
        function (){
            $(this).addClass("hover");
        }, 
        function (){
            $(this).removeClass("hover");
        }
   );
   
   //CCD 连接按钮 js事件///////////////////////////////////
   $('#ccdConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsCCD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60CcdSendData',
            data : {ccdConnect:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次连接ccd!');
            },
        });
   });
   
   //CCD 断开按钮 js事件///////////////////////////////////
   $('#ccdDisConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsCCD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60CcdSendData',
            data : {ccdConnect:2,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次断开ccd!');
            },
        });
   });
   
   //CCD 停止曝光 js事件/////////////////////////////////////
   $('#ccdStopExpose').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsCCD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60CcdSendData',
            data : {StopExpose:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次停止曝光!');
            },
        });
   });
   
   //CCD 终止曝光 js事件/////////////////////////////////////
   $('#ccdAbortExpose').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsCCD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60CcdSendData',
            data : {AbortExpose:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次终止曝光!');
            },
        });
   });
   
   //CCD 带参数指令  js事件//////////////////////////////////////////////
	var ccdForm = $('#at60Ccd');
    var ccdSelect = ccdForm.find('div');

    ccdSelect.click(function () {
         $(this).find('input:radio').prop('checked', true);
         var notcheck = ccdSelect.not($(this));
         notcheck.addClass('notCheck');
         notcheck.find('input[type="text"]').val('');
         $(this).removeClass('notCheck');
     });
	
	//ccd 表单数据验证////////////////////////////////////////////
	function checkCcd ()
	{
		var msg = ''; //定义错误提示
		var formElemt = $('#at60Ccd');
		
		if (formElemt.find("input[value='1']").prop('checked')) //制冷温度
		{
			var temperature = formElemt.find("input[name='temperature']").val();
			/*if ()
			{
				.......
			}*/
		}else if (formElemt.find("input[value='2']").prop('checked')){//曝光策略
			var validFlag = formElemt.find("input[name='validFlag']").val();//数据标志位
			var startTime = formElemt.find("input[name='startTime']").val();//起始时刻
			var duration = formElemt.find("input[name='duration']").val();//曝光时间
			var delay = formElemt.find("input[name='delay']").val();//延迟时间
			var objectName = formElemt.find("input[name='objectName']").val();//目标名称
			var objectType = formElemt.find("select[name='objectType']").val();//目标类型
			var objectRightAscension = formElemt.find("input[name='objectRightAscension']").val();//目标赤经
			var objectDeclination = formElemt.find("input[name='objectDeclination']").val();//目标赤纬
			var objectEpoch = formElemt.find("select[name='objectEpoch']").val();//目标历元
			var objectBand = formElemt.find("input[name='objectBand']").val();//拍摄波段
			var objectFilter = formElemt.find("select[name='objectFilter']").val();//滤光片
			var isSaveImage = formElemt.find("select[name='isSaveImage']").val();//是否保存图像
			/* if ()
			{
				........
			} */
		}else if (formElemt.find("input[value='3']").prop('checked')){//开始曝光
			var isReadFrameSeq = formElemt.find("input[name='isReadFrameSeq']").val();
			var frameSequence = formElemt.find("input[name='frameSequence']").val();
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='4']").prop('checked')){//设置增益
			var mode = formElemt.find("input[name='mode']").val();//增益模式
			var gear = formElemt.find("input[name='gear']").val();//挡位
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='5']").prop('checked')){//设置读出速度模式值
			var ReadSpeedMode = formElemt.find("input[name='ReadSpeedMode']").val();//增益模式
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='6']").prop('checked')){//设置转移速度值
			var SetTransferSpeed = formElemt.find("input[name='SetTransferSpeed']").val();//增益模式
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='7']").prop('checked')){//设置bin
			var BinX = formElemt.find("input[name='BinX']").val();//binX
			var BinY = formElemt.find("input[name='BinY']").val();//binY
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='8']").prop('checked')){//设置Roi
			var startX = formElemt.find("input[name='startX']").val();//startX
			var startY = formElemt.find("input[name='startY']").val();//startY
			var imageWidth = formElemt.find("input[name='imageWidth']").val();//imageWidth
			var imageHeight = formElemt.find("input[name='imageHeight']").val();//imageHeight
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='9']").prop('checked')){//设置快门
			var shutter = formElemt.find("select[name='shutter']").val();
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='10']").prop('checked')){//设置帧转移
			var isEM = formElemt.find("input[name='isEM']").val();//isEM
			var eMValue = formElemt.find("input[name='eMValue']").val();//eMValue
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='11']").prop('checked')){//设置SetEM
			var isFullFrame = formElemt.find("input[name='isFullFrame']").val();
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='12']").prop('checked')){//设置CMOS
			var isNoiseFilter = formElemt.find("input[name='isNoiseFilter']").val();
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='13']").prop('checked')){//设置Base line
			var isBaseline = formElemt.find("input[name='isBaseline']").val();
			var baselineValue = formElemt.find("input[name='baselineValue']").val();
			/* if ()
			{
				.....
			} */
		}else if (formElemt.find("input[value='14']").prop('checked')){//设置Over Scan
			var isOverScan = formElemt.find("input[name='isOverScan']").val();
			/* if ()
			{
				.....
			} */
		}
	}//ccd 表单验证函数结束

	//CCD 带参数指令 表单提交 JS事件///////////////////////////////
    $('#ccdSbmt').click(function () {
        var form = $('#at60Ccd');    //获取ccd表单元素
        var formData = new FormData(form[0]);
        //执行ajax
        if (true)  //此处调用 checkCcd函数
		{
			$.ajax ({
              type: 'post',
              url : '/xinglong/at60/at60CcdSendData',
              data : formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
                alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
             },
             error:  function () {
               alert('网络异常,请重新提交');
            },
          })
		}     
    });
	
   //ccd 表单提交按钮 hover //////////////////////////////////
   $('#ccdSbmt').hover(
        function (){
            $(this).addClass("hover");
        }, 
        function (){
            $(this).removeClass("hover");
        }
   );
   
   //调焦器 带参数指令  js事件//////////////////////////////////////////////
	var focusForm = $('#at60Focus');
    var focusSelect = focusForm.find('div');

    focusSelect.click(function () {
         $(this).find('input:radio').prop('checked', true);
         var notcheck = focusSelect.not($(this));
         notcheck.addClass('notCheck');
         notcheck.find('input[type="text"]').val('');
         $(this).removeClass('notCheck');
     });
   
   //调焦器 连接按钮 js事件///////////////////////////////////
   $('#focusConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFocus input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FocusSendData',
            data : {focusConnect:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次连接调焦器!');
            },
      });
   });
   
    //调焦器 断开按钮 js事件///////////////////////////////////
   $('#focusDisConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFocus input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FocusSendData',
            data : {focusConnect:2,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次断开调焦器!');
            },
        });
   });
   
   //调焦器 停止运动按钮 js事件///////////////////////////////////
   $('#focusStop').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFocus input').not($(this)).removeClass('btnClick');
	   $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FocusSendData',
            data : {focusStop:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击停止按钮!');
            },
        });
   });
   
   //调焦器 找零按钮 js事件///////////////////////////////////
   $('#focusFindHome').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFocus input').not($(this)).removeClass('btnClick');
	   $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FocusSendData',
            data : {findHome:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击找零按钮!');
            },
        });
   }); 
   
    //调焦器 表单提交按钮 hover //////////////////////////////////
   $('#focusSbmt').hover(
        function (){
            $(this).addClass("hover");
        }, 
        function (){
            $(this).removeClass("hover");
        }
   );
   
   //调焦器 表单数据验证////////////////////////////////////////////
	function checkCcd ()
	{
		var msg = ''; //定义错误提示
		var formElemt = $('#at60Focus');
			
		if (formElemt.find("input[value='1']").prop('checked'))//目标位置
		{
			var setPosition = formElemt.find("input[name='setPosition']").val();
			/* if ()
			{
				......
			} */
		}else if (formElemt.find("input[value='2']").prop('checked')){//恒速转动
			var speed = formElemt.find("input[name='speed']").val();
			/* if ()
				{
					......
				} */
		}else if (formElemt.find("input[value='3']").prop('checked')){//使能温度补偿
			var enable = formElemt.find("input[name='enable']").val();
			/* if ()
				{
					......
				} */
		}else if (formElemt.find("input[value='4']").prop('checked')){//温度补偿系数
			var coefficient = formElemt.find("input[name='coefficient']").val();
			/* if ()
				{
					......
				} */
		}
	}//调焦器 表单数据验证函数 结束///////////////////////////////////
   
   //调焦器 带参数指令 表单提交 JS事件///////////////////////////////
    $('#focusSbmt').click(function () {
        var form = $('#at60Focus');    //获取ccd表单元素
        var formData = new FormData(form[0]);
        //执行ajax
		if (true)	//此处调用 checkCcd()函数
		{
			$.ajax ({
              type: 'post',
              url : '/xinglong/at60/at60FocusSendData',
              data : formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
                alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
             },
             error:  function () {
               alert('网络异常,请重新提交');
            },
          })
		}
        
    });
	
	//随动圆顶 连接按钮 js事件///////////////////////////////////
   $('#sDomeConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsSlaveD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60sDomeSendData',
            data : {sDomeConnect:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次连接圆顶!');
            },
        });
   });
   
   //随动圆顶 断开按钮 js事件///////////////////////////////////
   $('#sDomeDisConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsSlaveD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60sDomeSendData',
            data : {sDomeConnect:2,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次断开圆顶!');
            },
        });
   });
   
   //随动圆顶 停止运动按钮 js事件///////////////////////////////////
   $('#sDomeStop').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsSlaveD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60sDomeSendData',
            data : {sDomeStop:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击该按钮!');
           },
        });
   });
   
   //随动圆顶 打开天窗 按钮 js事件///////////////////////////////////
   $('#sDomeScuttle').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsSlaveD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60sDomeSendData',
            data : {OpenScuttle:1,},             
            success:  function (info) {
               alert(info);
               if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次打开天窗!');
            },
        });
   });
   
   //随动圆顶 关闭天窗 按钮 js事件///////////////////////////////////
   $('#sDomeScuttleClose').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsSlaveD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60sDomeSendData',
            data : {OpenScuttle:2,},             
            success:  function (info) {
               alert(info);
               if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次关闭天窗!');
            },
        });
   });
	
	//随动圆顶 带参数指令  js事件//////////////////////////////////////////////
	var domeForm = $('#at60Dome');
    var domeSelect = domeForm.find('div');

    domeSelect.click(function () {
         $(this).find('input:radio').prop('checked', true);
         var notcheck = domeSelect.not($(this));
         notcheck.addClass('notCheck');
         notcheck.find('input[type="text"]').val('');
         $(this).removeClass('notCheck');
     });
	 
	//随动圆顶 表单数据验证////////////////////////////////////////////
	function checkSlaveDome ()
	{
		var msg = ''; //定义错误提示
		var formElemt = $('#at60Dome');
			
		if (formElemt.find("input[value='1']").prop('checked'))//目标方位
		{
			var domePosition = formElemt.find("input[name='domePosition']").val();
			/* if ()
			{
				......
			} */
		}else if (formElemt.find("input[value='2']").prop('checked')){//转动速度
			var RotateSpeed = formElemt.find("input[name='RotateSpeed']").val();
			/* if ()
				{
					......
				} */
		}else if (formElemt.find("input[value='3']").prop('checked')){//风帘位置
			var shadePosition = formElemt.find("input[name='shadePosition']").val();
			/* if ()
				{
					......
				} */
		}else if (formElemt.find("input[value='4']").prop('checked')){//风帘运动
			var shadeAction = formElemt.find("select[name='shadeAction']").val();
			/* if ()
				{
					......
				} */
		}
	}//随动圆顶 表单数据验证函数 结束///////////////////////////////////
	 
	//圆顶 带参数指令 表单提交 JS事件///////////////////////////////
    $('#domeSbmt').click(function () {
        var form = $('#at60Dome');    //获取ccd表单元素
        var formData = new FormData(form[0]);
        //执行ajax
		if (true) //此处调用  checkSlaveDome()函数
		{
			$.ajax ({
              type: 'post',
              url : '/xinglong/at60/at60sDomeSendData',
              data : formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
                alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
             },
             error:  function () {
               alert('网络异常,请重新提交');
            },
          })
		}
             
    });
	
	//随动圆顶 表单提交按钮 hover //////////////////////////////////
   $('#domeSbmt').hover(
        function (){
            $(this).addClass("hover");
        }, 
        function (){
            $(this).removeClass("hover");
        }
   );
   
   //全开圆顶 连接按钮 js 事件/////////////////////////////////////
    var fDomeConnectFlag = 0; //此处代码 需要修改
   $('#fDomeConnect').click(function (){
       var e = $(this);
       fDomeConnectFlag ++;
       if (e.val() == '连接圆顶')   val = '1';
       if (e.val() == '断开圆顶')   val = '2';
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60fDomeSendData',
            data : {fDomeConnect:val,},             
            success:  function (info) {
               alert(info);
                if (fDomeConnectFlag % 2 == 1 && info.indexOf('成功') != -1)
                {//连接圆顶指令 发送成功
                    e.val('断开圆顶');
                }else if(fDomeConnectFlag % 2 == 0 && info.indexOf('成功') != -1){//断开圆顶指令 发送成功
                    e.val('连接圆顶');
                }
            },
            error:  function () {
               alert('网络异常,请再次点击该按钮!');
            },
        });
   });
   
   //全开圆顶 打开 onchange事件/////////////////////////////////
    var fDome = $('#fDomeConnect').next('select');
	
   fDome.change(function (){
	   var fDomeVal = fDome.val();
	   if (fDomeVal !== '')
	   {
		   $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60fDomeSendData',
            data : {openDome:fDomeVal,},             
            success:  function (info) {
			    alert(info); 
            },
            error:  function () {
               alert('网络异常,请再次选择该指令!');
            },
        });
	   }
		
   });
   
   //滤光片  连接按钮 js 事件/////////////////////////////////
   $('#filterConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFilter input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FilterSendData',
            data : {filterConnect:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次连接滤光片!');
            },
        });
   });
   
   //滤光片  断开按钮 js 事件/////////////////////////////////
   $('#filterDisConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFilter input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FilterSendData',
            data : {filterConnect:2,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次断开滤光片!');
            },
        });
   });
   
   //滤光片 找零按钮 js事件//////////////////////////////////
   $('#filterFindHome').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFilter input').not($(this)).removeClass('btnClick');
	   $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FilterSendData',
            data : {filterFindHome:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次进行找零!');
            },
        });
   });
   
    //滤光片 提交位置指令 js事件//////////////////////////////////
	$('#filterPosBtn').click(function () {
		var filterPosEle = $('#filterPosi');
		var filterPosVal = filterPosEle.val();
		$.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FilterSendData',
            data : {filterPos:filterPosVal,},             
            success:  function (info) {
               alert(info);
			   filterPosEle.val('');
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次提交滤光片位置!');
			   filterPosEle.val('');
            },
        });
	});
	
	//滤光片 表单提交按钮 hover //////////////////////////////////
   $('#filterPosBtn').hover(
        function (){
            $(this).addClass("hover");
        }, 
        function (){
            $(this).removeClass("hover");
        }
   );
	
})