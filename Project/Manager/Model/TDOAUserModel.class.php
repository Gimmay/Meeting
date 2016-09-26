<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-22
	 * Time: 10:58
	 */
	namespace Manager\Model;

	class TDOAUserModel extends ManagerModel{
		protected $connection = "mysql://quasar:t2artworks@192.168.0.77:3336/td_oa#utf8";

		public function _initialize(){
			parent::_initialize();
		}

		public function getUserSelectList(){
			$sql = "SELECT
	concat(byname, ' - ', user_name) `html`, concat(byname, ',', user_name, ',', user_name_index) `keyword`,
	user_name `value`,
	concat('code=', byname, '&name=', user_name, '&birthday=', birthday, '&position=', user_priv_name, '&title=',
		   user_priv_name, '&mobile=', mobil_no, '&status=', not_login, '&gender=', sex, '&dept_code=', DEPT_ID, '&dept_name=', ifnull((select DEPT_NAME from department where user.DEPT_ID = department.DEPT_ID), '吉美集团')) `ext`
FROM user
WHERE not_login=0";

			return $this->query($sql);
		}
	}