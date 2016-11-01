<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-8
	 * Time: 17:20
	 */
	namespace Manager\Logic;

	class RoomLogic extends ManagerLogic{

		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'create';
					C('TOKEN_ON', false);
					/** @var \Core\Model\RoomModel $room_model */
					$room_model          = D('Core/Room');
					$data                = I('post.', '');
					$data['type']        = I('post.room_type', '');
					$data['code']        = I('post.room_number', '');
					$data['mid']         = I('get.id', 0, 'int');
					$data['hid']         = I('get.hid', 0, 'int');
					$data['creatime']    = time();    //创建时间
					$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					$data['person']      = I('post.person', '');
					$data['client_type'] = I('post.client_type', '');
					$room_result         = $room_model->createRecord($data);
					/** @var \Core\Model\AssignRoomModel $assign_room_model */
					$assign_room_model = D('Core/AssignRoom');
					$person_id         = explode(',', $data['person']);
					foreach($person_id as $v){
						$data['rid']        = $room_result['id'];
						$data['jid']        = $v;
						$data['come_time']  = strtotime(I('post.come_time'));
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$data['creatime']   = time();
						$assign_room_result = $assign_room_model->createRecord($data);
					}

					return array_merge($room_result, ['__ajax__' => false]);
				break;
				case 'delete';
					/** @var \Core\Model\RoomModel $room_model */
					$room_model = D('Core/Room');
					C('TOKEN_ON', false);
					$room_result = $room_model->deleteRoom(I('post.id'));

					return array_merge($room_result, ['__ajax__' => false]);
				break;
				case 'change_room';
					$id = I('post.id', '');
					/** @var \Core\Model\AssignRoomModel $assign_room_model */
					$assign_room_model  = D('Core/AssignRoom');
					$assign_room_result = $assign_room_model->findAssignRoom(2, [
						'jid'              => $id,
						'status'           => 'not deleted',
						'occupancy_status' => 1
					]);

					return array_merge($assign_room_result, ['__ajax__' => true]);
				break;
				case 'leave';
					C('TOKEN_ON', false);
					$id   = I('post.id', 0, 'int');
					$time = strtotime(I('post.leave_time', ''));
					/** @var \Core\Model\AssignRoomModel $assign_room_model */
					$assign_room_model  = D('Core/AssignRoom');
					$assign_room_result = $assign_room_model->alterAssignRoom(['jid' => $id], ['leave_time' => $time]);

					return array_merge($assign_room_result, ['__ajax__' => false]);
				break;
				case 'alter_room';
					$room_id = I('get.rid');
					$join_id = I('post.jid');
					/** @var \Core\Model\AssignRoomModel $assign_room_model */
					$assign_room_model  = D('Core/AssignRoom');
					$assign_room_result = $assign_room_model->alterAssignRoom();

					return array_merge($assign_room_result, ['__ajax__' => false]);
				break;
				case 'details';
					/** @var \Core\Model\AssignRoomModel $assign_room_model */
					$assign_room_model = D('Core/AssignRoom');
					/** @var \Core\Model\JoinModel $join_model */
					$join_model         = D('Core/Join');
					$assign_room_result = $assign_room_model->findAssignRoom(2, ['rid' => I('post.id', '')]);
					$id                 = [];
					foreach($assign_room_result as $k => $v){
						$id[] = $assign_room_result[$k]['jid'];
					}
					$join = [];
					foreach($id as $k1 => $v1){
						$leave_time  = $assign_room_result[$k1]['leave_time'];
						$join_result = $join_model->findRecord(1, ['id' => $v1]);
						$join[]      = [
							'name'       => $join_result['name'],
							'id'         => $join_result['id'],
							'leave_time' => $leave_time
						];
					}

					return array_merge($join, ['__ajax__' => true]);
				break;
				case 'alter';
					/** @var \Core\Model\RoomModel $room_model */
					$room_model = D('Core/Room');
					C('TOKEN_ON', false);
					$room_result = $room_model->alterRoom(I('post.id'), [
						'hotel_name' => I('post.hotel_name'),
						'mid'        => I('post.meeting_name'),
						'comment'    => I('post.comment')
					]);

					return array_merge($room_result, ['__ajax__' => false]);
				break;
				default:
					return ['status' => false, 'message' => '缺少参数'];
				break;
			}
		}

		public function findHotel(){
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/employee');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\RoomModel $room_model */
			$room_model  = D('Core/Room');
			$room_result = $room_model->findRoom(2, [
				'keyword' => I('get.keyword', ''),
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
				'status'  => 'not deleted',
			]);
			foreach($room_result as $k => $v){
				$meeting_result                  = $meeting_model->findMeeting(1, ['id' => $room_result[$k]['mid']]);
				$room_result[$k]['meeting_name'] = $meeting_result['name'];
				$employee_result                 = $employee_model->findEmployee(1, ['id' => $room_result[$k]['creator']]);
				$room_result[$k]['client_name']  = $employee_result['name'];
				$room_result[$k]['mobile']       = $employee_result['mobile'];
			}

			return $room_result;
		}

		public function findMeeting(){
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\HotelModel $hotel_model */
			$hotel_model    = D('Core/Hotel');
			$meeting_result = $meeting_model->findMeeting(1, [
				'id'  => I('get.id', 0, 'int'),
				'hid' => I('get.hid', 0, 'int')
			]);
			foreach($meeting_result as $k => $v){
				$hotel_result                 = $hotel_model->findHotel(1, ['id' => $meeting_result]['hid']);
				$meeting_result['hotel_name'] = $hotel_result['name'];
			}

			return $meeting_result;
		}

		public function findRoom(){
			/** @var \Core\Model\AssignRoomModel $assign_room_model */
			$assign_room_model = D('Core/AssignRoom');
			/** @var \Core\Model\RoomModel $room_model */
			$room_model  = D('Core/Room');
			$room_result = $room_model->findRoom(2, [
				'status'  => 'not deleted',
				'keyword' => I('get.keyword', 0, 'int')
			]);
			foreach($room_result as $k => $v){
				$room_result[$k]['count'] = $assign_room_model->findAssignRoom(0, ['rid' => $room_result[$k]['id']]);
			}

			return $room_result;
		}

		public function selectMeetingJoin(){
			/** @var \Core\Model\JoinModel $join_model */
			$join_model  = D('Core/Join');
			$join_result = $join_model->findRecord(2, ['mid' => I('get.id', 0, 'int'), 'keyword' => I('get.keyword')]);

			/** @var \Core\Model\AssignRoomModel $assign_room_model */
			$assign_room_model = D('Core/AssignRoom');
			/** @var \Core\Model\RoomModel $room_model */
			$room_model  = D('Core/Room');
			$room_result = $room_model->findRoom(2, ['hid' => I('get.hid', 0, 'int')]);
			$id          = [];
			$jid         = [];
			foreach($room_result as $k => $v){
				$id [] = $room_result[$k]['id'];
			}
			foreach($id as $k1 => $v1){
				$assign_room_result = $assign_room_model->findAssignRoom(2, ['rid' => $v1]);
				foreach($assign_room_result as $k2 => $v2){
					$jid []  = $assign_room_result[$k2]['jid'];

				}
			}
			$client_list = [];
			foreach ($join_result as $k3=>$v3){
				if(in_array($v3['id'],$jid)){
					continue;
				}else{
					$client_list[]=$v3;
				}
			}

			return $client_list;
		}

		public function selectHotelRoom(){
			return $this->field('name html, id value, name keyword')->where("status != 5 and status != 0")->select();
		}
	}