<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:55
	 */
	namespace Core\Model;

	use Exception;

	class MeetingModel extends CoreModel{
		protected $tableName   = 'meeting';
		protected $tablePrefix = 'workflow_';

		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 查询会议记录
		 *
		 * @param int   $type   操作类型 0表示获取记录数 1表示获取单条记录 2表示获取结果集
		 * @param array $filter 过滤条件 可传入的数组索引值包括 id, status, keyword, _limit, _order
		 *
		 * @return int|array
		 */
		public function findMeeting($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 4];
				else $where['status'] = $filter['status'];
			}
			if(isset($filter['keyword']) && $filter['keyword']){
				$where['name'] = ['like', "%$filter[keyword]%"];
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

		public function createMeeting($data){
			if($this->create($data)){
				$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
				$data['creatime'] = time();
				if(I('post.city')){
					$data['place'] = I('post.province')."-".I('post.city')."-".I('post.area')."-".I('post.address_detail');
				}else{
					$data['place'] = I('post.province')."-".I('post.area')."-".I('post.address.detail');
				}


				$result           = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建成功'] : [
					'status'  => false,
					'message' => $this->getError()
				];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function alterMeeting($id, $data){
			if($this->create($data)){
				if(I('post.city')){
					$data['place'] = I('post.province')."-".I('post.city')."-".I('post.area')."-".I('post.address_detail');
				}else{
					$data['place'] = I('post.province')."-".I('post.area')."-".I('post.address.detail');
				}
				$result = $this->where(['id' => $id])->save($data);

				return $result ? ['status' => true, 'message' => '修改成功'] : [
					'status'  => false,
					'message' => $this->getError()
				];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function deleteMeeting($ids){
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