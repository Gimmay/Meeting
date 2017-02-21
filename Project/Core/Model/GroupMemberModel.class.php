<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:50
	 */
	namespace Core\Model;

	use Think\Exception;

	class GroupMemberModel extends CoreModel{
		protected $tableName       = 'group_member';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['main.id'] = $filter['id'];
			if(isset($filter['name'])) $where['name'] = $filter['name'];
			if(isset($filter['gid'])) $where['gid'] = $filter['gid'];
			if(isset($filter['cid'])) $where['cid'] = $filter['cid'];
			if(isset($filter['mid'])) $where['sub.mid'] = $filter['mid'];
			if(isset($filter['time'])) $where['time'] = $filter['time'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['main.status'] = ['neq', 2];
				else $where['main.status'] = $filter['status'];
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->limit($filter['_limit'])->count();
						else $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->limit($filter['_limit'])->where($where)->count();
						else $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->where($where)->count();
					}
				break;
				case 1: // find
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->field('sub.*, main.id, main.cid, main.time')->limit($filter['_limit'])->order($filter['_order'])->find();
						else $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->field('sub.*, main.id, main.cid, main.time')->order($filter['_order'])->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->field('sub.*, main.id, main.cid, main.time')->limit($filter['_limit'])->where($where)->order($filter['_order'])->find();
						else $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->field('sub.*, main.id, main.cid, main.time')->where($where)->order($filter['_order'])->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->field('sub.*, main.id, main.cid, main.time')->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->field('sub.*, main.id, main.cid, main.time')->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->field('sub.*, main.id, main.cid, main.time')->limit($filter['_limit'])->order($filter['_order'])->where($where)->select();
						else $result = $this->alias('main')->join('workflow_group sub on main.gid = sub.id')->field('sub.*, main.id, main.cid, main.time')->order($filter['_order'])->where($where)->select();
					}
				break;
			}

			return $result;
		}

		public function createRecord($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '创建成功', 'id' => $result];
					else return ['status' => false, 'message' => '没有创建'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function createMultiMember($data){
			try{
				$result = $this->addAll($data);
				if($result) return ['status' => true, 'message' => '创建成功'];
				else return ['status' => false, 'message' => '创建失败'];
			}catch(Exception $error){
				$message = $error->getMessage();

				return ['status' => false, 'message' => $message];
			}
		}

		public function deleteRecord($filter){
			if($this->create()){
				try{
					$result = $this->where($filter)->save(['status' => 2]);
					if($result) return ['status' => true, 'message' => '删除成功'];
					else return ['status' => false, 'message' => '没有删除任何'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function alterRecord($filter, $data){
			if($this->create($data)){
				try{
					$result = $this->where($filter)->save($data);
					if($result) return ['status' => true, 'message' => '修改成功'];
					else return ['status' => false, 'message' => '未做任何修改'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function dropRecord($filter){
			if($this->create()){
				try{
					$result = $this->where($filter)->delete();
					if($result) return ['status' => true, 'message' => '删除成功'];
					else return ['status' => false, 'message' => '没有删除任何'];
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