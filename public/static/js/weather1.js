//气象详情页js/////////////////////////////////////////
$(function (){
	//云量图 显示更多 按钮 hover///////////////////
	$('#cloudPic a').hover(
        function (){
			$(this).css("color", '#fff');
            $(this).addClass("hover");
        }, 
        function (){
			$(this).css("color", '#7A7A7A');
            $(this).removeClass("hover");
        }
   );
   
})
