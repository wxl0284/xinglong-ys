/** 设备1js  at60*/
 $(function () {
 	//ajax 实时更新60cm望远镜子设备状态数据///////////////////////	
	var getStatusErr = 0;  //控制错误时，弹窗数量
	/******************如下定义变量 存储各个需实时更新数据的元素***************/
	var date = $('#date');
	var utcTime = $('#utcTime');
	var siderealTime = $('#siderealTime');
	var curstatus = $('#curstatus');
	var trackError = $('#trackError');
	var hourAngle = $('#hourAngle');
	var coverStatus = $('#coverStatus');
	var rightAscension = $('#rightAscension');
	var declination = $('#declination');
	var trackObjectName = $('#trackObjectName');
	var trackType = $('#trackType');
	var targetRightAscension = $('#targetRightAscension');
	var targetDeclination = $('#targetDeclination');
	var azmiuth = $('#azmiuth');
	var elevation = $('#elevation');
	var RightAscensionSpeed = $('#RightAscensionSpeed');
	var declinationSpeed = $('#declinationSpeed');
	var derotatorPositon = $('#derotatorPositon');
	var targetDerotatorPosition = $('#targetDerotatorPosition');
	var axis1TrackError = $('#axis1TrackError');
	var axis2TrackError = $('#axis2TrackError');
	var axis3TrackError = $('#axis3TrackError');
	var stamp = $('#stamp');
	var siderealTime_1 = $('#siderealTime_1');
	var hourAngle_1 = $('#hourAngle_1');
	var rightAscension_1 = $('#rightAscension_1');
	var declination_1 = $('#declination_1');
	var J2000RightAscension = $('#J2000RightAscension');
	var J2000Declination = $('#J2000Declination');
	var azmiuth_1 = $('#azmiuth_1');
	var elevation_1 = $('#elevation_1');
	var derotatorPositon_1 = $('#derotatorPositon_1');
	var targetRightAscension_1 = $('#targetRightAscension_1');
	var targetDeclination_1 = $('#targetDeclination_1');
	var targetJ2000RightAscension = $('#targetJ2000RightAscension');
	var targetJ2000Declination = $('#targetJ2000Declination');
	var targetDerotatorPosition_1 = $('#targetDerotatorPosition_1');
	var gimbPic = $('#gimbPic');
	var gimbStatus = $('#gimbStatus');
	var ccdStatus = $('#ccdStatus');
	var ccdStatus_1 = $('#ccdStatus_1');
	var baseLine = $('#baseLine');
	var readMode = $('#readMode');
	var ObserveBand = $('#ObserveBand');
	var TargetRightAscension = $('#TargetRightAscension');
	var TargetDeclination = $('#TargetDeclination');
	var focusStatus = $('#focusStatus');
	var curPos = $('#curPos');
	var targetPosition = $('#targetPosition');
	var focusIsHomed = $('#focusIsHomed');
	var compens = $('#compens');
	var compensX = $('#compensX');
	var domeStatus = $('#domeStatus');
	var domeStatus_1 = $('#domeStatus_1');
	var scuttle = $('#scuttle');
	var shadeStatus = $('#shadeStatus');
	var errorStr = $('#errorStr');
	var filterStatus = $('#filterStatus');
	var filterStatus_1 = $('#filterStatus_1');
	var filterIsHomed = $('#filterIsHomed');
	var filterErrStr = $('#filterErrStr');
	var planNum = $('#planNum');
	/******************如下定义变量 存储各个需实时更新数据的元素 结束**********/
	function getStatus()
	{
		$.ajax({
			type : 'post',
            url : '/xinglong/at60status/devsStatus',           
            success:  function (info) {
				getStatusErr = 0; //将err变量重置为0
                var info = eval( '(' + info +')' );
				date.html(info.date);
				utcTime.html(info.UTC);
				siderealTime.html(info.siderealTime);
				curstatus.html(info.curstatus); //转台状态
				trackError.html(info.trackError);
				hourAngle.html(info.hourAngle);
				coverStatus.html(info.coverStatus);
				rightAscension.html(info.rightAscension);
				declination.html(info.declination);
				trackObjectName.html(info.trackObjectName);
				trackType.html(info.trackType);
				targetRightAscension.html(info.targetRightAscension);
				targetDeclination.html(info.targetDeclination);
				azmiuth.html(info.azmiuth);//当前方位
				elevation.html(info.elevation);//当前俯仰
				RightAscensionSpeed.html(info.RightAscensionSpeed);
				declinationSpeed.html(info.declinationSpeed);
				derotatorPositon.html(info.derotatorPositon);
				targetDerotatorPosition.html(info.targetDerotatorPosition);
				axis1TrackError.html(info.axis1TrackError);
				axis2TrackError.html(info.axis2TrackError);
				axis3TrackError.html(info.axis3TrackError);
				//接下来为gimbal可变属性
				stamp.html(info.timeStamp); //时间戳
				siderealTime_1.html(info.siderealTime); //恒星时
				hourAngle_1.html(info.hourAngle); //时角
				rightAscension_1.html(info.rightAscension); //赤经
				declination_1.html(info.declination); //赤纬
				//J2000赤经
				J2000RightAscension.html(info.J2000RightAscension);
				//j2000赤纬
				J2000Declination.html(info.J2000Declination);
				azmiuth_1.html(info.azmiuth);//当前方位
				elevation_1.html(info.elevation);//当前俯仰
				//当前消旋位置
				derotatorPositon_1.html(info.derotatorPositon);
				//目标赤经
				targetRightAscension_1.html(info.targetRightAscension);
				//目标赤纬
				targetDeclination_1.html(info.targetDeclination);
				//目标j2000赤经
				targetJ2000RightAscension.html(info.targetJ2000RightAscension);
				//目标j2000赤纬
				targetJ2000Declination.html(info.targetJ2000Declination);
				//目标消旋位置 targetDerotatorPosition
				targetDerotatorPosition_1.html(info.targetDerotatorPosition);
				//gimbal可变属性 结束////////////////////////////
				
				//60cm各子设备状态//////////////////////////////////
				//转台状态////////////////////////////
				if (info.curstatus == '异常')
				{
					gimbPic.attr('src', '/static/images-1/error.jpg');
				}else{
					gimbPic.attr('src', '/static/images-1/ok.jpg');
				}
				gimbStatus.html('转台:' + info.curstatus);
				//转台状态结束/////////////////////////////
				
				//ccd状态/////////////////////////////////////
				/* if (info.curstatus == '异常')
				{
					$('#gimbPic').attr('src', '/static/images-1/error.jpg');
				} */
				ccdStatus.html('CCD:' + info.ccdCurStatus);
				//如下为ccd可变属性
				ccdStatus_1.html(info.ccdCurStatus);
				baseLine.html(info.ccdBaseline);
				readMode.html(info.ccdReadOutMode);
				ObserveBand.html(info.ccdObserveBand);
				TargetRightAscension.html(info.ccdJ2000RightAscension);
				TargetDeclination.html(info.ccdJ2000Declination);				
				//ccd 状态结束/////////////////////////////////
				focusStatus.html('调焦器:' + info.focusCurStatus);
				//如下为 调焦器可变属性/////////////////////////////////
				curPos.html(info.focusPosition);
				targetPosition.html(info.focusTargetPos);
				//找零状态
				focusIsHomed.html(info.focusIsHomed);
				//是否温度补偿
				compens.html(info.focusIsTCompensation);
				//温度补偿系数
				compensX.html(info.focusTCompenensation);
				//调焦器 状态///////////////////////////////////////////////
				//调焦器 状态 结束//////////////////////////////////////////
				
				//圆顶状态//////////////////////////////////////////////////
				/* if (info.curstatus == '异常')
				{
					$('#gimbPic').attr('src', '/static/images-1/error.jpg');
				}else{
					$('#gimbPic').attr('src', '/static/images-1/ok.jpg');
				} */
				domeStatus.html('圆顶:' + info.slaveDomeCurstatus);
				domeStatus_1.html(info.slaveDomeCurstatus);
				scuttle.html(info.slaveDomeScuttleStatus);//天窗状态
				shadeStatus.html(info.slaveDomeShadeStatus);//风帘状态
				errorStr.html(info.slaveDomeErrorStatus);//错误标识
				//圆顶状态 结束/////////////////////////////////////////////
				/* if (info.curstatus == '异常')
				{
					$('#gimbPic').attr('src', '/static/images-1/error.jpg');
				}else{
					$('#gimbPic').attr('src', '/static/images-1/ok.jpg');
				} */
				filterStatus.html('滤光片:' + info.filterCurstatus);
				filterStatus_1.html(info.filterCurstatus);
				filterIsHomed.html(info.filterIsHomed);
				filterErrStr.html(info.filterErrorStatus);
				//观测计划tag 
				planNum.html(info.planNum);
				//滤光片状态////////////////////////////////////////////////
				
				//滤光片状态 结束///////////////////////////////////////////
				//60cm各子设备状态结束//////////////////////////////
            },
			error: function (){
				getStatusErr ++;
				if(getStatusErr <= 1)
				{
					layer.alert('网络异常,设备实时数据无法获取!');
				}
			},
		});
	}
	//setInterval (getStatus, 1800);
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
    
/*//接管望远镜按钮 js事件 ///////////////////////////////////////
     $('#takeOver').click(function () {
       //var e = $(this);
   
       var t = $('.easyui-datetimespinner');//获取时间输入框
       var v1 = t.first().val();
       var v2 = t.last().val();
       
       if (!v1 || !v2)
       {
           layer.alert('请正确输入:起始/结束时间！');return;
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
     
    });//////////////////////////////////////////////////////////*/
	
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
		divs.not(commandDiv).hide();//隐藏其他指令
		commandDiv.show();//显示相应指令		
		commandDiv.click();//同时执行此指令的点击事件
	});
