/****atpage页面之datagrid插件 js***/
//观测计划的赤经和赤纬 js事件//////////////////////////////
	//赤经 的js事件//////////////////////////////////
	//赤经之小时 js事件
	//console.log(configData.ccd[0].lowcoolert);
	$('#planInfo').on('keyup', 'td[field="rightAscension1"] input.textbox-text', function () {
		var that = $(this);
		var patn = /^\d{2}$/;
		var v = $.trim(that.val());
		//v_R = v.replace(/(-|\+)/g, ''); //将+或-替换为空字符
		if (patn.test(v))
		{
			that.blur();
		}
	});
	
	//赤经之小时 blur事件
	$('#planInfo').on('blur', 'td[field="rightAscension1"] input.textbox-text', function () {
		var that = $(this);
		var patn = /^\d{1,2}$/;
		var v = $.trim(that.val());
		var parent_td_rightAscension1 = that.closest('td[field="rightAscension1"]'); //当前input的父元素:td[field="rightAscension1"]
		var td_rightAscension2 = parent_td_rightAscension1.siblings('td[field="rightAscension2"]'); //同辈的：td[field="rightAscension2"]
		
		if (!patn.test(v) || v >= 24 || v < 0)
		{
			layer.tips('参数超限', that, {tips : 1,tipsMore: true});
		}else{//focus进入到分钟的input
			td_rightAscension2.find('input.textbox-text').focus();
		}
	});
	
	//赤经之分钟 js事件
	$('#planInfo').on('keyup', 'td[field="rightAscension2"] input.textbox-text', function () {
		var that = $(this);
		var patn = /^\d{2}$/;
		var v = $.trim(that.val());
		if (patn.test(v))
		{
			that.blur();
		}
	})
	
	//赤经之分钟 blur事件
	$('#planInfo').on('blur', 'td[field="rightAscension2"] input.textbox-text', function () {
		var that = $(this);
		var patn = /^\d{1,2}$/;
		var v = $.trim(that.val());
		var parent_td_rightAscension2 = that.closest('td[field="rightAscension2"]'); //当前input的父元素:td[field="rightAscension2"]
		var td_rightAscension3 = parent_td_rightAscension2.siblings('td[field="rightAscension3"]'); //同辈的：td[field="rightAscension3"]

		if (!patn.test(v) || v < 0 || v >= 60)
		{
			layer.tips('参数超限', that, {tips: 1, tipsMore: true});
		}else{//focus进入到秒的input
			td_rightAscension3.find('input.validatebox-text').focus();
		}
	
	})
	
	//赤经之秒 js事件
	$('#planInfo').on('blur', 'td[field="rightAscension3"] input.textbox-text', function () {
		var that = $(this);
		var v = $.trim($(this).val());
		if (!$.isNumeric(v) || v >= 60 || v < 0)
		{
			layer.tips('秒参数超限', that, {tips : 1,tipsMore: true});
		}	
	})
	//赤经 的js事件 结束/////////////////////////////////////
	
	//赤纬 的js事件//////////////////////////////////
	//赤纬之小时 js事件
	$('#planInfo').on('keyup', 'td[field="declination1"] input.textbox-text', function () {
		var that = $(this);
		var v = $.trim(that.val());
		v_R = v.replace(/-/, ''); //将-替换为空字符
		if (v_R.length == 2)
		{
			that.blur();
		}
	});
	
	//赤纬之小时 blur事件
	$('#planInfo').on('blur', 'td[field="declination1"] input.textbox-text', function () {
		var that = $(this);
		var v = $.trim(that.val());
		var patn = /^-?\d{1,2}$/;
		var parent_td_declination1 = that.closest('td[field="declination1"]'); //当前input的父元素:td[field="declination1"]
		var td_declination2 = parent_td_declination1.siblings('td[field="declination2"]'); //同辈的：td[field="declination2"]

		if (!patn.test(v) || v > 90 || v < -90)
		{
			layer.tips('参数超限', that, {tips : 1,tipsMore: true});
		}else{//focus进入到分钟的input
			td_declination2.find('input.validatebox-text').focus();
		}
	
	});
	
	//赤纬之分钟 js事件
	$('#planInfo').on('keyup', 'td[field="declination2"] input.textbox-text', function () {
		var that = $(this);
		var patn = /^\d{2}$/;
		var v = $.trim(that.val());
		if (patn.test(v))
		{
			that.blur();
		}
	})
	
	//赤纬之分钟 blur事件
	$('#planInfo').on('blur', 'td[field="declination2"] input.textbox-text', function () {
		var that = $(this);
		var v = $.trim(that.val());
		var patn = /^\d{1,2}$/;
		var parent_td_declination2 = that.closest('td[field="declination2"]'); //当前input的父元素:td[field="declination2"]
		var td_declination3 = parent_td_declination2.siblings('td[field="declination3"]'); //同辈的：td[field="declination3"]
		
		if (!patn.test(v) || v > 59 || v < 0)
		{
			layer.tips('参数超限', that, {tips : 1,tipsMore: true});
		}else{//focus进入到秒的input
			td_declination3.find('input.validatebox-text').focus();
		}
	});
	
	//赤纬之秒 js事件
	$('#planInfo').on('blur', 'td[field="declination3"] input.textbox-text', function () {
		var that = $(this);
		var v = $.trim(that.val());
		if (!$.isNumeric(v) || v >= 60 || v < 0)
		{
			layer.tips('参数超限', that, {tips : 1,tipsMore: true});
		}	
	})
	//赤纬 的js事件//////////////////////////////////
