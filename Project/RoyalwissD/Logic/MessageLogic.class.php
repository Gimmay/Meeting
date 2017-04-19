<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-30
	 * Time: 14:46
	 */
	namespace RoyalwissD\Logic;

	use CMS\Controller\CMS;
	use CMS\Logic\Session;
	use CMS\Logic\UserLogic;
	use General\Logic\SMSMobset;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\ExternalInterface\Wechat\EnterpriseAccountLibrary;
	use Quasar\ExternalInterface\Wechat\OfficialAccountLibrary;
	use Quasar\Utility\StringPlus;
	use RoyalwissD\Model\MessageCorrelationModel;
	use RoyalwissD\Model\MessageModel;
	use stdClass;

	class MessageLogic extends RoyalwissDLogic{
		/**
		 * 处理POST/GET请求
		 *
		 * @param string $type 请求类型
		 * @param array  $opt  相关参数
		 *
		 * @return mixed
		 */
		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'save_configure':
					if(!in_array('SEVERAL-MESSAGE.CONFIGURE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有配置消息的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
					$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
					$meeting_configure_logic = new MeetingConfigureLogic();
					$meeting_id              = I('get.mid', 0, 'int');
					$post                    = I('post.');
					$result                  = $meeting_configure_model->modify(['mid' => $meeting_id], [
						'message_mode'                => $meeting_configure_logic->encodeMessageMode($post['message_sms'], $post['message_wechat_enterprise'], $post['message_wechat_official'], $post['message_email']),
						'wechat_official_configure'   => $post['wechat_official_configure'],
						'wechat_enterprise_configure' => $post['wechat_enterprise_configure'],
						'sms_mobset_configure'        => $post['sms_mobset_configure'],
						'email_configure'             => $post['email_configure']
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'create':
					if(!in_array('SEVERAL-MESSAGE.CREATE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有新建消息模板的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$post       = I('post.');
					/** @var \RoyalwissD\Model\MessageModel $message_model */
					$message_model = D('RoyalwissD/Message');
					$str_obj       = new StringPlus();
					$result        = $message_model->create(array_merge($post, [
						'mid'         => $meeting_id,
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'context'     => $_POST['context'],
						'creator'     => Session::getCurrentUser(),
						'creatime'    => Time::getCurrentTime()
					]));

					return array_merge($result, [
						'__ajax__' => true,
						'nextPage' => U('manage', ['mid' => $meeting_id])
					]);
				break;
				case 'assign_message':
					if(!in_array('SEVERAL-MESSAGE.USE_TO', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有使用消息模板的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$action     = I('post.action', '');
					$message_id = I('post.id', 0, 'int');
					/** @var \RoyalwissD\Model\MessageModel $message_model */
					$message_model = D('RoyalwissD/Message');
					$result        = $message_model->useTo($meeting_id, $message_id, $action);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify':
					if(!in_array('SEVERAL-MESSAGE.MODIFY', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有修改消息模板的权限',
						'__ajax__' => true
					];
					$id         = I('get.id', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					$post       = I('post.');
					/** @var \RoyalwissD\Model\MessageModel $message_model */
					$message_model = D('RoyalwissD/Message');
					$str_obj       = new StringPlus();
					$result        = $message_model->modify(['id' => $id, 'mid' => $meeting_id], array_merge($post, [
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'context'     => $_POST['context']
					]));

					return array_merge($result, [
						'__ajax__' => true,
						'nextPage' => U('manage', ['mid' => $meeting_id])
					]);
				break;
				case 'delete':
					if(!in_array('SEVERAL-MESSAGE.DELETE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有删除消息模板的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id         = explode(',', $id_str);
					/** @var \RoyalwissD\Model\MessageModel $message_model */
					$message_model = D('RoyalwissD/Message');
					$result        = $message_model->drop(['id' => ['in', $id], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable':
					if(!in_array('SEVERAL-MESSAGE.ENABLE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有启用消息模板的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id         = explode(',', $id_str);
					/** @var \RoyalwissD\Model\MessageModel $message_model */
					$message_model = D('RoyalwissD/Message');
					$result        = $message_model->enable(['id' => ['in', $id], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable':
					if(!in_array('SEVERAL-MESSAGE.DISABLE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有禁用消息模板的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id         = explode(',', $id_str);
					/** @var \RoyalwissD\Model\MessageModel $message_model */
					$message_model = D('RoyalwissD/Message');
					$result        = $message_model->disable(['id' => ['in', $id], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'update_send_report':
					if(!UserLogic::isPermitted('SEVERAL-MESSAGE.SEND_HISTORY-GET_SMS_SEND_STATUS')) return [
						'status'   => false,
						'message'  => '您没有更新消息发送状态的权限',
						'__ajax__' => true
					];
					$meeting_id  = I('get.mid', 0, 'int');
					$send_report = $this->getSMSStatus($meeting_id);
					if(!$send_report['status']) return array_merge($send_report, ['__ajax__' => true]);
					/** @var \RoyalwissD\Model\MessageSendHistoryModel $message_send_history_model */
					$message_send_history_model = D('RoyalwissD/MessageSendHistory');
					$this_database              = $message_send_history_model::DATABASE_NAME;
					$this_table                 = $message_send_history_model::TABLE_NAME;
					$replace_char               = '#%SMS_ID%#';
					$sql_main                   = "
UPDATE $this_database.$this_table
SET send_status =  CASE sms_id i$replace_char END
";
					$sql_where                  = "WHERE mid = $meeting_id AND sms_id IN ($replace_char) ";
					$count                      = 0;
					foreach($send_report['data'] as $value){
						$sql_where = str_replace($replace_char, "'$value[smsID]',$replace_char", $sql_where);
						$sql_main  = str_replace("i$replace_char", " WHEN '$value[smsID]' THEN '$value[code]'i$replace_char", $sql_main);
						$count++;
					}
					$sql    = "$sql_main $sql_where";
					$sql    = str_replace(",$replace_char", '', $sql);
					$sql    = str_replace("i$replace_char", '', $sql);
					$result = $message_send_history_model->execute($sql);
					if($result) return [
						'status'   => true,
						'message'  => "成功获取并更新[$count]条短信发送记录",
						'__ajax__' => true
					];
					else return [
						'status'   => false,
						'message'  => "获取失败更新失败",
						'__ajax__' => true
					];
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true];
				break;
			}
		}

		/**
		 * 设定额外数据
		 *
		 * @param string $type 操作类型
		 * @param mixed  $data 处理数据
		 *
		 * @return mixed
		 */
		public function setData($type, $data){
			switch($type){
				case 'manage':
					$get  = $data['urlParam'];
					$data = $data['list'];
					$list = [];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					foreach($data as $key => $message){
						// 1、筛选数据
						if(isset($keyword)){
							$found = 0;
							if($found == 0 && stripos($message['name'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($message['name_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						$message['status_code'] = $message['status'];
						$message['status']      = GeneralModel::STATUS[$message['status']];
						$message['type_code']   = $message['type'];
						$message['type']        = MessageModel::TYPE[$message['type']];
						$message['action_code'] = $message['action'];
						$message['action']      = [];
						foreach(explode(',', $message['action']) as $action){
							$message['action_list'][] = [
								'name' => MessageCorrelationModel::ACTION[$action],
								'id'   => $action
							];
							$message['action'][]      = MessageCorrelationModel::ACTION[$action];
						}
						$message['action'] = implode(', ', $message['action']);
						$list[]            = $message;
					}

					return $list;
				break;
				case 'sendHistory':
					$list = [];
					$get  = $data['urlParam'];
					$data = $data['list'];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					foreach($data as $key => $val){
						if(isset($keyword)){
							$found = 0;
							if($found == 0 && stripos($val['client'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($val['client_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($val['message'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($val['context'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						$val['type_code']        = $val['type'];
						$val['type']             = MessageModel::TYPE[$val['type']];
						$val['action_code']      = $val['action'];
						$val['action']           = MessageCorrelationModel::ACTION[$val['action']];
						$val['status_code']      = $val['status'];
						$val['status']           = GeneralModel::STATUS[$val['status']];
						$val['send_status_code'] = $val['send_status'];
						switch($val['type_code']){
							case 1:
								$val['send_status'] = SMSMobset::STATUS[$val['send_status']];
							break;
							case 2:
								$val['send_status'] = EnterpriseAccountLibrary::SEND_STATUS[$val['send_status']];
							break;
							case 3:
								$val['send_status'] = OfficialAccountLibrary::SEND_STATUS[$val['send_status']];
							break;
							case 4:
							break;
						}
						$list[] = $val;
					}

					return $list;
				break;
				default:
					return $data;
				break;
			}
		}

		/**
		 * 获取（首易）短信剩余条数
		 *
		 * @param $meeting_id
		 *
		 * @return int|number
		 */
		public function getSMSBalance($meeting_id){
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			$sms_mobset_configure    = $meeting_configure_model->getSMSMobsetConfigure($meeting_id);
			if(!$sms_mobset_configure['status']) return 0;
			else $sms_mobset_configure = $sms_mobset_configure['data'];
			$sms_mobset_obj = new SMSMobset($sms_mobset_configure['url'], $sms_mobset_configure['corpID'], $sms_mobset_configure['user'], $sms_mobset_configure['pass']);

			return $sms_mobset_obj->getBalance();
		}

		/**
		 * 获取（首易）短信的发送状态
		 *
		 * @param $meeting_id
		 *
		 * @return array
		 */
		public function getSMSStatus($meeting_id){
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			$sms_mobset_configure    = $meeting_configure_model->getSMSMobsetConfigure($meeting_id);
			if(!$sms_mobset_configure['status']) return $sms_mobset_configure;
			else $sms_mobset_configure = $sms_mobset_configure['data'];
			$sms_mobset_obj = new SMSMobset($sms_mobset_configure['url'], $sms_mobset_configure['corpID'], $sms_mobset_configure['user'], $sms_mobset_configure['pass']);

			return $sms_mobset_obj->getStatus();
		}

		/**
		 * 获取（首易）短信抬头
		 *
		 * @param $meeting_id
		 *
		 * @return int|number
		 */
		public function getSMSSign($meeting_id){
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			$sms_mobset_configure    = $meeting_configure_model->getSMSMobsetConfigure($meeting_id);
			if(!$sms_mobset_configure['status']) return null;
			else $sms_mobset_configure = $sms_mobset_configure['data'];
			$sms_mobset_obj = new SMSMobset($sms_mobset_configure['url'], $sms_mobset_configure['corpID'], $sms_mobset_configure['user'], $sms_mobset_configure['pass']);

			return $sms_mobset_obj->getSign();
		}

		/**
		 * 根据消息模板和系统动作发送消息
		 *
		 * @param int              $meeting_id 会议ID
		 * @param int|string|array $client_id  客户ID
		 * @param string           $message    消息内容
		 *
		 * @return array
		 */
		public function sendMessage($meeting_id, $client_id, $message){
			// todo
		}

		/**
		 * 根据消息模板和系统动作发送消息
		 *
		 * @param int              $meeting_id 会议ID
		 * @param int|string|array $client_id  客户ID
		 * @param int              $action     动作ID
		 *
		 * @return array
		 */
		public function sendMessageByAction($meeting_id, $client_id, $action){
			if(is_numeric($client_id)) $client_id = [$client_id];
			elseif(is_string($client_id)) $client_id = explode(',', $client_id);
			elseif(is_array($client_id)) ;
			else return ['status' => false, 'message' => '参数类型错误'];
			// 1、检测客户是否被禁用或者未审核
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			$client_list  = $client_model->getList([
				$client_model::CONTROL_COLUMN_PARAMETER['status']            => ['=', 1],
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus'] => ['=', 1],
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['clientID']     => [
					'in',
					'('.implode(',', $client_id).')'
				],
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID']    => $meeting_id
			]);
			if(count($client_list)<count($client_id)) return [
				'status'  => false,
				'message' => '发送消息失败：存在可能禁用/未审核的客户'
			];
			// 2、获取会议配置
			// 包括发送消息时对那些平台进行发送
			// 以及发送消息所使用的接口配置
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			if(!$meeting_configure_model->fetch(['mid' => $meeting_id])) return [
				'status'  => false,
				'message' => '找不到会议配置信息'
			];
			$meeting_configure       = $meeting_configure_model->getObject();
			$meeting_configure_logic = new MeetingConfigureLogic();
			$message_mode            = $meeting_configure_logic->decodeMessageMode($meeting_configure['message_mode']);
			/** @var \General\Model\ApiConfigureModel $api_configure_model */
			$api_configure_model = D('General/ApiConfigure');
			// 3、配置检测
			// ---如果开启SMS发送的话
			$sms_mobset_obj = new stdClass();
			if($message_mode['sms'] == 1){
				if(!$api_configure_model->fetch(['id' => $meeting_configure['sms_mobset_configure']])) return [
					'status'  => false,
					'message' => '缺少短信接口的配置参数'
				];
				$sms_mobset_configure = $api_configure_model->getObject();
				$sms_mobset_obj       = new SMSMobset($sms_mobset_configure['sms_mobset_url'], $sms_mobset_configure['sms_mobset_corpid'], $sms_mobset_configure['sms_mobset_user'], $sms_mobset_configure['sms_mobset_pass']);
				$sms_flag             = 1;
			}
			else $sms_flag = 0;
			// ---如果开启微信企业号发送的话
			$wechat_enterprise_obj = new stdClass();
			$appid                 = 0;
			if($message_mode['wechatEnterprise'] == 1){
				if(!$api_configure_model->fetch(['id' => $meeting_configure['wechat_enterprise_configure']])) return [
					'status'  => false,
					'message' => '缺少微信企业号接口的配置参数'
				];
				$wechat_enterprise_configure = $api_configure_model->getObject();
				$appid                       = $wechat_enterprise_configure['wechat_enterprise_appid'];
				$wechat_enterprise_obj       = new EnterpriseAccountLibrary($wechat_enterprise_configure['wechat_enterprise_corpid'], $wechat_enterprise_configure['wechat_enterprise_corpsecret']);
				$wechat_enterprise_flag      = 1;
			}
			else $wechat_enterprise_flag = 0;
			// ---如果开启微信公众号发送的话
			$wechat_official_obj = new stdClass();
			if($message_mode['wechatOfficial'] == 1){
				if(!$api_configure_model->fetch(['id' => $meeting_configure['wechat_official_configure']])) return [
					'status'  => false,
					'message' => '缺少微信公众号接口的配置参数'
				];
				$wechat_official_configure = $api_configure_model->getObject();
				$wechat_official_obj       = new OfficialAccountLibrary($wechat_official_configure['wechat_official_appid'], $wechat_official_configure['wechat_official_appsecret']);
				$wechat_official_flag      = 1;
			}
			else $wechat_official_flag = 0;
			// ---如果开启邮件发送的话
			if($message_mode['email'] == 1){
				if(!$api_configure_model->fetch(['id' => $meeting_configure['email_configure']])) return [
					'status'  => false,
					'message' => '缺少邮件发送的配置参数'
				];
				// todo
			}
			else $email_flag = 0;
			/** @var \RoyalwissD\Model\MessageModel $message_model */
			$message_model = D('RoyalwissD/Message');
			$send_result   = [
			];
			// 4、如果开启SMS发送的话
			if($sms_flag == 1){
				$message = $message_model->getMessage($meeting_id, 1, $action);
				if($message['context'] !== null){
					$send_result['sms'] = [
						'type'  => MessageModel::TYPE[1],
						'count' => 0
					];
					/** @var \RoyalwissD\Model\MessageSendHistoryModel $message_send_history_model */
					$message_send_history_model = D('RoyalwissD/MessageSendHistory');
					$client_list                = $this->_analysisMessage($meeting_id, $client_list, $message['context']);
					foreach($client_list as $client){
						$result = $sms_mobset_obj->send($client['send_message'], [$client['mobile']], true);
						if($result['status']){
							$message_send_history_model->create([
								'mid'         => $meeting_id,
								'cid'         => $client['cid'],
								'message_id'  => $message['id'],
								'context'     => $client['send_message'],
								'sms_id'      => $result['data'][0]['SmsID'],
								'type'        => 1,
								'action'      => $action,
								'send_status' => 15,
								'creator'     => Session::getCurrentUser(),
								'creatime'    => Time::getCurrentTime()
							]);
							$send_result['sms']['count']++;
						}
						else $message_send_history_model->create([
							'mid'         => $meeting_id,
							'cid'         => $client['cid'],
							'message_id'  => $message['id'],
							'context'     => $client['send_message'],
							'type'        => 1,
							'action'      => $action,
							'send_status' => 11,
							'creator'     => Session::getCurrentUser(),
							'creatime'    => Time::getCurrentTime()
						]);
					}
				}
			}
			// 5、如果开启微信企业号推送的话
			if($wechat_enterprise_flag == 1){
				$message = $message_model->getMessage($meeting_id, 2, $action);
				if($message['context'] !== null){
					$send_result['wechatEnterprise'] = [
						'type'  => MessageModel::TYPE[2],
						'count' => 0
					];
					/** @var \RoyalwissD\Model\MessageSendHistoryModel $message_send_history_model */
					$message_send_history_model = D('RoyalwissD/MessageSendHistory');
					$client_list                = $this->_analysisMessage($meeting_id, $client_list, $message['context']);
					foreach($client_list as $client){
						$result = $wechat_enterprise_obj->sendMessage($wechat_enterprise_obj::MESSAGE_TYPE['text'], $client['send_message'], $appid, ['user' => [$client['wechat_userid']]]);
						if($result['status']){
							$message_send_history_model->create([
								'mid'         => $meeting_id,
								'cid'         => $client['cid'],
								'message_id'  => $message['id'],
								'context'     => $client['send_message'],
								'type'        => 2,
								'action'      => $action,
								'send_status' => 1,
								'creator'     => Session::getCurrentUser(),
								'creatime'    => Time::getCurrentTime()
							]);
							$send_result['wechatEnterprise']['count']++;
						}
						else $message_send_history_model->create([
							'mid'         => $meeting_id,
							'cid'         => $client['cid'],
							'message_id'  => $message['id'],
							'context'     => $client['send_message'],
							'type'        => 2,
							'action'      => $action,
							'send_status' => 0,
							'creator'     => Session::getCurrentUser(),
							'creatime'    => Time::getCurrentTime()
						]);
					}
				}
			}
			// 6、如果开启微信公众号推送的话
			if($wechat_official_flag == 1){
				$message = $message_model->getMessage($meeting_id, 3, $action);
				if($message !== null){
				}
				// todo
			}
			// 7、如果开启邮件提醒的话
			//						if($email_flag == 1){
			//							$message = $message_model->getMessage($meeting_id, 4, $action);
			//							if($message!==null){
			//
			//							}
			//							// todo
			//						}
			return ['status' => true, 'message' => '发送请求已提交', 'data' => $send_result];
		}

		private function _analysisMessage($meeting_id, $client_list, $message_module){
			/** @var \RoyalwissD\Model\MeetingModel $meeting_model */
			$meeting_model  = D('RoyalwissD/Meeting');
			$meeting        = $meeting_model->getList([
				$meeting_model::CONTROL_COLUMN_PARAMETER['status']         => ['<>', 2],
				$meeting_model::CONTROL_COLUMN_PARAMETER_SELF['user']      => Session::getCurrentUser(),
				$meeting_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => ['=', $meeting_id]
			]);
			$meeting        = $meeting[0];
			$message_module = str_replace('<:会议名称:>', $meeting['name'], $message_module);
			$message_module = str_replace('<:会议地点:>', $meeting['place'], $message_module);
			$message_module = str_replace('<:会议开始时间:>', $meeting['start_time'], $message_module);
			$message_module = str_replace('<:会议结束时间:>', $meeting['end_time'], $message_module);
			$message_module = str_replace('<:会议签到开始时间:>', $meeting['sign_start_time'], $message_module);
			$message_module = str_replace('<:会议签到结束时间:>', $meeting['sign_end_time'], $message_module);
			$message_module = str_replace('<:会议负责人:>', $meeting['director'], $message_module);
			$message_module = str_replace('<:会议主办方:>', $meeting['host'], $message_module);
			$message_module = str_replace('<:会议策划方:>', $meeting['plan'], $message_module);
			$message_module = str_replace('<:会议简介:>', $meeting['director'], $message_module);
			foreach($client_list as $key => $client){
				$message                           = $message_module;
				$message                           = str_replace('<:客户名称:>', $client['name'], $message);
				$message                           = str_replace('<:客户会所:>', $client['unit'], $message);
				$message                           = str_replace('<:客户手机号:>', $client['mobile'], $message);
				$message                           = str_replace('<:客户二维码:>', $client['sign_qrcode'], $message);
				$message                           = str_replace('<:客户签到二维码:>', $client['sign_code_qrcode'], $message);
				$message                           = str_replace('<:客户房间号:>', $client['hotel_room_name'], $message);
				$message                           = str_replace('<:客户所在分组:>', $client['group_name'], $message);
				$client_list[$key]['send_message'] = $message;
			}

			return $client_list;
		}
	}