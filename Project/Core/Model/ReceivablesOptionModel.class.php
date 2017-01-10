<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:50
	 */
	namespace Core\Model;

	use Quasar\StringPlus;
	use Think\Exception;

	class ReceivablesOptionModel extends CoreModel{
		protected $tableName       = 'receivables_option';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['mid'])) $where['mid'] = $filter['mid'];
			if(isset($filter['rid'])) $where['rid'] = $filter['rid'];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['pay_method'])) $where['pay_method'] = $filter['pay_method'];
			if(isset($filter['pos_machine'])) $where['pos_machine'] = $filter['pos_machine'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 3];
				else $where['status'] = $filter['status'];
			}
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['code']   = ['like', "%$filter[keyword]%"];
				$condition['_logic'] = 'or';
				$where['_complex']   = $condition;
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
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->order($filter['_order'])->where($where)->select();
						else $result = $this->order($filter['_order'])->where($where)->select();
					}
				break;
			}

			return $result;
		}
		

		public function createRecord($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '创建项目成功', 'id' => $result];
					else return ['status' => false, 'message' => '没有创建项目'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function createMultiRecord($data){
			try{
				$result = $this->addAll($data);
				if($result) return ['status' => true, 'message' => '创建项目成功'];
				else return ['status' => false, 'message' => '创建项目失败'];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				if(!$exception['status']) return $exception;

				return ['status' => false, 'message' => $message];
			}
		}

		public function deleteRecord($id){
			if($this->create()){
				try{
					$result = $this->where(['id' => ['in', $id]])->save(['status' => 3]);
					if($result) return ['status' => true, 'message' => '删除成功'];
					else return ['status' => false, 'message' => '没有删除任何项目'];
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
	}