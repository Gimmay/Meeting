<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-16
	 * Time: 18:07
	 */
	namespace Core\Model;

	class PermissionModel extends CoreModel{
		protected $tableName   = 'permission';
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

		public function getPermissionOfEmployee($eid, $type = 'list', $not_assigned = false, $keyword = ''){
			$type   = strtolower($type);
			$result = [];
			if($not_assigned){
				$keyword     = "%$keyword%";
				$sql         = "select `id`, `code`, `name` from system_permission where id not in (
	select pid from system_assign_permission WHERE `type` = 1 and `oid` = $eid -- 员工权限
	UNION
	select pid from system_assign_permission where `type` = 0 and `oid` in ( -- 角色权限
		select rid from user_assign_role join system_role on system_role.id = user_assign_role.rid where `type` = 0 and `oid` = $eid and system_role.status != 2
	)
) and (system_permission.name like '$keyword' or system_permission.pinyin_code like '$keyword')";
				$permission = $this->query($sql);
			}
			else{
				$sql         = "select `id`, `code`, `name`, 1 `type` from system_permission where id in (
	select pid from system_assign_permission WHERE `type` = 1 and `oid` = $eid -- 员工权限
)
union
select `id`, `code`, `name`, 0 `type`
from system_permission where id in (
	select pid from system_assign_permission where `type` = 0 and `oid` in ( -- 角色权限
		select rid from user_assign_role join system_role on system_role.id = user_assign_role.rid where `type` = 0 and `oid` = $eid and system_role.status != 2
	)
)";
				$permission = $this->query($sql);
			}
			if($type == 'arr' || $type == 'str'){
				foreach($permission as $val) array_push($result, $val['id']);
				if($type == 'str') $result = implode(',', $result);
			}elseif($type == 'code'){
				foreach($permission as $val) array_push($result, $val['code']);
			}else $result = $permission;

			return $result;
		}

		public function getPermissionOfRole($rid, $type = 'list', $not_assigned = false, $keyword = ''){
			$type   = strtolower($type);
			$result = [];
			if($not_assigned){
				$keyword     = "%$keyword%";
				$sql         = "SELECT name, id FROM system_permission
WHERE id NOT IN (
	SELECT pid FROM system_assign_permission where oid = $rid and type = 0
) and (system_permission.pinyin_code like '$keyword' or system_permission.name like '$keyword');";
				$permission = $this->query($sql);
			}
			else{
				$sql         = "SELECT name, id FROM system_permission
WHERE id IN (
	SELECT pid FROM system_assign_permission where oid = $rid and type = 0
);";
				$permission = $this->query($sql);
			}
			if($type == 'arr' || $type == 'str'){
				foreach($permission as $val) array_push($result, $val['id']);
				if($type == 'str') $result = implode(',', $result);
			}
			else $result = $permission;

			return $result;
		}
	}