/*望远镜配置页面 js*/
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
    //望远镜列表js代码结束///////////////////////////////////
    
    /*选择望远镜下拉列表 ajax判断19个固定属性是否添加足够*/
    var allOptions = $('#allOptions');  //页面中所有的配置项
    var atNo = $('#atNo');
    atNo.change(function (){
        var val = $(this).val();

        if(val !== '0') //执行ajax请求 判断
        {
            $.ajax({
                url: '/conf_num',
                type: 'post',
                success:  function (info) {
                    if( info != 0 )    //返回信息需要提示给用户
                    {
                        layer.alert(info);
                        atNo.val('0');  //将望远镜选择下拉框置为初始值
                        allOptions.addClass('displayNo');
                        if (info.indexOf('登录') !== -1)
                        {
                            location.href = '/';
                        }
                    }else{
                        atNo.val(val);
                        //显示页面中的配置选项
                        allOptions.removeClass('displayNo');
                    }
                },
                error:  function () {
                      layer.alert('网络异常,请再次选择!');
                },
            });
        }//执行ajax请求 判断 结束
    })
    /*选择望远镜下拉列表 ajax判断19个固定属性是否添加足够 结束*/
   
})