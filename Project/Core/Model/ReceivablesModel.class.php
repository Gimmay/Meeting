<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-10
	 * Time: 16:46
	 */
	namespace Core\Model;

	use Exception;

	class ReceivablesModel extends CoreModel{
		protected $tableName       = 'receivables';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function createRecord($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '收款成功'];
					else return ['status' => false, 'message' => '收款失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $message];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id']) && $filter['id']) $where['id'] = $filter['id'];
			if(isset($filter['cid']) && $filter['cid']) $where['cid'] = $filter['cid'];
			if(isset($filter['mid']) && $filter['mid']) $where['mid'] = $filter['mid'];
			if(isset($filter['keyword']) && $filter['keyword']){
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

		public function alterRecord($id, $data){
			if($this->create($data)){
				try{
					$result = $this->where(['id' => ['in', $id]])->save($data);

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

		public function sumPrice($filter = []){
			$where = [];
			if(isset($filter['cid']) && $filter['cid']) $where['cid'] = $filter['cid'];
			if(isset($filter['mid']) && $filter['mid']) $where['mid'] = $filter['mid'];
			if($where == []) $result = $this->sum('price');
			else $result = $this->where($where)->sum('price');

			return $result;
		}
	}