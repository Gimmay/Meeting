<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-16
	 * Time: 16:56
	 */
	namespace Core\Model;

	use Exception;

	class RoleModel extends CoreModel{
		protected $tableName   = 'role';
		protected $tablePrefix = 'system_';

		public function _initialize(){
			parent::_initialize();
		}

		public function createRole($data){
			if($this->create()){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '创建角色成功', 'id' => $result];
					else return ['status' => false, 'message' => '没有创建角色'];
				}catch(Exception $error){
					$message = $error->getMessage();
					if(stripos($message, 'Duplicate entry')) return [
						'status'  => false,
						'message' => "$data[code]工号已存在"
					];
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findRole($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['level'])) $where['level'] = [$filter['level']['operator'], $filter['level']['value']];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
			}
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['name']        = ['like', "%$filter[keyword]%"];
				$condition['pinyin_code'] = ['like', "%$filter[keyword]%"];
				$condition['_logic']      = 'or';
				$where['_complex']        = $condition;
			}
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

		public function alterRole($filter, $data){
			if($this->create($data)){
				$result = $this->where($filter)->save($data);

				return $result ? ['status' => true, 'message' => '修改成功'] : [
					'status'  => false,
					'message' => $this->getError()
				];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function getMaxRoleLevel($oid, $type = 0){
			$result = $this->alias('main')->join('user_assign_role sub on sub.rid = main.id')->where('sub.oid = %d and sub.type = %d', [
				$oid,
				$type
			])->min('level');
			if(!$result) $result = 5;

			return $result;
		}

		public function getUserOfRole($id, $type = 'list'){
			$type   = strtolower($type);
			$result = [];
			$sql    = "(select main.id, main.name, 'employee' type from user_employee main join user_assign_role sub on sub.oid = main.id and sub.type = 0 where sub.rid = $id and main.status != 2)
UNION
(select main.id, main.name, 'client' type from user_client main join user_assign_role sub on sub.oid = main.id and sub.type = 1 where sub.rid = $id and main.status != 2)";
			$list   = $this->query($sql);
			if($type == 'list') return $list;
			foreach($list as $val) array_push($result, $val['id']);
			if($type == 'str') $result = implode(',', $result);

			return $result;
		}

		public function getRoleOfEmployee($eid, $type = 'list', $not_assigned = false, $keyword = ''){
			$type = strtolower($type);
			switch($type){
				case 'str':
				case 'arr':
					$result = [];
					if($not_assigned){
						$keyword   = "%$keyword%";
						$sql       = "select sub.id from system_role main where main.id not in (select sub.rid from user_assign_role sub where sub.type = 0 and sub.oid = $eid) and (main.name like '$keyword' or main.pinyin_code like '$keyword') and main.status != 2";
						$role_list = $this->query($sql);
					}
					else $role_list = M('system_role')->alias('main')->join('user_assign_role sub on main.id = sub.rid and sub.type = 0')->where('sub.oid = %d and main.status != 2', [$eid])->field('main.name name, main.id id')->select();
					foreach($role_list as $val) array_push($result, $val['id']);
					if($type == 'str') $result = implode(',', $result);
				break;
				case 'list':
				default:
					if($not_assigned){
						$keyword = "%$keyword%";
						$sql     = "select main.name name, main.id id from system_role main where main.id not in (select sub.rid from user_assign_role sub where sub.type = 0 and sub.oid = $eid) and (main.name like '$keyword' or main.pinyin_code like '$keyword') and main.status != 2";
						$result  = $this->query($sql);
					}
					else $result = M('system_role')->alias('main')->join('user_assign_role sub on main.id = sub.rid and sub.type = 0')->where("sub.oid = %d and main.status != 2", [$eid])->field('main.name name, main.id id')->select();
				break;
			}

			return $result;
		}

		public function deleteRole($ids){
			if($this->create()){
				try{
					$where['id'] = ['in', $ids];
					$result      = $this->where($where)->save(['status' => 2]);
					if($result) return ['status' => true, 'message' => '删除成功'];
					else return ['status' => false, 'message' => '删除失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}
	}