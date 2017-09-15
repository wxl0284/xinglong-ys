	var table = $('#dg'); //定义全局table 变量
	var editRow = undefined;  //全局开关, 编辑
	//导入计划文件 上传////////////////////////////////////////////
	function importPlan ()
	{
		$(function (){
			var planForm = $('#imptPlan');
			var plan = planForm.find('input');
			planForm[0].reset();//重置表单数据
			plan.click(); //打开上传文件的选择窗口
			
			plan.one('change', function () {
				var formData = new FormData(planForm[0]);
				$.ajax({
					type: 'post',
		            url : '/xinglong/at60/importPlan',
		            data : formData,
					processData : false,
					contentType : false, 
		            success: function (info) {
						if (!info.match("^\{(.+:.+,*){1,}\}$"))
						{//非json数据
							alert(info);
							if (info.indexOf('登录') !== -1)
							{
								location.href = '/';
							}
						}else{
							var datas = eval('(' + info + ')');
							var arr = [];
							var ii = 0;
							for (var p in datas)
							{
								arr[ii] = datas[p];
								ii ++;
							}
							table.datagrid({
									data: arr,
							});
							editRow = undefined; //新加
						}
						
					},
		            error:  function () {
		               alert('网络异常,请重新导入计划');
		            },
				});
			});

		});
	}
	
	//添加计划 /////////////////////////////////
	function addPlan ()
	{
		if (editRow == undefined)
		{
			table.datagrid('insertRow', {
			index:0,
			row:{},
			});
			//将第一行设为 可编辑
			table.datagrid('beginEdit', 0);
			editRow = 0;
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
	
	var filterData = [
	{filterId:'U',name:'U'},
	{filterId:'B',name:'B'},
	{filterId:'V',name:'V'},
	{filterId:'R',name:'R'},
	{filterId:'I',name:'I'},];
	
	$(function(){
		table.datagrid({
			idField: 'id',
			width:1200,
			height:500,
			toolbar: '#toolbar',
			singleSelect:true,
			checkOnSelect:true,
			selectOnCheck:true,
			dragSelection: true,
			striped: true,
			dropAccept:'tbody tr',
			rownumbers:true,
			//fitColumns:false,
			onLoadSuccess: function(){
				$(this).datagrid('enableDnd');
			},

			columns:[[
			{field:'id', title:'id', width:5, checkbox:true,},
			{field:'target', title:'观测目标名', width:120,
				editor:{
					type:'validatebox',
					options:{required:true,missingMessage:'目标名必填!'},
				},
			},
			{field:'type', title:'目标类型', width:100,
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
					},
				},
			},
			{field:'rightAscension', title:'赤经', width:80,
				editor:{
					type:'numberbox',
					options:{required:true,},
				},
			},
			{field:'declination', title:'赤纬', width:80,
				editor:{
					type:'numberbox',
					options:{required:true,},
			}},
			{field:'epoch', title:'历元', width:80,
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
					},
				},
			},
			{field:'exposureTime', title:'曝光时间', width:80,
				editor:{
					type:'numberbox',
					options:{required:true,},},
			},
			{field:'delayTime', title:'delayTime', width:80,
				editor:{
					type:'numberbox',
					options:{required:true,},},
			},
			{field:'exposureCount', title:'曝光数量', width:80,
				editor:{
					type:'numberbox',
					options:{required:true,},},
			},
			{field:'filter', title:'滤光片', width:80,
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
					},
				},
			},
			{field:'gain', title:'增益', width:80,
				editor:{
					type:'numberbox',
					options:{required:true,},},
			},
			{field:'bin', title:'Bin', width:80,
				editor:{
					type:'numberbox',
					options:{required:true,},},
			},
			{field:'readout', title:'读出速度', width:80,
				editor:{
					type:'numberbox',
					options:{required:true,},},
			},
			{field:'del', title:'删除', width:80,
				formatter:function(value,row,index){
					return '<a onclick="delPlan1(this)">删除</a> ';
				}
			},
		]],
		onAfterEdit: function (rowIndex, rowData, changes){
			editRow = undefined;
		},
	});
}) //jquery 结束编辑
	//自定义的函数
	function getrow(target){
		var tr = $(target).closest('tr.datagrid-row');
		return parseInt(tr.attr('datagrid-row-index'));
	}
	function delPlan1(target){
		table.datagrid('deleteRow', getrow(target));
		editRow = undefined; //新加
}