//每个设备 指令导航栏 nav下span 点击事件  结束///////////////////
	
//转台 连接按钮js事件/////////////////////////////////
    $('#gimbalConnect').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/gimbal',
			data : {connect:1,
				at:at,	//望远镜序号
			},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
              error:  function () {
	              layer.alert('网络异常,请再次连接！');
            },
        });
    }); 
	
//转台 断开连接按钮 js事件/////////////////////////////////
    $('#gimbalDisConnect').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/gimbal',
            data : {connect:2, at:at},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
              error:  function () {
	              layer.alert('网络异常,请再次断开连接！');
            },
        });
    }); 
	
//转台 找零按钮js事件//////////////////////////////////
    $('#gimbalFindhome').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/gimbal',
            data : {findHome:1,at:at},             
            success:  function (info) {
               layer.alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               layer.alert('网络异常,请再次点击：找零按钮!');
            },
        });
    });
    
//转台 复位按钮 js事件////////////////////////////////////
    $('#gimbalPark').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/gimbal',
            data : {park:1, at:at},             
            success:  function (info) {
               layer.alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               layer.alert('网络异常,请再次复位!');
            },
        });
    }); 
	 
//转台 停止按钮 js事件///////////////////////////////////
    $('#gimbalStop').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/gimbal',
            data : {stop:1, at:at},             
            success:  function (info) {
              layer.alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	            layer.alert('网络异常,请再次点击：停止按钮!');
            },
        });
    });  
	
