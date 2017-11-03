<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"D:\xampp\htdocs\demo\public/../application/xinglong\view\user\add.html";i:1509435995;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <title>用户添加</title>
     <link rel="stylesheet" type="text/css" href="/static/css/common-1.css" />
     <link rel="stylesheet" type="text/css" href="/static/css/userAdd.css" />
	 <script type="text/javascript" src="/static/plugin/easyui-1.3.5/jquery.min.js"></script>
	 <script type="text/javascript" src="/static/js/user.js"></script>
</head>
<body>
	<img src='/static/images-1/top.jpg' style='width:100%'/>
	<header class='pos_r'>
	     <div class='pos_r'>
	      <img class='pos_a' src='/static/images-0/logo1.png'/>
		  <div id='atList' class='pos_a'>
				望远镜列表
				<ul id='atListUl' class='center displayNo'>
					<li><a href='<?php echo url("/xinglong/at60"); ?>' target='_blank'>60CM</a></li>
					<hr>
					<li><a href='<?php echo url("/xinglong/at80"); ?>' target='_blank'>80CM</a></li>
					<hr>
					<li>85CM</li>
					<hr>
					<li>85CM</li>
				</ul>
		   </div>
		   <div id='atConfig' class='pos_a'>
				配置望远镜
				<ul id='atConfigList' class='center displayNo'>
					<li><a href='<?php echo url("/xinglong/page_config/at60config"); ?>' target='_blank'>配置60CM</a></li>
					<hr>
					<li><a href='<?php echo url("/xinglong/page_config/at80config"); ?>' target='_blank'>配置80CM</a></li>
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
		 <form id='fm' class='center_mrg'>
			<table>
				<tr>
				   <td>用户名</td><td><input class='border ipt' type='text' name='username' placeholder='6-12位字母数字_及-'/></td>
				</tr>
				<tr>
					<td>密 码</td><td><input class='border ipt' type='password' name='passwd' placeholder='6-12位字母数字_及-'/></td>
				</tr>
				<tr>
					<td>确认密码</td><td><input class='border ipt' type='password' name='rePasswd' placeholder='6-12位字母数字_及-'/></td>
				</tr>
					<tr><td>用户权限</td>
					<td>
					    <select class='border ipt' name="role">
						   <option value="2">普通用户</option>
						   <option value="3">操作者</option>
						   <option value="4">科学家</option>
					    </select>
					</td>
				</tr>
				<tr>
					<td colspan='2' class='center'><input class='border1' id='btn' type='button' value='确认提交' /></td>
				</tr> 
			</table>
		  </form>
    </div><br />
	<footer class='center clear'>
		<span>中科院国家天文台兴隆观测基地</span>
	</footer>
	<script>
		$(function () {
			$('#btn').click(function (){
				var form = $('#fm');
				var formData = new FormData(form[0]);
				$.ajax({
					type: 'post',
					url: '/xinglong/user/doadd',
					data: formData,
					processData : false,
					contentType : false,  
		            success:  function (info) {
		                alert(info);
						if (info.indexOf('登录') !== -1)
						{
							location.href = '/';
						}else if (info.indexOf('用户成功') !== -1)
						{
							location.href = '/xinglong/user';
						}
		            },
		            error:  function () {
		               alert('网络异常,请重新提交');
		            },
				});
			});
		})
	</script>
</body>
</html>