<?php
	namespace Mobile\Controller;

	use Core\Controller\CoreController;
	use Core\Logic\PermissionLogic;

	class MobileController extends CoreController{
		public function _initialize(){
			parent::_initialize();
		}

		protected function getWeixinID($redirect = 1){
			if(!isset($_SESSION['MOBILE_WEIXIN_ID'])){
				setcookie('WEIXIN_REDIRECT_URL', $_SERVER['REDIRECT_URL'], null, '/');
				$this->redirect('Weixin/verify', ['redirect' => $redirect]);
				return 0;
			}else return $_SESSION['MOBILE_WEIXIN_ID'];
		}
	}