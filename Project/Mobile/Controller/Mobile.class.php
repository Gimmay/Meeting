<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-26
	 * Time: 17:37
	 */

	namespace Mobile\Controller;

	use Mobile\Logic\Session;
	use Think\Controller;

	class Mobile extends Controller{
		public function _initialize(){
		}

		/**
		 * 获取微信ID
		 *
		 * @param int $redirect   是否重定向到对应的结果页
		 * @param int $meeting_id 会议ID
		 *
		 * @return null
		 */
		protected function getWechatID($redirect = 1, $meeting_id = 0){
			if(isset($_SESSION[Session::VISITOR_WECHAT_ID])) return $_SESSION[Session::VISITOR_WECHAT_ID];
			$this->redirect('Wechat/verify', ['redirect' => $redirect, 'mid' => $meeting_id]);

			return null;
		}
	}