//转台 急停按钮 js事件/////////////////////////////////////
    $('#gimbalEmergenceStop').click(function () {
		$(this).addClass('btnClick');
		$('#btnsGimbal input').not($(this)).removeClass('btnClick');
        $.ajax({
            type : 'post',
            url : '/gimbal',
            data : {EmergenceStop:1, at:at},             
            success:  function (info) {
	              layer.alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次点击：急停按钮!');
            },
        });
    });/////////////////////////////////////////// 
	
//转台 带参数指令  js事件///////////////////////////////////////
	var gimbalForm = $('#at60Gimbal');
    var gimbalSelect = gimbalForm.find('div');

    gimbalSelect.click(function () {
         $(this).find('input:radio').prop('checked', true);
         var notcheck = gimbalSelect.not($(this));
         notcheck.addClass('notCheck');
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
    
//验证转台 表单指令数据 逐一验证//////////////////////////////
	//验证 跟踪恒星-赤经之小时
	var inputIn1 = $('#inputIn1');
	var inputIn1_1 = $('#inputIn1_1');
	var inputIn1_2 = $('#inputIn1_2');
	
	inputIn1.keyup(function () {
		var patn = /^\d{2}$/;
		var v = $.trim($(this).val());
		if (patn.test(v))
		{
			$(this).blur();
		}
	});
	
	//赤经之小时 blur事件
	inputIn1.blur(function () {
		/*var patn = /^\d{1,2}$/;
		var v = $.trim($(this).val());
		var err = 0; //错误标识
		if (!patn.test(v) || v > 24 || v < 0)
		{
			err = 1;
			layer.tips('参数超限', $(this), {tips : 1,tipsMore: true});
		}else{
			inputIn1_1.focus();
		}
		
		$(this).data('err', err);*/
	});
	
	//赤经之分钟 js事件
	inputIn1_1.keyup(function () {
		var patn = /^\d{2}$/;
		var v = $.trim($(this).val());
		if (patn.test(v))
		{
			$(this).blur();
		}
	})
	
	//赤经之分钟 blur事件
	inputIn1_1.blur(function () {
		// var patn = /^\d{1,2}$/;
		// var v = $.trim($(this).val());
		// var err = 0;
		// if (!patn.test(v) || v < 0 || v >= 60)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限', $(this), {tips: 1, tipsMore: true});
		// }else{
		// 	inputIn1_2.focus();
		// }
	
		// $(this).data('err', err);
	})
	
	//赤经之秒 js事件
	inputIn1_2.blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		// if (!$.isNumeric(v) || v >= 60 || v < 0)
		// {
		// 	err = 1;
		// 	layer.tips('秒参数超限', $(this), {tips : 1,tipsMore: true});
		// }
		
		// $(this).data('err', err);
	})
	//验证 跟踪恒星-赤经 结束//////////////////////////
	
	//验证 跟踪恒星-赤纬之小时
	var inputIn2 = $('#inputIn2');
	var inputIn2_1 = $('#inputIn2_1');
	var inputIn2_2 = $('#inputIn2_2');
	
	inputIn2.keyup(function () {
		var v = $.trim($(this).val());
		v_R = v.replace(/-/, ''); //将-替换为空字符
		if (v_R.length == 2)
		{
			$(this).blur();
		}
	
	});
	
	//赤纬之小时 blur事件
	inputIn2.blur(function () {
		// var v = $.trim($(this).val());
		// var patn = /^-?\d{1,2}$/;
		// var err = 0;
		
		// if (!patn.test(v) || v > 90 || v < -90)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限', $(this), {tips : 1,tipsMore: true});
		// }else{
		// 	inputIn2_1.focus();
		// }
	
		// $(this).data('err', err);
	});
	
	//赤纬之分钟 js事件
	inputIn2_1.keyup(function () {
		var patn = /^\d{2}$/;
		var v = $.trim($(this).val());
		if (patn.test(v))
		{
			$(this).blur();
		}
	})
	
	//赤纬之分钟 blur事件
	inputIn2_1.blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		// var patn = /^\d{1,2}$/;
		
		// if (!patn.test(v) || v > 59 || v < 0)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限', $(this), {tips : 1,tipsMore: true});
		// }else{
		// 	inputIn2_2.focus();
		// }
		
		// $(this).data('err', err);
	});
	
	//赤纬之秒 js事件
	inputIn2_2.blur(function () {
		// var err = 0;
		// var v = $.trim($(this).val());
		
		// if (!$.isNumeric(v) || v >= 60 || v < 0)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限', $(this), {tips : 1,tipsMore: true});
		// }		
		// $(this).data('err', err);
	})
	//验证 跟踪恒星-赤纬 结束//////////////////////////
	
	//验证 设置目标名称//////////////////////////////////
	$('#objectNameInput').blur(function () {
		// var v = $.trim($(this).val());
		// var patn = /([\u4e00-\u9fa5]| )+/;
		// var err = 0;
		
		// if (patn.test(v) || v == '')
		// {
		// 	err = 1;
		// 	layer.tips('名称不能有汉字或空格!', $(this), {tipsMore: true});
		// }		
		// $(this).data('err', err);
	});////验证 设置目标名称 结束//////////////////////////
	
	//验证 指向固定位置 之方位和俯仰/////////////////////////////
	//方位
	$('#azimuth').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if (!$.isNumeric(v) || v < 0 || v > 360)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限!', $(this), {tipsMore: true});
		// }		
		// $(this).data('err', err);
	});
	
	//俯仰
	$('#elevationV').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if (!$.isNumeric(v)  || v < minelevationval || v > 90)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限!', $(this), {tipsMore: true});
		// }		
		// $(this).data('err', err);
	});
	//验证 指向固定位置 之方位和俯仰 结束//////////////////////////
	
	//验证 轴3指向固定位置 //////////////////////////////////
	$('#slewDerotatorV').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if ((!$.isNumeric(v)) || v < 0 || v > 360)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限!', $(this), {tipsMore: true});
		// }
		
		// $(this).data('err', err);
	});////验证 轴3指向固定位置 结束//////////////////////////
	
	//验证 速度修正-轴 //////////////////////////////////
	$('#speedXInput').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if (!(v == 1 || v == 2))
		// {
		// 	err = 1;
		// 	layer.tips('轴设置有误!', $(this), {tipsMore: true});
		// }		
		// $(this).data('err', err);
	});////验证 速度修正-轴 结束//////////////////////////
	
	//验证 速度修正-速度 //////////////////////////////////
	$('#speedCorrect').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if ((!$.isNumeric(v)) || (v > 1 || v < -1))
		// {
		// 	err = 1;
		// 	layer.tips('速度设置有误!', $(this), {tipsMore: true});
		// }
		
		// $(this).data('err', err);
	});////验证 速度修正-速度 结束//////////////////////////
	
	//验证 恒速运动-轴 //////////////////////////////////
	$('#speedFAxis').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if (!(v == 1 || v == 2))
		// {
		// 	layer.tips('轴设置有误!', $(this), {tipsMore: true});
		// }
		
		// $(this).data('err', err);
	});////验证 恒速运动-轴 结束//////////////////////////
	
	//验证 恒速运动-速度 //////////////////////////////////
	$('#speedFVal').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if ((!$.isNumeric(v)) || (v > 1 || v < -1))
		// {
		// 	err = 1;
		// 	layer.tips('速度设置有误!', $(this), {tipsMore: true});
		// }
		
		// $(this).data('err', err);
	});////验证 恒速运动-速度 结束//////////////////////////
	
	//验证 位置修正-轴 //////////////////////////////////
	$('#PositionCorrectAxis').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if (!(v == 1 || v == 2))
		// {
		// 	err = 1;
		// 	layer.tips('轴设置有误!', $(this), {tipsMore: true});
		// }
		
		// $(this).data('err', err);
	});////验证 位置修正-轴 结束//////////////////////////
	
	//验证 位置修正-速度 //////////////////////////////////
	$('#PositionCorrectVal').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if ((!$.isNumeric(v)) || (v > 1 || v < -1))
		// {
		// 	err = 1;
		// 	layer.tips('速度设置有误!', $(this), {tipsMore: true});
		// }
		
		// $(this).data('err', err);
	});////验证 位置修正-速度 结束//////////////////////////
