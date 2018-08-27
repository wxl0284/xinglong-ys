/*望远镜列表页面 js*/
$(function () {        
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
    //望远镜列表js代码结束////////////////////////////////

    //删除望远镜 
    $('table a.delete').on('click', function (){
        var atid = $(this).attr('atid');
        layer.confirm('确定删除？', {icon: 3, title:'提示'}, function(index){
            //执行删除
            location.href = "/at_delete/" + atid;
            layer.close(index);
        });
    });//删除望远镜 结束
})