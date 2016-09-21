<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:54
	 */
	namespace Core\Model;

	use Exception;
	use Quasar\StringPlus;

	class ClientModel extends CoreModel{
		protected $tableName   = 'client';
		protected $tablePrefix = 'user_';

		public function _initialize(){
			parent::_initialize();
		}

		public function createClient($data){
			$str_obj = new StringPlus();
			if($this->create()){
				$data['status']      = $data['status'] == 1 ? 0 : (($data['status'] == 0) ? 1 : 1);
				$data['creatime']    = time();
				$data['creator']     = I('session.MANAGER_USER_ID', 0, 'int');
				$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
				$data['password']    = $str_obj->makePassword($data['mobile'], '123456');
				$data['birthday']    = date('Y-m-d', strtotime($data['birthday']));
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '创建客户成功', 'id' => $result];
					else return ['status' => false, 'message' => $this->getError()];
				}catch(Exception $error){
					$message = $error->getMessage();
					if(stripos($message, 'Duplicate entry')) return [
						'status'  => false,
						'message' => "$data[code]手机号已存在"
					];
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findClient($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['status'])) $where['status'] = $filter['status'];
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
					if(!isset($filter['order'])) $filter['order'] = 'id desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->order($filter['order'])->select();
						else $result = $this->order($filter['order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->where($where)->order($filter['order'])->select();
						else $result = $this->where($where)->order($filter['order'])->select();
					}
				break;
			}

			return $result;
		}

	}