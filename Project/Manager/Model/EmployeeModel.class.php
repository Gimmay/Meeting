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
				if($user){
					if($user['status'] != 1) return ['status' => false, 'message' => '该用户已删除或者被禁用'];
					session('MANAGER_EMPLOYEE_ID', $user['id']);
					session('MANAGER_EMPLOYEE_CODE', $user['code']);
					session('MANAGER_EMPLOYEE_NAME', $user['name']);
					return ['status' => true, 'message' => '登入成功'];
				}
				else return ['status' => false, 'message' => '该用户不存在或用户名/密码错误'];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function getEmployeeSelectList(){
			$sql = "SELECT
	concat(`code`, ' - ', `name`) `html`, concat(`code`, ',', `name`, ',', pinyin_code) `keyword`,
	`id` `value`
FROM user_employee
WHERE status = 1";

			return $this->query($sql);
		}

		public function getEmployeeNameSelectList(){
			$sql = "SELECT
	`name` `html`, concat(`code`, ',', `name`, ',', pinyin_code) `keyword`,
	`name` `value`
FROM user_employee
WHERE status = 1";

			return $this->query($sql);
		}

		public function getPositionSelectList(){
			return $this->field("distinct position as value, position as keyword, position as html")->select();
		}

		public function getTitleSelectList(){
			return $this->field("distinct title as value, title as keyword, title as html")->select();
		}
	}