/*固定属性动态配置 js*/
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
    //望远镜列表js代码结束/////////////////////////////////
    
    //给全部固定属性的提交按钮 绑定点击事件
    var btn = $('#tbl input[type="button"]');
    btn.click(function () {
        var that = $(this);
        //获取提交按钮 同辈的input文本框
        var input = that.siblings('input');

        var conf = that.attr('id').replace('Btn', '');
        
        var confVal = $.trim( input.val() );

        //执行验证 输入不合格 直接return
        if( conf == 'focustype')
        {
            if ( focustype_valid(confVal) === false)  //验证 焦点类型
            {
                layer.alert('焦点类型输入不合法', {shade:false});return;
            }
        }else if( conf == 'imageBits' ){
            if ( imageBits_valid(confVal) === false)  //验证 图像位数
            {
                layer.alert('图像位数输入不合法', {shade:false}); return;
            }
        }else if( conf == 'coolerMode' ){
            if ( coolerMode_valid(confVal) === false)  //验证 制冷方式
            {
                layer.alert('制冷方式输入不合法', {shade:false});return;
            }
        }else if( conf == 'readoutMode' ){
            if ( readoutMode_valid(confVal) === false)  //验证 读出模式
            {
                layer.alert('读出模式输入不合法', {shade:false});  return;
            }
        }else if( conf == 'gainmode' ){
            if ( gainmode_valid(confVal) === false)  //验证 增益模式
            {
                layer.alert('增益模式输入不合法', {shade:false}); return;
            }
        }else if( conf == 'ShutterType' ){
            if ( ShutterType_valid(confVal) === false)  //验证 快门类型
            {
                layer.alert('快门类型输入不合法', {shade:false}); return;
            }
        }else if( conf == 'ShutterMode' ){
            if ( ShutterMode_valid(confVal) === false)  //验证 快门模式
            {
                layer.alert('快门模式输入不合法', {shade:false}); return;
            }
        }else if( conf == 'InterfaceType' ){
            if ( InterfaceType_valid(confVal) === false)  //验证 ccd接口类型
            {
                layer.alert('ccd接口类型输入不合法', {shade:false}); return;
            }
        }else if( conf == 'ExposeTriggerMode' ){
            if ( ExposeTriggerMode_valid(confVal) === false)  //验证 曝光触发模式
            {
                layer.alert('曝光触发模式输入不合法', {shade:false}); return;
            }
        }else if( conf == 'FilterSystem' ){
            if ( FilterSystem_valid(confVal) === false)  //验证 滤光片类型
            {
                layer.alert('滤光片类型输入不合法', {shade:false}); return;
            }
        }else if( conf == 'FilterShape' ){
            if ( FilterShape_valid(confVal) === false)  //验证 滤光片形状
            {
                layer.alert('滤光片形状输入不合法', {shade:false}); return;
            }
        }else if( conf == 'slaveDomeType' ){
            if ( slaveDomeType_valid(confVal) === false)  //验证 随动圆顶类型
            {
                layer.alert('随动圆顶类型输入不合法', {shade:false}); return;
            }
        }else if( conf == 'openDomeType' ){
            if ( slaveDomeType_valid(confVal) === false)  //验证 全开圆顶类型
            {
                layer.alert('全开圆顶类型输入不合法', {shade:false}); return;
            }
        }else if( conf == 'opticalStructure' ){
            if ( slaveDomeType_valid(confVal) === false)  //验证 导星镜焦点类型
            {
                layer.alert('导星镜焦点类型输入不合法', {shade:false}); return;
            }
        }
        //执行验证 结束//////////////
        //执行Ajax 提交数据
        $.ajax({
            url: '/conf_option_add',
            type: 'post',
            data: {
                conf: conf,
                conf_val: confVal, 
            },
            success:  function (info) {
                layer.alert(info, {
                    shade:false,
                    closeBtn:0,
                    yes:function (n){
                        layer.close(n);
                        if (info.indexOf('登录') !== -1)
                        {
                            location.href = '/';
                        }
                    },
                });
            },
            error:  function () {
              layer.alert('网络异常,请再次连接！', {shade:false});
            },
        })//ajax 结束
        
    })//全部固定属性的提交按钮 绑定点击事件 结束

    //验证焦点类型
    function focustype_valid (v)
    {
        //输入格式为[xxx xxx1]
        var patn = /^\[\w+ \w+\]$/;
        if ( !patn.test(v) )
        {
            return false;
        }
    }//验证焦点类型 结束

    //验证焦比
    // function focusratio_valid (v)
    // {
    //     //输入格式为[12.1 18.1]
    //     var patn = /^\[[0-9.]+ [0-9.]+\]$/;
    //     if ( !patn.test(v) )
    //     {
    //         return false;
    //     }
    // }//验证焦比 结束

    //验证图像位数
    function imageBits_valid (v)
    {
        //输入格式为正整数
        var patn = /^[1-9]+$/;
        if ( !patn.test(v) )
        {
            return false;
        }
    }//验证图像位数 结束

    //验证制冷方式
    function coolerMode_valid (v)
    {
        //输入格式为汉字
        var patn = /^[\u4e00-\u9fa5-]{2,}$/;
        if ( !patn.test(v) )
        {
            return false;
        }
    }//验证制冷方式 结束

    //验证读出模式
    function readoutMode_valid (v)
    {
        //输入格式为汉字 包含’支持‘
        //var patn = /^支持[\u4e00-\u9fa5]+读出$/;
        if ( $.trim(v).length < 1 )
        {
            return false;
        }
    }//验证读出模式 结束

    //验证增益模式
    function gainmode_valid (v)
    {
        //输入格式High Capacity
        var patn = /^\w+ \w+$/;
        if ( !patn.test(v) )
        {
            return false;
        }
    }//验证增益模式 结束

    //验证快门类型
    function ShutterType_valid (v)
    {
        //输入格式 机械快门
        var patn = /^[\u4e00-\u9fa5]+快门$/;
        if ( !patn.test(v) )
        {
            return false;
        }
    }//验证快门类型 结束

    //验证快门模式
    function ShutterMode_valid (v)
    {
        //输入格式 支持GlobalShutter
        //var patn = /^支持\w+$/;
        if ( $.trim(v).length < 1 )
        {
            return false;
        }
    }//验证快门模式 结束

    //验证ccd接口类型
    function InterfaceType_valid (v)
    {
        //输入格式 支持CameraLink
        //var patn = /^支持\w+(\w|.)*$/;
        if ( $.trim(v).length < 1 )
        {
            return false;
        }
    }//验证ccd接口类型 结束

    //验证曝光触发模式
    function ExposeTriggerMode_valid (v)
    {
        //输入格式 支持硬件触发
        var patn = /^支持\S+触发$/;
        if ( !patn.test(v) )
        {
            return false;
        }
    }//验证曝光触发模式 结束

    //验证滤光片类型
    function FilterSystem_valid (v)
    {
        //输入格式 Johnshon-Bessel
        var patn = /^\S(\S| )*$/;
        if ( !patn.test(v) )
        {
            return false;
        }
    }//验证滤光片类型 结束

    //验证滤光片形状
    function FilterShape_valid (v)
    {
        //输入格式 圆形
        var patn = /^[\u4e00-\u9fa5]形$/;
        if ( !patn.test(v) )
        {
            return false;
        }
    }//验证滤光片形状 结束

    //验证随动及全开圆顶类型，导星镜焦点类型
    function slaveDomeType_valid (v)
    {
        //输入格式 圆形
        var patn = /^.+$/;
        if ( !patn.test(v) )
        {
            return false;
        }
    }//验证随动及全开圆顶类型，导星镜焦点类型 结束

    //给table表内所有的查看配置项的a元素 绑定click事件
    var aElemt = $('#tbl td a');
    var winTable = $ ('#winTable'); //弹窗内的数据表格
    var tableStr =  "<tr><td>序号</td> <td class='widthAuto'>属性值</td> <td>操作</td></tr>";
    var tableTitle = $('#tableHead');

    aElemt.click(function () {
        var index = layer.load(1); //显示加载提示

        //执行Ajax 获取相应的配置数据
        var conf = $(this).attr('id');
        //获取 '查看：全开圆顶类型' 内的查看：'全开圆顶类型'
        var confName = $(this).html().replace('查看：', '');
        
        //首先删除可能存在的老数据
        var appendStr = winTable.find('tr.append');
        appendStr.each(function (){
            $(this).remove();
        });
        //要插入弹窗内表格中的html字串
        var htmlStr = '';

        $.ajax({
            url: '/get_conf',
            type: 'post',
            dataType: 'json',
            data: {
                conf: conf,
            },
            success: function (info) {
                layer.close(index);  //关闭加载提示
                if (info === 0)
                {//获取配置数据失败
                    tableTitle.html(confName);
                    winTable.html('获取配置数据失败或未添加配置!');
                }else{
                    var num = info.length; //固定属性的选项数目
                    for ( i = 0; i < num ; i++)
                    {
                        htmlStr += "<tr class='append'> <td>" 
                                + (i+1)
                                + "</td> <td class='widthAuto'>"
                                + (info[i]['conf_val'])
                                + "</td> <td> <a href='#' class='delete' confId='"
                                + (info[i]['id'])
                                + "'>删除</a></td> </tr>";
                    }

                    tableTitle.html(confName);
                    winTable.html(tableStr);
                    winTable.append(htmlStr); //将html字串插入弹窗内的表格
                }
                //然后弹出窗口
                $('#confPopup').removeClass('displayNo');
                /*$('#confWindow').window({
                    title : '配置列表',
                    //width : 460,
                    //height : 330,
                    width : 'auto',
                    height : 'auto',
                    collapsible : false,
                    minimizable : false,
                    maximizable : false,
                });//弹出窗口 结束*/
                layer.open({
                    type: 1,
                    title: '属性值列表',
                    area: '26%;',
                    shade: 0,
                    id: 'LAY_layuipro', //设定一个id，防止重复弹出
                    resize: false,
                    btnAlign: 'c',
                    moveType: 1, //拖拽模式，0或者1
                    content: $('#confWindow'),
                    cancel: function (){
                        $('#confPopup').addClass('displayNo');
                    },
                });
            },
            error: function (){
                layer.alert('网络异常，请重新查看!', {shade:false});
            }
        })//执行Ajax 获取相应的配置数据 结束
    });//给table表内所有的查看配置项的a元素 绑定click事件 结束

    //ajax 删除固定属性的可选项值
    winTable.on('click', 'a.delete', function () {
     
        var confId = $(this).attr('confId');
        //被选中删除的数据元素
        var td = $(this).parent();
        var tr = td.parent();

        layer.confirm('确定删除？', {icon: 3, title:'提示'}, function(index){
            //执行ajax
            $.ajax({
                url: '/del_conf',
                type: 'post',
                data: {
                    id: confId,
                },
                success: function (info){
                    tr.remove();
                    
                    layer.alert(info, {
                        shade:false,
                        closeBtn:0,
                        yes:function (n){
                            layer.close(n);
                            if (info.indexOf('登录') !== -1)
                            {
                                location.href = '/';
                            }
                        },
                    });
                },
                error: function (){
                    layer.alert('网络异常，请重新操作!', {shade:false});
                }
            });//ajax结束
            layer.close(index);
        });       
    })//ajax 删除固定属性的可选项值 结束
    
})