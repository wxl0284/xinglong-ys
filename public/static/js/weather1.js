//气象详情页js/////////////////////////////////////////
$(function (){
	//云量图 显示更多 按钮 hover///////////////////
	$('a.border').hover(
        function (){
			$(this).css("color", '#fff');
			//$(this).css("height", '45px');
            $(this).addClass("hover");
        }, 
        function (){
			$(this).css("color", '#7A7A7A');
            $(this).removeClass("hover");
        }
   );
   
 //ajax 1分钟请求一次气象信息
 var sec = $('#sec');
 var temperature = $('#temperature');
 var humidity = $('#humidity');
 var dewpoint = $('#dewpoint');
 var pressure = $('#pressure');
 var windspeed = $('#windspeed');
 var windspeed2 = $('#windspeed2');
 var windspeed10 = $('#windspeed10');
 var winddirection = $('#winddirection');
 var rainfall = $('#rainfall');
 var night_light = $('#night_light');

 function get_weather ()
 {
    $.ajax({
        url: '/get_weater',
        type: 'post',
        success: function (info) {
            if ( info.indexOf('获取失败') !== -1 )
            {
                alert(info);
            }else{
                info = $.parseJSON (info);
                
                sec.html(info.sec);
                temperature.html(info.temperature);
                humidity.html(info.humidity);
                dewpoint.html(info.dewpoint);
                pressure.html(info.pressure);
                windspeed.html(info.windspeed);
                windspeed2.html(info.windspeed2);
                windspeed10.html(info.windspeed10);
                winddirection.html(info.winddirection);
                rainfall.html(info.rainfall);
                night_light.html(info.night_light);
            }
        },//success() 结束
    });//ajax结束
 } //get_weather() 结束
 setInterval (get_weather, 1000*60); //1分钟刷新一次页面
//ajax 1分钟请求一次气象信息 结束  

 /*每5分钟请求一次云量图片数据*/
 var cloud_pic = $('#cloud_pic'); //图片的img元素
 var cloud_pic_time = $('#cloud_pic_time'); //时间显示

 function get_cloud_pic ()
 {
    $.ajax({
        url: '/get_cloud_pic',
        type: 'post',
        success: function (info){
            if (info != '未获取到')
            {
                let data = eval( "(" + info + ")" );
                cloud_pic.attr('src', '/' + data.file);
                cloud_pic_time.html(data.file_time);
            }
        },//success结束
    })
 } /*每5分钟请求一次云量图片数据 结束*/
  
 setInterval (get_cloud_pic, 1000*300); //5分钟更新一次
})
