<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-14
	 * Time: 16:55
	 */
	namespace Core\Model;

	class TDOAUserModel extends CoreModel{
		protected $connection = "mysql://quasar:t2artworks@192.168.0.77:3336/td_oa#utf8";
		protected $tableName  = 'user';

		public function _initialize(){
			parent::_initialize();
		}

		public function findUser($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['UID'] = $filter['id'];
			if(isset($filter['status'])) $where['NOT_LOGIN'] = $filter['status'];
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['MOBIL_NO']  = ['like', "%$filter[keyword]%"];
				$condition['BYNAME']    = ['like', "%$filter[keyword]%"];
				$condition['USER_NAME'] = ['like', "%$filter[keyword]%"];
				$condition['_logic']    = 'or';
				$where['_complex']      = $condition;
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
					if(!isset($filter['_order'])) $filter['_order'] = 'uid desc';
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

		public function getTableColumn(){
			return $this->query('show columns from td_oa.user');
		}
	}