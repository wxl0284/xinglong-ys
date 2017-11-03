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
})