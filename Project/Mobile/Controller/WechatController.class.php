<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-26
	 * Time: 17:45
	 */

	namespace Mobile\Controller;

	use Mobile\Logic\Cookie;
	use Mobile\Logic\Session;
	use Mobile\Logic\WechatLogic;

	class WechatController extends Mobile{
		public function _initialize(){
			parent::_initialize();
		}

		public function test(){
			echo $this->getWechatID(1, 9);
		}

		public function verify(){
			$meeting_id   = I('get.mid', 0, 'int');
			$is_redirect  = I('get.redirect', 0, 'int');
			$redirect_url = $_SERVER['REDIRECT_URL'];
			setcookie(Cookie::WECHAT_VERIFY_REDIRECT_URL, $redirect_url, null, '/');
			if(!isset($_SESSION[Session::VISITOR_WECHAT_ID])){
				$wechat_logic       = new WechatLogic();
				$wechat_information = $wechat_logic->getWechatUser($meeting_id);
				if($wechat_information['status']){
					session(Session::VISITOR_WECHAT_ID, $wechat_information);
					setcookie(Cookie::WECHAT_VERIFY_REDIRECT_URL);
					redirect($redirect_url);
					echo 1;

					return true;
				}
				else{
					setcookie(Cookie::WECHAT_VERIFY_REDIRECT_URL);
					if($is_redirect){
						// todo 没有关注的页面
						// todo
					}

					return false;
				}
			}
			else{
				setcookie(Cookie::WECHAT_VERIFY_REDIRECT_URL);
				redirect($redirect_url);

				return false;
			}
		}
	}