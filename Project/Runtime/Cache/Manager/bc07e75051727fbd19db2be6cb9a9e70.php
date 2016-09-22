<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<title>登入 - 会议系统</title>
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/common.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE); ?>">
	<link rel="stylesheet" href="<?php echo (SELF_STYLE); ?>">
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/jquery-3.1.0.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/md5/jQuery.md5.js"></script>
	<script src="<?php echo (COMMON_SCRIPT); ?>"></script>
	<script src="<?php echo (SELF_SCRIPT); ?>"></script>
</head>
<body>
	<div class="lay_wrap">
		<div class="lay_main">
			<div class="login_head">
				<!--<img src="" alt="logo">-->
				<div class="logo">
					<span>吉美国际</span>
				</div>
				<p class="title">
					<span>会议签到系统</span>
				</p>
			</div>
			<div class="login_group">
				<form action="" method="post">
					<div class="input_group flex_wrap">
						<p class="flex_5">
							<label for="username"><span class="user_icon glyphicon glyphicon-user"></span></label>
							<input type="text" name="username" class="username" id="username">
						</p>
						<p class="flex_5">
							<label for="password"><span class="pass_icon glyphicon glyphicon-lock"></span></label>
							<input type="password" name="password" class="password" id="password">
						</p>
						<p class="flex_2 btn_wrap">
							<button type="submit" class="login">登 录</button>
						</p>
					</div>
					<p class="other_link">
						<!--<label class="keep_password"> <input type="checkbox" value=""> 记住密码?</label>-->
						<a href="" class="forget_password">忘记密码</a> <!--<a class="register" href="#">注册</a>-->
					</p>
				</form>
			</div>
			<div class="login_footer">
				<p>Copyright 2016 吉美国际集团 版权所有</p>
			</div>
		</div>
	</div>
	<div class="lay_background">
		<img src="<?php echo (SELF_IMAGE_PATH); ?>/User/login/logo-bg.jpg" alt="">
	</div>
</body>
</html>