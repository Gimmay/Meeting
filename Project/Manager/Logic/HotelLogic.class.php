<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-8
	 * Time: 17:20
	 */
	namespace Manager\Logic;

	class HotelLogic extends ManagerLogic{

		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case'create';
					/** @var \Core\Model\HotelModel $room_model */
					$room_model = D('Core/Hotel');
					C('TOKEN_ON', false);
					$data             = I('post.', 0, '');
					$data['creatime'] = time();    //创建时间
					$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					$room_result      = $room_model->createRecord($data);

					return array_merge($room_result, ['__ajax__' => false]);
				break;
				case'delete';
					/** @var \Core\Model\HotelModel $room_model */
					$room_model = D('Core/Hotel');
					C('TOKEN_ON', false);
					$room_result = $room_model->deleteHotel(I('post.id'));

					return array_merge($room_result, ['__ajax__' => false]);
				break;
				case'alter';
					/** @var \Core\Model\HotelModel $room_model */
					$room_model = D('Core/Hotel');
					C('TOKEN_ON', false);
					$room_result = $room_model->alterHotel(I('post.id'), [
						'hotel_name' => I('post.hotel_name'),
						'mid'        => I('post.meeting_name'),
						'comment'    => I('post.comment')
					]);

					return array_merge($room_result, ['__ajax__' => false]);
				break;
			}
		}

		public function findHotel(){
			/** @var \Core\Model\HotelModel $room_model */
			$room_model  = D('Core/Hotel');
			$room_result = $room_model->carryTime();

			return $room_result;
		}

		public function findMeeting(){
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\HotelModel $hotel_model */
			$hotel_model = D('Core/Hotel');
			$meeting_result = $meeting_model->findMeeting(1, ['id'  => I('get.id', 0, 'int'),
															  'hid' => I('get.hid', 0, 'int')
			]);
			foreach($meeting_result as $k => $v){
				$hotel_result                 = $hotel_model->findHotel(1, ['id' => $meeting_result]['hid']);
				$meeting_result['hotel_name'] = $hotel_result['name'];
			}

			return $meeting_result;
		}

		public function selectMeetingJoin(){
			/** @var \Core\Model\JoinModel $join_model */
			$join_model  = D('Core/Join');
			$join_result = $join_model->findRecord(2, ['mid' => I('get.id', 0, 'int')]);

			return $join_result;
		}
	}