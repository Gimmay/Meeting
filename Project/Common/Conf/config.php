<?php
	return [
		'LOAD_EXT_CONFIG'         => 'mysql_config',
		'MODULE_ALLOW_LIST'       => ['Entry', 'Manager', 'Mobile'],
		'DEFAULT_MODULE'          => 'Manager',
		'DEFAULT_CONTROLLER'      => 'Meeting',
		'DEFAULT_ACTION'          => 'manage',
		'URL_MODEL'               => 2,
		'APP_SUB_DOMAIN_DEPLOY'   => 1,
		'TMPL_L_DELIM'            => '<{',
		'TMPL_R_DELIM'            => '}>',
		'URL_HTML_SUFFIX'         => 'aspx|asp|jsp|html|js|css',
		'PAGE_SUFFIX'             => '.aspx',
		'DEFAULT_FILTER'          => 'strip_tags,htmlspecialchars,addslashes',
		'TOKEN_ON'                => true, // 是否开启令牌验证 默认关闭
		'TOKEN_NAME'              => '__hash__', // 令牌验证的表单隐藏字段名称，默认为__hash__
		'TOKEN_TYPE'              => 'md5', // 令牌哈希验证规则 默认为MD5
		'TOKEN_RESET'             => true, // 令牌验证出错后是否重置令牌 默认为true
		'DEFAULT_CLIENT_PASSWORD' => '123456',
		'SHOW_PAGE_TRACE'         => false
	];