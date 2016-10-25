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
				case'create';
					/** @var \Core\Model\RoomModel $room_model */
					$room_model = D('Core/Room');
					C('TOKEN_ON', false);
					$data['comment']    = I('post.comment');
					$data['hotel_name'] = I('post.hotel_name', '');
					$data['creatime']   = time();    //创建时间
					$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					$data['status']     = 1;
					$data['mid']        = I('post.meeting_name', '');
					$room_result        = $room_model->createRecord($data);

					return array_merge($room_result, ['__ajax__' => false]);
				break;
				case'delete';
					/** @var \Core\Model\RoomModel $room_model */
					$room_model = D('Core/Room');
					C('TOKEN_ON', false);
					$room_result = $room_model->deleteRoom(I('post.id'));

					return array_merge($room_result, ['__ajax__' => false]);
				break;
				case'alter';
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
	}