//验证转台 表单指令数据 结束/////////////////////////////////////
var gimbal_form = $('#at60Gimbal');    //获取转台表单元素
//转台 带参数指令 表单提交//////////////////////////////////
    $('#gimbalSbmt').click(function () {
		var err = 0; //错误标识
		var textE = gimbal_form.children('div:not(.notCheck)').find('input.blur');
		textE.each(function () {
			$(this).blur();
			err += $(this).data('err');
		});
		
		if (err > 0){
			return;  //指令输入有误 不提交
		}
		
        var gimbal_formData = new FormData(gimbal_form[0]);  //将jquery对象转为js-dom对象
		gimbal_formData.append('at', at);
		//执行ajax
			$.ajax ({
              type: 'post',
              url : '/gimbal',
              data : gimbal_formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
				layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
              },
              error:  function () {
	              layer.alert('网络异常,请重新提交');
              },
        	});
	});
//转台 带参数指令 表单提交////////////////////////////////// 
  
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
            url : '/ccd',
            data : {
				ccdConnect:1,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次连接ccd!');
            },
        });
   });
   
   //CCD 断开按钮 js事件///////////////////////////////////
   $('#ccdDisConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsCCD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/ccd',
            data : {
				ccdConnect:2,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次断开ccd!');
            },
        });
   });
   
   //CCD 停止曝光 js事件/////////////////////////////////////
   $('#ccdStopExpose').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsCCD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/ccd',
			data : {
				StopExpose:1,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次停止曝光!');
            },
        });
   });
   
   //CCD 终止曝光 js事件/////////////////////////////////////
   $('#ccdAbortExpose').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsCCD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/ccd',
            data : {
				AbortExpose:1,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次终止曝光!');
            },
        });
   });
   
//CCD 带参数指令  js事件//////////////////////////////////////////////
	var ccdForm = $('#at60Ccd');
    var ccdSelect = ccdForm.find('div');

    ccdSelect.click(function () {
         $(this).find('input[name="command"]').prop('checked', true);
         var notcheck = ccdSelect.not($(this));
         notcheck.addClass('notCheck');
         $(this).removeClass('notCheck');
     });
	
