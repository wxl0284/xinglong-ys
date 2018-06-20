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
			ccd_config:configData.ccd[0], //此对象存储ccd的配置数据
			ccd_name:'CCD1',
			device_nav: {//此对象中的数据用以区分是否给各子设备加上蓝色底框
				dev_click: 'gimbal',  //区分各自设备
				gimbal_command: '',   //区分转台各指令
				ccd_command: '',   //区分ccd各指令
				gimbal_btn: '',   //区分转台各按钮型指令
				polarizingAngle: '',   //轴3工作模式之 是否显示起偏角
				speed_alter_btn: '',   //速度修正 标记8个按钮是否被点击
				pos_alter_btn: '',   //位置修正 标记8个按钮是否被点击
				cover_btn: '',   //镜盖操作 标记3个按钮是否被点击
				save_data_btn: '',   //保存同步数据 标记按钮是否被点击
				ccd_btn: '',   //区分ccd各按钮型指令
				select_ccd: '', //是否显示ccd下拉框
				ccdNo: '-1', //ccd序号
			},
			gimbal_form: {//转台表单指令的参数
				trackStar: {
					rightAscension1: '', rightAscension2: '', rightAscension3: '',
					epoch: '-1', speed: '-1', command:'trackStar', at:at
				},
				objName: {
					objectName: '', objectType: '-1', command:'set_obj_name', at:at
				},
				slewAzEl: {
					azimuth: '', elevation: '', command:'slewAzEl', at:at
				},
				slewDerotator: {
					slewDerotator: '', command:'slewDerotator', at:at
				},
				axis3_mode: {
					mode: '-1', polarizingAngle: '', command:'axis3Mode', at:at
				},
				speed_alter: {//速度修正指令
					axis: '', correction: '-1', command:'speed_alter', at:at
				},
				speed_fixed: {//恒速运动
					axis: '1', speed: '', command:'speed_fixed', at:at
				},
				position_alter: {//恒速运动
					axis: '', correction: '-1', command:'position_alter', at:at
				},
				cover_op: {//镜盖操作
					operation: '', command:'cover_op', at:at
				},
				setFocusType: {//焦点切换镜
					focusType: '-2', command:'setFocusType', at:at
				},
				save_data: {//保存同步数据
					command:'save_sync_data', at:at
				},
			},/**转台 表单 结束**/
			ccd_form: {//ccd1表单指令的参数
				coolTemp: {//ccdNo为ccd序号
					temp: '', command:'set_cool', ccdNo: '1', at:at
				},
				exposeParam: {
					validFlag: '', startTime: '', duration:'', delay: '', objName: '', objType: '-1',
					objectRightAscension1: '',objectRightAscension2: '', objectRightAscension3: '',
					objectDeclination1: '', objectDeclination2: '', objectDeclination3: '',
					objectEpoch: '-1', objectBand: '', objectFilter: '-1',isSaveImage: '-1',
					weatherGatherTime: '', temperature1: '', humidity: '', windSpeed: '',
					pressure: '', skyGatherTime: '', skyState: '', clouds: '', seeingGatherTime: '',
					seeing: '', dustGatherTime:'', dust: '', AMS: '', extinctionGatherTime: '',
					rightAscension: '', declination: '', band:'', extinctionFactor1: '', extinctionFactor2: '',
					extinctionFactor3: '',telescopeRightAscension: '', telescopeDeclination: '',
					focusLength:'', frameNum: '', command:'expose_param', ccdNo: '1', at:at
				},
			},/**ccd1 表单 结束**/
		},/********vue data属性对象 结束********/
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
			select_ccd:function (){//通过下拉选择获取各ccd配置数据
				var v = this.device_nav.ccdNo;
	
				if ( v != -1) //如果下拉框不是 -1 将configData中相应的ccd配置数据赋值给ccd_config
				{
					this.ccd_config = this.configData.ccd[v-1];
					this.ccd_name = 'CCD' + v;//将ccd1换为ccd2、ccd3...
					//将下拉选择框隐藏
					this.device_nav.select_ccd = ''; //将下拉选择框隐藏
					//执行CCD元素点击事件
					this.ccd_click();
				}
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
				if ( !patn.test(v) || v > 59 || v < 1 )
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
			gimbal_track_star_Dec1:function (tip) {
				var msg = '';
				var patn = /^-?\d{2}$/;
				var v = this.gimbal_form.trackStar.declination1;
				if ( !patn.test(v) || v > 90 || v < -90 )
				{
					msg = '赤纬小时参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.trackstar_dec1, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			gimbal_track_star_Dec2:function (tip) {
				var msg = '';
				var patn = /^\d{2}$/;
				var v = this.gimbal_form.trackStar.declination2;
				if ( !patn.test(v) || v > 59 || v < 1 )
				{
					msg = '赤纬分钟参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.trackstar_dec2, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			gimbal_track_star_Dec3:function (tip) {
				var msg = '';
				var v = this.gimbal_form.trackStar.declination3;
				if ( !$.isNumeric(v) || v >= 60 || v < 0 )
				{
					msg = '赤纬秒参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.trackstar_dec3, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			gimbal_dec1_up:function () {
				var res = this.gimbal_track_star_Dec1(false);
				if ( res === '' ) this.$refs.trackstar_dec2.focus();
			},
			gimbal_dec2_up:function () {
				var res = this.gimbal_track_star_Dec2(false);
				if ( res === '' ) this.$refs.trackstar_dec3.focus();
			},
			trackStar_sbmt:function () { //转台 跟踪恒星 指令提交
				var msg = '';
				msg += this.gimbal_track_star_Asc1(false);
				msg += this.gimbal_track_star_Asc2(false);
				msg += this.gimbal_track_star_Asc3(false);
				msg += this.gimbal_track_star_Dec1(false);
				msg += this.gimbal_track_star_Dec2(false);
				msg += this.gimbal_track_star_Dec3(false);
				var asc1 = Math.abs(this.gimbal_form.trackStar.rightAscension1);
				var asc2 = Math.abs(this.gimbal_form.trackStar.rightAscension2);
				var asc3 = Math.abs(this.gimbal_form.trackStar.rightAscension3);
				var dec1 = Math.abs(this.gimbal_form.trackStar.declination1);
				var dec2 = Math.abs(this.gimbal_form.trackStar.declination2);
				var dec3 = Math.abs(this.gimbal_form.trackStar.declination3);
				var asc = asc1 + asc2/60 + asc3/3600;  //赤经换算为数值
				var dec = dec1 + dec2/60 + dec3/3600;  //赤经换算为数值
				if ( asc > 24 ) msg += '赤经值超限!<br>';
				if ( dec > 90 ) msg += '赤纬值超限!<br>';
				if ( this.gimbal_form.trackStar.epoch == -1 )
				{
					msg += '历元未选择<br>';
				}
				if ( this.gimbal_form.trackStar.speed == -1 )
				{
					msg += '跟踪速度未选择';
				}
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{//表单参数无错误
					$.ajax({
						url: '/gimbal',
						type: 'post',
						data: this.gimbal_form.trackStar,
						success: function (info) {
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
							});/*layer.alert 结束*/
						},/*success方法 结束*/
						error: function () {
							layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
						},
					});/*ajax 结束*/
				}
			},/*转台 跟踪恒星 指令提交 结束*/
			obj_name_check:function (tip) {
				var msg = '';
				var v = this.gimbal_form.objName.objectName;
				var patn = /([\u4e00-\u9fa5]| )+/;
				if ( patn.test(v) || v == '' || v.length > 48)
				{
					msg = '名称不能有汉字或空格!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.objName, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			obj_name_sbmt:function () {
				var msg = '';
				msg += this.obj_name_check(false);
				if ( this.gimbal_form.objName.objectType == -1 )
				{
					msg += '目标类型未选择';
				}
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{//表单参数无错误
					$.ajax({
						url: '/gimbal',
						type: 'post',
						data: this.gimbal_form.objName,
						success: function (info) {
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
							});/*layer.alert 结束*/
						},/*success方法 结束*/
						error: function () {
							layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
						},
					});/*ajax 结束*/
				}
			},/******obj_name_sbmt 结束******/
			azimuth_check:function (tip) {
				var msg = '';
				var v = this.gimbal_form.slewAzEl.azimuth;
				if ( !$.isNumeric(v) || v > 360 || v < 0 )
				{
					msg = '方位参数超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.azimuth, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			elevation_check:function (tip) {
				var msg = '';
				var v = this.gimbal_form.slewAzEl.elevation;
				if ( !$.isNumeric(v) || v > 90 || v < configData.gimbal.minelevation*1 )
				{
					msg = '俯仰参数超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.elevation, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			slewAzEl_sbmt:function () {
				var msg = '';
				msg += this.azimuth_check(false);
				msg += this.elevation_check(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{//表单参数无错误
					$.ajax({
						url: '/gimbal',
						type: 'post',
						data: this.gimbal_form.slewAzEl,
						success: function (info) {
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
							});/*layer.alert 结束*/
						},/*success方法 结束*/
						error: function () {
							layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
						},
					});/*ajax 结束*/
				}
			},/**slewAzEl_sbmt 结束**/
			slewDerotator_check:function (tip) {
				var msg = '';
				var v = this.gimbal_form.slewDerotator.slewDerotator;
				if ( !$.isNumeric(v) || v < 0 || v > 360 )
				{
					msg = '参数超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.slewDerotator, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			slewDerotator_sbmt:function (tip) {
				var msg = this.slewDerotator_check(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{//表单参数无错误
					$.ajax({
						url: '/gimbal',
						type: 'post',
						data: this.gimbal_form.slewDerotator,
						success: function (info) {
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
							});/*layer.alert 结束*/
						},/*success方法 结束*/
						error: function () {
							layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
						},
					});/*ajax 结束*/
				}
			},/**slewDerotator_sbmt 结束**/
			polar_Angle_check:function (tip) {
				var msg = '';
				var v = this.gimbal_form.axis3_mode.polarizingAngle;
				if ( !$.isNumeric(v) || v < 0 || v > 360 )
				{
					msg = '起偏角参数超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.polar_Angle, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			axis3Mode_sbmt:function () {
				var msg = '';
				var v = this.gimbal_form.axis3_mode.mode;
				if ( v == -1 )
				{
					msg += '未选择模式!<br>';
				}
				if ( v == 2 )
				{
					msg += this.polar_Angle_check(false);
				}
				
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{//表单参数无错误
					$.ajax({
						url: '/gimbal',
						type: 'post',
						data: this.gimbal_form.axis3_mode,
						success: function (info) {
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
							});/*layer.alert 结束*/
						},/*success方法 结束*/
						error: function () {
							layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
						},
					});/*ajax 结束*/
				}
			},/**axis3Mode_sbmt 结束**/
			speed_alter_sbmt:function (btn){
				var that = this; //存储vue实例化的对象
				if ( this.gimbal_form.speed_alter.correction == -1)
				{
					layer.alert('未选择速度值!', {shade:false, closeBtn:0});return;
				}

				this.gimbal_form.speed_alter.axis = btn; //将按钮对应的方向 赋值给axis
				$.ajax({
					url: '/gimbal',
					type: 'post',
					data: this.gimbal_form.speed_alter,
					success: function (info) {
						that.device_nav.speed_alter_btn = btn; //将此按钮字体变红色
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
						});/*layer.alert 结束*/
					},/*success方法 结束*/
					error:function (){
						layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
					},
				})/*ajax 结束*/
			},/**speed_alter_sbmt 结束**/
			speed_fixed_check:function (tip){
				var msg = '';
				var v = this.gimbal_form.speed_fixed.speed;
				var v_max = 0; //定义最大速度值

				if ( !$.isNumeric(v) )
				{
					msg = '参数超限!';
				}

				switch (this.gimbal_form.speed_fixed.axis) { //判断是哪个轴
					case '1':
						v_max = configData.gimbal.maxaxis1speed;
						break;
					case '2':
						v_max = configData.gimbal.maxaxis2speed;
						break;
					case '3':
						v_max = configData.gimbal.maxaxis3speed;
						break;
				}

				if ( v == 0 || v > v_max*1 || v < v_max*-1)
				{
					msg = '参数超限!';
				}

				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.speed_fixed, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			speed_fixed_sbmt:function (){
				var msg = '';
				var msg = this.speed_fixed_check(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{//表单参数无错误
					$.ajax({
						url: '/gimbal',
						type: 'post',
						data: this.gimbal_form.speed_fixed,
						success: function (info) {
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
							});/*layer.alert 结束*/
						},/*success方法 结束*/
						error: function () {
							layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
						},
					});/*ajax 结束*/
				}
			},/**speed_fixed_sbmt 结束**/
			position_alter_sbmt:function (btn){
				var that = this; //存储vue实例化的对象
				if ( this.gimbal_form.position_alter.correction == -1)
				{
					layer.alert('未选择偏移量!', {shade:false, closeBtn:0});return;
				}

				this.gimbal_form.position_alter.axis = btn; //将按钮对应的方向 赋值给axis
				
				$.ajax({
					url: '/gimbal',
					type: 'post',
					data: this.gimbal_form.position_alter,
					success: function (info) {
						that.device_nav.pos_alter_btn = btn; //将此按钮字体变红色
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
						});/*layer.alert 结束*/
					},/*success方法 结束*/
					error:function (){
						layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
					},
				})/*ajax 结束*/
			},/**position_alter_sbmt 结束**/
			cover_sbmt:function (op){
				var that = this; //保存vue实例
				this.gimbal_form.cover_op.operation = op; //将按钮对应的操作 赋值给operation
				$.ajax({
					url: '/gimbal',
					type: 'post',
					data: this.gimbal_form.cover_op,
					success: function (info) {
						that.device_nav.cover_btn = op; //将此按钮字体变红色
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
						});/*layer.alert 结束*/
					},/*success方法 结束*/
					error:function (){
						layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
					},
				})/*ajax 结束*/
			},/**cover_sbmt 结束**/
			setFocusType_sbmt:function (){
				if ( this.gimbal_form.setFocusType.focusType == -2 )
				{
					layer.alert('未选择焦点类型!', {shade:false, closeBtn:0});return;
				}
				$.ajax({
					url: '/gimbal',
					type: 'post',
					data: this.gimbal_form.setFocusType,
					success: function (info) {
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
						});/*layer.alert 结束*/
					},/*success方法 结束*/
					error:function (){
						layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
					},
				})/*ajax 结束*/
			},/**setFocusType_sbmt 结束**/
			save_data_sbmt:function (p){
				var that = this;
				//this.gimbal_form.save_data.saveSyncData = p;
				$.ajax({
					url: '/gimbal',
					type: 'post',
					data: this.gimbal_form.save_data,
					success: function (info) {
						that.device_nav.save_data_btn = p; //将此按钮字体变红色
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
						});/*layer.alert 结束*/
					},/*success方法 结束*/
					error:function (){
						layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
					},
				})/*ajax 结束*/
			},/**save_data_sbmt 结束**/
			ccd_btn_command:function (n) {/*ccd 按钮型指令*/
				var that = this; //存储vue实例化的对象
				var btn_str = ''; //控制按钮的样式
				var btn_text = '';
				var data = {at: at, command: ''}; //提交的数据

				switch (n) {
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
						btn_str = 'stop_expose';
						btn_text = '停止曝光';
						data.command = 'stop_expose';
						break;
					case 4:
						btn_str = 'abort_expose';
						btn_text = '终止曝光';
						data.command = 'abort_expose';
						break;
				}
				//执行ajax
				$.ajax({
					type : 'post',
					url : '/ccd',
					data : data,             
		            success: function (info) {
						that.device_nav.ccd_btn = btn_str;  //更改按钮样式
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
			},/**ccd_btn_command 结束**/
			ccd_cool:function (tip) {
				var msg = '';
				var v = this.ccd_form.coolTemp.temp;
				//console.log(ccd_config.lowcoolert);return;
				if ( !$.isNumeric(v)|| v > 20 || v < ccd_config.lowcoolert*1 )
				{
					msg = '制冷温度参数超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_cool, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},/**ccd_cool 结束**/
			ccd_cool_sbmt:function (){
				var msg = this.ccd_cool(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{//表单参数无错误
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.coolTemp,
						success: function (info) {
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
							});/*layer.alert 结束*/
						},/*success方法 结束*/
						error:function (){
							layer.alert('网络异常,请重新提交!', {shade:false, closeBtn:0});
						},
					})/*ajax 结束*/
				}	
			},/******ccd_cool_sbmt 结束******/
			ccd_asc1:function (tip){ //拍摄目标赤经之 小时
				var msg = '';
				var patn = /^\d{2}$/;
				var v = this.ccd_form.exposeParam.objectRightAscension1;
				if ( !patn.test(v) || v > 24 || v < 0 )
				{
					msg = '目标赤经小时参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_objAsc1, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},/******ccd_asc1 结束******/
			ccd_asc2:function (tip){ //拍摄目标赤经之 分钟
				var msg = '';
				var patn = /^\d{2}$/;
				var v = this.ccd_form.exposeParam.objectRightAscension2;
				if ( !patn.test(v) || v > 59 || v < 1 )
				{
					msg = '目标赤经分钟参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_objAsc2, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},/******ccd_asc1 结束******/
			ccd_asc3:function (tip){ //拍摄目标赤经之 分钟
				var msg = '';
				var v = this.ccd_form.exposeParam.objectRightAscension3;
				if ( !$.isNumeric(v) || v >= 60 || v < 0 )
				{
					msg = '目标赤经秒参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_objAsc3, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},/******ccd_asc1 结束******/
			ccd_asc1_up:function () {
				var res = this.ccd_asc1(false);
				if ( res === '' ) this.$refs.ccd_objAsc2.focus();
			},
			ccd_asc2_up:function () {
				var res = this.ccd_asc2(false);
				if ( res === '' ) this.$refs.ccd_objAsc3.focus();
			},
			ccd_dec1:function (tip){ //拍摄目标赤纬之 小时
				var msg = '';
				var patn = /^-?\d{2}$/;
				var v = this.ccd_form.exposeParam.objectDeclination1;
				if ( !patn.test(v) || v > 90 || v < -90 )
				{
					msg = '目标赤纬小时参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_objDec1, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},/******ccd_dec1 结束******/
			ccd_dec2:function (tip){ //拍摄目标赤纬之 分钟
				var msg = '';
				var patn = /^\d{2}$/;
				var v = this.ccd_form.exposeParam.objectDeclination2;
				if ( !patn.test(v) || v > 59 || v < 1 )
				{
					msg = '目标赤纬分钟参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_objDec2, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},/******ccd_dec2 结束******/
			ccd_dec3:function (tip){ //拍摄目标赤纬之 秒
				var msg = '';
				var v = this.ccd_form.exposeParam.objectDeclination3;
				if ( !$.isNumeric(v) || v >= 60 || v < 0 )
				{
					msg = '目标赤纬秒参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_objDec3, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},/******ccd_dec3 结束******/
			ccd_dec1_up:function () {
				var res = this.ccd_dec1(false);
				if ( res === '' ) this.$refs.ccd_objDec2.focus();
			},
			ccd_dec2_up:function () {
				var res = this.ccd_dec2(false);
				if ( res === '' ) this.$refs.ccd_objDec3.focus();
			},
			ccd_duration:function (tip) {
				var msg = '';
				var v = this.ccd_form.exposeParam.duration;
				if ( !$.isNumeric(v) || v > ccd_config.maxexposuretime || v<ccd_config.maxexposuretime )
				{
					msg += '曝光时间参数超限!';
				}
				
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.duration, {shade:false,closeBtn:0})
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			ccd_exposeParam_sbmt:function (){
				var msg = '';
				msg += this.ccd_asc1(false);
				msg += this.ccd_asc2(false);
				msg += this.ccd_asc3(false);
				msg += this.ccd_dec1(false);
				msg += this.ccd_dec2(false);
				msg += this.ccd_dec3(false);
				msg += this.ccd_duration(false);
				var asc1 = Math.abs(this.ccd_form.exposeParam.objectRightAscension1);
				var asc2 = Math.abs(this.ccd_form.exposeParam.objectRightAscension2);
				var asc3 = Math.abs(this.ccd_form.exposeParam.objectRightAscension3);
				var dec1 = Math.abs(this.ccd_form.exposeParam.objectDeclination1);
				var dec2 = Math.abs(this.ccd_form.exposeParam.objectDeclination2);
				var dec3 = Math.abs(this.ccd_form.exposeParam.objectDeclination3);
				var asc = asc1 + asc2/60 + asc3/3600;  //赤经换算为数值
				var dec = dec1 + dec2/60 + dec3/3600;  //赤经换算为数值
				if ( asc > 24 ) msg += '赤经值超限!<br>';
				if ( dec > 90 ) msg += '赤纬值超限!<br>';
				if ( this.ccd_form.exposeParam.objectFilter == -1 )
				{
					msg += '拍摄波段滤光片系统未选择<br>';
				}
				if ( this.ccd_form.exposeParam.isSaveImage == -1 )
				{
					msg += '是否保存图像未选择<br>';
				}
				if ( this.ccd_form.exposeParam.objType == -1 )
				{
					msg += '拍摄目标类型未选择<br>';
				}
				if ( msg !== '' )//ccd_form.exposeParam.objType
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}//else{

				//}
			},/*ccd_exposeParam_sbmt 结束*/
		},/******methods 结束******/
	});/***************vue js结束*****************/

 	//ajax 实时更新60cm望远镜子设备状态数据///////////////////////	
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
				layer.alert('网络异常,设备实时数据无法获取!', {shade:false, closeBtn:0});
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
	});//观测计划 若为single和singleLoop 隐藏‘下一个’按钮 结束/////////
})