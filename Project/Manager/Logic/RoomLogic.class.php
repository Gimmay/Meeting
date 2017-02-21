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
				case 'create':
					if($this->permissionList['ROOM.CREATE']){
						/** @var \Core\Model\RoomModel $room_model */
						$room_model = D('Core/Room');
						/** @var \Core\Model\AssignRoomModel $assign_room_model */
						$assign_room_model = D('Core/AssignRoom');
						$room_id           = I('post.room_number', '');
						$room_list         = $room_model->findRoom(2, [
							'hid'    => I('get.hid', 0, 'int'),
							'status' => 'not deleted'
						]);
						$rid               = [];
						foreach($room_list as $k1 => $v1){
							if($room_list[$k1]['code'] == ''){
							}
							else{
								$rid[] = $room_list[$k1]['code'];
							}
						}
						if(in_array($room_id, $rid)){
							return array_merge(['message' => '房间号已存在'], ['__ajax__' => false]);
							exit;
						}
						else{
							C('TOKEN_ON', false);
							$data                = I('post.', '');
							$data['code']        = I('post.room_number', '');
							$data['mid']         = I('get.id', 0, 'int');
							$data['hid']         = I('get.hid', 0, 'int');
							$data['creatime']    = time();    //创建时间
							$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
							$data['person']      = I('post.person', '');
							$data['client_type'] = I('post.client_type', '');
							$room_result         = $room_model->createRecord($data);
							$person_id           = explode(',', $data['person']);
							foreach($person_id as $v){
								$data['mid']       = I('get.mid', 0, 'int');
								$data['rid']       = $room_result['id'];
								$data['cid']       = $v;
								$data['come_time'] = time();
								$data['creator']   = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
								$data['creatime']  = time();
								$assign_room_model->createRecord($data);
							}
						}

						return array_merge($room_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建房间的权限', '__ajax__' => false];
				break;
				case 'delete':
					if($this->permissionList['ROOM.DELETE']){
						/** @var \Core\Model\RoomModel $room_model */
						$room_model = D('Core/Room');
						C('TOKEN_ON', false);
						$room_result = $room_model->deleteRoom(I('post.id'));

						return array_merge($room_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有删除房间的权限', '__ajax__' => false];
				break;
				case 'choose_employee_2':
				case 'choose_client_2':
					if($this->permissionList['ROOM.ASSIGN']){
						/** @var \Core\Model\AssignRoomModel $assign_room_model */
						$assign_room_model = D('Core/AssignRoom');
						/** @var \Core\Model\JoinModel $join_model */
						$join_model         = D('Core/Join');
						$rid                = I('post.rid', '');
						$id                 = I('post.id', '');
						$assign_room_result = $assign_room_id = [];
						C('TOKEN_ON', false);
						foreach($id as $k => $v){
							$assign_room_result = $assign_room_model->createRecord([
								'rid'              => $rid,
								'cid'              => $v,
								'mid'              => I('get.mid', 0, 'int'),
								'come_time'        => time(),
								'occupancy_status' => 1,
								'status'           => 1,
								'creatime'         => time(),
								'creator'          => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')
								//当前创建者
							]);
							$assign_room_id[]   = $assign_room_result['id'];
						}
						foreach($assign_room_id as $k1 => $v2){
							$assign_room_results[]                 = $assign_room_model->findRecord(1, ['id' => $v2]);
							$join_result                           = $join_model->findRecord(1, [
								'mid' => I('get.mid', 0, 'int'),
								'cid' => $assign_room_results[$k1]['cid']
							]);
							$assign_room_results[$k1]['join_name'] = $join_result['name'];
						}

						return array_merge($assign_room_result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有分配房间的权限', '__ajax__' => true];
				break;
				case 'change_room':
					$keyword = I('post.keyword', '');
					$room_id = I('post.rid', '');
					/** @var \Core\Model\JoinModel $join_model */
					$join_model = D('Core/Join');
					/** @var \Core\Model\AssignRoomModel $assign_room_model */
					$assign_room_model = D('Core/AssignRoom');
					/** @var \Core\Model\RoomModel $room_model */
					$room_model  = D('Core/Room');
					$room_result = $room_model->findRoom(2, [
						'hid'     => I('get.hid', 0, 'int'),
						'status'  => 'not deleted',
						'keyword' => $keyword
					]);
					$new_list    = [];
					foreach($room_result as $k1 => $v1){
						if($v1['id'] == $room_id) continue;
						$assign_room_result = $assign_room_model->findRecord(2, [
							'rid'              => $v1['id'],
							'occupancy_status' => 1,
							'status'           => 1
						]);
						foreach($assign_room_result as $k3 => $v3){
							$join_result                     = $join_model->findRecord(1, [
								'mid' => I('get.mid', 0, 'int'),
								'cid' => $v3['cid']
							]);
							$assign_room_result[$k3]['name'] = $join_result['name'];
						}
						$v1['client'] = $assign_room_result;
						$new_list[]   = $v1;
					}

					return array_merge($new_list, ['__ajax__' => true]);
				break;
				case 'leave':
					if($this->permissionList['ROOM.ASSIGN']){
						C('TOKEN_ON', false);
						$id   = I('post.id', 0, 'int');
						$time = strtotime(I('post.leave_time', ''));
						$time = $time == 0 ? time() : $time;
						/** @var \Core\Model\AssignRoomModel $assign_room_model */
						$assign_room_model  = D('Core/AssignRoom');
						$assign_room_result = $assign_room_model->alterRecord([
							'cid' => $id,
							'mid' => I('get.mid', 0, 'int')
						], [
							'leave_time'       => $time,
							'occupancy_status' => 2
						]);

						return array_merge($assign_room_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有分配房间的权限', '__ajax__' => true];
				break;
				case 'get_employee':
					$room_logic  = new RoomLogic();
					$join_result = $room_logic->selectMeetingJoin('内部员工');

					return array_merge($join_result, ['__ajax__' => true]);
				break;
				case 'alter_room':
					if($this->permissionList['ROOM.ALTER']){
						/** @var \Core\Model\RoomModel $room_model */
						$room_model   = D('Core/Room');
						$data         = I('post.', '');
						$data['code'] = I('post.room_code', '');
						$id           = I('post.id', '');
						C('TOKEN_ON', false);
						$room_code = $room_model->findRoom(2, [
							'hid'    => I('get.hid', 0, 'int'),
							'status' => 'not deleted'
						]);
						$code_id   = [];
						foreach($room_code as $k => $v){
							$code_id[] = $room_code[$k]['code'];
						}
						$room_codes = $room_model->findRoom(1, ['id' => $id]);
						if($data['code'] == $room_codes['code']){
							$room_result = $room_model->alterRoom($id, $data);
						}
						elseif(in_array($data['code'], $code_id)){
							return array_merge(['message' => '当前房间已存在'], ['__ajax__' => false]);
						}
						else{
							$room_result = $room_model->alterRoom($id, $data);
						}

						return array_merge($room_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有修改房间的权限', '__ajax__' => false];
				break;
				case 'details':
					/** @var \Core\Model\AssignRoomModel $assign_room_model */
					$assign_room_model = D('Core/AssignRoom');
					/** @var \Core\Model\JoinModel $join_model */
					$join_model         = D('Core/Join');
					$assign_room_result = $assign_room_model->findRecord(2, ['rid' => I('post.id', 0, 'int')]);
					$join               = [];
					foreach($assign_room_result as $k1 => $v1){
						$leave_time       = $assign_room_result[$k1]['leave_time'];
						$occupancy_status = $assign_room_result[$k1]['occupancy_status'];
						$join_result      = $join_model->findRecord(1, [
							'mid' => I('get.mid', 0, 'int'),
							'cid' => $v1['cid']
						]);
						$join[]           = [
							'name'             => $join_result['name'],
							'id'               => $join_result['cid'],
							'leave_time'       => $leave_time,
							'occupancy_status' => $occupancy_status
						];
					}

					return array_merge($join, ['__ajax__' => true]);
				break;
				case 'alter':
					if($this->permissionList['ROOM.ALTER']){
						/** @var \Core\Model\RoomModel $room_model */
						$room_model = D('Core/Room');
						C('TOKEN_ON', false);
						$room_result = $room_model->alterRoom(I('post.id'), [
							'hotel_name' => I('post.hotel_name'),
							'mid'        => I('post.meeting_name'),
							'comment'    => I('post.comment')
						]);

						return array_merge($room_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有修改房间的权限', '__ajax__' => false];
				break;
				case 'room_add':
					if($this->permissionList['ROOM.CREATE']){
						/** @var \Core\Model\AssignRoomModel $assign_room_model */
						$assign_room_model = D('Core/AssignRoom');
						C('TOKEN_ON', false);
						$cid                = I('post.cid', 0, 'int');
						$orid               = I('post.orid', 0, 'int');
						$rid                = I('post.rid', 0, 'int');
						$assign_room_result = $assign_room_model->findRecord(1, [
							'mid'              => I('get.mid', 0, 'int'),
							'cid'              => $cid,
							'rid'              => $orid,
							'occupancy_status' => 1
						]);
						$assign_room_model->alterRecord(['id' => $assign_room_result['id']], ['occupancy_status' => 0]);
						$assign_room_creator = $assign_room_model->createRecord([
							'rid'       => $rid,
							'cid'       => $cid,
							'mid'       => I('get.mid', 0, 'int'),
							'come_time' => time(),
							'creatime'  => time(),
							'creator'   => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')
						]);

						return array_merge($assign_room_creator, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有创建房间的权限', '__ajax__' => false];
				break;
				case 'room_change':
					if($this->permissionList['ROOM.ASSIGN']){
						C('TOKEN_ON', false);
						$ocid = I('post.cid', '');    // 换房的客户
						$orid = I('post.orid', '');        // 换房的房间
						$cid  = I('post.ocid', '');     // 被换房的客户
						$rid  = I('post.rid', '');        // 被换房的房间
						$mid  = I('get.mid', 0, 'int');
						/** @var \Core\Model\AssignRoomModel $assign_room_model */
						$assign_room_model  = D('Core/AssignRoom');
						$assign_room_result = $assign_room_model->findRecord(1, [
							'mid'              => $mid,
							'cid'              => $ocid,
							'rid'              => $orid,
							'occupancy_status' => 1
						]); //查出选择的用户那个房间信息.
						$assign_room_model->alterRecord(['id' => $assign_room_result['id']], ['occupancy_status' => 0]);//把这个用户的住房状态改为已退房
						$assign_room_model->createRecord([
							'mid'       => $mid,
							'cid'       => $ocid,
							'rid'       => $rid,
							'come_time' => time(),
							'creatime'  => time(),
							'creator'   => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')
						]);//把这个用户换到跟他交换的房间里去
						$assign_room_results = $assign_room_model->findRecord(1, [
							'mid'              => $mid,
							'cid'              => $cid,
							'rid'              => $rid,
							'occupancy_status' => 1
						]); //查出选择的用户那个房间信息.
						$assign_room_model->alterRecord(['id' => $assign_room_results['id']], ['occupancy_status' => 0]);//把这个用户的住房状态改为已退房
						$assign_room_alter = $assign_room_model->createRecord([
							'mid'       => $mid,
							'cid'       => $cid,
							'rid'       => $orid,
							'come_time' => time(),
							'creatime'  => time(),
							'creator'   => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')
						]);//把这个用户换到跟他交换的房间里去
						return array_merge($assign_room_alter, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有分配房间的权限', '__ajax__' => true];
				break;
				case 'create_roomType':
					if($this->permissionList['ROOM_TYPE.CREATE']){
						/** @var \Core\Model\RoomTypeModel $room_type_model */
						$room_type_model  = D('Core/RoomType');
						$data             = I('post.', '');
						$data['creatime'] = time();
						$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$data['hid']      = I('get.hid', 0, 'int');
						$result           = $room_type_model->findRecord(2, ['hid' => I('get.hid', 0, 'int')]);
						foreach($result as $k => $v){
							if($data['name'] == $v['name']){
								return array_merge([
									'message' => '当前房间类型已存在',
									'status'  => false
								], ['__ajax__' => false]);
							}
						}
						$room_type_result = $room_type_model->createRecord($data);

						return array_merge($room_type_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建房间类型的权限', '__ajax__' => false];
				break;
				case 'alter_roomType':
					if($this->permissionList['ROOM_TYPE.ALTER']){
						/** @var \Core\Model\RoomTypeModel $room_type_model */
						$room_type_model  = D('Core/RoomType');
						$data             = I('post.');
						$room_type_result = $room_type_model->alterRecord(['id' => $data['id']], $data);

						return array_merge($room_type_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有修改房间类型的权限', '__ajax__' => false];
				break;
				case 'delete_roomType':
					if($this->permissionList['ROOM_TYPE.DELETE']){
						/** @var \Core\Model\RoomTypeModel $room_type_model */
						$room_type_model  = D('Core/RoomType');
						$id               = I('post.id', 0, 'int');
						$room_type_result = $room_type_model->deleteRecord(['id' => $id]);

						return array_merge($room_type_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有删除房间类型的权限', '__ajax__' => false];
				break;
				case 'get_type':
					$id = I('post.id', 0, 'int');
					/** @var \Core\Model\RoomTypeModel $room_type_model */
					$room_type_model = D('Core/RoomType');
					/** @var \Core\Model\RoomModel $room_model */
					$room_model                  = D('Core/Room');
					$room_result                 = $room_model->findRoom(0, [
						'type'   => $id,
						'status' => 1,
						'hid'    => I('get.hid', 0, 'int')
					]);
					$room_type_result            = $room_type_model->findRecord(1, ['id' => $id, 'status' => 1]);
					$number                      = $room_type_result['number']-$room_result;
					$room_type_result['surplus'] = $number;

					return array_merge($room_type_result, ['__ajax__' => true]);
				break;
				case 'get_room_type':
					$rid = I('post.id', 0, 'int');
					/** @var \Core\Model\RoomTypeModel $room_type_model */
					$room_type_model = D('Core/RoomType');
					/** @var \Core\Model\RoomModel $room_model */
					$room_model = D('Core/Room');
					/** @var \Core\Model\AssignRoomModel $assign_room_model */
					$assign_room_model = D('Core/AssignRoom');
					$room_type_result  = $room_type_model->findRecord(2, [
						'hid'    => I('get.hid', 0, 'int'),
						'status' => 1
					]);
					foreach($room_type_result as $k => $v){
						$room_count                      = $room_model->findRoom(0, [
							'type'   => $v['id'],
							'status' => 1,
							'hid'    => I('get.hid', 0, 'int')
						]);
						$room_type_result[$k]['surplus'] = $v['number']-$room_count;
					}
					$assign_room_result = $assign_room_model->findRecord(1, ['rid' => $rid]);
					if(!$assign_room_result){
						$assign_room_result = null;
					}
					else{
						$assign_room_result = true;
					}

					return ['data' => $room_type_result, 'status' => $assign_room_result, '__ajax__' => true];
				break;
				case 'get_client':
					$room_logic  = new RoomLogic();
					$join_result = $room_logic->selectMeetingJoin('not employee');

					return array_merge($join_result, ['__ajax__' => true]);
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
			$room_model = D('Core/Room');
			/** @var \Core\Model\RoomTypeModel $room_type_model */
			$room_type_model = D('Core/RoomType');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model  = D('Core/Join');
			$room_result = $room_model->findRoom(2, [
				'hid'     => I('get.hid', 0, 'int'),
				'status'  => 'not deleted',
				'keyword' => I('get.keyword', 0, 'int'),
			]);
			$join_result = [];
			foreach($room_result as $k => $v){
				$assign_room_result = $assign_room_model->findRecord(2, [
					'rid'              => $v['id'],
					'status'           => 1,
					'occupancy_status' => 1
				]);
				$check_name         = '';
				foreach($assign_room_result as $kk => $vv){
					$join_result = $join_model->findRecord(1, [
						'cid'     => $vv['cid'],
						'mid'     => I('get.mid', 0, 'int'),
						'keyword' => I('get.keyword', '')
					]);
					$check_name .= $join_result['name'].',';
				}
				$check                            = rtrim($check_name, ",");
				$room_type_result                 = $room_type_model->findRecord(1, ['id' => $v['type']]);
				$room_result[$k]['type_name']     = $room_type_result['name'];
				$employee_result                  = $employee_model->findEmployee(1, ['id' => $v['creator']]);
				$room_result[$k]['employee_name'] = $employee_result['name'];
				$room_result[$k]['count']         = $assign_room_model->findRecord(0, [
					'rid'              => $v['id'],
					'status'           => 1,
					'occupancy_status' => 1
				]);
				$room_result[$k]['check_name']    = $check;
				$join_result                      = $room_result;
			}
			if($_GET['keyword']){
				$new_list = [];
				foreach($join_result as $k => $v){
					if($v['check_name'] == '') continue;
					$new_list[] = $v;
				}

				return $new_list;
			}
			else{
				return $join_result;
			}
		}

		public function selectMeetingJoin($type){
			/** @var \Core\Model\JoinModel $join_model */
			$join_model  = D('Core/Join');
			$join_result = $join_model->findRecord(2, [
				'mid'           => I('get.mid', 0, 'int'),
				'keyword'       => I('post.keyword', ''),
				'_order'        => 'sign_time desc,pinyin_code asc',
				'status'        => 1,
				'review_status' => 1,
				'type'          => $type
			]);
			/** @var \Core\Model\AssignRoomModel $assign_room_model */
			$assign_room_model = D('Core/AssignRoom');
			/** @var \Core\Model\RoomModel $room_model */
			$room_model  = D('Core/Room');
			$room_result = $room_model->findRoom(2, ['hid' => I('get.hid', 0, 'int'), 'status' => 'not deleted']);
			$cid         = [];
			foreach($room_result as $k1 => $v1){
				$assign_room_result = $assign_room_model->findRecord(2, ['rid' => $v1['rid'], 'occupancy_status' => 1]);
				foreach($assign_room_result as $k2 => $v2) $cid[] = $v2['cid'];
			}
			$client_list = [];
			foreach($join_result as $k3 => $v3){
				if(in_array($v3['cid'], $cid)){
					continue;
				}
				else{
					$client_list[] = $v3;
				}
			}

			return $client_list;
		}

		public function getCheckInCount($hid){
			/** @var \Core\Model\RoomModel $room_model */
			$room_model = D('Core/Room');
			/** @var \Core\Model\AssignRoomModel $assign_room_model */
			$assign_room_model = D('Core/AssignRoom');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			$room_list    = $room_model->findRoom(2, ['hid' => $hid, 'status' => 1]);
			$count        = [
				'employee' => 0,
				'client'   => 0
			];
			foreach($room_list as $room){
				$assigned_room_list = $assign_room_model->findRecord(2, [
					'rid'              => $room['id'],
					'occupancy_status' => 1
				]);
				foreach($assigned_room_list as $val){
					$client = $client_model->findClient(1, ['id' => $val['cid']]);
					if($client['type'] == '内部员工') $count['employee']++;
					else $count['client']++;
				}
			}

			return $count;
		}

		public function getNotCheckInCount($hid, $mid){
			/** @var \Core\Model\RoomModel $room_model */
			$room_model = D('Core/Room');
			/** @var \Core\Model\AssignRoomModel $assign_room_model */
			$assign_room_model = D('Core/AssignRoom');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model    = D('Core/Join');
			$employee_list = $join_model->findRecord(2, [
				'mid'    => $mid,
				'type'   => '内部员工',
				'status' => 1
			]);
			$client_list   = $join_model->findRecord(2, [
				'mid'    => $mid,
				'type'   => 'not employee',
				'status' => 1
			]);
			$count         = [
				'employee' => 0,
				'client'   => 0
			];
			$room_list     = $room_model->findRoom(2, ['hid' => $hid, 'status' => 1]);
			foreach($room_list as $room){
				$assigned_room_list = $assign_room_model->findRecord(2, [
					'rid'              => $room['id'],
					'occupancy_status' => 1
				]);
				foreach($assigned_room_list as $val){
					$client = $client_model->findClient(1, ['id' => $val['cid']]);
					if($client['type'] == '内部员工') $count['employee']++;
					else $count['client']++;
				}
			}
			$count['employee'] = count($employee_list)-$count['employee'];
			$count['client']   = count($client_list)-$count['client'];

			return $count;
		}

		public function getRoomStatus($hid){
			/** @var \Core\Model\RoomModel $room_model */
			$room_model = D('Core/Room');
			/** @var \Core\Model\AssignRoomModel $assign_room_model */
			$assign_room_model = D('Core/AssignRoom');
			$room_list         = $room_model->findRoom(2, ['hid' => $hid, 'status' => 1]);
			$count             = [
				'full'      => 0,
				'available' => 0
			];
			foreach($room_list as $room){
				$check_in_client_count = $assign_room_model->findRecord(0, [
					'rid'              => $room['id'],
					'occupancy_status' => 1
				]);
				if($check_in_client_count>=$room['capacity']) $count['full']++;
				else $count['available']++;
			}

			return $count;
		}

		public function getRoomTypeCount($hid){
			/** @var \Core\Model\RoomModel $room_model */
			$room_model = D('Core/Room');
			/** @var \Core\Model\RoomTypeModel $room_type_model */
			$room_type_model  = D('Core/RoomType');
			$room_type_result = $room_type_model->findRecord(2, ['hid' => $hid, 'status' => 1]);
			$number           = 0;
			foreach($room_type_result as $k => $v){
				$number += $v['number'];
			}
			if($number == 0){
			}
			else{
				$room_count   = $room_model->findRoom(0, ['hid' => $hid, 'status' => 1]);
				$count        = $number-$room_count;
				$count_result = $count.'/'.$number;

				return $count_result;
			}
		}
	}