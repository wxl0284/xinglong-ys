/*固定属性动态配置 js*/
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
    //望远镜列表js代码结束/////////////////////////////////
    
    //给全部固定属性的提交按钮 绑定点击事件
    var btn = $('#tbl input[type="button"]');
    btn.click(function () {
        //获取提交按钮 同辈的input文本框
        var input = $(this).siblings('input');

        //执行验证 输入不合格 直接return

        var conf = input.attr('id');
        var confVal = input.val();
        //执行Ajax 提交数据
        $.ajax({
            url: '/conf_option_add',
            type: 'post',
            data: {
                conf: conf,
                conf_val: confVal, 
            },
            success:  function (info) {
                layer.alert(info);
                if (info.indexOf('登录') !== -1)
                {
                    location.href = '/';
                }
            },
             error:  function () {
              layer.alert('网络异常,请再次连接！');
            },
        })//ajax 结束
        
    })//全部固定属性的提交按钮 绑定点击事件 结束

})