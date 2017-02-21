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
			/** @var \Core\Model\MeetingModel $model */
			$model   = D('Core/Meeting');
			$meeting = $model->findMeeting(1, ['id' => $mid]);
			// Warning：如果更改了消息类型数量 这里必须做更改
			// B477A789FC61E5FC5221C889708449B460B207C5
			$config_message_type              = decbin($meeting['config_message_type']);
			$config_message_type              = sprintf('%02d', $config_message_type);
			$config_message_type              = strrev($config_message_type);
			$meeting['config_message_sms']    = $config_message_type[0];
			$meeting['config_message_wechat'] = $config_message_type[1];
			// Warning：如果更改了参会人员的创建检测字段 这里去下面位置做更改
			// 32B183DB71AE312536C905678B9F33FADFE63BD9
			$config_create_client                    = decbin($meeting['config_create_client']);
			$config_create_client                    = sprintf('%02d', $config_create_client);
			$config_create_client                    = strrev($config_create_client);
			$meeting['config_create_client_unit']   = $config_create_client[2];
			$meeting['config_create_client_name']   = $config_create_client[1];
			$meeting['config_create_client_mobile'] = $config_create_client[0];

			return [
				'message_sms'          => $meeting['config_message_sms'],
				'message_wechat'       => $meeting['config_message_wechat'],
				'wechat'               => $meeting['config_wechat'],
				'create_client_name'   => $meeting['config_create_client_name'],
				'create_client_mobile' => $meeting['config_create_client_mobile'],
				'create_client_unit' => $meeting['config_create_client_unit'],
			];
		}

		public function getSigner($mid, $with_mobile = true){
			/** @var \Core\Model\MeetingManagerModel $meeting_manager_model */
			$meeting_manager_model = D('Core/MeetingManager');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model   = D('Core/Employee');
			$permission_logic = new PermissionLogic();
			$manager_record   = $meeting_manager_model->findRecord(2, [
				'mid'    => $mid,
				'status' => 'not deleted'
			]);
			$signer           = [];
			foreach($manager_record as $val){
				$flag = $permission_logic->hasPermission([
					'WECHAT.CLIENT.VIEW',
					'WECHAT.CLIENT.REVIEW',
					'WECHAT.CLIENT.SIGN',
					'WECHAT.MEETING.VIEW'
				], $val['eid']);
				if($flag){
					$employee = $employee_model->findEmployee(1, ['id' => $val['eid']]);
					$signer[] = $with_mobile ? "<a href='tel:$employee[mobile]'>$employee[name]</a>" : $employee['name'];
				}
			}

			return $signer;
		}
	}