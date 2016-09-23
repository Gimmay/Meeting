<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 12:30
	 */
	namespace Manager\Logic;

	class AssignPermissionLogic extends ManagerLogic{
		public function assignPermission($pid, $oid, $type){
			/** @var \Core\Model\AssignPermissionModel $model */
			$model            = D('Core/AssignPermission');
			$data['pid']      = (int)$pid;
			$data['oid']      = (int)$oid;
			$data['type']     = $type == 0 ? 0 : ($type == 1 ? 1 : ($type == 2 ? 2 : 0));
			$data['creatime'] = time();
			//$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			$data['creator']  = -1;

			return $model->createRecord($data);
		}

		public function antiAssignPermission($pid, $oid, $type){
			/** @var \Core\Model\AssignPermissionModel $model */
			$model             = D('Core/AssignPermission');
			$condition['pid']  = (int)$pid;
			$condition['oid']  = (int)$oid;
			$condition['type'] = $type == 0 ? 0 : ($type == 1 ? 1 : ($type == 2 ? 2 : 0));

			return $model->deleteRecord($condition);
		}
	}