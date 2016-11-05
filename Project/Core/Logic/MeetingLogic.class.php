<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-1
	 * Time: 14:05
	 */
	namespace Core\Logic;

	class MeetingLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function initializeStatus(){
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model  = D('Core/Meeting');
			$meeting_record = $meeting_model->findMeeting(2, ['status' => 'not deleted']);
			foreach($meeting_record as $val){
				$start_time = strtotime($val['start_time']);
				$end_time   = strtotime($val['end_time']);
				if($start_time<=time() && $end_time>=time() && $val['status'] != 0){
					C('TOKEN_ON', false);
					$meeting_model->alterMeeting([$val['id']], ['status' => 3]);
				}
				elseif($end_time<time() && $val['status'] != 0){
					C('TOKEN_ON', false);
					$meeting_model->alterMeeting([$val['id']], ['status' => 4]);
				}
				elseif($start_time>time()){
					C('TOKEN_ON', false);
					$meeting_model->alterMeeting([$val['id']], ['status' => 2]);
				}
			}
		}
	}