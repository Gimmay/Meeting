<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:54
	 */
	namespace Core\Model;

	use Exception;

	class RecycleModel extends CoreModel{
		protected $autoCheckFields = false;

		public function _initialize(){
			parent::_initialize();
		}

		public function recoveryClient($ids){
			$model = M('user_client');
			if($this->create()){
				try{
					$where['id'] = ['in', $ids];
					$result      = $model->where($where)->save(['status' => 1]);
					if($result) return ['status' => true, 'message' => '恢复成功'];
					else return ['status' => false, 'message' => '没有恢复成功'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $model->getError()];
				}
			}
			else return ['status' => false, 'message' => $model->getError()];
		}

		public function recoveryEmployee($ids){
			$model = M('user_employee');
			if($this->create()){
				try{
					$where['id'] = ['in', $ids];
					$result      = $model->where($where)->save(['status' => 1]);
					if($result) return ['status' => true, 'message' => '恢复成功'];
					else return ['status' => false, 'message' => '没有恢复成功'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $model->getError()];
				}
			}
			else return ['status' => false, 'message' => $model->getError()];
		}

		public function recoveryRole($ids){
			$model = M('workflow_meeting');
			if($this->create()){
				try{
					$where['id'] = ['in', $ids];
					$result      = $model->where($where)->save(['status' => 1]);
					if($result) return ['status' => true, 'message' => '恢复成功'];
					else return ['status' => false, 'message' => '没有恢复成功'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $model->getError()];
				}
			}
			else return ['status' => false, 'message' => $model->getError()];
		}

		public function recoveryMeeting($ids){
			$model = M('system_role');
			if($this->create()){
				try{
					$where['id'] = ['in', $ids];
					$result      = $model->where($where)->save(['status' => 1]);
					if($result) return ['status' => true, 'message' => '恢复成功'];
					else return ['status' => false, 'message' => '没有恢复成功'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $model->getError()];
				}
			}
			else return ['status' => false, 'message' => $model->getError()];
		}

		public function recoveryCoupon($ids){
			$model = M('system_role');
			if($this->create()){
				try{
					$where['id'] = ['in', $ids];
					$result      = $model->where($where)->save(['status' => 1]);
					if($result) return ['status' => true, 'message' => '恢复成功'];
					else return ['status' => false, 'message' => '没有恢复成功'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $model->getError()];
				}
			}
			else return ['status' => false, 'message' => $model->getError()];
		}

		public function findClient($type = 2, $filter = []){
			$model = M('user_client');
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['name'])) $where['name'] = $filter['name'];
			if(isset($filter['mobile'])) $where['mobile'] = $filter['mobile'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
			};
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['mobile']      = ['like', "%$filter[keyword]%"];
				$condition['name']        = ['like', "%$filter[keyword]%"];
				$condition['pinyin_code'] = ['like', "%$filter[keyword]%"];
				$condition['_logic']      = 'or';
				$where['_complex']        = $condition;
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->count();
						else $result = $model->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->count();
						else $result = $model->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->find();
						else $result = $model->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->find();
						else $result = $model->where($where)->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $model->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->order($filter['_order'])->select();
						else $result = $model->where($where)->order($filter['_order'])->select();
					}
				break;
			}

			return $result;
		}

		public function findRole($type = 2, $filter = []){
			$model = M('system_role');
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['name'])) $where['name'] = $filter['name'];
			if(isset($filter['mobile'])) $where['mobile'] = $filter['mobile'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
			};
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['mobile']      = ['like', "%$filter[keyword]%"];
				$condition['name']        = ['like', "%$filter[keyword]%"];
				$condition['pinyin_code'] = ['like', "%$filter[keyword]%"];
				$condition['_logic']      = 'or';
				$where['_complex']        = $condition;
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->count();
						else $result = $model->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->count();
						else $result = $model->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->find();
						else $result = $model->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->find();
						else $result = $model->where($where)->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $model->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->order($filter['_order'])->select();
						else $result = $model->where($where)->order($filter['_order'])->select();
					}
				break;
			}

			return $result;
		}

		public function findEmployee($type = 2, $filter = []){
			$model = M('user_employee');
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['name'])) $where['name'] = $filter['name'];
			if(isset($filter['mobile'])) $where['mobile'] = $filter['mobile'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
			};
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['mobile']      = ['like', "%$filter[keyword]%"];
				$condition['name']        = ['like', "%$filter[keyword]%"];
				$condition['pinyin_code'] = ['like', "%$filter[keyword]%"];
				$condition['_logic']      = 'or';
				$where['_complex']        = $condition;
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->count();
						else $result = $model->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->count();
						else $result = $model->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->find();
						else $result = $model->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->find();
						else $result = $model->where($where)->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $model->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->order($filter['_order'])->select();
						else $result = $model->where($where)->order($filter['_order'])->select();
					}
				break;
			}

			return $result;
		}

		public function findRecycle($type = 2, $filter = []){
			$model = M('user_employee');
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['name'])) $where['name'] = $filter['name'];
			if(isset($filter['mobile'])) $where['mobile'] = $filter['mobile'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
			};
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['mobile']      = ['like', "%$filter[keyword]%"];
				$condition['name']        = ['like', "%$filter[keyword]%"];
				$condition['pinyin_code'] = ['like', "%$filter[keyword]%"];
				$condition['_logic']      = 'or';
				$where['_complex']        = $condition;
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->count();
						else $result = $model->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->count();
						else $result = $model->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->find();
						else $result = $model->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->find();
						else $result = $model->where($where)->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $model->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->order($filter['_order'])->select();
						else $result = $model->where($where)->order($filter['_order'])->select();
					}
				break;
			}

			return $result;
		}

		public function findMeeting($type = 2, $filter = []){
			$model = M('workflow_meeting');
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['name'])) $where['name'] = $filter['name'];
			if(isset($filter['mobile'])) $where['mobile'] = $filter['mobile'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
			};
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['mobile']      = ['like', "%$filter[keyword]%"];
				$condition['name']        = ['like', "%$filter[keyword]%"];
				$condition['pinyin_code'] = ['like', "%$filter[keyword]%"];
				$condition['_logic']      = 'or';
				$where['_complex']        = $condition;
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->count();
						else $result = $model->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->count();
						else $result = $model->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->find();
						else $result = $model->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->find();
						else $result = $model->where($where)->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $model->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->order($filter['_order'])->select();
						else $result = $model->where($where)->order($filter['_order'])->select();
					}
				break;
			}

			return $result;
		}

		public function findCoupon($type = 2, $filter = []){
			$model = M('workflow_coupon');
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['name'])) $where['name'] = $filter['name'];
			if(isset($filter['mobile'])) $where['mobile'] = $filter['mobile'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
			};
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['mobile']      = ['like', "%$filter[keyword]%"];
				$condition['name']        = ['like', "%$filter[keyword]%"];
				$condition['pinyin_code'] = ['like', "%$filter[keyword]%"];
				$condition['_logic']      = 'or';
				$where['_complex']        = $condition;
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->count();
						else $result = $model->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->count();
						else $result = $model->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->find();
						else $result = $model->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->find();
						else $result = $model->where($where)->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $model->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->order($filter['_order'])->select();
						else $result = $model->where($where)->order($filter['_order'])->select();
					}
				break;
			}

			return $result;
		}

		public function findCouponItem($type = 2, $filter = []){
			$model = M('workflow_coupon_item');
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['name'])) $where['name'] = $filter['name'];
			if(isset($filter['mobile'])) $where['mobile'] = $filter['mobile'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
			};
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['mobile']      = ['like', "%$filter[keyword]%"];
				$condition['name']        = ['like', "%$filter[keyword]%"];
				$condition['pinyin_code'] = ['like', "%$filter[keyword]%"];
				$condition['_logic']      = 'or';
				$where['_complex']        = $condition;
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->count();
						else $result = $model->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->count();
						else $result = $model->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->find();
						else $result = $model->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->find();
						else $result = $model->where($where)->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $model->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $model->limit($filter['_limit'])->where($where)->order($filter['_order'])->select();
						else $result = $model->where($where)->order($filter['_order'])->select();
					}
				break;
			}

			return $result;
		}

	}