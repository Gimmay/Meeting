<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:49
	 */
	namespace Manager\Controller;

	use Core\Controller\CoreController;
	use Core\Logic\MeetingRoleLogic;
	use Core\Logic\PermissionLogic;
	use Manager\Logic\EmployeeLogic;
	use Manager\Logic\ManagerLogic;

	class ManagerController extends CoreController{
		protected $permissionList  = null;
		protected $meetingViewList = null;
		protected $meetingID       = 0;

		public function _initialize(){
			parent::_initialize();
			$this->_checkLogin();
			$meeting_role_logic    = new MeetingRoleLogic();
			$permission_logic      = new PermissionLogic();
			$logic                 = new ManagerLogic();
			$this->permissionList  = $permission_logic->getPermissionList();
			$this->meetingViewList = $meeting_role_logic->getMeetingView(I('session.MANAGER_EMPLOYEE_ID', 0, 'int'));
			$meeting_name          = $logic->getMeetingName();
			$this->assign('cv_name', CONTROLLER_NAME.'_'.ACTION_NAME);
			$this->assign('c_name', CONTROLLER_NAME);
			$this->assign('meeting_name', $meeting_name);
			$this->assign('permission_list', $this->permissionList);
			$this->assign('meeting_view_list', $this->meetingViewList);
		}

		private function _checkLogin(){
			$canAccessDirectly = function (){
				$list  = [
					'employee/login'
				];
				$curcv = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);

				return in_array($curcv, $list) ? true : false;
			};
			$logic             = new EmployeeLogic();
			$is_login          = $logic->isLogin();
			if(!$canAccessDirectly()){
				if(!$is_login){
					$this->redirect('Employee/login');
					exit;
				}
			}
			elseif($is_login){
				$this->redirect('Index/index');
				exit;
			}
		}

		/**
		 * @param ManagerController $obj
		 *
		 * @return mixed
		 */
		protected function initMeetingID($obj){
			$meeting_id = I('get.mid', 0, 'int');
			if($meeting_id === 0){
				echo "<h1>缺少会议参数！</h1>";
				exit;
			}
			else{
				$obj->meetingID = $meeting_id;

				return $meeting_id;
			}
		}
	}