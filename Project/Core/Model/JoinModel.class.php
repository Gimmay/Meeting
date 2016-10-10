<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 16:31
	 */
	namespace Core\Model;

	use Exception;

	class JoinModel extends CoreModel{
		protected $tableName   = 'join';
		protected $tablePrefix = 'workflow_';

		public function _initialize(){
			parent::_initialize();
		}

		public function createRecord($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '创建成功', 'id' => $result];
					else return ['status' => false, 'message' => '创建失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['cid'])) $where['cid'] = $filter['cid'];
			if(isset($filter['mid'])) $where['mid'] = $filter['mid'];
			if(isset($filter['print_status'])) $where['print_status'] = $filter['print_status'];
			if(isset($filter['sign_status'])) $where['sign_status'] = $filter['sign_status'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
			};
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

		public function alterRecord($id, $data){
			if($this->create($data)){
				try{
					$result = $this->where(['id' => ['in', $id]])->save($data);
					if($result) return ['status' => true, 'message' => '操作成功'];
					else return ['status' => true, 'message' => $this->getError()];
				}catch(Exception $error){
					return ['status' => false, 'message' => $error->getMessage()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function deleteRecord($ids){
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