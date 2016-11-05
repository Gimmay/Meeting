<?php
	namespace Manager\Controller;

	use Manager\Logic\HotelLogic;
	use Manager\Logic\RoomLogic;

	class RoomController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$room_logic = new RoomLogic();
			if(IS_POST){
				$type = I('post.requestType');
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
			/** @var \Manager\Model\RoomModel $room_model_st */
			$room_model_st = D('Room');
			$room_result_st = $room_model_st->getRoomForSelect();
			$room_result = $room_logic->findRoom();
			$meeting_result = $room_logic->findMeeting();
			$join_result = $room_logic->selectMeetingJoin();
			$this->assign('list',$join_result);
			$this->assign('lists',$join_result);
			$this->assign('room_info',$room_result);
			$this->assign('info',$meeting_result);
			$this->display();
		}
	}