//观测计划的赤经和赤纬 js事件//////////////////////////////
	var table = $('#dg'); //定义全局table 变量
	var editRow = undefined;  //全局开关, 编辑
	var planForm = $('#imptPlan');
	
//导入计划文件 上传////////////////////////////////////////////
	function importPlan ()
	{
		//var planForm = $('#imptPlan');
		var plan = planForm.find('input');
		planForm[0].reset();//重置表单数据
		plan.click(); //打开上传文件的选择窗口
		
		plan.one('change', function () {
			var formData = new FormData(planForm[0]);

			$.ajax({
				type: 'post',
	            url : '/importplan',
	            data : formData,
				processData : false,
				contentType : false, 
	            success: function (info) {
					if (!info.match("^\{(.+:.+,*){1,}\}$"))  //非json数据
					{
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
					}else{			
						var info = $.parseJSON(info);
						var arr = [];
						var ii = 0;
						for (var p in info)
						{
							arr[ii] = info[p];
							ii ++;
						}
						
						table.datagrid({
								data: arr,
						});
						
						editRow = undefined; //否则 导入后无法插入新行
						planErr = 0; //将提交计划的错误标识 改为0
					}
					
				},
	            error:  function () {
	               layer.alert('网络异常,请重新导入计划', {shade:false, closeBtn:0});
	            },
			});
		});
	}
	
	//添加计划 /////////////////////////////////
	/*function addPlan ()
	{
		if (editRow == undefined)
		{
			var plans = table.datagrid('getRows');
			var n = plans.length;
			table.datagrid('insertRow', {
			index:n,
			row:{},
			});
			//将第一行设为 可编辑
			table.datagrid('beginEdit', n);
			editRow = n;
		}
		
	}
	
	//编辑计划 /////////////////////////////////
	function editPlan ()
	{
		var rows = table.datagrid('getSelections');
		if(rows.length == 1)
		{
			if(editRow != undefined)
			{
				table.datagrid('endEdit', editRow);
				editRow = undefined;
				table.datagrid('unselectRow', index);
			}
			
			if(editRow == undefined)
			{
				//获取当前选中的行索引
				var index = table.datagrid('getRowIndex', rows[0]);
				table.datagrid('beginEdit', index);
				table.datagrid('unselectRow', index);
				editRow = index;
			}
		}else{
			$.messager.alert('警告', '请选择一行做编辑', 'warning');
		}
	}
	
	//保存编辑 /////////////////////////////////
	function savePlan ()
	{
		//将第一行设为 结束编辑
		table.datagrid('endEdit', editRow);
		var res = table.datagrid('validateRow', editRow); //验证编辑的行
		if (!res)
		{
			alert('请检查第' + (editRow+1) + '行数据!');return;
			
		}
		table.datagrid('enableDnd');//编辑保存后启用拖放

	}*/
	
	//添加计划 /////////////////////////////////////
	function addPlan ()
	{
		if (editRow == undefined)
		{
			//获取被选中的行
			var selectRows = table.datagrid('getSelections');
			var num = selectRows.length;
			
			if (num == 0)
			{//未选中任一行 直接添加一个新行
				var plans = table.datagrid('getRows');
				var n = plans.length;
				table.datagrid('insertRow', {
				index:n, //在最后面新加一空行
				row:{},
				});
				//将此新加的一行设为可编辑
				table.datagrid('beginEdit', n);
				editRow = n;
			}else if (num > 1){//选中的多于1行
				layer.alert('添加时:只能选择一条数据!', {shade:false, closeBtn:0});return;
			}else if (num == 1){
				var num = table.datagrid('getRowIndex', selectRows[0]);
				table.datagrid('insertRow', {
					index : num + 1, //在选中行后面 新加一空行
					row:{},
				});
				
				//将此新加的一行设为可编辑
				table.datagrid('beginEdit', num + 1);
				editRow = num + 1;
			}
			//滚动至新插入的行那里
			table.datagrid('scrollTo', editRow);
		}else{
			layer.alert('请先保存编辑的第'+ (editRow+1) +'条数据!', {shade:false, closeBtn:0});return;
		}
		
	}//添加计划  结束/////////////////////////////////////
	
	//保存编辑 /////////////////////////////////
	function savePlan ()
	{
		//将编辑行设为 结束编辑
		table.datagrid('endEdit', editRow);
		var res = table.datagrid('validateRow', editRow); //验证编辑的行
		if (!res)
		{
			layer.alert('请检查第' + (editRow+1) + '行必填数据!', {shade:false, closeBtn:0}); return;
			
		}
		
		editRow = undefined;  //将editRow 置为初始的undefined
		planErr = 0;	//将提交计划的错误标识改为0
	}

	//datagrid 属性////////////////////////////////////////
	var targetType = [
	{typeId:'0',name:'恒星'},
	{typeId:'1',name:'太阳'},
	{typeId:'2',name:'月亮'},
	{typeId:'3',name:'彗星'},
	{typeId:'4',name:'行星'},
	{typeId:'5',name:'卫星'},
	{typeId:'6',name:'固定位置'},
	{typeId:'7',name:'本底'},
	{typeId:'8',name:'暗流'},
	{typeId:'9',name:'平场'},];
	
	var epochData = [
	{epochId:'0',name:'Real'},
	{epochId:'1',name:'J2000'},
	{epochId:'2',name:'B1950'},
	{epochId:'3',name:'J2050'},];
	
	//将固定属性中的滤光片名称:u/v/b，对变量filterData进行赋值
	var filterData = [];
	var plan_filter_option = configData.filter.filtername;
	var filterData_num = plan_filter_option.length;
	for (var filterData_i = 0; filterData_i < filterData_num; filterData_i++)
	{
		filterData[filterData_i] = {filterId:'', name:''};
		filterData[filterData_i].filterId = plan_filter_option[filterData_i];
		filterData[filterData_i].name = plan_filter_option[filterData_i];
	}

	/*var filterData = [
	{filterId:'U',name:'U'},
	{filterId:'B',name:'B'},
	{filterId:'V',name:'V'},
	{filterId:'R',name:'R'},
	{filterId:'I',name:'I'},
	];*/
	
	$(function(){
		//var table_w = ( $(window).width() ) * 0.889546;
		var table_w = ( $('#planInfo').width() ) * 1;
		table.datagrid({
			width: table_w,
			height:400,
			toolbar: '#toolbar',
			checkOnSelect:true,
			selectOnCheck:true,
			singleSelect:false,
			striped: true,
			rownumbers:true,
			/*idField: 'id',
			fitColumns:false,
			dropAccept:'tbody tr',
			singleSelect:true,
			dragSelection: true,
			onLoadSuccess: function(){
				$(this).datagrid('enableDnd'); //启用拖放
			},*/

			columns:[[
			{field:'id', title:'id', checkbox:true, rowspan:2},
			{field:'target',  title:'目标名称', width:table_w*0.118666667,rowspan:2,
				editor:{
					type:'validatebox',
					options:{required:true,missingMessage:'目标名必填!'},
				},
			},
			{field:'type', title:'目标类型', width:table_w*0.0653333333,rowspan:2,
				formatter:function(value){
					for(var i=0; i<targetType.length; i++){
						if (targetType[i].typeId == value) return targetType[i].name;
					}
					return value;
				},
				
				editor:{
					type:'combobox',
					options:{
						valueField:'typeId',
						textField:'name',
						data:targetType,
						required:true,
						missingMessage:'目标类型必选!'
					},
				},
			},
			{title:'赤经', colspan:5, width:table_w*0.1375,halign:'center'},
			{title:'赤纬', colspan:5, width:table_w*0.1375,halign:'center'},
			{field:'epoch', title:'历元',  width:table_w*0.055, rowspan:2,
				formatter:function(value){
					for(var i=0; i<epochData.length; i++){
						if (epochData[i].epochId == value) return epochData[i].name;
					}
					return value;
				},
				
				editor:{
					type:'combobox',
					options:{
						valueField:'epochId',
						textField:'name',
						data:epochData,
						required:true,
						missingMessage:'历元必选!'
					},
				},
			},
			{field:'exposureTime', title:'曝光时间',  width:table_w*0.059,rowspan:2,
				editor:{
					type:'numberbox',
					options:{
						required:true,
						missingMessage:'曝光时间必填!'
					},
				},
			},
			{field:'delayTime', title:'delayTime', width:table_w*0.069166667, rowspan:2,
				editor:{
					type:'numberbox',
				},
			},
			{field:'exposureCount', title:'曝光数量', width:table_w*0.059, rowspan:2, 
				editor:{
					type:'numberbox',
					options:{
						required:true,
						missingMessage:'曝光数量必填!'
					},
				},
			},
			{field:'filter', title:'滤光片',  width:table_w*0.045666667, rowspan:2,
				formatter:function(value){
					for(var i=0; i<filterData.length; i++){
						if (filterData[i].filterId == value) return filterData[i].name;
					}
					return value;
				},
				
				editor:{
					type:'combobox',
					options:{
						valueField:'filterId',
						textField:'name',
						data:filterData,
						required:true,
						missingMessage:'滤光片必选!'
					},
				},
			},
			{field:'gain', title:'增益',  width:table_w*0.032166667, rowspan:2,
				editor:{
					type:'numberbox',
				},
			},
			{field:'bin',  title:'Bin', width:table_w*0.026166667, rowspan:2,
				editor:{
					type:'numberbox',
				},
			},
			{field:'readout', title:'读出速度', width:table_w*0.059166667, rowspan:2,
				editor:{
					type:'numberbox',
					options:{
						required:true,
						missingMessage:'读出速度必填!'
					},
				},
			},
			{field:'del', title:'删除',  width:table_w*0.031266666, rowspan:2,
				formatter:function(value,row,index){
					return '<a onclick="delPlan1(this)">删除</a> ';
				}
			},
		],[
			{field:'rightAscension1', width:table_w*0.0325, title:'时',
				editor:{
					type:'numberbox',
					options:{required:true,missingMessage:'时,必填!'},
				},
			},
			{field:'c1', width:table_w*0.013,
				formatter:function(value,row,index){
					return ':';
				}
			},
		
			{field:'rightAscension2',  width:table_w*0.0325,title:'分',
				editor:{
					type:'numberbox',
					options:{required:true,missingMessage:'分,必填!'},
				},
			},
			{field:'c2', width:table_w*0.013,
				formatter:function(value,row,index){
					return ':';
				}
			},
			{field:'rightAscension3', width:table_w*0.063333333,title:'秒',
				editor:{
					type:'validatebox',
					options:{required:true,missingMessage:'秒,必填!'},
				},
			},
	
			{field:'declination1', width:table_w*0.0325, title:'时',
				editor:{
					type:'numberbox',
					options:{required:true,missingMessage:'时,必填!'},
				},
			},
			{field:'c3', width:table_w*0.013,
				formatter:function(value,row,index){
					return ':';
				}
			},
	
			{field:'declination2', width:table_w*0.0325, title:'分',
				editor:{
					type:'numberbox',
					options:{required:true,missingMessage:'分,必填!'},
				},
			},
			{field:'c4', width:table_w*0.013,
				formatter:function(value,row,index){
					return ':';
				}
			},
			{field:'declination3', width:table_w*0.063333333,title:'秒',
				editor:{
					type:'validatebox',
					options:{required:true,missingMessage:'秒,必填!'},
				},
			},
		]],
		//在双击一个单元格的时候开始编辑并生成编辑器，然后定位到编辑器的输入框上
		onDblClickCell: function(index,field,value){
			if (editRow == undefined && field != 'id') {
				table.datagrid('beginEdit', index); //对点击行 进行编辑
				var ed = table.datagrid('getEditor', {index:index,field:field});
				$(ed.target).focus();
				editRow = index;
			}	
		},
	});/*table.datagrid() 结束*/
}) //jquery 结束编辑
	//自定义的函数
	function getrow(target){
		var tr = $(target).closest('tr.datagrid-row');
		return parseInt(tr.attr('datagrid-row-index'));
	}
	function delPlan1(target){
		table.datagrid('deleteRow', getrow(target));
		editRow = undefined;
}

