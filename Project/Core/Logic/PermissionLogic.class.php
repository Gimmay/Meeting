<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-22
	 * Time: 16:17
	 */
	namespace Core\Logic;

	class PermissionLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 判断指定的对象是否拥有指定的权限
		 * *若 $pcode 参数是数组，则表示必须同时包含数组中所有的元素（权限码）才表示该对象拥有此权限
		 *
		 * @param array|string $pcode 权限码 可由一维数组和字符串构成
		 * @param int          $oid   对象ID
		 * @param int          $type  对象类型 包括员工和客户
		 *
		 * @return bool
		 */
		public function hasPermission($pcode, $oid, $type = 1){
			switch($type){
				case 1: // employee
					/** @var \Core\Model\PermissionModel $model */
					$model  = D('Core/Permission');
					$result = $model->getPermissionOfEmployee($oid, 'code');
					if(is_array($pcode)){
						$count = 0;
						foreach($pcode as $val){
							$val = strtoupper($val);
							if(in_array($val, $result)) $count++;
						}
						if($count == count($pcode)) return true;
						else return false;
					}
					if(is_string($pcode) && in_array($pcode, $result)) return true;
					else return false;
				break;
				case 2:
					// client todo
				break;
			}

			return false;
		}

		public function getPermissionList(){
			$permission_logic = new PermissionLogic();

			return [
				'createMeeting'                       => $permission_logic->hasPermission('CREATE_MEETING', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'deleteMeeting'                       => $permission_logic->hasPermission('DELETE_MEETING', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'alterMeeting'                        => $permission_logic->hasPermission('ALTER_MEETING', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'assignRoleForEmployee'               => $permission_logic->hasPermission([
					'ASSIGN_ROLE_FOR_EMPLOYEE',
					'ANTI_ASSIGN_ROLE_FOR_EMPLOYEE',
					'VIEW_ASSIGNED_ROLE_FOR_EMPLOYEE',
					'VIEW_UNASSIGNED_ROLE_FOR_EMPLOYEE'
				], I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'deleteEmployee'                      => $permission_logic->hasPermission('DELETE_EMPLOYEE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'createEmployee'                      => $permission_logic->hasPermission('CREATE_EMPLOYEE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'assignPermissionForEmployee'         => $permission_logic->hasPermission([
					'VIEW_UNASSIGNED_PERMISSION_FOR_EMPLOYEE',
					'VIEW_ASSIGNED_PERMISSION_FOR_EMPLOYEE',
					'ANTI_ASSIGN_PERMISSION_FOR_EMPLOYEE',
					'ASSIGN_PERMISSION_FOR_EMPLOYEE'
				], I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'viewAssignedRoleForEmployee'         => $permission_logic->hasPermission('VIEW_ASSIGNED_ROLE_FOR_EMPLOYEE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'viewAssignedPermissionForEmployee'   => $permission_logic->hasPermission('VIEW_ASSIGNED_PERMISSION_FOR_EMPLOYEE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'viewUnassignedRoleForEmployee'       => $permission_logic->hasPermission('VIEW_UNASSIGNED_ROLE_FOR_EMPLOYEE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'viewUnassignedPermissionForEmployee' => $permission_logic->hasPermission('VIEW_UNASSIGNED_PERMISSION_FOR_EMPLOYEE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'alterEmployee'                       => $permission_logic->hasPermission('ALTER_EMPLOYEE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'alterEmployeePassword'               => $permission_logic->hasPermission('ALTER_EMPLOYEE_PASSWORD', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'resetEmployeePassword'               => $permission_logic->hasPermission('RESET_EMPLOYEE_PASSWORD', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'viewEmployee'                        => $permission_logic->hasPermission('VIEW_EMPLOYEE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'deleteClient'                        => $permission_logic->hasPermission('DELETE_CLIENT', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'antiReviewClient'                    => $permission_logic->hasPermission('ANTI_REVIEW_CLIENT', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'antiSignClient'                      => $permission_logic->hasPermission('ANTI_SIGN_CLIENT', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'signClient'                          => $permission_logic->hasPermission('SIGN_CLIENT', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'createClient'                        => $permission_logic->hasPermission('CREATE_CLIENT', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'viewClient'                          => $permission_logic->hasPermission('VIEW_CLIENT', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'reviewClient'                        => $permission_logic->hasPermission('REVIEW_CLIENT', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'createRole'                          => $permission_logic->hasPermission('CREATE_ROLE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'viewRole'                            => $permission_logic->hasPermission('VIEW_ROLE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'deleteRole'                          => $permission_logic->hasPermission('DELETE_ROLE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'alterRole'                           => $permission_logic->hasPermission('ALTER_ROLE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'viewAssignedPermissionForRole'       => $permission_logic->hasPermission('VIEW_ASSIGNED_PERMISSION_FOR_ROLE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'viewUnassignedPermissionForRole'     => $permission_logic->hasPermission('VIEW_UNASSIGNED_PERMISSION_FOR_ROLE', I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
				'assignPermissionForRole'             => $permission_logic->hasPermission([
					'ASSIGN_PERMISSION_FOR_ROLE',
					'ANTI_ASSIGN_PERMISSION_FOR_ROLE',
					'VIEW_ASSIGNED_PERMISSION_FOR_ROLE',
					'VIEW_UNASSIGNED_PERMISSION_FOR_ROLE',
				], I('session.MANAGER_EMPLOYEE_ID', 0, 'int')),
			];
		}
	}