/*修改密码页 js*/
$(function () {
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
       var that = $(this);
       pass = $.trim( that.val() );

       var err = 0;
       if (!patn.test(pass))
       {
           err = 1;
           layer.tips('密码须为6-12位数字字母_—', that, {tips : 2,tipsMore: true});
       }

       that.data('err', err);
   });

   newPass.blur(function () {
       var that = $(this);
       newPassV = $.trim(that.val());
       var err = 0;

       if (!patn.test(newPassV))
       {
           err = 1;
           layer.tips('密码须为6-12位数字字母_—', that, {tips : 2,tipsMore: true});
       }
       
       that.data('err', err);
   });

   RePass.blur(function () {
       var that = $(this);
       rePassV = $.trim(that.val());
       var err = 0;
   
       if (!patn.test(rePassV))
       {
           err = 1;
           layer.tips('密码须为6-12位数字字母_—', that, {tips : 2,tipsMore: true});
       }else {
           if (rePassV != newPassV)
           {
               err = 1;
               layer.tips('确认密码与新密码不一致', that, {tips : 2,tipsMore: true});
           }
       }
       
       that.data('err', err);
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
                 layer.alert('网络异常,请重新提交', {shade:false});
            },
       });//ajax结束/////////////////////////

   })

})