//观测计划的 开始 停止 下一个按钮////////////////////////////
//观测计划的开始 按钮//////////////////////////////////
	var planStart = $('#planStart'); //计划开始 按钮
	var planStop = $('#planStop'); //计划停止 按钮
	var planErr = 0; //观测计划发送时的错误标识
	$('#planModes').on('click', 'button', function () {
		
		//获取模式值
		var modeVal = $('#modeSpan').val();
		var option = $(this).attr('id');
		var btnText = $(this).html();
		var rows = table.datagrid('getSelections');
		var index = table.datagrid('getRowIndex', rows[0]);
	
		if(index < 0){
			index = 0;
		}
		
		/* //若为single和singleLoop模式，只能选择一条发送
		if (modeVal == 1 || modeVal ==2)
		{
			if(rows.length > 1)
			{
				alert('single和singleLoop模式只能选择一条计划!');
				return;
			}
			
		} */
		if (planErr === 0)
		{
			$.ajax({
				type : 'post',
				url : '/plan',
				data : {
					planOption : option,
					mode : modeVal,
					start : index +1,
					command : 2, //标识 plan.php控制器中用以区别要执行的函数
					at : at,
					at_aperture: aperture,
				},             
	            success:  function (info) {
		            planErr = 0;
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
					if (info.indexOf('计划开始') !== -1)
					{
						//planStart.prop('disabled', true);
						//planStop.prop('disabled', false);
					}
					
					if (info.indexOf('计划停止') !== -1)
					{
						//planStart.prop('disabled', false);
					}
	            },
	            error:  function () {
				   layer.alert('网络异常,请再次点击'+ btnText +'按钮!', {shade:false, closeBtn:0});
	            },
			});
		}
	
	});
	//观测计划的开始 按钮 结束/////////////////////////////
