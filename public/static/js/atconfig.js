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
            all_ccd_config:{},//所有ccd的配置
            show_dev_form: {//控制是否显示各子设备的表单
                teleid: '0',//选择望远镜下拉框中的val,即望远镜的主键id
                show_gimbal:false, //控制显示转台的配置表单
                show_ccd:false, //控制显示ccd的配置表单
                ccd_form_num:'1',//ccd的数量
                ccd_no:'1',//ccd序号
                show_focus:false, //控制显示调焦器的配置表单
                show_filter:false, //控制显示滤光片转轮的配置表单
                show_sDome:false, //控制显示随动圆顶的配置表单
                show_oDome:false, //控制显示全开圆顶的配置表单
                show_guide:false, //控制显示导星镜的配置表单
            },//show_dev_form 结束
            gimbal_focus:{//存储转台 各焦点类型一一对应的焦比及焦距
                focustype:[],
                postData:{},//转台焦点类型须提交的数据
            },//gimbal_focus 结束
            confOption: {BinArray:['']},
            ccd_readOutSpeed:{//ccd读出速度
                speed_num:1, //显示的读出速度数量
            },//ccd读出速度 结束
            ccd_transSpeed:{//ccd转移速度
                speed_num:1, //显示的转移速度数量
            },//ccd转移速度 结束
            gimbal_config: {
                ip:'', type: '0', focustype: [], focusratio: '0', focuslength:'', maxaxis1speed:'', maxaxis2speed:'',
                maxaxis1speed:'', maxaxis1acceleration:'', maxaxis2acceleration:'', maxaxis3acceleration:'',
                axis1parkposition:'', axis2parkposition:'', axis3parkposition:'', minelevation:'', numtemperaturesensor:'',
                numhumiditysensor:'', attrversion:''
            }, //转台的配置信息
            gimbal_file: {}, //转台上传的文件
            ccd_config: {
                type: '0', imagebits: '0', coolermode: '0', gainnumber: '0', shuttertype: '0',
                binarray: '',ip:'', ccdid:'', name:'', xpixel:'', ypixel:'', xpixelsize:'', ypixelsize:'',
                sensorname:'', lowcoolert:'', maxexposuretime:'', minexposuretime:'', exposuretimeration:'', 
                fullwelldepth:'', /*readoutspeed:[],*/ readoutmode:[], transferspeed:[], gainmode:[], gainvaluearray:'',
                readoutnoisearray:'', shuttermode:[], interfacetype:[], emmaxvalue:'', exposetriggermode:[],
                emminvalue:'', attrversion:'' 
            }, //ccd的配置信息
            ccd_file: {}, //ccd上传的文件
            filter_slot:{//存储滤光片 各插槽一一对应的滤光片类型-名称-偏差值
                slotData:{},//各插槽的滤光片类型-名称-偏差值
            },//filter_slot 结束
            filter_config: {
                numberoffilter: '', ip:'', filterid:'',name:'', filtersize:'', filtershape:'0', attrversion:'',
                attrmodifytime:''
            },//滤光片的配置信息
            filter_file: {}, //滤光片转轮 上传的文件
        },//data 结束
        computed: {
            final_binArray: function (){
                var temp = this.confOption.BinArray[0];
                return temp.replace('[', '').replace(']', '').split(' '); //即：[ "1", "2", "3", "4" ]
            },
        },//computed 结束
        mounted:function () {

        },//mounted 结束
        methods: {
            tt:function (e) {
                //console.log(this.gimbal_focus.postData);
                //console.log(this.confOption.focustype);
                //layer.tips('haha', e.target);
            },
            show_focus_tr:function (v){
                if ( $.inArray(v, this.gimbal_focus.focustype) !== -1 )
                {
                    return true;
                }else{
                    return false;
                } 
            },//show_focus_tr() 结束
            check_focus_val: function (tip, k, n){//验证转台：焦点类型-焦比-焦距
                var msg = '';
                var patn = /^\[\d+\.?\d? \d+\.?\d?\]$/; //匹配[2.3 5.2]
                var ele = ''; //输入框
                if ( !patn.test(this.gimbal_focus.postData[k].focusratio) )
                {
                    switch (n)
                    {
                        case 1: //焦比
                            msg = this.confOption.focustype[k] + '焦比输入有误';
                            ele = 'focusR' + k;
                            break;
                        case 2: //焦距
                            msg = this.confOption.focustype[k] + '焦距输入有误';
                            ele = 'focusL' + k;
                            break;
                    }
                }
                if ( tip === true && msg !== '' )
                {
                    layer.tips(msg, this.$refs[ele]);
                }
                return msg !== '' ? msg + '<br>' : '';
            },//check_focus_val() 结束
            change_ccd: function (n) {//切换要配置的ccd
                //this.ccd_config = thi;
            },//change_ccd() 结束
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
                                //console.log($.parseJSON(info.gimbal_data.focustype));return;
                                that.confOption = info.confOption; //将14个配置项数据赋给
                                var focustype_num = info.confOption.focustype.length; //转台的焦点类型个数
                                for ( let i = 0; i < focustype_num; i++)
                                {
                                    that.gimbal_focus.postData[i] = {focusratio:"", focuslength:""}; //初始化gimbal_focus.postData
                                }

                                if (info.gimbal_data) //在页面显示转台的配置数据
                                {
                                    that.show_dev_form.show_gimbal = true; //显示gimbal配置表单
                                    if ( !info.gimbal_data.type ) info.gimbal_data.type = '0';
                                    
                                    var gimbal_focus_conf = $.parseJSON(info.gimbal_data.focustype); //处理焦点类型-焦比-焦距
                              
                                    let focus_num = gimbal_focus_conf.focus.length;
                                    if ( focus_num > 0 ) //有焦点类型被选择
                                    {
                                        that.gimbal_focus.focustype = gimbal_focus_conf.focus;
                                        delete gimbal_focus_conf.focus;
                                        for ( var p in gimbal_focus_conf )
                                        {
                                            that.gimbal_focus.postData[p[1]].focusratio = gimbal_focus_conf[p]['focusRatio'];
                                            that.gimbal_focus.postData[p[1]].focuslength = gimbal_focus_conf[p]['focusLeng'];
                                        }
                                    }//处理焦点类型-焦比-焦距 结束

                                    that.gimbal_config = info.gimbal_data;

                                    if (info.gimbal_file)
                                    {
                                        that.gimbal_file = info.gimbal_file;
                                    }
                                }//显示转台配置信息  结束

                                if ( info.ccd_data.ccd_num > 0 ) //在页面显示ccd的配置数据
                                {   //console.log(info.ccd_file); return;
                                    that.all_ccd_config = info.ccd_data; //将所有ccd的配置赋给 all_ccd_config
                                    that.show_dev_form.show_ccd = true; //显示ccd配置表单
                                    that.show_dev_form.ccd_form_num = info.ccd_data.ccd_num; //显示多个ccd
                                    if ( !info.ccd_data[0].type ) info.ccd_data[0].type = '0';
                                    if ( !info.ccd_data[0].imagebits ) info.ccd_data[0].imagebits = '0';
                                    if ( !info.ccd_data[0].coolermode ) info.ccd_data[0].coolermode = '0';
                                    if ( !info.ccd_data[0].gainnumber ) info.ccd_data[0].gainnumber = '0';
                                    if ( !info.ccd_data[0].shuttertype ) info.ccd_data[0].shuttertype = '0';
                        
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
                if ( v.length < 1 )
                {    
                    switch (n) {
                        case 1:
                            msg = '转台属性版本号输入有误';  break;
                        case 2:
                            msg = 'ccd属性版本号输入有误';  break;
                        case 3:
                            msg = '滤光片转轮属性版本号输入有误';  break;
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

                if ( this.gimbal_focus.focustype.length == 0 )  msg += '焦点类型未选择<br>';

                //接下来验证：每个被选择的焦点类型，其焦比、焦距是否输入正确
                if ( this.gimbal_focus.focustype.length > 0 ) //焦点类型已被选择
                {
                    $.each( this.confOption.focustype, function(i, v) //jquery遍历
                    {
                        if ( $.inArray(v, vm.gimbal_focus.focustype ) !== -1 ) //confOption.focustype中第i个元素被选择
                        {
                            msg += vm.check_focus_val(false, i, 1);
                            msg += vm.check_focus_val(false, i, 2);
                        }else{//未被选择
                            delete vm.gimbal_focus.postData[i]; //删除空的对象
                        }
                    });
                }//验证：每个被选择的焦点类型，其焦比、焦距是否输入正确 结束
    
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
            check_devId:function (tip, v, e) {//验证各设备 id
                var msg = '';
                if ( v.length < 1 )
                {
                    msg = '请输入设备id';
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
                            msg = '请输入ccd名称'; break;
                        case 'filter':
                            msg = '请输入滤光片转轮名称'; break;
                        case 'ccd_sensor':
                            msg = '请输入ccd传感器名称'; break;
                    }
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_devName() 结束
            check_bin: function (tip, v, e){//验证ccd bin
                var msg = '';
                var patn = /^\d+( \d+)*$/;
                if ( !patn.test(v) )
                {
                    msg = 'bin输入有误';
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_bin() 结束
            check_pixel: function (tip, v, e, n) {//验证 ccd x、y像素数目
                var msg = '';
                var patn = /^\d+$/;
                if ( !patn.test(v) || v < 1 )
                {
                    switch (n) {
                        case 'x':
                            msg = 'x像素输入有误';break;
                        case 'y':
                            msg = 'y像素输入有误';break;
                        default:
                            break;
                    } 
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_pixel() 结束
            check_pixel_size: function (tip, v, e, n) {//验证 ccd x、y像元
                var msg = '';
                if ( !$.isNumeric(v) || v <= 0 )
                {
                    switch (n) {
                        case 'x':
                            msg = 'x像元输入有误';break;
                        case 'y':
                            msg = 'y像元输入有误';break;
                        default:
                            break;
                    } 
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_pixel_size() 结束
            check_coolT: function (tip, v, e) {//验证 ccd 最低制冷温度
                var msg = '';
                if ( !$.isNumeric(v) || v > 50 || v < -196 )
                {
                    msg = '最低制冷温度输入有误';
            
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_coolT() 结束
            check_maxExposTime: function (tip, v, e, v1) {
                var msg = '';
                
                if ( !$.isNumeric(v) || v <= 0 )
                {
                    msg = '最大曝光时间输入有误';
                    if ( v1 !== undefined ) msg = '最小曝光时间输入有误';
            
                }

                if ( v1 !== undefined && v1 < v*1 )
                {
                    msg = '最小曝光时间输入有误';
            
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_maxExposTime() 结束
            check_intV:function (tip, v, e, n){
                var msg = '';
                var patn = /^\d+$/;
                if ( !patn.test(v) || v < 1 )
                {
                    switch (n) {
                        case 1:
                        msg = '曝光时间分辨率输入有误'; break;
                        case 2:
                            msg = '满阱电荷输入有误'; break;
                        default: break;
                    }
            
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_intV() 结束
            check_emV: function (tip, v, e, v1) {
                var msg = '';
                var patn = /^\d+$/;
                if ( !patn.test(v) )
                {
                    msg = '最大EM输入有误';
                    if ( v1 !== undefined ) msg = '最小EM输入有误';
            
                }

                if ( v1 !== undefined && v1 < v*1 )
                {
                    msg = '最小EM输入有误';
            
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_emV() 结束
            ccd_sbmt:function () {
                var that = this; //存储vue的实例
                var msg = '';
                //console.log(this.ccd_config.readoutspeed.length);return;
                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:false,closeBtn:0});return;
                }
                
                msg += this.check_devId(false, this.ccd_config.ccdid, this.$refs.ccdId, "ccd");
                msg += this.check_devName(false, this.ccd_config.name, this.$refs.ccdName, "ccd");
                msg += this.check_pixel(false, this.ccd_config.xpixel, this.$refs.ccdXpixel, "x");
                msg += this.check_pixel(false, this.ccd_config.ypixel, this.$refs.ccdYpixel, "y");
                msg += this.check_pixel_size(false, this.ccd_config.xpixelsize, this.$refs.ccdXpixelSize, "x");
                msg += this.check_pixel_size(false, this.ccd_config.ypixelsize, this.$refs.ccdYpixelSize, "y");
                msg += this.check_devName(false, this.ccd_config.sensorname, this.$refs.ccdSensorName, "ccd_sensor");
                msg += this.check_coolT(false, this.ccd_config.lowcoolert, this.$refs.ccdLowCoolerT);
                msg += this.check_maxExposTime(false, this.ccd_config.maxexposuretime, this.$refs.ccdMaxExposureTime);
                msg += this.check_maxExposTime(false, this.ccd_config.minexposuretime, this.$refs.ccdMinExposureTime, this.ccd_config.maxexposuretime);
                msg += this.check_intV(false, this.ccd_config.exposuretimeration, this.$refs.exposureTimeRation, 1);
                msg += this.check_intV(false, this.ccd_config.fullwelldepth, this.$refs.fullWellDepth, 2);
                msg += this.check_emV(false, this.ccd_config.emmaxvalue, this.$refs.emMaxValue);
                msg += this.check_emV(false, this.ccd_config.emminvalue, this.$refs.emMinValue, this.ccd_config.emmaxvalue);
                msg += this.check_version(false, this.ccd_config.attrversion, this.$refs.ccd_version, 2);
                msg += this.check_bin(false, this.ccd_config.binarray, this.$refs.bin);
                if ( this.ccd_config.type == '0' )              msg += '探测器类型未选择<br>';
                if ( this.ccd_config.imagebits == '0' )         msg += '图像位数未选择<br>';
                if ( this.ccd_config.coolermode == '0' )        msg += '制冷方式未选择<br>';
                if ( this.ccd_config.readoutspeed.length < 1 )  msg += '读出速度模式未选择<br>';
                if ( this.ccd_config.readoutmode.length < 1 )   msg += '读出模式未选择<br>';
                if ( this.ccd_config.transferspeed.length < 1 ) msg += '转移速度模式未选择<br>';
                if ( this.ccd_config.gainmode.length < 1 )      msg += '增益模式未选择<br>';
                if ( this.ccd_config.shuttertype == '0' )       msg += '快门类型未选择<br>';
                if ( this.ccd_config.shuttermode.length < 1)    msg += '快门模式未选择<br>';
                if ( this.ccd_config.issupportfullframe === undefined ) msg += '支持帧转移未选择<br>';
                if ( this.ccd_config.issupportem === undefined )        msg += '支持EM未选择<br>';
                if ( this.ccd_config.issupportscmosnoisefilter === undefined ) msg += '支持CMOS noise filter未选择<br>';
                if ( this.ccd_config.issupportbaseline === undefined ) msg += '支持base line未选择<br>';
                if ( this.ccd_config.issupportoverscan === undefined ) msg += '支持Over scan未选择<br>';
                if ( this.ccd_config.issupportroi === undefined )   msg += '支持开窗未选择<br>';
                if ( this.ccd_config.interfacetype.length < 1 )     msg += '接口类型未选择<br>';
                if ( this.ccd_config.exposetriggermode.length < 1 ) msg += '曝光触发模式未选择<br>';
                if ( this.ccd_config.canconnect === undefined )     msg += '支持连接未选择<br>';
                if ( this.ccd_config.cansetcoolert === undefined )  msg += '设置制冷温度未选择<br>';
                if ( this.ccd_config.cansetexposureparam === undefined ) msg += '设置曝光策略未选择<br>';
                if ( this.ccd_config.canstartexposure === undefined )   msg += '支持开始曝光未选择<br>';
                if ( this.ccd_config.canstopexposure === undefined )    msg += '支持停止曝光未选择<br>';
                if ( this.ccd_config.canabortexposure === undefined )   msg += '支持终止曝光未选择<br>';
                if ( this.ccd_config.cansetgain === undefined )         msg += '设置增益未选择<br>';
                if ( this.ccd_config.cansetreadoutspeedmode === undefined ) msg += '设置读出速度模式未选择<br>';
                if ( this.ccd_config.cansettransferspeedmode === undefined ) msg += '设置转移速度模式未选择<br>';
                if ( this.ccd_config.cansetbin === undefined ) msg += '设置BIN未选择<br>';
                if ( this.ccd_config.cansetroi === undefined ) msg += '设置ROI未选择<br>';
                if ( this.ccd_config.cansetshutter === undefined ) msg += '设置快门未选择<br>';
                if ( this.ccd_config.cansetfullframe === undefined ) msg += '设置帧转移未选择<br>';
                if ( this.ccd_config.cansetem === undefined ) msg += '设置EM未选择<br>';
                if ( this.ccd_config.cannoisefilter === undefined ) msg += '设置CMOS noise filter未选择<br>';
                if ( this.ccd_config.cansetbaseline === undefined ) msg += '设置Base line未选择<br>';
                if ( this.ccd_config.cansetoverscan === undefined ) msg += '设置Over scan未选择<br>';

                if ( msg !== '' )
                {
                    layer.alert(msg, {shade:false,closeBtn:0});return;
                }

                var postData = new FormData (this.$refs.ccd);
                postData.append('teleid', this.show_dev_form.teleid); //将望远镜Id 提交上去
                postData.append('ccdno', this.show_dev_form.ccd_no); //将ccd序号 提交上去
                $.ajax({
                    type: 'post',
                    url: 'ccd_config',
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
        
                            that.ccd_config.attrmodifytime = info.attrmodifytime; //显示属性更新时间             

                            if (info.file) that.ccd_file = info.file;
                        }//解析 处理 json 结束
                     },/*success方法结束 */
                     error:  function () {
                          layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0,});
                     }
                })/*ajax 结束*/
            },//ccd_sbmt() 结束
            check_ip:function (tip, v, e) {//验证各设备ip
                var msg = '';
                var patn = /^[A-Za-z0-9.:]{8,}$/;
                if ( !patn.test(v) )
                {
                    msg = 'ip输入有误';
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
                }
                
				return msg !== '' ? msg + '<br>' : '';
            },//check_ip() 结束
            check_slot:function (tip, v, e) {
                var msg = '';
                var patn = /^\d+$/;
                
                if ( !patn.test(v) || v < 1 )
                {
                    msg = '插槽数目输入有误';
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
                }
                
                if ( tip===true && msg === '' ) //初始化this.filter_slot.slotData
                {
                    for (let i = 1; i <= v*1; i++)
                    {
                        this.filter_slot.slotData[i] = {filterType:'0', filterName:'', filterComp:''};
                    }
                }
				return msg !== '' ? msg + '<br>' : '';
            },//check_slot() 结束
            check_filterSize:function (tip, v, e) {//验证插槽大小
                var msg = '';
                var patn = /^\[\d+\.?\d?,\d+\.?\d?,\d+\.?\d?\]$/;
                if ( !patn.test(v) )
                {
                    msg = '插槽大小输入有误';
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
                }
                
				return msg !== '' ? msg + '<br>' : '';
            },//check_filterSize() 结束
            check_filterName:function (tip, n) {//验证滤光片名称
                var msg = '';
                var v = this.filter_slot.slotData[n].filterName;
                var patn = /([\u4e00-\u9fa5]| )+/;
                var ele = 'filterName' + n;

				if ( patn.test(v) || v.length < 1 )
				{
					msg = '插槽' + n + ':滤光片名称输入有误';
				}

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs[ele]);
                }
                
				return msg !== '' ? msg + '<br>' : '';
            },//check_filterName() 结束
            check_filterComp: function (tip, n) {//验证滤光片 焦距偏差值
                var msg = '';
                var v = this.filter_slot.slotData[n].filterComp;
                var patn = /^\d+$/;
                var ele = 'filterComp' + n;

				if ( !patn.test(v) || v < 1 )
				{
					msg = '插槽' + n + ':偏差值输入有误';
				}

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, this.$refs[ele]);
                }
                
				return msg !== '' ? msg + '<br>' : '';
            },//check_filterComp() 结束
            filter_sbmt: function () {//滤光片提交
                var that = this; //存储vue的实例
                var msg = '';

                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:0, closeBtn:0});return;
                }

                msg += this.check_ip(false, this.filter_config.ip, this.$refs.filterIp);
                msg += this.check_devId(false, this.filter_config.filterid, this.$refs.filterId);
                msg += this.check_devName(false, this.filter_config.name, this.$refs.filterName, 'filter');
                msg += this.check_slot(false, this.filter_config.numberoffilter, this.$refs.filterNum);
                msg += this.check_filterSize(false, this.filter_config.filtersize, this.$refs.filterSize);

                var slot_num = this.filter_config.numberoffilter;

                if ( slot_num > 1 ) //如果插槽数目有值
                {
                    for (let i = 1; i <= slot_num; i++)
                    {
                        if ( this.filter_slot.slotData[i].filterType == '0' ) msg += '插槽' + i + ':滤光片类型未选择<br>';
                        msg += this.check_filterName(false, i);
                        msg += this.check_filterComp(false, i);
                    }
                }

                if ( this.filter_config.cansetfilterposition === undefined ) msg += '设置滤光片位置未选择<br>';
                if ( this.filter_config.canconnect === undefined ) msg += '支持连接未选择<br>';
                if ( this.filter_config.canfindhome === undefined ) msg += '支持找零未选择<br>';
                if ( this.filter_config.filtershape === '0' ) msg += '滤光片形状未选择<br>';
                msg += this.check_version(false, this.filter_config.attrversion, this.$refs.filterAttrVersion, 3);

                if ( msg !== '')
                {
                    layer.alert(msg, {shade:0, closeBtn:0}); return;
                }

                var filterData = new FormData (this.$refs.filter);
                filterData.append('teleid', this.show_dev_form.teleid); //将望远镜Id 提交上去

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
        
                            layer.alert(info.msg, {
                                shade:false,
                                closeBtn:0,
                                yes:function (n){
                                    layer.close(n);
                                },
                            });
        
                            that.filter_config.attrmodifytime = info.attrmodifytime; //显示属性更新时间             

                            if (info.file) that.filter_file = info.file;
                        }//解析 处理 json 结束
                     },/*success方法结束 */
                     error:  function () {
                          layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0,});
                     }
                })/*ajax 结束*/
            },//filter_sbmt() 结束
        },//methods 结束
    });//vue 结束////////////////////
 
     var filterSystem = $('#filterSystem'); //滤光片类型
     var filterShape = $('#filterShape'); //滤光片形状
     var sDomeType = $('#sDomeType'); //随动圆顶类型
     var oDomeType = $('#oDomeType'); //全开圆顶类型
     var guideScopeOpticalStructure = $('#guideScopeOpticalStructure'); //导星镜 焦点类型
     /* 获取所有要填入数据的元素对象 结束*/
    
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

    /*滤光片 提交按钮 点击事件*/
