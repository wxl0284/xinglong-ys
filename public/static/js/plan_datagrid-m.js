//观测计划的赤经和赤纬 js事件//////////////////////////////
	//赤经 的js事件//////////////////////////////////
	var planInfo = $('#planInfo');

	planInfo.on('keyup', 'td[field="rightAscension1"] input.textbox-text', function () {//赤经之小时 js事件
		var that = $(this);
		var parent_td_rightAscension1 = that.closest('td[field="rightAscension1"]'); //当前input的父元素:td[field="rightAscension1"]
		var td_rightAscension2 = parent_td_rightAscension1.siblings('td[field="rightAscension2"]'); //同辈的：td[field="rightAscension2"]
		var patn = /^\d{2}$/;
		var v = $.trim(that.val());
		//v_R = v.replace(/(-|\+)/g, ''); //将+或-替换为空字符
	
		if ( v.length == 2 )
		{
			if ( patn.test(v) && v <= 24 && v >= 0 )
			{
				td_rightAscension2.find('input').focus(); //光标进入分钟的输入框
			}else{
				layer.tips('参数超限', that, {tips : 1,tipsMore: true});
			}
		}else if ( v.length > 2 ){
			layer.tips('参数超限', that, {tips : 1,tipsMore: true});
		}
	});
	
	//赤经之分钟keyup事件
	planInfo.on('keyup', 'td[field="rightAscension2"] input.textbox-text', function () {
		var that = $(this);
		var patn = /^\d{2}$/;
		var v = $.trim(that.val());
		var parent_td_rightAscension2 = that.closest('td[field="rightAscension2"]'); //当前input的父元素:td[field="rightAscension2"]
		var td_rightAscension3 = parent_td_rightAscension2.siblings('td[field="rightAscension3"]'); //同辈的：td[field="rightAscension3"]

		if ( v.length == 2 )
		{
			if ( patn.test(v) && v < 60 && v >= 0 )
			{
				td_rightAscension3.find('input').focus(); //光标进入秒的输入框
			}else{
				layer.tips('参数超限', that, {tips : 1,tipsMore: true});
			}
		}else if ( v.length > 2 ){
			layer.tips('参数超限', that, {tips : 1,tipsMore: true});
		}
	})
	
	planInfo.on('blur', 'td[field="rightAscension3"] input', function () {//赤经之秒 js事件
		var that = $(this);
		var v = $.trim($(this).val());
		if (!$.isNumeric(v) || v >= 60 || v < 0)
		{
			layer.tips('秒参数超限', that, {tips : 1,tipsMore: true});
		}	
	})
	//赤经 的js事件 结束/////////////////////////////////////
	
	//赤纬 的js事件//////////////////////////////////
	planInfo.on('keyup', 'td[field="declination1"] input.textbox-text', function () {//赤纬之度 js事件
		var that = $(this);
		var v = $.trim(that.val());
		var patn = /^-?\d{1,2}$/;
		var v_R = v.replace(/-/, ''); //将-替换为空字符

		var parent_td_declination1 = that.closest('td[field="declination1"]'); //当前input的父元素:td[field="declination1"]
		var td_declination2 = parent_td_declination1.siblings('td[field="declination2"]'); //同辈的：td[field="declination2"]
		
		if ( v_R.length == 2 )
		{
			if ( patn.test(v) && v <= 90 && v >= -90 )
			{
				td_declination2.find('input').focus(); //光标进入分钟的输入框
			}else{
				layer.tips('参数超限', that, {tips : 1,tipsMore: true});
			}
		}else if ( v.length > 2 ){
			layer.tips('参数超限', that, {tips : 1,tipsMore: true});
		}
	});
	
	planInfo.on('keyup', 'td[field="declination2"] input.textbox-text', function () {//赤纬之分钟 js事件
		var that = $(this);
		var patn = /^\d{2}$/;
		var v = $.trim(that.val());
		var parent_td_declination2 = that.closest('td[field="declination2"]'); //当前input的父元素:td[field="declination2"]
		var td_declination3 = parent_td_declination2.siblings('td[field="declination3"]'); //同辈的：td[field="declination3"]

		if ( v.length == 2 )
		{
			if ( patn.test(v) && v < 60 && v >= 0 )
			{
				td_declination3.find('input').focus(); //光标进入秒的输入框
			}else{
				layer.tips('参数超限', that, {tips : 1,tipsMore: true});
			}
		}else if ( v.length > 2 ){
			layer.tips('参数超限', that, {tips : 1,tipsMore: true});
		}
	})
	
	//赤纬之秒 js事件
	planInfo.on('blur', 'td[field="declination3"] input', function () {
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
	var editRow = undefined;  //全局开关, 编辑的行
	var planForm = $('#imptPlan');
//导入计划文件 上传////////////////////////////////////////////
	function importPlan ()
	{
		//var planForm = $('#imptPlan');
		clearInterval ( plan_execute_i );//关闭请求tag的定时器
		plan_execute_i = undefined;

		var plan = planForm.find('input');
		planForm[0].reset();//重置表单数据
		plan.click(); //打开上传文件的选择窗口
		
		plan.one('change', function () {
			var formData = new FormData(planForm[0]);
			formData.append('aperture', aperture);
			formData.append('at', at);//将望远镜主键id发给php 根据此at查默认的bin 读出速度及增益

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
						var info_data = $.parseJSON(info);
						let arr = [];
						let ii = 0;
						for (let p in info_data)
						{
							arr[ii] = info_data[p];
							ii ++;
						}
						
						table.datagrid({ data: arr,	});//页面中写入计划数据
						
						editRow = undefined; //否则 无法拖动
						planErr = 0; //将提交计划的错误标识 改为0
						checked = []; //清空被选中的行索引
						clearInterval ( plan_execute_i ); //关闭查询执行哪条计划的定时器
						plan_execute_i = undefined;
						table.datagrid('enableDnd');
					}
				},//success 结束
	            error:  function () {
	               layer.alert('网络异常,请重新导入计划', {shade:false, closeBtn:0});
	            },
			});
		});
	}//导入计划文件 上传 结束 //////////////////
	
	//保存编辑（同时保存所有被添加和编辑的计划） /////////////////////////////////
	function savePlan ()
	{
		let msg = '';
		table.datagrid('endEdit', editRow);
		//table.datagrid('unselectRow', editRow);

		//验证计划数据
		let plans = table.datagrid ('getRows');
		let n = plans.length

		if ( n > 0 )
		{
			msg += plan_valid (plans, n);
		}

		if ( msg !== '' )
		{
			editRow = undefined;
			layer.alert(msg, {shade:false, closeBtn:0});
			table.datagrid('enableDnd'); //编辑保存后启用拖放
		}else{
			editRow = undefined;
			planErr = 0;	//将提交计划的错误标识改为0
			table.datagrid('enableDnd'); //编辑保存后启用拖放
		}
		table.datagrid('acceptChanges'); //接受数据改变
	}//savePlan() 结束
	
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
	for (let i = 0; i < filterData_num; i++)
	{
		filterData[i] = {filterId:'', name:''};
		filterData[i].filterId = plan_filter_option[i];
		filterData[i].name = plan_filter_option[i];
	}//对变量filterData进行赋值 结束

	//将固定属性中的bin，对变量binOption进行赋值
	var binOption = [];
	var ccd_bin_str = configData.ccd[0]['bin']; // "1*1#2*2#4*4#8*8#16*16"
	ccd_bin_str = ccd_bin_str.split('#'); //转为数组
	var ccd_bin_str_num = ccd_bin_str.length;
	
	for (let i = 0; i < ccd_bin_str_num; i++)
	{
		binOption[i] = {num:'', bin:''};
		binOption[i].num = ccd_bin_str[i].split('*')[0];
		binOption[i].bin = ccd_bin_str[i];
	}//对变量binOption进行赋值 结束
	//console.log(binOption);

	//将固定属性中的gain，对变量gainOption进行赋值
	var gainOption = [];
	var ccd_gain_val = configData.ccd[0]['gainnumber']; // 3(固定属性配置中的增益档位值)
	
	var ccd_gain_val_num = ccd_gain_val*1;
	
	for (let i = 0; i < ccd_gain_val_num; i++)
	{
		gainOption[i] = {num:'', gain:''};
		gainOption[i].num = i;
		let ii = i + 1;
		gainOption[i].gain = ii + '档';
	}//对变量gainOption进行赋值 结束

	//将固定属性中的readoutspeed，对变量readOption进行赋值
	var readOption = [];
	var ccd_readout_str = configData.ccd[0]['readoutspeed']; // "5#3#1#0.05"
	ccd_readout_str = ccd_readout_str.split('#'); //转为数组
	var ccd_readout_str_num = ccd_readout_str.length;
	
	for (let i = 0; i < ccd_readout_str_num; i++)
	{
		readOption[i] = {num:'', speed:''};
		readOption[i].num = i;
		readOption[i].speed = ccd_readout_str[i]+'MHz';
	}//对变量readOption进行赋值 结束

	//var IsCheckFlag = true; //标示是否是勾选复选框选中行的，true 是 ,false 否
	var checked = []; //存储被选中的行索引

	$(function(){
		var table_w = ( planInfo.width() ) * 1;
		table.datagrid({
			//idField: 'id', //不注释的话，getChecked方法会有bug
			width: table_w,
			height:400,
			toolbar: '#toolbar',
			checkOnSelect:true,
			selectOnCheck:true,
			singleSelect:false,
			striped: true,
			rownumbers:true,			
			remoteSort: false,
			dropAccept:'tr.datagrid-row', //哪些行允许被拖拽
			dragSelection: true, //拖拽所有选中的行，false只能拖拽单行
			//在双击一个单元格的时候开始编辑并生成编辑器，然后定位到编辑器的输入框上
			onDblClickCell: function(index,field,value){
				if ( field != 'id' && field != 'del' ) {
					table.datagrid('endEdit', editRow); //结束前一行编辑状态
					table.datagrid('beginEdit', index); //对点击行 进行编辑
					let ed = table.datagrid('getEditor', {index:index,field:field});
					ed.target.focus();
					editRow = index;
					table.datagrid('enableDnd'); //启用拖放
				}
			},		
			onBeforeDrag: function( dragRows ){//解决拖放与编辑的冲突问题
		　　　　 if(editRow !== undefined) return false; //如果处于编辑状态 拒绝拖动
			},
			onBeforeDrop: function( dtargetRow,sourceRow,point ){//判断是否连续选中，或拖动的行是否被选中
				if ( sourceRow.length === undefined ) //拖动的是一行，啥也不做
				{

				}else{
					checked = []; //首先把checked清空
					sourceRow.filter(function (v) {
						var index = table.datagrid("getRowIndex", v);
						checked.push(index);
					})

					checked.sort(); //升序排列
					var n = checked.length;
					if ( n > 1 && ( checked[n-1] - checked[0] ) > (n-1) ) //被选中的行不连续
					{
						layer.alert('拖动多行时必须连续选中', {shade:0, closeBtn:0}); return false;
					}
				}
			},
			onDrop: function (targetRow,sourceRow,point){//拖动到目标行释放时
				checked = []; //清空checked
				table.datagrid('acceptChanges');
			},
			onLoadSuccess: function(){//上传计划数据后启用拖放
				table.datagrid('enableDnd'); //启用拖放
			},
			columns:[[
			{field:'id', title:'id', checkbox:true, rowspan:2},
			{field:'target',  title:'目标名称', width:table_w*0.118666667,rowspan:2,
				editor:{ type:'text' },
			},
			{field:'type', title:'目标类型', width:table_w*0.0653333333,rowspan:2,
				formatter:function(v){
					for(let i=0; i<targetType.length; i++){
						if (targetType[i].typeId == v) return targetType[i].name;
					}
					return v;
				},
				editor:{
					type:'combobox',
					options:{
						valueField:'typeId',
						textField:'name',
						data:targetType,
						editable:false,
					},
				},
			},
			{title:'赤经', colspan:5, width:table_w*0.13133333,halign:'center',},
			{title:'赤纬', colspan:5, width:table_w*0.13133333,halign:'center'},
			{field:'epoch', title:'历元',  width:table_w*0.055, rowspan:2,
				formatter:function(value){
					for(let i=0; i<epochData.length; i++){
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
						editable:false,
					},
				},
			},
			{field:'exposureTime', title:'曝光<br>时间<br>（秒）',  width:table_w*0.045,rowspan:2,
				editor:{ type:'text' },
			},
			{field:'delayTime', title:'延迟<br>（秒）', width:table_w*0.042083335, rowspan:2,
				editor:{ type:'text' },
			},
			{field:'exposureCount', title:'曝光数', width:table_w*0.0405, rowspan:2, 
				editor:{ type:'text' },
			},
			{field:'filter', title:'滤光片',  width:table_w*0.051666667, rowspan:2,
				formatter:function(v){
					for(let i=0; i<filterData.length; i++){
						if (filterData[i].filterId == v) return filterData[i].name;
					}
					return v;
				},
				editor:{
					type:'combobox',
					options:{
						valueField:'filterId',
						textField:'name',
						data:filterData,
						editable: false,
					},
				},
			},
			{field:'gain', title:'增益',  width:table_w*0.050166667, rowspan:2,
				formatter:function(v){
					for(let i=0; i < gainOption.length; i++){
						if (gainOption[i].num == v) return gainOption[i].gain;
					}
					return v;
				},
				editor:{
					type:'combobox',
					options:{
						valueField:'num',
						textField:'gain',
						data:gainOption,
						editable: false,
					},
				},
			},
			{field:'bin',  title:'Bin', width:table_w*0.062750002, rowspan:2,
				formatter:function(v){
					for(let i=0; i < binOption.length; i++){
						if (binOption[i].num == v) return binOption[i].bin;
					}
					return v;
				},
				editor:{
					type:'combobox',
					options:{
						valueField:'num',
						textField:'bin',
						data:binOption,
						editable: false,
					},
				},
			},
			{field:'readout', title:'读出速度', width:table_w*0.086166667, rowspan:2,
				formatter:function(v){
					for(let i=0; i < readOption.length; i++){
						if (readOption[i].num == v) return readOption[i].speed;
					}
					return v;
				},
				editor:{
					type:'combobox',
					options:{
						valueField:'num',
						textField:'speed',
						data:readOption,
						editable: false,
					},
				},
			},
			{field:'del', title:'增&nbsp;&nbsp;|&nbsp;&nbsp;删',  width:table_w*0.051266666, rowspan:2,
				formatter:function(v,r,i){
					return '<a class="add">增&nbsp;&nbsp;</a>|<a class="del">&nbsp;&nbsp;删</a>';
				}
			},
		],[
			{field:'rightAscension1', width:table_w*0.028, title:'时',
				editor:{ type:'numberbox' },
			},
			{field:'c1', width:table_w*0.011,
				formatter:function(value,row,index){
					return ':';
				}
			},
			{field:'rightAscension2',  width:table_w*0.028,title:'分',
				editor:{ type:'numberbox' },
			},
			{field:'c2', width:table_w*0.011,
				formatter:function(value,row,index){
					return ':';
				}
			},
			{field:'rightAscension3', width:table_w*0.053333333,title:'秒',
				editor:{ type:'text' }
			},
	
			{field:'declination1', width:table_w*0.028, title:'度',
				editor:{ type:'numberbox' }
			},
			{field:'c3', width:table_w*0.011,
				formatter:function(value,row,index){
					return ':';
				}
			},
			{field:'declination2', width:table_w*0.028, title:'分',
				editor:{ type:'numberbox' },
			},
			{field:'c4', width:table_w*0.011,
				formatter:function(value,row,index){
					return ':';
				}
			},
			{field:'declination3', width:table_w*0.053333333,title:'秒',
				editor:{ type:'text'	}
			},
		]],
	});/*table.datagrid() 结束*/
	planInfo.on('dblclick', 'td[field="del"] a.add', function (){ add(this) }); //增加计划
	planInfo.on('dblclick', 'td[field="del"] a.del', function (){ delPlan1(this) }); //删除计划
}) //jquery 结束编辑

	//自定义的函数
	function getrow(target) //获取点击行的索引
	{
		var tr = $(target).closest('tr.datagrid-row');
		return parseInt(tr.attr('datagrid-row-index'));
	}

	function delPlan1(target) //删除行
	{	
		table.datagrid('endEdit', editRow); //结束编辑状态
		layer.confirm('确定删除？', {icon: 3, title:'提示',shade:0}, function(index){
			var rowIndex = getrow(target);
			if ( isNaN(rowIndex) ) { rowIndex = editRow };
			table.datagrid('endEdit', rowIndex); //结束编辑状态
			table.datagrid('uncheckRow', rowIndex); //取消勾选
			table.datagrid('unselectRow', rowIndex); //取消选中
			checked.splice( checked.indexOf(rowIndex), 1); //将此行索引从checked删除
			table.datagrid('deleteRow', rowIndex);
			editRow = undefined;
			table.datagrid('enableDnd');
			layer.close(index);
		});	
	}

	function delAll(target) //删除全部行
	{
		layer.confirm('确定删除？', {icon: 3, title:'提示',shade:0}, function(index){
			table.datagrid({ data:[] }); //执行删除
			checked = []; //清空被选中的行索引
			clearInterval ( plan_execute_i ); //关闭查询执行哪条计划的定时器
			plan_execute_i = undefined;
			editRow = undefined;
			layer.close(index);
		});
	}

	function add(target) //增加计划行
	{	
		var rowIndex = getrow(target); //当前行索引
		table.datagrid('endEdit', editRow);
			
		table.datagrid('insertRow', {
			index : rowIndex+1, //在选中行后面 新加一空行
			row:{type:'0',epoch:1,bin:1,readout:0,gain:0},
		});
		
		table.datagrid('beginEdit', rowIndex+1); //将此新加的一行设为可编辑

		editRow = rowIndex +1;
		table.datagrid('enableDnd'); //启用拖放
	}

	/*对计划排序*/
	var sortData = $('#sortPlan');

	sortData.click(function () {
		table.datagrid('endEdit', editRow); //结束编辑状态
		table.datagrid('unselectRow', editRow); //将编辑的行取消选择
		var plan = table.datagrid('getRows');//所有的计划数据
		var plan_num = plan.length;
		if ( plan_num <= 2 ) 
		{
			layer.alert('数据无须排序', {shade:0,closeBtn:0});return;
		}
		
		let field = $('#field').val();
		switch (field)
		{
			case '1':
				field = 'rightAscension'; break;
			case '2':
				field = 'declination'; break;
			default:
				layer.alert('请选择要排序的列', {shade:0,closeBtn:0}); return;
		}
		var sortOrder = $('#sortPlanOrder').val();
		sort_plan (plan, plan_num, field, sortOrder);
	});/*对计划排序 结束*/

	/*对观测计划排序*/
	function sort_plan (plan, num, field, order)
	{
		var temp,d1,d2;

		for (let i = 0; i < num-1; i++)
		{
			for (let j = 0; j < num-i-1; j++)
			{
				d1 = plan[j][field+1]*1 + plan[j][field+2]*1/60 + plan[j][field+3]*1/3600;
				d2 = plan[j+1][field+1]*1 + plan[j+1][field+2]*1/60 + plan[j+1][field+3]*1/3600;

				if ( order === 'asc' )
				{
					if ( d1 > d2 )
					{
						temp = plan[j];
						plan[j] = plan[j+1];
						plan[j+1] = temp;
					}
				}else if ( order === 'desc' )
				{
					if ( d1 < d2 )
					{
						temp = plan[j];
						plan[j] = plan[j+1];
						plan[j+1] = temp;
					}
				}
				
			}                    
		}
		table.datagrid({ data: plan });
	}/*对观测计划排序 结束*/

