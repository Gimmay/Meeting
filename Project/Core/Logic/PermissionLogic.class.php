<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-22
	 * Time: 16:17
	 */
	namespace Core\Logic;

	class PermissionLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 判断指定的对象是否拥有指定的权限
		 * *若 $pcode 参数是数组，则表示必须同时包含数组中所有的元素（权限码）才表示该对象拥有此权限
		 *
		 * @param array|string $pcode 权限码 可由一维数组和字符串构成
		 * @param int          $oid   对象ID
		 * @param int          $type  对象类型 包括员工和客户
		 *
		 * @return bool
		 */
		public function hasPermission($pcode, $oid, $type = 1){
			switch($type){
				case 1: // employee
					/** @var \Core\Model\PermissionModel $model */
					$model  = D('Core/Permission');
					$result = $model->getPermissionOfEmployee($oid, 'code');
					if(is_array($pcode)){
						$count = 0;
						foreach($pcode as $val){
							$val = strtoupper($val);
							if(in_array($val, $result)) $count++;
						}
						if($count == count($pcode)) return true;
						else return false;
					}
					if(is_string($pcode) && in_array($pcode, $result)) return true;
					else return false;
				break;
				case 2:
					// client todo
				break;
			}

			return false;
		}

		public function getPermissionList($employee_id = null){
			if($employee_id === null) $employee_id = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			/** @var \Core\Model\PermissionModel $model */
			$model    = D('Core/Permission');
			$result   = $model->getPermissionOfEmployee($employee_id, 'code');
			$new_list = [];
			foreach($result as $val) $new_list[$val] = true;

			return $new_list;
		}
	}