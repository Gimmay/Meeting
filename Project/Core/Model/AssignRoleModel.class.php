<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-16
	 * Time: 16:59
	 */
	namespace Core\Model;

	class AssignRoleModel extends CoreModel{
		protected $tableName   = 'assign_role';
		protected $tablePrefix = 'user_';

		public function _initialize(){
			parent::_initialize();
		}

		public function assignRole($id, $oid, $type){
			$data['rid']      = (int)$id;
			$data['oid']      = (int)$oid;
			$data['type']     = $type == 0 ? 0 : ($type == 1 ? 1 : 0);
			$data['creatime'] = time();
			$data['creator']  = I('session.MANAGER_USER_ID', 0, 'int');
			C('TOKEN_ON', false);
			if($this->create($data)){
				$result = $this->add($data);
				if($result) return ['status' => true, 'message' => '分配角色成功', 'id' => $result];
				else return ['status' => false, 'message' => $this->getError()];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function antiAssignRole($id, $oid, $type){
			$where['rid']  = (int)$id;
			$where['oid']  = (int)$oid;
			$where['type'] = $type == 0 ? 0 : ($type == 1 ? 1 : 0);
			C('TOKEN_ON', false);
			if($this->create($where)){
				$result = $this->where($where)->delete();
				if($result) return ['status' => true, 'message' => '取消角色成功'];
				else return ['status' => false, 'message' => $this->getError()];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function getUserByRole($rid, $type = 0){
			if($type == 0) $result = $this->alias('main')->join("user_employee sub on sub.id = main.rid")->where([
				'main.type' => 0,
				'main.rid'  => $rid
			])->field('sub.*')->select();
			else $result = $this->alias('main')->join("user_client sub on sub.id = main.rid")->where([
				'main.type' => 1,
				'main.rid'  => $rid
			])->field('sub.*')->select();

			return $result;
		}

		public function getRoleByUser($oid, $type = 0){
			$result        = $this->alias('main')->join("system_role sub on sub.id = main.rid")->where([
				'main.type' => $type,
				'main.oid'  => $oid
			])->field('sub.name')->select();
			$role_name_arr = [];
			foreach($result as $val) $role_name_arr[] = $val['name'];

			return implode(',', $role_name_arr);
		}
	}