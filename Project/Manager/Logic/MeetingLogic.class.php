<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-29
	 * Time: 20:50
	 */
	namespace Manager\Logic;

	use Core\Logic\ColumnControlLogic;
	use Core\Logic\LogLogic;
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
			array_unshift($result, [
				'value' => 0,
				'html'  => '(系统全局)'
			]);

			return $result;
		}

		public function handlerRequest($type, $ext = []){
			switch($type){
				case 'delete':
					if($this->permissionList['MEETING.DELETE']){
						/** @var \Core\Model\MeetingModel $model */
						$model = D('Core/Meeting');
						/** @var \Core\Model\JoinModel $join_model */
						$join_model  = D('Core/Join');
						$join_result = $join_model->findRecord(2, [
							'mid'    => I('post.id', 0, 'int'),
							'status' => 'not deleted'
						]);
						if($join_result) return array_merge(['message' => '当前会议存在参会人员<br>不允许删除'], ['__ajax__' => false]);
						else $result = $model->deleteMeeting([I('post.id', 0, 'int')]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有删除会议的权限',
						'__ajax__' => false
					];
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
						$message_result = $message_model->findRecord(1, [
							'mid'  => I('post.id'),
							'type' => 1
						]);
						if($message_result){
							$sign_result = $assign_message_model->alterRecord(['id' => $message_result['id']], ['message_id' => I('post.sign_mes', 0, 'int')]);
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
						$message_result = $message_model->findRecord(1, [
							'mid'  => I('post.id'),
							'type' => 2
						]);
						if($message_result){
							$anti_sign_result = $assign_message_model->alterRecord(['id' => $message_result['id']], [
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
						$message_result = $message_model->findRecord(1, [
							'mid'  => I('post.id'),
							'type' => 3
						]);
						if($message_result){
							$receivables_result = $assign_message_model->alterRecord(['id' => $message_result['id']], [
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
						return [
							'status'   => true,
							'message'  => '保存成功',
							'__ajax__' => false
						];
					}
					else{
						return [
							'status'   => false,
							'message'  => '保存失败',
							'__ajax__' => false
						];
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

					return [
						'data'     => $assign_message_result,
						'__ajax__' => true
					];
				break;
				case 'create':
					if($this->permissionList['MEETING.CREATE']){
						/** @var \Core\Model\MeetingModel $model */
						$model                       = D('Core/Meeting');
						$data                        = I('post.');
						$data['creator']             = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$data['creatime']            = time();
						$data['brief']               = $_POST['brief'];
						$data['kpi']                 = $data['kpi'] ? $data['kpi'] : null;
						$data['config_message_type'] = 0; // 默认消息发送不发送
						$result                      = $model->createMeeting($data);
						if($result['status']){
							$meeting_id = $result['id'];
							// 创建二维码
							$qrcode_obj = new QRCodeLogic();
							/** @var \Core\Model\MeetingModel $model */
							$model       = D('Core/Meeting');
							$url         = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]".U('/Mobile/Client/myMeeting', [
									'mid'  => $meeting_id,
									'sign' => 1
								]);
							$qrcode_file = $qrcode_obj->make($url);
							$remote_url  = '/'.trim($qrcode_file, './');
							$record      = $model->findMeeting(1, ['id' => $meeting_id]);
							C('TOKEN_ON', false);
							$model->alterMeeting(['id' => $record['id']], [
								'qrcode' => $remote_url
							]);
							// 创建该员工对会议的可见记录
							/** @var \Core\Model\MeetingManagerModel $meeting_manager_model */
							$meeting_manager_model = D('Core/MeetingManager');
							C('TOKEN_ON', false);
							$meeting_manager_model->createRecord([
								'eid'      => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
								'mid'      => $meeting_id,
								'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
								'creatime' => time()
							]);
							// 创建字段控制
							$column_control_logic = new ColumnControlLogic();
							$column_control_logic->initClientColumn($result['id']);
						}

						return array_merge($result, ['__ajax__' => false]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有创建会议的权限',
						'__ajax__' => false
					];
				break;
				case 'upload_image':
					$core_upload_logic = new UploadLogic();
					$result            = $core_upload_logic->upload($_FILES, '/Image/');

					return array_merge($result, [
						'__ajax__' => true,
						'imageUrl' => $result['data']['filePath']
					]);
				break;
				case 'alter':
					if($this->permissionList['MEETING.ALTER']){
						/** @var \Core\Model\MeetingModel $model */
						$model          = D('Core/Meeting');
						$qrcode_obj     = new QRCodeLogic();
						$id             = I('get.mid', 0, 'int');
						$url            = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]".U('Mobile/Client/myMeeting', [
								'mid'  => $id,
								'sign' => 1
							]);
						$qrcode_file    = $qrcode_obj->make($url);
						$remote_url     = '/'.trim($qrcode_file, './');
						$data           = I('post.');
						$data['brief']  = $_POST['brief'];
						$data['qrcode'] = $remote_url;
						$data['kpi']    = $data['kpi'] ? $data['kpi'] : null;
						$result         = $model->alterMeeting(['id' => $id], $data);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有修改会议的权限',
						'__ajax__' => false
					];
				break;
				case 'get_detail':
					/** @var \Core\Model\MeetingModel $model */
					$model = D('Core/Meeting');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model             = D('Core/Employee');
					$id                         = I('post.id', 0, 'int');
					$meeting_record             = $model->findMeeting(1, ['id' => $id]);
					$meeting_record['creatime'] = date('Y-m-d H:i:s', $meeting_record['creatime']);
					$creator                    = $employee_model->findEmployee(1, ['id' => $meeting_record['creator']]);
					$meeting_record['creator']  = $creator['name'];
					if($meeting_record['contacts_1_id']){
						$contacts_1                   = $employee_model->findEmployee(1, ['id' => $meeting_record['contacts_1_id']]);
						$meeting_record['contacts_1'] = $contacts_1['name'];
					}
					if($meeting_record['contacts_2_id']){
						$contacts_2                   = $employee_model->findEmployee(1, ['id' => $meeting_record['contacts_2_id']]);
						$meeting_record['contacts_2'] = $contacts_2['name'];
					}
					if($meeting_record['director_id']){
						$director                   = $employee_model->findEmployee(1, ['id' => $meeting_record['director_id']]);
						$meeting_record['director'] = $director['name'];
					}
					if($meeting_record['hid']){
						/** @var \Core\Model\HotelModel $hotel_model */
						$hotel_model             = D('Core/Hotel');
						$hotel                   = $hotel_model->findHotel(1, ['id' => $meeting_record['hid']]);
						$meeting_record['hotel'] = $hotel['name'];
					}
					$config_message_type                   = decbin($meeting_record['config_message_type']);
					$config_message_type                   = sprintf('%02d', $config_message_type);
					$meeting_record['config_message_type'] = $config_message_type[0] ? '微信, ' : '';
					$meeting_record['config_message_type'] .= $config_message_type[1] ? '短信, ' : '';
					$meeting_record['config_message_type'] = trim($meeting_record['config_message_type'], ', ');

					return array_merge($meeting_record, ['__ajax__' => true]);
				break;
				case 'get_role':
					/** @var \Core\Model\RoleModel $role_model */
					$role_model = D('Core/Role');
					/* 获取当前员工角色的最大等级 */
					$max_role_level = $role_model->getMaxRoleLevel(I('session.MANAGER_EMPLOYEE_ID', 0, 'int'));
					$role_result    = $role_model->findRole(2, [
						'level'  => [
							'value'    => $max_role_level,
							'operator' => 'egt'
						],
						'status' => 1
					]);

					return array_merge($role_result, ['__ajax__' => true]);
				break;
				case 'release':
					if($this->permissionList['MEETING.RELEASE']){
						$id = I('post.id', '');
						/** @var \Core\Model\MeetingModel $meeting_model */
						$meeting_model  = D('Core/Meeting');
						$meeting_status = $meeting_model->findMeeting(1, ['id' => $id]);
						if($meeting_status['status'] == 2){
							$meeting_result = $meeting_model->alterMeeting(['id' => $id], ['status' => 1]);
						}
						else{
							$meeting_result = $meeting_model->alterMeeting(['id' => $id], ['status' => 2]);
						}

						return array_merge($meeting_result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有发布会议的权限', '__ajax__' => true];
				break;
				case'get_employee':
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					/** @var \Core\Model\DepartmentModel $department_model */
					$department_model = D('Core/Department');
					$employee_result  = $employee_model->findEmployee(2, ['status' => 'not deleted']);
					foreach($employee_result as $k => $v){
						$department_result             = $department_model->findDepartment(1, ['id' => $v['did']]);
						$employee_result[$k]['d_name'] = $department_result['name'];
					}
					//					$employee_id           = [];
					//					foreach($employee_result as $k1 => $v1){
					//						$employee_id[] = $v1['id'];
					//					}
					//					$meeting_manager_result = $meeting_manager_model->findRecord(2, [
					//						'mid'    => I('post.mid', ''),
					//						'status' => 'not deleted'
					//					]);
					//					foreach($meeting_manager_result as $key => $val){
					//						$eid[] = $val['eid'];
					//					}
					//					$id = [];
					//					foreach($employee_id as $kk => $vv){
					//						if(in_array($vv, $eid)){
					//							continue;
					//						}
					//						else{
					//							$id[] = $vv;
					//						}
					//					}
					//					$result = [];
					//					foreach($id as $k2 => $v3){
					//						$result[]              = $employee_model->findEmployee(1, ['id' => $v3]);
					//						$department_result     = $department_model->findDepartment(1, ['id' => $result[$k2]['did']]);
					//						$result[$k2]['d_name'] = $department_result['name'];
					//					}
					return array_merge($employee_result, ['__ajax__' => true]);
				break;
				case 'get_employee2':
					//					$keyword = I('post.keyword', '');
					//					/** @var \Core\Model\EmployeeModel $employee_model */
					//					$employee_model = D('Core/Employee');
					//					/** @var \Core\Model\DepartmentModel $department_model */
					//					$department_model = D('Core/Department');
					//					/** @var \Core\Model\MeetingManagerModel $meeting_manager_model */
					//					$meeting_manager_model = D('Core/MeetingManager');
					//					$employee_result       = $employee_model->findEmployee(2, [
					//						'status'  => 'not deleted',
					//						'keyword' => $keyword
					//					]);
					//
					//					$employee_id           = [];
					//					foreach($employee_result as $k1 => $v1){
					//						$employee_id[] = $v1['id'];
					//					}
					//					$meeting_manager_result = $meeting_manager_model->findRecord(2, [
					//						'mid'    => I('post.mid', 0, 'int'),
					//						'status' => 'not deleted'
					//					]);
					//					$eid                    = [];
					//					foreach($meeting_manager_result as $key => $val){
					//						$eid[] = $val['eid'];
					//					}
					//					$id = [];
					//					foreach($employee_id as $kk => $vv){
					//						if(in_array($vv, $eid)){
					//							continue;
					//						}
					//						else{
					//							$id[] = $vv;
					//						}
					//					}
					//					$result = [];
					//					foreach($id as $k2 => $v3){
					//						$result[]              = $employee_model->findEmployee(1, ['id' => $v3]);
					//						$department_result     = $department_model->findDepartment(1, ['id' => $result[$k2]['did']]);
					//						$result[$k2]['d_name'] = $department_result['name'];
					//					}
					//
					//					return array_merge($employee_result, ['__ajax__' => true]);
					$keyword = I('post.keyword', '');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					/** @var \Core\Model\DepartmentModel $department_model */
					$department_model = D('Core/Department');
					$employee_result  = $employee_model->findEmployee(2, [
						'status'  => 'not deleted',
						'keyword' => $keyword
					]);
					foreach($employee_result as $k => $v){
						$department_result             = $department_model->findDepartment(1, ['id' => $v['did']]);
						$employee_result[$k]['d_name'] = $department_result['name'];
					}
					//					$employee_id           = [];
					//					foreach($employee_result as $k1 => $v1){
					//						$employee_id[] = $v1['id'];
					//					}
					//					$meeting_manager_result = $meeting_manager_model->findRecord(2, [
					//						'mid'    => I('post.mid', ''),
					//						'status' => 'not deleted'
					//					]);
					//					foreach($meeting_manager_result as $key => $val){
					//						$eid[] = $val['eid'];
					//					}
					//					$id = [];
					//					foreach($employee_id as $kk => $vv){
					//						if(in_array($vv, $eid)){
					//							continue;
					//						}
					//						else{
					//							$id[] = $vv;
					//						}
					//					}
					//					$result = [];
					//					foreach($id as $k2 => $v3){
					//						$result[]              = $employee_model->findEmployee(1, ['id' => $v3]);
					//						$department_result     = $department_model->findDepartment(1, ['id' => $result[$k2]['did']]);
					//						$result[$k2]['d_name'] = $department_result['name'];
					//					}
					return array_merge($employee_result, ['__ajax__' => true]);
				break;
				case 'save_employee':
					if($this->permissionList['MEETING.ASSIGN-MANAGER']){
						/** @var \Core\Model\MeetingManagerModel $meeting_manager_model */
						$meeting_manager_model = D('Core/MeetingManager');
						/** @var \Core\Model\AssignRoleModel $assign_role_model */
						$assign_role_model    = D('Core/AssignRole');
						$id                   = I('post.id', '');
						$mid                  = I('post.mid', '');
						$rid                  = I('post.rid', '');
						$meeting_manager_list = $meeting_manager_model->findRecord(2, ['mid' => $mid]); // 查出所有当前会议的数据
						$m_mid                = [];
						foreach($meeting_manager_list as $kk => $vv){
							$m_mid[] = $vv['eid'];  //查出当前所有会议的所有eid
						}
						foreach($id as $k1 => $v1){
							if(in_array($v1, $m_mid)){
								$meeting_manager_info  = $meeting_manager_model->findRecord(1, [
									'mid' => $mid,
									'eid' => $v1
								]);//查出当前会议.并且状态为已删除的数据.
								$meeitng_manager_alter = $meeting_manager_model->alterRecord(['id' => $meeting_manager_info['id']], ['status' => 1]);
								$assign_role_result    = $assign_role_model->createRecord([
									'rid'      => $rid,
									'oid'      => $v1,
									'type'     => 0,
									'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
									'creatime' => time()
								]);
							}
							else{
								foreach($id as $k => $v){
									$meeting_manager_result = $meeting_manager_model->createRecord([
										'mid'      => $mid,
										'eid'      => $v,
										'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
										'creatime' => time()
									]);
									$assign_role_result     = $assign_role_model->createRecord([
										'rid'      => $rid,
										'oid'      => $v,
										'type'     => 0,
										'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
										'creatime' => time()
									]);
								}
							}
						}

						return array_merge($meeting_manager_result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有分配会务人员的权限', '__ajax__' => false];
				break;
				case 'see_employee':
					$rid = I('post.rid', '');
					$mid = I('post.mid', '');
					/** @var \Core\Model\MeetingManagerModel $meeting_manager_model */
					$meeting_manager_model = D('Core/MeetingManager');
					/** @var \Core\Model\AssignRoleModel $assign_role_model */
					$assign_role_model = D('Core/AssignRole');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					/** @var \Core\Model\DepartmentModel $department_model */
					$department_model   = D('Core/Department');
					$assign_role_result = $assign_role_model->findRecord(2, [
						'rid'  => $rid,
						'type' => 0
					]);
					$oid                = [];
					foreach($assign_role_result as $k => $v){
						$oid[] = $v['oid'];
					}
					$meeting_manager_result = $meeting_manager_model->findRecord(2, [
						'mid'    => $mid,
						'status' => 'not deleted'
					]);
					$manager_id             = $eid = [];
					foreach($meeting_manager_result as $k1 => $v1){
						if(in_array($v1['eid'], $oid)) $manager_id[] = $v1['eid'];
						else continue;
					}
					$result = [];
					foreach($manager_id as $k1 => $v1) $result[] = $employee_model->findEmployee(1, ['id' => $v1]);
					foreach($result as $kk => $vv){
						$department_result     = $department_model->findDepartment(1, ['id' => $vv['did']]);
						$result[$kk]['d_name'] = $department_result['name'];
					}

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete_employee':
					if($this->permissionList['MEETING.ASSIGN-MANAGER']){
						$mid = I('post.mid', '');
						$eid = I('post.id', '');
						$rid = I('post.rid', '');
						/** @var \Core\Model\MeetingManagerModel $meeting_manager_model */
						$meeting_manager_model  = D('Core/MeetingManager');
						$meeting_manager_result = $meeting_manager_model->deleteRecord([
							'mid' => $mid,
							'eid' => $eid
						]);
						$assign_role_logic      = new AssignRoleLogic();
						$result                 = $assign_role_logic->antiAssignRole($rid, $eid, 0);

						return array_merge($meeting_manager_result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有分配会务人员的权限', '__ajax__' => true];
				break;
				case 'choose_hotel':
					$mid = I('get.mid', '');
					$id  = I('post.id', '');
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					$hid           = '';
					foreach($id as $k => $v){
						$hid .= $v.',';
					}
					$meeting_result = $meeting_model->alterMeeting(['id' => $mid], ['hid' => $hid]);

					return array_merge($meeting_result, ['__ajax__' => true]);
				break;
				case 'set_config':
					if($this->permissionList['MEETING.CONFIGURE']){
						/** @var \Core\Model\MeetingModel $model */
						$model = D('Core/Meeting');
						$data  = I('post.');
						// Warning：如果更改了消息类型数量 这里去下面位置做更改
						// B477A789FC61E5FC5221C889708449B460B207C5
						$message_wechat              = $data['message_wechat'] ? 1 : 0;
						$message_sms                 = $data['message_sms'] ? 1 : 0;
						$data['config_message_type'] = bindec("$message_wechat$message_sms");
						// Warning：如果更改了参会人员的创建检测字段 这里去下面位置做更改
						// 32B183DB71AE312536C905678B9F33FADFE63BD9
						$create_client_name           = $data['create_client_name'] ? 1 : 0;
						$create_client_mobile         = $data['create_client_mobile'] ? 1 : 0;
						$data['config_create_client'] = bindec("$create_client_name$create_client_mobile");
						$result                       = $model->alterMeeting(['id' => I('get.mid', 0, 'int')], $data);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有配置会议的权限', '__ajax__' => false];
				break;
				case 'client:set_column':
					$column_control_logic = new ColumnControlLogic();
					$post                 = I('post.');
					$mid                  = I('get.mid', 0, 'int');
					$data                 = [];
					foreach($post['code'] as $key => $val){
						$data[] = [
							'code'     => $val,
							'name'     => $post['name'][$key],
							'view'     => $post['view'][$key],
							'must'     => $post['must'][$key],
							'creatime' => time(),
							'table'    => 'user_client',
							'mid'      => $mid,
							'form'     => $post['form'][$key]
						];
					}
					$log_logic = new LogLogic();
					$log_logic->create([
						'dbTable'  => 'workflow_column_control',
						'dbColumn' => '*',
						'extend'   => 'PC',
						'action'   => '修改参会人员字段',
						'type'     => 'delete&create'
					]);
					$result = $column_control_logic->saveClientColumn($mid, $data);

					return array_merge($result, ['__ajax__' => false, '__return__' => '']);
				break;
				case 'upload_receipt_logo':
					$core_upload_logic = new UploadLogic();
					$result            = $core_upload_logic->upload($_FILES, '/Image/');
					$log_logic         = new LogLogic();
					$log_logic->create([
						'dbTable'  => 'system_upload',
						'dbColumn' => '*',
						'extend'   => 'PC',
						'action'   => '上传收据LOGO',
						'type'     => 'create'
					]);
					if($result['status']){
						$mid = I('get.mid', 0, 'int');
						/** @var \Core\Model\MeetingModel $meeting_model */
						$meeting_model = D('Core/Meeting');
						$result        = $meeting_model->alterMeeting(['id' => $mid], ['receipt_logo' => $result['data']['filePath']]);
						$log_logic     = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_meeting',
							'dbColumn' => 'receipt_logo',
							'extend'   => 'PC',
							'action'   => '修改收据LOGO',
							'type'     => 'modify'
						]);
					}

					return array_merge($result, ['__ajax__' => true]);
				break;
				default:
					return [
						'status'  => false,
						'message' => '参数错误'
					];
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
				$condition_2 = $permission_logic->hasPermission('MEETING.VIEW-ALL', I('session.MANAGER_EMPLOYEE_ID', 0, 'int'), 1);
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