//ccd 表单数据验证////////////////////////////////////////////
	//验证制冷温度
	$('#ccdTemperature').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if ((!$.isNumeric(v)) || v > 20 || v < -80)
		// {
		// 	err = 1;
		// 	layer.tips('制冷温度值有误!', $(this), {tipsMore: true});
		// }		
		// $(this).data('err', err);
	});
	
	//验证曝光时间
	$('#durationInput').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if ((!$.isNumeric(v)) || v < 0)
		// {
		// 	err = 1;
		// 	layer.tips('曝光时间值有误!', $(this), {tipsMore: true});
		// }		
		// $(this).data('err', err);
	});
	
	//验证delay-延迟时间
	$('#delayInput').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// if ((!$.isNumeric(v)) || v < 0)
		// {
		// 	err = 1;
		// 	layer.tips('延迟时间值有误!', $(this), {tipsMore: true});
		// }		
		// $(this).data('err', err);
	});
	
	//验证 目标名称
	$('#ccdObjectName').blur(function () {
		// var v = $.trim($(this).val());
		// var patn = /([\u4e00-\u9fa5]| )+/;
		// var err = 0;
		
		// if (patn.test(v) || v == '')
		// {
		// 	err = 1;
		// 	layer.tips('名称不能有汉字或空格空格!', $(this), {tipsMore: true});
		// }		
		// $(this).data('err', err);
	});
	
	//验证 曝光策略之赤经 赤纬///////////////////////////////
	//验证 赤经之小时
	var inputIn3 = $('#inputIn3');
	var inputIn3_1 = $('#inputIn3_1');
	var inputIn3_2 = $('#inputIn3_2');
	
	//赤经之小时 keyup事件
	inputIn3.keyup(function () {
		var patn = /^\d{2}$/;
		var v = $.trim($(this).val());
		if (patn.test(v))
		{
			$(this).blur();
		}
	});
	
	//赤经之小时 blur事件
	inputIn3.blur(function () {
		// var patn = /^\d{1,2}$/;
		// var v = $.trim($(this).val());
		// var err = 0; //错误标识
		// if (!patn.test(v) || v > 24 || v < 0)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限', $(this), {tips : 1,tipsMore: true});
		// }else{
		// 	inputIn3_1.focus();
		// }
		
		// $(this).data('err', err);
	});
	
	//赤经之分钟 js事件
	inputIn3_1.keyup(function () {
		var patn = /^\d{2}$/;
		var v = $.trim($(this).val());
		if (patn.test(v))
		{
			$(this).blur();
		}
	})
	
	//赤经之分钟 blur事件
	inputIn3_1.blur(function () {
		// var patn = /^\d{1,2}$/;
		// var v = $.trim($(this).val());
		// var err = 0;
		// if (!patn.test(v) || v < 0 || v >= 60)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限', $(this), {tips: 1, tipsMore: true});
		// }else{
		// 	inputIn3_2.focus();
		// }
	
		// $(this).data('err', err);
	})
	
	//赤经之秒 js事件
	inputIn3_2.blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		// if (!$.isNumeric(v) || v >= 60 || v < 0)
		// {
		// 	err = 1;
		// 	layer.tips('秒参数超限', $(this), {tips : 1,tipsMore: true});
		// }		
		// $(this).data('err', err);
	})
	//验证 赤经 结束//////////////////////////
	
	//验证 -赤纬之小时
	var inputIn4 = $('#inputIn4');
	var inputIn4_1 = $('#inputIn4_1');
	var inputIn4_2 = $('#inputIn4_2');
	
	//赤纬之小时 keyup事件
	inputIn4.keyup(function () {
		var v = $.trim($(this).val());
		v_R = v.replace(/-/, ''); //将-替换为空字符
		if (v_R.length == 2)
		{
			$(this).blur();
		}
	
	});
	
	//赤纬之小时 blur事件
	inputIn4.blur(function () {
		// var v = $.trim($(this).val());
		// var patn = /^-?\d{1,2}$/;
		// var err = 0;
		
		// if (!patn.test(v) || v > 90 || v < -90)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限', $(this), {tips : 1,tipsMore: true});
		// }else{
		// 	inputIn4_1.focus();
		// }
	
		// $(this).data('err', err);
	});
	
	//赤纬之分钟 js事件
	inputIn4_1.keyup(function () {
		var patn = /^\d{2}$/;
		var v = $.trim($(this).val());
		if (patn.test(v))
		{
			$(this).blur();
		}
	
	})
	
	//赤纬之分钟 blur事件
	inputIn4_1.blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		// var patn = /^\d{1,2}$/;
		
		// if (!patn.test(v) || v > 59 || v < 0)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限', $(this), {tips : 1,tipsMore: true});
		// }else{
		// 	inputIn4_2.focus();
		// }
		
		// $(this).data('err', err);
	});
	
	//赤纬之秒 js事件
	inputIn4_2.blur(function () {
		// var err = 0;
		// var v = $.trim($(this).val());
		
		// if (!$.isNumeric(v) || v >= 60 || v < 0)
		// {
		// 	err = 1;
		// 	layer.tips('参数超限', $(this), {tips : 1,tipsMore: true});
		// }		
		// $(this).data('err', err);
	})
	//验证 曝光策略之赤经 赤纬 结束//////////////////////////
	
	//验证 拍摄波段
	$('#objectBandInput').blur(function () {
		// var v = $.trim($(this).val());
		// var err = 0;
		
		// /* if (v1 == '' || v1 < 90 || v1 > 90 || !$.isNumeric(v1))
		// {
		// 	layer.tips('小时之数据输入有误!', $(this), {tipsMore: true});
		// } */

		// $(this).data('err', err);
	});
	
	//验证 帧序号
	$('#frameSequenceIn').blur(function () {
		/* var v = $.trim($(this).val());
		var err = 0;
		
		if (v1 == '' || v1 < 90 || v1 > 90 || !$.isNumeric(v1))
		{
			layer.tips('小时之数据输入有误!', $(this), {tipsMore: true});
		} 

		$(this).data('err', err);*/
	});
	
	//验证 读出速度模式值
	$('#ReadSpeedModeIn').blur(function () {
		/*var v = $.trim($(this).val());
		var patn = /^[1-9]$/;
		var err = 0;
		
		if (!patn.test(v))
		{
			err = 1;
			layer.tips('模式输入有误!', $(this), {tipsMore: true});
		}
		$(this).data('err', err);*/
	});
	
	//验证 转移速度值
	$('#TransferSpeedIn').blur(function () {
		/*var v = $.trim($(this).val());
		var patn = /^[1-9]$/;
		var err = 0;
		
		if (!patn.test(v))
		{
			err = 1;
			layer.tips('转移速度输入有误!', $(this), {tipsMore: true});
		}
		$(this).data('err', err);*/
	});
	
	//验证 设置Roi //////////////////////////////////////
	$('#startX').blur(function () {
		/*var v = $.trim($(this).val());		
		var patn = /^\d+$/; //必须为>=0的整数
		var err = 0;
		
		if (!patn.test(v))
		{
			err = 1;
			layer.tips('startX输入有误!', $(this), {tipsMore: true});
		}

		$(this).data('err', err);*/
	});
	
	$('#startY').blur(function () {
		/*var v = $.trim($(this).val());		
		var patn = /^\d+$/; //必须为>=0的整数
		var err = 0;
		
		if (!patn.test(v))
		{
			err = 1;
			layer.tips('startY输入有误!', $(this), {tipsMore: true});
		}			
		$(this).data('err', err);*/
	});
	
	$('#imageWidth').blur(function () {
		/*var v = $.trim($(this).val());		
		var patn = /^\d+$/; //必须为>=0的整数
		var err = 0;
		
		if (!patn.test(v))
		{
			err = 1;
			layer.tips('imageWidth输入有误!', $(this), {tipsMore: true});
		}			
		$(this).data('err', err);*/
	});
	
	$('#imageHeight').blur(function () {
		/*var v = $.trim($(this).val());		
		var patn = /^\d+$/; //必须为>=0的整数
		var err = 0;
		
		if (!patn.test(v))
		{
			err = 1;
			layer.tips('imageHeight输入有误!', $(this), {tipsMore: true});
		}			
		$(this).data('err', err);*/
	});
	//验证 设置Roi 结束////////////////////////////////////
	
	//验证 EmValue
	$('#eMValueIn').blur(function () {
		/*var v = $.trim($(this).val());		
		var patn = /^\d+$/; //必须为>=0的整数
		var err = 0;
		if (!patn.test(v))
		{
			err = 1;
			layer.tips('EmValue输入有误!', $(this), {tipsMore: true});
		}
		$(this).data('err', err);*/
	});
	
	//验证 baselineValue
	$('#baselineValueIn').blur(function () {
		/*var v = $.trim($(this).val());		
		var patn = /^\d+$/; //必须为>=0的整数
		var err = 0;
		
		if (!patn.test(v))
		{
			err = 1;
			layer.tips('此值输入有误!', $(this), {tipsMore: true});
		}
		$(this).data('err', err);*/
	});
