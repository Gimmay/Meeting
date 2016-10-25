<?php
	namespace Manager\Controller;

	use Manager\Logic\RoomLogic;

	class RoomController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$room_logic = new RoomLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $room_logic->handlerRequest($type);
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
			$find_room = $room_logic->findHotel();
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			$meeting_list  = $meeting_model->getMeetingForSelect();
			$this->assign('info', $find_room);
			$this->assign('meeting', $meeting_list);
			$this->display();
		}

	}