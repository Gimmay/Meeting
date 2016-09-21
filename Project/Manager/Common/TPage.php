<?php
	/**
	 * Create by PhpStorm
	 * User: 0967
	 * Time: 2016-9-13 10:43
	 */
	namespace ThinkPHP\Quasar\Page;
	function setTheme1(&$page_obj){
		/** @noinspection PhpUndefinedMethodInspection */
		$page_obj->setConfig('last', '末页');
		/** @noinspection PhpUndefinedMethodInspection */
		$page_obj->setConfig('first', '首页');
		$page_obj->lastSuffix = false;
		/** @noinspection PhpUndefinedMethodInspection */
		$page_obj->setConfig('theme', '%FIRST% %LINK_PAGE% %END%');
	}