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
				case 'create';
					/** @var \Core\Model\HotelModel $room_model */
					$room_model = D('Core/Hotel');
					C('TOKEN_ON', false);
					$data             = I('post.', 0, '');
					$data['creatime'] = time();    //创建时间
					$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					$room_result      = $room_model->createRecord($data);

					return array_merge($room_result, ['__ajax__' => false]);
				break;
				case 'delete';
					/** @var \Core\Model\HotelModel $room_model */
					$room_model = D('Core/Hotel');
					C('TOKEN_ON', false);
					$room_result = $room_model->deleteHotel(I('post.id'));

					return array_merge($room_result, ['__ajax__' => false]);
				break;
				case 'alter';
					/** @var \Core\Model\HotelModel $room_model */
					$room_model = D('Core/Hotel');
					C('TOKEN_ON', false);
					$room_result = $room_model->alterHotel(['id' => I('post.id', 0, 'int')], [
						'hotel_name' => I('post.hotel_name'),
						'mid'        => I('post.meeting_name'),
						'comment'    => I('post.comment')
					]);

					return array_merge($room_result, ['__ajax__' => false]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数'];
				break;
			}
		}

		public function setData($type, $data, $option = []){
			switch($type){
				case 'manage:set_meeting_column':
					$new_list = [];
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model  = D('Core/Meeting');
					$meeting_list   = $meeting_model->findMeeting(2, ['status' => 'not deleted']);
					$hotel_id_index = [];
					foreach($data as $key => $val){
						$hotel_id_index["index$val[id]"] = $key;
						$data[$key]['meeting_name']      = '';
						$data[$key]['meeting_status']    = null;
					}
					foreach($meeting_list as $val){
						$index = $hotel_id_index["index$val[hid]"];
						if(is_numeric($index)){
							$data[$index]['meeting_name']   = $val['name'];
							$data[$index]['meeting_status'] = $val['status'];
						}
					}
					foreach($data as $val){
						if($option['meetingType'] == 'using' && in_array($val['meeting_status'], [
								1,
								2,
								3
							])
						) $new_list[] = $val;
						elseif($option['meetingType'] == 'finish' && $val['meeting_status'] == 4) $new_list[] = $val;
						elseif($option['meetingType'] == 'not_use' && $val['meeting_status'] == null) $new_list[] = $val;
						elseif($option['meetingType'] == '') $new_list[] = $val;
					}

					return $new_list;
				break;
				default:
					return $data;
				break;
			}
		}
	}