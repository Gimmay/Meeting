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
					$id         = I('post.id', 0, 'int');
					$data  = I('post.','');
					C('TOKEN_ON', false);
					$room_result = $room_model->alterHotel(['id' => $id],
						$data
					);

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
						$data[$key]['meeting_status']    = [];
					}
					foreach($meeting_list as $val){
						$hid_arr    = explode(',', $val['hid']);
						$last_index = null;
						foreach($hid_arr as $key2 => $val2){
							$index = $hotel_id_index["index$val2"];
							if(is_numeric($index)){
								$last_index = $index;
								$data[$index]['meeting_name'] .= "$val[name], ";
								$data[$index]['meeting_status'][] = $val['status'];
							}
						}
						if(!($last_index === null)) $data[$last_index]['meeting_name'] = trim($data[$last_index]['meeting_name'], ', ');
					}
					foreach($data as $val){
						$condition_1 = in_array(1, $val['meeting_status']);
						$condition_2 = in_array(2, $val['meeting_status']);
						$condition_3 = in_array(3, $val['meeting_status']);
						$condition_4 = in_array(4, $val['meeting_status']);
						if($option['meetingType'] == 'using' && ($condition_1 || $condition_2 || $condition_3)) $new_list[] = $val;
						elseif($option['meetingType'] == 'finish' && $condition_4) $new_list[] = $val;
						elseif($option['meetingType'] == 'not_use' && count($val['meeting_status']) == 0) $new_list[] = $val;
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