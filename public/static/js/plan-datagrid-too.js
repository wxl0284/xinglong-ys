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
	planInfo.on('keyup', 'td[field="declination1"] input.textbox-text', function () {//赤纬之小时 js事件
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
	//var planForm = $('#imptPlan');
	
//导入计划文件 上传////////////////////////////////////////////
	/*function importPlan ()
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
						
						editRow = undefined; //否则 无法拖动
						planErr = 0; //将提交计划的错误标识 改为0
						checked = []; //清空被选中的行索引
						clearInterval ( plan_execute_i ); //关闭查询执行哪条计划的定时器
						table.datagrid('enableDnd');
					}
				},//success 结束
	            error:  function () {
	               layer.alert('网络异常,请重新导入计划', {shade:false, closeBtn:0});
	            },
			});
		});
	}//导入计划文件 上传 结束 ///////////////// */
	
	//保存编辑（同时保存所有被添加和编辑的计划） /////////////////////////////////
	function savePlan ()
	{
		var msg = '';
		table.datagrid('endEdit', editRow);
		//table.datagrid('unselectRow', editRow);

		//验证计划数据
		var plans = table.datagrid ('getRows');
		var n = plans.length

		// if ( n > 0 )
		// {
		// 	msg += plan_valid (plans, n);
		// }

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
	}

	//datagrid 属性////////////////////////////////////////
	var at_name = [
	{typeId:'60cm',name:'AT60'},
	{typeId:'80cm',name:'AT80'},
	{typeId:'85cm',name:'AT85'}];
	
	var epochData = [
	{epochId:'0',name:'Real'},
	{epochId:'1',name:'J2000'},
	{epochId:'2',name:'B1950'},
	{epochId:'3',name:'J2050'},];
	
	//将固定属性中的滤光片名称:u/v/b，对变量filterData进行赋值
	/*var filterData = [];
	var plan_filter_option = configData.filter.filtername;
	var filterData_num = plan_filter_option.length;
	for (var filterData_i = 0; filterData_i < filterData_num; filterData_i++)
	{
		filterData[filterData_i] = {filterId:'', name:''};
		filterData[filterData_i].filterId = plan_filter_option[filterData_i];
		filterData[filterData_i].name = plan_filter_option[filterData_i];
    }*/
	var filterData = [
        {filterId:'U',name:'U'},
        {filterId:'B',name:'B'},
        {filterId:'V',name:'V'},
        {filterId:'R',name:'R'},
        {filterId:'I',name:'I'},
        {filterId:'N',name:'N'},
        {filterId:'N',name:'N'},
        {filterId:'U',name:'U'},
        {filterId:'W',name:'W'},
        {filterId:'g',name:'g'},
        {filterId:'r',name:'r'},
        {filterId:'u',name:'u'},
        {filterId:'i',name:'i'},
        {filterId:'z',name:'z'},];

	//var IsCheckFlag = true; //标示是否是勾选复选框选中行的，true 是 ,false 否
	var checked = []; //存储被选中的行索引
    var tempChecked = [];

	$(function(){
        //var table_w = ( planInfo.width() ) * 1;
		var table_w = 1200;
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
					var ed = table.datagrid('getEditor', {index:index,field:field});
					$(ed.target).focus();
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
			{field:'at', title:'望远镜', width:table_w*0.0653333333,rowspan:2,
				formatter:function(value){
					for(var i=0; i<at_name.length; i++){
						if (at_name[i].typeId == value) return at_name[i].name;
					}
					return value;
				},
				editor:{
					type:'combobox',
					options:{
						valueField:'typeId',
						textField:'name',
						data:at_name,
					},
				},
			},
			{title:'赤经', colspan:5, width:table_w*0.1375,halign:'center',},
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
						data:epochData
					},
				},
			},
			{field:'exposureTime', title:'曝光时间<br>（秒）',  width:table_w*0.059,rowspan:2,
				editor:{ type:'text' },
			},
			{field:'delayTime', title:'delayTime<br>（秒）', width:table_w*0.069166667, rowspan:2,
				editor:{ type:'text' },
			},
			{field:'exposureCount', title:'曝光数量', width:table_w*0.059, rowspan:2, 
				editor:{ type:'text' },
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
				editor:{ type:'numberbox' },
			},
			{field:'del', title:'增&nbsp;&nbsp;|&nbsp;&nbsp;删',  width:table_w*0.051266666, rowspan:2,
				formatter:function(value,row,index){
					return '<a class="add">增&nbsp;&nbsp;</a>|<a class="del">&nbsp;&nbsp;删</a>';
				}
			},
		],[
			{field:'rightAscension1', width:table_w*0.0325, title:'时',
				editor:{ type:'numberbox' },
			},
			{field:'c1', width:table_w*0.013,
				formatter:function(value,row,index){
					return ':';
				}
			},
			{field:'rightAscension2',  width:table_w*0.0325,title:'分',
				editor:{ type:'numberbox' },
			},
			{field:'c2', width:table_w*0.013,
				formatter:function(value,row,index){
					return ':';
				}
			},
			{field:'rightAscension3', width:table_w*0.053333333,title:'秒',
				editor:{ type:'text' }
			},
	
			{field:'declination1', width:table_w*0.0325, title:'度',
				editor:{ type:'numberbox' }
			},
			{field:'c3', width:table_w*0.013,
				formatter:function(value,row,index){
					return ':';
				}
			},
			{field:'declination2', width:table_w*0.0325, title:'分',
				editor:{ type:'numberbox' },
			},
			{field:'c4', width:table_w*0.013,
				formatter:function(value,row,index){
					return ':';
				}
			},
			{field:'declination3', width:table_w*0.053333333,title:'秒',
				editor:{ type:'text' }
			},
		]],
    });/*table.datagrid() 结束*/
    
    table.datagrid('insertRow', {
        index : 0, 
        row:{},
    });

    table.datagrid('beginEdit', 0); //将此新加的一行设为可编辑
    editRow = 0;
    table.datagrid('enableDnd');

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
			row:{},
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
			// if ( rows.length > 0 ) //将被选中的计划的索引存入 checked_plans
			// {
			// 	rows.filter(function (v) {
					
			// 		checked_plans.push ( table.datagrid('getRowIndex', v) );
			// 	})
			// }
			var checked_plans_index = JSON.stringify( checked_plans ); //将被选中的计划的索引转为字符串，发给php
			
			$.ajax({
				type : 'post',
				url : '/ToO_start_stop',
				data : {
					planOption : option,
					mode : modeVal,
					//checked_plans_index :  checked_plans_index,
					start : index +1,
					//command : 2, //标识 plan.php控制器中用以区别要执行的函数
					//at : at,
					//at_aperture: aperture,
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
					//if (info.indexOf('计划开始') !== -1)
					//{
						//planStart.prop('disabled', true);
						//planStop.prop('disabled', false);

						//将index这一索引对应的计划高亮
						// table.datagrid('scrollTo', index); //滚动到第index行
						// table.datagrid({
						// 	rowStyler: function (i, row) {
						// 		if( i == (index) ){
						// 			return 'background-color:#18fd65;';
						// 		}
						// 	},
						// }) //高亮 结束

						//plan_execute_i =  setInterval (get_plan_tag, 2000); //开始查询正执行的计划
					//}
					
					// if (info.indexOf('计划停止') !== -1)
					// {
					// 	planStart.prop('disabled', false);
					// 	clearInterval ( plan_execute_i ); //关闭查询执行哪条计划的定时器
					// }
	            },
	            error:  function () {
				   layer.alert('网络异常,请再次点击'+ btnText +'按钮!', {shade:false, closeBtn:0});
	            },
			});
		}
	});
	//观测计划的开始 按钮 结束/////////////////////////////
