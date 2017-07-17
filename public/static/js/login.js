/*登录页js*/

//登录按钮 点击 后提交 /////////////////////////
function sbmt ()
{
	$('input:text').removeClass('displayNo');
	var username = $(':text').val();  //获取用户名的输入值
	var reg = new RegExp("^[a-zA-Z0-9_-]{6,12}$");
	if (!username.match(reg))
	{
	   layer.tips('用户名须为6-12位数字字母_及-!', '#ipt1');return;
	}
	$('input:password').removeClass('displayNo');
	//验证密码
	var paswd = $(':password').val();  //获取密码的输入值
	var reg = new RegExp("^[a-zA-Z0-9_-]{6,12}$");
	if (!paswd.match(reg))
	{
	   layer.tips('密码须为6-12位数字字母_及-!', '#ipt2');return;
	}
	document.getElementsByTagName("form")[0].submit()
}

$(function (){
	//input 输入框点击后显示 js事件//////////////////////////////
	$('form p').click(function () {
		var input = $(this).find('input');
		//js 验证登录数据
		if (input.prop('type') == 'password') //若点击的是密码输入框
		{
			$('input:text').removeClass('displayNo');
			var username = $(':text').val();  //获取用户名的输入值
			var reg = new RegExp("^[a-zA-Z0-9_-]{6,12}$");
			if (!username.match(reg))
			{
			   layer.tips('用户名须为6-12位数字字母_及-!', '#ipt1');return;
			}
		}
	    input.removeClass('displayNo');	
		input.focus();
	});
})
