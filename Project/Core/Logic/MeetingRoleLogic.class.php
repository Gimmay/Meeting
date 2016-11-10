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

		/**
		 * @param $employee_id
		 *
		 * @return array
		 */
		public function getMeetingView($employee_id){
			/** @var \Core\Model\MeetingManagerModel $meeting_manager_model */
			$meeting_manager_model = D('Core/MeetingManager');
			$meeting_id_list       = $meeting_manager_model->findRecord(2, [
				'eid'    => $employee_id,
				'status' => 1
			]);
			$meeting_id_arr        = [];
			foreach($meeting_id_list as $val) $meeting_id_arr[] = $val['mid'];

			return $meeting_id_arr;
		}
	}