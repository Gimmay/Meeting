<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-16
	 * Time: 18:07
	 */
	namespace Core\Model;

	class PermissionModel extends CoreModel{
		protected $tableName = 'permission';
		protected $tablePrefix = 'system_';
		
		public function _initialize(){
			parent::_initialize();
		}
		public function findPermission($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id']) && $filter['id']) $where['id'] = $filter['id'];
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

		public function getPermissionOfEmployee($eid = null, $type = 'list', $not_assigned = false, $keyword = ''){
			if($eid == null) $eid = I('session.MANAGER_USER_ID', 0, 'int');
			$type   = strtolower($type);
			$result = [];
			if($not_assigned){
				$keyword     = "%$keyword%";
				$sql         = "select id, name from system_permission where id not in (
	select pid from system_assign_permission WHERE type = 1 and oid = $eid -- 员工权限
	UNION
	select pid from system_assign_permission where type = 0 and oid in ( -- 角色权限
		select rid from user_assign_role where type = 0 and oid = $eid
	)
) and (system_permission.name like '$keyword' or system_permission.pinyin_code like '$keyword')";
				$action_list = $this->query($sql);
			}
			else{
				$sql         = "select `id`, `name`, 1 `type` from system_permission where id in (
	select pid from system_assign_permission WHERE type = 1 and oid = $eid -- 员工权限
)
union
select `id`, `name`, 0 `type` from system_permission where id in (
	select pid from system_assign_permission where type = 0 and oid in ( -- 角色权限
		select rid from user_assign_role where type = 0 and oid = $eid
	)
)";
				$action_list = $this->query($sql);
			}
			if($type == 'arr' || $type == 'str'){
				foreach($action_list as $val) array_push($result, $val['id']);
				if($type == 'str') $result = implode(',', $result);
			}
			else $result = $action_list;

			return $result;
		}
	}