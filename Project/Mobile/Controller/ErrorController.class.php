<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-26
	 * Time: 12:02
	 */
	namespace Mobile\Controller;

	class ErrorController extends MobileController{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 可能的原因
		 * 1、未注册该员工记录
		 * 2、未同步微信企业号与系统的微信记录
		 * 3、企业号中的手机号与系统录入的手机号不匹配
		 */
		public function notRegister(){
			echo '<h1>没有注册</h1>';
			//$this->display();
		}

		/**
		 * 可能的原因
		 * 1、没有参加该次会议
		 * 2、未同步微信企业号与系统的微信记录
		 * 3、企业号中的手机号与系统录入的手机号不匹配
		 */
		public function notJoin(){
			echo '<h1>没有参会</h1>';
			//$this->display();
		}

		/**
		 * URL缺少会议参数
		 */
		public function requireMeeting(){
			echo '<h1>缺少会议参数</h1>';
			//$this->display();
		}
		/**
		 * URL缺少参会人员参数
		 */
		public function requireClient(){
			echo '<h1>缺少参会参数</h1>';
		}

		/**
		 * 没有授予权限
		 */
		public function notPermission(){
			echo '<h1>没有权限</h1>';
		}

	}