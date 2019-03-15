/** 望远镜控制页面js*/
$(function () {
	var planInfo = $('#planInfo'); //观测计划的表格
	
	var vm = new Vue({//vue 实例化
		el: '#all',
		data: {
			vue_operate: can_operate, //can_operate为atpage.html中的全局变量
			configData: configData, //configData是后端返回的json数据
			ccd_config:configData.ccd[0], //此对象存储ccd的配置数据
			ccd_name:'CCD1',
			//png_dir: [], //存储每一个观测图片的目录
			at_image:[], //存储所有获取的普通格式观测图像
			img4_data: [], //存储截取的4个普通格式图片
			at_image_dir:'', //各望远镜普通观测图像目录
			at_fitsImg_dir:'', //各望远镜fits观测图像目录
			next_pre_click:0, //向左或向右按钮点击的次数
			big_img:[], //观测图像的大图名称
			new_png: '', //最新观测图片
			fits_head:'', //fits head
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
				filter_btn: '',   //区分filter各按钮型指令
				filterPos_btn: '', //滤光片位置提交 按钮
				focus_btn: '',  //区分调焦器各按钮
				focus_command: '', //调焦器指令是否点击
				sDome_btn: '', //随动圆顶各按钮指令
				sDome_command: '', //随动圆顶各指令
				oDome_btn: '', //全开圆顶各按钮
				guide_command: '', //导星镜 指令
				guide_btn: '', //导星镜 按钮
			},
			gimbal_form: {//转台表单指令的参数
				trackStar: {
					rightAscension1: '', rightAscension2: '', rightAscension3: '',
					declination1: '', declination2: '', declination3: '',
					epoch: '-1', speed: '-1', command:'trackStar', at:at, at_aperture: aperture
				},
				objName: {
					objectName: '', objectType: '-1', command:'set_obj_name', at:at, at_aperture: aperture
				},
				slewAzEl: {
					azimuth: '', elevation: '', command:'slewAzEl', at:at, at_aperture: aperture
				},
				slewDerotator: {
					slewDerotator: '', command:'slewDerotator', at:at, at_aperture: aperture
				},
				axis3_mode: {//设置轴3工作模式
					mode: '-1', polarizingAngle: '', command:'axis3Mode', at:at, at_aperture: aperture
				},
				speed_alter: {//速度修正指令
					axis: '', correction: '-1', command:'speed_alter', at:at, at_aperture: aperture
				},
				speed_fixed: {//恒速运动
					axis: '1', speed: '', command:'speed_fixed', at:at, at_aperture: aperture
				},
				position_alter: {//恒速运动
					axis: '', correction: '-1', command:'position_alter', at:at, at_aperture: aperture
				},
				cover_op: {//镜盖操作
					operation: '', command:'cover_op', at:at, at_aperture: aperture
				},
				setFocusType: {//焦点切换镜
					focusType: '-2', command:'setFocusType', at:at, at_aperture: aperture
				},
				save_data: {//保存同步数据
					command:'save_sync_data', at:at, at_aperture: aperture
				},
			},/**转台 表单 结束**/
			ccd_form: {//ccd1表单指令的参数
				coolTemp: {//ccdNo为ccd序号
					temp: '', command:'set_cool', at:at, at_aperture: aperture
				},
				exposeParam: {
					validFlag: '', startTime: '', duration:'', delay: '', objName: '', objType: '-1',
					objectRightAscension1: '',objectRightAscension2: '', objectRightAscension3: '',
					objectDeclination1: '', objectDeclination2: '', objectDeclination3: '',
					objectEpoch: '-1', objectBand: '-1', objectFilter: '-1',isSaveImage: '-1',
					weatherGatherTime: '', temperature1: '', humidity: '', windSpeed: '',
					pressure: '', skyGatherTime: '', skyState: '', clouds: '', seeingGatherTime: '',
					seeing: '', dustGatherTime:'', dust: '', AMS: '', extinctionGatherTime: '',
					rightAscension: '', declination: '', band:'', extinctionFactor1: '', extinctionFactor2: '',
					extinctionFactor3: '',telescopeRightAscension: '', telescopeDeclination: '',
					focusLength:'', frameNum: '1', command:'expose_param', at:at, at_aperture: aperture
				},
				start_expose: {
					isReadFrameSeq:'1', frameSequence: '', command:'start_expose', at:at, at_aperture: aperture
				},
				set_gain: {
					mode:'-1', gear: '-1', command:'set_gain', at:at, at_aperture: aperture
				},
				readout_speed_form: {
					readout_mode:'-1', command:'readout_speed', at:at, at_aperture: aperture
				},
				transfer_speed_form: {
					transfer_mode:'-1', command:'transfer_speed', at:at, at_aperture: aperture
				},
				set_bin_form: {
					bin:'-1', command:'set_bin', at:at, at_aperture: aperture
				},
				set_roi_form: {
					startX:0, startY: 0, imageW:0, imageH:0, command:'set_roi', at:at, at_aperture: aperture
				},
				set_shutter_form: {
					shutter:'-1', command:'set_shutter', at:at, at_aperture: aperture
				},
				set_frame_form: {
					frame:'1', command:'set_frame', at:at, at_aperture: aperture
				},
				set_em_form: {
					em:'1', emV:'', command:'set_em', at:at, at_aperture: aperture
				},
				set_cmos_form: {
					isNoiseFilter:'1', command:'set_cmos', at:at, at_aperture: aperture
				},
				set_baseLine_form: {
					isBaseLine:'1', baseLineV:'', command:'set_baseLine', at:at, at_aperture: aperture
				},
				overScan_form: {
					over_scan:'1', command:'over_scan', at:at, at_aperture: aperture
				},
			},/**ccd 表单 结束**/
			filter_form: {//滤光片表单
				filter_position: {
					filter_pos: '-1', command:'set_filterPos', at:at, at_aperture: aperture
				},
			},/** 滤光片表单 结束**/
			focus_form: {
				objPos_form: {
					pos: '',  command:'set_objPos', at:at, at_aperture: aperture
				},
				fixSpeed_form: {
					speed: '',  command:'fix_speed', at:at, at_aperture: aperture
				},
				tempera_form: {
					enable: '1',  command:'tempera_enable', at:at, at_aperture: aperture
				},
				temperatureE_form: {
					coefficient: '',  command:'temperature_coef', at:at, at_aperture: aperture
				},
			},/** 调焦器表单 结束**/
			sDome_form: {
				objPos_form: {
					position: '',  command:'set_objPos', at:at, at_aperture: aperture
				},
				rotateSpeed_form: {
					speed: '',  command:'set_speed', at:at, at_aperture: aperture
				},
				shadePos_form: {
					position: '',  command:'set_shade', at:at, at_aperture: aperture
				},
				shadeAction_form: {
					action: '-1',  command:'set_action', at:at, at_aperture: aperture
				},
			},/** 随动圆顶表单 结束**/
			guide_form: {
				coverAction_form: {
					action: '-1',  command:'set_cover', at:at, at_aperture: aperture
				},
				autoFocus_form: {
					enable: '1',  command:'auto_focus', at:at, at_aperture: aperture
				},
			},/** 导星镜 表单 结束**/
			gimbal_status: {},
			gimbal_s: false,//控制转台当前状态的字体颜色
			ccd_status: {},
			ccd_s: false,
			sDome_status: {},
			sDome_s: false,
			filter_status: {},
			filter_s: false,
			focus_status: {},
			focus_s: false,
		},/********vue data属性对象 结束********/
		computed: {//计算属性
			gimbal_focus: function () {
				let t = eval ( '(' + this.configData.gimbal.focustype + ')' );
				let temp = {}; //定义一个对象临时存储数据
				temp.focus = t.focus;
				delete t.focus; //删除t中的focus

				let j = 0; //
				for (p in t)
				{
					temp['v'+j] = t[p];
					j++;
				}
				return temp;
			},//gimbal_focus 结束
			ccd_gainMode: function (){
				var gainMode = this.ccd_config.gainmode.split(', ');
				var final_gainMode = {
					High_Sensitivity: null, High_Capacity: null
				};
				gainMode.filter(
					function (e) {
						if ( e.indexOf('ensitiv') !== -1 )
						{
							final_gainMode.High_Sensitivity = e;
						}else if ( e.indexOf('apaci') !== -1 )
						{
							final_gainMode.High_Capacity = e;
						}
					}
				);
				return final_gainMode;
			}, /*ccd_gainMode 结束*/
			readout_speed_Mode: function (){//处理 读出速度
				return this.ccd_config.readoutspeed.split('#');
				/*var readout_speed_mode = this.ccd_config.readoutspeed.split('#');
				var final_readout_speed_mode = {
					A: null, B: null, C:null
				};
				readout_speed_mode.filter(
					function (e) {
						if ( e == 'A' )
						{
							final_readout_speed_mode.A = e;
						}else if ( e == 'B' )
						{
							final_readout_speed_mode.B = e;
						}else if ( e == 'C' )
						{
							final_readout_speed_mode.C = e;
						}
					}
				);
				return final_readout_speed_mode;*/
			}, /*readout_speed_Mode 结束*/
			transfer_speed_Mode: function (){//处理 转移速度
				return this.ccd_config.transferspeed.split('#');
				/*var transfer_speed_mode = this.ccd_config.transferspeed.split('#');
				var final_transfer_speed_mode = {
					a: null, b: null };
				transfer_speed_mode.filter(
					function (e) {
						if ( e == 'a' )
						{
							final_transfer_speed_mode.a = e;
						}else if ( e == 'b' )
						{
							final_transfer_speed_mode.b = e;
						}
					}
				);
				return final_transfer_speed_mode;*/
			}, /*transfer_speed_Mode 结束*/
			shutter_Mode: function (){
				var shutter_mode = this.ccd_config.shuttermode.split(', ');
				var final_shutter_mode = {
					GlobalShutter: null, RollingShutter: null
				};
				shutter_mode.filter(
					function (e) {
						if ( (e.toLowerCase()).indexOf('global') !== -1 )
						{
							final_shutter_mode.GlobalShutter = e;
						}else if ( (e.toLowerCase()).indexOf('roll') !== -1 )
						{
							final_shutter_mode.RollingShutter = e;
						}
					}
				);
				return final_shutter_mode;
			}, /*readout_speed_Mode 结束*/
			bin: function (){//处理ccd的bin
				return this.ccd_config.bin.split('#');
				/*var temp = this.ccd_config.bin.split('#'); ////以'#'把binarray分割为数组:['2*2', '4*4']
				var bin_arr = [];
				temp.filter(function (v) {
					bin_arr.push( v.substr(0,1) ); //取'2*2'这个字符串的第一个字符, 放入数组bin_arr中
				});
				return bin_arr;*/
			},//bin 结束
		},/*computed 结束*/
		methods: {
			plan_click: function () {
				localStorage.setItem('page_pos', 'plan');
				this.device_nav.dev_click = 'plan';
				planInfo.removeClass('displayNo');
				//console.log(table.datagrid());
				//let t = table.datagrid();
				//for (let i = 0; i < 20000000; i++) { i*1; }

				//if ( table.datagrid() !== undefined && table.datagrid('getRows').length < 1 )
				if ( table.datagrid('getRows').length < 1 )
				{
					table.datagrid('insertRow', {
						index : 0, 
						row:{type:'0',epoch:1,bin:1,readout:0,gain:0},
					});
					
					table.datagrid('beginEdit', 0); //将此新加的一行设为可编辑
					editRow = 0;
					table.datagrid('enableDnd');
				}
			},
			gimbal_click: function () {
				localStorage.setItem('page_pos', 'gimbal');
				this.device_nav.dev_click = 'gimbal';
				this.device_nav.gimbal_command = '';
				planInfo.addClass('displayNo');
			},
			gimbal_btn_command: function (connect) { //转台之 连接 断开 找零 复位 停止 急停
				var that = this; //存储vue实例化的对象
				var btn_str = ''; //控制按钮的样式
				var btn_text = '';
				var data = {at: at, command: '',at_aperture:aperture}; //提交的数据

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
				localStorage.setItem('page_pos', 'ccd');
				this.device_nav.dev_click = 'ccd';
				planInfo.addClass('displayNo');
			},
			select_ccd: function (){//通过下拉选择获取各ccd配置数据
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
				localStorage.setItem('page_pos', 'filter');
				this.device_nav.dev_click = 'filter';
				planInfo.addClass('displayNo');
			},
			sDome_click: function () {
				localStorage.setItem('page_pos', 'sDome');
				this.device_nav.dev_click = 'sDome';
				planInfo.addClass('displayNo');
			},
			oDome_click: function () {
				localStorage.setItem('page_pos', 'oDome');
				this.device_nav.dev_click = 'oDome';
				planInfo.addClass('displayNo');
			},
			focus_click: function () {
				localStorage.setItem('page_pos', 'focus');
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
				localStorage.setItem('page_pos', 'image');
				this.device_nav.dev_click = 'pic';
				planInfo.addClass('displayNo');
				var t = this;
				//ajax 获取观测图像
				$.ajax({
					url: '/get_image',
					type: 'post',
					data: {
						at_aperture: aperture, 
					},
					success: function (info){
						if ( info.indexOf('img') !== -1 ) //返回了图像信息
						{
							let img_data = info.split('#');
							t.at_image = $.parseJSON(img_data[1]);
							
							t.img4_data = t.at_image.slice(0,4); //截取前4个图片 赋值给img4_data
							t.big_img = t.img4_data[0]; //将第一个图片，显示


							/*之前的 代码 目录只有一个的情况
							t.png_dir = img_data[2];
							img_data = $.parseJSON(img_data[1]);
							t.at_image = img_data; //将目录中所有图片信息赋值给at_image
							t.img4_data = img_data.slice(0,4); //截取前4个图片 赋值给img4_data
							t.big_img = t.img4_data[0]; //将第一个图片，显示*/
						}else{//为获取到图像
							t.img4_data = []; //将页面原有图片清空
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
						}
					},//success 结束
					error:function () {
						layer.alert('网络异常, 获取观测图像失败', {shade:false, closeBtn:0});
					}//error 结束
				})//ajax获取观测图像结束
			},
			next_4_pic: function () {//观测图像向右按钮 点击事件
				let n = this.at_image.length; //图片总数
				let m = Math.floor( n/4 ); //可以向右点击的次数
				if ( this.next_pre_click < m )  this.next_pre_click ++; //向左向右点击次数加1
				let head = this.next_pre_click * 4; //每点击1次，起始位置增加4
				this.img4_data = this.at_image.slice(head, head+4); 
			},
			pre_4_pic: function () {//观测图像向左按钮 点击事件
				if ( this.next_pre_click > 0 ) this.next_pre_click --; //向左向右点击次数减1
				let head = this.next_pre_click * 4; //每点击1次，起始位置减去4
				this.img4_data = this.at_image.slice(head, head+4); 
			},
			show_big_img: function (k) {//被点击后 显示大图
				this.big_img = this.img4_data[k];
			},
			gimbal_track_star_Asc1: function (tip) {
				var msg = '';
				var patn = /^\d{1, 2}$/;
				var v = this.gimbal_form.trackStar.rightAscension1;
				if ( !patn.test(v) || v > 24 || v < 0 )
				{
					msg = '赤经小时参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.trackstar_asc1);
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			gimbal_track_star_Asc2: function (tip) {
				var msg = '';
				var patn = /^\d{1,2}$/;
				var v = this.gimbal_form.trackStar.rightAscension2;
				if ( !patn.test(v) || v > 59 || v < 0 )
				{
					msg = '赤经分钟参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.trackstar_asc2);
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
					layer.tips(msg, this.$refs.trackstar_asc3);
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
				var patn = /^-?\d{1,2}$/;
				var v = this.gimbal_form.trackStar.declination1;
				if ( !patn.test(v) || v > 90 || v < -90 )
				{
					msg = '赤纬度参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.trackstar_dec1);
				}
				return msg !== '' ? msg + '<br>' : '';
			},
			gimbal_track_star_Dec2:function (tip) {
				var msg = '';
				var patn = /^\d{1,2}$/;
				var v = this.gimbal_form.trackStar.declination2;
				if ( !patn.test(v) || v > 59 || v < 0 )
				{
					msg = '赤纬分钟参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.trackstar_dec2);
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
					layer.tips(msg, this.$refs.trackstar_dec3);
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
				if ( patn.test(v) || v.length > 48 || v.length < 1 )
				{
					msg = '名称不能有汉字或空格!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.objName);
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
					layer.tips(msg, this.$refs.azimuth);
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
					layer.tips(msg, this.$refs.elevation);
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
					layer.tips(msg, this.$refs.slewDerotator);
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
					layer.tips(msg, this.$refs.polar_Angle);
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
			speed_alter_sbmt:function (btn, stop){
				var that = this; //存储vue实例化的对象

				if ( this.gimbal_form.speed_alter.correction == -1 && stop === undefined )//未传入第2个参数时
				{
					layer.alert('未选择速度值!', {shade:false, closeBtn:0});return;
				}

				this.gimbal_form.speed_alter.axis = btn; //将按钮对应的方向 赋值给axis
				
				if ( stop === 0 )//鼠标松开 发送速度0
				{
					for (let i = 0; i < 10000000; i++) { i*1; i/1; i*1 } //延迟1秒多 再往下执行
					this.gimbal_form.speed_alter.correction = '0';
				}

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
					layer.tips(msg, this.$refs.speed_fixed);
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
				data.at_aperture = aperture; //提交数据加上 口径
				data.ccdNo = this.device_nav.ccdNo; //提交数据加上 口径

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
				if ( !$.isNumeric(v)|| v > 20 || v < this.ccd_config.lowcoolert*1 )
				{
					msg = '制冷温度参数超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_cool);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/**ccd_cool 结束**/
			ccd_cool_sbmt:function (){
				var msg = this.ccd_cool(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{//表单参数无错误
					this.ccd_form.coolTemp.ccdNo = this.device_nav.ccdNo;
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
			ccd_validFlag:function (tip){

			},//ccd_validFlag() 结束
			ccd_startTime:function (tip){

			},//ccd_startTime() 结束
			ccd_delay:function (tip){

			},//ccd_delay() 结束
			ccd_objName:function (tip){

			},//ccd_objName() 结束
			ccd_exposeNum:function (tip){
				var msg = '';
				var patn = /^\d+$/;
				var v = this.ccd_form.exposeParam.frameNum;
				
				if ( !patn.test(v) || v < 1 )
				{
					msg = '曝光帧数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_objAsc1);
				}
				return msg !== '' ? msg + '<br>' : '';
			},//ccd_exposeNum() 结束
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
					layer.tips(msg, this.$refs.ccd_objAsc1);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/******ccd_asc1 结束******/
			ccd_asc2:function (tip){ //拍摄目标赤经之 分钟
				var msg = '';
				var patn = /^\d{2}$/;
				var v = this.ccd_form.exposeParam.objectRightAscension2;
				if ( !patn.test(v) || v > 59 || v < 0 )
				{
					msg = '目标赤经分钟参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_objAsc2);
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
					layer.tips(msg, this.$refs.ccd_objAsc3);
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
			ccd_dec1:function (tip){ //拍摄目标赤纬之 度
				var msg = '';
				var patn = /^-?\d{2}$/;
				var v = this.ccd_form.exposeParam.objectDeclination1;
				if ( !patn.test(v) || v > 90 || v < -90 )
				{
					msg = '目标赤纬度参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_objDec1);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/******ccd_dec1 结束******/
			ccd_dec2:function (tip){ //拍摄目标赤纬之 分钟
				var msg = '';
				var patn = /^\d{2}$/;
				var v = this.ccd_form.exposeParam.objectDeclination2;
				if ( !patn.test(v) || v > 59 || v < 0 )
				{
					msg = '目标赤纬分钟参数超限';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.ccd_objDec2);
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
					layer.tips(msg, this.$refs.ccd_objDec3);
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
				if ( !$.isNumeric(v) || v > this.ccd_config.maxexposuretime*1 || v < this.ccd_config.minexposuretime*1 )
				{
					msg += '曝光时间参数超限!';
				}
				
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.duration);
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
				msg += this.ccd_exposeNum(false);				
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
				}else{
					this.ccd_form.exposeParam.ccdNo = this.device_nav.ccdNo;
					this.ccd_form.exposeParam.filter = this.configData.filter.filtername;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.exposeParam,
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
			},/*ccd_exposeParam_sbmt 结束*/
			ccd_startExpose_sbmt:function () {
				this.ccd_form.start_expose.ccdNo = this.device_nav.ccdNo;
				$.ajax({
					url: '/ccd',
					type: 'post',
					data: this.ccd_form.start_expose,
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
			},/*ccd_startExpose_sbmt 结束*/
			ccd_gain_sbmt:function () {
				var msg = '';
				if ( this.ccd_form.set_gain.mode == -1 )
				{
					msg += '增益模式未选择<br>';
				}
				if ( this.ccd_form.set_gain.gear == -1 )
				{
					msg += '增益档位未选择<br>';
				}

				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{
					this.ccd_form.set_gain.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.set_gain,
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
			},/*ccd_gain_sbmt 结束*/
			readout_speed_sbmt:function () {
				if ( this.ccd_form.readout_speed_form.readout_mode == -1 )
				{
					layer.alert('读出速度模式未选择!', {shade:false,closeBtn:0});return;
				}else{
					this.ccd_form.readout_speed_form.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.readout_speed_form,
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
			},/*readout_speed_sbmt 结束*/
			transfer_speed_sbmt:function () {
				if ( this.ccd_form.transfer_speed_form.transfer_mode == -1 )
				{
					layer.alert('转移速度模式未选择!', {shade:false,closeBtn:0});return;
				}else{
					this.ccd_form.transfer_speed_form.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.transfer_speed_form,
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
			},/*transfer_speed_sbmt 结束*/
			set_bin_sbmt:function () {
				var msg = '';
				if ( this.ccd_form.set_bin_form.bin == -1 )
				{
					msg += 'bin未选择!<br>';
				}
				/*if ( this.ccd_form.set_bin_form.binX == -1 )
				{
					msg += 'binX未选择!<br>';
				}

				if ( this.ccd_form.set_bin_form.binY == -1 )
				{
					msg += 'binY未选择!<br>';
				}

				if ( this.ccd_form.set_bin_form.binY !== this.ccd_form.set_bin_form.binX )
				{
					msg += 'binX与binY不一致!';
				}*/
				
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false,closeBtn:0});return;
				}else{
					this.ccd_form.set_bin_form.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.set_bin_form,
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
			},/*set_bin_sbmt 结束*/
			ccd_roi_x:function (tip) {
				var msg = '';
				var v = this.ccd_form.set_roi_form.startX;
				var patn = /^\d+$/;
				if ( !patn.test(v) || v > this.ccd_config.xpixel-1 || v < 0 )
				{
					msg = 'X坐标参数超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.x);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*ccd_roi_x 结束*/
			ccd_roi_y:function (tip) {
				var msg = '';
				var v = this.ccd_form.set_roi_form.startY;
				var patn = /^\d+$/;
				if ( !patn.test(v) || v > this.ccd_config.ypixel-1 || v < 0 )
				{
					msg = 'Y坐标参数超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.y);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*ccd_roi_y 结束*/
			ccd_roi_w:function (tip) {
				var msg = '';
				var v = this.ccd_form.set_roi_form.imageW*1;
				var x = this.ccd_form.set_roi_form.startX*1;
				var patn = /^\d+$/;
				if ( !patn.test(v) || (v+x) > this.ccd_config.xpixel*1 || v < 1 )
				{
					msg = 'width参数超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.w);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*ccd_roi_w 结束*/
			ccd_roi_h:function (tip) {
				var msg = '';
				var v = this.ccd_form.set_roi_form.imageH*1;
				var y = this.ccd_form.set_roi_form.startY*1;
				var patn = /^\d+$/;
				if ( !patn.test(v) || (v+y) > this.ccd_config.ypixel*1 || v < 1 )
				{
					msg = 'height参数超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.h);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*ccd_roi_h 结束*/
			set_roi_sbmt:function () {
				var msg = '';
				msg += this.ccd_roi_x(false);
				msg += this.ccd_roi_y(false);
				msg += this.ccd_roi_w(false);
				msg += this.ccd_roi_h(false);

				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false, closeBtn:0});
				}else{
					this.ccd_form.set_roi_form.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.set_roi_form,
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
			},/*set_roi_sbmt 结束*/
			set_shutter_sbmt:function (){
				if ( this.ccd_form.set_shutter_form.shutter == -1 )
				{
					layer.alert('快门模式未选择!', {shade:false, closeBtn:0});
				}else{
					this.ccd_form.set_shutter_form.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.set_shutter_form,
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
			},/*set_shutter_sbmt 结束*/
			set_frame_sbmt:function (){
				this.ccd_form.set_frame_form.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.set_frame_form,
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
			},/*set_frame_sbmt 结束*/
			ccd_em_check:function (tip) {
				var msg = '';
				var v = this.ccd_form.set_em_form.emV;
				var patn = /^\d+$/;
				if ( !patn.test(v) || v > this.ccd_config.emmaxvalue*1 || v < this.ccd_config.emminvalue*1)
				{
					msg = 'em值超限!';
				}
				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.em);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*ccd_em_check 结束*/
			set_em_sbmt:function (){
				var msg = '';
				if (this.ccd_form.set_em_form.em == 1)
				{
					msg = this.ccd_em_check(false);
				}
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:0,closeBtn:0});
				}else{
					this.ccd_form.set_em_form.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.set_em_form,
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
			},/*set_em_sbmt 结束*/
			set_cmos_sbmt:function (){
				this.ccd_form.set_cmos_form.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.set_cmos_form,
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
			},/*set_cmos_sbmt 结束*/
			ccd_baseLine_check:function (tip){
				var msg = '';
				var v = this.ccd_form.set_baseLine_form.baseLineV;
				var patn = /^\d+$/;
				if ( !patn.test(v) )
				{
					msg = 'baseLine值超限!';
				}

				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.base);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*ccd_baseLine_check 结束*/
			set_baseLine_sbmt:function (){
				var msg = '';
				if (this.ccd_form.set_baseLine_form.isBaseLine == 1)
				{
					msg = this.ccd_baseLine_check(false);
				}
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:0,closeBtn:0});
				}else{
					this.ccd_form.set_baseLine_form.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.set_baseLine_form,
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
			},/*set_baseLine_sbmt 结束*/
			overScan_sbmt:function (){
				this.ccd_form.overScan_form.ccdNo = this.device_nav.ccdNo;
					$.ajax({
						url: '/ccd',
						type: 'post',
						data: this.ccd_form.overScan_form,
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
			},/*set_baseLine_sbmt 结束*/
			filter_btn_command:function (n){
				var that = this; //存储vue实例化的对象
				var btn_str = ''; //控制按钮的样式
				var btn_text = '';
				var data = {at: at, command: '', at_aperture:aperture}; //提交的数据

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
						btn_str = 'findhome';
						btn_text = '找零';
						data.command = 'findhome';
						break;
				}
				$.ajax({//执行ajax
					type : 'post',
					url : '/filter',
					data : data,             
				    success: function (info) {
						that.device_nav.filter_btn = btn_str;  //更改按钮样式
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
			},/*filter_btn_command 结束*/
			filterPos_sbmt:function (){
				var msg = '';
				if ( this.filter_form.filter_position.filter_pos == -1 )
				{
					layer.alert('您未选择滤光片!', {shade:false, closeBtn:0});return;
				}
				$.ajax({//执行ajax
					type : 'post',
					url : '/filter',
					data : this.filter_form.filter_position,             
				    success: function (info) {
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
		        		layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
		            },
				});
			},/*filterPos_sbmt 结束*/
			focus_btn_command:function (n) {
				var that = this; //存储vue实例化的对象
				var btn_str = ''; //控制按钮的样式
				var btn_text = '';
				var data = {at: at, command: '', at_aperture:aperture}; //提交的数据

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
						btn_str = 'findhome';
						btn_text = '找零';
						data.command = 'findhome';
						break;
					case 4:
						btn_str = 'stop';
						btn_text = '停止';
						data.command = 'stop';
						break;
				}
				$.ajax({//执行ajax
					type : 'post',
					url : '/focus',
					data : data,             
				    success: function (info) {
						that.device_nav.focus_btn = btn_str;  //更改按钮样式
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
			},/*focus_btn_command 结束*/
			focus_objPos_check:function (tip) {
				var msg = '';
				var v = this.focus_form.objPos_form.pos;
				if ( !$.isNumeric(v) || v > this.configData.focus.maxvalue*1 || v < this.configData.focus.minvalue*1 )
				{
					msg = '目标位置值超限!';
				}

				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.focus_pos);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*focus_objPos_check 结束*/
			focus_objPos_sbmt:function (tip) {
				var msg = '';
				msg = this.focus_objPos_check(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false, closeBtn:0}); return;
				}else{
					$.ajax({//执行ajax
						type : 'post',
						url : '/focus',
						data : this.focus_form.objPos_form,             
					    success: function (info) {
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
			        		layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
			            },
					});
				}
			},/*focus_objPos_sbmt 结束*/
			focus_speed_check:function (tip) {
				var msg = '';
				var v = this.focus_form.fixSpeed_form.speed;
				if ( !$.isNumeric(v) || v > this.configData.focus.maxspeed*1 || v <= 0 )
				{
					msg = '速度值超限!';
				}

				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.focus_speed);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*focus_speed_check 结束*/
			focus_speed_sbmt:function () {
				var msg = '';
				msg = this.focus_speed_check(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false, closeBtn:0}); return;
				}else{
					$.ajax({//执行ajax
						type : 'post',
						url : '/focus',
						data : this.focus_form.fixSpeed_form,             
					    success: function (info) {
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
			        		layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
			            },
					});
				}
			},/*focus_speed_sbmt 结束*/
			tempera_enable_sbmt:function (){
				$.ajax({//执行ajax
					type : 'post',
					url : '/focus',
					data : this.focus_form.tempera_form,             
				    success: function (info) {
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
		        		layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
		            },
				});
			},/*tempera_enable_sbmt 结束*/
			temperature_coef_check:function (tip) {
				var msg = '';
				var v = this.focus_form.temperatureE_form.coefficient;
				if ( !$.isNumeric(v) || v <= 0 )
				{
					msg = '补偿系数超限!';
				}

				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.coefficient);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*temperature_coef_check 结束*/
			temperature_e_sbmt:function () {
				var msg = '';
				msg = this.temperature_coef_check(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false, closeBtn:0}); return;
				}else{
					$.ajax({//执行ajax
						type : 'post',
						url : '/focus',
						data : this.focus_form.temperatureE_form,             
					    success: function (info) {
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
			        		layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
			            },
					});
				}
			},/*temperature_e_sbmt 结束*/
			sDome_btn_command:function (n) {
				var that = this; //存储vue实例化的对象
				var btn_str = ''; //控制按钮的样式
				var btn_text = '';
				var data = {at: at, command: '', at_aperture:aperture}; //提交的数据

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
						btn_str = 'stop';
						btn_text = '停止运动';
						data.command = 'stop';
						break;
					case 4:
						btn_str = 'open';
						btn_text = '打开';
						data.command = 'open';
						break;
					case 5:
						btn_str = 'close';
						btn_text = '关闭';
						data.command = 'close';
						break;
				}
				$.ajax({//执行ajax
					type : 'post',
					url : '/slavedome',
					data : data,             
				    success: function (info) {
						that.device_nav.sDome_btn = btn_str;  //更改按钮样式
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
			},/*sDome_btn_command 结束*/
			sDome_objPos_check:function (tip) {
				var msg = '';
				var v = this.sDome_form.objPos_form.position;
				if ( !$.isNumeric(v) || v < 0 || v >= 360 )
				{
					msg = '目标方位值超限!';
				}

				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.objPos);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*sDome_objPos_check 结束*/
			sDome_objPos_sbmt:function () {
				var msg = '';
				msg = this.sDome_objPos_check(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false, closeBtn:0}); return;
				}else{
					$.ajax({//执行ajax
						type : 'post',
						url : '/slavedome',
						data : this.sDome_form.objPos_form,             
					    success: function (info) {
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
			        		layer.alert('网络异常,请再次提交', {shade:false, closeBtn:0});
			            },
					});
				}
			},/*sDome_objPos_sbmt 结束*/
			sDome_rotate_check:function (tip) {
				var msg = '';
				var v = this.sDome_form.rotateSpeed_form.speed;
				if ( !$.isNumeric(v) || v <= 0 || v > this.configData.sDome.maxspeed*1 )
				{
					msg = '转动速度值超限!';
				}

				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.rotate);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*sDome_rotate_check 结束*/
			sDome_rotate_sbmt:function () {
				var msg = '';
				msg = this.sDome_rotate_check(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false, closeBtn:0}); return;
				}else{
					$.ajax({//执行ajax
						type : 'post',
						url : '/slavedome',
						data : this.sDome_form.rotateSpeed_form,             
					    success: function (info) {
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
			        		layer.alert('网络异常,请再次提交', {shade:false, closeBtn:0});
			            },
					});
				}
			},/*sDome_rotate_sbmt 结束*/
			sDome_shade_check:function (tip){
				var msg = '';
				var v = this.sDome_form.shadePos_form.position;
				if ( !$.isNumeric(v) || v < 0 || v > 90 )
				{
					msg = '风帘位置值超限!';
				}

				if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.shade);
				}
				return msg !== '' ? msg + '<br>' : '';
			},/*sDome_shade_check 结束*/
			sDome_shade_sbmt:function () {
				var msg = '';
				msg = this.sDome_shade_check(false);
				if ( msg !== '' )
				{
					layer.alert(msg, {shade:false, closeBtn:0}); return;
				}else{
					$.ajax({//执行ajax
						type : 'post',
						url : '/slavedome',
						data : this.sDome_form.shadePos_form,             
					    success: function (info) {
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
			        		layer.alert('网络异常,请再次提交', {shade:false, closeBtn:0});
			            },
					});
				}
			},/*sDome_shade_sbmt 结束*/
			sDome_action_sbmt:function (){
				if ( this.sDome_form.shadeAction_form.action == -1 )
				{
					layer.alert('请选择风帘运动方向!');return;
				}

				$.ajax({//执行ajax
					type : 'post',
					url : '/slavedome',
					data : this.sDome_form.shadeAction_form,             
				    success: function (info) {
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
		        		layer.alert('网络异常,请再次提交', {shade:false, closeBtn:0});
		            },
				});
			},/*sDome_action_sbmt 结束*/
			oDome_btn_command:function (n) {
				var that = this; //存储vue实例化的对象
				var btn_str = ''; //控制按钮的样式
				var btn_text = '';
				var data = {at: at, command: '', at_aperture: aperture}; //提交的数据

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
						btn_str = 'open';
						btn_text = '打开';
						data.command = 'open';
						break;
					case 4:
						btn_str = 'close';
						btn_text = '关闭';
						data.command = 'close';
						break;
					case 5:
						btn_str = 'stop';
						btn_text = '停止';
						data.command = 'stop';
						break;
				}
				
				$.ajax({//执行ajax
					type : 'post',
					url : '/opendome',
					data : data,             
				    success: function (info) {
						that.device_nav.oDome_btn = btn_str;  //更改按钮样式
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
			},/*oDome_btn_command 结束*/
			guide_btn_command:function (n){
				var that = this; //存储vue实例化的对象
				var btn_str = ''; //控制按钮的样式
				var btn_text = '';
				var data = {at: at, command: '', at_aperture: aperture}; //提交的数据

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
				}
				
				$.ajax({//执行ajax
					type : 'post',
					url : '/guide',
					data : data,             
				    success: function (info) {
						that.device_nav.guide_btn = btn_str;  //更改按钮样式
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
			},/*guide_btn_command 结束*/
			guide_action_sbmt:function (){
				$.ajax({//执行ajax
					type : 'post',
					url : '/guide',
					data : this.guide_form.coverAction_form,              
				    success: function (info) {
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
				       layer.alert('网络异常,请再次提交', {shade:false, closeBtn:0});
				    },
				});			
			},/*guide_action_sbmt 结束*/
			guide_focus_sbmt:function () {
				//console.log(this.gimbal_status.curstatus);
			},/*guide_focus_sbmt 结束*/
		},/******methods 结束******/
	});/***************vue js结束*****************/
	/*定义一个值 标记页面当前在何位置 刷新页面后 还显示原来位置的页面*/
	var current_page_pos = localStorage.getItem('page_pos');

	switch (current_page_pos)
	{
		case 'gimbal'://转台
			vm.gimbal_click(); break;
		case 'ccd'://ccd
			vm.ccd_click(); break;
		case 'filter'://滤光片
			vm.filter_click(); break;
		case 'focus'://调焦器
			vm.focus_click(); break;
		case 'sDome'://随动圆顶
			vm.sDome_click(); break;
		case 'oDome'://全开圆顶
			vm.oDome_click(); break;
		case 'plan'://观测计划
			vm.plan_click(); break;
		case 'image'://观测图像
			vm.pic_click(); break;
	}
	/*定义一个值 标记页面当前在何位置 刷新页面后 还显示原来位置的页面*/

	var status_err = 0;
	//var plan_cooper_i = 0; //控制弹窗数量和导入动作执行的次数
	// localStorage.setItem('cooper', 0); //控制协同计划弹窗数量 刷新页面时要将此值置为0
	// localStorage.setItem('too', 0); //控制Too计划弹窗的变量 刷新页面时要将此值置为0

	function getStatus() //实时更新各设备状态
	{
		var cooper = localStorage.getItem('cooper'); //控制协同计划弹窗的变量
		var too = localStorage.getItem('too'); //控制Too计划弹窗的变量

		$.ajax({
			type : 'post',
			url : '/get_status', 
			data : {at_aperture: aperture},         
            success:  function (info) {
                var info = eval( '(' + info +')' );
				//显示最新的观测图片
				vm.new_png = info.new_png_pic;
				if ( info.fits_head ) vm.fits_head = eval( '(' + info.fits_head +')' ); //若info.fits_head 有值时
				
				if ( info.gimbal )  //显示转台状态信息
				{
					//show_gimbal_status (info.gimbal);
					//判断新的状态值与原有状态值是否一致 不一致时将
					let status = vm.gimbal_status.curstatus;
					if ( status && status != info.gimbal.curstatus ) //有值且与新值不一样
					{
						vm.gimbal_s = !vm.gimbal_s; //取反
					}
					vm.gimbal_status = info.gimbal;
				}

				if ( info.ccd )  //显示ccd状态信息
				{
					//判断新的状态值与原有状态值是否一致 不一致时将
					let status = vm.ccd_status.ccdCurStatus;
					if ( status && status != info.ccd.ccdCurStatus ) //有值且与新值不一样
					{
						vm.ccd_s = !vm.ccd_s; //取反
					}
					vm.ccd_status = info.ccd;
				}
				
				if ( info.focus ) //显示调焦器状态信息
				{
					//判断新的状态值与原有状态值是否一致 不一致时将
					let status = vm.focus_status.focusCurStatus;
					if ( status && status != info.focus.focusCurStatus ) //有值且与新值不一样
					{
						vm.focus_s = !vm.focus_s; //取反
					}
					vm.focus_status = info.focus;
				}
				
				if ( info.sDome )//显示随动圆顶状态信息
				{
					//判断新的状态值与原有状态值是否一致 不一致时将
					let status = vm.sDome_status.sDomeCurstatus;
					if ( status && status != info.sDome.sDomeCurstatus ) //有值且与新值不一样
					{
						vm.sDome_s = !vm.sDome_s; //取反
					}
					vm.sDome_status = info.sDome;
				}
				
				if ( info.filter ) //显示滤光片状态信息
				{
					//判断新的状态值与原有状态值是否一致 不一致时将
					let status = vm.filter_status.filterCurstatus;
					if ( status && status != info.filter.filterCurstatus ) //有值且与新值不一样
					{
						vm.filter_s = !vm.filter_s; //取反
					}
					vm.filter_status = info.filter;
				}

				if ( info.plan_cooper ) //处理协同计划信息
				{
					if ( !cooper || cooper < 1 )
					{
						if ( info.plan_cooper.data != '无协同计划' )	//有协同计划数据
						{
							localStorage.setItem('cooper', ++cooper);//将cooper加1
							let user = info.plan_cooper.submiter; //计划提交者
							let t = info.plan_cooper.time; //计划提交时间
							let s = '用户'+ user + '于'+ t + '提交协同观测计划，导入此页面请点确定';

							layer.alert(s, {
								shade:false,
								closeBtn:1,
								type:1,//alert默认是0，此时设为1,可防止被其他alert覆盖冲掉
								btn: ['确定', '不导入'],
								yes:function (n){//点击确定
									vm.plan_click();
									let  plan_cooper = info.plan_cooper.data;
									let cooper_arr = [];
									let ii = 0;

									for (let p in plan_cooper)
									{
										cooper_arr[ii] = plan_cooper[p];
										ii++;
									}
									
									table.datagrid({
										data: cooper_arr,
									});
									
									modeSpan.val('3');//将执行模式赋值为sequence
									
									$.ajax({//Ajax把plancooper中import字段变为1
										url: '/changeplancooperimport',
										type: 'post',
										data: {at_aperture: aperture, import:1},
										success: function (){
											localStorage.setItem('cooper', 0);//将cooper置为0
										},
										//error: function (){},
									})//ajax结束
									layer.close(n);
								},//yes函数结束
								btn2: function(index) {//点击不导入时
									$.ajax({//Ajax把plancooper中giveup字段变为1
										url: '/changeplancooperimport',
										type: 'post',
										data: {at_aperture: aperture, give_up:1},
										success: function (){
											localStorage.setItem('cooper', 0);//将cooper置为0
										},
										//error: function (){},
									})//ajax结束
									layer.close(index);
								},//btn2函数结束
								cancel: function() {//关闭按钮
									localStorage.setItem('cooper', 0);//将cooper置为0
								}
							});
						}//处理协同计划数据 结束
					}
				}//处理协同计划信息 结束

				if ( info.plan_too ) //处理ToO计划信息
				{
					if ( !too || too < 1 )//控制弹窗个数，只弹出1个
					{
						if ( info.plan_too.data != '无ToO计划' )	//有ToO计划数据
						{
							localStorage.setItem('too', ++too);//将too加1
							let user = info.plan_cooper.submiter; //计划提交者
							let t = info.plan_cooper.time; //计划提交时间
							let s = '用户'+ user + '于'+ t + '提交协同观测计划，导入此页面请点确定';

							layer.alert(s, {
								shade:false,
								type: 1,
								closeBtn:1,
								btn: ['确定', '不导入'],
								yes:function (n){//点击确定
									too_import = true; //此时停止按钮不会弹框
									vm.plan_click();

									let  plan_too = info.plan_too.data;
									let too_arr = [];
									let ii = 0;
									
									for (let p in plan_too)
									{
										too_arr[ii] = plan_too[p];
										ii ++;
									}
									
									table.datagrid({
										data: too_arr,
									});
						
									//modeSpan.val(info.plan_too.exemode);//将执行模式赋值
									modeSpan.val('3');//将执行模式赋值为sequence

									//执行提交计划指令
									planStop.click();
									for (let i=0; i < 10000000; i++) { i*1; i/1; } //空循环 延迟1秒
									planStart.click(); //点击 '开始按钮'（执行：提交和开始2个操作）
									//执行提交计划指令
									$.ajax({//Ajax把plantoo中import字段变为1
										url: '/changeplantooimport',
										type: 'post',
										data: {at_aperture: aperture, import: 1},
										success: function (){
											localStorage.setItem('too', 0);//将too 置为0
										},
										//error: function (){},
									})//ajax结束
									layer.close(n);
								},//yes函数结束
								btn2: function(index) {//点击不导入时
									$.ajax({//Ajax把plantoo中giveup字段变为1
										url: '/changeplantooimport',
										type: 'post',
										data: {at_aperture: aperture, give_up: 1},
										success: function (){
											localStorage.setItem('too', 0);//将too 置为0
										},
										//error: function (){},
									})//ajax结束
									layer.close(index);
									//layer.msg('算了，算了,不删了...');
								},//btn2函数结束
								cancel: function () {
									localStorage.setItem('too', 0);//将too 置为0
								}
							});
						}//处理协同计划数据 结束
					}
				}//处理ToO计划信息 结束
            },/* success方法 结束*/
			error: function (){
				status_err ++;
				if (status_err == 1) clearInterval(status_interval);
				layer.alert('网络异常,设备实时数据无法获取!', {shade:false, closeBtn:0});
			},
		});
	}//getStatus() //实时更新各设备状态 结束
	var status_interval = setInterval (getStatus, 1800);  //实时显示各设备状态信息

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
})