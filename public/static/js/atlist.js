/*望远镜列表页面 js*/
$(function () {
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