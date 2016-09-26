<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 11:41
	 */
	namespace Manager\Logic;

	class SignPlaceLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function create($data){
			/** @var \Core\Model\SignPlaceModel $model */
			$model            = D('Core/SignPlace');
			$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			$data['mid']      = I('get.mid', 0, 'int');
			$data['place']    = I('post.address_province', '').'-'.I('post.address_city', '').'-'.I('post.address_area', '').'-'.I('post.address_detail', '');
			$data['creatime'] = time();

			return $model->createRecord($data);
		}

		public function setExtendColumn($list){
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			foreach($list as $key => $val){
				$meeting                     = $meeting_model->findMeeting(1, ['id' => $val['mid']]);
				$director                    = $employee_model->findEmployee(1, ['id' => $val['director_id']]);
				$sign_director               = $employee_model->findEmployee(1, ['id' => $val['sign_director_id']]);
				$list[$key]['meeting']       = $meeting['name'];
				$list[$key]['director']      = $director['name'];
				$list[$key]['sign_director'] = $sign_director['name'];
			}
			return $list;
		}
	}