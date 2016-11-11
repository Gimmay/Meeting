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

		public function handlerRequest($type){
			switch($type){
				case 'delete':
					/** @var \Core\Model\SignPlaceModel $model */
					$model  = D('Core/SignPlace');
					$data   = I('post.id');
					$result = $model->deleteRecord($data);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'create':
					/** @var \Core\Model\SignPlaceModel $model */
					$model            = D('Core/SignPlace');
					$data             = I('post.');
					$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['mid']      = I('get.mid', 0, 'int');
					$data['creatime'] = time();

					return $model->createRecord($data);
				break;
				case 'alter':
					/** @var \Core\Model\SignPlaceModel $model */
					$model = D('Core/SignPlace');
					$data  = I('post.');
					$id    = I('get.id', 0, 'int');

					return $model->alterRecord(['id' => $id], $data);
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setData($type, $data){
			switch($type){
				case 'alter:get_extend_column':
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model        = D('Core/Employee');
					$meeting               = $meeting_model->findMeeting(1, ['id' => $data['mid']]);
					$director              = $employee_model->findEmployee(1, ['id' => $data['director_id']]);
					$sign_director         = $employee_model->findEmployee(1, ['id' => $data['sign_director_id']]);
					$data['meeting']       = $meeting['name'];
					$data['director']      = $director['name'];
					$data['sign_director'] = $sign_director['name'];

					return $data;
				break;
				case 'manage:get_extend_column':
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					foreach($data as $key => $val){
						$meeting                     = $meeting_model->findMeeting(1, [
							'id'     => $val['mid'],
							'status' => 'not deleted'
						]);
						$director                    = $employee_model->findEmployee(1, [
							'id'     => $val['director_id'],
							'status' => 'not deleted'
						]);
						$sign_director               = $employee_model->findEmployee(1, [
							'id'     => $val['sign_director_id'],
							'status' => 'not deleted'
						]);
						$data[$key]['meeting']       = $meeting['name'];
						$data[$key]['director']      = $director['name'];
						$data[$key]['sign_director'] = $sign_director['name'];
					}

					return $data;
				default:
					return $data;
				break;
			}
		}
	}