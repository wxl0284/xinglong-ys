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

    $('#btn').click(function (){
        
        var formData = new FormData(form[0]);
        $.ajax({
            type: 'post',
            url: '/user/doadd',
            data: formData,
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
                            location.href = '/xinglong/user';
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