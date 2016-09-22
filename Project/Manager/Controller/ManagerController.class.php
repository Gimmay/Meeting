<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:49
	 */
	namespace Manager\Controller;

	use Core\Controller\CoreController;

	class ManagerController extends CoreController{
		public function _initialize(){
			parent::_initialize();
			$this->_checkLogin();
			$this->_assignEmployeeName();
			$this->assign('cv_name', CONTROLLER_NAME.':'.ACTION_NAME);
		}

		private function _checkLogin(){
			$canAccessDirectly = function (){
				$list  = [
					'employee/login'
				];
				$curcv = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);

				return in_array($curcv, $list) ? true : false;
			};
			$model             = D('Core/Employee');
			$is_login          = $model->isLogin();
			if(!$canAccessDirectly()){
				if(!$is_login){
					$this->redirect('Employee/login');
					exit;
				}
			}
			elseif($is_login){
				$this->redirect('Index/index');
				exit;
			}
		}

		private function _assignEmployeeName(){
			$model    = D('Core/Employee');
			$employee = $model->findEmployee(1, ['id' => I('session.MANAGER_USER_ID',0,'int')]);
			$this->assign('curname', $employee['name']);
		}
	}