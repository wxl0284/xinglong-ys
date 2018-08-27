/*用户编辑页面 js*/
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
    var id = form.find('input[name="id"]').val();
    var name = form.find('input[name="username"]');
    var pass = form.find('input[name="password"]');
    var patn = /[\w-]{6,12}/;

    name.blur(function () {
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;
        if (!patn.test(v))
        {
            err = 1;
            layer.tips('用户名须为6-12位数字字母—或_', that, {tips : 2,tipsMore: true});
        }

        that.data('err', err);
    });

    pass.blur(function () {
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;
        if (v !== '')
        {
            if (!patn.test(v))
            {
                err = 1;
                layer.tips('密码须为6-12位数字字母—或_', that, {tips : 2,tipsMore: true});
            }
        }
        
        that.data('err', err);
    });

    $('#btn').click(function () {
        var err = 0; //错误标识
        //获取用户名和密码框
        var textIpt = form.find('input.text');
        var nameV = $.trim(name.val());
        var passWd = $.trim(pass.val());
        var role = form.find('select[name="role"]').val();

        textIpt.each(function () {
            $(this).blur();
            err += $(this).data('err');
        });
        
        if (err > 0){
            return;  //指令输入有误 不提交
        }

        $.ajax({
            type: 'post',
            url : '/user/doEdit',
            data : {
                id: id,
                username: nameV,
                password: passWd,
                role: role,
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
                        }else if (info.indexOf('用户成功') !== -1)
                        {
                            location.href = '/user';
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