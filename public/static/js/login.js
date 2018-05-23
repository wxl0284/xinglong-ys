/*登录页js*/
$(function (){
	var form = $('form');
	var input = form.find('#ipt1, #ipt2');
	
	//给用户名和密码框 绑定focus事件////////////////////////
	form.children('p').click(function () {
		//显示输入框
		input.eq(0).removeClass('displayNo');
		input.eq(1).removeClass('displayNo');
	})
	
	/* $('#ipt2').click(function () {
		//显示输入框 并获取焦点
		$(this).removeClass('displayNo').focus();
	}) */
	//给用户名和密码框 绑定 blur事件///////////////////////
	input.blur (function (){
		var that = $(this);
		var v = $.trim(that.val());
		var err = 0;
		var patn = /^[a-zA-Z0-9_-]{6,12}$/;
		
		if (!patn.test(v))
		{
			err = 1;
			layer.tips('请输入6-12位数字字母_及-', that, {tipsMore: true});
		}
		that.data('err', err);
	})
	
	//提交按钮 js事件//////////////////////////////
	$('#btn').click(function (){
		var err = 0;
		//首先执行blur 检查输入的值
		input.each(function () {
			$(this).removeClass('displayNo');
			$(this).blur();
			err += $(this).data('err');
		});

		if ( err === 0 )
		{
			form.submit();
		}
	})

})