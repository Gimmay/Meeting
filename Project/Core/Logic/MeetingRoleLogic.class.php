<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-30
	 * Time: 21:32
	 */
	namespace Core\Logic;

	class MeetingRoleLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function getMeetingView(){
			/** @var \Core\Model\AssignRoleModel $model */
			$model = D('Core/AssignRole');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$role_list     = $model->getRoleEffect(I('session.MANAGER_EMPLOYEE_ID', 0, 'int'));
			$meeting_id    = [];
			foreach($role_list as $val1){
				if($val1 == 0){
					$meeting_id   = [];
					$meeting_list = $meeting_model->findMeeting(2);
					foreach($meeting_list as $val2) $meeting_id[] = $val2['id'];
				}
				else{
					$meeting_id[] = $val1;
				}
			}

			return $meeting_id;
		}
	}