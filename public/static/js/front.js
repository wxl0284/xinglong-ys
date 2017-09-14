//导航栏 望远镜列表////////////////////////////////////
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
   
   $('#at60').click(function () {
	   location = '/xinglong/at60';
   });
   $('#at80').click(function () {
	   location = '/xinglong/at80';
   });
})
//导航栏 望远镜列表////////////////////////////////////