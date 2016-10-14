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
			$logic = new MessageLogic();
			$list  = $logic->getAllMessage();
			$this->assign('list', $list);
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
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			$meeting_list  = $meeting_model->getMeetingForSelect();
			$this->assign('meeting', $meeting_list);
			$this->display();
		}

	}