//ccd 表单数据验证 结束/////////////////////////////////////////
	var ccd_form = $('#at60Ccd');    //获取ccd表单元素
//CCD 带参数指令 表单提交 JS事件///////////////////////////////
    $('#ccdSbmt').click(function () {
		var err = 0; //错误标识
		var textE = ccd_form.children('div:not(.notCheck)').find('input.blur');
		textE.each(function () {
			$(this).blur();
			err += $(this).data('err');
		});
		if (err > 0){
			return;  //指令输入有误 不提交
		}
		
		var ccd_formData = new FormData(ccd_form[0]);
		ccd_formData.append('at', at);
        //执行ajax
			$.ajax ({
              type: 'post',
              url : '/ccd',
              data : ccd_formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
				layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交');
            },
          })    
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
   
//调焦器 带参数指令  js事件///////////////////////////////////////
	var focusForm = $('#at60Focus');
    var focusSelect = focusForm.find('div');

    focusSelect.click(function () {
         $(this).find('input[name="command"]').prop('checked', true);
         var notcheck = focusSelect.not($(this));
         notcheck.addClass('notCheck');
         $(this).removeClass('notCheck');
     });
   
//调焦器 连接按钮 js事件///////////////////////////////////
   $('#focusConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFocus input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/focus',
            data : {
				focusConnect:1,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次连接调焦器!');
            },
      });
   });
   
//调焦器 断开按钮 js事件///////////////////////////////////
   $('#focusDisConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFocus input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/focus',
            data : {
				focusConnect:2,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次断开调焦器!');
            },
        });
   });
   
//调焦器 停止运动按钮 js事件///////////////////////////////////
   $('#focusStop').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFocus input').not($(this)).removeClass('btnClick');
	   $.ajax({
            type : 'post',
            url : '/focus',
            data : {
				focusStop:1,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次点击停止按钮!');
            },
        });
   });
   
//调焦器 找零按钮 js事件///////////////////////////////////
   $('#focusFindHome').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFocus input').not($(this)).removeClass('btnClick');
	   $.ajax({
            type : 'post',
            url : '/focus',
            data : {
				findHome:1,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次点击找零按钮!');
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
	//验证 目标位置
	$('#setPositionIn').blur(function () {
		/*var v = $.trim($(this).val());	
		var err = 0;
		
		if (!$.isNumeric(v) || v < 0)
		{
			err = 1;
			layer.tips('此值输入有误!', $(this), {tipsMore: true});
		}
		$(this).data('err', err);*/
	});
	
	//验证 恒速运动
	$('#speedIn').blur(function () {
		/*var v = $.trim($(this).val());	
		var err = 0;
		
		if (!$.isNumeric(v) || v <= 0)
		{
			err = 1;
			layer.tips('此值输入有误!', $(this), {tipsMore: true});
		}
		$(this).data('err', err);*/
	});
	
	//验证 温度补偿系数
	$('#coefficientIn').blur(function () {
		/*var v = $.trim($(this).val());	
		var err = 0;
		
		if (!$.isNumeric(v) || v == 0)
		{
			err = 1;
			layer.tips('此值输入有误!', $(this), {tipsMore: true});
		}
		$(this).data('err', err);*/
	});
//调焦器 表单数据验证 结束/////////////////////////////////////////
var focus_form = $('#at60Focus');    //获取focus表单元素  
//调焦器 带参数指令 表单提交 JS事件///////////////////////////////
    $('#focusSbmt').click(function () {
		var err = 0;
        //var form = $('#at60Focus');    //获取focus表单元素
        var textE = focus_form.children('div:not(.notCheck)').find('input.blur');
		textE.each(function () {
			$(this).blur();
			err += $(this).data('err');
		});
		
		if (err > 0){
			return;  //指令输入有误 不提交
		}
		
		var focus_formData = new FormData(focus_form[0]);
		focus_formData.append('at',at);
		$.ajax ({
             type: 'post',
             url : '/focus',
             data : focus_formData,
             processData : false,
             contentType : false,  
             success:  function (info) {
                layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交');
            },
         })        
    });
	
//随动圆顶 连接按钮 js事件///////////////////////////////////
   $('#sDomeConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsSlaveD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/slavedome',
            data : {
				sDomeConnect:1,
				at : at,
			},             
            success:  function (info) {
	              layer.alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次连接圆顶!');
            },
        });
   });
   
