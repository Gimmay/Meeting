<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-20
	 * Time: 18:02
	 */
	namespace Manager\Logic;

	class CarLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch(strtolower($type)){
				case 'create':
					/** @var \Core\Model\CarModel $model */
					$model            = D('Core/Car');
					$data             = I('post.');
					$data['status']   = 1;
					$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['creatime'] = time();
					$result           = $model->createCar($data);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'alter':
				break;
				case 'delete':
				break;
				case 'dispatch':
				break;
				case 'hitching':
				break;
				case 'get_not_ride_client':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model = D('Core/Join');
					/** @var \Core\Model\AssignCarModel $assign_car_model */
					$assign_car_model = D('Core/AssignCar');
					/** @var \Core\Model\CarModel $model */
					$model = D('Core/Car');
					//$join_model->findRecord(2, [''])
				break;
				case 'get_ride_client':
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setData($type, $data, $option = []){
			switch($type){
				case 'manage:set_and_filter':
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					$new_list      = [];
					$type          = strtolower($option['type']);
					foreach($data as $key => $val){
						$meeting_record = $meeting_model->findMeeting(1, [
							'id'     => $val['mid'],
							'status' => 'not deleted'
						]);
						if($type == 'using'){ // 1、mid字段有值 2、会议进行中
							if($meeting_record){
								$meeting_start_time = strtotime($meeting_record['start_time']);
								$meeting_end_time   = strtotime($meeting_record['end_time']);
								$now_time           = time();
								if($meeting_start_time<$now_time && $meeting_end_time>$now_time){
									$val['meeting'] = $meeting_record['name'];
									$new_list[]     = $val;
								}
							}
						}
						elseif($type == 'not_use'){ // mid没有值
							if(!$val['mid']) $new_list[] = $val;
						}
						elseif($type == 'complete'){ // 1、mid有值 2、会议已开完
							if($meeting_record){
								$meeting_end_time = strtotime($meeting_record['end_time']);
								$now_time         = time();
								if($meeting_end_time<$now_time){
									$val['meeting'] = $meeting_record['name'];
									$new_list[]     = $val;
								}
							}
						}
						else{ // 全部的
							if($meeting_record){
								$val['meeting'] = $meeting_record['name'];
								$new_list[]     = $val;
							}
							else $new_list[] = $val;
						}
					}

					return $new_list;
				break;
				default:
					return $data;
				break;
			}
		}
	}