<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-12
	 * Time: 8:51
	 */
	namespace Core\Model;

	use Exception;

	class CarModel extends CoreModel{
		protected $tableName       = 'car';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function createCar($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '创建成功', 'id' => $result];
					else return ['status' => false, 'message' => '创建失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $message];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findCar($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 5];
				elseif($status == 'available') $where['status'] = ['in', [0, 1]];
				elseif($status == 'not available') $where['status'] = ['in', [2, 3, 4]];
				else $where['status'] = $filter['status'];
			}
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['plate_number'] = ['like', "%$filter[keyword]%"];
				$condition['type']         = ['like', "%$filter[keyword]%"];
				$condition['color']        = ['like', "%$filter[keyword]%"];
				$condition['model']        = ['like', "%$filter[keyword]%"];
				$condition['_logic']       = 'or';
				$where['_complex']         = $condition;
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

		public function alterCar($filter = [], $data = []){
			if($this->create($data)){
				try{
					$result = $this->where($filter)->save($data);
					if($result) return ['status' => true, 'message' => '修改成功'];
					else return ['status' => false, 'message' => '修改失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function deleteCar($filter = [], $data = []){
			if($this->create($data)){
				try{
					$result = $this->where($filter)->save(['status' => 5]);
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