//随动圆顶 断开按钮 js事件///////////////////////////////////
   $('#sDomeDisConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsSlaveD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/slavedome',
            data : {
				sDomeConnect:2,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次断开圆顶!');
            },
        });
   });
   
//随动圆顶 停止运动按钮 js事件///////////////////////////////////
   $('#sDomeStop').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsSlaveD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/slavedome',
			data : {
				sDomeStop:1,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次点击该按钮!');
           },
        });
   });
   
//随动圆顶 打开天窗 按钮 js事件///////////////////////////////////
   $('#sDomeScuttle').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsSlaveD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/slavedome',
            data : {
				OpenScuttle:1,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
               if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次打开天窗!');
            },
        });
   });
   
//随动圆顶 关闭天窗 按钮 js事件///////////////////////////////////
   $('#sDomeScuttleClose').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsSlaveD input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/slavedome',
            data : {
				OpenScuttle:2,
				at: at,
			},             
            success:  function (info) {
	              layer.alert(info);
               if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次关闭天窗!');
            },
        });
   });
	
//随动圆顶 带参数指令  js事件//////////////////////////////////////
	var domeForm = $('#at60Dome');
    var domeSelect = domeForm.find('div');

    domeSelect.click(function () {
         $(this).find('input:radio').prop('checked', true);
         var notcheck = domeSelect.not($(this));
         notcheck.addClass('notCheck');
         $(this).removeClass('notCheck');
     });
	 
//随动圆顶 表单数据验证//////////////////////////////////////////
	//验证 目标方位
	$('#domePositionIn').blur(function () {
		/*var v = $.trim($(this).val());	
		var err = 0;
		
		if (!$.isNumeric(v) || v < 0 || v > 360)
		{
			err = 1;
			layer.tips('此值输入有误!', $(this), {tipsMore: true});
		}
		$(this).data('err', err);*/
	});
	
	//验证 转动速度
	$('#RotateSpeedIn').blur(function () {
		/*var v = $.trim($(this).val());	
		var err = 0;
		
		if (!$.isNumeric(v) || v <= 0)
		{
			err = 1;
			layer.tips('此值输入有误!', $(this), {tipsMore: true});
		}
		$(this).data('err', err);*/
	});
	
	//验证 风帘位置
	$('#shadePositionIn').blur(function () {
		/*var v = $.trim($(this).val());	
		var err = 0;
		
		if (!$.isNumeric(v) || v < 0 || v > 90)
		{
			err = 1;
			layer.tips('此值输入有误!', $(this), {tipsMore: true});
		}
		$(this).data('err', err);*/
	});
