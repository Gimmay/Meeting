<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-19
	 * Time: 17:22
	 */
	namespace Manager\Model;

	class EmployeeModel extends ManagerModel{
		protected $tableName   = 'employee';
		protected $tablePrefix = 'user_';

		public function _initialize(){
			parent::_initialize();
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

		public function getEmployeeSelectList(){
			$sql = "SELECT
	concat(`code`, ' - ', `name`) `html`, concat(`code`, ',', `name`, ',', pinyin_code) `keyword`,
	`id` `value`
FROM user_employee
WHERE status = 1";

			return $this->query($sql);
		}
	}