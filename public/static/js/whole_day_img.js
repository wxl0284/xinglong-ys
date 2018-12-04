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
    }, //data 结束
    methods: {
        
    },//methods 结束
});/***************vue js结束*****************/
	
})//jquery 末尾