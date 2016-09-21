<?php
	namespace Core\Controller;

	use Think\Controller;

	abstract class CoreController extends Controller{
		public function _initialize(){
			$this->_buildConstVariables();
		}

		private function _buildConstVariables(){
			define('COMMON_SCRIPT_PATH', '/'.APP_PATH.'Resources/Scripts/Common');
			define('COMMON_STYLE_PATH', '/'.APP_PATH.'Resources/Styles/Common');
			define('COMMON_IMAGE_PATH', '/'.APP_PATH.'Resources/Images/Common');
			define('SELF_SCRIPT_PATH', '/'.APP_PATH.'Resources/Scripts/'.MODULE_NAME);
			define('SELF_STYLE_PATH', '/'.APP_PATH.'Resources/Styles/'.MODULE_NAME);
			define('SELF_IMAGE_PATH', '/'.APP_PATH.'Resources/Images/'.MODULE_NAME);
			define('COMMON_SCRIPT', '/'.APP_PATH.'Resources/Scripts/Common/common.js');
			define('COMMON_STYLE', '/'.APP_PATH.'Resources/Styles/Common/common.css');
			define('SELF_SCRIPT', '/'.APP_PATH.'Resources/Scripts/'.MODULE_NAME.'/'.CONTROLLER_NAME.'_'.ACTION_NAME.'.js');
			define('SELF_STYLE', '/'.APP_PATH.'Resources/Styles/'.MODULE_NAME.'/'.CONTROLLER_NAME.'_'.ACTION_NAME.'.css');
		}
	}