<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-13
	 * Time: 9:15
	 */
	namespace Core\Model;

	class DepartmentModel extends CoreModel{
		protected $tableName   = 'department';
		protected $tablePrefix = 'user_';

		public function _initialize(){
			parent::_initialize();
		}

		public function findDepartment($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['id'] = $filter['id'];
			if(isset($filter['status'])) $where['status'] = $filter['status'];
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

		public function getDepartmentSelectList(){
			$sql = "select main.name as keyword, main.id as value, concat(main.name, ' (', ifnull((select sub.name from user_department sub where sub.id = main.parent_id), '吉美集团'), ')') html from user_department main";

			return $this->query($sql);
		}
	}