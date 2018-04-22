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
                        var info = $.parseJSON(info);
                        //console.log(info.confOption.InterfaceType);
                        //console.log(info);
                        /*将19个动态增减的固定显示与配置页面*/
                        show_19confOption (info.confOption);
                        /*将19个动态增减的固定显示与配置页面 结束*/
                        layer.close(index);  //关闭加载提示
                    }
                },
                error:  function () {
                      layer.alert('网络异常,请再次选择!');
                      layer.close(index);  //关闭加载提示
                },
            });//执行ajax请求 判断 结束
        }else{
            layer.close(index);  //关闭加载提示
        }
    })/*选择望远镜下拉列表 ajax判断19个固定属性是否添加足够 结束*/
    
     /* 获取所有要填入数据的元素对象*/
     var gimbalFocustype = $('#gimbalFocustype'); //转台 焦点类型
     var gimbalFocusratio = $('#gimbalFocusratio'); //转台 焦比
     var ccdImageBits = $('#ccdImageBits'); //ccd-No1 图像位数
     //var ccd_2ImageBits = $('#ccd_2ImageBits'); //ccd-No2 图像位数
     //var ccd_3ImageBits = $('#ccd_3ImageBits'); //ccd-No2 图像位数
     //var ccd_4ImageBits = $('#ccd_4ImageBits'); //ccd-No3 图像位数
     /* 获取所有要填入数据的元素对象 结束*/
 
     //显示19个固定属性选项
     function show_19confOption (data)
     { 
        //显示转台之焦点类型
        var showData = data.focustype;
        var resHtml = '<option value="0">请选择</option>';
        var n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + (i+1) + '">' + showData[i] +'</option>';
        }
        gimbalFocustype.html(resHtml);
        //显示转台之焦点类型 结束

        //显示转台之焦比
        showData = data.focusratio;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + (i+1) + '">' + showData[i] +'</option>';
        }
        gimbalFocusratio.html(resHtml);
        //显示转台之焦比 结束

        //显示ccd-No1之图像位数
        showData = data.imageBits;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + (i+1) + '">' + showData[i] +'</option>';
        }
        ccdImageBits.html(resHtml);
        //显示ccd-No1之图像位数 结束

        /*//显示ccd-No2之图像位数
        showData = data.imageBits;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + (i+1) + '">' + showData[i] +'</option>';
        }
        ccd_2ImageBits.html(resHtml);
        //显示ccd-No2之图像位数 结束

        //显示ccd-No3之图像位数
        showData = data.imageBits;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + (i+1) + '">' + showData[i] +'</option>';
        }
        ccd_3ImageBits.html(resHtml);
        //显示ccd-No3之图像位数 结束

        //显示ccd-No4之图像位数
        showData = data.imageBits;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + (i+1) + '">' + showData[i] +'</option>';
        }
        ccd_4ImageBits.html(resHtml);
        //显示ccd-No4之图像位数 结束*/

     }//显示19个固定属性选项 结束 

})/*jquery 初始化函数 末尾*/

   

