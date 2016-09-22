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
		 *
		 * *若 $pid 参数是数组，则表示必须同时包含数组中所有的元素（权限ID）才表示该对象拥有此权限
		 *
		 * @param array|int $pid  权限ID 可由一维数组和整型数值构成
		 * @param int       $oid  对象ID
		 * @param int       $type 对象类型 包括员工和客户
		 *
		 * @return bool
		 */
		public function hasPermission($pid, $oid, $type = 1){
			$result = false;
			switch($type){
				case 1: // employee
					/** @var \Core\Model\PermissionModel $model */
					$model  = D('Core/Permission');
					$result = $model->getPermissionOfEmployee($oid, 'arr');
					if(is_array($pid)){
						$count = 0;
						foreach($pid as $val){
							if(in_array($val, $result)) $count++;
						}
						if($count == count($pid)) return true;
						else return false;
					}
					if(is_numeric($pid) && in_array($pid, $result)) return true;
					else return false;
				break;
				case 2:
					// client todo
				break;
			}

			return false;
		}
	}