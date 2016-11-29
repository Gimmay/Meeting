<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-26
	 * Time: 16:27
	 */
	namespace Core\Model;

	use Exception;

	class SystemMessageModel extends CoreModel{
		protected $tableName       = 'message';
		protected $tablePrefix     = 'system_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function findMessage($type, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['receiver'])) $where['receiver'] = $filter['receiver'];
			if(isset($filter['status'])) $where['status'] = $filter['status'];
			if(isset($filter['sender'])) $where['sender'] = $filter['sender'];
			if(isset($filter['type'])) $where['type'] = $filter['type'];
			if(isset($filter['keyword']) && $filter['keyword']){
				$where['type'] = ['like', "%$filter[keyword]%"];
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

		public function createMessage($data){
			try{
				$result = $this->add($data);
				if($result) return ['status' => true, 'message' => '记录成功', 'id' => $result];
				else return ['status' => false, 'message' => '记录失败'];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				if(!$exception['status']) return $exception;
				else return ['status' => false, 'message' => $this->getError()];
			}
		}

		public function deleteMessage($filter){
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

		public function readMessage($filter){
			try{
				$result = $this->where($filter)->save(['status' => 1]);
				if($result) return ['status' => true, 'message' => '已读取'];
				else return ['status' => false, 'message' => '读取失败'];
			}catch(Exception $error){
				$result = $this->deleteMessage($filter);
				if($result['status']) return ['status' => true, 'message' => '已读取'];
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				if(!$exception['status']) return $exception;
				else return ['status' => false, 'message' => $this->getError()];
			}
		}
	}