<?php
	namespace Manager\Controller;

	use Manager\Logic\HotelLogic;
	use Manager\Logic\RoomLogic;

	class RoomController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
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
			/** @var \Manager\Model\RoomModel $room_model_st */
			$room_model_st = D('Room');
			/** @var \Core\Model\HotelModel $hotel_model */
			$hotel_model       = D('Core/Hotel');
			/** @var \Core\Model\RoomTypeModel $room_type_model */
			$room_type_model = D('Core/RoomType');
			$room_result_st    = $room_model_st->getRoomForSelect();
			$room_result       = $room_logic->findRoom();
			$meeting_result    = $room_logic->findMeeting();
			$join_result       = $room_logic->selectMeetingJoin();
			$hotel_result_name = $hotel_model->findHotel(1, ['id' => I('get.hid', 0, 'int')]);
			$room_type_result = $room_type_model->findRecord(2,['status'=>1,'hid'=>I('get.hid',0,'int')]);
			$this->assign('type',$room_type_result);
			$this->assign('list', $join_result);
			$this->assign('hotel_name', $hotel_result_name);
			$this->assign('lists', $join_result);
			$this->assign('room_info', $room_result);
			$this->assign('info', $meeting_result);
			$this->display();
		}

		public function roomType(){
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
			/** @var \Core\Model\RoomTypeModel $room_type_model */
			$room_type_model  = D('Core/RoomType');
			$room_type_result = $room_type_model->findRecord(2, [
				'status' => 'not deleted',
				'hid'=>I('get.hid',0,'int')
			]);
			$this->assign('type', $room_type_result);
			$this->display();
		}
	}