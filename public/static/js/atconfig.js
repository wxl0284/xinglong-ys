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
                        //console.log(info.ccd_data.binarray);return;
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

     //根据插槽数目的值，在插槽序号的下拉框中显示相应数目的插槽 并显示各槽的滤光片类型
     /*1、首先获取插槽数目的值*/
     var filterNum = $('#filterNum'); //插槽数目这个input框
     /*2、插槽序号下拉框 点击事件*/
     var filterSlot = $('#filterSlot'); //插槽序号 下拉框
     filterSlot.click(function () {
        //判断是否输入了插槽数目
        var slotNum = $.trim( filterNum.val() );
        if ( !$.isNumeric(slotNum) || slotNum < 1)
        {
            layer.alert('请先输入插槽数目的值');return;
        }

        /*根据插槽数目，显示相应数目的槽*/
        var slotHtml = '<option value="0">选择插槽</option>';
        for (var slot = 1; slot <= slotNum; slot++)
        {
            slotHtml += '<option value="' + slot + '">插槽' + slot + '</option>';
        }
        $(this).html(slotHtml);
     })/*2、插槽序号下拉框 点击事件 结束*/
     
     /*3、插槽序号下拉框 change事件*/
     filterSlot.change(function () {
        if ( $(this).val() != 0)
        {
            layer.alert('change');
            //此方法 未完 待续
        }
     })
     /*插槽序号下拉框 change事件 结束*/
     //根据插槽数目的值，在插槽序号的下拉框中显示相应数目的插槽 并显示各槽的滤光片类型 结束

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
    var axis3Acceleration = $('#gimbalMaxAxis3Acceleration'); //转台 轴3最加速度
    var axis1ParkPosition = $('#gimbalAxis1ParkPosition'); //转台 轴1复位位置
    var axis2ParkPosition = $('#gimbalAxis2ParkPosition'); //转台 轴2复位位置
    var axis3ParkPosition = $('#gimbalAxis3ParkPosition'); //转台 轴2复位位置
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
    
    //无第3轴时 禁用：轴3最大速度、轴3最大加速度、轴3复位位置gimbalHaveAxis3
    axis3_No.click(function () {
        axis3_maxSpeed.prop('disabled', true);
        axis3_maxAccel.prop('disabled', true);
        axis3_ParkPos.prop('disabled', true);
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
        var gimbalRadio = gimbalForm.find('input[type="radio"]:checked'); //获取被点击的单选框
        //console.log(gimbalRadio.length);return;
        //若漏过某个选项，则提示用户，不提交表单
        if ( gimbalRadio.length < 20)
        {
            layer.alert('您遗漏了单选项固定属性!'); return;
        }
        //检查望远镜下拉选择框 是否选择了某望远镜
        var atId = atNo.val();
        if ( atId == 0)
        {//未选择某个望远镜
            layer.alert('请选择您要配置的望远镜!');return;
        }
        var gimbalData = new FormData(gimbalForm[0]);
        gimbalData.append('teleid', atId); //将某望远镜的id 加入表单数据中

        $.ajax({
            type: 'post',
            url: 'gimbal_config',
            data : gimbalData,
            processData : false,
            contentType : false,
            success:  function (info) {
                if ( info.indexOf('{') == -1 ) //info 不是json数据
                {
                    layer.alert(info);
                    if (info.indexOf('登录') !== -1)
                    {
                    	location.href = '/';
                    }
                }else{//解析 处理 json
                    var info = $.parseJSON(info);
                    layer.alert(info.msg);
                    gimbalFile.html(info.attr_modify);
                    //在页面显示已上传的文件名
                    if ( info.file ) //有已上传的文件信息
                    {
                        show_file (gimbalFile, info.file);
                    }else{
                        gimbalFile.html('');
                    }
                }//解析 处理 json 结束

             },
             error:  function () {
	              layer.alert('网络异常,请重新提交');
             },
        })
    });//转台 提交按钮 js事件 结束
    //提交转台配置 之js事件 结束////////////////////////////

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
        data.maxaxis1speed !== '' && maxAxis1Speed.val(data.maxaxis1speed);
        data.maxaxis2speed !== '' && maxAxis2Speed.val(data.maxaxis2speed);
        data.maxaxis3speed !== '' && maxAxis3Speed.val(data.maxaxis3speed);
        data.maxaxis1acceleration !== '' && axis1Acceleration.val(data.maxaxis1acceleration);
        data.maxaxis2acceleration !== '' && axis2Acceleration.val(data.maxaxis2acceleration);
        data.maxaxis3acceleration !== '' && axis3Acceleration.val(data.maxaxis3acceleration);
        data.axis1parkposition !== '' && axis1ParkPosition.val(data.axis1parkposition);
        data.axis2parkposition !== '' && axis2ParkPosition.val(data.axis2parkposition);
        data.axis3parkposition !== '' && axis3ParkPosition.val(data.axis3parkposition);
        data.haveaxis3 == '1' ? axis3_have.click() : axis3_No.click();
        data.haveaxis5 == '1' ? haveAxis5.click() : haveAxis5_1.click();
        data.minelevation !== '' && gimbalMinElevation.val(data.minelevation);
        data.numtemperaturesensor !== '' && numTemperatureSensor.val(data.numtemperaturesensor);
        data.numhumiditysensor !== '' && numHumiditySensor.val(data.numhumiditysensor);
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
        data.attrversion !== '' && gimbalAttrVersion.val(data.attrversion);
        data.attrmodifytime !== undefined && gimbalAttrModifyTime.html(data.attrmodifytime);
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
    /*ccd-No1 表单元素获取 结束*/
    
    /*ccd 提交按钮 点击事件*/
    ccdBtn_1.click(function () {
        var ccd_1_Radio = ccd_1Form.find('input[type="radio"]:checked'); //获取被点击的单选框
        //console.log(gimbalRadio.length);return;
        //若漏过某个选项，则提示用户，不提交表单
        if ( ccd_1_Radio.length < 23)
        {
            layer.alert('您遗漏了单选项固定属性!'); return;
        }
        //检查望远镜下拉选择框 是否选择了某望远镜
        var atId = atNo.val();
        if ( atId == 0)
        {//未选择某个望远镜
            layer.alert('请选择您要配置的望远镜!');return;
        }
        var ccd_1_Data = new FormData(ccd_1Form[0]);
        ccd_1_Data.append('teleid', atId); //将某望远镜的id 加入表单数据中
        ccd_1_Data.append('ccdno', '1'); //将此望远镜的序号 加入表单数据中

        $.ajax({
            type: 'post',
            url: 'ccd_config',
            data : ccd_1_Data,
            processData : false,
            contentType : false,
            success:  function (info) {
                if ( info.indexOf('{') == -1 ) //info 不是json数据
                {
                    layer.alert(info);
                    if (info.indexOf('登录') !== -1)
                    {
                    	location.href = '/';
                    }
                }else{//解析 处理 json
                    var info = $.parseJSON(info);
                    layer.alert(info.msg);
                    ccdTeleId.html(info.atname);
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
	              layer.alert('网络异常,请重新提交');
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
        data.xpixel !== '' && ccdXpixel.val(data.xpixel);
        data.ypixel !== '' && ccdYpixel.val(data.ypixel);
        data.xpixelsize !== '' && ccdXpixelSize.val(data.xpixelsize);
        data.ypixelsize !== '' && ccdYpixelSize.val(data.ypixelsize);
        data.sensorname !== '' && ccdSensorName.val(data.sensorname);
        data.imagebits === undefined ? ccdImageBits.val('0') : ccdImageBits.val(data.imagebits);
        data.coolermode === undefined ? ccdCoolerMode.val('0') : ccdCoolerMode.val(data.coolermode);
        data.lowcoolert !== '' && ccdLowCoolerT.val(data.lowcoolert);
        data.maxexposuretime !== '' && ccdMaxExposureTime.val(data.maxexposuretime);
        data.minexposuretime !== '' && ccdMinExposureTime.val(data.minexposuretime);
        data.exposuretimeration !== '' && ccdExposureTimeRation.val(data.exposuretimeration);
        data.fullwelldepth !== '' && ccdFullWellDepth.val(data.fullwelldepth);
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
       data.emmaxvalue !== '' && ccdEmMaxValue.val(data.emmaxvalue);
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
       data.attrversion !== '' && ccdAttrVersion.val(data.attrversion);
       data.attrmodifytime !== undefined && ccdAttrModifyTime.html(data.attrmodifytime);
    }/*显示ccd-No1配置数据 结束*/       

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

   

