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
			if($this->permissionList['ROOM.VIEW']){
				/** @var \Core\Model\HotelModel $hotel_model */
				$hotel_model = D('Core/Hotel');
				/** @var \Core\Model\RoomTypeModel $room_type_model */
				$room_type_model = D('Core/RoomType');
				/** @var \Core\Model\JoinModel $join_model */
				$join_model                = D('Core/Join');
				$room_logic                = new RoomLogic();
				$hotel_id                  = I('get.hid', 0, 'int');
				$assigned_client_count     = $room_logic->getCheckInCount($hotel_id);
				$not_assigned_client_count = $room_logic->getNotCheckInCount($hotel_id, I('get.mid', 0, 'int'));
				$room_status               = $room_logic->getRoomStatus($hotel_id);
				$room_type_count           = $room_logic->getRoomTypeCount($hotel_id);
				$statistics                = [
					'assigned_employee'   => $assigned_client_count['employee'],
					'assigned_client'     => $assigned_client_count['client'],
					'not_assign_employee' => $not_assigned_client_count['employee'],
					'not_assign_client'   => $not_assigned_client_count['client'],
					'full_room'           => $room_status['full'],
					'available_room'      => $room_status['available'],
					'total'               => $join_model->findRecord(0, ['status' => 1, 'mid' => $this->meetingID])
				];
				$room_result               = $room_logic->findRoom();
				$meeting_result            = $room_logic->findMeeting();
				$hotel_result_name         = $hotel_model->findHotel(1, ['id' => $hotel_id]);
				$room_type_result          = $room_type_model->findRecord(2, ['status' => 1, 'hid' => $hotel_id]);
				$this->assign('type', $room_type_result);
				$this->assign('statistics', $statistics);
				$this->assign('hotel_name', $hotel_result_name);
				$this->assign('room_info', $room_result);
				$this->assign('info', $meeting_result);
				$this->assign('count', $room_type_count);
				$this->display();
			}
			else $this->error('您没有查看房间模块的权限');
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
			if($this->permissionList['ROOM_TYPE.VIEW']){
				/** @var \Core\Model\RoomTypeModel $room_type_model */
				$room_type_model = D('Core/RoomType');
				/** @var \Core\Model\RoomModel $room_model */
				$room_model       = D('Core/Room');
				$room_type_result = $room_type_model->findRecord(2, [
					'status'  => 'not deleted',
					'hid'     => I('get.hid', 0, 'int'),
					'keyword' => I('get.keyword', '')
				]);
				$number           = [];
				foreach($room_type_result as $k => $v){
					$number[] .= $v['id'];
				}
				foreach($room_type_result as $k => $v){
					$room_count                      = $room_model->findRoom(0, [
						'type'   => $v['id'],
						'status' => 1,
						'hid'    => I('get.hid', 0, 'int')
					]);
					$room_type_result[$k]['surplus'] = $v['number']-$room_count;
				}
				$room_type_count = $room_logic->getRoomTypeCount(I('get.hid', 0, 'int'));
				$this->assign('type', $room_type_result);
				$this->assign('count', $room_type_count);
				$this->display();
			}
			else $this->error('您没有查看房间类型模块的权限');
		}
	}