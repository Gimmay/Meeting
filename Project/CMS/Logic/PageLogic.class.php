<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-6
	 * Time: 14:25
	 */
	namespace CMS\Logic;

	class PageLogic extends CMSLogic{
		/**
		 * @param \Think\Page $page_obj Thinkphp框架的Page类
		 */
		public static function setTheme1(&$page_obj){
			/** @noinspection PhpUndefinedMethodInspection */
			$page_obj->setConfig('last', '末页');
			/** @noinspection PhpUndefinedMethodInspection */
			$page_obj->setConfig('first', '首页');
			$page_obj->lastSuffix = false;
			/** @noinspection PhpUndefinedMethodInspection */
			$page_obj->setConfig('theme', '%FIRST% %LINK_PAGE% %END%');
		}

		public function handlerRequest($type, $opt = []){
		}

		public function setData($type, $data){
		}
	}