<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"D:\xampp\htdocs\demo\public/../application/xinglong\view\weather\clouds.html";i:1509414935;}*/ ?>
<!doctype html>
<html>
<head>
	<meta charset='utf-8'>
	<title>云量图片</title>
	<link rel="stylesheet" type="text/css" href="/static/css/common-1.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/clouds.css" />
	<script type="text/javascript" src="/static/plugin/easyui-1.3.5/jquery.min.js"></script>
    <script type="text/javascript" src="/static/js/clouds.js"></script>
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
	</header>
	<div id='all'>
	  <div id='main'>
		<br /><span >云量相机图片</span><br /><br />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<img class='cloudPic' src='/static/images-1/clouds_2.png' />
		<br /><br /><img src='/static/images-1/page.png' />
	  </div>
	</div>
	<footer class='center'>
		<span>中科院国家天文台兴隆观测基地</span>
	</footer>
</body>
</html>