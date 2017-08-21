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
   
   //显示导航栏望远镜列表////////////////////////   
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
   });//////////////////////////////////
   
})
