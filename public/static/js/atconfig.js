/*望远镜配置页面 js*/
$(function () {
    //显示导航栏望远镜列表///////////////////////////////////// 
       var ul = $('#atListUl');
       $('#atList').hover(
            function (){  ul.show();}, 
           function (){ ul.hide();}
       );

        //各望远镜配置 js事件
       var configList = $('#atConfigList');
       $('#atConfig').hover(
            function (){ configList.show(); }, 
           function (){ configList.hide(); } 
       );
/************* 读取16个动态增减的配置数据 *************/
    window.onfocus = function () {//此处需对转台和导星镜的焦点类型及其焦距焦比进行处理
        var share_conf_data = localStorage.getItem('share_conf_data');
        if ( share_conf_data )
        {
            vm.confOption = eval( '(' + share_conf_data + ')' );
            console.log( vm.confOption );  //是数组
            //处理转台的焦点类型及其焦距焦比
            let gimbal_focus_num = vm.confOption.focustype.length;

            if ( gimbal_focus_num >= 1 )
            {
                //如果gimbal_focus没有相应的postData对象，则创建
                for (let i = 0; i < gimbal_focus_num; i++)
                {
                    if ( !vm.gimbal_focus.postData[i] )  vm.gimbal_focus.postData[i] = {focusratio:"", focuslength:""};              
                }
            }
            //处理转台的焦点类型及其焦距焦比 结束

            //处理导星镜的焦点类型及其焦距
            let guide_focus_num = vm.confOption.opticalStructure.length;

            if ( guide_focus_num >= 1 )
            {
                //如果gimbal_focus没有相应的postData对象，则创建
                for (let i = 0; i < guide_focus_num; i++)
                {
                    if ( !vm.guide_focus.postData[i] )  vm.guide_focus.postData[i] = {focuslength:""};              
                }
            }
            //处理导星镜的焦点类型及其焦距 结束
        }
    }//window.onfcus() 结束
/************* 读取16个动态增减的配置数据 *************/
/************* vue 开始 *************/
    var vm = new Vue ({ //vue 开始
        el: '#all',
        data: {
            update_gain_noise:0,
            rows:1, //ccd 增益值及读出噪声值输入表格的行数
            r_spd:0, //ccd 读出速度的数目
            readoutspeed:[], //ccd 读出速度
            t_spd:0, //ccd 转移速度的数目
            transferspeed:[], //ccd 转移速度
            gain_num:'', //ccd 增益挡位
            gainMode:[], //ccd 增益模式
            gain_noise:{//存储增益模式-读出速度-转移速度-增益挡位-增益值-噪声值, 并将此对象提交给php
                
            },//gain_noise 结束
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
            confOption: {//动态增减的16个固定属性选项

            },//动态增减的16个固定属性选项
            gimbal_config: {
                ip:'', atname:'', address:'', longitude:0, latitude:0, altitude:0, type: '0', focustype: [], focusratio: '0', focuslength:'',
                maxaxis1speed:'', maxaxis1acceleration:'', maxaxis2acceleration:'', maxaxis3acceleration:'', maxaxis1speed:'', maxaxis2speed:'',
                axis1parkposition:'', axis2parkposition:'', axis3parkposition:'', minelevation:'', numtemperaturesensor:'',
                numhumiditysensor:'', attrversion:'', haveaxis3: 2, haveaxis5: 2, canconnect: 2, canfindhome:2, cantrackstar:2,
                cansetobjectname:2, canslewazel:2, canslewderotator:2, canconfigderotator:2, canstop:2, cansettrackspeed:2, canpark:2, canfixedmove:2,
                canpositioncorrect:2, cancoveroperation:2, canfocusoperation:2, canemergencystop:2, cansavesyncdata:2, cantracksatellite:2, canconfigproperty:2,
            }, //转台的配置信息
            gimbal_file: {}, //转台上传的文件
            ccd_config: {
                type: '0', imagebits: [], coolermode: '0', gainnumber: '0', shuttertype: '0',
                bin:[], ip:'', ccdid:'', name:'', xpixel:'', ypixel:'', xpixelsize:'', ypixelsize:'',
                sensorname:'', lowcoolert:'', maxexposuretime:'', minexposuretime:'', exposuretimeration:'', 
                fullwelldepth:'', readoutspeed:[], readoutmode:[], transferspeed:[], gainmode:[], gainvaluearray:'',
                readoutnoisearray:'', shuttermode:[], interfacetype:[], emmaxvalue:'', exposetriggermode:[],
                emminvalue:'', attrversion:'', issupportfullframe:2, issupportem:2, issupportscmosnoisefilter:2,
                issupportbaseline:2, issupportoverscan:2, issupportroi:2, canconnect:2, cansetcoolert:2,
                cansetexposureparam:2, canstartexposure:2, canabortexposure:2, cansetgain:2, cansetreadoutspeedmode:2,
                cansettransferspeedmode:2, cansetbin:2, cansetroi:2, cansetshutter:2, cansetfullframe:2, cansetem:2,
                cannoisefilter:2, cansetbaseline:2, cansetoverscan:2,
            }, //ccd的配置信息
            ccd_file: {}, //ccd_No1的上传的文件
            ccd_files:{}, //当前望远镜的所有ccd的文件数据
            filter_slot:{//存储滤光片 各插槽一一对应的滤光片类型-名称-偏差值
                slotData:{},//各插槽的滤光片类型-名称-偏差值
            },//filter_slot 结束
            filter_config: {
                numberoffilter: '', ip:'', filterid:'',name:'', filtersize:'', filtershape:'0', attrversion:'',
                attrmodifytime:'', cansetfilterposition:2, canconnect:2, canfindhome:2,
            },//滤光片的配置信息
            filter_file: {}, //滤光片转轮 上传的文件
            guide_focus: {//存储导星镜 各焦点类型一一对应的焦距
                focustype:[],
                postData:{},//导星镜 焦点类型须提交的数据
            },//guide_focus 结束
            guide_config: {//导星镜配置信息
                ip:'', guidescopeid:'', name:'', aperture:'', attrmodifytime:'', attrversion:'', canconnect:2,
                hasmirrorcover:2, issupportautofocus:2, canenableautofocus:2,
            },//guide_config 结束
            guide_file: {}, //导星镜 上传的文件
            oDome_config: {//全开圆顶配置信息
                diameter:'', ip:'', name:'', odomeid:'', type:'0', attrversion:'', attrmodifytime:'', canopendome:2, canconnect:2,
            },//oDome_config 结束
            oDome_file: {}, //全开圆顶 上传的文件
            sDome_config: {//随动圆顶配置信息
                ip:'', sdomeid:'', name:'', dometype:'0', maxspeed:'', diameter:'', attrversion:'', attrmodifytime:'',
                hasshade:2, cansetdomepositin:2, cansetshadeposition:2, cansetrotatespeed:2, canstop:2, canopenshutter:2,
                cansetshadespeed:2, canconnect:2,
            },//oDome_config 结束
            sDome_file: {}, //随动圆顶 上传的文件
            focus_config: {//调焦器配置信息
                ip:'', focusid:'', name:'', increment:'', maxvalue:'', minvalue:'', maxspeed:'', attrversion:'', attrmodifytime:'',
                canconnect:2, canfindhome:2, cantemperturecompensate:2, cansetposition:2, cansetspeed:2, canstop:2, 
                canenabletemperturecompensate:2, cansettemperturecompensatecoefficient:2,
            },//focus_config 结束
            focus_file: {}, //调焦器 上传的文件
        },//data 结束
        computed: {
        },//computed 结束
        watch: {
            readoutspeed: function (newV){//监听readoutspeed
                var r = newV.length, //r: 读出速度的数目
                    m = this.gainMode.length, //m: 增益模式的数目
                    t =  this.transferspeed.length, // 转移速度的数目
                    g = this.gain_num; //增益挡位

                var r1 = r == 0 ? 1 : r;
                var m1 = m == 0 ? 1 : m;
                var t1 = t == 0 ? 1 : t;
                var g1 = g === '' ? 1 : g;

                this.rows = m1 * r1 * t1 *g1; //表格总行数
                this.set_gain_noise(this.rows, m, r, t, g);
            },
            transferspeed: function (newV){//监听transferspeed
                var t = newV.length,
                    m = this.gainMode.length,
                    r =  this.readoutspeed.length,
                    g = this.gain_num;

                var r1 = r == 0 ? 1 : r;
                var m1 = m == 0 ? 1 : m;
                var t1 = t == 0 ? 1 : t;
                var g1 = g === '' ? 1 : g;

                this.rows = m1 * r1 * t1 *g1;

                this.set_gain_noise(this.rows, m, r, t, g);
            },
            gainMode: function (newV){//监听data.gainMode
                var m = newV.length,
                    t = this.transferspeed.length,
                    r = this.readoutspeed.length,
                    g = this.gain_num;

                var r1 = r == 0 ? 1 : r;
                var m1 = m == 0 ? 1 : m;
                var t1 = t == 0 ? 1 : t;
                var g1 = g === '' ? 1 : g;

                this.rows = m1 * r1 * t1 *g1;

                this.set_gain_noise(this.rows, m, r, t, g);
            },
            gain_num: function (newV){//监听data.gain_num
                var patn = /^\d+$/;
                if ( !patn.test(newV) || newV <= 0 )
                {
                    this.gain_num = ''; //输入错误或不输入，都将挡位置为''
                }          

                var g = this.gain_num,
                    m = this.gainMode.length,
                    t = this.transferspeed.length,
                    r = this.readoutspeed.length;

                var r1 = r == 0 ? 1 : r;
                var m1 = m == 0 ? 1 : m;
                var t1 = t == 0 ? 1 : t;
                var g1 = g === '' ? 1 : g;

                this.rows = m1 * r1 * t1 *g1;

                this.set_gain_noise(this.rows, m, r, t, g);
            },
        },//watch 结束
        methods: {
            tt:function () {
                // var temp = this.gain_noise[1];
                // this.gain_noise[1] = this.gain_noise[3];
                // this.gain_noise[3] = temp;;
            },
            check_atName: function (tip, v, e) {//验证望远镜名称
                var msg = '';
                if ( !v || v.length < 1 )
                {
                    msg = '望远镜名称输入有误';
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },
            check_Address: function (tip, v, e,) {//验证隶属观测站
                var msg = '';
                if ( !v || v.length < 1 )
                {
                    msg = '隶属观测站输入有误';
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },
            check_JW: function (tip, v, JW, e) {//验证 经纬度
                var msg = '';

                switch (JW)
                {
                    case 'J':
                        if ( !$.isNumeric(v) || v < -180 || v > 180 )
                        {
                            msg = '经度输入有误';
                        }
                        break;
                    case 'W':
                        if ( !$.isNumeric(v) || v < -90 || v > 90 )
                        {
                            msg = '纬度输入有误';
                        }
                        break;
                    default:
                        msg = '经纬度数据异常';  break;
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },
            check_altitude: function (tip, v, e) {//验证海拔
                var msg = '';
                if ( !$.isNumeric(v) || v > 6000 || v < -1000 )
                {
                    msg = '海拔输入有误';
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },
            downLoadFile:function (v, dev) {//各设备说明文件用ajax下载
                var that = this; //存储vue的实例
                var params = ''; //根据dev，组装url请求的参数:params

                if ( dev === 'ccd' )
                {
                    params = dev + this.show_dev_form.teleid + '_' + this.show_dev_form.ccd_no + '/' + v;
                }else{
                    params = dev + this.show_dev_form.teleid + '/' + v;
                }

                var url = '/download/' + params;
                var xhr = new XMLHttpRequest();
                xhr.open('GET', url, true);    // 也可以使用POST方式，true表示异步
                xhr.responseType = "blob";  // 返回类型blob
                // 定义请求完成的处理函数，请求前也可以增加加载框/禁用下载按钮逻辑
                xhr.onload = function () {// 请求完成
                    if (this.status === 200)// 返回200
                    {
                        var blob = this.response;

                        if ( blob.size < 30 && blob.size > 1 ) //服务器返回的是错误提示，而不是文件的二进制数据
                        {
                            layer.alert('您无权限下载!', {shade:0, closeBtn:0});return;
                        }else if ( blob.size == 1 ){
                            layer.alert('下载的文件不存在!', {shade:0, closeBtn:0});return;
                        }

                        var reader = new FileReader();
                        reader.readAsDataURL(blob);  // 转换为base64，可以直接放入a的href
                        reader.onload = function (e) {
                            that.$refs.down.download = v; //that.$refs.down 页面中一个a元素
                            that.$refs.down.href = e.target.result;
                            that.$refs.down.click();
                        }
                    }else if (this.status >= 400) {
                        layer.alert('网络异常!', {shade:0, closeBtn:0});
                    }
                };
                xhr.send();  //发送请求
                /*var file = '说明书.docx';
                var url = 'xinglong/atconfig/downLoadFlie?filename=' + file;
                var xhr = new XMLHttpRequest();
                xhr.open('POST', url, true);    // 也可以使用POST方式，根据接口
                xhr.responseType = "blob";  // 返回类型blob
                // 定义请求完成的处理函数，请求前也可以增加加载框/禁用下载按钮逻辑
                xhr.onload = function () {// 请求完成
                  if (this.status === 200) {// 返回200
                    var blob = this.response;
                    var reader = new FileReader();
                    reader.readAsDataURL(blob);  // 转换为base64，可以直接放入a的href
                    reader.onload = function (e) {
                      that.$refs.down.download = file; //that.$refs.down 页面中一个a元素
                      that.$refs.down.href = e.target.result;
                      that.$refs.down.click();
                    }
                  }
                };
                xhr.send();   // 发送ajax请求*/
            },//downLoadFile 结束
            set_gain_noise: function (rows, m, r, t, g){//设置this.gain_noise对象
                
                //this.gain_noise = {}; //每次生成先将此对象置为空

                for (let i = 1; i <= rows; i++) //初始化this.gain_noise对象，i即为当前的行数
                {
                    //每行都是一个对象
                    if ( !this.gain_noise[i] )
                    {
                        this.gain_noise[i] = {gainMode:'', readOut_speed:'', transfer_speed:'', gain_gear:'', gainVal:0, noiseVal:0};
                    }

                    switch (m) 
                    {
                        case 0: //无增益模式被选中
                            this.generate_table(i, r, t, g, m, rows);  break;
                        case 1: //增益模式被选中一个
                            this.gain_noise[i].gainMode = this.gainMode[0];
                            this.generate_table(i, r, t, g, m, rows);  break;
                        case 2: //增益模式被选中2个
                            if ( i <= rows/2 ) //第一个增益模式，即生成表格的上半部分
                            {
                                this.gain_noise[i].gainMode = 'High Sensitivity';
                                this.generate_table(i, r, t, g, m, rows);
                            }else if ( i > rows/2 ) //第2个增益模式，即生成表格的下半部分
                            {
                                this.generate_table(i, r, t, g, m, rows);
                                this.gain_noise[i].gainMode = 'High Capacity';
                            }
                           break;
                    }
                }
                for (var p in this.gain_noise) //删除gain_noise中多于rows数量的行
                {
                    if ( p > rows ) delete this.gain_noise[p];
                }
            },//set_gain_noise 结束
            generate_table: function (i, r, t, g, m, rows) { //生成 读出速度-转移速度-增益挡位
                m = m ? m : 1; //若无增益模式被选中，将增益模式数目置为1

                if( r > 0 ) //有读出速度
                {
                    t = t ? t : 1;
                    g = g ? g : 1;
                    var gt = g*t; //转移速度数目*增益挡位，得出每个读出速度要显示的行数

                    for (let j = 1; j <= r; j++)
                    {
                        if ( ( i > (j-1)*gt ) && i <= j*gt ) this.gain_noise[i].readOut_speed = this.readoutspeed[j-1];
                        
                        if ( m == 2 ) //将表格下半部分减去半数表格行数的差值 比对后，即为表格下半部分的读出速度
                        {
                            if ( ( i-rows/2 > (j-1)*gt ) && i-rows/2 <= j*gt ) this.gain_noise[i].readOut_speed = this.readoutspeed[j-1];
                        }
                    }
                }

                if( t > 0 ) //有转移速度
                {
                    g = g ? g : 1;
                    r = r ? r : 1;
                    var rtm = r*t*m; //转移速度要循环的次数

                    for (let k = 1; k <= rtm; k++)
                    {
                        let index = (k - 1) % t; //当前行数要对应显示的转移速度索引
                        if ( ( i > (k-1)*g ) && i <= k*g ) this.gain_noise[i].transfer_speed = this.transferspeed[index]; 
                    }
                }

                if( g > 0 ) //有增益挡位
                {
                    var i_g = i%g; //当前行数对增益挡位取余，余数就是当前行的挡位值

                    for (let p = 1; p <= g; p++)
                    {
                        this.gain_noise[i].gain_gear = i_g ? i_g : g;  //余数为零时，显示最大的挡位         
                    }
                }
            },// generate_table() 结束
            delete_readOutSpeed: function (v) {//删除ccd 读出速度
                this.r_spd--;
                this.readoutspeed.splice(v-1, 1);
            },//delete_readOutSpeed 结束
            delete_transferSpeed: function (v) {//删除ccd 转移速度
                this.t_spd--;
                this.transferspeed.splice(v-1, 1);
            },//delete_transferSpeed 结束
            show_focus_tr:function (v){
                if ( $.inArray(v, this.gimbal_focus.focustype) !== -1 )
                {
                    return true;
                }else{
                    return false;
                } 
            },//show_focus_tr() 结束
            show_guide_tr: function (v){
                if ( $.inArray(v, this.guide_focus.focustype) !== -1 )
                {
                    return true;
                }else{
                    return false;
                } 
            },//show_guide_tr() 结束
            check_focus_val: function (tip, k, n){//验证转台：焦点类型-焦比-焦距
                var msg = '';
                var ele = ''; //输入框

                if ( !$.isNumeric(this.gimbal_focus.postData[k].focusratio) || this.gimbal_focus.postData[k].focusratio < 0 )
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
            check_guideFocusL:function (tip, k){//验证导星镜焦距
                var msg = '';
                var ele = 'guideFocus' + k; //输入框
                if ( !$.isNumeric(this.guide_focus.postData[k].focuslength) || this.guide_focus.postData[k].focuslength <= 0 )
                {
                    msg = this.confOption.opticalStructure[k] + '：焦距输入有误';
                }
                if ( tip === true && msg !== '' )
                {
                    layer.tips(msg, this.$refs[ele]);
                }
                return msg !== '' ? msg + '<br>' : '';
            },//check_guideFocusL() 结束
            change_ccd: function (n) {//切换要配置的ccd，n为ccd的序号
                if ( this.all_ccd_config[n-1] )  this.ccd_config = this.all_ccd_config[n-1]; //将被选中的ccd配置数据赋值给this.ccd_config

                //处理第n个ccd的读出速度、转移速度、增益值、噪声值等的显示
                this.gainMode = this.ccd_config.gainmode; //将增益模式赋给data.gainMode
                this.readoutspeed = this.ccd_config.readoutspeed; //将读出速度数组赋给data.readoutspeed
                if ( this.readoutspeed.length > 0 ) this.r_spd = this.readoutspeed.length; //显示读出速度input框
                this.transferspeed = this.ccd_config.transferspeed; //将转移速度数组赋给data.transferspeed
                if ( this.transferspeed.length > 0 ) this.t_spd = this.transferspeed.length; //显示转移速度input框
                this.gain_num = this.ccd_config.gainnumber; //将增益挡位赋给data.gain_num
                //接下来处理从数据库中gain_noise字段获取的json字符串
                if ( this.ccd_config.gain_noise )
                {
                    this.gain_noise = $.parseJSON( this.ccd_config.gain_noise );
                }
                //处理第n个ccd的读出速度、转移速度、增益值、噪声值等的显示 结束

                //然后将第n个ccd的上传文件数据赋值给ccd_file
                if ( this.ccd_files[n] !== 0 ) //第n个ccd有上传文件
                {
                   this.ccd_file = this.ccd_files[n]; 
                }else{//第n个ccd无上传文件
                    this.ccd_file = {};
                }
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
                                that.confOption = info.confOption; //将16个配置项数据赋给
                                var focustype_num = info.confOption.focustype.length; //转台的焦点类型个数
                                for ( let i = 0; i < focustype_num; i++)
                                {
                                    that.gimbal_focus.postData[i] = {focusratio:"", focuslength:""}; //初始化gimbal_focus.postData
                                }

                                var guide_focus_num = info.confOption.opticalStructure.length; //导星镜的焦点类型个数
                                for ( let i = 0; i < guide_focus_num; i++)
                                {
                                    that.guide_focus.postData[i] = {focuslength:""}; //初始化guide_focus.postData
                                }

                                if (info.gimbal_data) //在页面显示转台的配置数据
                                {
                                    that.show_dev_form.show_gimbal = true; //显示gimbal配置表单
                                    if ( !info.gimbal_data.type ) info.gimbal_data.type = '0';
                                    
                                    var gimbal_focus_conf = {focus:[],}; //初始化gimbal_focus_conf
                                    if ( info.gimbal_data.focustype )
                                    {
                                        gimbal_focus_conf = $.parseJSON(info.gimbal_data.focustype); //处理焦点类型-焦比-焦距
                                    }
                                    
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
                                 
                                    //将转台配置数据赋值给gimbal_config,且避免将gimbal_config内原有的初始属性值给删了
                                    for (let p in info.gimbal_data)
                                    {
                                        that.gimbal_config[p] = info.gimbal_data[p];                                
                                    }

                                    if (info.gimbal_file)
                                    {
                                        that.gimbal_file = info.gimbal_file;
                                    }
                                }//显示转台配置信息  结束
                                else
                                {
                                    that.show_dev_form.show_gimbal = false; //显示gimbal配置表单
                                }

                                if ( info.ccd_data.ccd_num > 0 ) //在页面显示ccd的配置数据
                                {   //console.log(info.ccd_file); return;
                                    that.all_ccd_config = info.ccd_data; //将所有ccd的配置赋给 all_ccd_config
                                    that.show_dev_form.show_ccd = true; //显示ccd配置表单
                                    that.show_dev_form.ccd_form_num = info.ccd_data.ccd_num; //显示多个ccd
                                    if ( !info.ccd_data[0].type ) info.ccd_data[0].type = '0';
                                    //if ( !info.ccd_data[0].imagebits ) info.ccd_data[0].imagebits = '0';
                                    if ( !info.ccd_data[0].coolermode ) info.ccd_data[0].coolermode = '0';
                                    if ( !info.ccd_data[0].gainnumber ) info.ccd_data[0].gainnumber = '0';
                                    if ( !info.ccd_data[0].shuttertype ) info.ccd_data[0].shuttertype = '0';

                                    //that.ccd_config = info.ccd_data[0]; //将第一个ccd中的配置数据赋值给ccd_config
                                    //将ccd配置数据赋值给ccd_config,且避免将ccd_config内原有的初始属性值给删了
                                    for (let p in info.ccd_data[0])
                                    {
                                        that.ccd_config[p] = info.ccd_data[0][p];                                
                                    }

                                    that.gainMode = that.ccd_config.gainmode; //将增益模式赋给data.gainMode
                                    that.readoutspeed = that.ccd_config.readoutspeed; //将读出速度数组赋给data.readoutspeed
                                    if ( that.readoutspeed.length > 0 ) that.r_spd = that.readoutspeed.length; //显示读出速度input框
                                    that.transferspeed = that.ccd_config.transferspeed; //将转移速度数组赋给data.transferspeed
                                    if ( that.transferspeed.length > 0 ) that.t_spd = that.transferspeed.length; //显示转移速度input框
                                    that.gain_num = that.ccd_config.gainnumber; //将增益挡位赋给data.gain_num
                                    //接下来处理从数据库中gain_noise字段获取的json字符串
                                    if ( that.ccd_config.gain_noise )
                                    {
                                        that.gain_noise = $.parseJSON( that.ccd_config.gain_noise );
                                    }

                                    if (info.ccd_file)
                                    {
                                        if ( info.ccd_file[1] != 0 ) that.ccd_file = info.ccd_file[1]; //若ccd1有上传文件
                                        that.ccd_files = info.ccd_file; //将返回的所有关于ccd文件的数据赋值给ccd_files
                                    }
                                }//显示ccd 配置信息  结束
                                else
                                {
                                    that.show_dev_form.show_ccd = false; //不显示ccd配置表单
                                }

                                if (info.filter_data) //在页面显示滤光片的配置数据
                                {//console.log(info.filter_data);
                                    that.show_dev_form.show_filter = true; //显示filter配置表单
                                    if ( !info.filter_data.filtershape ) info.filter_data.type = '0';
                                    
                                    var slot_filter_conf = $.parseJSON(info.filter_data.filtersystem); //处理滤光片类型-滤光片名称-偏差值
                       
                                    let slot_num = slot_filter_conf.slot_num;
                                    if ( slot_num > 0 ) //有 插槽数目
                                    {
                                        for ( let i = 1; i <= slot_num; i++ )
                                        {
                                            that.filter_slot.slotData[i] = {filterType:'', filterName:'', filterComp:''}; //初始化filter_slot.slotData[i]
                                            that.filter_slot.slotData[i].filterType = slot_filter_conf.filterType[i-1];
                                            that.filter_slot.slotData[i].filterName = slot_filter_conf.filterName[i-1];
                                            that.filter_slot.slotData[i].filterComp = slot_filter_conf.filterComp[i-1];
                                        }
                                    }//处理滤光片类型-滤光片名称-偏差值 结束

                                    //将filter配置数据赋值给filter_config,且避免将filter_config内原有的初始属性值给删了
                                    for (let p in info.filter_data)
                                    {
                                        that.filter_config[p] = info.filter_data[p];                                
                                    }
                                    //that.filter_config = info.filter_data;

                                    if (info.filter_file)
                                    {
                                        that.filter_file = info.filter_file;
                                    }
                                }//显示滤光片配置信息  结束
                                else
                                {
                                    that.show_dev_form.show_filter = false; //不显示filter配置表单
                                }

                                if (info.focus_data) //在页面显示调焦器配置数据
                                {
                                    that.show_dev_form.show_focus = true;  //将调焦器的选项勾选
                                    //将focus配置数据赋值给focus_config,且避免将focus_config内原有的初始属性值给删了
                                    for (let p in info.focus_data)
                                    {
                                        that.focus_config[p] = info.focus_data[p];                                
                                    }
                                    //that.focus_config = info.focus_data;

                                    if (info.focus_file)
                                    {
                                        that.focus_file = info.focus_file;
                                    }
                                }/*在页面显示调焦器的配置数据 结束*/
                                else
                                {
                                    that.show_dev_form.show_focus = false;  //不显示调焦器配置表单
                                }

                                if (info.sDome_data) //在页面显示随动圆顶的配置数据
                                {   //console.log(info.filter_data);
                                    that.show_dev_form.show_sDome = true; //显示随动圆顶配置表单
                                    if ( !info.sDome_data.dometype ) info.sDome_data.dometype = '0';

                                    //that.sDome_config = info.sDome_data;
                                    //将focus配置数据赋值给sDome_config,且避免将sDome_config内原有的初始属性值给删了
                                    for (let p in info.sDome_data)
                                    {
                                       that.sDome_config[p] = info.sDome_data[p];                                
                                    }

                                    if (info.sDome_file)
                                    {
                                        that.sDome_file = info.sDome_file;
                                    }
                                }//显示随动圆顶配置信息  结束
                                else
                                {
                                    that.show_dev_form.show_sDome = false; //不显示随动圆顶配置表单
                                }

                                if (info.oDome_data) //在页面显示全开圆顶的配置数据
                                {
                                    that.show_dev_form.show_oDome = true; //显示全开圆顶配置表单
                                    if ( !info.oDome_data.type ) info.oDome_data.type = '0';

                                    //that.oDome_config = info.oDome_data;
                                    //将focus配置数据赋值给oDome_config,且避免将oDome_config内原有的初始属性值给删了
                                    for (let p in info.oDome_data)
                                    {
                                       that.oDome_config[p] = info.oDome_data[p];                                
                                    }

                                    if (info.oDome_file)
                                    {
                                        that.oDome_file = info.oDome_file;
                                    }
                                }//显示全开圆顶配置信息  结束
                                else
                                {
                                    that.show_dev_form.show_oDome = false; //不显示全开圆顶配置表单
                                }

                                if (info.guide_data) //在页面显示导星镜的配置数据
                                {
                                    that.show_dev_form.show_guide = true; //显示导星镜配置表单

                                    var guide_focus_conf = $.parseJSON(info.guide_data.focuslength); //处理焦点类型-焦距
                              
                                    let focus_num = guide_focus_conf.focus.length;
                                    if ( focus_num > 0 ) //有焦点类型被选择
                                    {
                                        that.guide_focus.focustype = guide_focus_conf.focus;
                                        delete guide_focus_conf.focus;
                                        for ( var p in guide_focus_conf )
                                        {
                                            that.guide_focus.postData[p[1]].focuslength = guide_focus_conf[p]['focusLeng'];
                                        }
                                    }//处理焦点类型-焦距 结束

                                    //that.guide_config = info.guide_data;
                                    //将focus配置数据赋值给oDome_config,且避免将oDome_config内原有的初始属性值给删了
                                    for (let p in info.guide_data)
                                    {
                                        that.guide_config[p] = info.guide_data[p];                                
                                    }

                                    if (info.guide_file)
                                    {
                                        that.guide_file = info.guide_file;
                                    }
                                }//显示导星镜配置信息  结束
                                else
                                {
                                    that.show_dev_form.show_guide = false; //不显示导星镜配置表单
                                }

                                layer.close(index);  //关闭 加载图标
                            }
                        },//success 结束
                        error:  function () {
                            layer.alert('网络异常,请重新选择望远镜!', {shade:false, closeBtn:0});
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
                if ( !patn.test(v) || v < 0 ) //可以为零
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
                if ( !v || v.length < 1 )
                {    
                    switch (n) {
                        case 1:
                            msg = '转台属性版本号输入有误';  break;
                        case 2:
                            msg = 'ccd属性版本号输入有误';  break;
                        case 3:
                            msg = '滤光片转轮属性版本号输入有误';  break;
                        case 4:
                            msg = '导星镜属性版本号输入有误';  break;
                        case 5:
                            msg = '全开圆顶属性版本号输入有误';  break;
                        case 6:
                            msg = '随动圆顶属性版本号输入有误';  break;
                        case 7:
                            msg = '调焦器属性版本号输入有误';  break;
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
                var t = this.gimbal_config;

                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:false,closeBtn:0});return;
                }

                var msg = ''; //错误提示
                msg += this.check_atName(false, t.atname);
                msg += this.check_Address(false, t.address);
                msg += this.check_JW(false, t.longitude, "J");
                msg += this.check_JW(false, t.latitude, "W");
                msg += this.check_altitude(false, t.altitude);
                if ( t.type === '0' )  msg += '类型未选择<br>';

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
                        }
                    });
                }//验证：每个被选择的焦点类型，其焦比、焦距是否输入正确 结束
                //console.log( t.canfindhome );return;
                msg += this.check_ip(false, t.ip, this.$refs.gimbalIp);
                msg += this.check_axisSpeed(false, t.maxaxis1speed, this.$refs.axis1speed, 1);

                msg += this.check_axisSpeed(false, t.maxaxis2speed, this.$refs.axis2speed, 2);

                if (t.haveaxis3 == 1)
                {
                    msg += this.check_axisSpeed (false,t.maxaxis3speed, this.$refs.axis3speed, 3);
                }

                msg += this.check_axisAcce(false, t.maxaxis1acceleration, this.$refs.axis1_acce, 1);

                msg += this.check_axisAcce(false, t.maxaxis2acceleration, this.$refs.axis2_acce, 2);

                if (t.haveaxis3 == 1)
                {
                    msg += this.check_axisAcce (false,t.maxaxis3acceleration, this.$refs.axis3_acce, 3);
                }
                
                msg += this.check_axisPark(false, t.axis1parkposition, this.$refs.axis1Park, 1);
                
                msg += this.check_axisPark(false, t.axis2parkposition, this.$refs.axis2Park, 2);

                if (t.haveaxis3 == 1)
                {
                    msg += this.check_axisPark (false,t.axis3parkposition, this.$refs.axis3Park, 3);
                }
                
                msg += this.check_minelevation(false, t.minelevation, this.$refs.minElev);
                
                msg += this.check_Num(false, t.numtemperaturesensor, this.$refs.tempera_num, 1);
                
                msg += this.check_Num(false, t.numhumiditysensor, this.$refs.humid_num, 2);

                msg += this.check_version(false, t.attrversion, this.$refs.gimbal_version, 1);

                if ( !(t.haveaxis3 == '1' || t.haveaxis3 == '0') ) msg += '是否有第3轴未选择<br>';
                if ( !(t.haveaxis5 == '1' || t.haveaxis5 == '0') ) msg += '是否有镜盖未选择<br>';
                if ( !(t.canconnect == '1' || t.canconnect == '0') ) msg += '是否支持连接未选择<br>';
                if ( !(t.canfindhome == '1' || t.canfindhome == '0') ) msg += '是否支持找零未选择<br>';
                if ( !(t.cantrackstar == '1' || t.cantrackstar == '0') ) msg += '是否支持跟踪恒星未选择<br>';
                if ( !(t.cansetobjectname == '1' || t.cansetobjectname == '0') ) msg += '支持设置目标名称未选择<br>';
                if ( !(t.canslewazel == '1' || t.canslewazel == '0') ) msg += '是否支持指向固定位置未选择<br>';
                if ( !(t.canslewderotator == '1' || t.canslewderotator == '0') ) msg += '是否支持轴3指向固定位置未选择<br>';
                if ( !(t.canconfigderotator == '1' || t.canconfigderotator == '0') ) msg += '是否支持设置轴3工作模式未选择<br>';
                if ( !(t.canstop == '1' || t.canstop == '0') ) msg += '是否支持停止未选择<br>';
                if ( !(t.cansettrackspeed == '1' || t.cansettrackspeed == '0') ) msg += '支持设置跟踪速度未选择<br>';
                if ( !(t.canpark == '1' || t.canpark == '0') ) msg += '是否支持复位未选择<br>';
                if ( !(t.canfixedmove == '1' || t.canfixedmove == '0') ) msg += '是否支持恒速运动未选择<br>';
                if ( !(t.canpositioncorrect == '1' || t.canpositioncorrect == '0') ) msg += '是否支持位置修正未选择<br>';
                if ( !(t.cancoveroperation == '1' || t.cancoveroperation == '0') ) msg += '是否支持镜盖操作未选择<br>';
                if ( !(t.canfocusoperation == '1' || t.canfocusoperation == '0') ) msg += '是否支持焦点切换镜未选择<br>';
                if ( !(t.canemergencystop == '1' || t.canemergencystop == '0') ) msg += '是否支持急停未选择<br>';
                if ( !(t.cansavesyncdata == '1' || t.cansavesyncdata == '0') ) msg += '是否支持保存同步数据未选择<br>';
                if ( !(t.cantracksatellite == '1' || t.cantracksatellite == '0') ) msg += '是否支持跟踪卫星未选择<br>';
                if ( !(t.canconfigproperty == '1' || t.canconfigproperty == '0') ) msg += '是否支持属性设置未选择<br>';
                
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
                if ( !v || v.length < 1 )
                {
                    switch (dev) {
                        case 'ccd':
                            msg = '请输入ccd Id'; break;
                        case 'filter':
                            msg = '请输入滤光片转轮 ID'; break;
                        case 'guide':
                            msg = '请输入导星镜 ID'; break;
                        case 'oDome':
                            msg = '请输入全开圆顶 ID'; break;
                        case 'sDome':
                            msg = '请输入随动圆顶 ID'; break;
                        case 'focus':
                            msg = '请输入调焦器 ID'; break;
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

                if ( !v || v.length < 1 )
                {
                    switch (dev) {
                        case 'ccd':
                            msg = '请输入ccd名称'; break;
                        case 'filter':
                            msg = '请输入滤光片转轮名称'; break;
                        case 'ccd_sensor':
                            msg = '请输入ccd传感器名称'; break;
                        case 'guide':
                            msg = '请输入导星镜名称'; break;
                        case 'oDome':
                            msg = '请输入全开圆顶名称'; break;
                        case 'sDome':
                            msg = '请输入随动圆顶名称'; break;
                        case 'focus':
                            msg = '请输入调焦器名称'; break;
                    }
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_devName() 结束
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
                
                if ( !$.isNumeric(v) || v < 0 )
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
            check_focusV: function (tip, v, e, v1) {
                var msg = '';
                
                if ( !$.isNumeric(v) || v <= 0 )
                {
                    msg = '调焦器最大值输入有误';
                    if ( v1 !== undefined ) msg = '调焦器最小值输入有误';
            
                }

                if ( v1 !== undefined && v1 < v*1 )
                {
                    msg = '调焦器最小值输入有误';
            
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
				}
				return msg !== '' ? msg + '<br>' : '';
            },//check_focusV() 结束
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
            gain_noise_sort:function (dev, order) {//ajax请求ccd增益、噪声值显示表格的各字段排序,通过php对数据排序，暂被gain_noise_sort1()替代
                var that = this; //存储vue的实例

                $.ajax({
                    type: 'post',
                    url: 'gainNoiseSort',
                    data : {
                        field: dev,
                        order: order,
                        teleid: this.show_dev_form.teleid,
                        ccdno: this.show_dev_form.ccd_no,
                    },
                    success:  function (info) {
                        if ( info.indexOf('{') == -1 ) //info 不是json数据
                        {
                            layer.alert(info, {
                                shade:0,
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

                            //将排序后的gain_noise字段数据转为js对象后赋给that.gain_noise
                            that.gain_noise = info;
                        }
                     },/*success方法结束 */
                     error:  function () {
                          layer.alert('网络异常,请重新提交', {shade:0, closeBtn:0,});
                     }
                })//ajax结束
            },//gain_noise_sort 结束
            gain_noise_sort1:function (v, order){//对ccd增益-噪声值各字段排序
                //首先看表格中行数是否大于2，小于2则不用排序
                if ( this.rows < 3 )
                {
                    layer.alert('无须排序或缺少数据', {shade:0,closeBtn:0}); return;
                }
                //开始冒泡排序
                switch (v)
                {
                    case 'readOut_speed': //对读出速度排序
                        if ( order === 'asc' ) //升序
                        {
                            this.sort_field (this.rows, 'readOut_speed', 'asc');
                        }else if ( order === 'desc' ) //降序
                        {
                            this.sort_field (this.rows, 'readOut_speed', 'desc');
                        }
                        break;
                    case 'transfer_speed': //对转移速度排序
                        if ( order === 'asc' ) //升序
                        {
                            this.sort_field (this.rows, 'transfer_speed', 'asc');
                        }else if ( order === 'desc' ) //降序
                        {
                            this.sort_field (this.rows, 'transfer_speed', 'desc');
                        }
                        break;
                    case 'gain_gear': //对增益挡位排序
                        if ( order === 'asc' ) //升序
                        {
                            this.sort_field (this.rows, 'gain_gear', 'asc');
                        }else if ( order === 'desc' ) //降序
                        {
                            this.sort_field (this.rows, 'gain_gear', 'desc');
                        }
                        break;
                    case 'gainVal': //对增益值排序
                        if ( order === 'asc' ) //升序
                        {
                            this.sort_field (this.rows, 'gainVal', 'asc');
                        }else if ( order === 'desc' ) //降序
                        {
                            this.sort_field (this.rows, 'gainVal', 'desc');
                        }
                        break;
                    case 'noiseVal': //对噪声值排序
                        if ( order === 'asc' ) //升序
                        {
                            this.sort_field (this.rows, 'noiseVal', 'asc');
                        }else if ( order === 'desc' ) //降序
                        {
                            this.sort_field (this.rows, 'noiseVal', 'desc');
                        }
                        break;
                }
            },//gain_noise_sort1() 结束
            sort_field:function (rows, v, order){//对增益-噪声值的某列进行排序
                var temp;
                if ( order === 'asc' ) //升序
                {
                    for (let i = 1; i < rows; i++)
                    {
                        for (let j = 1; j <= rows-i; j++)
                        {
                            if ( this.gain_noise[j][v] > this.gain_noise[j+1][v] )
                            {
                                temp = this.gain_noise[j];
                                this.gain_noise[j] = this.gain_noise[j+1];
                                this.gain_noise[j+1] = temp;
                            }
                        }                    
                    }
                    this.update_gain_noise++; //跟新vue的一个数据,跟新vue的一个数据,此数据绑定到页面最后a元素的id,作用是更新页面
                }else if (  order === 'desc') //降序
                {
                    for (let i = 1; i < rows; i++)
                    {
                        for (let j = 1; j <= rows-i; j++)
                        {
                            if ( this.gain_noise[j][v] < this.gain_noise[j+1][v] )
                            {
                                temp = this.gain_noise[j];
                                this.gain_noise[j] = this.gain_noise[j+1];
                                this.gain_noise[j+1] = temp;
                            }
                        }                    
                    }
                    this.update_gain_noise++; //跟新vue的一个数据,此数据绑定到页面最后a元素的id,作用是更新页面
                }
            },//sort_field 结束
            gain_noise_sbmt:function () {//提交保存 增益-噪声值到ccdconf表中的gain_noise字段内
                var that = this; //存储vue的实例
                var msg = '';
           
                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:0,closeBtn:0});return;
                }

                //根据this.rows值 循环验证每条增益值、读出噪声值
                for (let i = 1; i <= this.rows; i++)
                {
                    msg += this.check_gain_noise(false, i, 1); //验证增益值
                    msg += this.check_gain_noise(false, i, 2); //验证噪声值
                }

                if ( msg !== '' )
                {
                    layer.alert(msg, {shade:0,closeBtn:0});return;
                }
                
                //接下来将读出速度，转移速度，增益模式，增益挡位加入要提交的表单数据中
                var tempData = {}; //存储 读出速度，转移速度，增益模式，增益挡位
                if ( this.readoutspeed.length > 0 ) //有读出速度
                {
                    tempData.readOut = {};
                    for (let i = 0; i < this.readoutspeed.length; i++)
                    {
                        tempData.readOut[i] = this.readoutspeed[i];
                    }
                }

                if ( this.transferspeed.length > 0 ) //有转移速度
                {
                    tempData.transfer = {};
                    for (let i = 0; i < this.transferspeed.length; i++)
                    {
                        tempData.transfer[i] = this.transferspeed[i];
                    }
                }

                if ( this.gainMode.length > 0 ) //有增益模式
                {
                    tempData.gain = {};
                    for (let i = 0; i < this.gainMode.length; i++)
                    {
                        tempData.gain[i] = this.gainMode[i];
                    }
                }

                if ( this.gain_num ) //有增益挡位
                {
                    tempData.gear = this.gain_num;
                }
                //将读出速度，转移速度，增益模式，增益挡位加入要提交的表单数据中 结束
                $.ajax({
                    type: 'post',
                    url: 'gain_noise',
                    data : {
                        gain_noise: that.gain_noise,
                        teleid: this.show_dev_form.teleid,
                        ccdno: this.show_dev_form.ccd_no,
                        fourData: tempData
                    },
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
                          layer.alert('网络异常,请重新提交', {shade:0, closeBtn:0,});
                     }
                })/*ajax 结束*/
            },//gain_noise_sbmt() 结束
            ccd_sbmt:function () {
                var that = this; //存储vue的实例
                var t = this.ccd_config;
                var msg = '';
           
                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:false,closeBtn:0});return;
                }
                
                msg += this.check_ip(false, t.ip, this.$refs.ccdIp);
                msg += this.check_devId(false, t.ccdid, this.$refs.ccdId, "ccd");
                msg += this.check_devName(false, t.name, this.$refs.ccdName, "ccd");
                msg += this.check_pixel(false, t.xpixel, this.$refs.ccdXpixel, "x");
                msg += this.check_pixel(false, t.ypixel, this.$refs.ccdYpixel, "y");
                msg += this.check_pixel_size(false, t.xpixelsize, this.$refs.ccdXpixelSize, "x");
                msg += this.check_pixel_size(false, t.ypixelsize, this.$refs.ccdYpixelSize, "y");
                msg += this.check_devName(false, t.sensorname, this.$refs.ccdSensorName, "ccd_sensor");
                msg += this.check_coolT(false, t.lowcoolert, this.$refs.ccdLowCoolerT);
                msg += this.check_maxExposTime(false, t.maxexposuretime, this.$refs.ccdMaxExposureTime);
                msg += this.check_maxExposTime(false, t.minexposuretime, this.$refs.ccdMinExposureTime, t.maxexposuretime);
                msg += this.check_intV(false, t.exposuretimeration, this.$refs.exposureTimeRation, 1);
                msg += this.check_intV(false, t.fullwelldepth, this.$refs.fullWellDepth, 2);
                msg += this.check_emV(false, t.emmaxvalue, this.$refs.emMaxValue);
                msg += this.check_emV(false, t.emminvalue, this.$refs.emMinValue, t.emmaxvalue);
                msg += this.check_version(false, t.attrversion, this.$refs.ccd_version, 2);

                if ( t.type == '0' )              msg += '探测器类型未选择<br>';
                if ( t.imagebits.length < 1 )     msg += '图像位数未选择<br>';
                if ( t.coolermode == '0' )        msg += '制冷方式未选择<br>';
                if ( t.bin.length < 1 )           msg += 'Bin未选择<br>';
                if ( t.readoutmode.length < 1 )   msg += '读出模式未选择<br>';
                if ( t.shuttertype == '0' )       msg += '快门类型未选择<br>';
                if ( t.shuttermode.length < 1)    msg += '快门模式未选择<br>';
                if ( !(t.issupportfullframe == '1' || t.issupportfullframe == '0') ) msg += '支持帧转移未选择<br>';
                if ( !(t.issupportem == '1' || t.issupportem == '0') )   msg += '支持EM未选择<br>';
                if ( !(t.issupportscmosnoisefilter == '1' || t.issupportscmosnoisefilter == '0') ) msg += '支持CMOS noise filter未选择<br>';
                if ( !(t.issupportbaseline == '1' || t.issupportbaseline == '0') ) msg += '支持base line未选择<br>';
                if ( !(t.issupportoverscan == '1' || t.issupportoverscan == '0') ) msg += '支持Over scan未选择<br>';
                if ( !(t.issupportroi == '1' || t.issupportroi == '0') )   msg += '支持开窗未选择<br>';
                if ( t.interfacetype.length < 1 )     msg += '接口类型未选择<br>';
                if ( t.exposetriggermode.length < 1 ) msg += '曝光触发模式未选择<br>';
                if ( !(t.canconnect == '1' || t.canconnect == '0') )     msg += '支持连接未选择<br>';
                if ( !(t.cansetcoolert == '1' || t.cansetcoolert == '0') )  msg += '设置制冷温度未选择<br>';
                if ( !(t.cansetexposureparam == '1' || t.cansetexposureparam == '0') ) msg += '设置曝光策略未选择<br>';
                if ( !(t.canstartexposure == '1' || t.canstartexposure == '0') )   msg += '支持开始曝光未选择<br>';
                if ( !(t.canstopexposure == '1' || t.canstartexposure == '0') )    msg += '支持停止曝光未选择<br>';
                if ( !(t.canabortexposure == '1' || t.canabortexposure == '0') )   msg += '支持终止曝光未选择<br>';
                if ( !(t.cansetgain == '1' || t.cansetgain == '0') )     msg += '设置增益未选择<br>';
                if ( !(t.cansetreadoutspeedmode == '1' || t.cansetreadoutspeedmode == '0') ) msg += '设置读出速度模式未选择<br>';
                if ( !(t.cansettransferspeedmode == '1' || t.cansettransferspeedmode == '0') ) msg += '设置转移速度模式未选择<br>';
                if ( !(t.cansetbin == '1' || t.cansetbin == '0') ) msg += '设置BIN未选择<br>';
                if ( !(t.cansetroi == '1' || t.cansetroi == '0') ) msg += '设置ROI未选择<br>';
                if ( !(t.cansetshutter == '1' || t.cansetshutter == '0') ) msg += '设置快门未选择<br>';
                if ( !(t.cansetfullframe == '1' || t.cansetfullframe == '0') ) msg += '设置帧转移未选择<br>';
                if ( !(t.cansetem == '1' || t.cansetem == '0') ) msg += '设置EM未选择<br>';
                if ( !(t.cannoisefilter == '1' || t.cannoisefilter == '0') ) msg += '设置CMOS noise filter未选择<br>';
                if ( !(t.cansetbaseline == '1' || t.cansetbaseline == '0') ) msg += '设置Base line未选择<br>';
                if ( !(t.cansetoverscan == '1' || t.cansetoverscan == '0') ) msg += '设置Over scan未选择<br>';

                if ( msg !== '' )
                {
                    layer.alert(msg, {shade:0,closeBtn:0});return;
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
                var ipv4 = /^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/;
                var ipv6 = /^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/;
                if ( !ipv4.test(v) && !ipv6.test(v) ) //既不满足ipv4,也不满足ipv6
                {
                    msg = 'ip输入有误';
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
                }
                
				return msg !== '' ? msg + '<br>' : '';
            },//check_ip() 结束
            check_readOut:function(tip, v, n){//验证读出速度
                var msg = '';
                var ele, v1;
                switch (n) {
                    case 1:
                        ele = 'readSpd'+v;
                        v1 = this.readoutspeed[v-1];
        
                        if ( !$.isNumeric(v1) || v1 <= 0 )
                        {
                            msg = '读出速度输入有误';
                            this.readoutspeed[v-1] = '';
                        }
        
                        break;
                    case 2:
                        ele = 'transSpd'+v;
                        v1 = this.transferspeed[v-1];
        
                        if ( !$.isNumeric(v1) || v1 <= 0 )
                        {
                            msg = '转移速度输入有误';
                            this.transferspeed[v-1] = '';
                        }

                        break;
                }

                if ( tip===true && msg !== '' )
                {
                    layer.tips(msg, this.$refs[ele]);
                }

				return msg !== '' ? msg + '<br>' : '';
            },//check_readOut 结束
            check_gain_noise:function (tip, k, n){//验证增益值或读出噪声值
                var msg = '';
                var ele, v;
                switch (n) {
                    case 1:
                        ele = 'gainV'+k;
                        v = this.gain_noise[k].gainVal;
    
                        if ( !$.isNumeric(v) || v < 0 )
                        {
                            msg = '第' + k + '行增益值输入有误';
                        }
        
                        break;
                    case 2:
                        ele = 'noiseV'+k;
                        v = this.gain_noise[k].noiseVal;
        
                        if ( !$.isNumeric(v) || v < 0 )
                        {
                            msg = '第' + k + '行读出噪声值输入有误';
                        }
        
                        break;
                }
               
                if ( tip===true && msg !== '' )
                {
                    layer.tips(msg, this.$refs[ele]);
                }

				return msg !== '' ? msg + '<br>' : '';
            },//check_gain_noise 结束
            check_num: function (tip, v, e, dev) {//验证各设备 数字类型的文本框
                var msg = '';
                
                if ( !$.isNumeric(v) || v <= 0 )
                {
                    switch (dev) {
                        case 'guide':
                            msg = '导星镜口径输入有误'; break;
                        case 'oDome_D':
                            msg = '全开圆顶尺寸输入有误'; break;
                        case 'sDome_speed':
                            msg = '随动圆顶最大转动速度输入有误'; break;
                        case 'sDome_D':
                            msg = '随动圆顶尺寸输入有误'; break;
                        case 'focus_I':
                            msg = '调焦器分辨率输入有误'; break;
                        case 'focus_S':
                            msg = '调焦器最大速度输入有误'; break;
                        case 'readOut':
                            msg = '读出速度输入有误'; break;
                    }
                }

                if ( tip===true && msg !== '' )
				{
					layer.tips(msg, e);
                }
                
				return msg !== '' ? msg + '<br>' : '';
            },//check_num() 结束
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
                        //this.filter_slot.slotData[i] = {filterType:'0', filterName:'', filterComp:''};
                        if ( !this.filter_slot.slotData[i] ) this.filter_slot.slotData[i] = {filterType:'0', filterName:'', filterComp:''};
                    }
                }
				return msg !== '' ? msg + '<br>' : '';
            },//check_slot() 结束
            check_filterSize:function (tip, v, e) {//验证插槽大小
                var msg = '';
                var patn = /^\d+\.?\d? \d+\.?\d? \d+\.?\d?$/;
                
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
                var patn = /([\u4e00-\u9fa5]| )+/; //不能有汉字和空格
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

				if ( !patn.test(v) || v < 0 ) //可以为零
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
                var t = this.filter_config;
                var msg = '';

                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:0, closeBtn:0});return;
                }

                msg += this.check_ip(false, t.ip, this.$refs.filterIp);
                msg += this.check_devId(false, t.filterid, this.$refs.filterId, 'filter');
                msg += this.check_devName(false, t.name, this.$refs.filterName, 'filter');
                msg += this.check_slot(false, t.numberoffilter, this.$refs.filterNum);
                msg += this.check_filterSize(false, t.filtersize, this.$refs.filterSize);

                var slot_num = t.numberoffilter;

                if ( slot_num > 1 ) //如果插槽数目有值
                {
                    for (let i = 1; i <= slot_num; i++)
                    {
                        if ( this.filter_slot.slotData[i].filterType == '0' ) msg += '插槽' + i + ':滤光片类型未选择<br>';
                        msg += this.check_filterName(false, i);
                        msg += this.check_filterComp(false, i);
                    }
                }

                if ( !(t.cansetfilterposition == '1' || t.cansetfilterposition == '0') ) msg += '设置滤光片位置未选择<br>';
                if ( !(t.canconnect == '1' || t.canconnect == '0') ) msg += '支持连接未选择<br>';
                if ( !(t.canfindhome == '1' || t.canfindhome == '0') ) msg += '支持找零未选择<br>';
                if ( t.filtershape === '0' ) msg += '滤光片形状未选择<br>';
                msg += this.check_version(false, t.attrversion, this.$refs.filterAttrVersion, 3);

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
            oDome_sbmt: function (){//提交全开圆顶配置
                var that = this; //存储vue的实例
                var t = this.oDome_config;
                var msg = '';

                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:0, closeBtn:0});return;
                }

                msg += this.check_ip(false, t.ip, this.$refs.oDomeIp);
                msg += this.check_devId(false, t.odomeid, this.$refs.oDomeId, 'oDome');
                msg += this.check_devName(false, t.name, this.$refs.oDomeName, 'oDome');
                if ( t.type === '0' ) msg += '全开圆顶类型未选择<br>';
                msg += this.check_num(false, t.diameter, this.$refs.oDomeDiameter, 'oDome_D');
                if ( !(t.canopendome == '1' || t.canopendome == '0') ) msg += '支持打开圆顶未选择<br>';
                msg += this.check_version(false, t.attrversion, this.$refs.odomeAttrversion, 5);
                if ( !(t.canconnect == '1' || t.canconnect == '0') ) msg += '支持连接未选择';
                
                if ( msg !== '')
                {
                    layer.alert(msg, {shade:0, closeBtn:0}); return;
                }

                var oDomeData = new FormData (this.$refs.oDome);
                oDomeData.append('teleid', this.show_dev_form.teleid); //将望远镜Id 提交上去

                $.ajax({
                    type: 'post',
                    url: 'oDome_config',
                    data : oDomeData,
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
        
                            that.oDome_config.attrmodifytime = info.attrmodifytime; //显示属性更新时间             

                            if (info.file) that.oDome_file = info.file;
                        }//解析 处理 json 结束
                     },//success方法结束 
                     error:  function () {
                          layer.alert('网络异常,请重新提交', {shade:0, closeBtn:0,});
                     }
                })//ajax 结束
            },//oDome_sbmt() 结束
            sDome_sbmt:function (){//提交随动圆顶配置数据
                var that = this; //存储vue的实例
                var t = this.sDome_config;
                var msg = '';

                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:0, closeBtn:0});return;
                }

                msg += this.check_ip(false, t.ip, this.$refs.sDomeIp);
                msg += this.check_devId(false, t.sdomeid, this.$refs.sDomeId, 'sDome');
                msg += this.check_devName(false, t.name, this.$refs.sDomeName, 'sDome');
                if ( t.dometype === '0' ) msg += '随动圆顶类型未选择<br>';
                msg += this.check_num(false, t.maxspeed, this.$refs.sDomeMaxSpeed, "sDome_speed");
                msg += this.check_num(false, t.diameter, this.$refs.sDomeDiameter, "sDome_D");
                if ( !(t.hasshade == '1' || t.hasshade == '0') ) msg += '有无风帘未选择<br>';
                if ( !(t.cansetdomepositin == '1' || t.cansetdomepositin == '0') ) msg += '设置目标方位未选择<br>';
                if ( !(t.cansetshadeposition == '1' || t.cansetshadeposition == '0') ) msg += '设置风帘位置未选择<br>';
                if ( !(t.cansetrotatespeed == '1' || t.cansetrotatespeed == '0') ) msg += '设置转动速度未选择<br>';
                if ( !(t.canstop == '1' || t.canstop == '0') ) msg += '支持停止运动未选择<br>';
                if ( !(t.canopenshutter == '1' || t.canopenshutter == '0') ) msg += '支持打开天窗未选择<br>';
                if ( !(t.cansetshadespeed == '1' || t.cansetshadespeed == '0') ) msg += '控制风帘运动未选择<br>';
                if ( !(t.canconnect == '1' || t.canconnect == '0') ) msg += '支持连接未选择<br>';
                msg += this.check_version(false, t.attrversion, this.$refs.sDome_version, 6);
                if ( msg !== '')
                {
                    layer.alert(msg, {shade:0, closeBtn:0}); return;
                }

                var sDomeData = new FormData (this.$refs.sDome);
                sDomeData.append('teleid', this.show_dev_form.teleid); //将望远镜Id 提交上去

                $.ajax({
                    type: 'post',
                    url: 'slaveDome_config',
                    data : sDomeData,
                    processData : false,
                    contentType : false,
                    success:  function (info) {
                        if ( info.indexOf('{') == -1 ) //info 不是json数据
                        {
                            layer.alert(info, {
                                shade:0,
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
                                shade:0,
                                closeBtn:0,
                                yes:function (n){
                                    layer.close(n);
                                },
                            });
        
                            that.sDome_config.attrmodifytime = info.attrmodifytime; //显示属性更新时间             

                            if (info.file) that.sDome_file = info.file;
                        }//解析 处理 json 结束
                     },//success方法结束 
                     error:  function () {
                          layer.alert('网络异常,请重新提交', {shade:0, closeBtn:0,});
                     }
                })//ajax 结束
            },//sDome_sbmt() 结束
            guide_sbmt:function (){//提交导星镜配置数据
                var that = this; //存储vue的实例
                var t = this.guide_config;

                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:0,closeBtn:0});return;
                }

                var msg = '';
                msg += this.check_ip(false, t.ip, this.$refs.guideScopeIp);
                msg += this.check_devId(false, t.guidescopeid, this.$refs.guideScopeId, 'guide');
                msg += this.check_devName(false, t.name, this.$refs.guideScopeName, "guide");
                msg += this.check_num(false, t.aperture, this.$refs.guideScopeAperture, "guide");
                if ( !(t.canopencover == '1' || t.canopencover == '0') ) msg += '支持镜盖操作未选择<br>';
                if ( !(t.canconnect == '1' || t.canconnect == '0') ) msg += '支持连接未选择<br>';
                if ( !(t.hasmirrorcover == '1' || t.hasmirrorcover == '0') ) msg += '有无镜盖未选择<br>';
                if ( !(t.issupportautofocus == '1' || t.issupportautofocus == '0') ) msg += '支持自动调焦未选择<br>';
                if ( !(t.canenableautofocus == '1' || t.canenableautofocus == '0') ) msg += '使能自动调焦未选择<br>';
                msg += this.check_version(false, t.attrversion, this.$refs.guideAttrVersion, 4);
                if ( this.guide_focus.focustype.length == 0 )  msg += '焦点类型未选择<br>';
                
                //接下来验证：每个被选择的焦点类型，其焦距是否输入正确
                if ( this.guide_focus.focustype.length > 0 ) //焦点类型已被选择
                {
                    $.each( this.confOption.opticalStructure, function(i, v) //jquery遍历
                    {
                        if ( $.inArray(v, vm.guide_focus.focustype ) !== -1 ) //confOption.opticalStructure中第i个元素被选择
                        {
                            msg += vm.check_guideFocusL(false, i);
                        }
                    });
                }//验证：每个被选择的焦点类型，其焦距是否输入正确 结束
                
                if ( msg !== '' )
                {
                    layer.alert(msg, {shade:false, closeBtn:0});return;
                }
                
                var guide_Data = new FormData (this.$refs.guideScope);
                guide_Data.append('teleid', this.show_dev_form.teleid); //将望远镜Id 提交上去
                
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
                                shade:0,
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
        
                            that.guide_config.attrmodifytime = info.attrmodifytime; //显示属性更新时间             

                            if (info.file) that.guide_file = info.file;
                        }//解析 处理 json 结束
                     },/*success方法结束 */
                     error:  function () {
                          layer.alert('网络异常,请重新提交', {shade:0, closeBtn:0,});
                     }
                })//ajax 结束
            },//guide_sbmt() 结束
            focus_sbmt:function (){//提交调焦器配置数据
                var that = this; //存储vue的实例
                var t = this.focus_config;

                if ( this.show_dev_form.teleid == '0' )  //未选择某个望远镜
                {
                    layer.alert('请选择您要配置的望远镜!', {shade:0,closeBtn:0});return;
                }

                var msg = '';
                msg += this.check_ip(false, t.ip, this.$refs.focusIp);
                msg += this.check_devId(false, t.focusid, this.$refs.focusId, 'focus');
                msg += this.check_devName(false, t.name, this.$refs.focusName, "focus");
                msg += this.check_focusV(false, t.maxvalue, this.$refs.focusMaxValue);
                msg += this.check_focusV(false, t.minvalue, this.$refs.focusMinValue, t.maxvalue);
                msg += this.check_num(false, t.increment, this.$refs.focusIncrement, "focus_I");
                msg += this.check_num(false, t.maxspeed, this.$refs.focusMaxSpeed, "focus_S");
                if ( !(t.canconnect == '1' || t.canconnect == '0') ) msg += '支持连接未选择<br>';
                if ( !(t.canfindhome == '1' || t.canfindhome == '0') ) msg += '支持找零未选择<br>';
                if ( !(t.cantemperturecompensate == '1' || t.cantemperturecompensate == '0') ) msg += '进行温度补偿未选择<br>';
                if ( !(t.cansetposition == '1' || t.cansetposition == '0') ) msg += '设置目标位置未选择<br>';
                if ( !(t.cansetspeed == '1' || t.cansetspeed == '0') ) msg += '设置恒速运动未选择<br>';
                if ( !(t.canstop == '1' || t.canstop == '0') ) msg += '支持停止运动未选择<br>';
                if ( !(t.canenabletemperturecompensate == '1' || t.canenabletemperturecompensate == '0') ) msg += '使能温度补偿未选择<br>';
                if ( !(t.cansettemperturecompensatecoefficient == '1' || t.cansettemperturecompensatecoefficient == '0') ) msg += '设置温度补偿系数未选择<br>';
                msg += this.check_version(false, t.attrversion, this.$refs.focusAttrVersion, 7);
                
                if ( msg !== '' )
                {
                    layer.alert(msg, {shade:false, closeBtn:0});return;
                }
                
                var focus_Data = new FormData (this.$refs.focus);
                focus_Data.append('teleid', this.show_dev_form.teleid); //将望远镜Id 提交上去
                
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
                                shade:0,
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
        
                            that.focus_config.attrmodifytime = info.attrmodifytime; //显示属性更新时间             

                            if (info.file) that.focus_file = info.file;
                        }//解析 处理 json 结束
                     },/*success方法结束 */
                     error:  function () {
                          layer.alert('网络异常,请重新提交', {shade:0, closeBtn:0,});
                     }
                })//ajax 结束
            },//focus_sbmt() 结束
        },//methods 结束
    });//vue 结束////////////////////
})/*jquery 初始化函数 末尾*/