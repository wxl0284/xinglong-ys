/** 望远镜控制页面js*/
 $(function () {
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
	var planInfo = $('#planInfo'); //观测计划的表格
	
	var vm = new Vue({//vue 实例化
		el: '#all',
		data: {
			configData: configData, //configData是后端返回的json数据
			device_nav: {//此对象中的数据用以区分是否给各子设备加上蓝色底框
				dev_click: 'gimbal',  //区分各自设备
				gimbal_command: '',   //区分转台各指令
				gimbal_btn: '',   //区分转台各按钮型指令
			},
			gimbal_form: {//转台表单指令的参数
				trackStar: {
					rightAscension1: '', rightAscension2: '', rightAscension3: '',
					epoch: '-1', speed: '-1', command:'trackStar', at:at
				},
			},
		},
		methods: {
			plan_click: function () {
				this.device_nav.dev_click = 'plan';
				planInfo.removeClass('displayNo');
			},
			gimbal_click: function () {
				this.device_nav.dev_click = 'gimbal';
				this.device_nav.gimbal_command = '';
				planInfo.addClass('displayNo');
			},
			gimbal_btn_command: function (connect) { //转台之 连接 断开 找零 复位 停止 急停
				var that = this; //存储vue实例化的对象
				var btn_str = ''; //控制按钮的样式
				var btn_text = '';
				var data = {at: at, command: ''}; //提交的数据

				switch (connect) {
					case 1:
						btn_str = 'connect';
						btn_text = '连接';
						data.command = 'connect';
						break;
					case 2:
						btn_str = 'disConnect';
						btn_text = '断开';
						data.command = 'disConnect';
						break;
					case 3:
						btn_str = 'findhome';
						btn_text = '找零';
						data.command = 'findhome';
						break;
					case 4:
						btn_str = 'park';
						btn_text = '复位';
						data.command = 'park';
						break;
					case 5:
						btn_str = 'stop';
						btn_text = '停止';
						data.command = 'stop';
						break;
					case 6:
						btn_str = 'emergstop';
						btn_text = '急停';
						data.command = 'emergstop';
						break;
					default:
						break;
				}
				//执行ajax
				$.ajax({
					type : 'post',
					url : '/gimbal',
					data : data,             
		            success: function (info) {
						that.device_nav.gimbal_btn = btn_str;  //更改按钮样式
						layer.alert(info, {
							shade:false,
							closeBtn:0,
							yes:function (n){
								layer.close(n);
								if (info.indexOf('登录') !== -1)
								{
									location.href = '/';
								}
							},
						});
		            },
		            error: function () {
			        	layer.alert('网络异常,请再次' + btn_text, {shade:false, closeBtn:0});
		            },
				});
			},/*转台之 连接 断开 找零 复位 停止 急停 结束*/
			ccd_click: function () {
				this.device_nav.dev_click = 'ccd';
				planInfo.addClass('displayNo');
			},
			filter_click: function () {
				this.device_nav.dev_click = 'filter';
				planInfo.addClass('displayNo');
			},
			sDome_click: function () {
				this.device_nav.dev_click = 'sDome';
				planInfo.addClass('displayNo');
			},
			oDome_click: function () {
				this.device_nav.dev_click = 'oDome';
				planInfo.addClass('displayNo');
			},
			focus_click: function () {
				this.device_nav.dev_click = 'focus';
				planInfo.addClass('displayNo');
			},
			guide_click: function () {
				this.device_nav.dev_click = 'guide';
				planInfo.addClass('displayNo');
			},
			handle_click: function () {
				this.device_nav.dev_click = 'handle';
				planInfo.addClass('displayNo');
			},
			spectrum_click: function () {
				this.device_nav.dev_click = 'spectrum';
				planInfo.addClass('displayNo');
			},
			pic_click: function () {
				this.device_nav.dev_click = 'pic';
				planInfo.addClass('displayNo');
			},
			gimbal_track_star_Asc1: function (tip) {
				var msg = '';
				var patn = /^\d{2}$/;
				var v = this.gimbal_form.trackStar.rightAscension1;
				if ( !patn.test(v) || v > 24 || v < 0 )
				{
					msg = '赤经小时参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.trackstar_asc1, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			gimbal_track_star_Asc2: function (tip) {
				var msg = '';
				var patn = /^\d{2}$/;
				var v = this.gimbal_form.trackStar.rightAscension2;
				if ( !patn.test(v) || v > 59 || v < 0 )
				{
					msg = '赤经分钟参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.trackstar_asc2, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			gimbal_track_star_Asc3: function (tip) {
				var msg = '';
				var v = this.gimbal_form.trackStar.rightAscension3;
				if ( !$.isNumeric(v) || v >= 60 || v < 0 )
				{
					msg = '赤经秒参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.trackstar_asc3, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			gimbal_Asc1_up:function () {
				var res = this.gimbal_track_star_Asc1(false);
				if ( res === '' ) this.$refs.trackstar_asc2.focus();
			},
			gimbal_Asc2_up:function () {
				var res = this.gimbal_track_star_Asc2(false);
				if ( res === '' ) this.$refs.trackstar_asc3.focus();
			},
			trackStar_sbmt:function () { //转台 跟踪恒星 指令提交
				var msg = '';
				msg += this.gimbal_track_star_Asc1(false);
				msg += this.gimbal_track_star_Asc2(false);
				msg += this.gimbal_track_star_Asc3(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{//表单参数无错误
					$.ajax({
						url: '1.php',
						type: 'post',
						data: this.gimbal_form.trackStar
					});/*ajax 结束*/
				}
			},/*转台 跟踪恒星 指令提交 结束*/
		},
	});/***************vue js结束*****************/

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
	/*将转台实时状态信息显示在页面*/
	function show_gimbal_status (info)
	{
		//date.html(info.date);
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

	}/*将转台实时状态信息显示在页面 结束*/

	/*将ccd实时状态信息显示在页面*/
	function show_ccd_status (info)
	{
		ccdStatus.html('CCD:' + info.ccdCurStatus);
		//如下为ccd可变属性
		ccdStatus_1.html(info.ccdCurStatus);
		baseLine.html(info.ccdBaseline);
		readMode.html(info.ccdReadOutMode);
		ObserveBand.html(info.ccdObserveBand);
		TargetRightAscension.html(info.ccdJ2000RightAscension);
		TargetDeclination.html(info.ccdJ2000Declination);
	}/*将ccd实时状态信息显示在页面 结束*/

	/*将调焦器实时状态信息显示在页面*/
	function show_focus_status (info)
	{
		focusStatus.html('调焦器:' + info.focusCurStatus);
		curPos.html(info.focusPosition);
		targetPosition.html(info.focusTargetPos);
		//找零状态
		focusIsHomed.html(info.focusIsHomed);
		//是否温度补偿
		compens.html(info.focusIsTCompensation);
		//温度补偿系数
		compensX.html(info.focusTCompenensation);
	}/*将调焦器实时状态信息显示在页面 结束*/

	/*将随动圆顶实时状态信息显示在页面*/
	function show_sDome_status (info)
	{
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
	}/*将随动圆顶实时状态信息显示在页面 结束*/

	/*将滤光片实时状态信息显示在页面*/
	function show_filter_status (info)
	{
		filterStatus.html('滤光片:' + info.filterCurstatus);
		filterStatus_1.html(info.filterCurstatus);
		filterIsHomed.html(info.filterIsHomed);
		filterErrStr.html(info.filterErrorStatus);
	}/*将滤光片实时状态信息显示在页面 结束*/

	function getStatus()
	{
		$.ajax({
			type : 'post',
			url : '/get_status', 
			data : {at: at,},         
            success:  function (info) {
				getStatusErr = 0; //将err变量重置为0
                var info = eval( '(' + info +')' );
				
				//显示转台状态信息
				if ( info.gimbal )
				{
					show_gimbal_status (info.gimbal);
				}

				//显示ccd状态信息
				if ( info.ccd )
				{
					show_ccd_status (info.ccd);
				}
				
				//显示调焦器状态信息
				if ( info.focus )
				{
					show_focus_status (info.focus);
				}
				
				//显示随动圆顶状态信息
				if ( info.sDome )
				{
					show_sDome_status (info.sDome);
				}
				
				//显示滤光片状态信息
				if ( info.filter )
				{
					show_filter_status (info.filter);
				}
            },/* success方法 结束*/
			error: function (){
				getStatusErr ++;
				if(getStatusErr <= 1)
				{
					layer.alert('网络异常,设备实时数据无法获取!', {shade:false, closeBtn:0});
				}
			},
		});
	}
	//setInterval (getStatus, 1800);  //实时显示各设备状态信息

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
   /* $('#devsNav table').on('click', 'a', function (){
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
    }); *///////////////////////////////////////////////////////
    
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
				layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
              },
              error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
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
	            layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次连接ccd!', {shade:false, closeBtn:0});
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
	             layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次断开ccd!', {shade:false, closeBtn:0});
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
	            layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次停止曝光!', {shade:false, closeBtn:0});
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
	            layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次终止曝光!', {shade:false, closeBtn:0});
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
				layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
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
	             layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次连接调焦器!', {shade:false, closeBtn:0});
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
	              layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次断开调焦器!', {shade:false, closeBtn:0});
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
	            layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次点击停止按钮!', {shade:false, closeBtn:0});
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
	           layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次点击找零按钮!', {shade:false, closeBtn:0});
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
	                layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
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
			       layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	             layer.alert('网络异常,请再次连接圆顶!', {shade:false, closeBtn:0});
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
	           layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次断开圆顶!', {shade:false,closeBtn:0});
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
	           layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次点击该按钮!', {shade:false,closeBtn:0});
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
	            layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次打开天窗!', {shade:false, closeBtn:0});
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
	             layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次关闭天窗!', {shade:false, closeBtn:0});
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
	               layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
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
				layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
                layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
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
	           layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次连接滤光片!', {shade:false, closeBtn:0});
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
	            layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次断开滤光片!', {shade:false, closeBtn:0});
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
	            layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	              layer.alert('网络异常,请再次进行找零!', {shade:false, closeBtn:0});
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
	            layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
					},
				});
            },
            error:  function () {
	            layer.alert('网络异常,请再次提交滤光片位置!', {shade:false, closeBtn:0});
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
			that.addClass('click');
			notThis.removeClass('click');			

			layer.alert(info, {
				shade:false,
				closeBtn:0,
				yes:function (n){
					layer.close(n);
					if (info.indexOf('登录') !== -1)
					{
						location.href = '/';
					}
				},
			});
        },
        error:  function () {
              layer.alert('网络异常,请再次点击镜盖操作!', {shade:false, closeBtn:0});
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
			that.addClass('click');
			notThis.removeClass('click');
			layer.alert(info, {
				shade:false,
				closeBtn:0,
				yes:function (n){
					layer.close(n);
					if (info.indexOf('登录') !== -1)
					{
						location.href = '/';
					}
				},
			});
       },
       error:  function () {
            layer.alert('网络异常,请再次发送同步数据指令!', {shade:false, closeBtn:0});
       },
	});
});
/****************转台之保存同步数据 js事件 结束***************/

})



