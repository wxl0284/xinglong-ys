<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"D:\xampp\htdocs\demo\public/../application/xinglong\view\user\edit.html";i:1509436056;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <title>编辑用户</title>
     <link rel="stylesheet" type="text/css" href="/static/css/common-1.css" />
     <link rel="stylesheet" type="text/css" href="/static/css/userEdit.css" />
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
		<form id='fm' class='center_mrg' action="<?php echo url('user/doEdit'); ?>" method='post'>
			<input type='hidden' name='id' value='<?php echo $userData['id']; ?>' />
			<table>
				<tr>
				   <td>用户名</td><td><input class='border ipt' type='text' name='username' value='<?php echo $userData['username']; ?>' /></td>
				</tr>
				<tr>
					<td>密 码</td><td><input class='border ipt' type='password' name='passwd' placeholder='置空为不修改' /></td>
				</tr>
				<?php if($userData['role'] !== 1): ?>
				<tr><td>用户权限</td>
					<td>
					    <select class='border ipt' name="role">
						   <option value="2" <?php if($userData['role'] == 2): ?>
						   selected='selected' <?php endif; ?>
						   >普通用户</option>
						   <option value="3" <?php if($userData['role'] == 3): ?>
						   selected='selected' <?php endif; ?>>操作者</option>
						   <option value="4" <?php if($userData['role'] == 4): ?>
						   selected='selected' <?php endif; ?>>科学家</option>
					   </select>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td colspan='2' class='center'><input class='border1' id='btn' type='submit' value='确认提交' /></td>
				</tr> 
			</table>
		  </form>
    </div>
	<script>
	//若修改用户信息的表单未输入数据，则禁止提交
	var username = "<?php echo $userData['username']; ?>";
	var role = "<?php echo $userData['role']; ?>";
	var form = $('#fm');
	$(function (){
		form.submit(function (){
			var nameVal = form.find('input[name="username"]').val();
			var passwd = form.find('input[name="passwd"]').val();
			var roleVal = form.find('select').val();
			
			if(nameVal === username && passwd === '' && roleVal == role)
			{
				alert('您未改动任何用户信息!')
				return false; //表单未改动，不提交
			}
		});
	})
	</script>
	<footer class='center clear'>
		<span>中科院国家天文台兴隆观测基地</span>
	</footer>
</body>
</html>