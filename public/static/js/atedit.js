/*编辑望远镜页面 js*/
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
    //提交望远镜的数据
    var atForm = $('#form');
    var atName = atForm.find('input[name="atname"]'); //望远镜名称
    var address = atForm.find('input[name="address"]'); //望远镜地址
    var longitude = atForm.find('input[name="longitude"]'); //经度
    var latitude = atForm.find('input[name="latitude"]'); //纬度
    var altitude = atForm.find('input[name="altitude"]'); //海拔
    var aperture = atForm.find('input[name="aperture"]'); //口径

    //各input框的blur
    //验证望远镜名 之blur
    atName.blur(function () {
        var v = $.trim($(this).val());
		var patn = /\d+望远镜/;
		var err = 0;
		
		if ( !patn.test(v) )
		{
			err = 1;
			layer.tips('望远镜名格式错误!', $(this), {tipsMore: true});
		}		
		$(this).data('err', err);
    });//验证望远镜名 之blur 结束

     //验证望远镜地址 之blur
     address.blur(function () {
        var v = $.trim($(this).val());
		var err = 0;
		
		if ( v.length < 2 )
		{
			err = 1;
			layer.tips('望远镜地址格式错误!', $(this), {tipsMore: true});
		}		
		$(this).data('err', err);
    });//验证望远镜地址 之blur 结束

    //验证望远镜经度 之blur
    longitude.blur(function () {
        var v = $.trim($(this).val());
        v1 = v.replace('°', ''); //将字符°替换为空串
		var err = 0;
		
		if ( !$.isNumeric(v1) || v1 > 180 || v1 < -180 || v.indexOf('°') == -1)
		{
			err = 1;
			layer.tips('经度格式错误!', $(this), {tipsMore: true});
		}		
		$(this).data('err', err);
    });//验证望远镜经度 之blur 结束

    //验证望远镜纬度 之blur
    latitude.blur(function () {
        var v = $.trim($(this).val());
        v1 = v.replace('°', ''); //将字符°替换为空串
		var err = 0;
		
		if ( !$.isNumeric(v1) || v1 > 90 || v1< -90 || v.indexOf('°') == -1)
		{
			err = 1;
			layer.tips('纬度格式错误!', $(this), {tipsMore: true});
		}		
		$(this).data('err', err);
    });//验证望远镜纬度 之blur 结束

    //验证望远镜海拔 之blur
    altitude.blur(function () {
        var v = $.trim($(this).val());
        v = v.toLowerCase(); //转为小写
        v1 = v.replace('m', ''); //将字符m 替换为空串
		var err = 0;
		
		if ( !$.isNumeric(v1) || v1 > 6000 || v1 < -1000 || v.indexOf('m') == -1)
		{
			err = 1;
			layer.tips('海拔格式错误!', $(this), {tipsMore: true});
		}		
		$(this).data('err', err);
    });//验证望远镜海拔 之blur 结束

    //验证望远镜口径 之blur
    aperture.blur(function () {
        var v = $.trim($(this).val());
        v = v.toLowerCase(); //转为小写
        v1 = v.replace('cm', ''); //将字符m 替换为空串
		var err = 0;
		
		if ( !$.isNumeric(v1) || v.indexOf('cm') == -1)
		{
			err = 1;
			layer.tips('口径格式错误!', $(this), {tipsMore: true});
		}		
		$(this).data('err', err);
    });//验证望远镜口径 之blur 结束

    //提交按钮 点击事件
    atForm.find('input[type="button"]').click(function () {
        var err = 0; //输入错误标识
        //验证每个input输入框
        var input = atForm.find('input[type="text"]');
        input.each(function () {
			$(this).blur();
			err += $(this).data('err');
        });
   
		if (err > 0){
			return;  //指令输入有误 不提交
		}
        var formData = new FormData(atForm[0]); //转为js-dom对象
    
        $.ajax ({
            type: 'post',
            url : '/at_doedit',
            data : formData,
            processData : false,
            contentType : false,  
            success:  function (info) {
                alert(info);
                if (info.indexOf('登录') !== -1)
                {
                    location.href = '/';
                }else if(info.indexOf('编辑望远镜信息ok') !== -1){
                    location.href = '/atlist';
                }
           },
           error: function () {
               layer.alert('网络异常,请重新提交');
           },
        });
    })//提交按钮 点击事件  结束

})