//     filterBtn.click(function () {
//         //检查望远镜下拉选择框 是否选择了某望远镜
//         var atId = atNo.val();
//         if ( atId == 0)
//         {//未选择某个望远镜
//             layer.alert('请选择您要配置的望远镜!', {shade:false, closeBtn:0});return;
//         }

//         var filterData = new FormData(filterForm[0]);
//         filterData.append('teleid', atId); //将某望远镜的id 加入表单数据中
//         //将各插槽之滤光片类型数据加入表单 
//         //加入之前先验证 filter_type的元素个数不能少于插槽数目值
//         var tempStr = handle_filtersystem_data (filter_type);
//         filterData.append('filtersystem', tempStr); //将各插槽之滤光片类型数据加入表单 结束

//         $.ajax({
//             type: 'post',
//             url: 'filter_config',
//             data : filterData,
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
//                     filterAttrModifyTime.html(info.attrmodifytime); //显示属性更新时间
//                     //在页面显示已上传的文件名
//                     if ( info.file ) //有已上传的文件信息
//                     {
//                         show_file (filterFile, info.file);
//                     }else{
//                         filterFile.html('');
//                     }
//                 }//解析 处理 json 结束
//              },
//              error:  function () {
// 	              layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
//              },
//         })
//     }); /*滤光片 提交按钮 点击事件 结束*/
    /*滤光片表单 按钮 js事件 结束*/

    /*显示滤光片配置数据 */
    // function show_filter_data (data)
    // { 
    //     filterIp.val(data.ip);
    //     //filterId.val(data.filterid);
    //     filterName.val(data.name);
    //     filterTeleId.html(data.atname);
    //     filterNum.val(data.numberoffilter);
    //     data.numberoffilter !== undefined && filterNum.blur(); //如果插槽数目有值，执行插槽数目input框的blur事件
    //     /*将滤光片类型的值赋予：filter_type*/
    //     if (data.filtersystem)
    //     {
    //         filter_type = {}; //将此对象重置为空
    //         data.filtersystem = data.filtersystem.split('#'); //得到['1:aa', '2:cc']
    //         var filtersystem_num = data.filtersystem.length;
    //         for (var filtersystem_i = 0; filtersystem_i < filtersystem_num; filtersystem_i++)
    //         {
    //             var temp_data = data.filtersystem[filtersystem_i].split(':');
    //             filter_type[ temp_data[0] ] = temp_data[1];
    //         }
    //     }/*将滤光片类型的值赋予：filter_type 结束*/
    //     filterSize.val(data.filtersize);
    //     filter_Name.val(data.filtername);
    //     focusLengthCompensate.html(data.filterfocuslengthcompensate);
    //     filterShape.val(data.filtershape);
    //     data.cansetfilterposition == '1' ? canSetFilterPosition.click() : canSetFilterPosition_1.click();
    //     data.canconnect == '1' ? filterCanConnect.click() : filterCanConnect_1.click();
    //     data.canfindhome == '1' ? filterCanFindHome.click() : filterCanFindHome_1.click();
    //     filterAttrVersion.val(data.attrversion);
    // }/*显示滤光片配置数据 结束*/

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