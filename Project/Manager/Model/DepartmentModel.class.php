<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-20
	 * Time: 15:00
	 */
	namespace Manager\Model;

	class DepartmentModel extends ManagerModel{
		protected $tableName       = 'department';
		protected $tablePrefix     = 'user_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function getDepartmentSelectList(){
			$sql = "select temp.value, CONCAT(temp.name, ' (', temp.company, ')') html, CONCAT(temp.name,'&',temp.company) keyword from (
select self.id value, self.name, IFNULL((select parent_2.name from user_department parent_2 where parent.parent_id = parent_2.id), parent.name) company
from user_department self, user_department parent
where self.parent_id <> 0 and parent.id = self.parent_id) temp";

			return $this->query($sql);
		}

		public function getCompanySelectList(){
			$sql = "select name keyword, name html, name value from user_department
where parent_id = 0";

			return $this->query($sql);
		}
	}