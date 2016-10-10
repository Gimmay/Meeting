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

	class ManagerController extends CoreController{
		protected $permissionList  = null;
		protected $meetingViewList = null;

		public function _initialize(){
			parent::_initialize();
			$meeting_role_logic    = new MeetingRoleLogic();
			$permission_logic      = new PermissionLogic();
			$this->permissionList  = $permission_logic->getPermissionList();
			$this->meetingViewList = $meeting_role_logic->getMeetingView();
			$this->_checkLogin();
			$this->_assignEmployeeName();
			$this->assign('cv_name', CONTROLLER_NAME.':'.ACTION_NAME);
			$this->assign('c_name', CONTROLLER_NAME);
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
			/** @var \Manager\Model\EmployeeModel $model */
			$model    = D('Employee');
			$is_login = $model->isLogin();
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

		private function _assignEmployeeName(){
			$model    = D('Core/Employee');
			$employee = $model->findEmployee(1, ['id' => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')]);
			$this->assign('curname', $employee['name']);
		}
	}