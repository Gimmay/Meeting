<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-16
	 * Time: 17:35
	 */
	namespace Core\Model;

	class AssignPermissionModel extends CoreModel{
		protected $tableName   = 'assign_permission';
		protected $tablePrefix = 'system_';

		public function _initialize(){
			parent::_initialize();
		}

		public function assignPermission($aid, $oid, $type){
			$data['pid']      = (int)$aid;
			$data['oid']      = (int)$oid;
			$data['type']     = $type == 0 ? 0 : ($type == 1 ? 1 : ($type == 2 ? 2 : 0));
			$data['creatime'] = time();
			$data['creator']  = I('session.MANAGER_USER_ID', 0, 'int');
			C('TOKEN_ON', false);
			if($this->create($data)){
				$result = $this->add($data);
				if($result) return ['status' => true, 'message' => '授权成功', 'id'=>$result];
				else return ['status' => false, 'message' => $this->getError()];
			}else return ['status' => false, 'message' => $this->getError()];
		}

		public function antiAssignPermission($aid, $oid, $type){
			$where['pid']  = (int)$aid;
			$where['oid']  = (int)$oid;
			$where['type'] = $type == 0 ? 0 : ($type == 1 ? 1 : ($type == 2 ? 2 : 0));
			C('TOKEN_ON', false);
			if($this->create($where)){
				$result = $this->where($where)->delete();
				if($result) return ['status' => true, 'message' => '收回权限成功'];
				else return ['status' => false, 'message' => $this->getError()];
			}else return ['status' => false, 'message' => $this->getError()];
		}
	}