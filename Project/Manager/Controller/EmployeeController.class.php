<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:50
	 */
	namespace Manager\Controller;

	use Core\Logic\PermissionLogic;
	use Manager\Logic\EmployeeLogic;
	use Quasar\StringPlus;
	use Think\Page;

	class EmployeeController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function login(){
			if(IS_POST){
				/** @var \Manager\Model\EmployeeModel $model */
				$model    = D('Employee');
				$username = I('post.username', '');
				$password = I('post.password', '');
				$result   = $model->checkLogin($username, $password);
				if($result['status']) $this->success($result['message'], U('Meeting/manage', ['type' => 'ing']));
				else $this->error($result['message'], '', 3);
				exit;
			}
			$this->display();
		}

		public function manage(){
			$employee_logic = new EmployeeLogic();
			if(IS_POST){
				$type = strtolower(I('post.requestType', ''));
				if($type == '') $type = strtolower(I('get.requestType', ''));
				$result = $employee_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\EmployeeModel $model */
			$model = D('Core/Employee');
			/** @var \Core\Model\RoleModel $role_model */
			$role_model = D('Core/Role');
			if($this->permissionList['EMPLOYEE.VIEW']){
				$logic = new EmployeeLogic();
				/* 获取当前员工角色的最大等级 */
				$max_role_level = $role_model->getMaxRoleLevel(I('session.MANAGER_EMPLOYEE_ID', 0, 'int'));
				if(isset($_GET['rid'])){
					$role_id = I('get.rid', 0, 'int');
					/* 获取当前条件下员工记录数 */
					$list_total = $model->listEmployee(0, [
						'keyword' => I('get.keyword', ''),
						'status'  => 'not deleted',
						'rid'     => $role_id,
						'did'     => isset($_GET['did']) ? I('get.did', 0, 'int') : null
					]);
					/* 分页设置 */
					$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
					\ThinkPHP\Quasar\Page\setTheme1($page_object);
					$page_show = $page_object->show();
					/* 当前页的员工记录列表 */
					$employee_list = $model->listEmployee(2, [
						'keyword' => I('get.keyword', ''),
						'_limit'  => $page_object->firstRow.','.$page_object->listRows,
						'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
						'status'  => 'not deleted',
						'rid'     => $role_id,
						'did'     => isset($_GET['did']) ? I('get.did', 0, 'int') : null
					]);
				}
				else{
					/* 获取当前条件下员工记录数 */
					$list_total = $model->findEmployee(0, [
						'keyword' => I('get.keyword', ''),
						'status'  => 'not deleted',
						'did'     => isset($_GET['did']) ? I('get.did', 0, 'int') : null
					]);
					/* 分页设置 */
					$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
					\ThinkPHP\Quasar\Page\setTheme1($page_object);
					$page_show = $page_object->show();
					/* 当前页的员工记录列表 */
					$employee_list = $model->findEmployee(2, [
						'keyword' => I('get.keyword', ''),
						'_limit'  => $page_object->firstRow.','.$page_object->listRows,
						'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
						'status'  => 'not deleted',
						'did'     => isset($_GET['did']) ? I('get.did', 0, 'int') : null
					]);
				}
				$employee_list = $logic->writeExtendInformation($employee_list);
				/* 为每条用户记录设定最大的角色等级 */
				$employee_list = $employee_logic->setMaxRoleLevel($employee_list);
				$this->assign('list', $employee_list);
				$this->assign('page_show', $page_show);
				$this->assign('max_role_level', $max_role_level);
				$this->display();
			}
			else $this->error('您没有查看员工的权限');
		}

		public function create(){
			if($this->permissionList['EMPLOYEE.CREATE']){
				$logic = new EmployeeLogic();
				if(IS_POST){
					$result = $logic->handlerRequest('create');
					if($result['__ajax__']){
						unset($result['__ajax__']);
						echo json_encode($result);
					}
					else{
						unset($result['__ajax__']);
						if($result['status']) $this->success($result['message'], U('manage'));
						else $this->error($result['message'], '', 3);
					}
					exit;
				}
				/** @var \Manager\Model\EmployeeModel $model */
				$model = D('Manager/Employee');
				/** @var \Manager\Model\TDOAUserModel $oa_user_model */
				$oa_user_model = D('TDOAUser');
				/** @var \Manager\Model\DepartmentModel $dept_model */
				$dept_model = D('Manager/Department');
				/* 获取职位列表（for select插件） */
				$position = $model->getPositionSelectList();
				/* 获取OA用户列表（for select插件） */
				$oa_user = $oa_user_model->getUserSelectList();
				/* 获取部门列表（for select插件） */
				$dept    = $dept_model->getDepartmentSelectList();
				$company = $dept_model->getCompanySelectList();
				$this->assign('position', $position);
				$this->assign('oa_user', $oa_user);
				$this->assign('dept', $dept);
				$this->assign('company', $company);
				$this->display();
			}
			else $this->error('您没有创建员工的权限');
		}

		public function alter(){
			if($this->permissionList['EMPLOYEE.ALTER']){
				$logic = new EmployeeLogic();
				if(IS_POST){
					$result = $logic->handlerRequest('alter');
					if($result['__ajax__']){
						unset($result['__ajax__']);
						echo json_encode($result);
					}
					else{
						unset($result['__ajax__']);
						if($result['status']) $this->success($result['message'], $_POST['redirectUrl'] ? I('post.redirectUrl', '') : U('manage'));
						else $this->error($result['message'], '', 3);
					}
					exit;
				}
				/** @var \Manager\Model\EmployeeModel $model */
				$model = D('Manager/Employee');
				/** @var \Core\Model\EmployeeModel $core_model */
				$core_model = D('Core/Employee');
				$info       = $core_model->findEmployee(1, ['id' => I('get.id', 0, 'int'), 'status' => 'not deleted']);
				$info       = $logic->writeExtendInformation($info, true);
				/** @var \Manager\Model\DepartmentModel $dept_model */
				$dept_model = D('Manager/Department');
				/* 获取职位列表（for select插件） */
				$position = $model->getPositionSelectList();
				/* 获取部门列表（for select插件） */
				$dept    = $dept_model->getDepartmentSelectList();
				$company = $dept_model->getCompanySelectList();
				$this->assign('position', $position);
				$this->assign('dept', $dept);
				$this->assign('company', $company);
				$this->assign('employee', $info);
				$this->display();
			}
			else $this->error('您没有修改员工的权限');
		}

		public function forgetPassword(){
			$this->display();
		}
	}