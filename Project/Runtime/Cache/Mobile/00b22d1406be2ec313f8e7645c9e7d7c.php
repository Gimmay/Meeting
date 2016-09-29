<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta id="viewport" name="viewport" content="width=device-width,minimum-scale=1,maximum-scale=1,initial-scale=1,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<title>个人中心</title>
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/bootstrap-theme.min.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/bootstrap/datetimepicker/bootstrap-datetimepicker.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Toast/jquery.quasar.toast.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/jQuery/Quasar.Loading/jquery.quasar.loading.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE_PATH); ?>/mobile_common.css">
	<link rel="stylesheet" href="<?php echo (COMMON_STYLE); ?>">
	<link rel="stylesheet" href="<?php echo (SELF_STYLE); ?>">
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/jquery-3.1.0.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Select/jquery.quasar.select.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Toast/jquery.quasar.toast.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/jQuery/Quasar.Loading/jquery.quasar.loading.js"></script>
	<script src="<?php echo (COMMON_SCRIPT_PATH); ?>/mobile_rem.js"></script>
	<script src="<?php echo (COMMON_SCRIPT); ?>"></script>
	<script src="<?php echo (SELF_SCRIPT); ?>"></script>
</head>
<body>
	<div id="mb_wrap">
		<div class="mb_body">
			<div class="my_box">
				<div class="my_title">
					<div class="photo">
						<img src="<?php echo ($info["avatar"]); ?>">
					</div>
					<p class="name"><?php echo ($info["name"]); ?></p>
					<p class="address"><?php echo ($info["address"]); ?></p>
				</div>

				<div class="qr_code">
					<img src="<?php echo ($info["sign_qrcode"]); ?>">
				</div>
			</div>

			<div class="my_infor">
				<div class="title">
					<p>会议名称：<?php echo ($info["meeting_name"]); ?> </p>
				</div>
				<div class="personal">
					<p>
						会议简介：<?php echo ($info["meeting_brief"]); ?>
					</p>
					<p>
						起始时间：<?php echo ($info["meeting_start_time"]); ?> — <?php echo ($info["meeting_end_time"]); ?>
					</p>
					<p>
						会议地点：<?php echo ($info["meeting_place"]); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
	<script>
		MyCenterObject = {
			config:{
				signStatus:'<?php echo ($info["sign_status"]); ?>'
			},
			object:{
				toast:$().QuasarToast()
			}
		}
	</script>
</body>
</html>