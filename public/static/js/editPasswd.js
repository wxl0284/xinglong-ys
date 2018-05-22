/*修改密码页 js*/
/*用户管理首页 js*/
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

   var form = $('#fm');
   var username = form.find('input[name="username"]').val();
   var passwd = form.find('input[name="passwd"]');
   var newPass = form.find('input[name="passwdNew"]');
   var RePass = form.find('input[name="RepasswdNew"]');
   var pass = ''; //原密码
   var newPassV = ''; //新密码 
   var rePassV = ''; //确认密码 
   var patn = /[\w-]{6,12}/;

   passwd.blur(function () {
       pass = $.trim($(this).val());

       var err = 0;
       if (!patn.test(pass))
       {
           err = 1;
           layer.tips('密码须为6-12位数字字母_—', $(this), {tips : 2,tipsMore: true});
       }

       $(this).data('err', err);
   });

   newPass.blur(function () {
       newPassV = $.trim($(this).val());
       var err = 0;

       if (!patn.test(newPassV))
       {
           err = 1;
           layer.tips('密码须为6-12位数字字母_—', $(this), {tips : 2,tipsMore: true});
       }
       
       $(this).data('err', err);
   });

   RePass.blur(function () {
       rePassV = $.trim($(this).val());
       var err = 0;
   
       if (!patn.test(rePassV))
       {
           err = 1;
           layer.tips('密码须为6-12位数字字母_—', $(this), {tips : 2,tipsMore: true});
       }else {
           if (rePassV != newPassV)
           {
               err = 1;
               layer.tips('确认密码与新密码不一致', $(this), {tips : 2,tipsMore: true});
           }
       }
       
       $(this).data('err', err);
   });

   $('#btn').click(function () {
       var err = 0; //错误标识
       //获取用户名和密码框
       var textIpt = form.find('input.text');

       textIpt.each(function () {
           $(this).blur();
           err += $(this).data('err');
       });
       
       if (err > 0){
           return;  //指令输入有误 不提交
       }

       $.ajax({
           type: 'post',
            url : '/user/edit_passwd',
            data : {
               username: username,
               passwd: pass,
               passwdNew: newPassV,
               RepasswdNew: rePassV,
           }, 
           success:  function (info) {
                layer.alert(info, {shade:false});
               if (info.indexOf('登录') !== -1)
               {
                   location.href = '/';
               }
           },
            error:  function () {
                 layer.alert('网络异常,请重新提交', {shade:false});
            },
       });//ajax结束/////////////////////////

   })

})