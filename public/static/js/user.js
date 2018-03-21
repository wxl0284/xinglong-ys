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
                alert(info);
                if (info.indexOf('登录') !== -1)
                {
                    location.href = '/';
                }else if (info.indexOf('用户成功') !== -1)
                {
                    location.href = '/xinglong/user';
                }
            },
            error:  function () {
               alert('网络异常,请重新提交');
            },
        });
    });

})