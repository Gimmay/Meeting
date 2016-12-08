<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-26
	 * Time: 9:56
	 */
	namespace Mobile\Controller;

	use Core\Logic\WxCorpLogic;

	class WechatController extends MobileController{
		public function _initialize(){
			parent::_initialize();
		}

		public function notFollow(){
			$this->display();
		}

		public function verify(){
			$redirect = I('get.redirect', 0, 'int');
			if(!isset($_SESSION['MOBILE_WECHAT_ID'])){
				$wxcorp_logic   = new WxCorpLogic();
				$wechat_user_id = $wxcorp_logic->getUserID();
				if($wechat_user_id){
					$redirect_url = I('cookie.WECHAT_REDIRECT_URL', '');
					setcookie('WECHAT_REDIRECT_URL');
					$_SESSION['MOBILE_WECHAT_ID'] = $wechat_user_id;
					redirect($redirect_url);

					return true;
				}
				else{
					if($redirect) $this->redirect('Wechat/notFollow');

					return false;
				}
			}
			else{
				$redirect_url = I('cookie.WECHAT_REDIRECT_URL', '');
				setcookie('WECHAT_REDIRECT_URL');
				redirect($redirect_url);
				return true;
			}
		}
	}