<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-19
	 * Time: 17:22
	 */
	namespace Manager\Model;

	use Quasar\StringPlus;

	class EmployeeModel extends ManagerModel{
		protected $tableName       = 'employee';
		protected $tablePrefix     = 'user_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function checkLogin($name, $pwd){
			$str_obj = new StringPlus();
			$pwd     = $str_obj->makePassword($pwd, $name);
			if($this->create()){
				$user = $this->where([
					'code'     => $name,
					'password' => $pwd
				])->find();
				if($user['status'] != 1) return ['status' => false, 'message' => '该用户已删除或者被禁用'];
				if($user){
					session('MANAGER_EMPLOYEE_ID', $user['id']);
					session('MANAGER_EMPLOYEE_CODE', $user['code']);

					return ['status' => true, 'message' => '登入成功'];
				}
				else return ['status' => false, 'message' => '该用户不存在或用户名/密码错误'];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function isLogin(){
			return isset($_SESSION['MANAGER_EMPLOYEE_ID']) && session('MANAGER_EMPLOYEE_ID') ? true : false;
		}

		public function getEmployeeSelectList(){
			$sql = "SELECT
	concat(`code`, ' - ', `name`) `html`, concat(`code`, ',', `name`, ',', pinyin_code) `keyword`,
	`id` `value`
FROM user_employee
WHERE status = 1";

			return $this->query($sql);
		}
	}