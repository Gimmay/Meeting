<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-12-13
	 * Time: 15:08
	 */
	namespace Core\Model;

	use Exception;

	class SignResultModel extends CoreModel{
		protected $tableName       = 'sign_result';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function createRecord($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '记录创建成功', 'id' => $result];
					else return ['status' => false, 'message' => '记录创建失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $message];
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

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['mid'])) $where['mid'] = $filter['mid'];
			if(isset($filter['cid'])) $where['cid'] = $filter['cid'];
			if(isset($filter['status'])) $where['status'] = $filter['status'];
			if(isset($filter['sign_time'])){
				if($filter['sign_time']['start']&&$filter['sign_time']['end']) $where['sign_time'] = [['egt', $filter['sign_time']['start']], 'elt', $filter['sign_time']['end']];
				elseif($filter['sign_time']['start']) $where['sign_time'] = ['egt', $filter['sign_time']['start']];
				elseif($filter['sign_time']['end']) $where['sign_time'] = ['elt', $filter['sign_time']['end']];
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
	}