/*用户编辑页面 js*/
$(function () {
    var form = $('#fm');
    var id = form.find('input[name="id"]').val();
    var role = form.find('input[name="role"]').val();
    var name = form.find('input[name="username"]');
    var pass = form.find('input[name="password"]');
    var look = form.find('input[name="look[]"]'); //可查看的望远镜
    var lk = $('#look'); //可查看望远镜的td元素
    var patn = /[\w-]{6,12}/;

    name.blur(function () {
        let that = $(this);
        let v = $.trim(that.val());
        let err = 0;
        if (!patn.test(v))
        {
            err = 1;
            layer.tips('用户名须为6-12位数字字母—或_', that, {tips : 2,tipsMore: true});
        }

        that.data('err', err);
    });

    pass.blur(function () {
        let that = $(this);
        let v = $.trim(that.val());
        let err = 0;
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
        //console.log(look.length);return;
        let err = 0; //错误标识
        //获取用户名和密码框
        let textIpt = form.find('input.text');
        let nameV = $.trim(name.val());
        let passWd = $.trim(pass.val());

        textIpt.each(function () {//验证用户名和密码
            $(this).blur();
            err += $(this).data('err');
        });
        /*然后 验证 编辑的不是管理员时 是否选择了可查看的望远镜*/
        let check = 0;

        if ( role != 1 )
        {        
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
        }
        /*然后 验证 编辑的不是管理员时 是否选择了可查看的望远镜 结束*/

        if ( err > 0 || ( role != 1 && check == 0 ) )
        {
            return;  //指令输入有误 不提交
        }

        let form_data = new FormData(form[0]); //获取表单数据

        $.ajax({
            type: 'post',
            url : '/user/doEdit',
            data : form_data,
            processData: false,
            contentType: false,
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