<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 20:50
	 */
	namespace Manager\Logic;

	use Core\Logic\UploadLogic;

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

		public function handlerRequest($type, $ext = []){
			switch($type){
				case 'delete':
					if($this->permissionList['MEETING.DELETE']){
						/** @var \Core\Model\MeetingModel $model */
						$model  = D('Core/Meeting');
						$result = $model->deleteMeeting([I('post.id', 0, 'int')]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有删除会议的权限', '__ajax__' => false];
				break;
				case 'message':
					/** @var \Core\Model\AssignMessageModel $assign_message_model */
					$assign_message_model = D('Core/Assign_Message');
					$data                 = I('post.mid');
					$result_message       = $assign_message_model->findRecord(2, ['mid' => I('post.id', 0, 'int')]);
					if(!$result_message){
						$data['message_id'] = I('post.sign_mes', '');
						$data['mid']        = I('post.id');
						$data['creatime']   = time();    //创建时间
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$data['type']       = 1;
						$data['status']     = 1;
						C('TOKEN_ON', false);
						$sign_mes           = $assign_message_model->createRecord($data);
						$data['message_id'] = I('post.anti_sign', '');
						$data['type']       = 2;
						$anti_sign          = $assign_message_model->createRecord($data);
						$data['message_id'] = I('post.receivables_mes', '');
						$data['type']       = 3;
						$receivables_mes    = $assign_message_model->createRecord($data);
					}
					else{
						C('TOKEN_ON', false);
						$sign_record        = $assign_message_model->findRecord(1, [
							'type' => 1,
							'mid'  => I('post.id', 0, 'int')
						]);
						$anti_sign_record   = $assign_message_model->findRecord(1, [
							'type' => 2,
							'mid'  => I('post.id', 0, 'int')
						]);
						$receivables_record = $assign_message_model->findRecord(1, [
							'type' => 3,
							'mid'  => I('post.id', 0, 'int')
						]);
						$sign_result        = $assign_message_model->alterRecord([$sign_record['id']], ['message_id' => I('post.sign_mes', 0, 'int')]);
						$anti_sign_result   = $assign_message_model->alterRecord([$anti_sign_record['id']], ['message_id' => I('post.anti_sign_mes', 0, 'int')]);
						$receivables_result = $assign_message_model->alterRecord([$receivables_record['id']], ['message_id' => I('post.receivables_mes', 0, 'int')]);
						$count              = 0;
						if($sign_result['status']) $count++;
						if($anti_sign_result['status']) $count++;
						if($receivables_result['status']) $count++;
						if($count == 3) return ['status' => true, 'message' => '修改成功', '__ajax__' => false];
						elseif($count == 0) return ['status' => false, 'message' => '修改失败', '__ajax__' => false];
						else return ['status' => true, 'message' => '部分修改', '__ajax__' => false];
					}

					return $receivables_mes;
				break;
				case 'get_message_temp':
					/** @var \Core\Model\AssignMessageModel $message_model */
					$message_model = D('Core/AssignMessage');
					$id            = I('post.id', 0, 'int');
					$result        = $message_model->findRecord(2, ['mid' => $id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'create':
					if($this->permissionList['MEETING.CREATE']){
						/** @var \Core\Model\MeetingModel $model */
						$model            = D('Core/Meeting');
						$data             = I('post.');
						$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$data['creatime'] = time();
						$data['brief']    = $_POST['brief'];
						if(I('post.city')) $data['place'] = I('post.province', '')."-".I('post.city', '')."-".I('post.area', '')."-".I('post.address_detail', '');
						else $data['place'] = I('post.province', '')."-".I('post.area', '')."-".I('post.address.detail', '');
						$result = $model->createMeeting($data);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建会议的权限', '__ajax__' => false];
				break;
				case 'upload_image':
					$getSavePath          = function ($data){
						return UPLOAD_PATH.$data['data']['logo_upload']['savepath'].$data['data']['logo_upload']['savename'];
					};
					$getResult            = function ($data){
						return $data['data']['logo_upload'];
					};
					$core_upload_logic    = new UploadLogic();
					$manager_upload_logic = new \Manager\Logic\UploadLogic();
					$result1              = $core_upload_logic->upload($_FILES, '/Image/');
					if(!$result1['status']) return array_merge($result1, ['__ajax__' => true]);
					$result2 = $manager_upload_logic->create($getSavePath($result1), $getResult($result1));

					return array_merge($result2, ['__ajax__' => true, 'imageUrl' => trim($getSavePath($result1), '.')]);
				break;
				case 'alter':
					if($this->permissionList['MEETING.ALTER']){
						/** @var \Core\Model\MeetingModel $model */
						$model         = D('Core/Meeting');
						$id            = I('get.id', 0, 'int');
						$data          = I('post.');
						$data['brief'] = $_POST['brief'];
						if(I('post.area')) $data['place'] = I('post.province')."-".I('post.city')."-".I('post.area')."-".I('post.address_detail');
						elseif(I('post.city')) $data['place'] = I('post.province')."-".I('post.city')."-".I('post.address_detail');
						else $data['place'] = $ext['info']['place'];
						$result = $model->alterMeeting([$id], $data);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有修改会议的权限', '__ajax__' => false];
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
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