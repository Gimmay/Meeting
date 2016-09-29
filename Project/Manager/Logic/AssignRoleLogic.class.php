<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 13:51
	 */
	namespace Manager\Logic;

	class AssignRoleLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function assignRole($rid, $oid, $type){
			/** @var \Core\Model\AssignRoleModel $model */
			$model            = D('Core/AssignRole');
			$data['rid']      = (int)$rid;
			$data['oid']      = (int)$oid;
			$data['type']     = $type == 0 ? 0 : ($type == 1 ? 1 : 0);
			$data['creatime'] = time();
			$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			C('TOKEN_ON', false);
			return $model->createRecord($data);
		}

		public function antiAssignRole($id, $oid, $type){
			/** @var \Core\Model\AssignRoleModel $model */
			$model         = D('Core/AssignRole');
			$where['rid']  = (int)$id;
			$where['oid']  = (int)$oid;
			$where['type'] = $type == 0 ? 0 : ($type == 1 ? 1 : 0);
			C('TOKEN_ON', false);

			return $model->deleteRecord($where);
		}
	}