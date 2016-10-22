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
					else return ['status' => false, 'message' => $message];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['cid'])) $where['sub.id'] = $filter['cid'];
			if(isset($filter['id'])) $where['main.id'] = $filter['id'];
			if(isset($filter['mid'])) $where['mid'] = $filter['mid'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted'){
					$where['sub.status'] = ['neq', 2];
					$where['main.status'] = ['neq', 2];
				}
				else $where['main.status'] = $filter['status']; // todo 是否要client表也要处理
			};
			if(isset($filter['sign_status'])){
				$status = strtolower($filter['sign_status']);
				if($status == 'not signed') $where['sign_status'] = ['neq', 1];
				else $where['sign_status'] = $filter['sign_status'];
			}
			if(isset($filter['review_status'])){
				$status = strtolower($filter['review_status']);
				if($status == 'not reviewed') $where['review_status'] = ['neq', 1];
				else $where['review_status'] = $filter['review_status'];
			}
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['club']        = ['like', "%$filter[keyword]%"];
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
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->limit($filter['_limit'])->field('sub.*, registration_date, registration_type, review_status, review_time, sign_time, sign_status, sign_type, sign_qrcode, sign_code, print_status, print_times, main.id id, sub.id cid, mid')->find();
						else $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->field('sub.*, registration_date, registration_type, review_status, review_time, sign_time, sign_status, sign_type, sign_qrcode, sign_code, print_status, print_times, main.id id, sub.id cid, mid')->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->limit($filter['_limit'])->where($where)->field('sub.*, registration_date, registration_type, review_status, review_time, sign_time, sign_status, sign_type, sign_qrcode, sign_code, print_status, print_times, main.id id, sub.id cid, mid')->find();
						else $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->where($where)->field('sub.*, registration_date, registration_type, review_status, review_time, sign_time, sign_status, sign_type, sign_qrcode, sign_code, print_status, print_times, main.id id, sub.id cid, mid')->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'main.creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->limit($filter['_limit'])->order($filter['_order'])->field('sub.*, registration_date, registration_type, review_status, review_time, sign_time, sign_status, sign_type, sign_qrcode, sign_code, print_status, print_times, main.id id, sub.id cid, mid')->select();
						else $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->order($filter['_order'])->field('sub.*, registration_date, registration_type, review_status, review_time, sign_time, sign_status, sign_type, sign_qrcode, sign_code, print_status, print_times, main.id id, sub.id cid, mid')->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->limit($filter['_limit'])->where($where)->order($filter['_order'])->field('sub.*, registration_date, registration_type, review_status, review_time, sign_time, sign_status, sign_type, sign_qrcode, sign_code, print_status, print_times, main.id id, sub.id cid, mid')->select();
						else $result = $this->alias('main')->join('join user_client sub on main.cid = sub.id')->where($where)->order($filter['_order'])->field('sub.*, registration_date,registration_type,  review_status, review_time, sign_time, sign_status, sign_type, sign_qrcode, sign_code, print_status, print_times, main.id id, sub.id cid, mid')->select();
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
					else return ['status' => false, 'message' => $message];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}
	}