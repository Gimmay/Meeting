<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 14:04
	 */
	namespace Manager\Logic;

	use Core\Logic\PermissionLogic;
	use Quasar\StringPlus;

	class EmployeeLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function create($data){
			/** @var \Core\Model\EmployeeModel $model */
			$model = D('Core/Employee');
			$str_obj = new StringPlus();
			$data['status']      = $data['status'] == 1 ? 0 : (($data['status'] == 0) ? 1 : 1);
			$data['creatime']    = time();
			$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
			$data['password']    = $str_obj->makePassword($data['password'], $data['code']);
			$data['birthday']    = date('Y-m-d', strtotime($data['birthday']));
			return $model->createEmployee($data);
		}

		public function getPermissionList(){
			$permission_logic = new PermissionLogic();

			return [
				'list'                    => $permission_logic->hasPermission(2, I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'getAssignedRole'         => $permission_logic->hasPermission(7, I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'getUnassignedRole'       => $permission_logic->hasPermission(8, I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'assignRole'              => $permission_logic->hasPermission([
					5,
					6,
					7,
					8
				], I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'getAssignedPermission'   => $permission_logic->hasPermission(9, I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'getUnassignedPermission' => $permission_logic->hasPermission(10, I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'assignPermission'        => $permission_logic->hasPermission([
					11,
					12,
					9,
					10
				], I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'delete'                  => $permission_logic->hasPermission(3, I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'alter'                   => $permission_logic->hasPermission(4, I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'create'                  => $permission_logic->hasPermission(1, I('session.MANAGER_EMPLOYEE_ID', 0, 'int'))
			];
		}

		public function handlerRequest($type){
			$p_list = $this->getPermissionList();
			switch($type){
				case 'get_assigned_role':
					if($p_list['getAssignedRole']){
						/** @var \Core\Model\RoleModel $role_model */
						$role_model = D('Core/Role');
						$result     = $role_model->getRoleOfEmployee(I('post.id', 0, 'int'), null);
						echo json_encode($result);
					}
					else echo json_encode(['status' => false, 'message' => '您没有查询已分配的角色的权限']);

					return -1;
				break;
				case 'get_unassigned_role':
					if($p_list['getUnassignedRole']){
						/** @var \Core\Model\RoleModel $role_model */
						$role_model = D('Core/Role');
						$result     = $role_model->getRoleOfEmployee(I('post.id', 0, 'int'), null, true, I('post.keyword', ''));
						echo json_encode($result);
					}
					else echo json_encode(['status' => false, 'message' => '您没有查询未分配角色的权限']);

					return -1;
				break;
				case 'assign_role':
					if($p_list['assignRole']){
						$assign_role_logic = new AssignRoleLogic();
						$result            = $assign_role_logic->assignRole(I('post.rid', 0, 'int'), I('post.id', 0, 'int'), 0);
						echo json_encode($result);
					}
					else echo json_encode(['status' => false, 'message' => '您没有分配角色的权限']);

					return -1;
				break;
				case 'anti_assign_role':
					if($p_list['assignRole']){
						$assign_role_logic = new AssignRoleLogic();
						$result            = $assign_role_logic->antiAssignRole(I('post.rid', 0, 'int'), I('post.id', 0, 'int'), 0);
						echo json_encode($result);
					}
					else echo json_encode(['status' => false, 'message' => '您没有收回角色的权限']);

					return -1;
				break;
				case 'get_assigned_permission':
					if($p_list['getAssignedPermission']){
						/** @var \Core\Model\PermissionModel $permission_model */
						$permission_model = D('Core/Permission');
						$result           = $permission_model->getPermissionOfEmployee(I('post.id', 0, 'int'), null);
						echo json_encode($result);
					}
					else echo json_encode(['status' => false, 'message' => '您没有查询已分配权限的权限']);

					return -1;
				break;
				case 'get_unassigned_permission':
					if($p_list['getUnassignedPermission']){
						/** @var \Core\Model\PermissionModel $permission_model */
						$permission_model = D('Core/Permission');
						$result           = $permission_model->getPermissionOfEmployee(I('post.id', 0, 'int'), null, true, I('post.keyword', ''));
						echo json_encode($result);
					}
					else echo json_encode(['status' => false, 'message' => '您没有查询未分配权限的权限']);

					return -1;
				break;
				case 'assign_permission':
					if($p_list['assignPermission']){
						$assign_permission_logic = new AssignPermissionLogic();
						$result                  = $assign_permission_logic->assignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 1);
						echo json_encode($result);
					}
					else echo json_encode(['status' => false, 'message' => '您没有分配权限的权限']);

					return -1;
				break;
				case 'anti_assign_permission':
					if($p_list['assignPermission']){
						$type                    = I('post.type', 1, 'int');
						$result                  = ['status' => false, '参数错误'];
						$assign_permission_logic = new AssignPermissionLogic();
						if($type == 0) $result = $assign_permission_logic->antiAssignPermission(I('post.pid', 0, 'int'), I('post.rid', 0, 'int'), 0);
						if($type == 1) $result = $assign_permission_logic->antiAssignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 1);
						echo json_encode($result);
					}
					else echo json_encode(['status' => false, 'message' => '您没有收回权限的权限']);

					return -1;
				break;
				case 'delete':
					if($p_list['delete']){
						/** @var \Core\Model\EmployeeModel $model */
						$model = D('Core/Employee');

						return $model->deleteEmployee(I('post.id'));
					}
					else return ['status' => false, 'message' => '您没有删除员工的权限'];
				break;
				default:
					echo json_encode(['status' => false, 'message' => '参数错误']);

					return -1;
				break;
			}
		}

		public function writeExtendInformation($list, $single = false){
			/** @var \Core\Model\AssignRoleModel $assign_role_model */
			$assign_role_model = D('Core/AssignRole');
			/** @var \Core\Model\DepartmentModel $department_model */
			$department_model = D('Core/Department');
			if($single){
				$role               = $assign_role_model->getRoleByUser($list['id']);
				$list['role']       = $role;
				$department         = $department_model->findDepartment(1, ['id' => $list['did']]);
				$list['department'] = $department['name'];
			}
			else{
				foreach($list as $key => $val){
					$role                     = $assign_role_model->getRoleByUser($val['id']);
					$list[$key]['role']       = $role;
					$department               = $department_model->findDepartment(1, ['id' => $val['did']]);
					$list[$key]['department'] = $department['name'];
				}
			}

			return $list;
		}
	}