//观测计划的 开始 停止 下一个按钮//////////////////////////////
	//观测计划的开始 按钮//////////////////////////////////
	$('#planModes').on('click', 'button', function () {
		//获取模式值
		var modeVal = $('#planModes').find('input:checked').val();
		var option = $(this).attr('id');
		var btnText = $(this).html();
		var rows = table.datagrid('getSelections');
		var index = table.datagrid('getRowIndex', rows[0]);
		//alert(index);return;
		if(option === 'planStart')
		{
			submitPlan();
			
		}
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
		$.ajax({
            type : 'post',
            url : '/xinglong/at60/at60PlanOption',
            data : {planOption : option,
					mode : modeVal,
					start : index +1,
					},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
				
				if (info.indexOf('计划停止') !== -1)
				{
					$('#planStart').show();
				}
            },
            error:  function () {
               alert('网络异常,请再次点击'+ btnText +'按钮!');
            },
        });
	
	});
	//观测计划的开始 按钮 结束/////////////////////////////
//观测计划的 开始 停止 下一个按钮 结束//////////////////////////////


//保存并提交计划 ////////////////////////////////////////////
	function submitPlan ()
	{
		var plans = table.datagrid('getRows');	//选中所有记录
		//console.log(plans);return;
		var n = plans.length;
		if ( n< 1) 
		{
			alert('无计划数据，请先导入计划或添加计划!');return;
		}
		//console.log(a.length);return;
		
		//js验证数据
		for(var i = 0; i < n; i++)
		{
			if ($.trim(plans[i].target) === '')
			{
				alert('请填写第' + (i+1) + '条计划:目标名称!');return;
			}
			if ($.trim(plans[i].type) === '')
			{
				alert('请选择第' + (i+1) + '条计划:目标类型!');return;
			}
			if ($.trim(plans[i].rightAscension) === '')
			{
				alert('请填写第' + (i+1) + '条计划:赤经!');return;
			}
			if ($.trim(plans[i].declination) === '')
			{
				alert('请填写第' + (i+1) + '条计划:赤纬!');return;
			}
			if ($.trim(plans[i].epoch) === '')
			{
				alert('请选择第' + (i+1) + '条计划:历元!');return;
			}
			if ($.trim(plans[i].exposureTime) === '')
			{
				alert('请填写第' + (i+1) + '条计划:曝光时间!');return;
			}
			if ($.trim(plans[i].delayTime) === '')
			{
				alert('请填写第' + (i+1) + '条计划:delayTime!');return;
			}
			if ($.trim(plans[i].exposureCount) === '')
			{
				alert('请填写第' + (i+1) + '条计划:曝光数量!');return;
			}
			if ($.trim(plans[i].filter) === '')
			{
				alert('请选择第' + (i+1) + '条计划:滤光片!');return;
			}
			if ($.trim(plans[i].gain) === '')
			{
				alert('请填写第' + (i+1) + '条计划:增益!');return;
			}
			if ($.trim(plans[i].bin) === '')
			{
				alert('请填写第' + (i+1) + '条计划:Bin!');return;
			}
			if ($.trim(plans[i].readout) === '')
			{
				alert('请填写第' + (i+1) + '条计划:读出速度!');return;
			}
		}
		
		//ajax 发送数据到后台
		$.ajax({
			type: 'post',
			url: '/xinglong/at60/savePlan',
			data: {planData: plans},
			success: function (info){
				alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
				
				if (info.indexOf('计划发送完毕') !== -1)
				{
					$('#planStart').hide();
				}
			},
			error: function (){
				alert('网络异常,请重新提交计划！');
			},
		});
	}//保存并提交计划  结束//////////////////////////////////////////