//随动圆顶 表单数据验证 结束//////////////////////////////////////////
var sDome_form = $('#at60Dome');    //获取圆顶表单元素
//随动圆顶 带参数指令 表单提交 JS事件///////////////////////////////
    $('#domeSbmt').click(function () {
		var err = 0;
        //var form = $('#at60Dome');    //获取ccd表单元素
		var textE = sDome_form.children('div:not(.notCheck)').find('input.blur');
		textE.each(function () {
			$(this).blur();
			err += $(this).data('err');
		});
		
		if (err > 0){
			return;  //指令输入有误 不提交
		}
		
		var sDome_formData = new FormData(sDome_form[0]);
		sDome_formData.append('at', at);
        
		$.ajax ({
              type: 'post',
              url : '/slavedome',
              data : sDome_formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
                layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交');
            },
        })           
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
   
   //全开圆顶 按钮 js 事件/////////////////////////////////////
   var oDome_command = $('#oDome_command');
   //var oDome_btn = oDome_command.children('input');
   var oDome_btn = oDome_command.children('input'); //所有的按钮
   var oDome_btn_3 = oDome_command.children('input:gt(1)'); //后面3个按钮
   /****************全开圆顶 连接/断开****************/
   oDome_command.on('click', 'input', function () {
	    var that = $(this);
	    that.addClass('btnClick');
		oDome_btn.not(that).removeClass('btnClick');
		var v = that.val();
		var fDomeConnect = ''; //连接/断开 指令的参数
		var openDome = ''; //打开/关闭/停止 指令的参数

		if ( v == '连接圆顶')
		{
			fDomeConnect = 1; 
		}else if ( v == '断开圆顶' ){
			fDomeConnect = 2;
		}else if ( v == '打开' ){
			openDome = 1;
		}else if ( v == '关闭' ){
			openDome = 0;
		}else if ( v == '停止运动' ){
			openDome = 2;
		}

		$.ajax ({
            type: 'post',
            url : '/opendome',
            data : {
				at : at,
				fDomeConnect : fDomeConnect,
				openDome : openDome
			},
            success:  function (info) {
				layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
                layer.alert('网络异常,请重新提交');
           },
       });
   })/****************全开圆顶 连接/断开   结束****************/
//     var fDomeConnectFlag = 0; //此处代码 需要修改
//    $('#fDomeConnect').click(function (){
//        var e = $(this);
//        fDomeConnectFlag ++;
//        if (e.val() == '连接圆顶')   val = '1';
//        if (e.val() == '断开圆顶')   val = '2';
//        $.ajax({
//             type : 'post',
//             url : '/opendome',
//             data : {fDomeConnect:val,},             
//             success:  function (info) {
// 	              layer.alert(info);
//                 if (fDomeConnectFlag % 2 == 1 && info.indexOf('成功') != -1)
//                 {//连接圆顶指令 发送成功
//                     e.val('断开圆顶');
//                 }else if(fDomeConnectFlag % 2 == 0 && info.indexOf('成功') != -1){//断开圆顶指令 发送成功
//                     e.val('连接圆顶');
//                 }
//             },
//             error:  function () {
// 	              layer.alert('网络异常,请再次点击该按钮!');
//             },
//         });
//    });
   
   //全开圆顶 打开 onchange事件/////////////////////////////////
//     var fDome = $('#fDomeConnect').next('select');
	
//    fDome.change(function (){
// 	   var fDomeVal = fDome.val();
// 	   if (fDomeVal !== '')
// 	   {
// 		   $.ajax({
//             type : 'post',
//             url : '/opendome',
//             data : {openDome:fDomeVal,},             
//             success:  function (info) {
// 			    layer.alert(info); 
//             },
//             error:  function () {
// 	              layer.alert('网络异常,请再次选择该指令!');
//             },
//         });
// 	   }
		
//    });
   
   //滤光片  按钮 js 事件 结束/////////////////////////////////
   $('#filterConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFilter input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/filter',
            data : {filterConnect:1,at:at,},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次连接滤光片!');
            },
        });
   });
   
   //滤光片  断开按钮 js 事件/////////////////////////////////
   $('#filterDisConnect').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFilter input').not($(this)).removeClass('btnClick');
       $.ajax({
            type : 'post',
            url : '/filter',
            data : {filterConnect:2,at:at,},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次断开滤光片!');
            },
        });
   });
   
   //滤光片 找零按钮 js事件//////////////////////////////////
   $('#filterFindHome').click(function (){
	   $(this).addClass('btnClick');
	   $('#btnsFilter input').not($(this)).removeClass('btnClick');
	   $.ajax({
            type : 'post',
            url : '/filter',
            data : {filterFindHome:1,at:at,},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次进行找零!');
            },
        });
   });
   
	//滤光片 提交位置指令 js事件//////////////////////////////////
	var filterPosEle = $('#filterPosi');
	$('#filterPosBtn').click(function () {
		var filterPosVal = filterPosEle.val();
		$.ajax({
            type : 'post',
            url : '/filter',
            data : {filterPos:filterPosVal,at:at,},             
            success:  function (info) {
	              layer.alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
	              layer.alert('网络异常,请再次提交滤光片位置!');
			   filterPosEle.val('0');
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
	
//观测计划 执行模式 js /////////////////////////////////////
	$('#modeSpan').hover(
		function (){
			$('#modeVal').show();
			var planMode = $('#modeVal input');
			//观测计划 若为single和singleLoop 隐藏‘下一个’按钮/////
			if(planMode.eq(0).prop('checked') || planMode.eq(1).prop('checked'))
			{
				$('#planNext').hide();
			}
			
		}, 
		function (){
			$('#modeVal').hide();
		}
	);
//观测计划 执行模式 js结束 /////////////////////////////////////

//观测计划 若为single和singleLoop 隐藏‘下一个’按钮///////////////
	//var planMode = $('#modeVal input');
	//console.log(planMode);return;
	
	/* if(planMode.eq(0).prop('checked') || planMode.eq(1).prop('checked'))
	{
		//layer.alert(planMode[0])
		$('#planNext').hide();
	}else if(planMode.eq(2).prop('checked') || planMode.eq(3).prop('checked')){
		$('#planNext').show();
	} */
	$('#modeVal').on('click', 'input', function () {
		if($(this).val() == 3 || $(this).val() == 4)
		{
			$('#planNext').show();
		}
		
		if($(this).val() == 1 || $(this).val() == 2)
		{
			$('#planNext').hide();
		}
	});

//观测计划 若为single和singleLoop 隐藏‘下一个’按钮 结束/////////

/******************转台之镜盖操作指令 js事件**********************/
$('#coverOp').on('click', 'input:button', function () {
	var val;  //不同按钮对应的提交数据值
	var that = $(this); //当前被点击的元素
	var notThis = that.siblings('input[type="button"]'); //另外2个按钮
	var v = that.val();

	if (v == '打开')
	{
		val = 1;
	}else if (v == '关闭'){
		val = 2;
	}else if (v == '停止'){
		val = 0;
	}
	$.ajax({
		url: '/gimbal',
		type: 'post',
		data: {
			command: 9,
			openCover: val,
			at: at,
		},
		success:  function (info) {
            layer.alert(info);
			that.addClass('click');
			notThis.removeClass('click');
			if (info.indexOf('登录') !== -1)
			{
				location.href = '/';
			}
            },
            error:  function () {
	              layer.alert('网络异常,请再次点击镜盖操作!');
            },
	});
});
/******************转台之镜盖操作指令 js事件 结束*****************/

/******************转台之保存同步数据 js事件**********************/
$('#saveData').on('click', 'input:button', function () {
	var val;  //不同按钮对应的提交数据值
	var that = $(this); //当前被点击的元素
	var notThis = that.siblings('input[type="button"]'); //另外1个按钮
	var v = that.val();

	if (v == '是')
	{
		val = 1;
	}else if (v == '否'){
		val = 0;
	}
	$.ajax({
		url: '/gimbal',
		type: 'post',
		data: {
			command: 11,
			saveSyncData: val,
			at: at,
		},
		success:  function (info) {
            layer.alert(info);
			that.addClass('click');
			notThis.removeClass('click');
			if (info.indexOf('登录') !== -1)
			{
				location.href = '/';
			}
            },
            error:  function () {
	              layer.alert('网络异常,请再次发送同步数据指令!');
            },
	});
});
/****************转台之保存同步数据 js事件 结束***************/
})