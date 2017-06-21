/* 
* 设备1js  at60
*/

 $(function () {
    //接管望远镜按钮 js事件
   /*  var flag = 0;
    $('#takeOver').click(function () {
       var e = $('#takeOver');
       flag ++;
       if (e.val() == '接管望远镜') val = 1;
       if (e.val() == '取消接管')   val = 0;
        $.ajax({
            type : 'post',
            url : '/xinglong/dev1/at60SendMsg',
            data : {takeOver:val,},             
            success:  function (info) {
               alert(info);
                if (flag % 2 == 1)
                {
                    e.val('取消接管');
                }else if(flag % 2 == 0){
                    e.val('接管望远镜');
                }
        
            },
              error:  function () {
               alert('网络异常,请再次点击：接管按钮!');
            },
        });
     
    }); */
    
    //望远镜 子设备导航栏 js事件 ////////////////////////////
    $('#devsNav table').on('click', 'a', function (){
        var btm = $('#devsNav a.borderBtm').not($(this));  //排除自己被多次点击时的情况
        var dev = $(this).attr('name');
        var display = $('div.display');
        
        $(this).addClass('borderBtm');
		btm.removeClass('borderBtm');
        //将对应子设备的信息显示出来
        dev = '#' + dev;
        dev = $(dev);
        display.removeClass('display');
        display.addClass('displayNo');
        dev.removeClass('displayNo');
        dev.addClass('display');
    }); 
    
    //接管望远镜按钮 js事件 
     $('#takeOver').click(function () {
       //var e = $(this);
   
       var t = $('.easyui-datetimespinner');//获取时间输入框
       var v1 = t.first().val();
       var v2 = t.last().val();
       
       if (!v1 || !v2)
       {
           alert('请正确输入:起始/结束时间！');return;
       }
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60TakeOver',
            data : {takeOver:1,},             
            success:  function (info) {
               alert(info);
            },
            error:  function () {
               alert('网络异常,请再次点击：接管按钮!');
            },
        });
     
    });
	
	//转台 连接按钮js事件/////////////////////////////////
    $('#gimbalConnect').click(function () {
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {connect:1},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
              error:  function () {
               alert('网络异常,请再次连接！');
            },
        });
    }); 
	
	//转台 断开连接按钮 js事件/////////////////////////////////
    $('#gimbalDisConnect').click(function () {
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {connect:2},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
              error:  function () {
               alert('网络异常,请再次断开连接！');
            },
        });
    }); 
	
	//转台 找零按钮js事件//////////////////////////////////
    $('#gimbalFindhome').click(function () {
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {findHome:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击：找零按钮!');
            },
        });
    });
    
	//转台 复位按钮 js事件////////////////////////////////////
    $('#gimbalPark').click(function () {
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {park:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次复位!');
            },
        });
    }); 
	 
	//转台 停止按钮 js事件///////////////////////////////////
    $('#gimbalStop').click(function () {
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {stop:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击：停止按钮!');
            },
        });
    });  
	
	//转台 急停按钮 js事件/////////////////////////////////////
    $('#gimbalEmergenceStop').click(function () {
        $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60GimbalSendData',
            data : {EmergenceStop:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击：急停按钮!');
            },
        });
    }); 
	
	//转台 带参数指令  js事件////////////////////////////////////////////////
	var gimbalForm = $('#at60Gimbal');
    var gimbalSelect = gimbalForm.find('div');

    gimbalSelect.click(function () {
         $(this).find('input:radio').prop('checked', true);
         var notcheck = gimbalSelect.not($(this));
         notcheck.addClass('notCheck');
         notcheck.find('input[type="text"]').val('');
         $(this).removeClass('notCheck');
         
     });
     
    //转台 轴3工作模式 仅对模式2有效 js事件/////////////////
	$('#at60Axis3').change(function (){
        var e = $(this).next('span');
       if ($(this).val() == 2)
       {
          e.removeClass('displayNo');
           
       }else{
           e.addClass('displayNo');
       }
    });
    
    //验证转台 表单指令数据
	function checkGimbal ()
	{
		if ($('input[value="1"]').prop('checked')) //验证跟踪恒星 指令数据
		{
			var rightAscension = $('input[name="rightAscension"]').val();
			var declination = $('input[name="declination"]').val();
			var epoch = $('select[name="epoch"]').val();
			var speed = $('select[name="speed"]').val();
			//定义各指令数据格式
			var reg = new RegExp("^[a-zA-Z0-9_]{6,10}$");
			var msg = ''; //定义错误提示
			/* if (数据1未通过) //验证未通过
			{
				msg += '提示信息1\n';
			}
			
			if (数据2未通过){
				msg += '提示信息2\n';
			}
			.....
			if (msg != '')  //有数据错误
			{
				alert(msg);	return false;
			}else{//数据无误
				return true;
			}
			*/
		}else if($('input[value="2"]').prop('checked')){//验证目标名称 
			var objectName = $('input[name="objectName"]').val();
			var objectType = $('select[name="objectType"]').val();
			//定义各指令数据格式
			var reg = new RegExp("^[a-zA-Z0-9_]{6,10}$");
			var msg = ''; //定义错误提示
			/* if (数据1未通过) //验证未通过
			{
				msg += '提示信息1\n';
			}
			
			if (数据2未通过){
				msg += '提示信息2\n';
			}
			.....
			if (msg != '')  //有数据错误
			{
				alert(msg);	return false;
			}else{//数据无误
				return true;
			}
			*/
		}else if($('input[value="3"]').prop('checked')){//验证目标名称 
			var objectName = $('input[name="objectName"]').val();
			var objectType = $('select[name="objectType"]').val();
			//定义各指令数据格式
			var reg = new RegExp("^[a-zA-Z0-9_]{6,10}$");
			var msg = ''; //定义错误提示
			/* if (数据1未通过) //验证未通过
			{
				msg += '提示信息1\n';
			}
			
			if (数据2未通过){
				msg += '提示信息2\n';
			}
			.....
			if (msg != '')  //有数据错误
			{
				alert(msg);	return false;
			}else{//数据无误
				return true;
			}
			*/
		}
	}	//转台指令 验证函数结束
	
    //转台 带参数指令 表单提交//////////////////////////////////
    $('#gimbalSbmt').click(function () {
        var form = $('#at60Gimbal');    //获取转台表单元素
        var formData = new FormData(form[0]);  //将jquery对象转为js-dom对象
		if(true)	//验证数据 通过后执行ajax  checkGimbal()
		{
			//执行ajax
			$.ajax ({
              type: 'post',
              url : '/xinglong/at60/at60GimbalSendData',
              data : formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
                alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
              },
              error:  function () {
               alert('网络异常,请重新提交');
              },
        	});
		}
        
     
    });
    
   //转台 表单提交按钮 hover //////////////////////////////////
   $('#gimbalSbmt').hover(
        function (){
            $(this).addClass("hover");
        }, 
        function (){
            $(this).removeClass("hover");
        }
   );
   
   //CCD 连接按钮 js事件///////////////////////////////////
   $('#ccdConnect').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60CcdSendData',
            data : {ccdConnect:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次连接ccd!');
            },
        });
   });
   
   //CCD 断开按钮 js事件///////////////////////////////////
   $('#ccdDisConnect').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60CcdSendData',
            data : {ccdConnect:2,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次断开ccd!');
            },
        });
   });
   
   //CCD 停止曝光 js事件/////////////////////////////////////
   $('#ccdStopExpose').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60CcdSendData',
            data : {StopExpose:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次停止曝光!');
            },
        });
   });
   
   //CCD 终止曝光 js事件/////////////////////////////////////
   $('#ccdAbortExpose').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60CcdSendData',
            data : {AbortExpose:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次终止曝光!');
            },
        });
   });
   
   //CCD 带参数指令  js事件//////////////////////////////////////////////
	var ccdForm = $('#at60Ccd');
    var ccdSelect = ccdForm.find('div');

    ccdSelect.click(function () {
         $(this).find('input:radio').prop('checked', true);
         var notcheck = ccdSelect.not($(this));
         notcheck.addClass('notCheck');
         notcheck.find('input[type="text"]').val('');
         $(this).removeClass('notCheck');
     });
	 
	//CCD 带参数指令 表单提交 JS事件///////////////////////////////
    $('#ccdSbmt').click(function () {
        var form = $('#at60Ccd');    //获取ccd表单元素
        var formData = new FormData(form[0]);
        //执行ajax
        $.ajax ({
              type: 'post',
              url : '/xinglong/at60/at60CcdSendData',
              data : formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
                alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
             },
             error:  function () {
               alert('网络异常,请重新提交');
            },
        });
     
    });
	
   //ccd 表单提交按钮 hover //////////////////////////////////
   $('#ccdSbmt').hover(
        function (){
            $(this).addClass("hover");
        }, 
        function (){
            $(this).removeClass("hover");
        }
   );
   
   //调焦器 带参数指令  js事件//////////////////////////////////////////////
	var focusForm = $('#at60Focus');
    var focusSelect = focusForm.find('div');

    focusSelect.click(function () {
         $(this).find('input:radio').prop('checked', true);
         var notcheck = focusSelect.not($(this));
         notcheck.addClass('notCheck');
         notcheck.find('input[type="text"]').val('');
         $(this).removeClass('notCheck');
     });
   
   //调焦器 连接按钮 js事件///////////////////////////////////
   $('#focusConnect').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FocusSendData',
            data : {focusConnect:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次连接调焦器!');
            },
      });
   });
   
    //调焦器 断开按钮 js事件///////////////////////////////////
   $('#focusDisConnect').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FocusSendData',
            data : {focusConnect:2,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次断开调焦器!');
            },
        });
   });
   
   //调焦器 停止运动按钮 js事件///////////////////////////////////
   $('#focusStop').click(function (){
	   $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FocusSendData',
            data : {focusStop:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击停止按钮!');
            },
        });
   });
   
   //调焦器 找零按钮 js事件///////////////////////////////////
   $('#focusFindHome').click(function (){
	   $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FocusSendData',
            data : {findHome:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击找零按钮!');
            },
        });
   }); 
   
    //调焦器 表单提交按钮 hover //////////////////////////////////
   $('#focusSbmt').hover(
        function (){
            $(this).addClass("hover");
        }, 
        function (){
            $(this).removeClass("hover");
        }
   );
   
   //调焦器 带参数指令 表单提交 JS事件///////////////////////////////
    $('#focusSbmt').click(function () {
        var form = $('#at60Focus');    //获取ccd表单元素
        var formData = new FormData(form[0]);
        //执行ajax
        $.ajax ({
              type: 'post',
              url : '/xinglong/at60/at60FocusSendData',
              data : formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
                alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
             },
             error:  function () {
               alert('网络异常,请重新提交');
            },
        });
     
    });
	
	//随动圆顶 连接按钮 js事件///////////////////////////////////
   $('#sDomeConnect').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60sDomeSendData',
            data : {sDomeConnect:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次连接圆顶!');
            },
        });
   });
   
   //随动圆顶 断开按钮 js事件///////////////////////////////////
   $('#sDomeDisConnect').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60sDomeSendData',
            data : {sDomeConnect:2,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次断开圆顶!');
            },
        });
   });
   
   //随动圆顶 停止运动按钮 js事件///////////////////////////////////
   $('#sDomeStop').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60sDomeSendData',
            data : {sDomeStop:1,},             
            success:  function (info) {
               alert(info);
			   if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次点击该按钮!');
           },
        });
   });
   
   //随动圆顶 打开天窗 按钮 js事件///////////////////////////////////
   $('#sDomeScuttle').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60sDomeSendData',
            data : {OpenScuttle:1,},             
            success:  function (info) {
               alert(info);
               if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次打开天窗!');
            },
        });
   });
   
   //随动圆顶 关闭天窗 按钮 js事件///////////////////////////////////
   $('#sDomeScuttleClose').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60sDomeSendData',
            data : {OpenScuttle:2,},             
            success:  function (info) {
               alert(info);
               if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次关闭天窗!');
            },
        });
   });
	
	//随动圆顶 带参数指令  js事件//////////////////////////////////////////////
	var domeForm = $('#at60Dome');
    var domeSelect = domeForm.find('div');

    domeSelect.click(function () {
         $(this).find('input:radio').prop('checked', true);
         var notcheck = domeSelect.not($(this));
         notcheck.addClass('notCheck');
         notcheck.find('input[type="text"]').val('');
         $(this).removeClass('notCheck');
     });
	 
	//圆顶 带参数指令 表单提交 JS事件///////////////////////////////
    $('#domeSbmt').click(function () {
        var form = $('#at60Dome');    //获取ccd表单元素
        var formData = new FormData(form[0]);
        //执行ajax
        $.ajax ({
              type: 'post',
              url : '/xinglong/at60/at60sDomeSendData',
              data : formData,
              processData : false,
              contentType : false,  
              success:  function (info) {
                alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
             },
             error:  function () {
               alert('网络异常,请重新提交');
            },
        });
     
    });
	
	//随动圆顶 表单提交按钮 hover //////////////////////////////////
   $('#domeSbmt').hover(
        function (){
            $(this).addClass("hover");
        }, 
        function (){
            $(this).removeClass("hover");
        }
   );
   
   //全开圆顶 连接按钮 js 事件/////////////////////////////////////
    var fDomeConnectFlag = 0; //此处代码 需要修改
   $('#fDomeConnect').click(function (){
       var e = $(this);
       fDomeConnectFlag ++;
       if (e.val() == '连接圆顶')   val = '1';
       if (e.val() == '断开圆顶')   val = '2';
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60fDomeSendData',
            data : {fDomeConnect:val,},             
            success:  function (info) {
               alert(info);
                if (fDomeConnectFlag % 2 == 1 && info.indexOf('成功') != -1)
                {//连接圆顶指令 发送成功
                    e.val('断开圆顶');
                }else if(fDomeConnectFlag % 2 == 0 && info.indexOf('成功') != -1){//断开圆顶指令 发送成功
                    e.val('连接圆顶');
                }
            },
            error:  function () {
               alert('网络异常,请再次点击该按钮!');
            },
        });
   });
   
   //全开圆顶 打开 onchange事件/////////////////////////////////
    var fDome = $('#fDomeConnect').next('select');
	
   fDome.change(function (){
	   var fDomeVal = fDome.val();
	   if (fDomeVal !== '')
	   {
		   $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60fDomeSendData',
            data : {openDome:fDomeVal,},             
            success:  function (info) {
			    alert(info); 
            },
            error:  function () {
               alert('网络异常,请再次选择该指令!');
            },
        });
	   }
		
   });
   
   //滤光片  连接按钮 js 事件/////////////////////////////////
   $('#filterConnect').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FilterSendData',
            data : {filterConnect:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次连接滤光片!');
            },
        });
   });
   
   //滤光片  断开按钮 js 事件/////////////////////////////////
   $('#filterDisConnect').click(function (){
       $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FilterSendData',
            data : {filterConnect:2,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次断开滤光片!');
            },
        });
   });
   
   //滤光片 找零按钮 js事件//////////////////////////////////
   $('#filterFindHome').click(function (){
	   $.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FilterSendData',
            data : {filterFindHome:1,},             
            success:  function (info) {
               alert(info);
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次进行找零!');
            },
        });
   });
   
    //滤光片 提交位置指令 js事件//////////////////////////////////
	$('#filterPosBtn').click(function () {
		var filterPosEle = $('#filterPosi');
		var filterPosVal = filterPosEle.val();
		$.ajax({
            type : 'post',
            url : '/xinglong/at60/at60FilterSendData',
            data : {filterPos:filterPosVal,},             
            success:  function (info) {
               alert(info);
			   filterPosEle.val('');
				if (info.indexOf('登录') !== -1)
				{
					location.href = '/';
				}
            },
            error:  function () {
               alert('网络异常,请再次提交滤光片位置!');
			   filterPosEle.val('');
            },
        });
	});
	

})