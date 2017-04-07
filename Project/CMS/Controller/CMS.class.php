<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-2
	 * Time: 16:15
	 */
	namespace CMS\Controller;

	use CMS\Logic\Session;
	use CMS\Model\CMSModel;
	use General\Logic\MeetingLogic;
	use General\Model\MeetingTypeModel;
	use General\Model\RoleModel;
	use Think\Controller;

	class CMS extends Controller{
		/** 登入页面控制器名以及操作名 */
		const LOGIN_PAGE = 'CMS/User/login';
		/** 登入后访问的页面控制器名以及操作名 */
		const FIRST_PAGE = 'CMS/Meeting/type';
		/** 不登入能够直接访问的页面列表(全小写) */
		const CAN_ACCESS_WITHOUT_LOGIN_PAGE_LIST = [
			'cms/user/login',
			'user/login',
		];
		/** 需要修改密码时能直接访问的页面列表(全小写) */
		const CAN_GOTO_WHEN_MODIFY_PASSWORD_LIST = [
			'cms/my/modifypassword2',
			'my/modifypassword2',
			'cms/my/logout',
			'my/logout'
		];
		/** 默认的每页记录数 */
		const PAGE_RECORD_COUNT = 20;
		/** URL的控制参数 */
		const URL_CONTROL_PARAMETER = [
			'keyword'     => 'keyword', // 关键字检索参数
			'page'        => 'p', // 分页参数
			'pageCount'   => 'pageCount',
			'orderColumn' => 'orderColumn', // 排序字段
			'orderMethod' => 'orderMethod', // 排序方式
		];
		/** 列表记录默认的排序字段 */
		const DEFAULT_ORDER_COLUMN = 'id';
		/** 列表记录默认的排序方式 */
		const DEFAULT_ORDER_METHOD = 'desc';
		/** @var int 登入用户的ID */
		protected $userID = 0;
		/** @var string 登入用户最高的用户等级 */
		protected $userHighestRoleLevel = RoleModel::LOWEST_LEVEL;

		public function _initialize(){
			$this->_checkLogin(); // 登入检测
			$this->_buildConstVariable(); // 创建常量
			$this->_assignVariable(); // 变量赋值
			$this->_initAttribute(); // 初始化属性
		}

		private function _initAttribute(){
			$this->userID = Session::getCurrentUser();
			/** @var \General\Model\UserModel $user_model */
			$user_model                 = D('General/User');
			$this->userHighestRoleLevel = $user_model->getUserHighestLevel($this->userID);
		}

		/**
		 * 检测是否已登入<br>
		 * 包含重定向
		 */
		private function _checkLogin(){
			$needModifyPassword   = function (){
				return isset($_SESSION[Session::MUST_MODIFY_PASSWORD]) && session(Session::MUST_MODIFY_PASSWORD) ? true : false;
			};
			$canAccessDirectlyWithoutLogin    = function (){
				$cur_cv = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);

				return in_array($cur_cv, self::CAN_ACCESS_WITHOUT_LOGIN_PAGE_LIST) ? true : false;
			};
			$canAccessDirectlyWhenModifyPassword    = function (){
				$cur_cv = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);

				return in_array($cur_cv, self::CAN_GOTO_WHEN_MODIFY_PASSWORD_LIST) ? true : false;
			};

			$isLogin              = function (){
				return isset($_SESSION[Session::LOGIN_USER_ID]) && session(Session::LOGIN_USER_ID) ? true : false;
			};
			$is_login             = $isLogin();
			$need_modify_password = $needModifyPassword();
			if($is_login){
				if($need_modify_password){
					if(!$canAccessDirectlyWhenModifyPassword()) $this->redirect('My/modifyPassword2');
				}
				else{
					if($canAccessDirectlyWithoutLogin()) $this->redirect(self::FIRST_PAGE);
				}
			}
			else{
				if(!$canAccessDirectlyWithoutLogin()){
					$this->redirect(self::LOGIN_PAGE);
					exit;
				}
			}
		}

		/**
		 * 绑定常量
		 */
		private function _buildConstVariable(){
			$setMCVName = function (){
				$mcv        = '/'.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
				$cv         = '/'.CONTROLLER_NAME.'/'.ACTION_NAME;
				$condition1 = strpos($_SERVER['REQUEST_URI'], $mcv);
				$condition2 = strpos($_SERVER['REQUEST_URI'], $cv);
				if(0 === $condition1 || 0<$condition1) define('TP_SYS_PARAM', $mcv);
				elseif(0 === $condition2 || 0<$condition2) define('TP_SYS_PARAM', $cv);
				define('MCV', $mcv);
				define('CV', $cv);
				define('PAGE_SUFFIX', C('PAGE_SUFFIX'));
			};
			define('PUBLIC_PATH', '/'.'./Public');
			define('COMMON_SCRIPT_PATH', '/'.APP_PATH.'Resource/Script');
			define('COMMON_STYLE_PATH', '/'.APP_PATH.'Resource/Style');
			define('COMMON_IMAGE_PATH', '/'.APP_PATH.'Resource/Image');
			define('SELF_SCRIPT_PATH', '/'.APP_PATH.MODULE_NAME.'/Resource/Script');
			define('SELF_STYLE_PATH', '/'.APP_PATH.MODULE_NAME.'/Resource/Style');
			define('SELF_IMAGE_PATH', '/'.APP_PATH.MODULE_NAME.'/Resource/Image');
			define('COMMON_SCRIPT', '/'.APP_PATH.'Resource/Script/common.js');
			define('COMMON_STYLE', '/'.APP_PATH.'Resource/Style/common.css');
			define('SELF_SCRIPT', '/'.APP_PATH.MODULE_NAME.'/Resource/Script/'.CONTROLLER_NAME.'_'.ACTION_NAME.'.js');
			define('SELF_STYLE', '/'.APP_PATH.MODULE_NAME.'/Resource/Style/'.CONTROLLER_NAME.'_'.ACTION_NAME.'.css');
			$setMCVName();
		}

		private function _assignVariable(){
			$buildPermissionList = function ($list){
				$result = [];
				foreach($list as $key => $val) $result[] = $val['code'];

				return $result;
			};
			$getMeetingName      = function (){
				$meeting_id       = I('get.mid', 0, 'int');
				$meeting_id_cache = I('session.'.Session::MEETING_ID, 0, 'int');
				if(($meeting_id != 0) && ($meeting_id != $meeting_id_cache)){
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					if($meeting_model->fetch(['id' => $meeting_id])){
						$meeting_record = $meeting_model->getObject();
						$this->assign('current_meeting_name', $meeting_record['name']);
						$this->assign('current_meeting_type', MeetingLogic::TYPE[$meeting_record['type']]);
					}
					else{
						// todo 报错 找不到会议
					}
				}
			};
			/** @var \General\Model\UserModel $user_model */
			$user_model = D('General/User');
			$user_id    = Session::getCurrentUser(); // 向Session输出当前登入用户的ID
			$this->assign('session_user_name', $_SESSION[Session::LOGIN_USER_NICKNAME] == '' ? $_SESSION[Session::LOGIN_USER_NAME] : $_SESSION[Session::LOGIN_USER_NICKNAME]);
			session(Session::LOGIN_USER_PERMISSION_LIST, $buildPermissionList($user_model->getPermission($user_id))); // 向Session输出权限列表码
			$getMeetingName(); // 绑定输出会议名称/类型
			$this->assign('c_name', CONTROLLER_NAME);
			$this->assign('cv_name', CONTROLLER_NAME.'_'.ACTION_NAME);
		}

		/**
		 * 获取页面每页的记录数
		 *
		 * @return int
		 */
		protected function getPageRecordCount(){
			return I('get.'.self::URL_CONTROL_PARAMETER['pageCount'], self::PAGE_RECORD_COUNT, 'int');
		}

		/**
		 * 获取模型查询方法的控制字段
		 *
		 * @return array
		 */
		protected function getModelControl(){
			//			$keyword_param = I('get.'.self::URL_CONTROL_PARAMETER['keyword'], '');
			$result = [];
			//			if(isset($_GET[self::URL_CONTROL_PARAMETER['keyword']]) && $keyword_param != '') $result[CMSModel::CONTROL_COLUMN_PARAMETER['keyword']] = $keyword_param;
			$result[CMSModel::CONTROL_COLUMN_PARAMETER['order']] = I('get.'.self::URL_CONTROL_PARAMETER['orderColumn'], self::DEFAULT_ORDER_COLUMN).' '.I('get.'.self::URL_CONTROL_PARAMETER['orderMethod'], self::DEFAULT_ORDER_METHOD);

			return $result;
		}
	}