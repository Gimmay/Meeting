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

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id']) && $filter['id']) $where['id'] = $filter['id'];
			if(isset($filter['rid']) && $filter['rid']) $where['rid'] = $filter['rid'];
			if(isset($filter['oid']) && $filter['oid']) $where['oid'] = $filter['oid'];
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->count();
						else $result = $this->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->where($where)->count();
						else $result = $this->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->find();
						else $result = $this->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->where($where)->find();
						else $result = $this->where($where)->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $this->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->where($where)->order($filter['_order'])->select();
						else $result = $this->where($where)->order($filter['_order'])->select();
					}
				break;
			}

			return $result;
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

		public function getRoleByUser($oid, $type = 0, $result_conf = ['column' => 'name', 'format' => 'string']){
			$where['main.type']  = $type;
			$where['main.oid']   = $oid;
			$where['sub.status'] = ['neq', 2];
			$result              = $this->alias('main')->join("system_role sub on sub.id = main.rid")->where($where)->field('sub.*')->select();
			$role_result         = [];
			foreach($result as $val){
				if($result_conf['column'] == 'name') $role_result[] = $val['name'];
				if($result_conf['column'] == 'id') $role_result[] = $val['id'];
			}
			if($result_conf['format'] == 'string') return implode(',', $role_result);
			elseif($result_conf['format'] == 'array') return $role_result;
			else return null;
		}
	}