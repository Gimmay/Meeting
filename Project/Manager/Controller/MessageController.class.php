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
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			/** @var \Core\Model\MessageModel $message_model */
			$message_model = D('Core/Message');
			$list_count = $message_model->findMessage(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 'not deleted',

			]);
			/* 分页设置 */
			$page_object = new Page($list_count, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			$list          = $message_model->findMessage(2,[
				'status'=>'not deleted',
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
			]);
			foreach($list as $k => $v){
				$employee_result     = $employee_model->findEmployee(1, ['id' => $list[$k]['creator']]);
				$list[$k]['creator'] = $employee_result['name'];
			}
			if(IS_POST){
				$data = I('post.id','');
				$result = $logic->deleteMessage($data);
				if($result['status']) $this->success($result['message']);
				else $this->error($result['message'], '', 3);
				exit;
			}
			$this->assign('list', $list);
			$this->assign('page_show', $page_show);
			$this->display();
		}

		public function create(){
			$logic = new MessageLogic();
			if(IS_POST){
				$type   = I('post.requestType', '');
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
			$sms_logic = new SMSLogic();
			$number  = $sms_logic->getBalance();  //发送短信 第一个参数填内容， 第二个参数填手机号数组

			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			$meeting_list  = $meeting_model->getMeetingForSelect();
			$this->assign('number',$number);
			$this->assign('meeting', $meeting_list);
			$this->display();
		}

		public function alter(){
			/** @var \Core\Model\MessageModel $message_model */
			$message_model  = D('Core/Message');
			$result_message = $message_model->findMessage(1, ['id' => I('get.id', 0, 'int')]);
			if(IS_POST){
				$logic  = new MessageLogic();
				$result = $logic->alterMessage();
				if($result['status']) $this->success($result['message'],U('Message/manage'));
				else $this->error($result['message'], '', 3);
				exit;
			}
			$this->assign('info', $result_message);
			$this->display();
		}
	}