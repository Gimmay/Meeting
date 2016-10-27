<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-12
	 * Time: 8:53
	 */
	namespace Core\Model;

	use Exception;

	class AssignRoomModel extends CoreModel{
		protected $tableName       = 'assign_room';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

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
					else return ['status' => false, 'message' => $message];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findAssignRoom($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['rid'])) $where['rid'] = $filter['rid'];
			if(isset($filter['mid'])) $where['mid'] = $filter['mid'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 3];
				else $where['status'] = $filter['status'];
			}
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['hotel_name']   = ['like', "%$filter[keyword]%"];
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

		public function deleteAssignRoom($id){
			if($this->create()){
				try{
					$result = $this->where(['id' => ['in', $id]])->save(['status' => 3]);
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

		public function alterAssignRoom($id, $data){
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
	}