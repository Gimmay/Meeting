<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 20:50
	 */
	namespace Manager\Logic;

	use Core\Logic\SendMessageLogic;
	use Core\Logic\SMSLogic;
	use Core\Logic\WxCorpLogic;

	class MessageLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $option = []){
			switch($type){
				case 'create':
					if($this->permissionList['MESSAGE.CREATE']){
						/** @var \Core\Model\MessageModel $model */
						$model = D('Core/Message');
						C('TOKEN_ON', false);
						$data             = I('post.');
						$data['context']  = $_POST['context'];
						$data['status']   = 1;
						$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$data['creatime'] = time();
						$result           = $model->createMessage($data);

						return array_merge($result, [
							'__ajax__'   => false,
							'__return__' => U('manage', ['mid' => $option['mid']])
						]);
					}
					else return ['message' => '您没有创建消息的权限', 'status' => false, '__ajax__' => false];
				break;
				case 'search';
					if($this->permissionList['MESSAGE.VIEW']){
						$mid         = $option['mid']; //会议id
						$signed      = I('post.signed', 0, 'int');// 签到状态
						$reviewed    = I('post.reviewed', 0, 'int'); // 审核状态
						$receivables = I('post.receivables', 0, 'int'); // 收款状态
						$printed     = I('post.printed', 0, 'int'); // 打印状态
						switch($signed){
							case 1:
								$filter['sign_status'] = 1;
							break;
							case 0:
								$filter['sign_status'] = 'not signed';
							break;
						}
						switch($reviewed){
							case 1:
								$filter['review_status'] = 1;
							break;
							case 0:
								$filter['review_status'] = 'not reviewed';
							break;
						}
						switch($printed){
							case 1:
								$filter['print_status'] = 1;
							break;
							case 0:
								$filter['print_status'] = 0;
							break;
						}
						$filter['mid']    = $mid;
						$filter['status'] = 1;
						/** @var \Core\Model\JoinModel $join_model */
						$join_model = D('Core/Join');
						/** @var \Core\Model\ReceivablesModel $receivables_model */
						$receivables_model = D('Core/Receivables');
						$result_join       = $join_model->findRecord(2, $filter);
						$receivables_list  = $receivables_model->findRecord(2, ['mid' => $mid, 'status' => 1]);
						$new_list          = [];
						switch($receivables){
							case 1:
							case 0:
								$receivables_client_arr = [];
								foreach($receivables_list as $val) $receivables_client_arr[] = $val['cid'];
								foreach($result_join as $val){
									if(!in_array($val['cid'], $receivables_client_arr) && $receivables == 0) $new_list[] = $val;
									if(in_array($val['cid'], $receivables_client_arr) && $receivables == 1) $new_list[] = $val;
								}
							break;
							case 2:
								$new_list = $result_join;
							break;
						}

						return array_merge($new_list, ['__ajax__' => true]);
					}
					else return ['message' => '您没有查询消息的权限', 'status' => false, '__ajax__' => true];
				break;
				case 'send';
					if($this->permissionList['MESSAGE.MANUAL-SEND-MESSAGE']){
						$id              = I('post.selected_p');
						$user_id         = explode(',', $id);
						$data['context'] = $_POST['context'];
						/** @var \Core\Model\ClientModel $user_model */
						$user_model = D('Core/Client');
						$sms_send   = ['status' => false, 'message' => '发送失败'];
						foreach($user_id as $k => $v){
							$user_result = $user_model->findClient(1, ['id' => $v]);
							$mobile      = $user_result['mobile'];
							$sms_logic   = new SMSLogic();
							$sms_send    = $sms_logic->send($data['context'], [$mobile]);  //发送短信 第一个参数填内容， 第二个参数填手机号数组
						}

						return array_merge($sms_send, ['__ajax__' => false]);
					}
					else return ['message' => '您没有发送消息的权限', 'status' => false, '__ajax__' => false];
				break;
				case 'delete':
					if($this->permissionList['MESSAGE.DELETE']){
						/** @var \Core\Model\MessageModel $message_model */
						$message_model = D('Core/Message');
						$result        = $message_model->deleteMessage(['id' => I('post.id', 0, 'int')]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['message' => '您没有删除消息的权限', 'status' => false, '__ajax__' => false];
				break;
				case 'alter':
					if($this->permissionList['MESSAGE.ALTER']){
						/** @var \Core\Model\MessageModel $message_model */
						$message_model   = D('Core/Message');
						$id              = I('get.id', 0, 'int');
						$data            = I('post.');
						$data['context'] = $_POST['context'];
						$result          = $message_model->alterMessage(['id' => $id], $data);

						return array_merge($result, [
							'__ajax__'   => false,
							'__return__' => U('manage', ['mid' => I('get.mid', 0, 'int')])
						]);
					}
					else return ['message' => '您没有修改消息的权限', 'status' => false, '__ajax__' => false];
				break;
				case 'assign_message':
					if($this->permissionList['MESSAGE.SELECT']){
						/** @var \Core\Model\AssignMessageModel $assign_message_model */
						$assign_message_model = D('Core/AssignMessage');
						$mid                  = I('get.mid', 0, 'int');
						$message_id           = I('post.id', 0, 'int');
						$data                 = I('post.');
						unset($data['id']);
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$data['creatime']   = time();
						$data['message_id'] = $message_id;
						$data['mid']        = $mid;
						$result             = $assign_message_model->createRecord($data);
						if($result['status']) return ['status' => true, 'message' => '分配成功', '__ajax__' => false];
						else return ['status' => false, 'message' => '分配失败', '__ajax__' => false];
					}
					else return ['message' => '您没有设定消息的权限', 'status' => false, '__ajax__' => false];
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setData($type, $data, $option = []){
			switch($type){
				case 'manage:set_using_status':
					/** @var \Core\Model\AssignMessageModel $assign_message_model */
					$assign_message_model  = D('Core/AssignMessage');
					$assign_message_record = $assign_message_model->findRecord(2, ['mid' => $option['mid']]);
					$assigned_message_list = ['id' => [], 'type' => []];
					$list                  = ['using' => [], 'notUse' => []];
					foreach($assign_message_record as $val){
						$assigned_message_list['id'][]   = $val['message_id'];
						$assigned_message_list['type'][] = $val['assign_type'];
					}
					foreach($data as $val){
						$flag        = false;
						$assign_type = '';
						foreach($assigned_message_list['id'] as $k => $v){
							if($v == $val['id']){
								// 此处枚举出系统所有的自动发送信息的操作
								// 与以下位置相互关联
								// D85D48D80FC93AFE0620F1549178C1D02B618F4A
								switch($assigned_message_list['type'][$k]){
									case 1:
										$assign_type .= '<a title=\'点击取消分配\' href=\''.U('RequestHandler/getHandler', [
												'mid'         => $option['mid'],
												'type'        => 1,
												'message_id'  => $v,
												'requestType' => 'cancel_assign_message'
											]).'\'>签到提醒</a>, ';
									break;
									case 2:
										$assign_type .= '<a title=\'点击取消分配\' href=\''.U('RequestHandler/getHandler', [
												'mid'         => $option['mid'],
												'type'        => 2,
												'message_id'  => $v,
												'requestType' => 'cancel_assign_message'
											]).'\'>取消签到提醒</a>, ';
									break;
									case 3:
										$assign_type .= '<a title=\'点击取消分配\' href=\''.U('RequestHandler/getHandler', [
												'mid'         => $option['mid'],
												'type'        => 3,
												'message_id'  => $v,
												'requestType' => 'cancel_assign_message'
											]).'\'>收款提醒开拓顾问</a>, ';
									break;
									case 4:
										$assign_type .= '<a title=\'点击取消分配\' href=\''.U('RequestHandler/getHandler', [
												'mid'         => $option['mid'],
												'type'        => 4,
												'message_id'  => $v,
												'requestType' => 'cancel_assign_message'
											]).'\'>发送邀请</a>, ';
									break;
								}
								$flag = true;
							}
						}
						if(!$flag){
							$val['assign_type'] = '未使用';
							$list['notUse'][]   = $val;
						}
						else{
							$val['assign_type'] = trim($assign_type, ', ');
							$list['using'][]    = $val;
						}
					}
					$list = array_merge($list['using'], $list['notUse']);

					return $list;
				break;
				default:
					return $data;
				break;
			}
		}

		private function replaceTempToMessage($temp, $meeting, $client){
			$message = $temp;
			$message = str_replace('<:参会人名称:>', $client['name'], $message);
			$message = str_replace('<:参会人单位:>', $client['unit'], $message);
			$message = str_replace('<:参会人手机号:>', $client['mobile'], $message);
			$message = str_replace('<:签到码:>', $client['sign_code'], $message);
			$message = str_replace('<:签到二维码:>', getShortUrl("$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]".U('Mobile/Client/myQRCode', [
					'mid' => $meeting['id'],
					'cid' => $client['id']
				])), $message);
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

		/**
		 * @param int   $mid           会议ID
		 * @param int   $message_type  信息类型 0：全部 1：短信 2：微信 3：收款后推送给开拓顾问 4：发送邀请
		 * @param int   $receiver_type 接收者类型 0：员工 1：参会人员
		 * @param int   $temp_type     消息模板类型
		 * @param array $receiver_list 接收者ID列表
		 *
		 * @return array
		 */
		public function send($mid, $message_type, $receiver_type, $temp_type, $receiver_list = []){
			/** @var \Core\Model\AssignMessageModel $assign_message_model */
			/** @var \Core\Model\MeetingModel $meeting_model */
			/** @var \Core\Model\EmployeeModel $employee_model */
			/** @var \Core\Model\ClientModel $client_model */
			/** @var \Core\Model\JoinModel $join_model $join_model */
			/** @var \Core\Model\MessageModel $message_model */
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model         = D('Core/WeixinID');
			$assign_message_model = D('Core/AssignMessage');
			$message_model        = D('Core/Message');
			$meeting_model        = D('Core/Meeting');
			$employee_model       = D('Core/Employee');
			$client_model         = D('Core/Client');
			$join_model           = D('Core/Join');
			$send_message_logic   = new SendMessageLogic();
			$sms_logic            = new SMSLogic();
			$wxcorp_logic         = new WxCorpLogic();
			$weixin_message_temp  = $sms_message_temp = [];
			// 查询出微信和微信的消息分配记录
			// ************************************************************
			// 注意：这里因为允许对一种操作类型分配多种消息模板
			//		# 消息模板A为微信模板 消息模板B为短信模板
			//		# M会议分配了消息模板A为签到提示 同时又分配了消息模板B为签到提示
			//		# 而分配消息表允许类型（区分何种操作）和会议ID相同
			//		# 所以根据消息类型、操作类型和会议ID查询分配记录会查询到多条记录
			//		# 这里取最后分配的那一条
			$weixin_assign_message = $assign_message_model->findRecord(1, [
				'mid'          => $mid,
				'type'         => $temp_type,
				'message_type' => 2
			]);
			$sms_assign_message    = $assign_message_model->findRecord(1, [
				'mid'          => $mid,
				'type'         => $temp_type,
				'message_type' => 1
			]);
			// ************************************************************
			$count          = ['weixin' => 0, 'sms' => 0];
			$meeting_record = $meeting_model->findMeeting(1, ['id' => $mid, 'status' => 'not deleted']);
			// 存在微信的消息分配记录 且指定发送微信信息或全部
			if($weixin_assign_message && ($message_type == 2 || $message_type == 0)){
				// 根据分配的消息记录查出微信的消息模板
				$weixin_message_temp = $message_model->findMessage(1, [
					'id'     => $weixin_assign_message['message_id'],
					'type'   => 2,
					'status' => 'not deleted'
				]);
				$found_temp          = true;
			}
			// 存在短信的消息分配记录 且指定发送短信信息或全部
			if($sms_assign_message && ($message_type == 1 || $message_type == 0)){
				// 根据分配的消息记录查出短信的消息模板
				$sms_message_temp = $message_model->findMessage(1, [
					'id'     => $sms_assign_message['message_id'],
					'type'   => 1,
					'status' => 'not deleted'
				]);
				$found_temp       = true;
			}
			// 不存在指定的消息记录
			if(!isset($found_temp) || !$found_temp) return ['status' => false, 'message' => '发送失败: 没有消息模板'];
			// 根据接收者逐一发送
			foreach($receiver_list as $val){
				if($receiver_type == 0){
					$employee = $employee_model->findEmployee(1, ['id' => $val, 'status' => 'not deleted']);
					switch($message_type){
						case 1:
							$content     = $this->replaceTempToMessage($sms_message_temp['context'], $meeting_record, $employee);
							$send_result = $sms_logic->send($content, [$employee['mobile']], true);
							if($send_result['status']){
								$count['sms']++;
								$send_message_logic->create($content, $employee['id'], 0, 1);
							}
							else $send_message_logic->create($content, $employee['id'], 0, 0);
						break;
						case 2:
							$weixin_record = $weixin_model->findRecord(1, [
								'oid'   => $employee['id'],
								'otype' => $receiver_type,
								'wtype' => 1
							]);
							$content       = $this->replaceTempToMessage($weixin_message_temp['context'], $meeting_record, $employee);
							$send_result   = $wxcorp_logic->sendMessage('text', $content, ['user' => [$weixin_record['weixin_id']]], 'client');
							if($send_result['status']){
								$count['weixin']++;
								$send_message_logic->create($content, $employee['id'], 0, 1);
							}
							else $send_message_logic->create($content, $employee['id'], 0, 0);
						break;
						case 0:
							$content     = $this->replaceTempToMessage($sms_message_temp['context'], $meeting_record, $employee);
							$send_result = $sms_logic->send($content, [$employee['mobile']], true);
							if($send_result['status']){
								$count['sms']++;
								$send_message_logic->create($content, $employee['id'], 0, 1);
							}
							else $send_message_logic->create($content, $employee['id'], 0, 0);
							$weixin_record = $weixin_model->findRecord(1, [
								'oid'   => $employee['id'],
								'otype' => $receiver_type,
								'wtype' => 1
							]);
							$content       = $this->replaceTempToMessage($weixin_message_temp['context'], $meeting_record, $employee);
							$send_result   = $wxcorp_logic->sendMessage('text', $content, ['user' => [$weixin_record['weixin_id']]], 'client');
							if($send_result['status']){
								$count['weixin']++;
								$send_message_logic->create($content, $employee['id'], 0, 1);
							}
							else $send_message_logic->create($content, $employee['id'], 0, 0);
						break;
					}
				}
				if($receiver_type == 1){
					$client              = $client_model->findClient(1, ['id' => $val, 'status' => 'not deleted']);
					$join_record         = $join_model->findRecord(1, [
						'mid'    => $mid,
						'cid'    => $client['id'],
						'status' => 'not deleted'
					]);
					$client['sign_code'] = $join_record['sign_code'];
					switch($message_type){
						case 1:
							$content     = $this->replaceTempToMessage($sms_message_temp['context'], $meeting_record, $client);
							$send_result = $sms_logic->send($content, [$client['mobile']], true);
							if($send_result['status']){
								$count['sms']++;
								$send_message_logic->create($content, $client['id'], 0, 1);
							}
							else $send_message_logic->create($content, $client['id'], 0, 0);
						break;
						case 2:
							$weixin_record = $weixin_model->findRecord(1, [
								'oid'   => $client['id'],
								'otype' => $receiver_type,
								'wtype' => 1
							]);
							$content       = $this->replaceTempToMessage($weixin_message_temp['context'], $meeting_record, $client);
							$send_result   = $wxcorp_logic->sendMessage('text', $content, ['user' => [$weixin_record['weixin_id']]], 'client');
							if($send_result['status']){
								$count['weixin']++;
								$send_message_logic->create($content, $client['id'], 0, 1);
							}
							else $send_message_logic->create($content, $client['id'], 0, 0);
						break;
						case 0:
							$content     = $this->replaceTempToMessage($sms_message_temp['context'], $meeting_record, $client);
							$send_result = $sms_logic->send($content, [$client['mobile']], true);
							if($send_result['status']){
								$count['sms']++;
								$send_message_logic->create($content, $client['id'], 0, 1);
							}
							else $send_message_logic->create($content, $client['id'], 0, 0);
							$weixin_record = $weixin_model->findRecord(1, [
								'oid'   => $client['id'],
								'otype' => $receiver_type,
								'wtype' => 1
							]);
							$content       = $this->replaceTempToMessage($weixin_message_temp['context'], $meeting_record, $client);
							$send_result   = $wxcorp_logic->sendMessage('text', $content, ['user' => [$weixin_record['weixin_id']]], 'client');
							if($send_result['status']){
								$count['weixin']++;
								$send_message_logic->create($content, $client['id'], 0, 1);
							}
							else $send_message_logic->create($content, $client['id'], 0, 0);
						break;
					}
				}
			}
			if($count['weixin'] == count($receiver_list) && $count['sms'] == count($receiver_list)) return [
				'status'  => true,
				'message' => '全部发送成功'
			];
			else{
				if($count['weixin'] == count($receiver_list)) return ['status' => true, 'message' => '微信信息全部发送成功'];
				if($count['sms'] == count($receiver_list)) return ['status' => true, 'message' => '短信信息全部发送成功'];
			}

			return ['status' => false, 'message' => '信息发送失败'];
		}
	}