//观测计划的 开始 停止 下一个按钮////////////////////////////
//观测计划的开始 按钮//////////////////////////////////
	var planStart = $('#planStart'); //计划开始 按钮
	var planStop = $('#planStop'); //计划停止 按钮
	var modeSpan = $('#modeSpan');
	var planErr = 0; //观测计划发送时的错误标识
	var checked_plans = []; //被选中的计划的行索引
	$('#planModes').on('click', 'button', function () {
		if ( !can_operate ) { return } //此用户无权限操作此望远镜时 return
		var self = $(this);
		//获取模式值
		var modeVal = modeSpan.val();
		var option = self.attr('id');
		var btnText = self.html();
		var rows = table.datagrid('getChecked');
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
			/*开始执行前 将所有计划数据和被checked的行索引存入浏览器本地存储*/
			checked_plans = []; //每次执行计划前都将checked_plans清空
			/*var plans_data = JSON.stringify( table.datagrid('getRows') ); //转为字符串
			localStorage.setItem (all_plans, plans_data); //将字符串存入all_plans变量内 所有正执行的计划都从php的缓存中读取 */
			/*开始执行前 将所有计划数据和被checked的行索引存入浏览器本地存储 结束*/

			if ( option == 'planStart' ) //如果点击的开始按钮 则执行提交 并执行正执行的计划
			{
				submitPlan();//提交计划
				if ( planErr == 1 )	{ return; }

				for (let i=0; i < 10000000; i++) { i*1; i/1; i*1; } //空循环 延迟1秒
			}

			if ( rows.length > 0 ) //将被选中的计划的索引存入 checked_plans
			{
				rows.filter(function (v) {
					
					checked_plans.push ( table.datagrid('getRowIndex', v) );
				})
			}
			var checked_plans_index = JSON.stringify( checked_plans ); //将被选中的计划的索引转为字符串，发给php
			
			$.ajax({
				type : 'post',
				url : '/plan',
				data : {
					planOption : option,
					mode : modeVal,
					checked_plans_index :  checked_plans_index,
					start : index +1,
					command : 2, //标识 plan.php控制器中用以区别要执行的函数
					at : at,
					at_aperture: aperture,
				},             
	            success:  function (info) {
		            planErr = 0;
		
					if( !(too_import && info.indexOf('计划停止') !== -1) ) //只要不是too_import导入后的停止按钮点击情况就都弹框
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
					}

					if (info.indexOf('计划开始') !== -1)
					{
						//planStart.prop('disabled', true);
						//planStop.prop('disabled', false);
						too_import = false;
						planStart.css("background-color","red");
						planStop.css("background-color","#e1e1e1");
						setTimeout(plan_executing, 2000)//执行正执行的计划	
					}
					
					if (info.indexOf('计划停止') !== -1)
					{
						//planStart.prop('disabled', false);
						too_import = false;
						planStop.css("background-color","red");
						planStart.css("background-color","#e1e1e1");
						clearInterval ( plan_execute_i ); //关闭查询执行哪条计划的定时器
						plan_execute_i = undefined;
						editRow = undefined;
					}
	            },
	            error:  function () {
				   planErr = 0;
				   layer.alert('网络异常,请再次点击'+ btnText +'按钮!', {shade:false, closeBtn:0});
	            },
			});
		}
	});
	//观测计划的开始 按钮 结束/////////////////////////////
