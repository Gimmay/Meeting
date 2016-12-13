<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-29
	 * Time: 14:38
	 */
	namespace Open\Controller;

	use Open\Logic\ComputerLogic;

	class ComputerController extends OpenController{
		public function _initialize(){
			parent::_initialize();
		}

		public function signResult(){
			$mid = I('get.mid', 0, 'int');
			if(IS_POST){
				$logic  = new ComputerLogic();
				$result = $logic->handlerRequest(I('post.requestType', ''), ['mid' => $mid]);
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
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$meeting       = $meeting_model->findMeeting(1, ['id' => $mid]);
			$this->assign('meeting', $meeting);
			$this->display();
		}
	}