<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-13
	 * Time: 18:01
	 */
	namespace Core\Model;

	use Exception;

	class AssignMessageModel extends CoreModel{
		protected $tableName       = 'assign_message';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['message_id'])) $where['sub.id'] = $filter['message_id'];
			if(isset($filter['id'])) $where['main.id'] = $filter['id'];
			if(isset($filter['mid'])) $where['mid'] = $filter['mid'];
			if(isset($filter['message_type'])) $where['sub.type'] = $filter['message_type'];
			if(isset($filter['type'])) $where['main.type'] = $filter['type'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted'){
					$where['sub.status']  = ['neq', 2];
					$where['main.status'] = ['neq', 2];
				}
				else $where['main.status'] = $filter['status'];
			};
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['name']        = ['like', "%$filter[keyword]%"];
				$condition['_logic']      = 'or';
				$where['_complex']        = $condition;
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->limit($filter['_limit'])->count();
						else $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->limit($filter['_limit'])->where($where)->count();
						else $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->limit($filter['_limit'])->field('sub.*, mid, main.type assign_type, main.id id, sub.id message_id ')->find();
						else $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->field('sub.*, mid, main.type assign_type, main.id id, sub.id message_id')->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->limit($filter['_limit'])->where($where)->field('sub.*, mid, main.type assign_type, main.id id, sub.id message_id')->find();
						else $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->where($where)->field('sub.*, mid, main.type assign_type, main.id id, sub.id message_id')->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'main.creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->limit($filter['_limit'])->order($filter['_order'])->field('sub.*, mid, main.type assign_type, main.id id, sub.id message_id')->select();
						else $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->order($filter['_order'])->field('sub.*, mid, main.type assign_type, main.id id, sub.id message_id')->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->limit($filter['_limit'])->where($where)->order($filter['_order'])->field('sub.*, mid, main.type assign_type, main.id id, sub.id message_id')->select();
						else $result = $this->alias('main')->join('join workflow_message sub on main.message_id = sub.id')->where($where)->order($filter['_order'])->field('sub.*, mid, main.type assign_type, main.id id, sub.id message_id')->select();
					}
				break;
			}

			return $result;
		}

		public function createRecord($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '创建会议成功'];
					else return ['status' => false, 'message' => '创建会议失败'];
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

		public function deleteRecord($ids){
			if($this->create()){
				try{
					$where['id'] = ['in', $ids];
					$result      = $this->where($where)->save(['status' => 4]);
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