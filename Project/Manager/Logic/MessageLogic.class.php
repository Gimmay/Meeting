<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 20:50
	 */
	namespace Manager\Logic;

	use Core\Logic\SMSLogic;
	use Core\Logic\WxCorpLogic;

	class MessageLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'create':
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
					$data['mid']      = I('post.mid');
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$result_join = $join_model->findRecord(2, ['mid' => I('post.mid'), 'review_status' => 1]);
					foreach($result_join as $k => $v){
						$mobile    = $result_join[$k]['mobile'];
						$sms_logic = new SMSLogic();
						$sms_send  = $sms_logic->send($data['context'], [$mobile]);  //发送短信 第一个参数填内容， 第二个参数填手机号数组
					}

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'search';
					$mid      = I('post.meeting_name'); //会议id
					$sign     = I('post.sign');//签到状态
					$reviewed = I('post.reviewed'); // 审核状态
					if($sign == 1){
						$data['sign_status'] = 0;
					}
					elseif($sign == 2){
						$data['sign_status'] = 1;
					}
					else{
					}
					if($reviewed == 1){
						$data['review_status'] = 0;
					}
					elseif($reviewed == 2){
						$data['review_status'] = 1;
					}
					else{
					}
					$data['mid']    = $mid;
					$data['status'] = 1;
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$result_join = $join_model->findRecord(2, $data);

					return array_merge($result_join, ['__ajax__' => true]);
				break;
				case 'send';
					$id              = I('post.selected_p');
					$user_id         = explode(',', $id);
					$data['context'] = I('post.context', '');
					/** @var \Core\Model\ClientModel $user_model */
					$user_model = D('Core/Client');
					foreach($user_id as $k => $v){
						$user_result = $user_model->findClient(1, ['id' => $v]);
						$mobile = $user_result['mobile'];
						$sms_logic   = new SMSLogic();
						$sms_send    = $sms_logic->send($data['context'], [$mobile]);  //发送短信 第一个参数填内容， 第二个参数填手机号数组
					}
					return $sms_send;

				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		private function replaceTempToMessage($temp, $meeting, $client){
			$message = $temp;
			$message = str_replace('<:参会人名称:>', $client['name'], $message);
			$message = str_replace('<:参会人会所:>', $client['club'], $message);
			$message = str_replace('<:参会人手机号:>', $client['mobile'], $message);
			$message = str_replace('<:签到码:>', $client['sign_code'], $message);
			$message = str_replace('<:签到二维码:>', getShortUrl("$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]".U('Mobile/Client/myQRCode', ['mid' => $meeting['id'], 'cid'=>$client['id']])), $message);
			$message = str_replace('<:签到(取消)时间:>', date('Y-m-d H:i:s'), $message);
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

		public function alterMessage(){
			/** @var \Core\Model\MessageModel $message_model */
			$message_model = D('Core/Message');
			$id            = I('get.id', 0, 'int');
			$result_alter  = $message_model->alterMessage(['id' => $id], [
				'name'    => I('post.name',''),
				'context' => $_POST['context']
			]);

			return $result_alter;
		}

		public function deleteMessage($data){
			/** @var \Core\Model\MessageModel $message_model */
			$message_model = D('Core/Message');
			$result = $message_model->deleteMessage($data);
			return $result;
		}

		public function send($mid, $type, $client_id_list = []){
			/** @var \Core\Model\AssignMessageModel $assign_message_model */
			/** @var \Core\Model\MeetingModel $meeting_model */
			/** @var \Core\Model\ClientModel $client_model */
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model         = D('Core/WeixinID');
			$assign_message_model = D('Core/AssignMessage');
			$meeting_model        = D('Core/Meeting');
			$client_model         = D('Core/Client');
			$sms_logic            = new SMSLogic();
			$wxcorp_logic         = new WxCorpLogic();
			$message_temp         = $assign_message_model->findRecord(1, ['mid' => $mid, 'type' => $type]);
			$meeting_record       = $meeting_model->findMeeting(1, ['id' => $mid]);
			$count                = 0;
			foreach($client_id_list as $key => $val){
				$client_record = $client_model->findClient(1, ['id' => $val]);
				$weixin_record = $weixin_model->findRecord(1, ['mobile' => $client_record['mobile']]);
				$content       = $this->replaceTempToMessage($message_temp['context'], $meeting_record, $client_record);
				// $result        = $sms_logic->send($content, [$client_record['mobile']]);
				$wxcorp_logic->sendMessage('text', $content, ['user' => [$weixin_record['weixin_id']]]);
				//if($result['status']) $count++;
				$count++;
			}
			if($count == count($client_id_list)) return ['status' => true, 'message' => '全部发送成功'];
			elseif($count == 0) return ['status' => true, 'message' => "发送失败"];
			else return ['status' => false, 'message' => "部分发送成功"];
		}
	}