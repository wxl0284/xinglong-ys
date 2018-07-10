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

    /************* vue 开始 *************/
    var vm = new Vue ({ //vue 开始
        el: '#all',
        data: {
            // show_dev_form:[],//各设备配置表单是否被选中
            // ccd_form_num:'1',//ccd配置表单的数量
            //at:'0',//选择望远镜下拉框中的val,即望远镜的主键id
            show_dev_form: {//控制是否显示各子设备的表单
                teleid: '0',//选择望远镜下拉框中的val,即望远镜的主键id
                show_gimbal:false, //控制显示转台的配置表单
                show_ccd:false, //控制显示ccd的配置表单
                ccd_form_num:'1',//ccd配置表单的数量
                show_focus:false, //控制显示调焦器的配置表单
                show_filter:false, //控制显示滤光片转轮的配置表单
                show_sDome:false, //控制显示随动圆顶的配置表单
                show_oDome:false, //控制显示全开圆顶的配置表单
                show_guide:false, //控制显示导星镜的配置表单
            },//show_dev_form 结束
            confOption: {BinArray:['']},
            test:'',
            gimbal_config: {
                type: '0', focustype: '0', focusratio: '0', 
            }, //转台的配置信息
            gimbal_file: {}, //转台上传的文件
            ccd_config: {
                type: '0', imagebits: '0', coolermode: '0', gainnumber: '0', shuttertype: '0',
                binarray: '0', 
            }, //ccd的配置信息
            ccd_readoutspeed: [],
            ccd_file: {}, //ccd上传的文件
        },//data 结束
        computed: {
            final_binArray: function (){
                var temp = this.confOption.BinArray[0];
                return temp.replace('[', '').replace(']', '').split(' '); //即：[ "1", "2", "3", "4" ]
            },
        },//computed 结束
        methods: {
            ff:function (){
                //console.log(this.show_dev_form);
                //this.show_dev_form.push('show_gimbal');
                console.log(this.ccd_config.readoutspeed);
            },//ff() 结束
            select_at:function (){
                var index = layer.load(1); //显示加载图标
                var that = this; //存储vue实例

                if( that.show_dev_form.teleid !== '0') //执行ajax请求
                {
                    $.ajax({
                        url: '/config',
                        type: 'post',
                        data: {id: that.show_dev_form.teleid,},   //将望远镜id发送给后端
                        success:  function (info) {
                            if ( info.indexOf('{') == -1 )  //不是json, 返回信息提示 给用户
                            {
                                layer.close(index);  //关闭加载提示
                                that.show_dev_form.teleid = 0;  //将望远镜选择下拉框置为初始值
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
                                var info = $.parseJSON(info); //根据json数据 显示配置项
                                //console.log(info.confOption);return;
                                that.confOption = info.confOption; //将19个配置项数据赋给
                                
                                if (info.gimbal_data) //在页面显示转台的配置数据
                                {
                                    that.show_dev_form.show_gimbal = true; //显示gimbal配置表单
                                    if ( !info.gimbal_data.type ) info.gimbal_data.type = '0';
                                    if ( !info.gimbal_data.focustype ) info.gimbal_data.focustype = '0';
                                    if ( !info.gimbal_data.focusratio ) info.gimbal_data.focusratio = '0';

                                    that.gimbal_config = info.gimbal_data;

                                    if (info.gimbal_file)
                                    {
                                        that.gimbal_file = info.gimbal_file;
                                    }
                                }//显示转台配置信息  结束

                                if ( info.ccd_data.ccd_num > 0 ) //在页面显示ccd的配置数据
                                {   //console.log(info.ccd_file); return;
                                    that.show_dev_form.show_ccd = true; //显示gimbal配置表单
                                    if ( !info.ccd_data[0].type ) info.ccd_data[0].type = '0';
                                    if ( !info.ccd_data[0].imagebits ) info.ccd_data[0].imagebits = '0';
                                    if ( !info.ccd_data[0].coolermode ) info.ccd_data[0].coolermode = '0';
                                    if ( !info.ccd_data[0].gainnumber ) info.ccd_data[0].gainnumber = '0';
                                    if ( !info.ccd_data[0].shuttertype ) info.ccd_data[0].shuttertype = '0';
                                    if ( !info.ccd_data[0].binarray ) info.ccd_data[0].binarray = '0';
                        
                                    that.ccd_config = info.ccd_data[0]; //将第一个ccd中的配置数据赋值给ccd_config
                                    
                                    if (info.ccd_file)
                                    {
                                        if ( info.ccd_file[1] != 0 ) that.ccd_file = info.ccd_file[1];
                                    }
                        
                                }//显示ccd 配置信息  结束

                                layer.close(index);  //关闭加载图标
                            }
                        },//success 结束
                        error:  function () {
                            layer.alert('网络异常,请再次选择!', {shade:false, closeBtn:0});
                            layer.close(index);  //关闭加载提示
                        },
                    });//ajax 结束
                }//执行ajax请求 结束
                else{//不执行ajax
                    layer.close(index);  //关闭加载图标
                }
            },//select_at() 结束
            check_focuslength: function (tip, v){//验证焦距
                var msg = '';
                var patn = /^\[\d+\.?\d? \d+\.?\d?\]$/;
                if ( !patn.test(v) )
                {
                    msg = '焦距输入有误';
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs.focus_L);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_focuslength() 结束
            check_axisSpeed: function (tip, v, e, n){//验证 轴 最大速度
                var msg = '';

                if ( !$.isNumeric(v) || v > 30 || v <= 0 )
                {
                    switch (n) {
                        case 1:
                            msg = '轴1最大速度输入有误';
                            break;
                        case 2:
                            msg = '轴2最大速度输入有误';
                            break;
                        case 3:
                            msg = '轴3最大速度输入有误';
                            break;
                    }                   
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_axisSpeed() 结束
            check_axisAcce: function (tip, v, e, n){//验证 轴 最大加速度
                var msg = '';

                if ( !$.isNumeric(v) || v > 5 || v <= 0 )
                {
                    switch (n) {
                        case 1:
                            msg = '轴1最大加速度输入有误';
                            break;
                        case 2:
                            msg = '轴2最大加速度输入有误';
                            break;
                        case 3:
                            msg = '轴3最大加速度输入有误';
                            break;
                    }                   
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_axisAcce() 结束
            check_axisPark: function (tip, v, e, n){//验证 轴 复位位置
                var msg = '';

                if ( !$.isNumeric(v) || v > 360 || v < 0 )
                {
                    switch (n) {
                        case 1:
                            msg = '轴1复位位置输入有误';
                            break;
                        case 2:
                            msg = '轴2复位位置输入有误';
                            break;
                        case 3:
                            msg = '轴3复位位置输入有误';
                            break;
                    }                   
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_axisPark() 结束
            check_minelevation: function (tip, v, e) {
                var msg = '';

                if ( !$.isNumeric(v) || v <= 10 )
                {       
                    msg = '俯仰最低值输入有误'; 
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_minelevation() 结束
            check_Num: function (tip, v, e, n) {//验证 温度、湿度传感器数目
                var msg = '';
                var patn = /^\d+$/;
                if ( !patn.test(v) || v < 1 )
                {    
                    if ( n == 1 )
                    {
                       msg = '温度传感器数目输入有误';  
                    }else if ( n == 2 )
                    {
                        msg = '湿度传感器数目输入有误';
                    }                    
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_Num() 结束
            check_version: function (tip, v, e, n) {//验证 属性版本号
                var msg = '';
                var patn = /^\d+$/;
                if ( !patn.test(v) || v < 1 )
                {    
                    switch (n) {
                        case 1:
                            msg = '转台版本号输入有误';
                            break;
                        case 2:
                            msg = 'ccd版本号输入有误';
                            break;
                        // case 3:
                        //     msg = '轴3复位位置输入有误';
                        //     break;
                    }                     
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_version() 结束
            gimbal_sbmt:function (){//提交转台配置信息
                var that = this; //存储vue的实例

                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:false,closeBtn:0});return;
                }

                var msg = '';
                if ( this.gimbal_config.type === '0' )  msg += '类型未选择<br>';

                if ( this.gimbal_config.focustype === '0' )  msg += '焦点类型未选择<br>';

                if ( this.gimbal_config.focusratio === '0')  msg += '焦比未选择<br>';

                msg += this.check_focuslength(false, this.gimbal_config.focuslength);

                msg += this.check_axisSpeed(false, this.gimbal_config.maxaxis1speed, this.$refs.axis1speed, 1);

                msg += this.check_axisSpeed(false, this.gimbal_config.maxaxis2speed, this.$refs.axis2speed, 2);

                if (this.gimbal_config.haveaxis3 == 1)
                {
                    msg += this.check_axisSpeed (false,this.gimbal_config.maxaxis3speed, this.$refs.axis3speed, 3);
                }

                msg += this.check_axisAcce(false, this.gimbal_config.maxaxis1acceleration, this.$refs.axis1_acce, 1);

                msg += this.check_axisAcce(false, this.gimbal_config.maxaxis2acceleration, this.$refs.axis2_acce, 2);

                if (this.gimbal_config.haveaxis3 == 1)
                {
                    msg += this.check_axisAcce (false,this.gimbal_config.maxaxis3acceleration, this.$refs.axis3_acce, 3);
                }
                
                msg += this.check_axisPark(false, this.gimbal_config.axis1parkposition, this.$refs.axis1Park, 1);
                
                msg += this.check_axisPark(false, this.gimbal_config.axis2parkposition, this.$refs.axis2Park, 2);

                if (this.gimbal_config.haveaxis3 == 1)
                {
                    msg += this.check_axisPark (false,this.gimbal_config.axis3parkposition, this.$refs.axis3Park, 3);
                }
                
                msg += this.check_minelevation(false, this.gimbal_config.minelevation, this.$refs.minElev);
                
                msg += this.check_Num(false, this.gimbal_config.numtemperaturesensor, this.$refs.tempera_num, 1);
                
                msg += this.check_Num(false, this.gimbal_config.numhumiditysensor, this.$refs.humid_num, 2);

                msg += this.check_version(false, this.gimbal_config.attrversion, this.$refs.gimbal_version, 1);

                if ( this.gimbal_config.haveaxis3 === undefined ) msg += '是否有第3轴未选择<br>';
                if ( this.gimbal_config.haveaxis5 === undefined ) msg += '是否有镜盖未选择<br>';
                if ( this.gimbal_config.canconnect === undefined ) msg += '是否支持连接未选择<br>';
                if ( this.gimbal_config.canfindhome === undefined ) msg += '是否支持找零未选择<br>';
                if ( this.gimbal_config.cantrackstar === undefined ) msg += '是否支持跟踪恒星未选择<br>';
                if ( this.gimbal_config.cansetobjectname === undefined ) msg += '是否支持设置目标名称未选择<br>';
                if ( this.gimbal_config.canslewazel === undefined ) msg += '是否支持指向固定位置未选择<br>';
                if ( this.gimbal_config.canslewderotator === undefined ) msg += '是否支持轴3指向固定位置未选择<br>';
                if ( this.gimbal_config.canconfigderotator === undefined ) msg += '是否支持设置轴3工作模式未选择<br>';
                if ( this.gimbal_config.canstop === undefined ) msg += '是否支持停止未选择<br>';
                if ( this.gimbal_config.cansettrackspeed === undefined ) msg += '是否支持设置跟踪速度未选择<br>';
                if ( this.gimbal_config.canpark === undefined ) msg += '是否支持复位未选择<br>';
                if ( this.gimbal_config.canfixedmove === undefined ) msg += '是否支持恒速运动未选择<br>';
                if ( this.gimbal_config.canpositioncorrect === undefined ) msg += '是否支持位置修正未选择<br>';
                if ( this.gimbal_config.cancoveroperation === undefined ) msg += '是否支持镜盖操作未选择<br>';
                if ( this.gimbal_config.canfocusoperation === undefined ) msg += '是否支持焦点切换镜未选择<br>';
                if ( this.gimbal_config.canemergencystop === undefined ) msg += '是否支持急停未选择<br>';
                if ( this.gimbal_config.cansavesyncdata === undefined ) msg += '是否支持保存同步数据未选择<br>';
                if ( this.gimbal_config.cantracksatellite === undefined ) msg += '是否支持跟踪卫星未选择<br>';
                if ( this.gimbal_config.canconfigproperty === undefined ) msg += '是否支持属性设置未选择<br>';
                
                if ( msg !== '' )
                {
                    layer.alert(msg, {shade:false, closeBtn:0});return;
                }
                var postData = new FormData (this.$refs.gimbal);
                postData.append('teleid', this.show_dev_form.teleid); //将望远镜Id 提交上去
                $.ajax({
                    type: 'post',
                    url: 'gimbal_config',
                    data : postData,
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
        
                            that.gimbal_config.attrmodifytime = info.attrmodifytime; //显示属性更新时间             

                            if (info.file) that.gimbal_file = info.file;
                        }//解析 处理 json 结束
                     },/*success方法结束 */
                     error:  function () {
                          layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0,});
                     }
                })/*ajax 结束*/
            },//gimbal_sbmt() 结束
            check_devId:function (tip, v, e, dev) {//验证各设备 id
                var msg = '';
                var patn = /^\d{5}$/;
                if ( !patn.test(v) )
                {
                    switch (dev) {
                        case 'ccd':
                            msg = 'ccd之id输入有误';break;
                        case 'filter':
                            msg = '滤光片转轮id输入有误';break;
                        case 'focus':
                            msg = '调焦器id输入有误';break;
                        case 'sDome':
                            msg = '随动圆顶id输入有误';break;
                        case 'oDome':
                            msg = '全开圆顶id输入有误';break;
                        case 'guide':
                            msg = '导星镜id输入有误';break;
                        default:
                            break;
                    } 
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_devId() 结束
            check_devName:function (tip, v, e, dev) {//验证各设备 名称
                var msg = '';
            
                if ( v.length < 1 )
                {
                    switch (dev) {
                        case 'ccd':
                            msg = '请输入ccd名称';break;
                        case 'filter':
                            msg = '请输入滤光片转轮名称';break;
                        case 'focus':
                            msg = '请输入调焦器名称';break;
                        case 'sDome':
                            msg = '请输入随动圆顶名称';break;
                        case 'oDome':
                            msg = '请输入全开圆顶名称';break;
                        case 'guide':
                            msg = '请输入导星镜名称';break;
                        default:
                            break;
                    } 
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_devName() 结束
            ccd_sbmt:function () {
                var that = this; //存储vue的实例
                var msg = '';
                //console.log(this.ccd_config.type);
                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:false,closeBtn:0});return;
                }
                if ( this.ccd_config.type == '0' ) msg += '探测器类型为选择<br>' 
            },//ccd_sbmt() 结束
        },//methods 结束
    });//vue 结束////////////////////
 
    
     /* 获取所有要填入数据的元素对象*/
     var gimbalFocustype = $('#gimbalFocustype'); //转台 焦点类型
     var gimbalFocusratio = $('#gimbalFocusratio'); //转台 焦比
     var ccdImageBits = $('#ccdImageBits'); //ccd-No1 图像位数
     var ccd_2ImageBits = $('#ccd_2ImageBits'); //ccd-No2 图像位数
     var ccd_3ImageBits = $('#ccd_3ImageBits'); //ccd-No3 图像位数
     var ccd_4ImageBits = $('#ccd_4ImageBits'); //ccd-No4 图像位数
     var ccd_5ImageBits = $('#ccd_5ImageBits'); //ccd-No4 图像位数
     var ccdCoolerMode = $('#ccdCoolerMode'); //ccd-No1 制冷方式
     var ccd_2CoolerMode = $('#ccd_2CoolerMode'); //ccd-No2 制冷方式
     var ccd_3CoolerMode = $('#ccd_3CoolerMode'); //ccd-No3 制冷方式
     var ccd_4CoolerMode = $('#ccd_4CoolerMode'); //ccd-No4 制冷方式
     var ccd_5CoolerMode = $('#ccd_5CoolerMode'); //ccd-No5 制冷方式
     var ccdReadoutSpeed = $('#ccdReadoutSpeed'); //ccd-No1 读出速度模式
     var ccd_2ReadoutSpeed = $('#ccd_2ReadoutSpeed'); //ccd-No2 读出速度模式
     var ccd_3ReadoutSpeed = $('#ccd_3ReadoutSpeed'); //ccd-No3 读出速度模式
     var ccd_4ReadoutSpeed = $('#ccd_4ReadoutSpeed'); //ccd-No4 读出速度模式
     var ccd_5ReadoutSpeed = $('#ccd_5ReadoutSpeed'); //ccd-No5 读出速度模式
     var ccdreadoutMode = $('#ccd_1readoutMode'); //ccd-No1 读出模式
     var ccd_2readoutMode = $('#ccd_2readoutMode'); //ccd-No2 读出模式
     var ccd_3readoutMode = $('#ccd_3readoutMode'); //ccd-No3 读出模式
     var ccd_4readoutMode = $('#ccd_4readoutMode'); //ccd-No4 读出模式
     var ccd_5readoutMode = $('#ccd_5readoutMode'); //ccd-No5 读出模式
     var ccdTransferSpeed = $('#ccdTransferSpeed'); //ccd-No1 转移速度模式
     var ccd_2TransferSpeed = $('#ccd_2TransferSpeed'); //ccd-No2 转移速度模式
     var ccd_3TransferSpeed = $('#ccd_3TransferSpeed'); //ccd-No3 转移速度模式
     var ccd_4TransferSpeed = $('#ccd_4TransferSpeed'); //ccd-No4 转移速度模式
     var ccd_5TransferSpeed = $('#ccd_5TransferSpeed'); //ccd-No5 转移速度模式
     var ccd_1gainmode = $('#ccd_1gainmode'); //ccd-No1 增益模式
     var ccd_2gainmode = $('#ccd_2gainmode'); //ccd-No2 增益模式
     var ccd_3gainmode = $('#ccd_3gainmode'); //ccd-No3 增益模式
     var ccd_4gainmode = $('#ccd_4gainmode'); //ccd-No4 增益模式
     var ccd_5gainmode = $('#ccd_5gainmode'); //ccd-No5 增益模式
     var ccd_1GainNumber = $('#ccd_1GainNumber'); //ccd-No1 增益档位
     var ccd_2GainNumber = $('#ccd_2GainNumber'); //ccd-No2 增益档位
     var ccd_3GainNumber = $('#ccd_3GainNumber'); //ccd-No3 增益档位
     var ccd_4GainNumber = $('#ccd_4GainNumber'); //ccd-No4 增益档位
     var ccd_5GainNumber = $('#ccd_5GainNumber'); //ccd-No5 增益档位
     var ccdShutterType = $('#ccdShutterType'); //ccd-No1 快门类型
     var ccd_2ShutterType = $('#ccd_2ShutterType'); //ccd-No2 快门类型
     var ccd_3ShutterType = $('#ccd_3ShutterType'); //ccd-No3 快门类型
     var ccd_4ShutterType = $('#ccd_4ShutterType'); //ccd-No4 快门类型
     var ccd_5ShutterType = $('#ccd_5ShutterType'); //ccd-No5 快门类型
     var ccd_1ShutterMode = $('#ccd_1ShutterMode'); //ccd-No1 快门模式
     var ccd_2ShutterMode = $('#ccd_2ShutterMode'); //ccd-No2 快门模式
     var ccd_3ShutterMode = $('#ccd_3ShutterMode'); //ccd-No3 快门模式
     var ccd_4ShutterMode = $('#ccd_4ShutterMode'); //ccd-No4 快门模式
     var ccd_5ShutterMode = $('#ccd_5ShutterMode'); //ccd-No5 快门模式
     var ccd_1BinArray = $('#ccd_1BinArray'); //ccd-No1 Bin
     var ccd_2BinArray = $('#ccd_2BinArray'); //ccd-No2 Bin
     var ccd_3BinArray = $('#ccd_3BinArray'); //ccd-No3 Bin
     var ccd_4BinArray = $('#ccd_4BinArray'); //ccd-No4 Bin
     var ccd_5BinArray = $('#ccd_5BinArray'); //ccd-No5 Bin
     var ccd_1InterfaceType = $('#ccd_1InterfaceType'); //ccd-No1 接口类型
     var ccd_2InterfaceType = $('#ccd_2InterfaceType'); //ccd-No2 接口类型
     var ccd_3InterfaceType = $('#ccd_3InterfaceType'); //ccd-No3 接口类型
     var ccd_4InterfaceType = $('#ccd_4InterfaceType'); //ccd-No4 接口类型
     var ccd_5InterfaceType = $('#ccd_5InterfaceType'); //ccd-No5 接口类型
     var ccd_1ExposeTriggerMode = $('#ccd_1ExposeTriggerMode'); //ccd-No1 曝光触发模式
     var ccd_2ExposeTriggerMode = $('#ccd_2ExposeTriggerMode'); //ccd-No2 曝光触发模式
     var ccd_3ExposeTriggerMode = $('#ccd_3ExposeTriggerMode'); //ccd-No3 曝光触发模式
     var ccd_4ExposeTriggerMode = $('#ccd_4ExposeTriggerMode'); //ccd-No4 曝光触发模式
     var ccd_5ExposeTriggerMode = $('#ccd_5ExposeTriggerMode'); //ccd-No5 曝光触发模式
     var filterSystem = $('#filterSystem'); //滤光片类型
     var filterShape = $('#filterShape'); //滤光片形状
     var sDomeType = $('#sDomeType'); //随动圆顶类型
     var oDomeType = $('#oDomeType'); //全开圆顶类型
     var guideScopeOpticalStructure = $('#guideScopeOpticalStructure'); //导星镜 焦点类型
     /* 获取所有要填入数据的元素对象 结束*/

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



    /*ccd-No1 表单元素获取*/
    /*如下：其他所有的ccd表单*/
    var ccdBtn_2 = $('#ccdBtn-2'); //ccd-No2提交按钮
    var ccd_2Form = $('#ccd-2'); //ccd-No2表单
    var ccd_2_File = $('#ccd_2_File'); //ccd2相关文件区
    var ccd_2_TeleId = $('#ccd_2_TeleId'); //ccd2隶属望远镜
    var ccd_2AttrModifyTime = $('#ccd_2AttrModifyTime'); //ccd2属性更新时间

    var ccdBtn_3 = $('#ccdBtn-3'); //ccd-No3提交按钮
    var ccd_3Form = $('#ccd-3'); //ccd-No3表单
    var ccd_3_File = $('#ccd_3_File'); //ccd3相关文件区
    var ccd_3_TeleId = $('#ccd_3_TeleId'); //ccd3隶属望远镜
    var ccd_3AttrModifyTime = $('#ccd_3AttrModifyTime'); //ccd3属性更新时间

    var ccdBtn_4 = $('#ccdBtn-4'); //ccd-No4提交按钮
    var ccd_4Form = $('#ccd-4'); //ccd-No4表单
    var ccd_4_File = $('#ccd_4_File'); //ccd4相关文件区
    var ccd_4_TeleId = $('#ccd_4_TeleId'); //ccd4隶属望远镜
    var ccd_4AttrModifyTime = $('#ccd_4AttrModifyTime'); //ccd4属性更新时间

    var ccdBtn_5 = $('#ccdBtn-5'); //ccd-No5提交按钮
    var ccd_5Form = $('#ccd-5'); //ccd-No5表单
    var ccd_5_File = $('#ccd_5_File'); //ccd5相关文件区
    var ccd_5_TeleId = $('#ccd_5_TeleId'); //ccd5隶属望远镜
    var ccd_5AttrModifyTime = $('#ccd_5AttrModifyTime'); //ccd5属性更新时间
    /*以上：其他所有的ccd表单*/
    var ccdBtn_1 = $('#ccdBtn-1'); //ccd-No1提交按钮
    var ccd_1Form = $('#ccd-1'); //ccd-No1表单
    var ccdFile = $('#ccdFile'); //ccd-No1相关文件区
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
    
    /*ccd-No1 提交按钮 点击事件*/
//     ccdBtn_1.click(function () {
//         //检查望远镜下拉选择框 是否选择了某望远镜
//         var atId = atNo.val();
//         //检查是否选择了ccd的数量
//         var ccd_Num = ccd_num.val();

//         if ( atId == 0)
//         {//未选择某个望远镜
//             layer.alert('请选择您要配置的望远镜!', {shade:false, closeBtn:0});return;
//         }

//         if ( ccd_Num == 0)
//         {//未选择ccd的数量
//             layer.alert('请选择您ccd的数量!', {shade:false, closeBtn:0});return;
//         }

//         var ccd_1_Data = new FormData(ccd_1Form[0]);
//         ccd_1_Data.append('teleid', atId); //将某望远镜的id 加入表单数据中
//         ccd_1_Data.append('ccdno', '1'); //将此ccd的序号 加入表单数据中
//         ccd_1_Data.append('ccd_num', ccd_Num); //将ccd的数量 加入表单数据中
        
//         //验证文本框类型、下拉框、复选框、单选框配置项 是否都已选择 
//          if ( !ccd_select_valid (ccd_1_Data) )
//          {
//              return;
//          }//验证文本框类型、下拉框、复选框、单选框配置项  是否都已选择 结束

//         $.ajax({
//             type: 'post',
//             url: 'ccd_config',
//             data : ccd_1_Data,
//             processData : false,
//             contentType : false,
//             success:  function (info) {
//                 if ( info.indexOf('{') == -1 ) //info 不是json数据
//                 {
//                     layer.alert(info, {
//                         shade:false,
//                         closeBtn:0,
//                         yes:function (n){
//                             layer.close(n);
//                             if (info.indexOf('登录') !== -1)
//                             {
//                                 location.href = '/';
//                             }
//                         },
//                     });
//                 }else{//解析 处理 json
//                     var info = $.parseJSON(info);
//                     layer.alert(info.msg, {shade:false, closeBtn:0});
//                     //console.log(info);return;
//                     ccdAttrModifyTime.html(info.attrmodifytime); //显示属性更新时间
//                     //在页面显示已上传的文件名
//                     if ( info.file ) //有已上传的文件信息
//                     {
//                         show_file (ccdFile, info.file);
//                     }else{
//                         ccdFile.html('');
//                     }
//                 }//解析 处理 json 结束
//              },
//              error:  function () {
// 	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
//              },
//         })
//     });/*ccd-No1 提交按钮 点击事件 结束*/

    /*显示ccd-No1配置数据*/
    
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
                    ccd_errMsg += $(this).data('info') + '<br>';
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

        var ccd_1ReadoutSpeed_check_num = ccdReadoutSpeed.children('input:checkbox:checked').length; //读出速度模式 被选中的数量
        if ( ccd_1ReadoutSpeed_check_num < 1)
        {
            ccd_errMsg += '读出速度模式未选择!<br>';
        }

        var ccd_1TransferSpeed_check_num = ccdTransferSpeed.children('input:checkbox:checked').length; //转移速度模式 被选中的数量
        if ( ccd_1TransferSpeed_check_num < 1)
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
        var patn = /^[0-9]{5}$/;
        var err = 0;

        if ( !patn.test(v) )
        {
            err = 1;
            that.data('info', 'ccd Id应为5位数字!');
            layer.tips('ccd Id应为5位数字!', that, {tipsMore: true});
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
            that.data('info', 'ccd名称不能为空!');
            layer.tips('ccd名称不能为空!', that, {tipsMore: true});
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
            that.data('info', 'x像素输入有误!');
            layer.tips('x像素格式输入有误!', that, {tipsMore: true});
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
            that.data('info', 'y像素输入有误!');
            layer.tips('y像素格式输入有误!', that, {tipsMore: true});
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
            that.data('info', 'x像元输入有误!');
            layer.tips('x像元格式输入有误!', that, {tipsMore: true});
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
            that.data('info', 'y像元输入有误!');
            layer.tips('y像元格式输入有误!', that, {tipsMore: true});
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
            that.data('info', '传感器名称输入有误!');
            layer.tips('传感器名称输入有误!', that, {tipsMore: true});
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
            that.data('info', '最低制冷温度输入有误!');
            layer.tips('最低制冷温度输入有误!', that, {tipsMore: true});
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
            that.data('info', '最大曝光时间输入有误!');
            layer.tips('最大曝光时间输入有误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 最大曝光时间 结束

    ccdMinExposureTime.blur(function () {//验证 最小曝光时间
        var that = $(this);
        var v = $.trim(that.val());
        var v1 = $.trim(ccdMaxExposureTime.val());
        var err = 0;

        if ( !$.isNumeric(v) || v > v1 )
        {
            err = 1;
            that.data('info', '最小曝光时间输入有误!');
            layer.tips('最小曝光时间输入有误!', that, {tipsMore: true});
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
            that.data('info', '曝光时间分辨率输入有误!');
            layer.tips('曝光时间分辨率输入有误!', that, {tipsMore: true});
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
            that.data('info', '满阱电荷输入有误!');
            layer.tips('满阱电荷输入有误!!', that, {tipsMore: true});
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
            that.data('info', '最大EM输入有误!');
            layer.tips('最大EM格式输入有误!', that, {tipsMore: true});
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
            that.data('info', '最小EM输入有误!');
            layer.tips('最小EM格式输入有误!', that, {tipsMore: true});
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
            that.data('info', '属性版本号输入有误!');
            layer.tips('属性版本号输入有误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 属性版本号 结束
    /****************ccd-No1 验证结束 ****************/

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
        //filterId.val(data.filterid);
        filterName.val(data.name);
        filterTeleId.html(data.atname);
        filterNum.val(data.numberoffilter);
        data.numberoffilter !== undefined && filterNum.blur(); //如果插槽数目有值，执行插槽数目input框的blur事件
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
                    filter_errMsg += $(this).data('info') + '<br>';
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
            that.data('info', '滤光片id应为5位数字!');
            layer.tips('滤光片id应为5位数字!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 滤光片id 结束

    filterName.blur(function () {//验证 名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 2 )
        {
            err = 1;
            that.data('info', '转轮名称不能为空!');
            layer.tips('转轮名称不能为空!', that, {tipsMore: true});
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
            that.data('info', '插槽大小输入有误!');
            layer.tips('插槽大小输入有误!', that, {tipsMore: true});
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
            that.data('info', '各滤光片名称输入有误!');
            layer.tips('各滤光片名称输入有误!', that, {tipsMore: true});
        }else{//v值不为空

            if ( v.indexOf('/') === -1 ) //字符串中无'/', 则表明应该有一个插槽
            {
                if ( filterNum_v  != 1 )
                {
                    err = 1;
                    that.data('info', '滤光片名称设置与插槽数目不匹配!');
                    layer.tips('滤光片名称设置与插槽数目不匹配!', that, {tipsMore: true});
                }
            }else{//字符串中有'/', 则表明应该有大于一个插槽
                v_num = v.split ('/').length;
                if ( v_num != filterNum_v )
                {
                    err = 1;
                    that.data('info', '滤光片名称设置与插槽数目不匹配!');
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
            that.data('info', '焦距偏差值输入有误!');
            layer.tips('焦距偏差值输入有误!', that, {tipsMore: true});
        }else{
            v = v.replace('[');
            v = v.replace(']');
            var v_num = v.split(' ').length;

            if ( v_num != filterNum_v )
            {
                err = 1;
                that.data('info', '焦距偏差值与插槽数目不匹配!');
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
            that.data('info', '属性版本号输入有误!');
            layer.tips('属性版本号输入有误!', that, {tipsMore: true});
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
        //sDomeTeleId.html(data.atname);
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
                    sDome_errMsg += $(this).data('info') + '<br>';
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
        var patn = /^[0-9]{5}$/;
        var err = 0;

        if ( !$.isNumeric(v) )
        {
            err = 1;
            that.data('info', '随动圆顶id应为5位数字');
            layer.tips('随动圆顶id应为5位数字!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 随动圆顶id 结束

    sDomeName.blur(function () {//验证 随动圆顶名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 2 )
        {
            err = 1;
            that.data('info', '随动圆顶名称不能为空!');
            layer.tips('随动圆顶名称不能为空!', that, {tipsMore: true});
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
            that.data('info', '最大转动速度输入有误!');
            layer.tips('最大转动速度输入有误!', that, {tipsMore: true});
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
            that.data('info', '尺寸大小输入有误!');
            layer.tips('尺寸大小输入有误!', that, {tipsMore: true});
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
            that.data('info', '属性版本号输入有误!');
            layer.tips('属性版本号输入有误!', that, {tipsMore: true});
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
        //oDomeTeleId.html(data.atname);
        data.type === undefined ? oDomeType.val('0') : oDomeType.val(data.type);
        oDomeDiameter.val(data.diameter);
        data.canopendome == '1' ? oDomeCanOpenDome.click() : oDomeCanOpenDome_1.click();
        data.canconnect == '1' ? oDomeCanConnect.click() : oDomeCanConnect_1.click();
        odomeAttrversion.val(data.attrversion);
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
                    oDome_errMsg += $(this).data('info') + '<br>';
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
        var patn = /^[0-9]{5}$/;
        var err = 0;

        if ( !patn.test(v) )
        {
            err = 1;
            that.data('info', '全开圆顶id应为5位数字!');
            layer.tips('全开圆顶id应为5位数字!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 全开圆顶id 结束

    oDomeName.blur(function () {//验证 全开圆顶名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 2 )
        {
            err = 1;
            that.data('info', '全开圆顶名称不能为空!');
            layer.tips('全开圆顶名称不能为空!', that, {tipsMore: true});
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
            that.data('info', '全开圆顶尺寸输入有误!');
            layer.tips('全开圆顶尺寸输入有误!', that, {tipsMore: true});
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
            that.data('info', '属性版本号输入有误!');
            layer.tips('属性版本号输入有误!', that, {tipsMore: true});
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
        //focusTeleId.html(data.atname);
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
                    focus_errMsg += $(this).data('info') + '<br>';
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
        var patn = /^[0-9]{5}$/;
        var err = 0;

        if ( !patn.test(v) )
        {
            err = 1;
            that.data('info', '调焦器id应为5位数字!');
            layer.tips('调焦器id应为5位数字!', that, {tipsMore: true});
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
            that.data('info', '调焦器名称不能为空!');
            layer.tips('调焦器名称不能为空!', that, {tipsMore: true});
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
            that.data('info', '调焦器最大值输入有误!');
            layer.tips('调焦器最大值输入有误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 调焦器最大值 结束

    focusMinValue.blur(function () {//验证 最小值
        var that = $(this);
        var v = $.trim(that.val());
        var v1 = $.trim(focusMaxValue.val());
        var err = 0;

        if ( !$.isNumeric(v) || v <= 0 || v >= v1*1 )
        {
            err = 1;
            that.data('info', '调焦器最小值输入有误!');
            layer.tips('调焦器最小值输入有误!', that, {tipsMore: true});
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
            that.data('info', '调焦器分辨率输入有误!');
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
            that.data('info', '调焦器最大速度输入有误!');
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
            that.data('info', '调焦器版本号输入有误!');
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
        //guideScopeTeleId.html(data.atname);
        guideScopeAperture.val(data.aperture);
        guideScopeFocusLength.val(data.focuslength);
        data.opticalstructure === undefined ? guideScopeOpticalStructure.val('0') : guideScopeOpticalStructure.val(data.opticalstructure);
        data.hasmirrorcover == '1' ? guideScopeMirrorCover.click() : guideScopeMirrorCover_1.click();
        data.issupportautofocus == '1' ? SupportAutoFocus.click() : SupportAutoFocus_1.click();
        data.canconnect == '1' ? guideScopeCanConnect.click() : guideScopeCanConnect_1.click();
        data.canopencover == '1' ? guideScopeOpenCover.click() : guideScopeOpenCover_1.click();
        data.canenableautofocus == '1' ? guideEnableAutoFocus.click() : guideEnableAutoFocus_1.click();
        guideScopeAttrVersion.val(data.attrversion);
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
                    guide_errMsg += $(this).data('info') + '<br>';
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
        var patn = /^[0-9]{5}$/;
        var err = 0;

        if ( !patn.test(v) )
        {
            err = 1;
            that.data('info', '导星镜id应为5位数字!');
            layer.tips('导星镜id应为5位数字!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 导星镜id 结束

    guideScopeName.blur(function () {//验证 导星镜名称
        var that = $(this);
        var v = $.trim(that.val());
        var err = 0;

        if ( v.length < 2 )
        {
            err = 1;
            that.data('info', '导星镜名称不能为空!');
            layer.tips('导星镜名称不能为空!', that, {tipsMore: true});
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
            that.data('info', '导星镜焦距输入有误!');
            layer.tips('导星镜焦距格式输入有误!', that, {tipsMore: true});
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
            that.data('info', '导星镜口径输入有误!');
            layer.tips('导星镜口径输入有误!', that, {tipsMore: true});
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
            that.data('info', '属性版本号输入有误!');
            layer.tips('属性版本号输入有误!', that, {tipsMore: true});
        }		
        that.data('err', err);
    });//验证 版本号 结束
    /*************导星望远镜 js事件 结束*****************/
})/*jquery 初始化函数 末尾*/