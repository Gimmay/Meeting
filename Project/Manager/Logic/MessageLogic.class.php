<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 20:50
	 */
	namespace Manager\Logic;

	use Core\Logic\SMSLogic;

	class MessageLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function createMessage(){
			/** @var \Core\Model\MessageModel $model */
			$model = D('Core/Message');
			C('TOKEN_ON', false);
			$data             = I('post.');
			$data['type']     = 1;
			$data['context']  = $_POST['context'];
			$data['status']   = 1;
			$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
			$data['creatime'] = time(); //当前时间
			$result           = $model->createMessage($data);
			//			$data['mid']      = I('post.mid');
			//			/** @var \Core\Model\JoinModel $join_model */
			//			$join_model = D('Core/Join');
			//			$result_join = $join_model->findRecord(2,['mid'=>I('post.mid'),'review_status'=>1]);
			//			print_r($result_join);exit;
			//			foreach ($result_join as $k=>$v){
			//				$mobile = $result_join[$k]['mobile'];
			//				$sms_logic = new SMSLogic();
			//				$sms_send = $sms_logic->send($data['context'],[$mobile]);  //发送短信 第一个参数填内容， 第二个参数填手机号数组
			//			}
			return $result;
		}

		public function replaceTempToMessage($temp, $meeting, $client){
			$message = $temp;
			$message = str_replace('<:参会人名称:>', $client['name'], $message);
			$message = str_replace('<:参会人会所:>', $client['club'], $message);
			$message = str_replace('<:参会人手机号:>', $client['mobile'], $message);
			$message = str_replace('<:签到码:>', $client['sign_code'], $message);
			$message = str_replace('<:会议名称:>', $meeting['name'], $message);
			$message = str_replace('<:会议主办方:>', $meeting['host'], $message);
			$message = str_replace('<:会议策划方:>', $meeting['plan'], $message);
			$message = str_replace('<:会议开始时间:>', $meeting['start_time'], $message);
			$message = str_replace('<:会议结束时间:>', $meeting['end_time'], $message);
			$message = str_replace('<:会议负责人:>', $meeting['director_id'], $message);
			$message = str_replace('<:会议联系人1:>', $meeting['contact_id_1'], $message);
			$message = str_replace('<:会议联系人2:>', $meeting['contact_id_2'], $message);
			$message = str_replace('<:会议简介:>', $meeting['brief'], $message);

			return $message;
		}

		public function findManage(){
			/** @var \Core\Model\MessageModel $model */
			$model  = D('Core/Message');
			$result = $model->findMessage(2);

			return $result;
		}

		public function findMeeting(){
			/** @var \Core\Model\MeetingModel $model */
			$model  = D('Core/Meeting');
			$result = $model->findMeeting(2, ['status' => 2]);

			return $result;
		}
	}