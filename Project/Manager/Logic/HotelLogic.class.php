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
					if($this->permissionList['HOTEL.CREATE']){
						/** @var \Core\Model\HotelModel $room_model */
						$room_model = D('Core/Hotel');
						/** @var \Core\Model\MeetingModel $meeting_model */
						$meeting_model = D('Core/Meeting');
						C('TOKEN_ON', false);
						$data             = I('post.', 0, '');
						$data['mid']      = I('get.mid', 0, 'int');
						$data['creatime'] = time();    //创建时间
						$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$hotel_result     = $room_model->createRecord($data);    //创建酒店
						$meeting_result   = $meeting_model->findMeeting(1, ['id' => I('get.mid', 0, 'int')]);//查出当前会议的信息
						$hid              = $meeting_result['hid'].','.$hotel_result['id'];
						$meeting_model->alterMeeting(['id' => $meeting_result['id']], ['hid' => $hid]);

						return array_merge($hotel_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建酒店的权限', '__ajax__' => false];
				break;
				case 'delete';
					/** @var \Core\Model\HotelModel $room_model */
					$room_model = D('Core/Hotel');
					C('TOKEN_ON', false);
					$hotel_result = $room_model->deleteHotel(I('post.id'));

					return array_merge($hotel_result, ['__ajax__' => false]);
				break;
				case 'alter';
					/** @var \Core\Model\HotelModel $room_model */
					$room_model = D('Core/Hotel');
					$id         = I('post.id', 0, 'int');
					$data       = I('post.', '');
					C('TOKEN_ON', false);
					$hotel_result = $room_model->alterHotel(['id' => $id], $data);

					return array_merge($hotel_result, ['__ajax__' => false]);
				break;
				case 'get_hotel':
					$id = I('post.id', 0, 'int');
					/** @var \Core\Model\HotelModel $hotel_model */
					$hotel_model     = D('Core/Hotel');
					$hotel_result    = $hotel_model->findHotel(1, ['status' => 1, 'id' => $id]);
					$data['brief']   = $hotel_result['brief'];
					$data['comment'] = $hotel_result['comment'];

					return array_merge($data, ['__ajax__' => true]);
				break;
				case 'cancel_choose':
					$id = I('post.id', 0, 'int');
					C('TOKEN_ON', false);
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model  = D('Core/Meeting');
					$meeting_result = $meeting_model->findMeeting(1, ['id' => I('get.mid', 0, 'int')]);
					$hid            = explode(',', $meeting_result['hid']);
					$hotel_id       = [];
					foreach($hid as $k => $v){
						if($id == $v){
							continue;
						}
						else{
							$hotel_id[] = $v;
						}
					}
					$hotel_id          = implode(',', $hotel_id);
					$meeting_alter_hid = $meeting_model->alterMeeting(['id' => $meeting_result['id']], ['hid' => $hotel_id]);

					return array_merge($meeting_alter_hid, ['__ajax__' => true]);
				break;
				case 'choose':
					$id = I('post.id', 0, 'int');
					C('TOKEN_ON', false);
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model     = D('Core/Meeting');
					$meeting_result    = $meeting_model->findMeeting(1, ['id' => I('get.mid', 0, 'int')]);
					$hotel_id          = $id.','.$meeting_result['hid'];
					$hotel_id          = trim($hotel_id, ',');
					$meeting_alter_hid = $meeting_model->alterMeeting(['id' => $meeting_result['id']], ['hid' => $hotel_id]);

					return array_merge($meeting_alter_hid, ['__ajax__' => true]);
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
					$meeting_model   = D('Core/Meeting');
					$meeting         = $meeting_model->findMeeting(1, [
						'status' => 'not deleted',
						'id'     => I('get.mid', 0, 'int')
					]);
					$meeting_hid_arr = explode(',', $meeting['hid']);
					$hotel_id_index  = [];
					foreach($data as $key => $val){
						if(in_array($val['id'], $meeting_hid_arr)) $data[$key]['is_select'] = 1;
						else $data[$key]['is_select'] = 0;
						$hotel_id_index["index$val[id]"] = $key;
						$data[$key]['meeting_name']      = '';
						$data[$key]['meeting_status']    = [];
					}
					$last_index = null;
					foreach($meeting_hid_arr as $key2 => $val2){
						$index = $hotel_id_index["index$val2"];
						if(is_numeric($index)){
							$last_index = $index;
							$data[$index]['meeting_name'] .= "$meeting[name], ";
							$data[$index]['meeting_status'][] = $meeting['status'];
						}
					}
					if(!($last_index === null)) $data[$last_index]['meeting_name'] = trim($data[$last_index]['meeting_name'], ', ');
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