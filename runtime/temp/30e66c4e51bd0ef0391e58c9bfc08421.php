<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"D:\xampp\htdocs\demo\public/../application/xinglong\view\control\front.html";i:1505703729;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <title>首页</title>
     <link rel="stylesheet" type="text/css" href="/static/css/common-1.css" />
     <link rel="stylesheet" type="text/css" href="/static/css/front.css" />
	 <script type="text/javascript" src="/static/plugin/easyui-1.3.5/jquery.min.js"></script>
	 <script type="text/javascript" src="/static/js/front.js"></script>
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
		   <p class='pos_a'>
				<a href="<?php echo url('xinglong/control/front'); ?>">首页&nbsp;&nbsp;&nbsp;&nbsp;
				</a>
				<?php if(\think\Request::instance()->cookie('role') == 1): ?>
				<a href='/xinglong/user'>用户管理&nbsp;&nbsp;&nbsp;&nbsp;</a>
				<?php endif; ?>
				<a href='/xinglong/user/passwd'>修改密码&nbsp;&nbsp;&nbsp;&nbsp;
				</a>
				<span>欢迎!&nbsp;&nbsp;<?php echo (\think\Request::instance()->cookie('login')) ? \think\Request::instance()->cookie('login') :  ''; ?>&nbsp;
				</span>
		   </p>
		   <div class='pos_a'><a href='/xinglong/control/logout'>&nbsp;&nbsp;&nbsp;退出&nbsp;&nbsp;&nbsp;</a></div> 
	    </div>
	</header>
	<div id='all'>
		<br />
		<div id='weather' class='center'>
			<span class='title_text'>天 气 预 报</span><br />
			<span id='weatherD'><?php echo date('Y.m.d',time());?></span>
			<br />
			<a class='border1 more' href="<?php echo url('control/weatherMore'); ?>">显示详情</a><br /><br />
			<span style='font-size:20px;'><?php echo isset($weatherError) ? $weatherError : 	''; ?></span>
			<table class='center_mrg'>
				<?php if(isset($day)): ?>
				  <tr>
					<td>白天</td>
					<td><img src='<?php echo $dayPic; ?>' /><span><?php echo $weatherDay; ?></span></td>
					<td><img src='/static/images-1/temp.png' /><span style='padding-right:80px;'><?php echo $tmpDay; ?></span></td>
					<td><img src='/static/images-1/wind.jpg' /><span><?php echo $windDay; ?></span></td>
					<td><img src='/static/images-1/wind.jpg' /><span><?php echo $windPowerDay; ?></span></td>
				  </tr>
				  <tr ><td colspan='5'><div style='height:33px;'></div></td></tr>
				  <tr>
					<td>夜晚</td>
					<td><img src='<?php echo $nightPic; ?>' /><span><?php echo $weatherNight; ?></span></td>
					<td><img src='/static/images-1/temp.png' /><span style='padding-right:80px;'><?php echo $tmpNight; ?></span></td>
					<td><img src='/static/images-1/wind.jpg' /><span><?php echo $windNight; ?></span></td>
					<td><img src='/static/images-1/wind.jpg' /><span><?php echo $windPowerNight; ?></span></td>
				  </tr>
				<?php endif; if(isset($night)): ?>
				  <tr>
					<td>夜晚</td>
					<td><img src='<?php echo $nightPic; ?>' /><?php echo $weatherNight; ?></td>
					<td><img src='/static/images-1/temp.png' /><span style='padding-right:80px;'><?php echo $tmpNight; ?></span></td>
					<td><img src='/static/images-1/wind.jpg' /><span><?php echo $windNight; ?></span></td>
					<td><img src='/static/images-1/wind.jpg' /><span><?php echo $windPowerNight; ?></span></td>
				  </tr>
				<?php endif; ?>
			</table>
		</div><!--天气预报结束--><br />
	</div>
	<div id='map' class='center pos_r'>
		<span class='title_text'>望 远 镜 布 局</span><br /><br />
		<img src='/static/images-1/map.png' alt='望远镜分布图' style='width:100%;'/>
		<div style='top:12.88%;left:21.23%;' class='pos_a'>
			<img src='/static/images-1/pic.png' alt='216CM望远镜' title='2.16米望远镜' /><br>
			<span>2.16米望远镜</span>
		</div>
		<div style='top:30.34%;left:31.8%;' class='pos_a'>
			<img src='/static/images-1/pic.png' alt='郭守敬望远镜' title='郭守敬望远镜' /><br>
			<span>郭守敬望远镜</span>
		</div>
		<div style='top:25.8%;left:43%;' class='pos_a'>
			<img src='/static/images-1/pic.png' alt='1米反光镜' title='1米反光望远镜' /><br>
			<span>1米反光望远镜</span>
		</div>
		<div style='top:15.8%;left:52%;' class='pos_a'>
			<img src='/static/images-1/pic.png' alt='测光辅助望远镜' title='测光辅助望远镜' /><br>
			<span>测光辅助望远镜</span>
		</div>
		 <div style='top:17%;left:62%;' class='pos_a'>
			<img src='/static/images-1/pic.png' alt='85CM反光望远镜' title='85CM反光望远镜' /><br>
			<span>85CM反光望远镜</span>
		</div>
		<div style='top:31%;left:54%;' class='pos_a'>
			<a href="<?php echo url('/xinglong/at60'); ?>" target='_blank'><img src='/static/images-1/pic.png' alt='60CM反光望远镜' title='60CM反光望远镜' /></a><br>
			<span>60CM反光望远镜</span>
		</div>
		<div style='top:41%;left:46%;' class='pos_a'>
			<img src='/static/images-1/pic.png' alt='50CM反光望远镜' title='50CM反光望远镜' /><br>
			<span>50CM反光望远镜</span>
		</div>
		<div style='top:50%;left:39%;' class='pos_a'>
			<a href="<?php echo url('/xinglong/at80'); ?>" target='_blank'><img src='/static/images-1/pic.png' alt='80CM反光望远镜' title='80CM反光望远镜' /></a><br>
			<span>80CM反光望远镜</span>
		</div>
		<div style='top:68%;left:52%;' class='pos_a'>
			<img src='/static/images-1/pic.png' alt='公共天文台' title='公共天文台' /><br>
			<span>公共天文台</span>
		</div>
		<div style='top:70%;left:41%;' class='pos_a'>
			<img src='/static/images-1/pic.png' alt='126CM红外镜' title='126CM红外镜' /><br>
			<span>126CM红外镜</span>
		</div>
	</div><br />
	<div id='all_1'>
		<div id='notice'><!--公告栏开始-->
		   <div class='center'><span class='title_text'>公 告 栏</span></div><br>
			<ul class='center_mrg pos_r' style='width:1140px;'>
				<li class='frontLi fl '><span>用户admin在使用60CM望远镜 2017.07.03</span></li>
				<li class='frontLi fl'><span>用户atccs在使用85CM望远镜 2017.07.03</span></li>
				<li class='frontLi fl'><span>用户admin在使用60CM望远镜 2017.07.03</span></li>
				<li class='frontLi fl'><span>用户atccs在使用85CM望远镜 2017.07.03</span></li>
				<div class='clear'></div>
			</ul>
		</div><!--公告栏开始 -->
	</div><br />
	<footer class='center'>
		<span>中科院国家天文台兴隆观测基地</span>
	</footer>
</body>
</html>