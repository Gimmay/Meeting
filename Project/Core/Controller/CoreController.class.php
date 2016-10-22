<?php
	namespace Core\Controller;

	use Think\Controller;

	abstract class CoreController extends Controller{
		public function _initialize(){
			$this->_buildConstVariables();
		}

		private function _buildConstVariables(){
			$setMCVName = function (){
				$mcv        = '/'.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
				$mc         = '/'.CONTROLLER_NAME.'/'.ACTION_NAME;
				$condition1 = strpos($_SERVER['REQUEST_URI'], $mcv);
				$condition2 = strpos($_SERVER['REQUEST_URI'], $mc);
				if(0 === $condition1 || 0<$condition1) define('TP_SYS_PARAM', $mcv);
				elseif(0 === $condition2 || 0<$condition2) define('TP_SYS_PARAM', $mc);
				define('MCV', $mcv);
				define('PAGE_SUFFIX', C('PAGE_SUFFIX'));
			};
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
			$setMCVName();
		}
	}