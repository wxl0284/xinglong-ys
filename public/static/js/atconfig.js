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
    var atNo = $('#atNo');
    atNo.change(function (){
        var index = layer.load(2); //显示加载提示
        var val = $(this).val();

        if(val !== '0') //执行ajax请求 判断
        {
            $.ajax({
                url: '/config',
                type: 'post',
                data: {id: val,},   //将望远镜id发送给后端
                success:  function (info) {
                    //var info = $.parseJSON(info);
                    //console.log(info);return;
                    //判断返回的info 是否为json
                    if ( info.indexOf('{') == -1 )  //不是json, 返回信息需要提示给用户
                    {
                        layer.close(index);  //关闭加载提示
                        layer.alert(info);
                        atNo.val('0');  //将望远镜选择下拉框置为初始值
                        if (info.indexOf('登录') !== -1)
                        {
                            location.href = '/';
                        }
                    }else{//处理返回的json数据
                        atNo.val(val);
                        //根据json数据 显示配置项
                        
                        //layer.close(index);  //关闭加载提示
                    }
                },
                error:  function () {
                      layer.alert('网络异常,请再次选择!');
                },
            });
        }//执行ajax请求 判断 结束
    })/*选择望远镜下拉列表 ajax判断19个固定属性是否添加足够 结束*/
   
    /*ajax 提交转台配置数据*/
    
    /*ajax 提交转台配置数据 结束*/
})