<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 12:30
	 */
	namespace Manager\Logic;

	use Core\Logic\LogLogic;

	class AssignPermissionLogic extends ManagerLogic{
		public function assignPermission($pid, $oid, $type){
			/** @var \Core\Model\AssignPermissionModel $model */
			$model            = D('Core/AssignPermission');
			$data['pid']      = (int)$pid;
			$data['oid']      = (int)$oid;
			$data['type']     = $type == 0 ? 0 : ($type == 1 ? 1 : ($type == 2 ? 2 : 0));
			$data['creatime'] = time();
			$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			C('TOKEN_ON', false);
			$log_logic = new LogLogic();
			$log_logic->create([
				'dbTable'  => 'workflow_assign_permission',
				'dbColumn' => '*',
				'extend'   => 'PC',
				'action'   => '授予权限',
				'type'     => 'create'
			]);

			return $model->createRecord($data);
		}

		public function antiAssignPermission($pid, $oid, $type){
			/** @var \Core\Model\AssignPermissionModel $model */
			$model             = D('Core/AssignPermission');
			$condition['pid']  = (int)$pid;
			$condition['oid']  = (int)$oid;
			$condition['type'] = $type == 0 ? 0 : ($type == 1 ? 1 : ($type == 2 ? 2 : 0));
			C('TOKEN_ON', false);
			$log_logic = new LogLogic();
			$log_logic->create([
				'dbTable'  => 'workflow_assign_permission',
				'dbColumn' => '*',
				'extend'   => 'PC',
				'action'   => '收回权限',
				'type'     => 'delete'
			]);
			return $model->deleteRecord($condition);
		}

		public function multiAssignPermission($p_group, $oid, $type){
			/** @var \Core\Model\PermissionModel $model */
			$model = D('Core/Permission');
			/** @var \Core\Model\AssignPermissionModel $assign_permission_model */
			$assign_permission_model = D('Core/AssignPermission');
			$list                    = $model->findPermission(2, ['group' => $p_group]);
			$save_data               = [];
			$pid_arr                 = [];
			$creator                 = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			foreach($list as $key => $val){
				$pid_arr[]   = $val['id'];
				$save_data[] = [
					'pid'      => $val['id'],
					'oid'      => $oid,
					'type'     => $type,
					'creatime' => time(),
					'creator'  => $creator
				];
			}
			C('TOKEN_ON', false);
			$assign_permission_model->deleteRecord([
				'pid'  => ['in', $pid_arr],
				'oid'  => $oid,
				'type' => $type
			]);
			$result = $assign_permission_model->createMultiRecord($save_data);
			$log_logic = new LogLogic();
			$log_logic->create([
				'dbTable'  => 'workflow_assign_permission',
				'dbColumn' => '*',
				'extend'   => 'PC',
				'action'   => '批量授予权限',
				'type'     => 'create'
			]);
			return $result;
		}

		public function multiAntiAssignPermission($p_group, $oid, $type){
			/** @var \Core\Model\PermissionModel $model */
			$model = D('Core/Permission');
			/** @var \Core\Model\AssignPermissionModel $assign_permission_model */
			$assign_permission_model = D('Core/AssignPermission');
			$list                    = $model->findPermission(2, ['group' => $p_group]);
			$pid_arr                 = [];
			foreach($list as $key => $val) $pid_arr[] = $val['id'];
			$condition['pid']  = ['in', $pid_arr];
			$condition['oid']  = $oid;
			$condition['type'] = $type == 0 ? 0 : ($type == 1 ? 1 : ($type == 2 ? 2 : 0));
			C('TOKEN_ON', false);
			$result = $assign_permission_model->deleteRecord([
				'pid'  => ['in', $pid_arr],
				'oid'  => $oid,
				'type' => $type
			]);
			$log_logic = new LogLogic();
			$log_logic->create([
				'dbTable'  => 'workflow_assign_permission',
				'dbColumn' => '*',
				'extend'   => 'PC',
				'action'   => '批量收回权限',
				'type'     => 'delete'
			]);
			return $result;
		}
	}