//观测计划的 开始 停止 下一个按钮 结束//////////////////////////////

//var all_plans = aperture + 'all_plans';
//保存并提交计划 ////////////////////////////////////////////
	function submitPlan ()
	{
		var exeMode = $('#modeSpan').val();
		table.datagrid('endEdit', editRow);
		table.datagrid('unselectRow', editRow);
		table.datagrid('enableDnd'); //启用拖放
		var plans = table.datagrid('getRows');	//选中所有记录
		var n = plans.length;

		if ( n< 1) 
		{
			planErr = 1;
			layer.alert('请先导入计划或添加计划!', {shade:false, closeBtn:0});
		}

		/*var msg = plan_valid(plans, n);  //js验证数据

		if ( msg !== '')
		{
			layer.alert(msg, {shade:false, closeBtn:0});return;
        }*/
        
		/*将计划数据存入本地
		var all_plans_data = JSON.stringify( table.datagrid('getRows') ); //转为字符串
		localStorage.setItem (all_plans, all_plans_data); //将字符串存入all_plans变量内
		将计划数据存入本地 结束*/
		//var all_plans_data = JSON.stringify( plans ); //计划数据转为字符串

		if (planErr === 0)  //ajax 发送数据到后台
		{
			$.ajax({
				type: 'post',
				url: '/ToO_plan',
				data: {
					planData: plans,
					exeMode: exeMode,
					/*planDataStr: all_plans_data, //转为json字串所有计划数据
					command : 1,  //标识 plan.php控制器中用以区别要执行的函数
					at : at,
					at_aperture:aperture, //页面中的望远镜口径
					plan_filter_option : plan_filter_option, //将该望远镜filter的['u','v','b']提交
					maxExpose: configData.ccd[0].maxexposuretime,
					minExpose: configData.ccd[0].minexposuretime,*/
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

					// if (info.indexOf('观测计划发送完毕') !== -1)
					// {
					// 	planStart.prop('disabled', false); //启用 计划开始按钮
					// }
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

	/*function plan_valid(plans, n)
	{
		var msg = ''; //错误提示
		
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

			var asc1 = $.trim( plans[i].rightAscension1 );
			patn  = /^\d{1,2}$/;
			if ( !patn.test(asc1) || asc1 > 24 || asc1 < 0 || asc1 === '' )
			{
				msg += '第' + (i+1) + '条赤经小时参数超限!<br>';
			}
			
			var asc2 = $.trim( plans[i].rightAscension2 );
			
			if ( !patn.test(asc2) || asc2 > 59 || asc2 < 0 || asc2 === '' )
			{
				msg += '第' + (i+1) + '条赤经分钟参数超限!<br>';
			}

			var asc3 = $.trim( plans[i].rightAscension3 );
			
			if ( !$.isNumeric(asc3) || asc3 >= 60 || asc3 < 0 || asc3 === '' )
			{
				msg += '第' + (i+1) + '条赤经秒参数超限!<br>';
			}

			var asc = asc1*1 + asc2*1/60 + asc3*1/3600;
			if ( asc > 24 || asc < 0 )
			{
				msg += '第' + (i+1) + '条赤经参数超限!<br>';
			}

			var dec1 = $.trim( plans[i].declination1 );
			patn = /^-?\d{1,2}$/;

			if ( !patn.test(dec1) || dec1 > 90 || dec1 < -90 || dec1 === '' )
			{
				msg += '第' + (i+1) + '条赤纬小时参数超限!<br>';
			}

			var dec2 = $.trim( plans[i].declination2 );
			patn = /^\d{1,2}$/;

			if ( !patn.test(dec2) || dec2 > 59 || dec2 < 0 || dec2 === '' )
			{
				msg += '第' + (i+1) + '条赤纬分钟参数超限!<br>';
			}

			var dec3 = $.trim( plans[i].declination3 );

			if ( !$.isNumeric(dec3) || dec3 >= 60 || dec3 < 0 || dec3 === '')
			{
				msg += '第' + (i+1) + '条赤纬秒参数超限!<br>';
			}

			var dec = Math.abs(dec1) + dec2*1/60 + dec3*1/3600;
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

			/*var plan_gain = $.trim(plans[i].gain);
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
	} plan_valid  结束*/
	
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
 //var no_plan_execute = 0; //如果没有正执行的计划，此值加1
 //var plan_execute_i =  -1; //定时器的返回值

 //function plan_executing ()
 //{ //显示正在执行的计划
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
// 	$.ajax({
// 		url: '/plan',
// 		type: 'post',
// 		data: {at: at, command: 'get_cached_plan', at_aperture:aperture},
// 		success: function (info) {
// 			//console.log (info.indexOf('cache')); return;
// 			if ( info.indexOf('cache') !== -1 ) //缓存的计划数据
// 			{
// 				//解析数据，然后在页面显示
// 				info = info.split('#'); // info[1]为计划数据，info[2]为被选中的计划索引
				
// 				table.datagrid({ data: JSON.parse ( info[1] ) }); //在表格中显示本地存储的计划数据
// 				//然后逐一将这些索引的行 进行选中
// 				info = JSON.parse ( info[2] ); //将返回的被选中的行索引转为数组格式
// 				if ( info.length > 0 )
// 				{
// 					info.filter(function (v) {
// 						table.datagrid('checkRow', v);
// 					})
// 				} //逐一选中计划 结束

// 				editRow = undefined; //否则，无法拖动

// 			}else{
// 				no_plan_execute ++;
// 				if ( no_plan_execute <= 1 )
// 				{
// 					layer.alert(info, {
// 						shade:false,
// 						closeBtn:0,
// 						yes:function (n){
// 							layer.close(n);
// 							if ( info.indexOf('登录') !== -1 )
// 							{
// 								location.href = '/';
// 							}
// 						},
// 					});

// 					clearInterval ( plan_execute_i ); //无正在执行的计划，关闭定时器
// 				}
// 			}
// 		}, //success 结束
// 		error: function (){
// 			layer.alert('网络异常,请重新点击', {shade:false, closeBtn:0});
// 		} //error 结束
// 	}) //ajax 结束
// 	/*去php请求Cache中的：计划数据及被选中计划的行索引，然后在页面中显示这些计划并高亮正被执行的计划 结束*/

// 	no_plan_execute = 0; //如果没有正执行的计划，此值加1
// 	plan_execute_i =  setInterval (get_plan_tag, 2000);
//  }/*plan_executing() 结束*/

 /*查询正在执行第几条计划*/
//  function get_plan_tag ()
//  {
// 	$.ajax({
// 		url: '/plan',
// 		type: 'post',
// 		data: {at: at, command: 'get_plan', at_aperture:aperture},
// 		success: function (info) {
// 			if ( info.indexOf('tagOk') !== -1 )
// 			{//查到了tag字段
// 				var plan_tag = info.split('#')[1];
// 				table.datagrid('scrollTo', plan_tag-1); //滚动到第tag行
// 				table.datagrid({
// 					rowStyler: function (index, row) {
// 						if( index == (plan_tag-1) ){
// 							return 'background-color:#18fd65;';
// 						}
// 					},
// 				})
// 			}else{
// 				no_plan_execute ++;
// 				if ( no_plan_execute <= 1 )
// 				{
// 					layer.alert(info, {
// 						shade:false,
// 						closeBtn:0,
// 						yes:function (n){
// 							layer.close(n);
// 							if ( info.indexOf('登录') !== -1 )
// 							{
// 								location.href = '/';
// 							}
// 						},
// 					});

// 					clearInterval ( plan_execute_i ); //无正在执行的计划，关闭定时器
// 				}
// 			}
// 		},//success 方法结束
// 		error: function () {
// 			clearInterval ( plan_execute_i ); //无正在执行的计划，关闭定时器
// 			layer.alert('网络异常, 查询失败', {shade:false, closeBtn:0});
// 		} //error 结束
// 	})/*ajax 结束*/
//  }
 /*查询正在执行第几条计划 结束*/
 
 /*导入已提交的计划数据 从php的缓存中获得*/
//  function importData ()
//  {
// 	$.ajax({
// 		url: '/plan',
// 		type: 'post',
// 		data: {at: at, command: 'get_submited_plan', at_aperture:aperture},
// 		success: function (info) {
// 			if ( info.indexOf('cache') !== -1 ) //缓存的计划数据
// 			{
// 				//解析数据，然后在页面显示
// 				info = info.split('#'); // info[1]为计划数据，info[2]为被选中的计划索引
				
// 				table.datagrid({ data: JSON.parse ( info[1] ) }); //在表格中显示本地存储的计划数据

// 				editRow = undefined; //否则，无法拖动

// 			}else{
// 				layer.alert(info, {
// 					shade:false,
// 					closeBtn:0,
// 					yes:function (n){
// 						layer.close(n);
// 						if ( info.indexOf('登录') !== -1 )
// 						{
// 							location.href = '/';
// 						}
// 					},
// 				});
// 			}
// 		}, //success 结束
// 		error: function (){
// 			layer.alert('网络异常,请重新点击', {shade:false, closeBtn:0});
// 		} //error 结束
// 	}) //ajax 结束	
	
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
 //}/*导入已提交的计划数据 importData () 结束*/
 
 /*
  exportPlan (): 将页面已保存的观测计划导出至文件
 */
//  function exportPlan (aLink)
//  {
	//先验证数据是否都已保存，页面是否有数据，暂时不做
	// var all_plan = table.datagrid('getRows');
	// let num = all_plan.length;
	//直接将页面计划数据以文件下载的方式保存
	//let out_str = ''; //要写入文件的字串

// 	for ( let i = 0; i < num; i++ ) //循环每一条计划
// 	{
// 		let temp = all_plan[i];
// 		//if ( temp['declination1']*1 >= 0 ) temp['declination1'] = '+' + temp['declination1'];
// 		if ( temp['epoch'].indexOf('2000') !== -1 ) temp['epoch'] = '2000';

// 		if ( out_str.indexOf(temp['target']) !== -1 ) //是同一个目标的数据，只把后段数据加入out_str
// 		{
// 			out_str += '0,' + temp['exposureTime'] + ',' + temp['filter'] + ','
// 				   + temp['readout'] + ',' + temp['delayTime'] + ',' + temp['exposureCount'] + ',;\r\n';
// 		}else{//是一个新目标的数据
// 			out_str += '$,' + temp['target'] + ',' + temp['rightAscension1'] + ':'
// 				   + temp['rightAscension1'] + ':' + temp['rightAscension3'] + ','
// 				   + temp['declination1'] + ':' + temp['declination2'] + ':' + temp['declination3']
// 				   + ',' + temp['epoch'] + ',' + '1,;\r\n' + '0,' + temp['exposureTime'] + ',' + temp['filter'] + ','
// 				   + temp['readout'] + ',' + temp['delayTime'] + ',' + temp['exposureCount'] + ',;\r\n';
// 		}

// 	}//循环每一条计划 结束

// 	out_str =  encodeURIComponent(out_str);  
//     aLink.href = "data:text/strat;charset=utf-8," + out_str;

//  }//exportPlan() 结束

   

 function test ()
 {
	var d  = table.datagrid('getChecked');
	console.log(d);
	console.log('----');
	d  = table.datagrid('getSelections');
	console.log(d);
	console.log(checked);
 }
/*******实时获取 获取正在执行的计划 结束*******/