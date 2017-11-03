<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"D:\xampp\htdocs\demo\public/../application/xinglong\view\weather\weatherDetail.html";i:1509415061;}*/ ?>
<!doctype html>
<html>
<head>
	<meta charset='utf-8'>
	<title>气象信息详情</title>
	<link rel="stylesheet" type="text/css" href="/static/css/common-1.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/weatherDetail.css" />
	<script type="text/javascript" src="/static/plugin/easyui-1.3.5/jquery.min.js"></script>
	<script type="text/javascript" src="/static/plugin/echarts.js"></script>
	<script type="text/javascript" src="/static/js/weatherDetail.js"></script>
</head>
<body>
	<img src='/static/images-1/top.jpg' style='width:100%'/>
	<header class='pos_r'>
	    <div class='pos_r'>
	      <img class='pos_a' src='/static/images-0/logo1.png'/>
		  <div id='atList' class='pos_a'>
				望远镜列表
				<ul id='atListUl' class='center displayNo'>
					<li id='at60'>60CM</li>
					<hr>
					<li id='at80'>80CM</li>
					<hr>
					<li>85CM</li>
					<hr>
					<li>85CM</li>
				</ul>
		   </div>
		   <div id='atConfig' class='pos_a'>
				配置望远镜
				<ul id='atConfigList' class='center displayNo'>
					<li><a href='<?php echo url("/xinglong/page_config/at60config"); ?>'>配置60CM</a></li>
					<hr>
					<li><a href='<?php echo url("/xinglong/page_config/at80config"); ?>'>配置80CM</a></li>
					<hr>
					<li>配置85CM</li>
				</ul>
		   </div>
		   <p class='pos_a'>
				<a href="<?php echo url('xinglong/control/front'); ?>">首页&nbsp;&nbsp;&nbsp;&nbsp;
				</a>
				<?php if(\think\Session::get('role') == 1): ?>
				<a href='/xinglong/user'>用户管理&nbsp;&nbsp;&nbsp;&nbsp;</a>
				<?php endif; ?>
				<a href='/xinglong/user/passwd'>修改密码&nbsp;&nbsp;&nbsp;&nbsp;
				</a>
				<span>欢迎!&nbsp;&nbsp;<?php echo (\think\Session::get('login')) ? \think\Session::get('login') :  ''; ?>&nbsp;
				</span>
		   </p>
		   <div class='pos_a'><a href='/xinglong/control/logout'>&nbsp;&nbsp;&nbsp;退出&nbsp;&nbsp;&nbsp;</a></div> 
	    </div>
	</header><br>
	<div id='all' class='border'>
		<div><!--各类图表 开始-->
		   <div id='temperature' style='width:1000px;height:390px;margin:0 auto;'><!--温度图-->
		   </div>
		   <div id='windSpeed' style='width:1000px;height:390px;margin:20px auto;'><!--风速图-->
		   </div>
		   <div id='tempHumid' style='width:1000px;height:390px;margin:20px auto;'><!--温度湿度图-->
		   </div>
		   <div id='nightLight' style='width:1000px;height:390px;margin:20px auto;'><!--夜天光图-->
		   </div>
		   <div id='seeing' style='width:1000px;height:390px;margin:20px auto;'><!--视宁度-->
		   </div>
		   <div id='dust' style='width:1000px;height:390px;margin:20px auto;'><!--粉尘-->
		   </div>
		</div><!--各类图表 结束-->
	</div><br>
	<footer class='center'>
		<span>中科院国家天文台兴隆观测基地</span>
	</footer>
</body>
<script>
	//温度图 js//////////////////////////////////////////
	var tempaChart = echarts.init(document.getElementById('temperature'));
	var tempaData = <?php echo $tempaData; ?>;	//在模板中直接使用php分配过来的变量
	var optionTempa = {
		title: {
			text: '温度（℃）',
		},
		tooltip: {
			trigger: 'axis'
		},
		grid: {
			left: '3%',
			right: '4%',
			bottom: '3%',
			containLabel: true
		},
		xAxis: {
			type: 'category',
			boundaryGap: false,
			data: ['8:00','9:00','10:00','11:00','12:00','13:00','14:00','15:00']
		},
		yAxis: {
			type: 'value'
		},	
	   series: [
			{
				name:'温度',
				type:'line',
				data:tempaData	
			}
		]	
		
	}; 
	tempaChart.setOption(optionTempa);
//温度图 结束//////////////////////////////////////////////////////////
//风速图 js////////////////////////////////////////////////
	var windSpeedChart = echarts.init(document.getElementById('windSpeed'));
	var windSpeedData1 = <?php echo $windData2; ?>; //在模板中直接使用php分配过来的变量aa
	var windSpeedData2 = <?php echo $windData10; ?>; //在模板中直接使用php分配过来的变量aa
	var optionWind = {
		title: {
			text: '风速（米/秒）',
		},
		  legend: {
			data:['2分钟平均','10分钟平均'],
		},
		tooltip: {
			trigger: 'axis'
		},
		grid: {
			left: '3%',
			right: '4%',
			bottom: '3%',
			containLabel: true
		},
		xAxis: {
			type: 'category',
			boundaryGap: false,
			data: ['8:00','9:00','10:00','11:00','12:00','13:00','14:00','15:00']
		},
		yAxis: {
			type: 'value'
		},	
		series: [
			{
				name:'2分钟平均',
				type:'line',
				data:windSpeedData1	
			},
			{
				name:'10分钟平均',
				type:'line',
				data:windSpeedData2	
			},
		]	
	};
	windSpeedChart.setOption(optionWind);
