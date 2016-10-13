<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 20:50
	 */
	namespace Manager\Logic;

	class MeetingLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function getSelectListForRole(){
			/** @var \Manager\Model\MeetingModel $model */
			$model  = D('Meeting');
			$result = $model->getMeetingForSelect();
			array_unshift($result, ['value' => 0, 'html' => '(系统全局)']);

			return $result;
		}

		public function handlerRequest($type){
			switch($type){
				case 'delete':
					/** @var \Core\Model\MeetingModel $model */
					$model  = D('Core/Meeting');
					$result = $model->deleteMeeting([I('post.id', 0, 'int')]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'message':
					/** @var \Core\Model\AssignMessageModel $message */
					$message = D('Core/Assign_Message');
					$data = I('post.mid');
					$result_message = $message->findRecore(2,['mid'=>I('post.mid',0,'int')]);
					if(!$result_message){


						$data = I('post.');
						$data['message_id'] = I('post.sign_mes','');
						$data['mid'] = I('post.mid');
						$data['creatime']   = time();    //创建时间
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$data['type'] = 1;
						$data['status'] = 1;
						C('TOKEN_ON', false);
						$sign_mes = $message->createAssignMessage($data);
						$data['message_id'] = I('post.anti_sign','');
						$data['type'] = 2;
						$anti_sign = $message->createAssignMessage($data);
						$data['message_id'] = I('post.receivables_mes','');
						$data['type'] = 3;
						$receivables_mes = $message->createAssignMessage($data);
					}else{
						print_r(I('post.'));exit;
						C('TOKEN_ON', false);
						$sign_mes = $message->createAssignMessage($data);
					}


					return $receivables_mes;
				break;
				case 'get_message_temp':
					/** @var \Core\Model\AssignMessageModel $message_model */
					$message_model  = D('Core/AssignMessage');
					$id = I('post.id',0,'int');
					$result = $message_model->findRecord(2, ['mid'=>$id]);
					return array_merge($result, ['__ajax__'=>true]);
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function create($data){
			/** @var \Core\Model\MeetingModel $model */
			$model            = D('Core/Meeting');
			$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			$data['creatime'] = time();
			if(I('post.city')) $data['place'] = I('post.province', '')."-".I('post.city', '')."-".I('post.area', '')."-".I('post.address_detail', '');
			else $data['place'] = I('post.province', '')."-".I('post.area', '')."-".I('post.address.detail', '');

			return $model->createMeeting($data);
		}

		public function alter($id, $data, $info){
			/** @var \Core\Model\MeetingModel $model */
			$model = D('Core/Meeting');
			if(I('post.area')) $data['place'] = I('post.province')."-".I('post.city')."-".I('post.area')."-".I('post.address_detail');
			elseif(I('post.city')) $data['place'] = I('post.province')."-".I('post.city')."-".I('post.address_detail');
			else $data['place'] = $info['place'];

			return $model->alterMeeting([$id], $data); //传值到model里面操作
		}

		public function setExtendColumnForManage($list){
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$new_list       = [];
			$i              = 0;
			foreach($list as $key => $val){
				if(in_array($val['id'], $this->meetingViewList)){
					$tmp                           = $employee_model->findEmployee(1, ['id' => $val['director_id']]);
					$new_list[]                    = $val;
					$new_list[$i]['director_name'] = $tmp['name'];
					$i++;
				}
				else continue;
			}

			return $new_list;
		}
	}