<?php
	return [
		'LOAD_EXT_CONFIG'          => 'mysql_config',
		'MODULE_ALLOW_LIST'        => ['General', 'CMS', 'Mobile', 'RoyalwissD', 'WayneE'],
		'DEFAULT_MODULE'           => 'CMS',
		'DEFAULT_CONTROLLER'       => 'Meeting',
		'DEFAULT_ACTION'           => 'type',
		'URL_MODEL'                => 2,
		'APP_SUB_DOMAIN_DEPLOY'    => 1,
		'TMPL_L_DELIM'             => '<{',
		'TMPL_R_DELIM'             => '}>',
		'URL_HTML_SUFFIX'          => 'aspx|asp|jsp|html|js|css',
		'PAGE_SUFFIX'              => '.aspx',
		'DEFAULT_FILTER'           => 'strip_tags,htmlspecialchars,addslashes',
		'TOKEN_ON'                 => true,
		'TOKEN_NAME'               => '__hash__',
		'TOKEN_TYPE'               => 'md5',
		'TOKEN_RESET'              => true,
		'SHOW_PAGE_TRACE'          => false
	];