<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 16:09
	 */
	namespace Manager\Controller;

	use Manager\Logic\MessageLogic;

	class MessageController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$logic  = new MessageLogic();
			$result = $logic->findManage();
			$this->assign('list', $result);
			$this->display();
		}

		public function create(){
			$logic = new MessageLogic();
			$meeting = $logic->findMeeting();
			if(IS_POST){
				$result = $logic->createMessage();
				if($result['status']) $this->success($result['message'], U('create'));
				else $this->error($result['message']);
				exit;
			}
			
			$this->assign('meeting',$meeting);
			$this->display();
		}
	}