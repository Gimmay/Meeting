<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-20
	 * Time: 16:25
	 */
	namespace Manager\Logic;

	class ReportLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function setData($type, $data, $option = []){
			switch($type){
				case 'exportExcel:joinReceivables':
					$data = array_merge([
						[
							'cid'                => '客户ID',
							'id'                 => '客户参会ID',
							'mid'                => '会议ID',
							'meeting_name'       => '会议名称',
							'meeting_time'       => '会议时间',
							'name'               => '参会人名称',
							'gender'             => '参会人性别',
							'age'                => '参会人年龄',
							'mobile'             => '参会人手机',
							'price'              => '收款总和',
							'sign_status'        => '签到状态',
							'unit'               => '参会人单位',
							'team'               => '团队',
							'service_consultant' => '服务顾问',
							'develop_consultant' => '开拓顾问',
							'type'               => '类型',
							'registration_type'  => '报名类型',
							'status'             => '状态',
							'comment'            => '备注',
						]
					], $data);
					foreach($data as $i => $record){
						foreach($record as $key => $val){
							if(isset($option['exceptColumn']) && in_array($key, $option['exceptColumn'])) unset($data[$i][$key]);
							switch($key){
								case 'sign_status':
									switch($val){
										case 0:
										case 2:
											$data[$i][$key] = '未签到';
										break;
										case 1:
											$data[$i][$key] = '已签到';
										break;
									}
								break;
								case 'status':
								break;
								case 'gender':
									switch($val){
										case 0:
											$data[$i][$key] = '未指定';
										break;
										case 1:
											$data[$i][$key] = '男';
										break;
										case 2:
											$data[$i][$key] = '女';
										break;
									}
								break;
							}
						}
					}

					return $data;
				break;
				case 'receivablesDetail:set_column':
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					/** @var \Core\Model\ClientModel $client_model */
					$client_model = D('Core/Client');
					/** @var \Core\Model\PosMachineModel $pos_machine_model */
					$pos_machine_model = D('Core/PosMachine');
					/** @var \Core\Model\PayMethodModel $pay_method_model */
					$pay_method_model = D('Core/PayMethod');
					/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
					$receivables_type_model = D('Core/ReceivablesType');
					$keyword                = $option['keyword'];
					$new_list               = [];
					foreach($data as $key => $val){
						$client = $client_model->findClient(1, [
							'id'     => $val['cid'],
							'status' => 'not deleted'
						]);
						$payee  = $employee_model->findEmployee(1, [
							'id'     => $val['payee_id'],
							'status' => 'not deleted'
						]);
						$method = $pay_method_model->findRecord(1, ['id' => $val['method'], 'status' => 'not deleted']);
						$pos    = $pos_machine_model->findRecord(1, ['id' => $val['pos'], 'status' => 'not deleted']);
						$type   = $receivables_type_model->findRecord(1, [
							'id'     => $val['type'],
							'status' => 'not deleted'
						]);
						if($keyword == '') $found = true;
						else{
							if(!(stripos($client['name'], $keyword) === false)) $found = true;
							elseif(!(stripos($payee['name'], $keyword) === false)) $found = true;
							elseif(!(stripos($pos['name'], $keyword) === false)) $found = true;
							elseif(!(stripos($method['name'], $keyword) === false)) $found = true;
							elseif(!(stripos($type['name'], $keyword) === false)) $found = true;
							elseif(!(stripos($type['name'], $keyword) === false)) $found = true;
							else $found = false;
						}
						if($found){
							$val['client'] = $client['name'];
							$val['payee']  = $payee['name'];
							$val['pos']    = $pos['name'];
							$val['method'] = $method['name'];
							$val['type']   = $type['name'];
							$new_list[]    = $val;
						}
					}

					return $new_list;
				break;
				case 'manage:set_list':
					/** @var \Core\Model\AssignRoleModel $assign_role_model */
					$assign_role_model = D('Core/AssignRole');
					/** @var \Core\Model\RoleModel $role_model */
					$role_model = D('Core/Role');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model    = D('Core/Employee');
					$my_id             = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$my_role_arr       = $assign_role_model->getRoleByUser($my_id, 0, [
						'column' => 'id',
						'format' => 'array'
					]);
					$new_list          = $role_index = $employee_index = [];
					$sys_role_list     = $role_model->findRole(2, ['status' => 1]);
					$sys_employee_list = $employee_model->findEmployee(2, ['status' => 1]);
					foreach($sys_employee_list as $val) $employee_index[$val['id']] = $val['name'];
					foreach($sys_role_list as $val) $role_index[$val['id']] = $val['name'];
					foreach($data as $key => $val){
						$role_arr     = explode(',', $val['role']);
						$employee_arr = explode(',', $val['employee']);
						$can_view     = false;
						foreach($my_role_arr as $v){
							if(in_array($v, $role_arr) || in_array($my_id, $employee_arr)){
								$can_view = true;
								break;
							}
						}
						if(!$can_view) continue;
						$role = $employee = '';
						foreach($role_arr as $v) $role .= "$role_index[$v],";
						foreach($employee_arr as $v) $employee .= "$employee_index[$v],";
						$role            = trim($role, ',');
						$employee        = trim($employee, ',');
						$val['role']     = $role;
						$val['employee'] = $employee;
						$new_list[]      = $val;
					}

					return $new_list;
				break;
				case 'alter:set_column':
					$eid_arr  = explode(',', $data['employee']);
					$rid_arr  = explode(',', $data['role']);
					$employee = $role = '';
					/** @var \Core\Model\RoleModel $role_model */
					$role_model = D('Core/Role');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					foreach($eid_arr as $val){
						$temp_empl = $employee_model->findEmployee(1, ['id' => $val, 'status' => 1]);
						$employee .= "$temp_empl[name], ";
					}
					foreach($rid_arr as $val){
						$temp_role = $role_model->findRole(1, ['id' => $val, 'status' => 1]);
						$role .= "$temp_role[name], ";
					}
					$role                 = trim($role, ', ');
					$employee             = trim($employee, ', ');
					$data['role_str']     = $role;
					$data['employee_str'] = $employee;

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}

		public function handlerRequest($type, $option = []){
			switch($type){
				case 'get_role':
					/** @var \Core\Model\RoleModel $role_model */
					$role_model    = D('Core/Role');
					$employee_list = $role_model->findRole(2, ['status' => 1, 'keyword' => I('post.keyword', '')]);

					return ['data' => $employee_list, '__ajax__' => true];
				break;
				case 'get_employee':
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					/** @var \Core\Model\DepartmentModel $department_model */
					$department_model = D('Core/Department');
					$employee_list    = $employee_model->findEmployee(2, [
						'status'  => 1,
						'keyword' => I('post.keyword', '')
					]);
					$department_list  = $department_model->findDepartment(2, [
						'status'  => 1,
						'keyword' => I('post.keyword', '')
					]);
					$dept_index       = [];
					foreach($department_list as $key => $val) $dept_index[$val['id']] = $val['name'];
					foreach($employee_list as $key => $val){
						$department                        = $dept_index[$val['did']];
						$department                        = $department ? $department : '吉美集团';
						$employee_list[$key]['department'] = $department;
					}

					return ['data' => $employee_list, '__ajax__' => true];
				break;
				case 'create':
					$addSelfEmployeeID = function ($employee_str){
						$my_id        = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$employee_arr = explode(',', $employee_str);
						if(!in_array($my_id, $employee_arr)) $employee_arr[] = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');

						return implode(',', $employee_arr);
					};
					/** @var \Core\Model\ReportEntryModel $model */
					$model            = D('Core/ReportEntry');
					$data             = I('post.');
					$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['creatime'] = time();
					$data['employee'] = $addSelfEmployeeID($data['employee_arr']);
					$data['role']     = $data['role_arr'];
					$data['mid']      = I('get.mid', 0, 'int');
					$result           = $model->createRecord($data);

					return array_merge($result, [
						'__ajax__'   => false,
						'__return__' => U('manage', ['mid' => I('get.mid', 0, 'int')])
					]);
				break;
				case 'alter':
					$addSelfEmployeeID = function ($employee_str){
						$my_id        = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$employee_arr = explode(',', $employee_str);
						if(!in_array($my_id, $employee_arr)) $employee_arr[] = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');

						return implode(',', $employee_arr);
					};
					/** @var \Core\Model\ReportEntryModel $model */
					$model            = D('Core/ReportEntry');
					$data             = I('post.');
					$data['employee'] = $addSelfEmployeeID($data['employee_arr']);
					$data['role']     = $data['role_arr'];
					$result           = $model->alterRecord([
						'id'  => I('get.id', 0, 'int'),
						'mid' => I('get.mid', 0, 'int')
					], $data);

					return array_merge($result, [
						'__ajax__'   => false,
						'__return__' => U('manage', ['mid' => I('get.mid', 0, 'int')])
					]);
				break;
				case 'delete':
					/** @var \Core\Model\ReportEntryModel $model */
					$model  = D('Core/ReportEntry');
					$delete_id = explode(',', I('post.id'));
					$result = $model->deleteRecord(['id' => ['in', $delete_id]]);

					return array_merge($result, [
						'__ajax__' => false
					]);
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function getReportMenuList(){
			/** @var \Core\Model\ReportEntryModel $model */
			$model = D('Core/ReportEntry');
			/** @var \Core\Model\AssignRoleModel $assign_role_model */
			$assign_role_model = D('Core/AssignRole');
			$assigned_role     = $assign_role_model->getRoleByUser(I('session.MANAGER_EMPLOYEE_ID', 0, 'int'), 0, [
				'column' => 'id',
				'format' => 'array'
			]);
			$list              = $model->findRecord(2, ['status' => 1]);
			$visible_list      = [];
			foreach($list as $val){
				$role_arr = explode(',', $val['role']);
				foreach($role_arr as $role){
					if(in_array($role, $assigned_role)){
						$visible_list[] = $val;
						break;
					}
				}
			}

			return $visible_list;
		}
	}