//风速图 结束///////////////////////////////////////////////////
//温度湿度图 js////////////////////////////////////////////////
	var tempaHumidChart = echarts.init(document.getElementById('tempHumid'));
	var tmpData = <?php echo $tmpData; ?>; //在模板中直接使用php分配过来的变量
	var dewPointData = <?php echo $dewPointData; ?>; //在模板中直接使用php分配过来的变量
	var humiData = <?php echo $humiData; ?>; //在模板中直接使用php分配过来的变量
	var optionTmpHumi = {
		title: {
			text: '温度/露点温度（℃）-湿度（%）',
		},
		  legend: {
			data:['温度','露点温度','湿度'],
		},
		tooltip: {
			trigger: 'axis'
		},
		grid: {
			left: '3%',
			right: '4%',
			bottom: '3%',
			containLabel: true
		},
		xAxis: {
			type: 'category',
			boundaryGap: false,
			data: ['8:00','9:00','10:00','11:00','12:00','13:00','14:00','15:00'],
			//axisTick: {
			//    alignWithLabel: true
			//}
		},
		yAxis: [
		   {type: 'value',
			name: '温度',
			position: 'left',
		   },
		   {type: 'value',
			name: '湿度（%）',
			position: 'right',				   
		   }
		], 	
		series: [
			{
				name:'温度',
				type:'line',
				data:tmpData	
			},
			{
				name:'露点温度',
				type:'line',
				itemStyle:{normal:{color:'#c220df'}},
				data:dewPointData	
			},
			{
				name:'湿度',
				type:'line',
				yAxisIndex: 1,
				data:humiData	
			},
		]
	};
	tempaHumidChart.setOption(optionTmpHumi);
//温度 湿度 露点温度 结束//////////////////////////////////////////////////////
//夜天光 js//////////////////////////////////////////
	var nightLightChart = echarts.init(document.getElementById('nightLight'));
	var nightLightData = <?php echo $nightLight; ?>;	//在模板中直接使用php分配过来的变量
	var optionNightLight = {
		title: {
			text: '夜天光',
		},
		tooltip: {
			trigger: 'axis'
		},
		grid: {
			left: '3%',
			right: '4%',
			bottom: '3%',
			containLabel: true
		},
		xAxis: {
			type: 'category',
			boundaryGap: false,
			data: ['8:00','9:00','10:00','11:00','12:00','13:00','14:00','15:00']
		},
		yAxis: {
			type: 'value'
		},	
	   series: [
			{
				name:'夜天光',
				type:'line',
				data:nightLightData	
			}
		]	
		
	}; 
	nightLightChart.setOption(optionNightLight);
//夜天光 结束//////////////////////////////////////////////////////////
//视宁度 js//////////////////////////////////////////
	var seeingChart = echarts.init(document.getElementById('seeing'));
	var seeingData = <?php echo $seeing; ?>;	//在模板中直接使用php分配过来的变量
	var optionSeeing = {
		title: {
			text: '视宁度',
		},
		tooltip: {
			trigger: 'axis'
		},
		grid: {
			left: '3%',
			right: '4%',
			bottom: '3%',
			containLabel: true
		},
		xAxis: {
			type: 'category',
			boundaryGap: false,
			data: ['8:00','9:00','10:00','11:00','12:00','13:00','14:00','15:00']
		},
		yAxis: {
			type: 'value'
		},	
	   series: [
			{
				name:'视宁度',
				type:'line',
				data:seeingData	
			}
		]	
		
	}; 
	seeingChart.setOption(optionSeeing);
//视宁度 结束//////////////////////////////////////////////////////////
//粉尘 js//////////////////////////////////////////
	var dustChart = echarts.init(document.getElementById('dust'));
	var dustData = <?php echo $dust; ?>;	//在模板中直接使用php分配过来的变量
	var optionDust = {
		title: {
			text: '粉尘',
		},
		tooltip: {
			trigger: 'axis'
		},
		grid: {
			left: '3%',
			right: '4%',
			bottom: '3%',
			containLabel: true
		},
		xAxis: {
			type: 'category',
			boundaryGap: false,
			data: ['8:00','9:00','10:00','11:00','12:00','13:00','14:00','15:00']
		},
		yAxis: {
			type: 'value'
		},	
	   series: [
			{
				name:'粉尘',
				type:'line',
				data:dustData	
			}
		]	
		
	}; 
	dustChart.setOption(optionDust);
//粉尘 结束//////////////////////////////////////////////////////////

//显示导航栏望远镜列表//////////////////////////////////////// 
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
   //望远镜列表js代码结束///////////////////////////////////

</script>
</html>