<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-16
	 * Time: 16:59
	 */
	namespace Core\Model;

	use Exception;

	class AssignRoleModel extends CoreModel{
		protected $tableName   = 'assign_role';
		protected $tablePrefix = 'user_';

		public function _initialize(){
			parent::_initialize();
		}

		public function createRecord($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '分配角色成功', 'id' => $result];
					else return ['status' => false, 'message' => '没有分配角色'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function deleteRecord($condition){
			if($this->create($condition)){
				try{
					$result = $this->where($condition)->delete();
					if($result) return ['status' => true, 'message' => '取消角色成功'];
					else return ['status' => false, 'message' => '取消角色失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
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
			$where['main.type']  = $type;
			$where['main.oid']   = $oid;
			$where['sub.status'] = ['neq', 2];
			$result              = $this->alias('main')->join("system_role sub on sub.id = main.rid")->where($where)->field('sub.name')->select();
			$role_name_arr       = [];
			foreach($result as $val) $role_name_arr[] = $val['name'];

			return implode(',', $role_name_arr);
		}

		public function getRoleEffect($oid){
			$where['main.type']  = 0;
			$where['main.oid']   = $oid;
			$where['sub.status'] = ['neq', 2];
			$result          = $this->alias('main')->join("system_role sub on sub.id = main.rid")->where($where)->field('sub.effect')->select();
			$role_effect_arr = [];
			foreach($result as $val) $role_effect_arr[] = $val['effect'];

			return $role_effect_arr;
		}
	}