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

		public function getMeetingName(){
			$meeting_id       = I('get.mid', 0, 'int');
			$meeting_id_cache = I('cookie.MANAGER_MEETING_ID', 0, 'int');
			if(($meeting_id != 0) && ($meeting_id != $meeting_id_cache)){
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				$meeting       = $meeting_model->findMeeting(1, ['id' => $meeting_id]);
				setcookie('MANAGER_MEETING_NAME', $meeting['name'], '/');
				setcookie('MANAGER_MEETING_ID', $meeting['id'], '/');

				return $meeting['name'];
			}
			else{
				return I('cookie.MANAGER_MEETING_NAME', '');
			}
		}
	}