//观测计划的 开始 停止 下一个按钮 结束//////////////////////////////

var all_plans = aperture + 'all_plans';

//保存并提交计划 ////////////////////////////////////////////
	function submitPlan ()
	{
		table.datagrid('endEdit', editRow);
		table.datagrid('unselectRow', editRow);
		table.datagrid('enableDnd'); //启用拖放
		let plans = table.datagrid('getRows');	//选中所有记录
		let n = plans.length;
		if ( n < 1) 
		{
			planErr = 1;
			layer.alert('请先导入计划或添加计划!', {shade:false, closeBtn:0});return;
		}

		let msg = plan_valid(plans, n);  //js验证数据

		if ( msg !== '')
		{
			planErr = 1;
			layer.alert(msg, {shade:false, closeBtn:0});return;
		}
		/*将计划数据存入本地
		var all_plans_data = JSON.stringify( table.datagrid('getRows') ); //转为字符串
		localStorage.setItem (all_plans, all_plans_data); //将字符串存入all_plans变量内
		将计划数据存入本地 结束*/
		var all_plans_data = JSON.stringify( plans ); //计划数据转为字符串

		if (planErr === 0)  //ajax 发送数据到后台
		{
			$.ajax({
				type: 'post',
				url: '/plan',
				data: {
					planData: plans,
					planDataStr: all_plans_data,//转为json字串所有计划数据
					command: 1,//标识 plan.php控制器中用以区别要执行的函数
					at: at,
					at_aperture: aperture,//页面中的望远镜口径
					plan_filter_option: plan_filter_option,//将该望远镜filter的['u','v','b']提交
					maxExpose: configData.ccd[0].maxexposuretime,
					minExpose: configData.ccd[0].minexposuretime,
				},
				success: function (info){
					/*layer.alert(info, {
						shade:false,
						closeBtn:0,
						yes:function (n){
							layer.close(n);
							if (info.indexOf('登录') !== -1)
							{
								location.href = '/';
							}
						},
					});*/
					if (info.indexOf('超限') !== -1)//php验证计划数据超限时
					{
						planErr = 1;
					}

					if (info.indexOf('观测计划发送完毕') !== -1)
					{
						planErr = 0;
						light_tr_i = null; //重置被tag进行高亮的行
 						scrollTo_tr = null; //重置要被滚到的行
					}
				},
				error: function (){
					planErr = 0;
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
		let msg = ''; //错误提示
		
		for(let i = 0; i < n; i++)
		{
			let plan_target = $.trim( plans[i].target );
			let patn = /([\u4e00-\u9fa5]| )+/;
			if ( patn.test(plan_target) || plan_target == '' || plan_target.length > 48 )
			{
				msg += '第' + (i+1) + '条目标名格式错误!<br>';
			}

			let plan_type = $.trim( plans[i].type );
			patn = /^[0-9]$/;
			//console.log(plan_type);
			if ( !(patn.test(plan_type) || ( $.inArray(plan_type, ['恒星','太阳','月亮','彗星','行星','卫星','固定位置','本底','暗流','平场']) !== -1))  )
			{
				msg += '第' + (i+1) + '条目标类型超限!<br>';
			}

			let asc1 = $.trim( plans[i].rightAscension1 );
			patn  = /^\+?\d{1,2}$/;
			if ( !patn.test(asc1) || asc1 > 24 || asc1 < 0 || asc1 === '' )
			{
				msg += '第' + (i+1) + '条赤经小时参数超限!<br>';
			}
			
			let asc2 = $.trim( plans[i].rightAscension2 );
			
			if ( !patn.test(asc2) || asc2 > 59 || asc2 < 0 || asc2 === '' )
			{
				msg += '第' + (i+1) + '条赤经分钟参数超限!<br>';
			}

			let asc3 = $.trim( plans[i].rightAscension3 );
			
			if ( !$.isNumeric(asc3) || asc3 >= 60 || asc3 < 0 || asc3 === '' )
			{
				msg += '第' + (i+1) + '条赤经秒参数超限!<br>';
			}

			let asc = asc1*1 + asc2*1/60 + asc3*1/3600;
			if ( asc > 24 || asc < 0 )
			{
				msg += '第' + (i+1) + '条赤经参数超限!<br>';
			}

			let dec1 = $.trim( plans[i].declination1 );
			patn = /^\+?-?\d{1,2}$/;

			if ( !patn.test(dec1) || dec1 > 90 || dec1 < -90 || dec1 === '' )
			{
				msg += '第' + (i+1) + '条赤纬度参数超限!<br>';
			}

			let dec2 = $.trim( plans[i].declination2 );
			patn = /^\d{1,2}$/;

			if ( !patn.test(dec2) || dec2 > 59 || dec2 < 0 || dec2 === '' )
			{
				msg += '第' + (i+1) + '条赤纬分钟参数超限!<br>';
			}

			let dec3 = $.trim( plans[i].declination3 );

			if ( !$.isNumeric(dec3) || dec3 >= 60 || dec3 < 0 || dec3 === '')
			{
				msg += '第' + (i+1) + '条赤纬秒参数超限!<br>';
			}

			let dec = Math.abs(dec1) + dec2*1/60 + dec3*1/3600;
			if ( dec > 90 || dec < 0 )
			{
				msg += '第' + (i+1) + '条赤纬参数超限!<br>';	
			}

			let plan_epoch = $.trim(plans[i].epoch).toLocaleLowerCase();

			patn = /^[0-3]$/;
			if ( !( patn.test(plan_epoch) || ( $.inArray(plan_epoch, ['real','j2000','b1950','j2050']) !== -1 ) ) )
			{
				msg += '第' + (i+1) + '条历元超限!<br>';
			}

			let plan_exposureTime = $.trim(plans[i].exposureTime);

			if ( !$.isNumeric(plan_exposureTime) || plan_exposureTime > configData.ccd[0].maxexposuretime*1 || plan_exposureTime < configData.ccd[0].minexposuretime*1 )
			{
				msg += '第' + (i+1) + '条曝光时间超限（'+ configData.ccd[0].minexposuretime + '~' +configData.ccd[0].maxexposuretime+'）!<br>';
			}

			let plan_delayTime = $.trim(plans[i].delayTime);

			if ( !$.isNumeric(plan_delayTime) || plan_delayTime < 0 )
			{
				msg += '第' + (i+1) + '条延迟时间超限!<br>';
			}

			let plan_expCount = $.trim(plans[i].exposureCount);
			patn = /^\d+$/;
			if ( !patn.test(plan_expCount) || plan_expCount < 1 )
			{
				msg += '第' + (i+1) + '条曝光数量超限!<br>';
			}

			let plan_filter = $.trim(plans[i].filter);
			
			//if ( !patn.test(plan_filter) && ( $.inArray(plan_filter, plan_filter_option) == -1) )
			if ( $.inArray(plan_filter, plan_filter_option) == -1 )
			{
				msg += '第' + (i+1) + '条滤光片超限!<br>';
			}

			let plan_gain = $.trim(plans[i].gain);
			
			if ( !( patn.test(plan_gain) && plan_gain >= 0 && plan_gain <= ccd_gain_val-1 ) )
			{
				msg += '第' + (i+1) + '条增益超限!<br>';
			}

			let plan_bin = $.trim(plans[i].bin);
			if ( !( patn.test(plan_bin) && configData.ccd[0]['bin'].indexOf(plan_bin) !== -1 ) )
			{
				msg += '第' + (i+1) + '条bin超限!<br>';
			}

			let plan_readout = $.trim(plans[i].readout);
			if ( !( patn.test(plan_readout) && plan_gain >= 0 && plan_gain <= ccd_readout_str_num-1 ) )
			{
				msg += '第' + (i+1) + '条读出速度超限!<br>';
			}
		}
		return msg;
	}/*plan_valid  结束*/
	
//数据验证函数 /////////////////////////////////////////////
	/*function valid ()
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
	}*/
//数据验证函数 结束////////////////////////////////////////

/*******实时获取 获取正在执行的计划*******/

/*function get_plan () {
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
							clearInterval(get_plan_i); //清楚定时器
						}
					},
				});
			}else{
				var info = info.split('#'); //将计划数据与tag组成的字符串从其中的'#'分割为数组
				var plan_tag = info[1];
				
				var plan = $.parseJSON(info[0]);
				var arr = [];
				var ii = 0;
				for (var p in plan)
				{
					arr[ii] = plan[p];
					ii ++;
				}

				table.datagrid({
						data: arr,
				});
				table.datagrid('enableDnd');
				//editRow = undefined; //否则 导入后无法插入新行
				planErr = 0; //将提交计划的错误标识 改为0
				
				table.datagrid('scrollTo', plan_tag-1); //滚动到第tag行
				table.datagrid({
					rowStyler: function (index, row) {
						if( index == (plan_tag-1) ){
							return 'background-color:#18fd65;';
						}
					},
				})
			}
		},//success 方法结束
	})/*ajax 结束
}//get_plan()  结束*/

//var get_plan_i = setInterval (get_plan, 60000); //定时执行get_plan() 60秒执行一次查询，否则浏览器卡顿
//var get_plan_i = 0;
 var no_plan_execute = 0; //如果没有正执行的计划，此值加1
 var plan_execute_i = undefined; //定时器的返回值
 var light_tr = null; //存储datagrid中表格的所有行 用来将tag那行进行高亮
 var plan_in_page = null; // 存储最新的页面中正执行的计划

 function plan_executing ()
 { //显示正在执行的计划
	//console.log ( localStorage.getItem('all_plas') );
	/*原先的是从本地读取存储的计划数据 var plans_data = localStorage.getItem(all_plans);
	var parsed_all_plans = JSON.parse ( plans_data );

	var all_plans_num = 0; //已保存的计划条数
	
	if ( parsed_all_plans )
	{
		all_plans_num = parsed_all_plans.length;
	}

	if (  plans_data !== null &&  all_plans_num > 0 )
	{
		table.datagrid({ data: parsed_all_plans }); //在表格中显示本地存储的计划数据
		editRow = undefined; //否则，无法拖动
	}else{
		layer.alert('无计划被执行', {shade:0, closeBtn:0}); return;
	}

	//然后将被选中的计划，逐一选中
	if ( checked_plans.length > 0 )
	{
		checked_plans.filter(function (v) {
			table.datagrid('checkRow', v); //逐一选中计划
		})
	}*/

	/*去php请求Cache中的：计划数据及被选中计划的行索引，然后在页面中显示这些计划并高亮正被执行的计划*/
	$.ajax({
		url: '/plan',
		type: 'post',
		data: {at: at, command: 'get_cached_plan', at_aperture:aperture},
		success: function (info) {
			//console.log (info.indexOf('cache')); return;
			if ( info.indexOf('cache') !== -1 ) //缓存的计划数据
			{
				plan_in_page = null;
				//解析数据，然后在页面显示
				info = info.split('#'); // info[1]为计划数据，info[2]为被选中的计划索引
				
				table.datagrid({ data: JSON.parse ( info[1] ) }); //在页面显示服务器cache中的计划数据
				plan_in_page = JSON.parse ( info[1] );//获取到页面中正执行的所有计划 用来比对请求的tag表示的计划是否在页面中
				
				//然后逐一将这些索引的行 进行选中
				/*info = JSON.parse ( info[2] ); //将返回的被选中的行索引转为数组格式
				if ( info.length > 0 )
				{
					info.filter(function (v) {
						table.datagrid('checkRow', v);
					})
				} //逐一选中计划 结束*/
				light_tr = $('table.datagrid-btable tr'); //获取页面中的datagrid所有行
				light_tr_i = null;//重置此值
				scrollTo_tr = null; //重置此值
				editRow = undefined; //否则，无法拖动

			}else{
				no_plan_execute ++;
				if ( no_plan_execute <= 1 )
				{
					layer.alert(info, {
						shade:false,
						closeBtn:0,
						yes:function (n){
							layer.close(n);
							if ( info.indexOf('登录') !== -1 )
							{
								location.href = '/';
							}
						},
					});

					clearInterval ( plan_execute_i ); //无正在执行的计划，关闭定时器
					plan_execute_i = undefined;
				}
			}
		}, //success 结束
		error: function (){
			layer.alert('网络异常,请重新点击', {shade:false, closeBtn:0});
		} //error 结束
	}) //ajax 结束
	/*去php请求Cache中的：计划数据及被选中计划的行索引，然后在页面中显示这些计划并高亮正被执行的计划 结束*/

	no_plan_execute = 0; //如果没有正执行的计划，此值加1
	if ( plan_execute_i === undefined )//确保定时器只开一次
	{
		plan_execute_i =  setInterval (get_plan_tag, 2000); //开始查询tag
	}
 }/*plan_executing() 结束*/

 /*查询正在执行第几条计划*/
 var light_tr_i = null; //要被tag进行高亮的行
 var scrollTo_tr = null; //要被滚到的行

 function get_plan_tag ()
 {
	$.ajax({
		url: '/plan',
		type: 'post',
		data: {at: at, command: 'get_plan', at_aperture:aperture},
		success: function (info) {
			if ( info.indexOf('tagOk') !== -1 )
			{//查到了tag字段				
				let plan_data = info.split('#')[1]; //数据库中最新的一条计划数据
				plan_data = $.parseJSON(plan_data); //此望远镜正执行的计划数据

				if ( scrollTo_tr != plan_data.tag-1 )
				{
					table.datagrid('scrollTo', plan_data.tag-1); //滚动到tag那行
					scrollTo_tr = plan_data.tag-1; //将scrollTo_tr 重置
				}
				
				/*table.datagrid({
					rowStyler: function (index, row) {
						if( index == (plan_data.tag-1) ){
							return 'background-color:#18fd65;';
						}
					},
				}) 之前的做法 效率低*/

				if ( light_tr.length > 2 ) //表示有观测计划
				{
					if ( plan_data['target'] ==  plan_in_page[plan_data.tag-1]['target'] && plan_data['filter'] ==  plan_in_page[plan_data.tag-1]['filter'] ) //tag表示的计划与页面中
					{
						let t = light_tr.length/2 + plan_data.tag-1; //要被高亮的行号

						if ( light_tr_i === null )//第一次获取tag值 进行高亮时
						{
							light_tr[t].style.backgroundColor = '#00ee00';//背景变绿
							light_tr_i = t; //给light_tr_i赋值
						}else{//把已高亮的行先取消高亮 再高亮目前的行
							if ( light_tr_i != t )
							{
								light_tr[light_tr_i].style.backgroundColor = '#ffffff';//将前一条高亮的取消高亮
								light_tr[t].style.backgroundColor = '#00ee00';
								light_tr_i = t;
							}
						}
					}
				}
				 
				/*//let page_plan = table.datagrid('getRows');
				//let num = page_plan.length;
				
				if ( num > 0 )
				{
					// for (let i = 0; i < num; i++)
					// {
					// 	if ( plan_data['target'] == page_plan[i]['target'] && plan_data['filter'] == page_plan[i]['filter'])
					// 	{
					// 		plan_index = i;
					// 		break; //跳出for循环
					// 	}					
						
					// }

					table.datagrid('scrollTo', plan_data.tag-1); //滚动到第plan_index行
					table.datagrid({
						rowStyler: function (index, row) {
							if( index == (plan_data.tag-1) ){
								return 'background-color:#18fd65;';
							}
						},
					})
				}*/

			}else{
				no_plan_execute ++;
				if ( no_plan_execute <= 1 )
				{
					layer.alert(info, {
						shade:false,
						closeBtn:0,
						yes:function (n){
							layer.close(n);
							if ( info.indexOf('登录') !== -1 )
							{
								location.href = '/';
							}
						},
					});

					clearInterval ( plan_execute_i ); //无正在执行的计划，关闭定时器
					plan_execute_i = undefined;
				}
			}
		},//success 方法结束
		error: function () {
			clearInterval ( plan_execute_i ); //无正在执行的计划，关闭定时器
			layer.alert('网络异常, 查询失败', {shade:false, closeBtn:0});
		} //error 结束
	})/*ajax 结束*/
 }
 /*查询正在执行第几条计划 结束*/
 
 /*导入已提交的计划数据 从php的缓存中获得*/
 function importData ()
 {
	clearInterval ( plan_execute_i );
	plan_execute_i = undefined;

	$.ajax({
		url: '/plan',
		type: 'post',
		data: {at: at, command: 'get_submited_plan', at_aperture:aperture},
		success: function (info) {
			if ( info.indexOf('cache') !== -1 ) //缓存的计划数据
			{
				//解析数据，然后在页面显示
				info = info.split('#'); // info[1]为计划数据，info[2]为被选中的计划索引
				
				table.datagrid({ data: JSON.parse ( info[1] ) }); //在表格中显示本地存储的计划数据

				editRow = undefined; //否则，无法拖动

			}else{
				layer.alert(info, {
					shade:false,
					closeBtn:0,
					yes:function (n){
						layer.close(n);
						if ( info.indexOf('登录') !== -1 )
						{
							location.href = '/';
						}
					},
				});
			}
		}, //success 结束
		error: function (){
			layer.alert('网络异常,请重新点击', {shade:false, closeBtn:0});
		} //error 结束
	}) //ajax 结束	
	
	/* 原先的从本地存储读取的代码 var plans_data = localStorage.getItem(all_plans);
	var parsed_all_plans = JSON.parse ( plans_data );
	var all_plans_num = 0; //已保存的计划条数

	if ( parsed_all_plans )
	{
		all_plans_num = parsed_all_plans.length;
	}

	if (  plans_data !== null &&  all_plans_num > 0 )
	{
		table.datagrid({ data: parsed_all_plans }); //在表格中显示本地存储的计划数据
		editRow = undefined; //否则，无法拖动
	}else{
		layer.alert('已提交计划数据为空', {shade:0, closeBtn:0}); return;
	} */
 }/*导入已提交的计划数据 importData () 结束*/
 
 /*
  exportPlan (): 将页面已保存的观测计划导出至文件
 */
 function exportPlan (aLink)
 {
	//先验证数据是否都已保存，页面是否有数据
	let msg = '';
	table.datagrid('endEdit', editRow);
	let extport_plan_data = table.datagrid('getRows');
	let num = extport_plan_data.length;
	if ( num < 1 ) { layer.alert('无数据可导出', { shade:0, closeBtn:0}); return; }
	
	msg = plan_valid (extport_plan_data, num);
	
	if ( msg !== '' )
	{
		layer.alert(msg, {shade:false, closeBtn:0});return;
	}
	//验证数据 结束

	//直接将页面计划数据以文件下载的方式保存
	let out_str = ''; //要写入文件的字串
	let pre_target = ''; //前一个目标名（如果目标名有变化则另起一行开始新的记录）

	for ( let i = 0; i < num; i++ ) //循环每一条计划
	{
		let temp = extport_plan_data[i];
		let epoch = '';
		
		//if ( temp['declination1']*1 >= 0 ) temp['declination1'] = '+' + temp['declination1'];
		if ( temp['epoch'].indexOf('2000') !== -1 )
		{
			epoch = '2000';
		}else{
			epoch = temp['epoch'];
		}
		if ( pre_target == temp['target'] ) //是同一个目标的数据，只把后段数据加入out_str
		{
			out_str += '0,' + temp['exposureTime'] + ',' + temp['filter'] + ','
				   + temp['readout'] + ',' + temp['delayTime'] + ',' + temp['exposureCount'] + ',' + temp['gain'] + ',' + temp['bin'] + ',;\r\n';
		}else{//是一个新目标的数据
			out_str += '$,' + temp['target'] + ',' + temp['rightAscension1'] + ':' + temp['rightAscension2'] + ':'
				   + temp['rightAscension3'] + ',' + temp['declination1'] + ':' + temp['declination2'] + ':' + temp['declination3']
				   + ',' + epoch + ',' + '1,;\r\n' + '0,' + temp['exposureTime'] + ',' + temp['filter'] + ',' + temp['readout']
				   + ',' + temp['delayTime'] + ',' + temp['exposureCount'] + ',' + temp['gain'] + ',' + temp['bin'] + ',;\r\n';
		}

		pre_target = temp['target'];//把当前的目标名存起来 用以比对目标名是否改变
	}//循环每一条计划 结束

	out_str =  encodeURIComponent(out_str);  
    aLink.href = "data:text/strat;charset=utf-8," + out_str;

 }//exportPlan() 结束
/*
 addPlans ()
 页面‘编辑计划’调用的方法
 功能：停止向服务器请求正执行计划之tag、清空页面计划数据 
*/
 function addPlans ()
 {
	clearInterval ( plan_execute_i );
	plan_execute_i = undefined;

	checked = []; //清空被选中的行索引

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
 }//addPlans () 结束

 function delAll1(){
	// table.datagrid({
	// 	rowStyler: function (i=3) {
	// 		console.log('hehakk');
	// 		//if( index == (1) ){
	// 			return 'background-color:#18fd65;';
	// 		//}
	// 	},
	// })
	var light_tr = $('table.datagrid-btable tr');
	var light_tr_num = light_tr.length;
	//let x = light_tr_num/2 + 2;
	//console.log(light_tr_num);
	//light_tr[7].css('background', 'red');
	//light_tr[7].style.backgroundColor = 'red';
	//table.datagrid('highlightRow',3);
 }
/*******实时获取 获取正在执行的计划 结束*******/