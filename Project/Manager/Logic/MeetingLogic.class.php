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
			if(I('post.area')){
				$data['place'] = I('post.province')."-".I('post.city')."-".I('post.area')."-".I('post.address_detail');
			}
			elseif(I('post.city')){
				$data['place'] = I('post.province')."-".I('post.city')."-".I('post.address_detail');
			}
			else{
				$data['place'] = $info['place'];
			}

			return $model->alterMeeting([$id], $data); //传值到model里面操作
		}

		public function setExtendColumnForManage($list){
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$new_list       = [];
			$i = 0;
			foreach($list as $key => $val){
				if(in_array($val['id'], $this->meetingViewList)){
					$tmp                             = $employee_model->findEmployee(1, ['id' => $val['director_id']]);
					$new_list[]                      = $val;
					$new_list[$i]['director_name'] = $tmp['name'];
					$i++;
				}
				else continue;
			}

			return $new_list;
		}
	}