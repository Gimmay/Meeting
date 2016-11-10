<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-12
	 * Time: 18:42
	 */
	namespace Core\Model;

	use Exception;

	class JoinSignPlaceModel extends CoreModel{
		protected $tableName       = 'join_sign_place';
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
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function alterRecord($id, $data){
			if($this->create($data)){
				try{
					$result = $this->where(['id' => ['in', $id]])->save($data);
					if($result) return ['status' => true, 'message' => '操作成功'];
					else return ['status' => false, 'message' => '未做任何修改'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;

					return ['status' => false, 'message' => $message];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['cid'])) $where['sub.id'] = $filter['cid'];
			if(isset($filter['id'])) $where['main.id'] = $filter['id'];
			if(isset($filter['sid'])) $where['sid'] = $filter['sid'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['sub.status'] = ['neq', 2];
				else $where['sub.status'] = $filter['status'];
			};
			if(isset($filter['sign_status'])) $where['sign_status'] = $filter['sign_status'];
			if(isset($filter['review_status'])) $where['review_status'] = $filter['review_status'];
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['unit']        = ['like', "%$filter[keyword]%"];
				$condition['mobile']      = ['like', "%$filter[keyword]%"];
				$condition['name']        = ['like', "%$filter[keyword]%"];
				$condition['pinyin_code'] = ['like', "%$filter[keyword]%"];
				$condition['_logic']      = 'or';
				$where['_complex']        = $condition;
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->limit($filter['_limit'])->count();
						else $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->limit($filter['_limit'])->where($where)->count();
						else $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->limit($filter['_limit'])->field('sub.*, sign_time, sign_status, review_status, review_time, sign_type, main.id id, sub.id cid')->find();
						else $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->field('sub.*, sign_time, sign_status, review_status, review_time, sign_type, main.id id, sub.id cid')->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->limit($filter['_limit'])->where($where)->field('sub.*, sign_time, sign_status, review_status, review_time, sign_type, main.id id, sub.id cid')->find();
						else $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->where($where)->field('sub.*, sign_time, sign_status, review_status, review_time, sign_type, main.id id, sub.id cid')->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'main.creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->limit($filter['_limit'])->order($filter['_order'])->field('sub.*, sign_time, sign_status, review_status, review_time, sign_type, main.id id, sub.id cid')->select();
						else $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->order($filter['_order'])->field('sub.*, sign_time, sign_status, review_status, review_time, sign_type, main.id id, sub.id cid')->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->limit($filter['_limit'])->where($where)->order($filter['_order'])->field('sub.*, sign_time, sign_status, review_status, review_time, sign_type, main.id id, sub.id cid')->select();
						else $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->where($where)->order($filter['_order'])->field('sub.*, sign_time, sign_status, review_status, review_time, sign_type, main.id id, sub.id cid')->select();
					}
				break;
			}

			return $result;
		}

	}