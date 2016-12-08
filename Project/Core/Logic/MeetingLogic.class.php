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
					$meeting_model->alterMeeting(['id' => $val['id']], ['status' => 3]);
				}
				elseif($end_time<time() && $val['status'] != 0){
					C('TOKEN_ON', false);
					$meeting_model->alterMeeting(['id' => $val['id']], ['status' => 4]);
				}
				//				elseif($start_time>time()){
				//					C('TOKEN_ON', false);
				////					$meeting_model->alterMeeting(['id'=>$val['id']], ['status' => 2]);
				//				}
			}
		}

		public function getConfig($mid){
			// Warning：如果更改了消息类型数量 这里必须做更改
			// B477A789FC61E5FC5221C889708449B460B207C5
			/** @var \Core\Model\MeetingModel $model */
			$model                            = D('Core/Meeting');
			$meeting                          = $model->findMeeting(1, ['id' => $mid]);
			$config_message_type              = decbin($meeting['config_message_type']);
			$config_message_type              = sprintf('%02d', $config_message_type);
			$config_message_type              = strrev($config_message_type);
			$meeting['config_message_sms']    = $config_message_type[0];
			$meeting['config_message_wechat'] = $config_message_type[1];

			return [
				'message_sms'    => $meeting['config_message_sms'],
				'message_wechat' => $meeting['config_message_wechat'],
				'wechat'         => $meeting['config_wechat'],
			];
		}
	}