//观测计划的 开始 停止 下一个按钮 结束//////////////////////////////


//保存并提交计划 ////////////////////////////////////////////
	function submitPlan ()
	{
		var plans = table.datagrid('getRows');	//选中所有记录
		
		var n = plans.length;
		if ( n< 1) 
		{
			planErr = 1;
			layer.alert('请先导入计划或添加计划!', {shade:false, closeBtn:0});
		}
		
		if (!valid())	//执行验证
		{
			planErr = 1;
			layer.alert('请先保存计划或检查第' + (editRow+1) + '行必填数据!', {shade:false, closeBtn:0});
		}

		var msg = plan_valid(plans, n);  //js验证数据

		if ( msg !== '')
		{
			layer.alert(msg, {shade:false, closeBtn:0});return;
		}
		
		if (planErr === 0)  //ajax 发送数据到后台
		{
			$.ajax({
				type: 'post',
				url: '/plan',
				data: {
					planData: plans,
					command : 1,  //标识 plan.php控制器中用以区别要执行的函数
					at : at,
					at_aperture:aperture,
					plan_filter_option : plan_filter_option, //将该望远镜filter的['u','v','b']提交
					maxExpose: configData.ccd[0].maxexposuretime,
					minExpose: configData.ccd[0].minexposuretime,
				},
				success: function (info){
					planErr = 0;

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

					if (info.indexOf('观测计划发送完毕') !== -1)
					{
						//planStart.prop('disabled', false); //启用 计划开始按钮
					}
				},
				error: function (){
					layer.alert('网络异常,请重新提交计划！', {shade:false, closeBtn:0});
				},
			});
		}
	}//保存并提交计划  结束///////////////////////////////////

	/*
	* plan_valid() 验证计划的数据
	* 参数： plans 各条计划数据
	* 参数： n 计划的条数
	* return： 错误提示
	*/
	function plan_valid(plans, n)
	{
		var msg = ''; //错误提示
		var plan_types = []
		for(var i = 0; i < n; i++)
		{
			var plan_target = $.trim( plans[i].target );
			var patn = /([\u4e00-\u9fa5]| )+/;
			if ( patn.test(plan_target) || plan_target == '' || plan_target.length > 48 )
			{
				msg += '第' + (i+1) + '条目标名格式错误!<br>';
			}

			var plan_type = $.trim( plans[i].type );
			patn = /^[0-9]$/;

			if ( !patn.test(plan_type) && ( $.inArray(plan_type, ['恒星','太阳','月亮','彗星','行星','卫星','固定位置','本底','暗流','平场']) == -1)  )
			{
				msg += '第' + (i+1) + '条目标类型超限!<br>';
			}

			var asc1 = $.trim( plans[i].rightAscension1 )*1;
			patn  = /^\d{1,2}$/;
			
			if ( !patn.test(asc1) || asc1 > 24 || asc1 < 0 )
			{
				msg += '第' + (i+1) + '条赤经小时参数超限!<br>';
			}
			
			var asc2 = $.trim( plans[i].rightAscension2 )*1;
			
			if ( !patn.test(asc2) || asc2 > 59 || asc2 < 0 )
			{
				msg += '第' + (i+1) + '条赤经分钟参数超限!<br>';
			}

			var asc3 = $.trim( plans[i].rightAscension3 )*1;
			
			if ( !$.isNumeric(asc3) || asc3 >= 60 || asc3 < 0 )
			{
				msg += '第' + (i+1) + '条赤经秒参数超限!<br>';
			}

			var asc = asc1 + asc2/60 + asc3/3600;
			if ( asc > 24 || asc < 0 )
			{
				msg += '第' + (i+1) + '条赤经参数超限!<br>';
			}

			var dec1 = $.trim( plans[i].declination1 )*1;
			patn = /^-?\d{1,2}$/;

			if ( !patn.test(dec1) || dec1 > 90 || dec1 < -90 )
			{
				msg += '第' + (i+1) + '条赤纬小时参数超限!<br>';
			}

			var dec2 = $.trim( plans[i].declination2 )*1;
			patn = /^\d{1,2}$/;

			if ( !patn.test(dec2) || dec2 > 59 || dec2 < 0 )
			{
				msg += '第' + (i+1) + '条赤纬分钟参数超限!<br>';
			}

			var dec3 = $.trim( plans[i].declination3 )*1;

			if ( !$.isNumeric(dec3) || dec3 >= 60 || dec3 < 0 )
			{
				msg += '第' + (i+1) + '条赤纬秒参数超限!<br>';
			}

			var dec = Math.abs(dec1) + dec2/60 + dec3/3600;
			if ( dec > 90 || dec < 0 )
			{
				msg += '第' + (i+1) + '条赤纬参数超限!<br>';	
			}

			var plan_epoch = $.trim(plans[i].epoch).toLocaleLowerCase();
			patn = /^[0-3]$/;

			if ( !patn.test(plan_epoch) && ( $.inArray(plan_epoch, ['real','j2000','b1950','j2050']) == -1)  )
			{
				msg += '第' + (i+1) + '条历元超限!<br>';
			}

			var plan_exposureTime = $.trim(plans[i].exposureTime);

			if ( !$.isNumeric(plan_exposureTime) || plan_exposureTime > configData.ccd[0].maxexposuretime*1 || plan_exposureTime < configData.ccd[0].minexposuretime*1 )
			{
				msg += '第' + (i+1) + '条曝光时间超限（'+ configData.ccd[0].minexposuretime + '~' +configData.ccd[0].maxexposuretime+'）!<br>';
			}

			var plan_delayTime = $.trim(plans[i].delayTime);

			if ( !$.isNumeric(plan_delayTime) || plan_delayTime < 0 )
			{
				msg += '第' + (i+1) + '条延迟时间超限!<br>';
			}

			var plan_expCount = $.trim(plans[i].exposureCount);
			patn = /^\d+$/;
			if ( !patn.test(plan_expCount) || plan_expCount < 1 )
			{
				msg += '第' + (i+1) + '条曝光数量超限!<br>';
			}

			var plan_filter = $.trim(plans[i].filter);
			//patn = /^[0-9]$/;
			//if ( !patn.test(plan_filter) && ( $.inArray(plan_filter, plan_filter_option) == -1) )
			if ( $.inArray(plan_filter, plan_filter_option) == -1 )
			{
				msg += '第' + (i+1) + '条滤光片超限!<br>';
			}

			var plan_gain = $.trim(plans[i].gain);
			patn = /^\d+$/;
			if ( !patn.test(plan_gain) || plan_gain < 1 )
			{
				msg += '第' + (i+1) + '条增益超限!<br>';
			}

			var plan_bin = $.trim(plans[i].bin);
			patn = /^\d+$/;
			if ( !patn.test(plan_bin) || plan_gain < 1 )
			{
				msg += '第' + (i+1) + '条bin超限!<br>';
			}

			var plan_readout = $.trim(plans[i].readout);
			patn = /^\d+$/;
			if ( !patn.test(plan_readout) || plan_gain < 1 )
			{
				msg += '第' + (i+1) + '条读出速度超限!<br>';
			}
		}
		return msg;
	}/*plan_valid  结束*/
	
