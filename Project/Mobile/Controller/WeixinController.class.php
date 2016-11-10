<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-26
	 * Time: 9:56
	 */
	namespace Mobile\Controller;

	use Core\Logic\WxCorpLogic;

	class WeixinController extends MobileController{
		public function _initialize(){
			parent::_initialize();
		}

		public function notFollow(){
			$this->display();
		}

		public function verify(){
			$redirect = I('get.redirect', 0, 'int');
			if(!isset($_SESSION['MOBILE_WEIXIN_ID'])){
				$wxcorp_logic   = new WxCorpLogic();
				$weixin_user_id = $wxcorp_logic->getUserID();
				if($weixin_user_id){
					$redirect_url = I('cookie.WEIXIN_REDIRECT_URL', '');
					setcookie('WEIXIN_REDIRECT_URL');
					$_SESSION['MOBILE_WEIXIN_ID'] = $weixin_user_id;
					redirect($redirect_url);

					return true;
				}
				else{
					if($redirect) $this->redirect('Weixin/notFollow');

					return false;
				}
			}
			else{
				$redirect_url = I('cookie.WEIXIN_REDIRECT_URL', '');
				setcookie('WEIXIN_REDIRECT_URL');
				redirect($redirect_url);
				return true;
			}
		}
	}