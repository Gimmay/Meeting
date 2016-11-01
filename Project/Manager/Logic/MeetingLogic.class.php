<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 20:50
	 */
	namespace Manager\Logic;

	use Core\Logic\PermissionLogic;
	use Core\Logic\QRCodeLogic;
	use Core\Logic\UploadLogic;
	use Quasar\StringPlus;

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
					$count                = 0;
					$result_message       = $assign_message_model->findRecord(2, ['mid' => I('post.id', 0, 'int')]);
					if(I('post.sign_mes')){
						$data['message_id'] = I('post.sign_mes', '');
						$data['mid']        = I('post.id');
						$data['creatime']   = time();    //创建时间
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$data['type']       = 1;
						$data['status']     = 1;
						C('TOKEN_ON', false);
						/** @var \Core\Model\AssignMessageModel $message_model */
						$message_model  = D('Core/Assign_Message');
						$message_result = $message_model->findRecord(1, ['mid' => I('post.id'), 'type' => 1]);
						if($message_result){
							$sign_result = $assign_message_model->alterRecord([$message_result['id']], ['message_id' => I('post.sign_mes', 0, 'int')]);
						}
						else{
							$sign_result = $assign_message_model->createRecord($data);
						}
						if($sign_result['status']){
							$count++;
						}
					}
					if(I('post.anti_sign_mes')){
						$data['message_id'] = I('post.anti_sign_mes', '');
						$data['mid']        = I('post.id');
						$data['creatime']   = time();    //创建时间
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$data['type']       = 2;
						$data['status']     = 1;
						C('TOKEN_ON', false);
						/** @var \Core\Model\AssignMessageModel $message_model */
						$message_model  = D('Core/Assign_Message');
						$message_result = $message_model->findRecord(1, ['mid' => I('post.id'), 'type' => 2]);
						if($message_result){
							$anti_sign_result = $assign_message_model->alterRecord([$message_result['id']], [
								'message_id' => I('post.anti_sign_mes', 0, 'int'),
								'type'       => 2
							]);
						}
						else{
							$anti_sign_result = $assign_message_model->createRecord($data);
						}
						if($anti_sign_result['status']){
							$count++;
						}
					}
					if(I('post.receivables_mes')){
						$data['message_id'] = I('post.receivables_mes', '');
						$data['mid']        = I('post.id');
						$data['creatime']   = time();    //创建时间
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$data['type']       = 3;
						$data['status']     = 1;
						C('TOKEN_ON', false);
						/** @var \Core\Model\AssignMessageModel $message_model */
						$message_model  = D('Core/Assign_Message');
						$message_result = $message_model->findRecord(1, ['mid' => I('post.id'), 'type' => 3]);
						if($message_result){
							$receivables_result = $assign_message_model->alterRecord([$message_result['id']], [
								'message_id' => I('post.receivables_mes', 0, 'int'),
								'type'       => 3
							]);
						}
						else{
							$receivables_result = $assign_message_model->createRecord($data);
						}
						if($receivables_result['status']){
							$count++;
						}
					}
					if($count>0){
						return ['status' => true, 'message' => '保存成功', '__ajax__' => false];
					}
					else{
						return ['status' => false, 'message' => '保存失败', '__ajax__' => false];
					}
					//					elseif(I('post.sign_mes')){
					//						$data['message_id'] = I('post.sign_mes', '');
					//						$data['mid']        = I('post.id');
					//						$data['creatime']   = time();    //创建时间
					//						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					//						$data['type']       = 1;
					//						$data['status']     = 1;
					//						C('TOKEN_ON', false);
					//						$data['message_id'] = I('post.anti_sign', '');
					//						$data['type']       = 2;
					//						$anti_sign          = $assign_message_model->createRecord($data);
					//					}
					//					elseif(I('post.receivables_mes')){
					//						$data['message_id'] = I('post.sign_mes', '');
					//						$data['mid']        = I('post.id');
					//						$data['creatime']   = time();    //创建时间
					//						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					//						$data['type']       = 1;
					//						$data['status']     = 1;
					//						C('TOKEN_ON', false);
					//						$data['message_id'] = I('post.receivables_mes', '');
					//						$data['type']       = 3;
					//						$receivables_mes    = $assign_message_model->createRecord($data);
					//					}
					//					else{
					//						C('TOKEN_ON', false);
					//						$sign_record        = $assign_message_model->findRecord(1, [
					//							'type' => 1,
					//							'mid'  => I('post.id', 0, 'int')
					//						]);
					//						$anti_sign_record   = $assign_message_model->findRecord(1, [
					//							'type' => 2,
					//							'mid'  => I('post.id', 0, 'int')
					//						]);
					//						$receivables_record = $assign_message_model->findRecord(1, [
					//							'type' => 3,
					//							'mid'  => I('post.id', 0, 'int')
					//						]);
					//						$sign_result        = $assign_message_model->alterRecord([$sign_record['id']], ['message_id' => I('post.sign_mes', 0, 'int')]);
					//						$anti_sign_result   = $assign_message_model->alterRecord([$anti_sign_record['id']], ['message_id' => I('post.anti_sign_mes', 0, 'int')]);
					//						$receivables_result = $assign_message_model->alterRecord([$receivables_record['id']], ['message_id' => I('post.receivables_mes', 0, 'int')]);
					//						$count              = 0;
					//						if($sign_result['status']) $count++;
					//						if($anti_sign_result['status']) $count++;
					//						if($receivables_result['status']) $count++;
					//						if($count == 3) return ['status' => true, 'message' => '修改成功', '__ajax__' => false];
					//						elseif($count == 0) return ['status' => false, 'message' => '修改失败', '__ajax__' => false];
					//						else return ['status' => true, 'message' => '部分修改', '__ajax__' => false];
					//					}
				break;
				case 'get_message_temp':
					/** @var \Core\Model\AssignMessageModel $message_model */
					$message_model = D('Core/AssignMessage');
					$id            = I('post.id', 0, 'int');
					$result        = $message_model->findRecord(2, ['mid' => $id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_message':
					$id = I('post.id', '');
					/** @var \Core\Model\MessageModel $message_model */
					$message_model         = D('Core/Message');
					$assign_message_result = $message_model->findMessage(1, ['id' => $id]);

					return ['data' => $assign_message_result, '__ajax__' => true];
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
						if($result['status']){
							$meeting_id = $result['id'];
							// 创建二维码
							$qrcode_obj = new QRCodeLogic();
							/** @var \Core\Model\MeetingModel $model */
							$model       = D('Core/Meeting');
							$url         = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]".U('/Mobile/Client/myMeeting', ['mid'=>$meeting_id, 'sign'=>1]);
							$qrcode_file = $qrcode_obj->make($url);
							$remote_url  = '/'.trim($qrcode_file, './');
							$record      = $model->findMeeting(1, ['id' => $meeting_id]);
							C('TOKEN_ON', false);
							$model->alterMeeting([$record['id']], [
								'qrcode' => $remote_url
							]);
							// 创建该员工对会议的可见记录
							/** @var \Core\Model\MeetingManagerModel $meeting_manager_model */
							$meeting_manager_model = D('Core/MeetingManager');
							C('TOKEN_ON', false);
							$meeting_manager_model->createRecord([
								'eid'     => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
								'mid'     => $meeting_id,
								'creator' => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')
							]);
						}

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建会议的权限', '__ajax__' => false];
				break;
				case 'upload_image':
					$core_upload_logic = new UploadLogic();
					$result            = $core_upload_logic->upload($_FILES, '/Image/');

					return array_merge($result, ['__ajax__' => true, 'imageUrl' => $result['data']['filePath']]);
				break;
				case 'alter':
					if($this->permissionList['MEETING.ALTER']){
						/** @var \Core\Model\MeetingModel $model */
						$model          = D('Core/Meeting');
						$qrcode_obj     = new QRCodeLogic();
						$id             = I('get.id', 0, 'int');
						$url            = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]".U('Mobile/Client/myMeeting', ['mid'=>$id, 'sign'=>1]);
						$qrcode_file    = $qrcode_obj->make($url);
						$remote_url     = '/'.trim($qrcode_file, './');
						$data           = I('post.');
						$data['brief']  = $_POST['brief'];
						$data['qrcode'] = $remote_url;
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
			$employee_model   = D('Core/Employee');
			$permission_logic = new PermissionLogic();
			$new_list         = [];
			$i                = 0;
			foreach($list as $key => $val){
				$condition_1 = in_array($val['id'], $this->meetingViewList);
				$condition_2 = $permission_logic->hasPermission('MEETING.VIEW-ALL-MEETING', I('session.MANAGER_EMPLOYEE_ID', 0, 'int'), 1);
				if($condition_1 || $condition_2){
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