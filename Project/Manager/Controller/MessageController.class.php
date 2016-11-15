<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 16:09
	 */
	namespace Manager\Controller;

	use Core\Logic\SMSLogic;
	use Manager\Logic\MessageLogic;
	use Think\Page;

	class MessageController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function manage(){
			$logic = new MessageLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['MESSAGE.VIEW']){
				/** @var \Core\Model\MessageModel $message_model */
				$message_model = D('Core/Message');
				$logic         = new MessageLogic();
				$option        = [];
				switch(I('get.type', '')){
					case 'sms':
						$option['type'] = 1;
					break;
					case 'wechat':
						$option['type'] = 2;
					break;
				}
				$list_count = $message_model->findMessage(0, array_merge([
					'keyword' => I('get.keyword', ''),
					'status'  => 'not deleted',
				], $option));
				/* 分页设置 */
				$page_object = new Page($list_count, C('PAGE_RECORD_COUNT'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show = $page_object->show();
				$list      = $message_model->findMessage(2, array_merge([
					'status'  => 'not deleted',
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
				], $option));
				$list      = $logic->setData('manage:set_using_status', $list, ['mid' => $this->meetingID]);
				$this->assign('list', $list);
				$this->assign('page_show', $page_show);
				$this->display();
			}
			else $this->error('您没有查看消息模块的权限');
		}

		public function create(){
			if(IS_POST){
				$logic  = new MessageLogic();
				$result = $logic->handlerRequest('create', ['mid' => $this->meetingID]);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['MESSAGE.CREATE']){
				$sms_logic = new SMSLogic();
				$number    = $sms_logic->getBalance();  //发送短信 第一个参数填内容， 第二个参数填手机号数组
				/** @var \Manager\Model\MeetingModel $meeting_model */
				$meeting_model = D('Meeting');
				$meeting_list  = $meeting_model->getMeetingForSelect();
				$this->assign('number', $number);
				$this->assign('meeting', $meeting_list);
				$this->display();
			}
			else $this->error('您没有创建消息的权限');
		}

		public function alter(){
			if(IS_POST){
				$logic  = new MessageLogic();
				$result = $logic->handlerRequest('alter', ['mid' => $this->meetingID]);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['MESSAGE.ALTER']){
				/** @var \Core\Model\MessageModel $message_model */
				$message_model  = D('Core/Message');
				$result_message = $message_model->findMessage(1, ['id' => I('get.id', 0, 'int')]);
				$this->assign('info', $result_message);
				$this->display();
			}
			else $this->error('您没有创建消息的权限');
		}
	}