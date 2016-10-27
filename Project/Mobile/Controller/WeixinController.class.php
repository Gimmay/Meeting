<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-26
	 * Time: 9:56
	 */
	namespace Mobile\Controller;

	class WeixinController extends MobileController{
		public function _initialize(){
			parent::_initialize();
		}

		public function isNotFollow(){
			echo "<h1>我们不能获取到您的微信身份<br>或者<br>您似乎没有关注我们的企业号</h1>";
		}
	}