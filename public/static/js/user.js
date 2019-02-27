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
   var name = form.find('input[name="username"]'); //用户名
   var pass = form.find('input[name="passwd"]'); //密码
   var repass = form.find('input[name="rePasswd"]'); //确认密码
   var look = form.find('input[name="look[]"]'); //可查看的望远镜
   var operate = form.find('input[name="operate[]"]'); //可操作的望远镜
   var op = $('#op');
   var lk = $('#look');
   var textIpt = form.find('input.ipt');
   var patn = /[\w-]{6,12}/; //数字 字母 下划线及中划线

   name.blur(function () {//用户名校验
        let that = $(this);
        let v = $.trim(that.val());
        let err = 0;

        if (!patn.test(v))
        {
            err = 1;
            layer.tips('须为6-12位字母数字中划及下划线', that, {tips : 2,tipsMore: true});
        }

        that.data('err', err);
   });

   pass.blur(function () {//密码校验
        let that = $(this);
        let v = $.trim(that.val());
        let err = 0;

        if ( !patn.test(v))
        {
            err = 1;
            layer.tips('须为6-12位字母数字中划及下划线', that, {tips : 2,tipsMore: true});
        }

        that.data('err', err);
   });

   repass.blur(function () {//确认密码校验
        let that = $(this);
        let v0 = $.trim(pass.val());
        let v = $.trim(that.val());
        let err = 0;

        if ( v != v0 ) //确认密码与密码不一致
        {
            err = 1;
            layer.tips('两次密码不一致', that, {tips : 2,tipsMore: true});
        }

        that.data('err', err);
  });


    $('#btn').click(function (){

        let err = 0;
        textIpt.each(function () {
            $(this).blur();
            err += $(this).data('err');
        });

        let check = 0;
    
        for (let i=0; i<look.length; i++)
        {
            if ( look[i].checked )
            {
                check ++;
            }
        }

        if ( check == 0 )
        {
            layer.tips('请选择此用户能查看的望远镜!', lk, {tips : 2,tipsMore: true});
        }

        /*let check2 = 0;
    
        for (let i=0; i<operate.length; i++)
        {
            if ( operate[i].checked )
            {
                check2 ++;
            }
        }

        if ( check2 == 0 )
        {
            layer.tips('请选择此用户能操作的望远镜!', op, {tips : 2,tipsMore: true});
        }*/

        if ( err > 0  || check == 0 ){
            return;  //数据输入有误 不提交
        }

        let form_data = new FormData(form[0]); //获取表单数据

        $.ajax({
            type: 'post',
            url: '/user/doadd',
            data: form_data,
            processData : false,
            contentType : false,  
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
        });
    });

    //禁用用户
    $('table a.userOff').on('click', function (){
        var that = $(this);
        var uid = that.attr('uid');

        layer.confirm('确定禁用此用户？', {icon: 3, title:'提示'}, function(index){
            location.href = "/user/off/" + uid;
            layer.close(index);
        });
    });//禁用用户 结束
    
    //启用用户
    $('table a.userOn').on('click', function (){
        var that = $(this);
        var uid = that.attr('uid');

        layer.confirm('确定启用此用户？', {icon: 3, title:'提示'}, function(index){
            location.href = "/user/on/" + uid;
            layer.close(index);
        });
    });//启用用户 结束

})