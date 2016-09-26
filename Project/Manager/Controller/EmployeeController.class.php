<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:50
	 */
	namespace Manager\Controller;

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
			$role_model     = D('Core/Role');
			$employee_logic = new EmployeeLogic();
			$p_list         = $employee_logic->getPermissionList();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $employee_logic->handlerRequest($type);
				if($result === -1){
				}
				else{
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($p_list['list']){
				$logic = new EmployeeLogic();
				/* 获取当前员工角色的最大等级 */
				$max_role_level = $role_model->getMaxRoleLevel(I('session.MANAGER_EMPLOYEE_ID', 0, 'int'));
				/* 获取当前条件下员工记录数 */
				$list_total = $model->findEmployee(0, ['keyword' => I('get.keyword', ''), 'status' => 'not deleted']);
				/* 分页设置 */
				$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show = $page_object->show();
				/* 当前页的员工记录列表 */
				$employee_list = $model->findEmployee(2, [
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => I('get.column', 'id').' '.I('get.sort', 'desc'),
					'status'  => 'not deleted'
				]);
				$employee_list = $logic->writeExtendInformation($employee_list);
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
			$logic = new EmployeeLogic();
			if(IS_POST){
				$result = $logic->create(I('post.'));
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
				$result = $model->alterEmployee(I('get.id', 0, 'int'), I('post.')); //传值到model里面操作
				if($result['status']) $this->success('写入成功',U('manage')); //判断status存在
				else $this->error($result['message']);			  //判断status不存在
				exit;
			}
			$logic = new EmployeeLogic();
			$info    = $model->findEmployee(1, ['id' => I('get.id', 0, 'int'), 'status' => 'not deleted']);
			$info    = $logic->writeExtendInformation($info, true);
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