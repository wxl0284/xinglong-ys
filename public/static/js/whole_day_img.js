/** 各望远镜观测结果页面js*/
$(function () {
	//显示导航栏望远镜列表   
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
	
var vm = new Vue({//vue 实例化
    el: '#all',
    data: {
        img: pic_data,
		pic_dir: pic_dir,
		fits_dir: fits_dir,
		pic_data: pic_data,
		aperture: aperture, //此页面望远镜口径
		multi_down: [], //被选择的多个要下载的图片
    }, //data 结束
    methods: {
		multi_down_fit:function(){//进行多图下载
			let date_str = date_box.datebox('getValue');
			let t = this;
			let url = '/down_multi_fit';
			//验证提交的参数
				//验证口径、要下载的文件数量是否>2 日期格式
			//验证提交的参数 结束
			let param = {//提交的参数
				aperture: aperture,
				files: t.multi_down, //将要下载的文件名提交
				day: date_str, //日期
			};
			var xhr = new XMLHttpRequest();
			xhr.open('POST', url, true);    // 也可以使用POST方式，true表示异步
			xhr.responseType = "blob";  // 返回类型blob
			xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded"); //post，须设置请求头
			// 定义请求完成的处理函数，请求前也可以增加加载框/禁用下载按钮逻辑
			xhr.onload = function () {// 请求完成
				if (this.status === 200)// 返回200
				{
					let blob = this.response;
					//console.log(blob);
					if ( blob.size == 2 ) //服务器返回的是错误提示，提交的参数有误
					{//返回的11
						layer.alert('提交的参数有误!', {shade:0, closeBtn:0});return;
					}else if ( blob.size == 3 ){//返回的111
						layer.alert('打包下载文件时出错!', {shade:0, closeBtn:0});return;
					}else if ( blob.size == 1 ){//返回的0
						layer.alert('您没有权限下载!', {shade:0, closeBtn:0});return;
					}
			
					let reader = new FileReader();
					reader.readAsDataURL(blob);  // 转换为base64，可以直接放入a的href
					reader.onload = function (e)
					{
						t.$refs.down.download = 'fits.zip'; //that.$refs.down 页面中一个a元素
						t.$refs.down.href = e.target.result;
						t.$refs.down.click();
					}
				}else if (this.status >= 400) {
					layer.alert('网络异常!', {shade:0, closeBtn:0});
				}
			};//xhr.onload 结束
			xhr.send('param=' + JSON.stringify(param));  //发送请求
		},//multi_down_fit结束
		down_this_fit: function (file, file_name) {//下载当前这个fits图片
			let t = this;
			let url = '/down_fits_pic';
			//验证提交的参数
			if ( file.length < 10 )
			{
				layer.alert('下载文件路径有误!', {shade:0, closeBtn:0}); return;
			}//验证提交的参数 结束
			
			let param = {//提交的参数
				file: file,//要下载的文件（带部分路径 '/atccs-png/at60/2018124/aa.png'）
			};
			var xhr = new XMLHttpRequest();
			xhr.open('POST', url, true);    // 也可以使用POST方式，true表示异步
			xhr.responseType = "blob";  // 返回类型blob
			xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded"); //post，须设置请求头
			// 定义请求完成的处理函数，请求前也可以增加加载框/禁用下载按钮逻辑
			xhr.onload = function () {// 请求完成
				if (this.status === 200)// 返回200
				{
					let blob = this.response;
					//console.log(blob);
					if ( blob.size == 2 ) //服务器返回的是错误提示，提交的参数有误
					{//返回的11
						layer.alert('提交的参数有误!', {shade:0, closeBtn:0});return;
					}else if ( blob.size == 3 ){//返回的111
						layer.alert('打包下载文件时出错!', {shade:0, closeBtn:0});return;
					}else if ( blob.size == 1 ){//返回的0
						layer.alert('您没有权限下载!', {shade:0, closeBtn:0});return;
					}
			
					let reader = new FileReader();
					reader.readAsDataURL(blob);  // 转换为base64，可以直接放入a的href
					reader.onload = function (e)
					{
						t.$refs.down.download = file_name; //that.$refs.down 页面中一个a元素
						t.$refs.down.href = e.target.result;
						t.$refs.down.click();
					}
				}else if (this.status >= 400) {
					layer.alert('网络异常!', {shade:0, closeBtn:0});
				}
			};//xhr.onload 结束
			xhr.send('param=' + JSON.stringify(param));  //发送请求
		},//down_this_fit结束
	},//methods 结束
	/*watch: {
		multi_down: function (newV){
			this.show_multi_down = newV.length;
		},
	},//watch 结束*/
});/***************vue js结束*****************/

/*观测图像日期选择框 datebox*/
var date_box = $('#date');

date_box.datebox({ //datebox开始
   // onSelect: function(date){
   // },
   editable: false,
   required: true,
   formatter: function(date){
	   let y = date.getFullYear();
	   let m = date.getMonth()+1;
	   let d = date.getDate();
	   return y + '/' + m + '/' + d ;
   },
   parser: function (s){
	   if (!s) return new Date();  
	   let ss = (s.split('-'));  
	   // let y = parseInt(ss[0],10);  
	   // let m = parseInt(ss[1],10);  
	   // let d = parseInt(ss[2],10);  
	   let y = parseInt(ss[0]);  
	   let m = parseInt(ss[1]);  
	   let d = parseInt(ss[2]);  
	   if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
		   return new Date(y,m-1,d);  
	   } else { 
		   return new Date();  
	   } 
   },
})////datebox 结束
/*观测图像日期选择框 datebox 结束*/

	var d = localStorage.getItem('day');
	if ( d )
	{
		let dd = d.split('/');
		dd = dd[0] + '-' + dd[1] + '-' + dd[2];
		date_box.datebox('setValue', dd); //此处的赋值需要与上面parser方法一致
	}

	date_box.datebox('calendar').calendar({
		validator: function(date){
			let now = new Date();
			let d1 = new Date(now.getFullYear(), now.getMonth(), now.getDate());
			return date <= d1; //不能选择未来
			/* 只能选择今天及10之内的
			let now = new Date();
			let d1 = new Date(now.getFullYear(), now.getMonth(), now.getDate());
			let d2 = new Date(now.getFullYear(), now.getMonth(), now.getDate()+10);
			return d1<=date && date<=d2;*/
		}
	}) //不能选择未来 结束
	var sbmt = $('#sbmt');
	sbmt.click( function () {
		var day = date_box.datebox('getValue');
		let patn = /^\d{4}\/\d{1,2}\/\d{1,2}$/;

		if ( day.length < 8 || !patn.test(day) )
		{
			layer.alert('日期选择有误!', {shade:0, closeBtn:0});
		}else{
			localStorage.setItem('day', day);
			let h = location.href;
			let pre = h.substring(0, h.lastIndexOf('=')+1);
			//console.log(pre);return;
			location.href = pre + day;
		}
	})
	
})//jquery 末尾