<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:50
	 */
	namespace Core\Model;

	use Quasar\StringPlus;
	use Think\Exception;

	class EmployeeModel extends CoreModel{
		protected $tableName   = 'employee';
		protected $tablePrefix = 'user_';

		public function _initialize(){
			parent::_initialize();
		}

		public function isLogin(){
			return isset($_SESSION['MANAGER_USER_ID']) && session('MANAGER_USER_ID') ? true : false;
		}

		public function checkLogin($name, $pwd){
			$str_obj = new StringPlus();
			$pwd     = $str_obj->makePassword($pwd, $name);
			if($this->create()){
				$user = $this->where([
					'code'     => $name,
					'password' => $pwd
				])->find();
				if($user){
					session('MANAGER_USER_ID', $user['id']);
					session('MANAGER_USER_CODE', $user['code']);

					return ['status' => true, 'message' => '登入成功'];
				}
				else return ['status' => false, 'message' => '该用户不存在或用户名/密码错误'];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findEmployee($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['status'])) $where['status'] = $filter['status'];
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['code']        = ['like', "%$filter[keyword]%"];
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
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->order($filter['order'])->where($where)->select();
						else $result = $this->order($filter['order'])->where($where)->select();
					}
				break;
			}

			return $result;
		}

		public function createEmployee($data){
			$str_obj = new StringPlus();
			if($this->create()){
				$data['status']      = $data['status'] == 1 ? 0 : (($data['status'] == 0) ? 1 : 1);
				$data['creatime']    = time();
				$data['creator']     = I('session.MANAGER_USER_ID', 0, 'int');
				$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
				$data['password']    = $str_obj->makePassword($data['password'], $data['code']);
				$data['birthday']    = date('Y-m-d', strtotime($data['birthday']));
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '创建员工成功', 'id' => $result];
					else return ['status' => false, 'message' => $this->getError()];
				}catch(Exception $error){
					$message = $error->getMessage();
					if(stripos($message, 'Duplicate entry')) return [
						'status'  => false,
						'message' => "$data[code]工号已存在"
					];
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function deleteEmployee($ids){
			if($this->create()){
				$where['id'] = ['in', $ids];
				$result      = $this->where($where)->save(['status' => 0]);
				if($result) return ['status' => true, 'message' => '删除成功'];
				else return ['status' => false, 'message' => $this->getError()];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function alterPassword($old_pwd, $new_pwd){
			$str_obj = new StringPlus();
			$code    = I('session.MANAGER_USER_CODE', 0);
			$old_pwd = $str_obj->makePassword($old_pwd, $code);
			$new_pwd = $str_obj->makePassword($new_pwd, $code);
			if($this->create()){
				$result = $this->where(['code' => $code, 'password' => $old_pwd])->find();
				if($result){
					$result = $this->where(['code' => $code, 'password' => $old_pwd])->save(['password' => $new_pwd]);
					if($result) return ['status' => true, 'message' => '修改密码成功'];
					else return ['status' => false, 'message' => '密码未做修改'];
				}
				else return ['status' => false, 'message' => '该用户不存在或用户名/密码错误'];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function getPositionSelectList(){
			return $this->field("position as keyword, position as value, position as html")->select();
		}

		public function getTitleSelectList(){
			return $this->field("position as keyword, position as value, position as html")->select();
		}
	}