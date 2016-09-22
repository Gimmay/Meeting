<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:50
	 */
	namespace Manager\Controller;

	use Core\Logic\PermissionLogic;
	use Quasar\StringPlus;
	use Think\Page;

	class EmployeeController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function login(){
			if(IS_POST){
				/** @var \Core\Model\EmployeeModel $model */
				$model    = D('Core/Employee');
				$username = I('post.username', '');
				$password = I('post.password', '');
				$result   = $model->checkLogin($username, $password);
				if($result['status']) $this->success($result['message'], U('Meeting/manage'));
				else $this->error($result['message'], '', 3);
				exit;
			}
			$this->display();
		}

		public function logout(){
			session_unset();
			session_destroy();
			$this->success('注销成功');
		}

		public function alterPassword(){
			if(IS_POST){
				/** @var \Core\Model\EmployeeModel $model */
				$model   = D('Core/Employee');
				$old_pwd = $_POST['old_password'];
				$new_pwd = $_POST['new_password'];
				$result  = $model->alterPassword($old_pwd, $new_pwd);
				if($result['status']) $this->success($result['message']);
				else $this->error($result['message'], '', 3);
				exit;
			}
		}

		public function manage(){
			/** @var \Core\Model\EmployeeModel $model */
			$model = D('Core/Employee');
			/** @var \Core\Model\RoleModel $role_model */
			$role_model = D('Core/Role');
			/** @var \Core\Model\AssignRoleModel $assign_role_model */
			$assign_role_model = D('Core/AssignRole');
			/** @var \Core\Model\AssignPermissionModel $assign_permission_model */
			$assign_permission_model = D('Core/AssignPermission');
			/** @var \Core\Model\PermissionModel $permission_model */
			$permission_model = D('Core/Permission');
			$permission_logic = new PermissionLogic();
			$p_list           = [
				'list'                                                              => $permission_logic->hasPermission(2, I('session.MANAGER_USER_ID', 0, 'int')),
				'getAssignedRole'                                                   => $permission_logic->hasPermission(7, I('session.MANAGER_USER_ID', 0, 'int')),
				'getUnassignedRole'                                                 => $permission_logic->hasPermission(8, I('session.MANAGER_USER_ID', 0, 'int')),
				'assignRole'                                                        => $permission_logic->hasPermission([
					5, 6
				], I('session.MANAGER_USER_ID', 0, 'int')),
				'getAssignedPermission'                                             => $permission_logic->hasPermission(9, I('session.MANAGER_USER_ID', 0, 'int')),
				'getUnassignedPermission'                                           => $permission_logic->hasPermission(10, I('session.MANAGER_USER_ID', 0, 'int')),
				'assignPermission'                                                  => $permission_logic->hasPermission([
					11, 12
				], I('session.MANAGER_USER_ID', 0, 'int')),
				'delete'                                                            => $permission_logic->hasPermission(3, I('session.MANAGER_USER_ID', 0, 'int')),
				'alter'                                                             => $permission_logic->hasPermission(4, I('session.MANAGER_USER_ID', 0, 'int')),
				'create'                                                            => $permission_logic->hasPermission(1, I('session.MANAGER_USER_ID', 0, 'int'))
			];
			if(IS_POST){
				$type = strtolower(I('post.requestType', ''));
				switch($type){
					case 'get_assigned_role':
						if($p_list['getAssignedRole']){
							$result = $role_model->getRoleOfEmployee(I('post.id', 0, 'int'), null);
							echo json_encode($result);
						}
						else echo json_encode(['status' => false, 'message' => '您没有查询已分配的角色的权限']);
					break;
					case 'get_unassigned_role':
						if($p_list['getUnassignedRole']){
							$result = $role_model->getRoleOfEmployee(I('post.id', 0, 'int'), null, true, I('post.keyword', ''));
							echo json_encode($result);
						}
						else echo json_encode(['status' => false, 'message' => '您没有查询未分配角色的权限']);
					break;
					case 'assign_role':
						if($p_list['assignRole']){
							$result = $assign_role_model->assignRole(I('post.rid', 0, 'int'), I('post.id', 0, 'int'), 0);
							echo json_encode($result);
						}
						else echo json_encode(['status' => false, 'message' => '您没有分配角色的权限']);
					break;
					case 'anti_assign_role':
						if($p_list['assignRole']){
							$result = $assign_role_model->antiAssignRole(I('post.rid', 0, 'int'), I('post.id', 0, 'int'), 0);
							echo json_encode($result);
						}
						else echo json_encode(['status' => false, 'message' => '您没有收回角色的权限']);
					break;
					case 'get_assigned_permission':
						if($p_list['getAssignedPermission']){
							$result = $permission_model->getPermissionOfEmployee(I('post.id', 0, 'int'), null);
							echo json_encode($result);
						}
						else echo json_encode(['status' => false, 'message' => '您没有查询已分配权限的权限']);
					break;
					case 'get_unassigned_permission':
						if($p_list['getUnassignedPermission']){
							$result = $permission_model->getPermissionOfEmployee(I('post.id', 0, 'int'), null, true, I('post.keyword', ''));
							echo json_encode($result);
						}
						else echo json_encode(['status' => false, 'message' => '您没有查询未分配权限的权限']);
					break;
					case 'assign_permission':
						if($p_list['assignPermission']){
							$result = $assign_permission_model->assignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 1);
							echo json_encode($result);
						}
						else echo json_encode(['status' => false, 'message' => '您没有分配权限的权限']);
					break;
					case 'anti_assign_permission':
						if($p_list['assignPermission']){
							$type   = I('post.type', 1, 'int');
							$result = ['status' => false, '参数错误'];
							if($type == 0) $result = $assign_permission_model->antiAssignPermission(I('post.pid', 0, 'int'), I('post.rid', 0, 'int'), 0);
							if($type == 1) $result = $assign_permission_model->antiAssignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 1);
							echo json_encode($result);
						}
						else echo json_encode(['status' => false, 'message' => '您没有收回权限的权限']);
					break;
					case 'delete':
						if($p_list['delete']){
							$result = $model->deleteEmployee(I('post.id'));
							if($result['status']) $this->success($result['message']);
							else $this->error($result['message'], '', 3);
						}
						else echo json_encode(['status' => false, 'message' => '您没有删除员工的权限']);
					break;
					default:
					break;
				}
				exit;
			}
			if($p_list['list']){
				/** @var \Manager\Model\EmployeeModel $model_2 */
				$model_2 = D('Employee');
				/* 获取当前员工角色的最大等级 */
				$max_role_level = $role_model->getMaxRoleLevel(I('session.MANAGER_USER_ID', 0, 'int'));
				/* 获取当前条件下员工记录数 */
				$list_total = $model->findEmployee(0, ['keyword' => I('get.keyword', ''), 'status' => 'not deleted']);
				/* 分页设置 */
				$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show = $page_object->show();
				/* 当前页的员工记录列表 */
				$employee_list = $model->findEmployee(2, [
					'keyword' => I('get.keyword', ''), '_limit' => $page_object->firstRow.','.$page_object->listRows,
					'order'   => I('get.column', 'id').' '.I('get.sort', 'desc'), 'status' => 'not deleted'
				]);
				$employee_list = $model_2->writeExtendInformation($employee_list);
				/* 为每条用户记录设定最大的角色等级 */
				$employee_list = $this->_setMaxRoleLevel($employee_list);
				$this->assign('list', $employee_list);
				$this->assign('page_show', $page_show);
				$this->assign('max_role_level', $max_role_level ? $max_role_level : 5);
				$this->assign('permission', $p_list);
				$this->display();
			}
			else{
				$this->error('您没有查看员工模块的权限');
			}
		}

		public function create(){
			/** @var \Core\Model\EmployeeModel $model */
			$model = D('Core/Employee');
			if(IS_POST){
				$result = $model->createEmployee(I('post.'));
				if($result['status']) $this->success($result['message'], U('manage'));
				else $this->error($result['message'], '', 3);
				exit;
			}
			/** @var \Manager\Model\TDOAUserModel $oa_user_model */
			$oa_user_model = D('TDOAUser');
			/** @var \Core\Model\DepartmentModel $dept_model */
			$dept_model = D('Core/Department');
			/* 获取职位列表（for select插件） */
			$position = $model->getPositionSelectList();
			/* 获取OA用户列表（for select插件） */
			$oa_user = $oa_user_model->getUserSelectList();
			/* 获取部门列表（for select插件） */
			$dept = $dept_model->getDepartmentSelectList();
			$this->assign('position', $position);
			$this->assign('oa_user', $oa_user);
			$this->assign('dept', $dept);
			$this->display();
		}

		public function alter(){
			/** @var \Core\Model\EmployeeModel $model */
			$model = D('Core/Employee');
			if(IS_POST){
				echo 123;
				exit;
				$result = $model->createEmployee(I('post.'));
				if($result['status']) $this->success($result['message'], U('manage'));
				else $this->error($result['message'], '', 3);
				exit;
			}
			/** @var \Manager\Model\EmployeeModel $model_2 */
			$model_2 = D('Employee');
			$info    = $model->findEmployee(1, ['id' => I('get.id', 0, 'int'), 'status' => 'not deleted']);
			$info    = $model_2->writeExtendInformation($info, true);
			/** @var \Core\Model\DepartmentModel $dept_model */
			$dept_model = D('Core/Department');
			/* 获取职位列表（for select插件） */
			$position = $model->getPositionSelectList();
			/* 获取部门列表（for select插件） */
			$dept = $dept_model->getDepartmentSelectList();
			$this->assign('position', $position);
			$this->assign('dept', $dept);
			$this->assign('employee', $info);
			$this->display();
		}

		private function _setMaxRoleLevel($list){
			$result = [];
			/** @var \Core\Model\RoleModel $model */
			$model = D('Core/Role');
			foreach($list as $val){
				$tmp              = $model->getMaxRoleLevel($val['id']);
				$val['roleLevel'] = $tmp ? $tmp : 5;
				$result[]         = $val;
			}

			return $result;
		}

		public function enable(){
			/** @var \Core\Model\PermissionModel $model */
			$model = D('Core/Permission');
			print_r($model->getPermissionOfEmployee(I('get.id', 0, 'int'), 'arr'));
		}

		public function disable(){
		}

		public function test(){
			$str = new StringPlus();
			echo $str->makePassword('123456', '0967');
		}
	}