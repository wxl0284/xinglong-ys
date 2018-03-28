/*望远镜列表页面 js*/
$(function () {
    //页面加载完成后，默认所有选项为 未选中状态
        $('input').prop('checked', false);
        
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
        var r = confirm('确定删除此望远镜信息？');
        var uid = $(this).attr('atid');
        if (r)
        {
            $(this).attr('href', "/at_delete/" + uid);
        }else {
            $(this).attr('href', '#');
        }
    });//删除望远镜 结束
})