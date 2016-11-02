<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-2
	 * Time: 17:23
	 */
	namespace Manager\Controller;

	use Manager\Logic\EmployeeLogic;
	use Manager\Logic\MyLogic;

	class MyController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function information(){
			if(IS_POST){
				$logic  = new MyLogic();
				$result = $logic->handlerRequest('alter_information');
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
			/** @var \Manager\Model\EmployeeModel $model */
			$model = D('Manager/Employee');
			/** @var \Core\Model\EmployeeModel $core_model */
			$core_model     = D('Core/Employee');
			$employee_logic = new EmployeeLogic();
			$info           = $core_model->findEmployee(1, [
				'id'     => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
				'status' => 'not deleted'
			]);
			$info           = $employee_logic->writeExtendInformation($info, true);
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

		public function password(){
			$logic = new MyLogic();
			if(IS_POST){
				$result = $logic->handlerRequest('alter_password');
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
			$this->display();
		}

		public function logout(){
			session_unset();
			session_destroy();
			$this->success('注销成功');
		}
	}