//数据验证函数 /////////////////////////////////////////////
	function valid ()
	{
		if (editRow === undefined)
		{//从未进行编辑某一行
			return true;
		}
		
		var res = table.datagrid('validateRow', editRow); //验证编辑的行
		if (!res)
		{//编辑行 必填项有未填写的数据
			return false;			
		}
	}
	
	$('#tst').click(function () {
		/* tstdata = table.datagrid('clearSelections');
		var n = tstdata.length;
		
		console.log(tstdata); */
		//让正在执行的计划那行 执行mouseover(),此处eq()的2 就是正执行的行索引
		
	})
//数据验证函数 结束////////////////////////////////////////

/*******实时获取 获取正在执行的计划*******/
function get_plan () {
	$.ajax({
		url: '/plan',
		type: 'post',
		data: {at: at, command: 'get_plan', at_aperture:aperture},
		success: function (info) {
			
			if ( info.indexOf("{") === -1 )
			{//非json数据
				layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}
						if ( info.indexOf('无正执行计划') !== -1 )
						{
							//clearInterval(get_plan_i); //清楚定时器
						}
					},
				});
			}else{
				var info = info.split('#'); //将计划数据与tag组成的字符串从其中的'#'分割为数组
				var plan_tag = info[1];
				/*var plan = $.parseJSON(info[0]);

				var arr = [];
				var ii = 0;
				for (var p in plan)
				{
					arr[ii] = plan[p];
					ii ++;
				}

				table.datagrid({
						data: arr,
				}); */
				
				//editRow = undefined; //否则 导入后无法插入新行
				planErr = 0; //将提交计划的错误标识 改为0
				
				table.datagrid('scrollTo', plan_tag-1); //滚动到第tag行
				table.datagrid('highlightRow', plan_tag-1); //高亮第tag行		
			}
		},//success 方法结束
	})/*ajax 结束*/
}

//var get_plan_i = setInterval (get_plan, 60000); //定时执行get_plan() 60秒执行一次查询，否则浏览器卡顿
var get_plan_i = 0;
 function plan_executing (){ //显示正在执行的计划
	get_plan();
	//get_plan_i = setInterval (get_plan, 5000);
 }
/*******实时获取 获取正在执行的计划 结束*******/