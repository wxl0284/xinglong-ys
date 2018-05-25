/*望远镜配置页面 js*/
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
    //望远镜列表js代码结束///////////////////////////////////
    /*选择各望远镜之自设备组成的js*/
    var have_gimbal = $('#have_gimbal');

    var devsBtn = $('#devsBtn');

    devsBtn.click(function () {
        var v = have_gimbal.prop('checked');
        if ( have_gimbal.prop('checked') === true )
        {
            gimbalForm.show();
        }else{
            gimbalForm.hide();
        }
        
        var v = have_gimbal.prop('checked');
        if ( have_gimbal.prop('checked') === true )
        {
            gimbalForm.show();
        }else{
            gimbalForm.hide();
        }
    });
    /*************8选择各望远镜之自设备组成的js 结束**************/
    /*选择望远镜下拉列表 ajax判断19个固定属性是否添加足够*/
    var atNo = $('#atNo');
    atNo.change(function (){
        var index = layer.load(1); //显示加载提示
        var val = $(this).val();

        if(val !== '0') //执行ajax请求 判断
        {
            $.ajax({
                url: '/config',
                type: 'post',
                data: {id: val,},   //将望远镜id发送给后端
                success:  function (info) {
                    //判断返回的info 是否为json
                    if ( info.indexOf('{') == -1 )  //不是json, 返回信息需要提示给用户
                    {
                        layer.close(index);  //关闭加载提示
                        atNo.val('0');  //将望远镜选择下拉框置为初始值

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

                    }else{//处理返回的json数据
                        
                        atNo.val(val);
                        //根据json数据 显示配置项
                        var info = $.parseJSON(info);
                        //console.log(info.ccd_data.attrmodifytime);return;
                        /*将19个动态增减的固定显示与配置页面*/
                        show_19confOption (info.confOption);                      
                        /*将19个动态增减的固定显示与配置页面 结束*/

                        /*在页面显示转台的配置数据*/
                        if (info.gimbal_data) //若接收到转台配置数据
                        {
                            show_gimbal_data (info.gimbal_data);
                            if (info.gimbal_file)
                            {
                                show_file (gimbalFile, info.gimbal_file);
                            }else{
                                gimbalFile.html('');
                            }
                        }/*在页面显示转台的配置数据 结束*/

                        /*在页面显示ccd-No1的配置数据*/
                        if (info.ccd_data) //若接收到转台配置数据
                        {
                            show_ccd_data (info.ccd_data);
                            if (info.ccd_file)
                            {
                                show_file (ccdFile, info.ccd_file);
                            }else{
                                ccdFile.html('');
                            }
                        }/*在页面显示ccd-No1的配置数据 结束*/

                        /*在页面显示滤光片的配置数据*/
                        if (info.filter_data) //若接收到滤光片配置数据
                        {
                            show_filter_data (info.filter_data);
                            if (info.filter_file)
                            {
                                show_file (filterFile, info.filter_file);
                            }else{
                                filterFile.html('');
                            }
                        }/*在页面显示滤光片的配置数据 结束*/

                        /*在页面显示随动圆顶的配置数据*/
                        if (info.sDome_data) //若接收到滤光片配置数据
                        {
                            show_sDome_data (info.sDome_data);
                            if (info.sDome_file)
                            {
                                show_file (sDomeFile, info.sDome_file);
                            }else{
                                sDomeFile.html('');
                            }
                        }/*在页面显示随动圆顶的配置数据 结束*/

                        /*在页面显示全开圆顶的配置数据*/
                        if (info.oDome_data) //若接收到滤光片配置数据
                        {
                            show_oDome_data (info.oDome_data);
                            if (info.oDome_file)
                            {
                                show_file (oDomeFile, info.oDome_file);
                            }else{
                                oDomeFile.html('');
                            }
                        }/*在页面显示全开圆顶的配置数据 结束*/

                        /*在页面显示调焦器的配置数据*/
                        if (info.focus_data) //若接收到滤光片配置数据
                        {
                            show_focus_data (info.focus_data);
                            if (info.focus_file)
                            {
                                show_file (focusFile, info.focus_file);
                            }else{
                                focusFile.html('');
                            }
                        }/*在页面显示调焦器的配置数据 结束*/

                        /*在页面显示导星望远镜的配置数据*/
                        if (info.guide_data) //若接收到滤光片配置数据
                        {
                            show_guide_data (info.guide_data);
                            if (info.guide_file)
                            {
                                show_file (guideScopeFile, info.guide_file);
                            }else{
                                guideScopeFile.html('');
                            }
                        }/*在页面显示导星望远镜的配置数据 结束*/

                        layer.close(index);  //关闭加载提示
                    }
                },/*success 方法结束*/
                error:  function () {
                      layer.alert('网络异常,请再次选择!', {shade:false, closeBtn:0});
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
     var ccdCoolerMode = $('#ccdCoolerMode'); //ccd-No1 制冷方式
     var ccdReadoutSpeed = $('#ccdReadoutSpeed'); //ccd-No1 读出速度模式
     var ccdreadoutMode = $('#ccd_1readoutMode'); //ccd-No1 读出模式
     var ccdTransferSpeed = $('#ccdTransferSpeed'); //ccd-No1 转移速度模式
     var ccd_1gainmode = $('#ccd_1gainmode'); //ccd-No1 增益模式
     var ccd_1GainNumber = $('#ccd_1GainNumber'); //ccd-No1 增益档位
     var ccdShutterType = $('#ccdShutterType'); //ccd-No1 快门类型
     var ccd_1ShutterMode = $('#ccd_1ShutterMode'); //ccd-No1 快门模式
     var ccd_1BinArray = $('#ccd_1BinArray'); //ccd-No1 Bin
     var ccd_1InterfaceType = $('#ccd_1InterfaceType'); //ccd-No1 接口类型
     var ccd_1ExposeTriggerMode = $('#ccd_1ExposeTriggerMode'); //ccd-No1 曝光触发模式
     var filterSystem = $('#filterSystem'); //滤光片类型
     var filterShape = $('#filterShape'); //滤光片形状
     var sDomeType = $('#sDomeType'); //随动圆顶类型
     var oDomeType = $('#oDomeType'); //全开圆顶类型
     var guideScopeOpticalStructure = $('#guideScopeOpticalStructure'); //导星镜 焦点类型
     /* 获取所有要填入数据的元素对象 结束*/
 
     //显示19个固定属性选项
     function show_19confOption (data)
     { 
        //显示转台之焦点类型
        var showData = data.focustype;
        var resHtml = '<option value="0">请选择</option>';
        var n = showData.length;
       
        for (var i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        gimbalFocustype.html(resHtml);
        //显示转台之焦点类型 结束

        //显示转台之焦比
        showData = data.focusratio;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        gimbalFocusratio.html(resHtml);
        //显示转台之焦比 结束

        //显示ccd-No1之图像位数
        showData = data.imageBits;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
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

        //显示ccd-No1之制冷方式
        showData = data.coolerMode;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        ccdCoolerMode.html(resHtml);
        //显示ccd-No1之制冷方式 结束

        //显示ccd-No1之读出速度模式
        showData = data.readoutSpeed;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        ccdReadoutSpeed.html(resHtml);
        //显示ccd-No1之读出速度模式 结束

        //显示ccd-No1之读出模式
        showData = data.readoutMode;
        resHtml = '';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += showData[i] + "<input type='checkbox' name='readoutmode[]' value='"+ showData[i] +"'>&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        ccdreadoutMode.html(resHtml);
        //显示ccd-No1之读出模式 结束

        //显示ccd-No1之转移速度模式
        showData = data.transferSpeed;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        ccdTransferSpeed.html(resHtml);
        //显示ccd-No1之转移速度模式 结束

        //显示ccd-No1之增益模式
        showData = data.gainmode;
        resHtml = '';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += showData[i] + "<input type='checkbox' name='gainmode[]' value='"+ showData[i] +"'>&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        ccd_1gainmode.html(resHtml);
        //显示ccd-No1之增益模式 结束

        //显示ccd-No1之增益档位
        showData = data.gainNumber;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        ccd_1GainNumber.html(resHtml);
        //显示ccd-No1之增益档位 结束

        //显示ccd-No1之快门类型
        showData = data.ShutterType;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        ccdShutterType.html(resHtml);
        //显示ccd-No1之快门类型 结束

        //显示ccd-No1之快门模式
        showData = data.ShutterMode;
        resHtml = '';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += showData[i] + "<input type='checkbox' name='shuttermode[]' value='"+ showData[i] +"'>&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        ccd_1ShutterMode.html(resHtml);
        //显示ccd-No1之快门模式 结束

        //显示ccd-No1之Bin
        showData = data.BinArray;
        showData = showData[0].replace('[', '');
        showData = showData.replace(']', '');
        showData = showData.split(' ');
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] + '</option>';
        }
        ccd_1BinArray.html(resHtml);
        //显示ccd-No1之Bin 结束

        //显示ccd-No1之接口类型
        showData = data.InterfaceType;
        resHtml = '';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += showData[i] + "<input type='checkbox' name='interfacetype[]' value='"+ showData[i] +"'>&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        ccd_1InterfaceType.html(resHtml);
        //显示ccd-No1之接口类型 结束

        //显示ccd-No1之曝光触发模式
        showData = data.ExposeTriggerMode;
        resHtml = '';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += showData[i] + "<input type='checkbox' name='exposetriggermode[]' value='"+ showData[i] +"'>&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        ccd_1ExposeTriggerMode.html(resHtml);
        //显示ccd-No1之曝光触发模式 结束

        //显示滤光片类型
        showData = data.FilterSystem;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        filterSystem.html(resHtml);
        //显示滤光片类型 结束

        //显示滤光片形状
        showData = data.FilterShape;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        filterShape.html(resHtml);
        //显示滤光片形状 结束

        //显示随动圆顶类型
        showData = data.slaveDomeType;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        sDomeType.html(resHtml);
        //显示随动圆顶类型 结束

        //显示全开圆顶类型
        showData = data.openDomeType;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        oDomeType.html(resHtml);
        //显示全开圆顶类型 结束

        //显示全开圆顶类型
        showData = data.opticalStructure;
        resHtml = '<option value="0">请选择</option>';
        n = showData.length;
        for (i=0; i < n; i++)
        {
            resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        }
        guideScopeOpticalStructure.html(resHtml);
        //显示全开圆顶类型 结束

     }//显示19个固定属性选项 结束 

    //提交转台配置 之js事件/////////////////////////////////
    var gimbalForm = $('#gimbal'); //找到转台表单
    var gimbalBtn = $('#gimbalBtn'); //找到转台提交按钮
    var axis3_No = $('#gimbalHaveAxis3-1'); //无第3轴之radio
    var axis3_have = $('#gimbalHaveAxis3'); //无第3轴之radio
    var axis3_maxSpeed = $('#gimbalMaxAxis3Speed'); //轴3最大速度框
    var axis3_maxAccel = $('#gimbalMaxAxis3Acceleration'); //轴3最大加速度框
    var axis3_ParkPos = $('#gimbalAxis3ParkPosition'); //轴3复位位置框
    var gimbalCanSlewDerotator = $('#gimbalCanSlewDerotator'); //支持轴3指向固定位置
    var gimbalCanSlewDerotator_1 = $('#gimbalCanSlewDerotator-1'); //不支持轴3指向固定位置
    var gimbalCanConfigDerotator = $('#gimbalCanConfigDerotator'); //支持设置轴3工作模式
    var gimbalCanConfigDerotator_1 = $('#gimbalCanConfigDerotator-1'); //不支持设置轴3工作模式
    var gimbalFile = $('#gimbalFile'); //转台相关文件 显示
    var gimbalIp = $('#gimbalIp'); //转台ip
    var gimbalId = $('#gimbalId'); //转台 id
    var gimbalName = $('#gimbalName'); //转台 名称
    var gimbalAddress = $('#gimbalAddress'); //转台 隶属观测站
    var gimbalLongitude = $('#gimbalLongitude'); //转台 经度
    var gimbalLatitude = $('#gimbalLatitude'); //转台 纬度
    var gimbalAltitude = $('#gimbalAltitude'); //转台 海拔
    var gimbalAperture = $('#gimbalAperture'); //转台 口径
    var gimbalType = $('#gimbalType'); //转台 类型
    var focusLength = $('#gimbalFocuslength'); //转台 焦距
    var maxAxis1Speed = $('#gimbalMaxAxis1Speed'); //转台 轴1最大速度
    var maxAxis2Speed = $('#gimbalMaxAxis2Speed'); //转台 轴2最大速度
    var maxAxis3Speed = $('#gimbalMaxAxis3Speed'); //转台 轴3最大速度
    var axis1Acceleration = $('#gimbalMaxAxis1Acceleration'); //转台 轴1最加速度
    var axis2Acceleration = $('#gimbalMaxAxis2Acceleration'); //转台 轴2最加速度
    var axis1ParkPosition = $('#gimbalAxis1ParkPosition'); //转台 轴1复位位置
    var axis2ParkPosition = $('#gimbalAxis2ParkPosition'); //转台 轴2复位位置
    var gimbalHaveAxis3 = $('#gimbalHaveAxis3'); //转台 有轴3
    var gimbalHaveAxis3_1 = $('#gimbalHaveAxis3-1'); //转台 无轴3
    var haveAxis5 = $('#gimbalHaveAxis5'); //转台 有轴5
    var haveAxis5_1 = $('#gimbalHaveAxis5-1'); //转台 无轴5
    var gimbalMinElevation = $('#gimbalMinElevation'); //转台 俯仰最低值
    var numTemperatureSensor = $('#gimbalNumTemperatureSensor'); //转台 温度传感器数目
    var numHumiditySensor = $('#gimbalNumHumiditySensor'); //转台 湿度传感器数目
    var gimbalCanConnect = $('#gimbalCanConnect'); //转台 支持连接指令
    var gimbalCanConnect_1 = $('#gimbalCanConnect-1'); //转台 不支持连接
    var gimbalCanConnect_1 = $('#gimbalCanConnect-1'); //转台 不支持连接
    var gimbalCanFindHome = $('#gimbalCanFindHome'); //转台 支持找零
    var gimbalCanFindHome_1 = $('#gimbalCanFindHome-1'); //转台 不支持找零
    var gimbalCanTrackStar = $('#gimbalCanTrackStar'); //转台 支持跟踪恒星
    var gimbalCanTrackStar_1 = $('#gimbalCanTrackStar-1'); //转台 不支持跟踪恒星
    var gimbalCanSetObjectName = $('#gimbalCanSetObjectName'); //转台 支持设置目标名称
    var gimbalCanSetObjectName_1 = $('#gimbalCanSetObjectName-1'); //转台 不支持设置目标名称
    var gimbalCanSlewAzEl = $('#gimbalCanSlewAzEl'); //转台 支持指向固定位置
    var gimbalCanSlewAzEl_1 = $('#gimbalCanSlewAzEl-1'); //转台 不支持指向固定位置
    var gimbalCanStop = $('#gimbalCanStop'); //转台 支持停止
    var gimbalCanStop_1 = $('#gimbalCanStop-1'); //转台 不支持停止
    var gimbalCanSetTrackSpeed = $('#gimbalCanSetTrackSpeed'); //转台 支持设置跟踪速度
    var gimbalCanSetTrackSpeed_1 = $('#gimbalCanSetTrackSpeed-1'); //转台 不支持设置跟踪速度
    var gimbalCanPark = $('#gimbalCanPark'); //转台 支持复位
    var gimbalCanPark_1 = $('#gimbalCanPark-1'); //转台 不支持复位
    var gimbalCanFixedMove = $('#gimbalCanFixedMove'); //转台 支持恒速运动
    var gimbalCanFixedMove_1 = $('#gimbalCanFixedMove-1'); //转台 不支持恒速运动
    var canPositionCorrect = $('#gimbalCanPositionCorrect'); //转台 支持位置修正
    var canPositionCorrect_1 = $('#gimbalCanPositionCorrect-1'); //转台 不支持位置修正
    var gimbalCanCoverOperation = $('#gimbalCanCoverOperation'); //转台 支持镜盖操作
    var gimbalCanCoverOperation_1 = $('#gimbalCanCoverOperation-1'); //转台 不支持镜盖操作
    var gimbalCanFocusOperation = $('#gimbalCanFocusOperation'); //转台 支持焦点切换镜
    var gimbalCanFocusOperation_1 = $('#gimbalCanFocusOperation-1'); //转台 不支持焦点切换镜
    var gimbalCanEmergencyStop = $('#gimbalCanEmergencyStop'); //转台 支持急停指令
    var gimbalCanEmergencyStop_1 = $('#gimbalCanEmergencyStop-1'); //转台 不支持急停指令
    var canSaveSyncData = $('#gimbalCanSaveSyncData'); //转台 支持保存同步数据
    var canSaveSyncData_1 = $('#gimbalCanSaveSyncData-1'); //转台 不支持保存同步数据
    var canTrackSatellite = $('#gimbalCanTrackSatellite'); //转台 支持跟踪卫星
    var canTrackSatellite_1 = $('#gimbalCanTrackSatellite-1'); //转台 不支持跟踪卫星
    var gimbalCanconfigProperty = $('#gimbalCanconfigProperty'); //转台 支持属性设置
    var gimbalCanconfigProperty_1 = $('#gimbalCanconfigProperty-1'); //转台 不支持属性设置
    var gimbalAttrVersion = $('#gimbalAttrVersion'); //转台 属性版本号
    var gimbalAttrModifyTime = $('#gimbalAttrModifyTime'); //转台 属性更新时间
    var gimbal_h = $('#gimbal_h'); //标题
    
    //无第3轴时 禁用：轴3最大速度、轴3最大加速度、轴3复位位置gimbalHaveAxis3
    axis3_No.click(function () {
        axis3_maxSpeed.prop('disabled', true);axis3_maxSpeed.data('err',0);
        axis3_maxAccel.prop('disabled', true);axis3_maxAccel.data('err',0);
        axis3_ParkPos.prop('disabled', true);axis3_ParkPos.data('err',0);
        gimbalCanSlewDerotator_1.click();
        gimbalCanConfigDerotator_1.click();
    })

    //有第3轴时 启用：轴3最大速度、轴3最大加速度、轴3复位位置
    axis3_have.click(function () {
        axis3_maxSpeed.prop('disabled', false);
        axis3_maxAccel.prop('disabled', false);
        axis3_ParkPos.prop('disabled', false);
        gimbalCanSlewDerotator.click();
        gimbalCanConfigDerotator.click();
    })

    //转台提交按钮 点击事件
    gimbalBtn.click(function () {
        //检查望远镜下拉选择框 是否选择了某望远镜
        var atId = atNo.val();
        if ( atId == 0)
        {//未选择某个望远镜
            layer.alert('请选择您要配置的望远镜!', {shade:false,closeBtn:0});return;
        }

        var gimbal_form = new FormData(gimbalForm[0]);
        gimbal_form.append('teleid', atId); //将某望远镜的id 加入表单数据中
        //验证文本框类型、下拉框、复选框、 单选框配置项是否都已选择 
        if ( !gimbal_select_valid (gimbal_form) )
        {
            return;
        }//验证文本框类型、下拉框、复选框、 单选框配置项是否都已选择        

        $.ajax({
            type: 'post',
            url: 'gimbal_config',
            data : gimbal_form,
            processData : false,
            contentType : false,
            success:  function (info) {
                if ( info.indexOf('{') == -1 ) //info 不是json数据
                {
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
                }else{//解析 处理 json
                    var info = $.parseJSON(info);

                    layer.alert(info.msg, {
                        shade:false,
                        closeBtn:0,
                        yes:function (n){
                            layer.close(n);
                        },
                    });

                    gimbalAttrModifyTime.html(info.attrmodifytime); //显示属性更新时间
                    //在页面显示已上传的文件名
                    if ( info.file ) //有已上传的文件信息
                    {
                        show_file (gimbalFile, info.file);
                    }else{
                        gimbalFile.html('');
                    }
                }//解析 处理 json 结束

             },/*success方法结束 */
             error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0,});
             },
        })
    });//转台 提交按钮 js事件 结束
    //提交转台配置 之js事件 结束////////////////////////////$("input:disabled")
    var gimbal_text = gimbalForm.find('input[type="text"]');
    /*
    *gimbal_select_valid() 验证转台的所有须验证的text框、下拉框、下拉框有错误集中收集，一次提示，
      return bool值
    */
    function gimbal_select_valid (gimbal_form)
    {
        var gimbal_errMsg = ''; //转台表单的错误提示

        gimbal_text.each(//逐一验证文本输入框
            function () {
                $(this).blur();
                if ( $(this).data('err') == 1 )
                {
                    gimbal_errMsg += $(this).data('info') + '格式错误!<br>';
                }
            }
        );

        //逐一验证下拉选择框
        if ( gimbalType.val() == 0 ) //验证类型
        {
            gimbal_errMsg += '转台类型未选择!<br>';
        }

        if ( gimbalFocustype.val() == 0 ) //验证焦点类型
        {
            gimbal_errMsg += '焦点类型未选择!<br>';
        }

        if ( gimbalFocusratio.val() == 0 ) //验证焦比
        {
            gimbal_errMsg += '焦比未选择!<br>';
        }

        if ( gimbal_form.get('haveaxis3') === null ) //验证 是否有第3轴
        {
            gimbal_errMsg += '是否有第3轴未选择!<br>';
        }

        if ( gimbal_form.get('haveaxis5') === null ) //验证 镜盖（轴5）
        {
            gimbal_errMsg += '是否有镜盖（轴5）未选择!<br>';
        }

        if ( gimbal_form.get('canconnect') === null ) //验证 支持连接指令
        {
            gimbal_errMsg += '是否支持连接未选择!<br>';
        }

        if ( gimbal_form.get('canfindhome') === null ) //验证 支持找零指令
        {
            gimbal_errMsg += '是否支持找零未选择!<br>';
        }

        if ( gimbal_form.get('cantrackstar') === null ) //验证 支持跟踪恒星
        {
            gimbal_errMsg += '是否支持跟踪恒星未选择!<br>';
        }

        if ( gimbal_form.get('cansetobjectname') === null ) //验证 设置目标名称
        {
            gimbal_errMsg += '是否支持设置目标名称未选择!<br>';
        }

        if ( gimbal_form.get('canslewazel') === null ) //验证 指向固定位置
        {
            gimbal_errMsg += '是否支持指向固定位置未选择!<br>';
        }

        if ( gimbal_form.get('canslewderotator') === null ) //验证 轴3指向固定位置
        {
            gimbal_errMsg += '是否支持轴3指向固定位置未选择!<br>';
        }

        if ( gimbal_form.get('canconfigderotator') === null ) //验证 设置轴3工作模式
        {
            gimbal_errMsg += '是否支持设置轴3工作模式未选择!<br>';
        }

        if ( gimbal_form.get('canstop') === null ) //验证 停止指令
        {
            gimbal_errMsg += '是否支持停止指令未选择!<br>';
        }

        if ( gimbal_form.get('cansettrackspeed') === null ) //验证 设置跟踪速度
        {
            gimbal_errMsg += '是否支持设置跟踪速度未选择!<br>';
        }

        if ( gimbal_form.get('canpark') === null ) //验证 复位指令
        {
            gimbal_errMsg += '是否支持复位指令未选择!<br>';
        }

        if ( gimbal_form.get('canfixedmove') === null ) //验证 恒速运动
        {
            gimbal_errMsg += '是否支持恒速运动未选择!<br>';
        }

        if ( gimbal_form.get('canpositioncorrect') === null ) //验证 位置修正
        {
            gimbal_errMsg += '是否支持位置修正未选择!<br>';
        }

        if ( gimbal_form.get('cancoveroperation') === null ) //验证 镜盖操作
        {
            gimbal_errMsg += '是否支持镜盖操作未选择!<br>';
        }

        if ( gimbal_form.get('canfocusoperation') === null ) //验证 焦点切换镜
        {
            gimbal_errMsg += '是否支持焦点切换镜未选择!<br>';
        }

        if ( gimbal_form.get('canemergencystop') === null ) //验证 急停指令
        {
            gimbal_errMsg += '是否支持急停指令未选择!<br>';
        }

        if ( gimbal_form.get('cansavesyncdata') === null ) //验证 保存同步数据
        {
            gimbal_errMsg += '是否支持保存同步数据未选择!<br>';
        }

        if ( gimbal_form.get('cantracksatellite') === null ) //验证 跟踪卫星
        {
            gimbal_errMsg += '是否支持跟踪卫星未选择!<br>';
        }


        if ( gimbal_errMsg !== '' )
        {
            layer.alert(gimbal_errMsg, {shade:false, closeBtn:0});
            return false;
        }else{
            return true;
        }
    }/*gimbal_select_valid() 结束*/

    /*各text输入框的blur事件*/
    gimbalId.blur(function () {//转台id
        var that = $(this);
        var v = $.trim( that.val() );
        var err = 0;
		
		if ( v.length != 5 || !$.isNumeric(v) || v.indexOf('0') !== 0)
		{
            err = 1;  
            that.data('info', '转台id');
			layer.tips('id格式错误, 正确格式:03000!', that, {tipsMore: true});
        }
        that.data('err', err);
    })//转台id blur结束

    gimbalName.blur(function () {//望远镜名
        var that = $(this);
        var v = $.trim(that.val());
		var patn = /^\d(\d|\.)*m望远镜+$/;
		var err = 0;
		
		if ( !patn.test(v) )
		{
            err = 1;
            that.data('info', '望远镜名');
			layer.tips('望远镜名格式错误!', that, {tipsMore: true});
		}		
		that.data('err', err);
    });//望远镜名 blur结束

    gimbalAddress.blur(function () {//验证观测站
        var that = $(this);
        var v = $.trim(that.val());
		var err = 0;
		
		if ( v.length < 2 )
		{
            err = 1;
            that.data('info', '隶属观测站');
			layer.tips('隶属观测站格式错误!', that, {tipsMore: true});
		}		
		that.data('err', err);
    });//验证观测站 结束

    gimbalLongitude.blur(function () {//验证经度
        var that = $(this);
        var v = $.trim(that.val());
		var err = 0;
		
		if ( !$.isNumeric(v) || v > 180 || v < -180 )
		{
            err = 1;
            that.data('info', '经度');
			layer.tips('经度格式错误!', that, {tipsMore: true});
		}		
		that.data('err', err);
    });//验证经度 结束

    gimbalLatitude.blur(function () {//验证纬度
        var that = $(this);
        var v = $.trim(that.val());
		var err = 0;
		
		if ( !$.isNumeric(v) || v > 90 || v < -90 )
		{
            err = 1;
            that.data('info', '纬度');
			layer.tips('纬度格式错误!', that, {tipsMore: true});
		}		
		that.data('err', err);
    });//验证纬度 结束

    gimbalAltitude.blur(function () {//验证海拔
        var that = $(this);
        var v = $.trim(that.val());
		var err = 0;
		
		if ( !$.isNumeric(v) || v > 6000 || v < -1000 )
		{
            err = 1;
            that.data('info', '海拔');
			layer.tips('海拔格式错误!', that, {tipsMore: true});
		}		
		that.data('err', err);
    });//验证海拔 结束

    gimbalAperture.blur(function () {//验证口径
        var that = $(this);
        var v = $.trim(that.val());
		var err = 0;
		
		if ( !$.isNumeric(v) )
		{
            err = 1;
            that.data('info', '口径');
			layer.tips('口径格式错误!', that, {tipsMore: true});
		}		
		that.data('err', err);
    });//验证口径 结束

    focusLength.blur(function () {//焦距验证
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;
        var patn = /^\[[0-9.]+ [0-9.]+\]$/;
		
		if ( !patn.test(v) )
		{
            err = 1;
            that.data('info', '焦距');
			layer.tips('焦距格式错误!', that, {tipsMore: true});
		}		
        that.data('err', err);

    }); //焦距验证 结束

    maxAxis1Speed.blur(function () {//验证 轴1最大速度
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;
		
        if ( !$.isNumeric(v) || v > 30 || v < 0 )
		{
            err = 1;
            that.data('info', '轴1最大速度');
			layer.tips('轴1最大速度错误!', that, {tipsMore: true});
		}		
        that.data('err', err);
    }); //验证 轴1最大速度 结束

    maxAxis2Speed.blur(function () {//验证 轴2最大速度
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;
		
        if ( !$.isNumeric(v) || v > 30 || v < 0 )
		{
            err = 1;
            that.data('info', '轴2最大速度');
			layer.tips('轴2最大速度错误!', that, {tipsMore: true});
		}		
        that.data('err', err);
    }); //验证 轴2最大速度 结束

    axis3_maxSpeed.blur(function () {//验证 轴3最大速度
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v > 30 || v < 0 )
        {
            err = 1;
            that.data('info', '轴3最大速度');
            layer.tips('轴3最大速度错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 轴3最大速度 结束

    axis1Acceleration.blur(function () {//验证 轴1最大加速度
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v > 5 || v < 0 )
        {
            err = 1;
            that.data('info', '轴1最大加速度');
            layer.tips('轴1最大加速度错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 轴1最大加速度 结束

    axis2Acceleration.blur(function () {//验证 轴2最大加速度
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v > 5 || v < 0 )
        {
            err = 1;
            that.data('info', '轴2最大加速度');
            layer.tips('轴2最大加速度错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 轴2最大加速度 结束

    axis3_maxAccel.blur(function () {//验证 轴3最大加速度
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v > 5 || v < 0 )
        {
            err = 1;
            that.data('info', '轴3最大加速度');
            layer.tips('轴3最大加速度错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 轴3最大加速度 结束

    axis1ParkPosition.blur(function () {//验证 轴1复位位置
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v > 360 || v < 0 )
        {
            err = 1;
            that.data('info', '轴1复位位置');
            layer.tips('轴1复位位置错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 轴1复位位置 结束

    axis2ParkPosition.blur(function () {//验证 轴2复位位置
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v > 360 || v < 0 )
        {
            err = 1;
            that.data('info', '轴2复位位置');
            layer.tips('轴2复位位置错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 轴2复位位置 结束

    axis3_ParkPos.blur(function () {//验证 轴3复位位置
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v > 360 || v < 0 )
        {
            err = 1;
            that.data('info', '轴3复位位置');
            layer.tips('轴3复位位置错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 轴3复位位置 结束

    gimbalMinElevation.blur(function () {//验证 俯仰最低值
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v < 10 || v > 360)
        {
            err = 1;
            that.data('info', '俯仰最低值');
            layer.tips('俯仰最低值错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 俯仰最低值 结束

    numTemperatureSensor.blur(function () {//验证 温度传感器数目
        var that = $(this);
        var v = $.trim(that.val());
        var patn = /^[0-9]+$/;
        var err = 0;

        if ( !patn.test(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '温度传感器数目');
            layer.tips('温度传感器数目错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 温度传感器数目 结束

    numHumiditySensor.blur(function () {//验证 湿度传感器数目
        var that = $(this);
        var v = $.trim(that.val());
        var patn = /^[0-9]+$/;
        var err = 0;

        if ( !patn.test(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '湿度传感器数目');
            layer.tips('湿度传感器数目错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 湿度传感器数目 结束

    gimbalAttrVersion.blur(function () {//验证 属性版本号
        var that = $(this);
        var v = $.trim(that.val());
        var patn = /^[0-9]+$/;
        var err = 0;

        if ( !patn.test(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '属性版本号');
            layer.tips('属性版本号错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 属性版本号 结束
    /*转台 各text输入框的blur事件 结束*/

    /*显示转台的配置数据*/
    function show_gimbal_data (data)
    {
        //显示转台之焦点类型
        // var showData = data.focustype;
        // var resHtml = '<option value="0">请选择</option>';
        // var n = showData.length;
       
        // for (var i=0; i < n; i++)
        // {
        //     resHtml += '<option value="' + showData[i] + '">' + showData[i] +'</option>';
        // }
        // gimbalFocustype.html(resHtml);
        gimbalIp.val(data.ip);
        gimbalId.val(data.atid);
        gimbalName.val(data.atname);
        gimbalAddress.val(data.address);
        gimbalLongitude.val(data.longitude);
        gimbalLatitude.val(data.latitude);
        gimbalAltitude.val(data.altitude);
        gimbalAperture.val(data.aperture);
        data.type === undefined ? gimbalType.val('0') : gimbalType.val(data.type);
        data.focustype === undefined ? gimbalFocustype.val('0') : gimbalFocustype.val(data.focustype);
        data.focusratio === undefined ? gimbalFocusratio.val('0') : gimbalFocusratio.val(data.focusratio);
        focusLength.val(data.focuslength);
        maxAxis1Speed.val(data.maxaxis1speed);
        maxAxis2Speed.val(data.maxaxis2speed);
        maxAxis3Speed.val(data.maxaxis3speed);
        axis1Acceleration.val(data.maxaxis1acceleration);
        axis2Acceleration.val(data.maxaxis2acceleration);
        axis3_maxAccel.val(data.maxaxis3acceleration);
        axis1ParkPosition.val(data.axis1parkposition);
        axis2ParkPosition.val(data.axis2parkposition);
        axis3_ParkPos.val(data.axis3parkposition);
        data.haveaxis3 == '1' ? axis3_have.click() : axis3_No.click();
        data.haveaxis5 == '1' ? haveAxis5.click() : haveAxis5_1.click();
        gimbalMinElevation.val(data.minelevation);
        numTemperatureSensor.val(data.numtemperaturesensor);
        numHumiditySensor.val(data.numhumiditysensor);
        data.canconnect == '1' ? gimbalCanConnect.click() : gimbalCanConnect_1.click();
        data.canfindhome == '1' ? gimbalCanFindHome.click() : gimbalCanFindHome_1.click();
        data.cantrackstar == '1' ? gimbalCanTrackStar.click() : gimbalCanTrackStar_1.click();
        data.cansetobjectname == '1' ? gimbalCanSetObjectName.click() : gimbalCanSetObjectName_1.click();
        data.canslewazel == '1' ? gimbalCanSlewAzEl.click() : gimbalCanSlewAzEl_1.click();
        data.canstop == '1' ? gimbalCanStop.click() : gimbalCanStop_1.click();
        data.cansettrackspeed == '1' ? gimbalCanSetTrackSpeed.click() : gimbalCanSetTrackSpeed_1.click();
        data.canpark == '1' ? gimbalCanPark.click() : gimbalCanPark_1.click();
        data.canfixedmove == '1' ? gimbalCanFixedMove.click() : gimbalCanFixedMove_1.click();
        data.canpositioncorrect == '1' ? canPositionCorrect.click() : canPositionCorrect_1.click();
        data.cancoveroperation == '1' ? gimbalCanCoverOperation.click() : gimbalCanCoverOperation_1.click();
        data.canfocusoperation == '1' ? gimbalCanFocusOperation.click() : gimbalCanFocusOperation_1.click();
        data.canemergencystop == '1' ? gimbalCanEmergencyStop.click() : gimbalCanEmergencyStop_1.click();
        data.cansavesyncdata == '1' ? canSaveSyncData.click() : canSaveSyncData_1.click();
        data.cantracksatellite == '1' ? canTrackSatellite.click() : canTrackSatellite_1.click();
        data.canconfigproperty == '1' ? gimbalCanconfigProperty.click() : gimbalCanconfigProperty_1.click();
        gimbalAttrVersion.val(data.attrversion);
        data.attrmodifytime ? (gimbalAttrModifyTime.html(data.attrmodifytime), gimbal_h.html('转台固定属性')) : (gimbalAttrModifyTime.html(''), gimbal_h.html('转台固定属性:未进行配置'));
    }/*显示转台的配置数据 结束*/

    /*ccd-No1 表单元素获取*/
    var ccdBtn_1 = $('#ccdBtn-1'); //提交按钮
    var ccd_1Form = $('#ccd-1'); //表单
    var ccdFile = $('#ccdFile'); //ccd相关文件区
    var ccdIp = $('#ccdIp'); // ccd ip
    var ccdId = $('#ccdId'); // ccd id
    var ccdTeleId = $('#ccdTeleId'); // ccd 隶属望远镜
    var ccdName = $('#ccdName'); // ccd name
    var ccdXpixel = $('#ccdXpixel'); // ccd x像素
    var ccdYpixel = $('#ccdYpixel'); // ccd y像素
    var ccdXpixelSize = $('#ccdXpixelSize'); // ccd x像元
    var ccdYpixelSize = $('#ccdYpixelSize'); // ccd Y像元
    var ccdSensorName = $('#ccdSensorName'); // ccd 传感器名称
    var ccdLowCoolerT = $('#ccdLowCoolerT'); // ccd 最低制冷温度
    var ccdMaxExposureTime = $('#ccdMaxExposureTime'); // ccd 最大曝光时间
    var ccdMinExposureTime = $('#ccdMinExposureTime'); // ccd 最小曝光时间
    var ccdExposureTimeRation = $('#ccdExposureTimeRation'); // ccd 曝光时间分辨率
    var ccdFullWellDepth = $('#ccdFullWellDepth'); // ccd 满阱电荷
    var ccdIsSupportFullFrame = $('#ccdIsSupportFullFrame'); // ccd 支持帧转移
    var ccdIsSupportFullFrame_1 = $('#ccdIsSupportFullFrame-1'); // ccd 不支持帧转移
    var ccdIsSupportEM = $('#ccdIsSupportEM'); // ccd 支持EM
    var ccdIsSupportEM_1 = $('#ccdIsSupportEM-1'); // ccd 不支持EM
    var isSupportsCmosNoiseFilter = $('#ccdIsSupportsCmosNoiseFilter'); // ccd 支持CMOS noise filter
    var isSupportsCmosNoiseFilter_1 = $('#ccdIsSupportsCmosNoiseFilter-1'); // ccd 不支持CMOS noise filter
    var isSupportBaseLine = $('#ccdIsSupportBaseLine'); // ccd 支持base line
    var isSupportBaseLine_1 = $('#ccdIsSupportBaseLine-1'); // ccd 不支持base line
    var isSupportOverScan = $('#ccdIsSupportOverScan'); // ccd 支持Over scan
    var isSupportOverScan_1 = $('#ccdIsSupportOverScan-1'); // ccd 不支持Over scan
    var ccdIsSupportROI = $('#ccdIsSupportROI'); // ccd 支持开窗
    var ccdIsSupportROI_1 = $('#ccdIsSupportROI-1'); // ccd 不支持开窗
    var ccdEmMaxValue = $('#ccdEmMaxValue'); // ccd 最大EM
    var ccdEmMinValue = $('#ccdEmMinValue'); // ccd 最小EM
    var ccdCanConnect = $('#ccdCanConnect'); // ccd 支持连接
    var ccdCanConnect_1 = $('#ccdCanConnect-1'); // ccd 不支持连接
    var canSetCoolerT = $('#ccdCanSetCoolerT'); // ccd 制冷温度
    var canSetCoolerT_1 = $('#ccdCanSetCoolerT-1'); // ccd 不制冷温度
    var setExposureParam = $('#ccdCanSetExposureParam'); // ccd 设置曝光策略
    var setExposureParam_1 = $('#ccdCanSetExposureParam-1'); // ccd 不设置曝光策略
    var canStartExposure = $('#ccdCanStartExposure'); // ccd 支持开始曝光
    var canStartExposure_1 = $('#ccdCanStartExposure-1'); // ccd 不支持开始曝光
    var ccdCanStopExposure = $('#ccdCanStopExposure'); // ccd 支持停止曝光
    var ccdCanStopExposure_1 = $('#ccdCanStopExposure-1'); // ccd 不支持停止曝光
    var ccdCanAbortExposure = $('#ccdCanAbortExposure'); // ccd 支持终止曝光
    var ccdCanAbortExposure_1 = $('#ccdCanAbortExposure-1'); // ccd 不支持终止曝光
    var ccdCanSetGain = $('#ccdCanSetGain'); // ccd 支持设置增益
    var ccdCanSetGain_1 = $('#ccdCanSetGain-1'); // ccd 不支持设置增益
    var setReadoutSpeedMode = $('#ccdCanSetReadoutSpeedMode'); // ccd 支持设置读出速度模式
    var setReadoutSpeedMode_1 = $('#ccdCanSetReadoutSpeedMode-1'); // ccd 不支持设置读出速度模式
    var setTransferSpeedMode = $('#ccdCanSetTransferSpeedMode'); // 支持设置转移速度模式
    var setTransferSpeedMode_1 = $('#ccdCanSetTransferSpeedMode-1'); // 不支持设置转移速度模式
    var ccdCanSetBin = $('#ccdCanSetBin'); // 支持设置bin
    var ccdCanSetBin_1 = $('#ccdCanSetBin-1'); // 不支持设置bin
    var ccdCanSetROI = $('#ccdCanSetROI'); // 支持设置roi
    var ccdCanSetROI_1 = $('#ccdCanSetROI-1'); // 不支持设置roi
    var ccdCanSetShutter = $('#ccdCanSetShutter'); // 支持设置快门
    var ccdCanSetShutter_1 = $('#ccdCanSetShutter-1'); // 不支持设置快门
    var canSetFullFrame = $('#ccdCanSetFullFrame'); // 支持设置帧转移
    var canSetFullFrame_1 = $('#ccdCanSetFullFrame-1'); // 不支持设置帧转移
    var ccdCanSetEM = $('#ccdCanSetEM'); // 支持设置EM
    var ccdCanSetEM_1 = $('#ccdCanSetEM-1'); // 不支持设置EM
    var canNoiseFilter = $('#ccdCanNoiseFilter'); // 支持CMOS noise filter
    var canNoiseFilter_1 = $('#ccdCanNoiseFilter-1'); // 不支持CMOS noise filter
    var setBaseline = $('#ccdCanSetBaseline'); // 支持Base line
    var setBaseline_1 = $('#ccdCanSetBaseline-1'); // 不支持Base line
    var ccdSetOverScan = $('#ccdCanSetOverScan'); // 支持Over scan
    var ccdSetOverScan_1 = $('#ccdCanSetOverScan-1'); // 不支持Over scan
    var ccdAttrVersion = $('#ccdAttrVersion'); // 属性版本号
    var ccdAttrModifyTime = $('#ccdAttrModifyTime'); // 属性更新时间
    var ccd_h = $('#ccd_h'); //标题
    /*ccd-No1 表单元素获取 结束*/
    
    /*ccd 提交按钮 点击事件*/
    ccdBtn_1.click(function () {
        //检查望远镜下拉选择框 是否选择了某望远镜
        var atId = atNo.val();
        if ( atId == 0)
        {//未选择某个望远镜
            layer.alert('请选择您要配置的望远镜!', {shade:false, closeBtn:0});return;
        }

        var ccd_1_Data = new FormData(ccd_1Form[0]);
        ccd_1_Data.append('teleid', atId); //将某望远镜的id 加入表单数据中
        ccd_1_Data.append('ccdno', '1'); //将此望远镜的序号 加入表单数据中
        
        //验证文本框类型、下拉框、复选框、单选框配置项 是否都已选择 
         if ( !ccd_select_valid (ccd_1_Data) )
         {
             return;
         }//验证文本框类型、下拉框、复选框、单选框配置项  是否都已选择 结束

        $.ajax({
            type: 'post',
            url: 'ccd_config',
            data : ccd_1_Data,
            processData : false,
            contentType : false,
            success:  function (info) {
                if ( info.indexOf('{') == -1 ) //info 不是json数据
                {
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
                }else{//解析 处理 json
                    var info = $.parseJSON(info);
                    layer.alert(info.msg, {shade:false, closeBtn:0});
                    ccdAttrModifyTime.html(info.attrmodifytime); //显示属性更新时间
                    //在页面显示已上传的文件名
                    if ( info.file ) //有已上传的文件信息
                    {
                        show_file (ccdFile, info.file);
                    }else{
                        ccdFile.html('');
                    }
                }//解析 处理 json 结束
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
             },
        })
    });/*ccd 提交按钮 点击事件 结束*/

    /*显示ccd-No1配置数据*/
    function show_ccd_data (data)
    {
        ccdIp.val(data.ip);
        ccdId.val(data.ccdid);
        ccdName.val(data.name);
        ccdTeleId.html(data.atname);
        ccdXpixel.val(data.xpixel);
        ccdYpixel.val(data.ypixel);
        ccdXpixelSize.val(data.xpixelsize);
        ccdYpixelSize.val(data.ypixelsize);
         ccdSensorName.val(data.sensorname);
        data.imagebits === undefined ? ccdImageBits.val('0') : ccdImageBits.val(data.imagebits);
        data.coolermode === undefined ? ccdCoolerMode.val('0') : ccdCoolerMode.val(data.coolermode);
        ccdLowCoolerT.val(data.lowcoolert);
        ccdMaxExposureTime.val(data.maxexposuretime);
        ccdMinExposureTime.val(data.minexposuretime);
        ccdExposureTimeRation.val(data.exposuretimeration);
        ccdFullWellDepth.val(data.fullwelldepth);
        data.readoutspeed === undefined ? ccdReadoutSpeed.val('0') : ccdReadoutSpeed.val(data.readoutspeed);
        /*读出模式*/
        if ( data.readoutmode )
        {
            data.readoutmode = data.readoutmode.split('#');
            var readout_mode_n = data.readoutmode.length;
            for (var read_out_i = 0; read_out_i < readout_mode_n; read_out_i++)
            {//将对应的选项设为checked
                ccdreadoutMode.find('input[value="' + data.readoutmode[read_out_i] + '"]').prop('checked', true);
            }
        }/*读出模式 结束*/
        data.transferspeed === undefined ? ccdTransferSpeed.val('0') : ccdTransferSpeed.val(data.transferspeed);
        /*增益模式*/
        if ( data.gainmode )
        {
            data.gainmode = data.gainmode.split('#');
            var gainmode_n = data.gainmode.length;
            for (var gainmode_i = 0; gainmode_i < gainmode_n; gainmode_i++)
            {//将对应的选项设为checked
                ccd_1gainmode.find('input[value="' + data.gainmode[gainmode_i] + '"]').prop('checked', true);
            }
        }/*增益模式 结束*/
        data.gainnumber === undefined ? ccd_1GainNumber.val('0') : ccd_1GainNumber.val(data.gainnumber);
        /*
         *增益值和读出噪声值 暂时未做
        */
       data.shuttertype === undefined ? ccdShutterType.val('0') : ccdShutterType.val(data.shuttertype);
       /*快门模式*/
       if ( data.shuttermode )
       {
           data.shuttermode = data.shuttermode.split('#');
           var shuttermode_n = data.shuttermode.length;
           for (var shuttermode_i = 0; shuttermode_i < shuttermode_n; shuttermode_i++)
           {//将对应的选项设为checked
            ccd_1ShutterMode.find('input[value="' + data.shuttermode[shuttermode_i] + '"]').prop('checked', true);
           }
       }/*快门模式 结束*/
       data.issupportfullframe == '1' ? ccdIsSupportFullFrame.click() : ccdIsSupportFullFrame_1.click();
       data.issupportem == '1' ? ccdIsSupportEM.click() : ccdIsSupportEM_1.click();
       data.issupportscmosnoisefilter == '1' ? isSupportsCmosNoiseFilter.click() : isSupportsCmosNoiseFilter_1.click();
       data.issupportbaseline == '1' ? isSupportBaseLine.click() : isSupportBaseLine_1.click();
       data.issupportoverscan == '1' ? isSupportOverScan.click() : isSupportOverScan_1.click();
       data.binarray === undefined ? ccd_1BinArray.val('0') : ccd_1BinArray.val(data.binarray);
       data.issupportroi == '1' ? ccdIsSupportROI.click() : ccdIsSupportROI_1.click();
       /*接口类型*/
       if ( data.interfacetype )
       {
           data.interfacetype = data.interfacetype.split('#');
           var interfacetype_n = data.interfacetype.length;
           for (var interfacetype_i = 0; interfacetype_i < interfacetype_n; interfacetype_i++)
           {//将对应的选项设为checked
            ccd_1InterfaceType.find('input[value="' + data.interfacetype[interfacetype_i] + '"]').prop('checked', true);
           }
       }/*接口类型 结束*/
       /*曝光触发模式*/
       if ( data.exposetriggermode )
       {
           data.exposetriggermode = data.exposetriggermode.split('#');
           var exposetrigger_n = data.exposetriggermode.length;
           for (var exposetrigger_i = 0; exposetrigger_i < exposetrigger_n; exposetrigger_i++)
           {//将对应的选项设为checked
            ccd_1ExposeTriggerMode.find('input[value="' + data.exposetriggermode[exposetrigger_i] + '"]').prop('checked', true);
           }
       }/*曝光触发模式 结束*/
       ccdEmMaxValue.val(data.emmaxvalue);
       ccdEmMinValue.val(data.emminvalue);
       data.canconnect == '1' ? ccdCanConnect.click() : ccdCanConnect_1.click();
       data.cansetcoolert == '1' ? canSetCoolerT.click() : canSetCoolerT_1.click();
       data.cansetexposureparam == '1' ? setExposureParam.click() : setExposureParam_1.click();
       data.canstartexposure == '1' ? canStartExposure.click() : canStartExposure_1.click();
       data.canstopexposure == '1' ? ccdCanStopExposure.click() : ccdCanStopExposure_1.click();
       data.canabortexposure == '1' ? ccdCanAbortExposure.click() : ccdCanAbortExposure_1.click();
       data.cansetgain == '1' ? ccdCanSetGain.click() : ccdCanSetGain_1.click();
       data.cansetreadoutspeedmode == '1' ? setReadoutSpeedMode.click() : setReadoutSpeedMode_1.click();
       data.cansettransferspeedmode == '1' ? setTransferSpeedMode.click() : setTransferSpeedMode_1.click();
       data.cansetbin == '1' ? ccdCanSetBin.click() : ccdCanSetBin_1.click();
       data.cansetroi == '1' ? ccdCanSetROI.click() : ccdCanSetROI_1.click();
       data.cansetshutter == '1' ? ccdCanSetShutter.click() : ccdCanSetShutter_1.click();
       data.cansetfullframe == '1' ? canSetFullFrame.click() : canSetFullFrame_1.click();
       data.cansetem == '1' ? ccdCanSetEM.click() : ccdCanSetEM_1.click();
       data.cannoisefilter == '1' ? canNoiseFilter.click() : canNoiseFilter_1.click();
       data.cansetbaseline == '1' ? setBaseline.click() : setBaseline_1.click();
       data.cansetoverscan == '1' ? ccdSetOverScan.click() : ccdSetOverScan_1.click();
       data.attrmodifytime ? ( ccdAttrModifyTime.html(data.attrmodifytime), ccd_h.html('CCD-No1固定属性') ) : ( ccdAttrModifyTime.html(''),  ccd_h.html('CCD-No1固定属性:未进行配置'));
       ccdAttrVersion.val(data.attrversion);
    }/*显示ccd-No1配置数据 结束*/
    
    /*验证ccd-No1 表单数据*/
    /*
    * ccd_select_valid () ：验证ccd 表单数据
    * return bool值
    */
    var ccd_text = ccd_1Form.find('input[type="text"]');
    var ccdType = $('#ccdType'); //探测器类型

    function ccd_select_valid (ccd_1_Data)
    {
        var ccd_errMsg = ''; //ccd表单的错误提示

        ccd_text.each(//逐一验证文本输入框
            function () {
                $(this).blur();
                if ( $(this).data('err') == 1 )
                {
                    ccd_errMsg += $(this).data('info') + '格式错误!<br>';
                }
            }
        );
        //逐一验证下拉选择框
        if ( ccdType.val() == 0 ) //验证类型
        {
            ccd_errMsg += '探测器类型未选择!<br>';
        }

        if ( ccdImageBits.val() == 0 ) //验证 图像位数
        {
            ccd_errMsg += '图像位数未选择!<br>';
        }

        if ( ccdCoolerMode.val() == 0 ) //验证 制冷方式
        {
            ccd_errMsg += '制冷方式未选择!<br>';
        }

        if ( ccdReadoutSpeed.val() == 0 ) //验证 读出速度模式
        {
            ccd_errMsg += '读出速度模式未选择!<br>';
        }

        if ( ccdTransferSpeed.val() == 0 ) //验证 转移速度模式
        {
            ccd_errMsg += '转移速度模式未选择!<br>';
        }

        if ( ccd_1GainNumber.val() == 0 ) //验证 增益档位
        {
            ccd_errMsg += '增益档位未选择!<br>';
        }

        if ( ccdShutterType.val() == 0 ) //验证 快门类型
        {
            ccd_errMsg += '快门类型未选择!<br>';
        }

        if ( ccd_1BinArray.val() == 0 ) //验证 Bin值
        {
            ccd_errMsg += 'Bin值未选择!<br>';
        }

        var ccdreadoutMode_check_num = ccdreadoutMode.children('input:checkbox:checked').length; //读出模式复选框 被选中的数量
        if ( ccdreadoutMode_check_num < 1)
        {
            ccd_errMsg += '读出模式未选择!<br>';
        }

        var ccd_1gainmode_check_num = ccd_1gainmode.children('input:checkbox:checked').length; //增益模式复选框 被选中的数量
        if ( ccd_1gainmode_check_num < 1)
        {
            ccd_errMsg += '增益模式未选择!<br>';
        }

        var ccd_1ShutterMode_check_num = ccd_1ShutterMode.children('input:checkbox:checked').length; //快门模式复选框 被选中的数量
        if ( ccd_1ShutterMode_check_num < 1)
        {
            ccd_errMsg += '快门模式未选择!<br>';
        }

        var ccd_1InterfaceType_check_num = ccd_1InterfaceType.children('input:checkbox:checked').length; //接口类型复选框 被选中的数量
        if ( ccd_1InterfaceType_check_num < 1)
        {
            ccd_errMsg += '接口类型未选择!<br>';
        }

        var ccd_1ExposeTriggerMode_check_num = ccd_1ExposeTriggerMode.children('input:checkbox:checked').length; //曝光触发模式复选框 被选中的数量
        if ( ccd_1ExposeTriggerMode_check_num < 1)
        {
            ccd_errMsg += '曝光触发模式未选择!<br>';
        }

        if ( ccd_1_Data.get('issupportfullframe') === null ) //验证 支持帧转移
        {
            ccd_errMsg += '是否支持帧转移未选择!<br>';
        }

        if ( ccd_1_Data.get('issupportem') === null ) //验证 支持EM
        {
            ccd_errMsg += '是否支持EM未选择!<br>';
        }

        if ( ccd_1_Data.get('issupportscmosnoisefilter') === null ) //验证 CMOS noise filter
        {
            ccd_errMsg += 'CMOS noise filter未选择!<br>';
        }

        if ( ccd_1_Data.get('issupportbaseline') === null ) //验证 支持base line
        {
            ccd_errMsg += '是否支持base line未选择!<br>';
        }

        if ( ccd_1_Data.get('issupportoverscan') === null ) //验证 支持Over scan
        {
            ccd_errMsg += '是否支持Over scan未选择!<br>';
        }

        if ( ccd_1_Data.get('issupportroi') === null ) //验证 支持开窗
        {
            ccd_errMsg += '是否支持开窗未选择!<br>';
        }

        if ( ccd_1_Data.get('issupportroi') === null ) //验证 支持开窗
        {
            ccd_errMsg += '是否支持开窗未选择!<br>';
        }

        if ( ccd_1_Data.get('canconnect') === null ) //验证 支持连接
        {
            ccd_errMsg += '是否支持连接未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetcoolert') === null ) //验证 设置制冷温度
        {
            ccd_errMsg += '是否支持设置制冷温度未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetexposureparam') === null ) //验证 设置曝光策略
        {
            ccd_errMsg += '是否支持设置曝光策略未选择!<br>';
        }

        if ( ccd_1_Data.get('canstartexposure') === null ) //验证 支持开始曝光
        {
            ccd_errMsg += '是否支持开始曝光未选择!<br>';
        }

        if ( ccd_1_Data.get('canstopexposure') === null ) //验证 支持停止曝光
        {
            ccd_errMsg += '是否支持停止曝光未选择!<br>';
        }

        if ( ccd_1_Data.get('canabortexposure') === null ) //验证 支持终止曝光
        {
            ccd_errMsg += '是否支持终止曝光未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetgain') === null ) //验证 设置增益
        {
            ccd_errMsg += '是否支持设置增益未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetreadoutspeedmode') === null ) //验证 读出速度模式
        {
            ccd_errMsg += '是否支持设置读出速度模式未选择!<br>';
        }

        if ( ccd_1_Data.get('cansettransferspeedmode') === null ) //验证 转移速度模式
        {
            ccd_errMsg += '是否支持设置转移速度模式未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetbin') === null ) //验证 设置BIN
        {
            ccd_errMsg += '是否支持设置BIN未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetroi') === null ) //验证 设置ROI
        {
            ccd_errMsg += '是否支持设置ROI未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetshutter') === null ) //验证 设置快门
        {
            ccd_errMsg += '是否支持设置快门未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetfullframe') === null ) //验证 设置帧转移
        {
            ccd_errMsg += '是否支持设置帧转移未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetem') === null ) //验证 设置EM
        {
            ccd_errMsg += '是否支持设置EM未选择!<br>';
        }

        if ( ccd_1_Data.get('cannoisefilter') === null ) //验证 设置CMOS noise filter
        {
            ccd_errMsg += '是否支持设置CMOS noise filter未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetbaseline') === null ) //验证 设置Base line
        {
            ccd_errMsg += '是否支持设置Base line未选择!<br>';
        }

        if ( ccd_1_Data.get('cansetoverscan') === null ) //验证 设置Over scan
        {
            ccd_errMsg += '是否支持设置Over scan未选择!<br>';
        }

        if ( ccd_errMsg !== '' )
        {
            layer.alert(ccd_errMsg, {shade:false, closeBtn:0});
            return false;
        }else{
            return true;
        }
    }/*验证ccd-No1 表单数据 结束*/

    ccdId.blur(function () {//验证 ccdId
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v.length != 5 )
        {
            err = 1;
            that.data('info', 'ccd Id');
            layer.tips('ccd Id格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 ccdId 结束

    ccdName.blur(function () {//验证 ccd名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 2 )
        {
            err = 1;
            that.data('info', 'ccd 名称');
            layer.tips('ccd 名称格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 ccd名称 结束

    ccdXpixel.blur(function () {//验证 x像素
        var that = $(this);
        var v = $.trim(that.val());
        var patn = /^[0-9]{4,}$/;
        var err = 0;

        if ( !patn.test(v) || v < 1024)
        {
            err = 1;
            that.data('info', 'x像素');
            layer.tips('x像素格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 x像素 结束

    ccdYpixel.blur(function () {//验证 y像素
        var that = $(this);
        var v = $.trim(that.val());
        var patn = /^[0-9]{4,}$/;
        var err = 0;

        if ( !patn.test(v) || v < 1024)
        {
            err = 1;
            that.data('info', 'y像素');
            layer.tips('y像素格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 y像素 结束

    ccdXpixelSize.blur(function () {//验证 x像元
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0)
        {
            err = 1;
            that.data('info', 'x像元');
            layer.tips('x像元格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 x像元 结束

    ccdYpixelSize.blur(function () {//验证 y像元
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0)
        {
            err = 1;
            that.data('info', 'y像元');
            layer.tips('y像元格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 y像元 结束

    ccdSensorName.blur(function () {//验证 传感器名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 3)
        {
            err = 1;
            that.data('info', '传感器名称');
            layer.tips('传感器名称格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 传感器名称 结束

    ccdLowCoolerT.blur(function () {//验证 最低制冷温度
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) )
        {
            err = 1;
            that.data('info', '最低制冷温度');
            layer.tips('最低制冷温度格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 最低制冷温度 结束

    ccdMaxExposureTime.blur(function () {//验证 最大曝光时间
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0)
        {
            err = 1;
            that.data('info', '最大曝光时间');
            layer.tips('最大曝光时间格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 最大曝光时间 结束

    ccdMinExposureTime.blur(function () {//验证 最小曝光时间
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0)
        {
            err = 1;
            that.data('info', '最小曝光时间');
            layer.tips('最小曝光时间格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 最小曝光时间 结束

    ccdExposureTimeRation.blur(function () {//验证 曝光时间分辨率
        var that = $(this);
        var v = $.trim(that.val());
        var patn = /^[0-9]+$/;
        var err = 0;

        if ( !patn.test(v) || v < 1)
        {
            err = 1;
            that.data('info', '曝光时间分辨率');
            layer.tips('曝光时间分辨率格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 曝光时间分辨率 结束

    ccdFullWellDepth.blur(function () {//验证 满阱电荷
        var that = $(this);
        var v = $.trim(that.val());
        var patn = /^[0-9]+$/;
        var err = 0;

        if ( !patn.test(v) || v < 1)
        {
            err = 1;
            that.data('info', '满阱电荷');
            layer.tips('满阱电荷格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 满阱电荷 结束

    /*验证 增益值*/

    /*验证 读出噪声值*/

    ccdEmMaxValue.blur(function () {//验证 最大EM
        var that = $(this);
        var v = $.trim(that.val());
        var patn = /^[0-9]+$/;
        var err = 0;

        if ( !patn.test(v) )
        {
            err = 1;
            that.data('info', '最大EM');
            layer.tips('最大EM格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 最大EM 结束
    
    ccdEmMinValue.blur(function () {//验证 最小EM
        var that = $(this);
        var v = $.trim(that.val());
        var patn = /^[0-9]+$/;
        var err = 0;
        var ccd_EmMaxVal = $.trim(ccdEmMaxValue.val());
 
        if ( !patn.test(v) || v >= parseInt(ccd_EmMaxVal) )
        {
            err = 1;
            that.data('info', '最小EM');
            layer.tips('最小EM格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 最小EM 结束

    ccdAttrVersion.blur(function () {//验证 属性版本号
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 1 )
        {
            err = 1;
            that.data('info', '属性版本号');
            layer.tips('属性版本号格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 属性版本号 结束
    /****************ccd-No1 js结束 ****************/

    /*****************滤光片表单 按钮 js事件*************/
    var filterBtn = $('#filterBtn');
    var filterForm = $('#filter'); //滤光片表单
    var filterFile = $('#filterFile'); //滤光片相关文件
    var filterIp = $('#filterIp'); //滤光片ip
    var filterId = $('#filterId'); //滤光片id
    var filterName = $('#filterName'); //名称
    var filterTeleId = $('#filterTeleId'); //隶属望远镜
    var filterSize = $('#filterSize'); //插槽大小
    var filter_Name = $('#filter_Name'); //滤光片名称
    var focusLengthCompensate = $('#filterFocusLengthCompensate'); //滤光片焦距偏差值
    var canSetFilterPosition = $('#canSetFilterPosition'); //支持 设置滤光片位置
    var canSetFilterPosition_1 = $('#canSetFilterPosition-1'); //不支持 设置滤光片位置
    var filterCanConnect = $('#filterCanConnect'); //支持 连接
    var filterCanConnect_1 = $('#filterCanConnect-1'); //不支持 连接
    var filterCanFindHome = $('#filterCanFindHome'); //支持 找零
    var filterCanFindHome_1 = $('#filterCanFindHome-1'); //不支持 找零
    var filterAttrModifyTime = $('#filterAttrModifyTime'); //属性更新时间
    var filterAttrVersion = $('#filterAttrVersion'); //属性 版本号
    var filter_h = $('#filter_h'); //标题
 //根据插槽数目的值，在插槽序号的下拉框中显示相应数目的插槽 并显示各槽的滤光片类型
    /*1、首先获取插槽数目的值*/
    var filterNum = $('#filterNum'); //插槽数目这个input框
    /*2、插槽数目input框 blur事件*/
    var filterSlot = $('#filterSlot'); //插槽序号 下拉框
    filterNum.blur(function () {
        var that = $(this);
        var err = 0;

        //判断是否输入了插槽数目
        var slotNum = $.trim( that.val() );
        if ( !$.isNumeric(slotNum) || slotNum < 1)
        {
            err = 1;
            layer.tips('插槽数目的值有误!', that, {tipsMore: true});
            return;
        }
        that.data('err', err);
        
        //跟据新输入的插槽数目，删除filter_type中多余的数据
        for (p in filter_type)
        {
            if ( p > slotNum )
            {
                delete filter_type[p]
            }
        }

        /*根据插槽数目，显示相应数目的槽*/
        var slotHtml = '<option value="0">选择插槽</option>';
        for (var slot = 1; slot <= slotNum; slot++)
        {
            slotHtml += '<option value="' + slot + '">插槽' + slot + '</option>';
        }
        filterSlot.html(slotHtml);
    })/*插槽数目input框 blur事件结束*/
     
    /*3、插槽序号下拉框 点击事件*/
    var filter_type = {};  //定义一个对象存放各插槽的滤光片类型

    filterSlot.click(function (){
        //获取子元素<option>的数目
        var option_num = $(this).children('option').length;
        if ( option_num < 2 )
        {
            layer.tips('请先输入插槽数目!', $(this), {tipsMore: true});return;
        }
        //获取相应的插槽序号的滤光片类型，并显示之
        var slot_num = $(this).val();
        if ( filter_type[slot_num] )
        {
            filterSystem.val( filter_type[slot_num] );
        }
     
    })
    /*插槽序号下拉框 点击事件 结束*/

    /*4、滤光片类型下拉框 点击事件*/

    filterSystem.click(function () {
        var filterSlot_val = filterSlot.val(); //获取插槽序号的值
        if ( filterSlot_val == 0)
        {
            layer.alert('请先选择插槽序号!', {shade:false, closeBtn:0});return;
        }
        var filter_val = $(this).val();
        if ( filter_val != 0 )
        {//将滤光片类型的值存入 filter_type
            filter_type[filterSlot_val] = filter_val;
        }
        //console.log(filter_type[1]);
    })/*滤光片类型下拉框 点击事件 结束*/

    //根据插槽数目的值，在插槽序号的下拉框中显示相应数目的插槽 并显示各槽的滤光片类型 结束

    /* handle_filtersystem_data ()
    *  参数：filter_type 
    *  将filter_type的对象形式数据转为字符串形式
    *  parameter：{1:'a', 2:'b'}
    *   return: 1:a#2:b
    */
    function handle_filtersystem_data (filter_type)
    {
        var filter_system_str = '';
        var filter_system_i = 1;
         for (var p in filter_type)
         {
            filter_system_str += filter_system_i + ':' + filter_type[p] + '#';
            filter_system_i ++;
         }
         filter_system_str = filter_system_str.substring(0, filter_system_str.length-1);
        return filter_system_str;
    }/*将filter_type的对象形式数据转为字符串形式 结束*/

    /*滤光片 提交按钮 点击事件*/
    filterBtn.click(function () {
        //检查望远镜下拉选择框 是否选择了某望远镜
        var atId = atNo.val();
        if ( atId == 0)
        {//未选择某个望远镜
            layer.alert('请选择您要配置的望远镜!', {shade:false, closeBtn:0});return;
        }

        var filterData = new FormData(filterForm[0]);
        filterData.append('teleid', atId); //将某望远镜的id 加入表单数据中
        //将各插槽之滤光片类型数据加入表单 
        //加入之前先验证 filter_type的元素个数不能少于插槽数目值
        var tempStr = handle_filtersystem_data (filter_type);
        filterData.append('filtersystem', tempStr); //将各插槽之滤光片类型数据加入表单 结束

        //验证滤光片的各配置项
        if ( !filter_select_valid (filterData) )
        {
            return;
        }//验证滤光片的各配置项 结束

        $.ajax({
            type: 'post',
            url: 'filter_config',
            data : filterData,
            processData : false,
            contentType : false,
            success:  function (info) {
                if ( info.indexOf('{') == -1 ) //info 不是json数据
                {
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
                }else{//解析 处理 json
                    var info = $.parseJSON(info);
                    layer.alert(info.msg, {shade:false, closeBtn:0});
                    filterAttrModifyTime.html(info.attrmodifytime); //显示属性更新时间
                    //在页面显示已上传的文件名
                    if ( info.file ) //有已上传的文件信息
                    {
                        show_file (filterFile, info.file);
                    }else{
                        filterFile.html('');
                    }
                }//解析 处理 json 结束
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
             },
        })
    }); /*滤光片 提交按钮 点击事件 结束*/
    /*滤光片表单 按钮 js事件 结束*/

    /*显示滤光片配置数据 */
    function show_filter_data (data)
    {
        filterIp.val(data.ip);
        filterId.val(data.filterid);
        filterName.val(data.name);
        filterTeleId.html(data.atname);
        filterNum.val(data.numberoffilter);
        data.numberoffilter !== '' && filterNum.blur(); //如果插槽数目有值，执行插槽数目input框的blur事件
        /*将滤光片类型的值赋予：filter_type*/
        if (data.filtersystem)
        {
            filter_type = {}; //将此对象重置为空
            data.filtersystem = data.filtersystem.split('#'); //得到['1:aa', '2:cc']
            var filtersystem_num = data.filtersystem.length;
            for (var filtersystem_i = 0; filtersystem_i < filtersystem_num; filtersystem_i++)
            {
                var temp_data = data.filtersystem[filtersystem_i].split(':');
                filter_type[ temp_data[0] ] = temp_data[1];
            }
        }/*将滤光片类型的值赋予：filter_type 结束*/
        filterSize.val(data.filtersize);
        filter_Name.val(data.filtername);
        focusLengthCompensate.html(data.filterfocuslengthcompensate);
        filterShape.val(data.filtershape);
        data.cansetfilterposition == '1' ? canSetFilterPosition.click() : canSetFilterPosition_1.click();
        data.canconnect == '1' ? filterCanConnect.click() : filterCanConnect_1.click();
        data.canfindhome == '1' ? filterCanFindHome.click() : filterCanFindHome_1.click();
        data.attrmodifytime ? ( filterAttrModifyTime.html(data.attrmodifytime), filter_h.html('滤光片固定属性') ) : ( filterAttrModifyTime.html(''), filter_h.html('滤光片固定属性:未进行配置') );
        filterAttrVersion.val(data.attrversion);
    }/*显示滤光片配置数据 结束*/

    /*验证滤光片表单数据*/
    var filter_text = filterForm.find('input[type="text"]');
    /*
    *filter_select_valid () 验证滤光片表单的数据
    return：bool值
    */
    function filter_select_valid (filter_data)
    {
        var filter_errMsg = ''; //滤光片表单的错误提示

        filter_text.each(//逐一验证文本输入框
            function () {
                $(this).blur();
                if ( $(this).data('err') == 1 )
                {
                    filter_errMsg += $(this).data('info') + '格式错误!<br>';
                }
            }
        );

        //验证各插槽的滤光片类型是否都已进行配置选择
        var filter_type_p = 0;
        for ( p in filter_type )
        {
            filter_type_p ++;
        }
        if ( filter_type_p != parseInt( filterNum.val() ) )
        {
            filter_errMsg += '未对全部滤光片配置滤光片类型!<br>';
            layer.tips('未对全部滤光片配置滤光片类型!', filterSystem, {tipsMore: true});
        }

        if ( filterShape.val() == 0 ) //验证 滤光片形状
        {
            filter_errMsg += '滤光片形状未选择!<br>';
        }

        if ( filter_data.get('cansetfilterposition') === null ) //验证 设置滤光片位置
        {
            filter_errMsg += '是否支持设置滤光片位置未选择!<br>';
        }

        if ( filter_data.get('canconnect') === null ) //验证 支持连接
        {
            filter_errMsg += '是否支持连接未选择!<br>';
        }

        if ( filter_data.get('canfindhome') === null ) //验证 支持找零
        {
            filter_errMsg += '是否支持找零未选择!<br>';
        }

        if ( filter_errMsg !== '' )
        {
            layer.alert(filter_errMsg, {shade:false,closeBtn:0});
            return false;
        }else{
            return true;
        }
    }/*filter_select_valid () 结束*/
    
    //滤光片text框 blur事件
    filterId.blur(function () {//验证 滤光片id
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v.length != 5 )
        {
            err = 1;
            that.data('info', '滤光片id');
            layer.tips('滤光片id格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 滤光片id 结束

    filterName.blur(function () {//验证 名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 3 )
        {
            err = 1;
            that.data('info', '滤光片名称');
            layer.tips('滤光片名称格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 滤光片id 结束

    filterSize.blur(function () {//验证 插槽大小
        var that = $(this);
        var v = $.trim(that.val());
        var patn = /^\[[0-9]+,[0-9]+,[0-9]+\]$/;
        var err = 0;

        if ( !patn.test(v) )
        {
            err = 1;
            that.data('info', '插槽大小');
            layer.tips('插槽大小格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 插槽大小 结束

    filter_Name.blur(function () {//验证 滤光片名称
        var that = $(this);
        var filterNum_v = parseInt( $.trim( filterNum.val() ) )
        var v = $.trim(that.val());
        var err = 0;

        if ( !v )//v值为空
        {
            err = 1;
            that.data('info', '滤光片名称');
            layer.tips('滤光片名称格式错误!', that, {tipsMore: true});
        }else{//v值不为空

            if ( v.indexOf('/') === -1 ) //字符串中无'/', 则表明应该有一个插槽
            {
                if ( filterNum_v  != 1 )
                {
                    err = 1;
                    that.data('info', '滤光片名称');
                    layer.tips('滤光片名称设置与插槽数目不匹配!', that, {tipsMore: true});
                }
            }else{//字符串中有'/', 则表明应该有大于一个插槽
                v_num = v.split ('/').length;
                if ( v_num != filterNum_v )
                {
                    err = 1;
                    that.data('info', '滤光片名称');
                    layer.tips('滤光片名称设置与插槽数目不匹配!', that, {tipsMore: true});
                }
            } 
        }	
        that.data('err', err);
    });//验证 滤光片名称 结束

    focusLengthCompensate.blur(function () {//验证 滤光片焦距偏差值
        var that = $(this);
        var filterNum_v = parseInt( $.trim( filterNum.val() ) );
        var v = $.trim(that.val());
        var patn = /^\[[0-9]+( )?([0-9 ]+)?\]$/;
        var err = 0;
        
        if ( !patn.test(v) )
        {
            err = 1;
            that.data('info', '焦距偏差值');
            layer.tips('焦距偏差值格式错误!', that, {tipsMore: true});
        }else{
            v = v.replace('[');
            v = v.replace(']');
            var v_num = v.split(' ').length;

            if ( v_num != filterNum_v )
            {
                err = 1;
                that.data('info', '焦距偏差值');
                layer.tips('焦距偏差值与插槽数目不匹配!', that, {tipsMore: true});
            }
        }

        that.data('err', err);
    });//验证 焦距偏差值 结束

    filterAttrVersion.blur(function () {//验证 版本号
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;
        
        if ( v.length < 1 )
        {
            err = 1;
            that.data('info', '版本号');
            layer.tips('版本号格式错误!', that, {tipsMore: true});
        }
        that.data('err', err);
    });//验证 版本号 结束
    /**********************滤光片text框 blur事件 结束*****/
    /*************验证滤光片表单数据 结束*************/

    /*****随动圆顶  js事件**************/
    var sDomeBtn = $('#sDomeBtn'); //随动圆顶 提交按钮
    var sDomeForm = $('#sDome'); //随动圆顶 表单
    var sDomeTeleId = $('#sDomeTeleId'); //随动圆顶 隶属望远镜
    var sDomeFile = $('#sDomeFile'); //随动圆顶 相关文件
    var sDomeIp = $('#sDomeIp'); //随动圆顶 ip
    var sDomeId = $('#sDomeId'); //随动圆顶 id
    var sDomeName = $('#sDomeName'); //随动圆顶 名称
    var sDomeMaxSpeed = $('#sDomeMaxSpeed'); //随动圆顶 最大转动速度
    var sDomeDiameter = $('#sDomeDiameter'); //随动圆顶 尺寸大小
    var sDomeHasShade = $('#sDomeHasShade'); //随动圆顶 风帘
    var sDomeHasShade_1 = $('#sDomeHasShade-1'); //随动圆顶 无风帘
    var sDomeCanSetDomePositin = $('#sDomeCanSetDomePositin'); //随动圆顶 设置目标方位
    var sDomeCanSetDomePositin_1 = $('#sDomeCanSetDomePositin-1'); //随动圆顶 不支持设置目标方位
    var sDomeCanSetShadePosition = $('#sDomeCanSetShadePosition'); //随动圆顶 支持设置风帘位置
    var sDomeCanSetShadePosition_1 = $('#sDomeCanSetShadePosition-1'); //随动圆顶 不支持设置风帘位置
    var sDomeCanSetRotateSpeed = $('#sDomeCanSetRotateSpeed'); //随动圆顶 支持设置转动速度
    var sDomeCanSetRotateSpeed_1 = $('#sDomeCanSetRotateSpeed-1'); //随动圆顶 不支持设置转动速度
    var sDomeCanStop = $('#sDomeCanStop'); //随动圆顶 支持停止运动
    var sDomeCanStop_1 = $('#sDomeCanStop-1'); //随动圆顶 不支持停止运动
    var sDomeCanOpenShutter = $('#sDomeCanOpenShutter'); //随动圆顶 支持打开天窗
    var sDomeCanOpenShutter_1 = $('#sDomeCanOpenShutter-1'); //随动圆顶 不支持打开天窗
    var sDomeCanSetShadeSpeed = $('#sDomeCanSetShadeSpeed'); //随动圆顶 控制风帘运动
    var sDomeCanSetShadeSpeed_1 = $('#sDomeCanSetShadeSpeed-1'); //随动圆顶 控制风帘运动 否
    var sDomeCanConnect = $('#sDomeCanConnect'); //随动圆顶 支持连接
    var sDomeCanConnect_1 = $('#sDomeCanConnect-1'); //随动圆顶 支持连接 否
    var sDomeAttrModifyTime = $('#sDomeAttrModifyTime'); //属性更新时间
    var sDomeAttrVersion = $('#sDomeAttrVersion'); //属性 版本号
    var sDome_h = $('#sDome_h'); //标题

    sDomeBtn.click(function () {
        var atId = atNo.val();
        if ( atId == 0)
        {//未选择某个望远镜
            layer.alert('请选择您要配置的望远镜!', {shade:false, closeBtn:0});return;
        }
        var sDome_Data = new FormData(sDomeForm[0]);
        sDome_Data.append('teleid', atId); //将某望远镜的id 加入表单数据中

        //验证 随动圆顶的各配置项
        if ( !sDome_select_valid (sDome_Data) )
        {
            return;
        }//随动圆顶的各配置项 结束
        
        $.ajax({
            type: 'post',
            url: 'slaveDome_config',
            data : sDome_Data,
            processData : false,
            contentType : false,
            success:  function (info) {
                if ( info.indexOf('{') == -1 ) //info 不是json数据
                {
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

                }else{//解析 处理 json
                    var info = $.parseJSON(info);
                    layer.alert(info.msg, {shade:false, closeBtn:0});
                    sDomeAttrModifyTime.html(info.attrmodifytime);
                    //在页面显示已上传的文件名
                    if ( info.file ) //有已上传的文件信息
                    {
                        show_file (sDomeFile, info.file);
                    }else{
                        sDomeFile.html('');
                    }
                }//解析 处理 json 结束
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
             },
        })
    });/*随动圆顶 ajax 结束*/

    /*显示随动圆顶配置数据*/
    function show_sDome_data (data)
    {
        sDomeIp.val(data.ip);
        sDomeId.val(data.sdomeid);
        sDomeName.val(data.name);
        sDomeTeleId.html(data.atname);
        data.dometype === undefined ? sDomeType.val('0') : sDomeType.val(data.dometype);
        sDomeMaxSpeed.val(data.maxspeed);
        sDomeDiameter.val(data.diameter);
        data.hasshade == '1' ? sDomeHasShade.click() : sDomeHasShade_1.click();
        data.cansetdomepositin == '1' ? sDomeCanSetDomePositin.click() : sDomeCanSetDomePositin_1.click();
        data.cansetshadeposition == '1' ? sDomeCanSetShadePosition.click() : sDomeCanSetShadePosition_1.click();
        data.cansetrotatespeed == '1' ? sDomeCanSetRotateSpeed.click() : sDomeCanSetRotateSpeed_1.click();
        data.canstop == '1' ? sDomeCanStop.click() : sDomeCanStop_1.click();
        data.canopenshutter == '1' ? sDomeCanOpenShutter.click() : sDomeCanOpenShutter_1.click();
        data.cansetshadespeed == '1' ? sDomeCanSetShadeSpeed.click() : sDomeCanSetShadeSpeed_1.click();
        data.canconnect == '1' ? sDomeCanConnect.click() : sDomeCanConnect_1.click();
        data.attrmodifytime ? (sDomeAttrModifyTime.html(data.attrmodifytime), sDome_h.html('随动圆顶固定属性') ) : (sDomeAttrModifyTime.html(''), sDome_h.html('随动圆顶固定属性:未进行配置') );
        sDomeAttrVersion.val(data.attrversion);
    }/*显示随动圆顶配置数据 结束*/

    var sDome_text = sDomeForm.find('input[type="text"]'); //随动圆顶 text框
    /*
     sDome_select_valid () 验证 随动圆顶各表单项
     return bool
    */
    function sDome_select_valid (sDome_form)
    {
        var sDome_errMsg = ''; //随动圆顶表单的错误提示

        sDome_text.each(//逐一验证文本输入框
            function () {
                $(this).blur();
                if ( $(this).data('err') == 1 )
                {
                    sDome_errMsg += $(this).data('info') + '格式错误!<br>';
                }
            }
        );

        if ( sDomeType.val() == 0 ) //验证 类型
        {
            sDome_errMsg += '随动圆顶类型未选择!<br>';
        }

        if ( sDome_form.get('hasshade') === null ) //验证 风帘
        {
            sDome_errMsg += '是否有风帘未选择!<br>';
        }

        if ( sDome_form.get('cansetdomepositin') === null ) //验证 设置目标方位
        {
            sDome_errMsg += '是否支持设置目标方位未选择!<br>';
        }

        if ( sDome_form.get('cansetshadeposition') === null ) //验证 设置风帘位置
        {
            sDome_errMsg += '是否支持设置风帘位置未选择!<br>';
        }

        if ( sDome_form.get('cansetrotatespeed') === null ) //验证 设置转动速度
        {
            sDome_errMsg += '是否支持设置转动速度未选择!<br>';
        }

        if ( sDome_form.get('canstop') === null ) //验证 支持停止运动
        {
            sDome_errMsg += '是否支持停止运动未选择!<br>';
        }

        if ( sDome_form.get('canopenshutter') === null ) //验证 支持打开天窗
        {
            sDome_errMsg += '是否支持打开天窗未选择!<br>';
        }

        if ( sDome_form.get('cansetshadespeed') === null ) //验证 控制风帘运动
        {
            sDome_errMsg += '是否支持控制风帘运动未选择!<br>';
        }

        if ( sDome_form.get('canconnect') === null ) //验证 支持连接
        {
            sDome_errMsg += '是否支持连接未选择!<br>';
        }

        if ( sDome_errMsg !== '' )
        {
            layer.alert(sDome_errMsg, {shade:false, closeBtn:0});
            return false;
        }else{
            return true;
        }
    }/**sDome_select_valid () 结束**/

    sDomeId.blur(function () {//验证 随动圆顶id
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v.length != 5 )
        {
            err = 1;
            that.data('info', '随动圆顶id');
            layer.tips('随动圆顶id格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 随动圆顶id 结束

    sDomeId.blur(function () {//验证 随动圆顶id
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v.length != 5 )
        {
            err = 1;
            that.data('info', '随动圆顶id');
            layer.tips('随动圆顶id格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 随动圆顶id 结束

    sDomeName.blur(function () {//验证 随动圆顶名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 10 )
        {
            err = 1;
            that.data('info', '随动圆顶名称');
            layer.tips('随动圆顶名称须10位字符!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 随动圆顶名称 结束

    sDomeMaxSpeed.blur(function () {//验证 最大转动速度
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '最大转动速度');
            layer.tips('最大转动速度格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 随动圆顶id 结束

    sDomeDiameter.blur(function () {//验证 尺寸大小
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '尺寸大小');
            layer.tips('尺寸大小格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 尺寸大小 结束

    sDomeAttrVersion.blur(function () {//验证 版本号
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 1 )
        {
            err = 1;
            that.data('info', '版本号');
            layer.tips('版本号格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 版本号 结束
    /************随动圆顶  js事件 结束*******************/

    /*全开圆顶 js事件*/
    var oDomeForm = $('#oDome');  //获取表单
    var oDomeBtn = $('#oDomeBtn');  //获取 提交按钮
    var oDomeTeleId = $('#oDomeTeleId');  //全开圆顶 隶属望远镜
    var oDomeFile = $('#oDomeFile');  //全开圆顶 相关文件
    var oDomeIp = $('#oDomeIp');  //全开圆顶 ip
    var oDomeId = $('#oDomeId');  //全开圆顶 id
    var oDomeName = $('#oDomeName');  //名称
    var oDomeDiameter = $('#oDomeDiameter');  //尺寸
    var oDomeCanOpenDome = $('#oDomeCanOpenDome');  //打开圆顶 是
    var oDomeCanOpenDome_1 = $('#oDomeCanOpenDome-1');  //打开圆顶 否
    var oDomeCanConnect = $('#oDomeCanConnect');  //支持连接 是
    var oDomeCanConnect_1 = $('#oDomeCanConnect-1');  //支持连接 否
    var odomeAttrversion = $('#odomeAttrversion');  //属性版本号
    var oDomeAttrModifyTime = $('#oDomeAttrModifyTime');  //属性 更新时间
    var oDome_h = $('#oDome_h');  //标题

    oDomeBtn.click(function () {
        //检查望远镜下拉选择框 是否选择了某望远镜
        var atId = atNo.val();
        if ( atId == 0)
        {//未选择某个望远镜
            layer.alert('请选择您要配置的望远镜!', {shade:false, closeBtn:0});return;
        }
        var oDome_Data = new FormData(oDomeForm[0]);
        oDome_Data.append('teleid', atId); //将某望远镜的id 加入表单数据中
        
        //验证 全开圆顶的各配置项
        if ( !oDome_select_valid (oDome_Data) )
        {
            return;
        }//全开圆顶的各配置项 结束

        $.ajax({
            type: 'post',
            url: 'oDome_config',
            data : oDome_Data,
            processData : false,
            contentType : false,
            success:  function (info) {
                if ( info.indexOf('{') == -1 ) //info 不是json数据
                {
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
                }else{//解析 处理 json
                    var info = $.parseJSON(info);
                    layer.alert(info.msg, {shade:false, closeBtn:0});
                    oDomeAttrModifyTime.html(info.attrmodifytime);
                    //在页面显示已上传的文件名
                    if ( info.file ) //有已上传的文件信息
                    {
                        show_file (oDomeFile, info.file);
                    }else{
                        oDomeFile.html('');
                    }
                }//解析 处理 json 结束
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
             },
        })
    });/*全开圆顶 ajax 结束*/

    function show_oDome_data (data)
    {
        oDomeIp.val(data.ip);
        oDomeId.val(data.odomeid);
        oDomeName.val(data.name);
        oDomeTeleId.html(data.atname);
        data.type === undefined ? oDomeType.val('0') : oDomeType.val(data.type);
        oDomeDiameter.val(data.diameter);
        data.canopendome == '1' ? oDomeCanOpenDome.click() : oDomeCanOpenDome_1.click();
        data.canconnect == '1' ? oDomeCanConnect.click() : oDomeCanConnect_1.click();
        odomeAttrversion.val(data.attrversion);
        data.attrmodifytime ? ( oDomeAttrModifyTime.html(data.attrmodifytime), oDome_h.html('全开圆顶固定属性') ) : ( oDomeAttrModifyTime.html(''), oDome_h.html('全开圆顶固定属性:未进行配置') ) ;
    }

    var oDome_text = oDomeForm.find('input[type="text"]'); //全开圆顶 text框
    /*
    *oDome_select_valid () 验证全开圆顶的表单项
    return: bool
    */
    function oDome_select_valid (oDome_form)
    {
        var oDome_errMsg = ''; //全开圆顶表单的错误提示

        oDome_text.each(//逐一验证文本输入框
            function () {
                $(this).blur();
                if ( $(this).data('err') == 1 )
                {
                    oDome_errMsg += $(this).data('info') + '格式错误!<br>';
                }
            }
        );

        if ( oDomeType.val() == 0 ) //验证 类型
        {
            oDome_errMsg += '全开圆顶类型未选择!<br>';
        }

        if ( oDome_form.get('canopendome') === null ) //验证 支持打开圆顶
        {
            oDome_errMsg += '是否支持打开圆顶未选择!<br>';
        }

        if ( oDome_form.get('canconnect') === null ) //验证 支持连接
        {
            oDome_errMsg += '是否支持连接未选择!<br>';
        }

        if ( oDome_errMsg !== '' )
        {
            layer.alert(oDome_errMsg, {shade:false, closeBtn:0});
            return false;
        }else{
            return true;
        }
    }/*oDome_select_valid () 结束*********************/

    oDomeId.blur(function () {//验证 全开圆顶id
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v.length != 5 )
        {
            err = 1;
            that.data('info', '全开圆顶id');
            layer.tips('全开圆顶id格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 全开圆顶id 结束

    oDomeName.blur(function () {//验证 全开圆顶名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 10 )
        {
            err = 1;
            that.data('info', '全开圆顶名称');
            layer.tips('全开圆顶名称须10位字符!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 全开圆顶名称 结束

    oDomeDiameter.blur(function () {//验证 全开圆顶尺寸
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '全开圆顶尺寸');
            layer.tips('全开圆顶尺寸格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 全开圆顶尺寸 结束

    odomeAttrversion.blur(function () {//验证 版本号
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 1 )
        {
            err = 1;
            that.data('info', '版本号');
            layer.tips('版本号格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 版本号 结束
    /**************全开圆顶 js事件 结束**************/

    /*调焦器 js事件*/
    var focusBtn = $('#focusBtn'); //获取调焦器 提交按钮
    var focusForm = $('#focus'); //调焦器 表单
    var focusAttrModifyTime = $('#focusAttrModifyTime'); //属性更新时间
    var focusFile = $('#focusFile'); //相关文件
    var focusIp = $('#focusIp'); //ip
    var focusId = $('#focusId'); //id
    var focusName = $('#focusName'); //名称
    var focusTeleId = $('#focusTeleId'); //隶属望远镜
    var focusMaxValue = $('#focusMaxValue'); //最大值
    var focusMinValue = $('#focusMinValue'); //最小值
    var focusIncrement = $('#focusIncrement'); //分辨率
    var focusMaxSpeed = $('#focusMaxSpeed'); //最大速度
    var focusCanConnect = $('#focusCanConnect'); //连接 是
    var focusCanConnect_1 = $('#focusCanConnect-1'); //连接 否
    var focusCanFindHome = $('#focusCanFindHome'); //找零 是
    var focusCanFindHome_1 = $('#focusCanFindHome-1'); //找零 否
    var focusTempeCompensate = $('#focusTempeCompensate'); //进行温度补偿 是
    var focusTempeCompensate_1 = $('#focusTempeCompensate-1'); //进行温度补偿 否
    var focusCanSetPosition = $('#focusCanSetPosition'); //进行温度补偿 是
    var focusCanSetPosition_1 = $('#focusCanSetPosition-1'); //进行温度补偿 否
    var focusCanSetSpeed = $('#focusCanSetSpeed'); //设置恒速运动 是
    var focusCanSetSpeed_1 = $('#focusCanSetSpeed-1'); //设置恒速运动 否
    var focusCanStop = $('#focusCanStop'); //支持停止运动 是
    var focusCanStop_1 = $('#focusCanStop-1'); //支持停止运动 否
    var enableTemperCompensate = $('#enableTemperCompensate'); //使能温度补偿 是
    var enableTemperCompensate_1 = $('#enableTemperCompensate-1'); //使能温度补偿 否
    var focusTemperCompensateCoef_1 = $('#focusTemperCompensateCoef-1'); //设置温度补偿系数 否
    var focusAttrVersion = $('#focusAttrVersion'); //属性版本号
    var focus_h = $('#focus_h'); //标题

    focusBtn.click(function () {
        //检查望远镜下拉选择框 是否选择了某望远镜
        var atId = atNo.val();
        if ( atId == 0)
        {//未选择某个望远镜
            layer.alert('请选择您要配置的望远镜!', {shade:false, closeBtn:0});return;
        }
        var focus_Data = new FormData(focusForm[0]);
        focus_Data.append('teleid', atId); //将某望远镜的id 加入表单数据中

        //验证文本框类型、下拉框、复选框、单选框配置项 是否都已选择 
        if ( !focus_select_valid (focus_Data) )
        {
            return;
        }//验证文本框类型、下拉框、复选框、单选框配置项  是否都已选择 结束        

        $.ajax({
            type: 'post',
            url: 'focus_config',
            data : focus_Data,
            processData : false,
            contentType : false,
            success:  function (info) {
                if ( info.indexOf('{') == -1 ) //info 不是json数据
                {
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
                }else{//解析 处理 json
                    var info = $.parseJSON(info);
                    layer.alert(info.msg, {shade:false, closeBtn:0});
                    focusAttrModifyTime.html(info.attrmodifytime);
                    //在页面显示已上传的文件名
                    if ( info.file ) //有已上传的文件信息
                    {
                        show_file (focusFile, info.file);
                    }else{
                        focusFile.html('');
                    }
                }//解析 处理 json 结束
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
             },
        })
    });/*调焦器 ajax 结束*/

    function show_focus_data (data)
    {
        focusIp.val(data.ip);
        focusId.val(data.focusid);
        focusName.val(data.name);
        focusTeleId.html(data.atname);
        focusMaxValue.val(data.maxvalue);
        focusMinValue.val(data.minvalue);
        focusIncrement.val(data.increment);
        focusMaxSpeed.val(data.maxspeed);
        data.canconnect == '1' ? focusCanConnect.click() : focusCanConnect_1.click();
        data.canfindhome == '1' ? focusCanFindHome.click() : focusCanFindHome_1.click();
        data.cantemperturecompensate == '1' ? focusTempeCompensate.click() : focusTempeCompensate_1.click();
        data.cansetposition == '1' ? focusCanSetPosition.click() : focusCanSetPosition_1.click();
        data.cansetspeed == '1' ? focusCanSetSpeed.click() : focusCanSetSpeed_1.click();
        data.canstop == '1' ? focusCanStop.click() : focusCanStop_1.click();
        data.canenabletemperturecompensate == '1' ? enableTemperCompensate.click() : enableTemperCompensate_1.click();
        data.cansettemperturecompensatecoefficient == '1' ? focusTemperCompensateCoef.click() : focusTemperCompensateCoef_1.click();
        focusAttrVersion.val(data.attrversion);
        data.attrmodifytime ? (focusAttrModifyTime.html(data.attrmodifytime), focus_h.html('调焦器固定属性') ) : (focusAttrModifyTime.html(''), focus_h.html('调焦器固定属性:未进行配置') );
    }

    var focus_text = focusForm.find('input[type="text"]'); //调焦器 text框
    /*
    *focus_select_valid () 验证 调焦器 表单各项
    return：bool
    */
    function focus_select_valid (focus_form)
    {
        var focus_errMsg = ''; //全开圆顶表单的错误提示

        focus_text.each(//逐一验证文本输入框
            function () {
                $(this).blur();
                if ( $(this).data('err') == 1 )
                {
                    focus_errMsg += $(this).data('info') + '格式错误!<br>';
                }
            }
        );

        if ( focus_form.get('canconnect') === null ) //验证 支持连接
        {
            focus_errMsg += '是否支持连接未选择!<br>';
        }

        if ( focus_form.get('canfindhome') === null ) //验证 支持找零
        {
            focus_errMsg += '是否支持找零未选择!<br>';
        }

        if ( focus_form.get('cantemperturecompensate') === null ) //验证 进行温度补偿
        {
            focus_errMsg += '是否支持进行温度补偿未选择!<br>';
        }

        if ( focus_form.get('cansetposition') === null ) //验证 设置目标位置
        {
            focus_errMsg += '是否支持设置目标位置未选择!<br>';
        }

        if ( focus_form.get('cansetspeed') === null ) //验证 设置恒速运动
        {
            focus_errMsg += '是否支持设置恒速运动未选择!<br>';
        }
        
        if ( focus_form.get('canstop') === null ) //验证 支持停止运动
        {
            focus_errMsg += '是否支持停止运动未选择!<br>';
        }

        if ( focus_form.get('canenabletemperturecompensate') === null ) //验证 使能温度补偿
        {
            focus_errMsg += '是否使能温度补偿未选择!<br>';
        }

        if ( focus_form.get('cansettemperturecompensatecoefficient') === null ) //验证 设置温度补偿系数
        {
            focus_errMsg += '是否设置温度补偿系数未选择!<br>';
        }

        if ( focus_errMsg !== '' )
        {
            layer.alert(focus_errMsg, {shade:false, closeBtn:0});
            return false;
        }else{
            return true;
        }
    }/*  focus_select_valid () 结束*********/

    focusId.blur(function () {//验证 调焦器id
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v.length != 5 )
        {
            err = 1;
            that.data('info', '调焦器id');
            layer.tips('调焦器id须5位数字!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 调焦器id 结束

    focusName.blur(function () {//验证 调焦器名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 2 )
        {
            err = 1;
            that.data('info', '调焦器名称');
            layer.tips('调焦器名称格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 调焦器id 结束

    focusMaxValue.blur(function () {//验证 最大值
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '调焦器最大值');
            layer.tips('调焦器最大值输入错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 调焦器最大值 结束

    focusMinValue.blur(function () {//验证 最小值
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '调焦器最小值');
            layer.tips('调焦器最小值输入错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 调焦器最小值 结束

    focusIncrement.blur(function () {//验证 分辨率
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '调焦器分辨率');
            layer.tips('调焦器分辨率输入错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 调焦器分辨率 结束

    focusMaxSpeed.blur(function () {//验证 最大速度
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '调焦器最大速度');
            layer.tips('调焦器最大速度输入错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 最大速度 结束

    focusAttrVersion.blur(function () {//验证 版本号
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 1 )
        {
            err = 1;
            that.data('info', '调焦器版本号');
            layer.tips('调焦器版本号输入错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });
    /***************调焦器 js事件 结束**************/

    /*导星望远镜 js事件*/
    var guideScopeBtn = $('#guideScopeBtn'); //提交按钮
    var guideScope = $('#guideScope'); //表单
    var guideScopeAttrModifyTime = $('#guideScopeAttrModifyTime'); //属性更新时间
    var guideScopeFile = $('#guideScopeFile'); //相关文件
    var guideScopeIp = $('#guideScopeIp'); //ip
    var guideScopeId = $('#guideScopeId'); //id
    var guideScopeName = $('#guideScopeName'); //名称
    var guideScopeTeleId = $('#guideScopeTeleId'); //隶属望远镜
    var guideScopeAperture = $('#guideScopeAperture'); //口径
    var guideScopeFocusLength = $('#guideScopeFocusLength'); //焦距
    var guideScopeMirrorCover = $('#guideScopeMirrorCover'); //镜盖（轴5） 有
    var guideScopeMirrorCover_1 = $('#guideScopeMirrorCover-1'); //镜盖（轴5） 无
    var SupportAutoFocus = $('#SupportAutoFocus'); //自动调焦 是
    var SupportAutoFocus_1 = $('#SupportAutoFocus-1'); //自动调焦 否
    var guideScopeCanConnect = $('#guideScopeCanConnect'); //连接 是
    var guideScopeCanConnect_1 = $('#guideScopeCanConnect-1'); //连接 否
    var guideScopeOpenCover = $('#guideScopeOpenCover'); //镜盖操作 是
    var guideScopeOpenCover_1 = $('#guideScopeOpenCover-1'); //镜盖操作 否
    var guideEnableAutoFocus = $('#guideEnableAutoFocus'); //使能自动调焦 是
    var guideEnableAutoFocus_1 = $('#guideEnableAutoFocus-1'); //使能自动调焦 否
    var guideScopeAttrVersion = $('#guideScopeAttrVersion'); //版本号
    var guideScope_h = $('#guideScope_h'); //标题
    

    guideScopeBtn.click(function () {
        //检查望远镜下拉选择框 是否选择了某望远镜
        var atId = atNo.val();
        if ( atId == 0)
        {//未选择某个望远镜
            layer.alert('请选择您要配置的望远镜!', {shade:false, closeBtn:0});return;
        }
        var guide_Data = new FormData(guideScope[0]);
        guide_Data.append('teleid', atId); //将某望远镜的id 加入表单数据中

        //验证文本框类型、下拉框、复选框、单选框配置项 是否都已选择 
        if ( !guide_select_valid (guide_Data) )
        {
            return;
        }//验证文本框类型、下拉框、复选框、单选框配置项  是否都已选择 结束        

        $.ajax({
            type: 'post',
            url: 'guideScope_config',
            data : guide_Data,
            processData : false,
            contentType : false,
            success:  function (info) {
                if ( info.indexOf('{') == -1 ) //info 不是json数据
                {
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
                }else{//解析 处理 json
                    var info = $.parseJSON(info);
                    layer.alert(info.msg, {shade:false, closeBtn:0});
                    guideScopeAttrModifyTime.html(info.attrmodifytime);
                    //在页面显示已上传的文件名
                    if ( info.file ) //有已上传的文件信息
                    {
                        show_file (guideScopeFile, info.file);
                    }else{
                        guideScopeFile.html('');
                    }
                }//解析 处理 json 结束
             },
             error:  function () {
	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
             },
        })
    });/*导星望远镜 ajax 结束*/

    function show_guide_data (data)
    {
        guideScopeIp.val(data.ip);
        guideScopeId.val(data.guidescopeid);
        guideScopeName.val(data.name);
        guideScopeTeleId.html(data.atname);
        guideScopeAperture.val(data.aperture);
        guideScopeFocusLength.val(data.focuslength);
        data.opticalstructure === undefined ? guideScopeOpticalStructure.val('0') : guideScopeOpticalStructure.val(data.opticalstructure);
        data.hasmirrorcover == '1' ? guideScopeMirrorCover.click() : guideScopeMirrorCover_1.click();
        data.issupportautofocus == '1' ? SupportAutoFocus.click() : SupportAutoFocus_1.click();
        data.canconnect == '1' ? guideScopeCanConnect.click() : guideScopeCanConnect_1.click();
        data.canopencover == '1' ? guideScopeOpenCover.click() : guideScopeOpenCover_1.click();
        data.canenableautofocus == '1' ? guideEnableAutoFocus.click() : guideEnableAutoFocus_1.click();
        guideScopeAttrVersion.val(data.attrversion);
        data.attrmodifytime ? (guideScopeAttrModifyTime.html(data.attrmodifytime), guideScope_h.html('导星望远镜固定属性') ) : (guideScopeAttrModifyTime.html(''), guideScope_h.html('导星望远镜固定属性:未进行配置') );
    }

    var guide_text = guideScope.find('input[type="text"]'); //导星镜 text框
    /*
    * guide_select_valid () 验证导星镜 表单各项
    * return: bool
    */
    function guide_select_valid (guide_form)
    {
        var guide_errMsg = ''; //全开圆顶表单的错误提示

        guide_text.each(//逐一验证文本输入框
            function () {
                $(this).blur();
                if ( $(this).data('err') == 1 )
                {
                    guide_errMsg += $(this).data('info') + '格式错误!<br>';
                }
            }
        );

        if ( guideScopeOpticalStructure.val() == 0 )
        {
            guide_errMsg += '导星镜焦点类型未选择!<br>';
        }

        if ( guide_form.get('hasmirrorcover') === null ) //验证 镜盖（轴5）
        {
            guide_errMsg += '是否有镜盖（轴5）未选择!<br>';
        }

        if ( guide_form.get('issupportautofocus') === null ) //验证 支持自动调焦
        {
            guide_errMsg += '是否支持自动调焦未选择!<br>';
        }

        if ( guide_form.get('canconnect') === null ) //验证 支持连接
        {
            guide_errMsg += '是否支持连接未选择!<br>';
        }

        if ( guide_form.get('canopencover') === null ) //验证 支持镜盖操作
        {
            guide_errMsg += '是否支持镜盖操作未选择!<br>';
        }

        if ( guide_form.get('canenableautofocus') === null ) //验证 使能自动调焦
        {
            guide_errMsg += '是否使能自动调焦未选择!<br>';
        }

        if ( guide_errMsg !== '' )
        {
            layer.alert(guide_errMsg, {shade:false, closeBtn:0});
            return false;
        }else{
            return true;
        }
    }/*******guide_select_valid () 结束***************/

    guideScopeId.blur(function () {//验证 导星镜id
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v.length != 5 )
        {
            err = 1;
            that.data('info', '导星镜id');
            layer.tips('导星镜id格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 导星镜id 结束

    guideScopeName.blur(function () {//验证 导星镜名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 3 )
        {
            err = 1;
            that.data('info', '导星镜名称');
            layer.tips('导星镜名称格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 导星镜id 结束

    guideScopeFocusLength.blur(function () {//验证 焦距
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '导星镜焦距');
            layer.tips('导星镜焦距格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 导星镜焦距 结束

    guideScopeAperture.blur(function () {//验证 口径
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0 )
        {
            err = 1;
            that.data('info', '导星镜口径');
            layer.tips('导星镜口径格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 导星镜口径 结束

    guideScopeAttrVersion.blur(function () {//验证 版本号
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 1 )
        {
            err = 1;
            that.data('info', '版本号');
            layer.tips('版本号格式错误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 版本号 结束
    /*************导星望远镜 js事件 结束*****************/

    /*显示各设备相关文件*/
    function show_file (selector, file_data)
    {
        var file_html= '';
        var file_num = file_data.length;
        for (var file_i = 0; file_i < file_num; file_i ++)
        {
            file_html += '<a title="点击下载">' + file_data[file_i] + '</a>' + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        selector.html(file_html);
    }/*显示各设备相关文件 结束*/
})/*jquery 初始化函数 末尾*/