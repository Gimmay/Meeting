<?php
	/**Created by PhpStorm */
	namespace RoyalwissD\Controller;

	use CMS\Logic\PageLogic;
	use RoyalwissD\Logic\MeetingConfigureLogic;
	use RoyalwissD\Logic\MessageLogic;
	use RoyalwissD\Model\MessageCorrelationModel;
	use RoyalwissD\Model\MessageModel;
	use Think\Page;

	class MessageController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
			$this->initMeetingID();
		}

		public function create(){
			$message_logic = new MessageLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $message_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			/** @var \RoyalwissD\Model\MessageModel $message_model */
			$message_model = D('RoyalwissD/Message');
			$this->assign('message_type_list', $message_model::TYPE);
			$this->display();
		}

		public function manage(){
			$message_logic = new MessageLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $message_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			$this->assign('message_action', MessageCorrelationModel::ACTION);
			// 获取会议配置信息 以及 消息发送模式
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			$meeting_configure_logic = new MeetingConfigureLogic();
			if(!$meeting_configure_model->fetch(['mid' => $this->meetingID])) $this->error('找不到会议配置信息');
			$meeting_configure = $meeting_configure_model->getObject();
			$message_mode      = $meeting_configure_logic->decodeMessageMode($meeting_configure['message_mode']);
			$this->assign('message_mode', $message_mode);
			$this->assign('meeting_configure', $meeting_configure);
			// 获取接口配置信息
			/** @var \RoyalwissD\Model\MeetingModel $meeting_model */
			$meeting_model = D('RoyalwissD/Meeting');
			$meeting_type  = $meeting_model->getMeetingType($this->meetingID);
			/** @var \General\Model\ApiConfigureModel $api_configure_model */
			$api_configure_model = D('General/ApiConfigure');
			$api_configure_list  = $api_configure_model->getSelectedList($meeting_type);
			$this->assign('api_configure_list', $api_configure_list);
			// 获取列表数据
			/** @var \RoyalwissD\Model\MessageModel $message_model */
			$message_model        = D('RoyalwissD/Message');
			$model_control_column = $this->getModelControl();
			$option               = [];
			if(isset($_GET['type'])){
				switch(I('get.type', '')){
					case 'sms':
						$option['type'] = ['=', 1];
						$sms_balance    = $message_logic->getSMSBalance($this->meetingID);
						$this->assign('sms_balance', $sms_balance);
					break;
					case 'wechatEnterprise':
						$option['type'] = ['=', 2];
					break;
					case 'wechatOfficial':
						$option['type'] = ['=', 3];
					break;
					case 'email':
						$option['type'] = ['=', 4];
					break;
				}
			}
			$list        = $message_model->getList(array_merge($model_control_column, $option, [
				$message_model::CONTROL_COLUMN_PARAMETER['status']         => ['<>', 2],
				$message_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			$page_object = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$pagination = $page_object->show();
			$list       = $message_logic->setData('manage', $list);
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
			$this->display();
		}

		public function modify(){
			$message_logic = new MessageLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $message_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			/** @var \RoyalwissD\Model\MessageModel $message_model */
			$message_model = D('RoyalwissD/Message');
			$message_id    = I('get.id', 0, 'int');
			if(!$message_model->fetch(['id' => $message_id, 'mid' => $this->meetingID])) $this->error('找不到消息模板');
			$this->assign('message', $message_model->getObject());
			$this->assign('message_type_list', $message_model::TYPE);
			$this->display();
		}
	}
