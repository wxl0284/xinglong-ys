<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"D:\xampp\htdocs\demo\public/../application/xinglong\view\control\login.html";i:1513060817;}*/ ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset='utf-8'>
        <title>兴隆台</title>
		<link rel="stylesheet" type="text/css" href="/static/css/log.css" />
		<script type="text/javascript" src="/static/plugin/easyui-1.3.5/jquery.min.js"></script>
		<script type="text/javascript" src="/static/plugin/layer/layer.js"></script>
		<script type="text/javascript" src="/static/js/login.js"></script>
    </head>
    <body>
        <div id='background'>
		  <div id='back'>
			<img src='/static/images-0/logBack.jpg' width='100%' />
			<img id='logo' src='/static/images-0/LOGO.png' />
			<img id='rect' src='/static/images-0/back.png' />
			<div id='fm'>
			   <form action="/xinglong/control/dologin" method="post">
                  <input type="hidden"	name="__token__" value="<?php echo \think\Request::instance()->token(); ?>" />
                   <p>
					  <img id='img1' src='/static/images-0/inputBak.png' />
					  <input id='ipt1' class='displayNo' type="text" name="username" placeholder='6-12位数字或字母_及-' />
					  <img id='img1_1' src='/static/images-0/count.png' />
                   </p>
                   <p id='p2'> 
					  <img id='img2' src='/static/images-0/inputBak.png' />
					  <input id='ipt2' class='displayNo' type="password" name="password" placeholder='6-12位数字或字母_及-'/>
					  <img id='img1_2' src='/static/images-0/psd.png' />
                   </p>
				   <!-- <input type='submit' value='登录' />  -->
               </form>
			</div>
			<div id='btn'>
			  <img src='/static/images-0/log.png' />
			  <div><a href='javascript:;' >登 录</a></div>
			</div>
		  </div>
        </div>
    </body>
</html>