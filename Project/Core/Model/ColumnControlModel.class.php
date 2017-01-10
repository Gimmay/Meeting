<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-12-27
	 * Time: 10:10
	 */
	namespace Core\Model;

	use Exception;

	class ColumnControlModel extends CoreModel{
		protected $tableName       = 'column_control';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function createMultiRecord($data){
			try{
				$result = $this->addAll($data);
				if($result) return ['status' => true, 'message' => '创建成功', 'id' => $result];
				else return ['status' => false, 'message' => '创建失败'];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				if(!$exception['status']) return $exception;
				else return ['status' => false, 'message' => $this->getError()];
			}
		}

		public function deleteRecord($filter){
			if($this->create()){
				try{
					$result = $this->where($filter)->delete();
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

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['mid'])) $where['mid'] = $filter['mid'];
			if(isset($filter['code'])) $where['code'] = $filter['code'];
			if(isset($filter['table'])) $where['table'] = $filter['table'];

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
	}