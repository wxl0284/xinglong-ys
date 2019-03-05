/*添加望远镜页面 js*/
$(function () {
    //提交新增望远镜的数据
    var atForm = $('#form');
    //var atId = atForm.find('input[name="atid"]'); //望远镜id
    var atName = atForm.find('input[name="atname"]'); //望远镜名称
    var address = atForm.find('input[name="address"]'); //望远镜地址
    var longitude = atForm.find('input[name="longitude"]'); //经度
    var latitude = atForm.find('input[name="latitude"]'); //纬度
    var altitude = atForm.find('input[name="altitude"]'); //海拔
    var aperture = atForm.find('select[name="aperture"]'); //口径

    //各input框的blur
    //验证望远镜id 之blur
    /*atId.blur(function () {
        var v = $.trim($(this).val());
		//var patn = /0\d{4}/;
		var err = 0;
		
		if ( v.length != 5 || !$.isNumeric(v) || v.indexOf('0') !== 0)
		{
			err = 1;
			layer.tips('望远镜id格式错误!', $(this), {tipsMore: true});
		}		
		$(this).data('err', err);
    });//验证望远镜id 之blur 结束

    *///验证望远镜名 之blur 结束

    //验证望远镜名 之blur
    atName.blur(function () {
        let t = $(this);
        let v = $.trim(t.val());
		//var patn = /^\d(\d|\.)*m望远镜+$/;
		let err = 0;
		
		if ( v.length < 2 )
		{
			err = 1;
			layer.tips('名称须不少于2个字符!', t, {tipsMore: true});
		}		
		t.data('err', err);
    });

     //验证望远镜地址 之blur
     address.blur(function () {
        let t = $(this);
        let v = $.trim(t.val());
		let err = 0;
		
		if ( v.length < 2 )
		{
			err = 1;
			layer.tips('地址须不少于2个字符!', t, {tipsMore: true});
		}		
		t.data('err', err);
    });//验证望远镜地址 之blur 结束

    //验证望远镜经度 之blur
    longitude.blur(function () {
        let t = $(this);
        let v = $.trim(t.val());
        //v1 = v.replace('°', ''); //将字符°替换为空串
		let err = 0;
		
		if ( !$.isNumeric(v) || v > 180 || v < -180 )
		{
			err = 1;
			layer.tips('经度须在-180和180之间!', t, {tipsMore: true});
		}		
		t.data('err', err);
    });//验证望远镜经度 之blur 结束

    //验证望远镜纬度 之blur
    latitude.blur(function () {
        let t = $(this);
        let v = $.trim(t.val());
        //v1 = v.replace('°', ''); //将字符°替换为空串
		let err = 0;
		
		if ( !$.isNumeric(v) || v > 90 || v < -90 )
		{
			err = 1;
			layer.tips('纬度须在-90和90之间!', t, {tipsMore: true});
		}		
		t.data('err', err);
    });//验证望远镜纬度 之blur 结束

    //验证望远镜海拔 之blur
    altitude.blur(function () {
        let t = $(this);
        let v = $.trim(t.val());
        //v1 = v.replace('m', ''); //将字符m 替换为空串
		let err = 0;
		
		if ( !$.isNumeric(v) || v > 6000 || v < -1000 )
		{
			err = 1;
			layer.tips('海拔须在-1000和6000之间!', t, {tipsMore: true});
		}		
		t.data('err', err);
    });//验证望远镜海拔 之blur 结束

    //验证望远镜口径 之blur
    aperture.blur(function () {
        let t = $(this);
        let v = $.trim(t.val());
        //v1 = v.replace('cm', ''); //将字符m 替换为空串
		let err = 0;
		
		if ( v == '0' )
		{
			err = 1;
			layer.tips('口径不能为空!', t, {tipsMore: true});
		}		
		t.data('err', err);
    });//验证望远镜口径 之blur 结束

    //提交按钮 点击事件
    atForm.find('input[type="button"]').click(function () {
        let t = $(this);
        let err = 0; //输入错误标识
        //验证每个input输入框
        let input = atForm.find('input[type="text"], select');//查找input和select
        
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
            url : '/at_doadd',
            data : formData,
            processData : false,
            contentType : false,  
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
                        if (info.indexOf('添加望远镜ok') !== -1)
                        {
                            location.href='/atadd';
                        }
                    },
                });
           },
           error: function () {
               layer.alert('网络异常,请重新提交', {shade:false, closeBtn:0});
           },
        });
    })//提交按钮 点击事件  结束

})