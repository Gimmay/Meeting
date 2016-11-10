<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-14
	 * Time: 16:16
	 */
	namespace Manager\Logic;

	use Core\Logic\CoreLogic;
	use Core\Logic\MeetingRoleLogic;
	use Core\Logic\PermissionLogic;

	class ManagerLogic extends CoreLogic{
		protected $permissionList  = null;
		protected $meetingViewList = null;

		public function _initialize(){
			parent::_initialize();
			$permission_logic      = new PermissionLogic();
			$meeting_role_logic    = new MeetingRoleLogic();
			$this->permissionList  = $permission_logic->getPermissionList();
			$this->meetingViewList = $meeting_role_logic->getMeetingView(I('session.MANAGER_EMPLOYEE_ID', 0, 'int'));
		}
	}