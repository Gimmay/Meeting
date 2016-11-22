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
		protected $tableName       = 'employee';
		protected $tablePrefix     = 'user_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function findEmployee($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['did'])) $where['did'] = $filter['did'];
			if(isset($filter['name'])) $where['name'] = $filter['name'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
			}
			if(isset($filter['code'])) $where['code'] = $filter['code'];
			if(isset($filter['mobile'])) $where['mobile'] = $filter['mobile'];
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

		public function listEmployee($type = 2, $filter = []){ // 与角色表做连表查询
			$where   = '0 = 0';
			$where_1 = '';
			$limit   = '';
			$order   = '';
			$field   = '*';
			if(isset($filter['id'])) $where .= " and id = $filter[id]";
			if(isset($filter['did'])) $where .= " and did = $filter[did]";
			if(isset($filter['rid'])) $where_1 .= " and system_role.id = $filter[rid]";
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where .= " and status != 2";
				else $where .= " and status = $filter[status]";
			}
			if(isset($filter['keyword']) && $filter['keyword']) $where .= " and (code like '%$filter[keyword]%' or mobile like '%$filter[keyword]%'  or name like '%$filter[keyword]%'  or pinyin_code like '%$filter[keyword]%')";
			if(isset($filter['_limit'])) $limit = "limit $filter[_limit]";
			if(isset($filter['_order'])) $order = "order by $filter[_order]";
			if((int)$type == 0) $field = 'count(*) count';
			$sql = "SELECT
	$field
FROM(
	SELECT * FROM user_employee
	WHERE id IN (
		SELECT oid FROM user_assign_role
		join system_role on user_assign_role.rid = system_role.id
		WHERE oid = user_employee.id AND type = 0 $where_1
	)
) main_table
WHERE $where
$order
$limit
";
			switch((int)$type){
				case 0: // count
					$result = $this->query($sql);
					$result = $result[0]['count'];
				break;
				case 1: // find
					$result = $this->query($sql);
					$result = $result[0];
				break;
				case 2: // select
				default:
					$result = $this->query($sql);
				break;
			}

			return $result;
		}

		public function createEmployee($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '创建员工成功', 'id' => $result];
					else return ['status' => false, 'message' => '没有创建员工'];
				}catch(Exception $error){
					$message = $error->getMessage();
					if(stripos($message, 'Duplicate entry')) return [
						'status'  => false,
						'message' => "工号[$data[code]]或手机号[$data[mobile]]已存在"
					];
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $message];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function deleteEmployee($id){
			if($this->create()){
				try{
					$result = $this->where(['id' => ['in', $id]])->save(['status' => 2]);
					if($result) return ['status' => true, 'message' => '删除成功'];
					else return ['status' => false, 'message' => '没有删除任何员工'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function alterEmployee($filter, $data){
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
	}