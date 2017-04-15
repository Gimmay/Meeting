<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 14:30
	 */
	namespace General\Model;

	use CMS\Logic\Session;
	use Exception;
	use General\Logic\Time;
	use General\Model\RoleModel as GeneralRoleModel;

	class RoleModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'role';
		const TABLE_NAME = 'role';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';
		/** 角色的最高等级 */
		const HIGHEST_LEVEL = '1';
		/** 角色的最低等级 */
		const LOWEST_LEVEL = '10';
		/** 角色等级的判定规则 */
		const LEVEL_ROLE = '>=';

		/**
		 * 修改信息
		 *
		 * @param array $filter      过滤条件
		 * @param array $information 修改信息
		 *
		 * @return array
		 */
		public function modifyInformation($filter, $information){
			try{
				$result = $this->where($filter)->save($information);

				return $result ? ['status' => true, 'message' => '修改成功'] : [
					'status'  => false,
					'message' => '修改失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 获取在该角色下的用户
		 *
		 * @param int  $role_id  角色ID
		 * @param bool $assigned 是否输出分配的用户
		 *
		 * @return array
		 */
		public function getUser($role_id, $assigned = true){
			$table_role             = $this->tableName;
			$table_user_assign_role = UserAssignRoleModel::TABLE_NAME;
			$table_user             = UserModel::TABLE_NAME;
			$common_database        = GeneralModel::DATABASE_NAME;
			$sql                    = $assigned ? "
SELECT u.* FROM $common_database.$table_role r
JOIN $common_database.$table_user_assign_role uar ON uar.rid = r.id AND uar.status = 1
JOIN $common_database.$table_user u ON uar.uid = u.id AND u.status = 1
WHERE u.id = $role_id
" : "
SELECT * FROM $common_database.$table_user
WHERE id NOT IN (
	SELECT u.id FROM $common_database.$table_role r
	JOIN $common_database.$table_user_assign_role uar ON uar.rid = r.id AND uar.status = 1
	JOIN $common_database.$table_user u ON uar.uid = u.id AND u.status = 1
	WHERE u.id = $role_id
)";

			return $this->query($sql);
		}

		/**
		 * 获取授予该角色的权限
		 *
		 * @param int    $role_id  角色ID
		 * @param string $keyword  检索关键字
		 * @param bool   $assigned 是否输出授权的权限
		 *
		 * @return array
		 */
		public function getPermission($role_id, $keyword = '', $assigned = true){
			$table_role                   = $this->tableName;
			$table_role_assign_permission = RoleAssignPermissionModel::TABLE_NAME;
			$table_permission             = PermissionModel::TABLE_NAME;
			$common_database              = GeneralModel::DATABASE_NAME;
			$keyword                      = $keyword == '' ? '' : " AND (
				p.name like '%$keyword%'
				OR p.name_pinyin like '%$keyword%'
				OR p.module like '%$keyword%'
				OR p.module_pinyin like '%$keyword%'
			)";
			$sql                          = $assigned ? "
SELECT p.*, LEFT(code, LOCATE('.', code)-1) module_code
FROM $common_database.$table_role r
JOIN $common_database.$table_role_assign_permission rap on rap.rid = r.id AND rap.status = 1
JOIN $common_database.$table_permission p on p.id = rap.pid
WHERE r.id = $role_id
$keyword
" : "
SELECT p.*, LEFT(code, LOCATE('.', code)-1) module_code FROM $common_database.$table_permission p
WHERE p.id NOT IN (
	SELECT p1.id FROM $common_database.role r
	JOIN $common_database.$table_role_assign_permission rap ON rap.rid = r.id AND rap.status = 1
	JOIN $common_database.$table_permission p1 ON p1.id = rap.pid
	WHERE r.id = $role_id
)
$keyword
";

			return $this->query($sql);
		}

		/**
		 * 创建角色
		 *
		 * @param array $data 角色数据
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建角色成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建角色失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 授权
		 *
		 * @param int|string|array $permission 权限ID数据
		 * @param int              $role_id    角色ID
		 *
		 * @return array 执行结果
		 */
		public function grantPermission($permission, $role_id){
			if(is_numeric($permission) || is_string($permission)){ // 逐项授权
				/** @var \General\Model\RoleAssignPermissionModel $role_assign_permission_model */
				$role_assign_permission_model = D('General/RoleAssignPermission');
				$data                         = [
					'pid'      => $permission,
					'rid'      => $role_id,
					'creatime' => Time::getCurrentTime(),
					'creator'  => Session::getCurrentUser(),
					'type'     => 1
				];
				$result                       = $role_assign_permission_model->create($data); // 授权
				return $result;
			}
			elseif(is_array($permission)){ // 批量授权
				/** @var \General\Model\RoleAssignPermissionModel $role_assign_permission_model */
				$role_assign_permission_model = D('General/RoleAssignPermission');
				$exist_record_count           = $role_assign_permission_model->tally([
					'pid'    => ['in', $permission],
					'rid'    => $role_id,
					'status' => 1
				]); // Warning: 先查找有否重复数据
				if($exist_record_count>=count($permission)) return ['status' => false, 'message' => '已授权无需重复授权'];
				if($exist_record_count>0 && $exist_record_count<count($permission)){
					$result = $role_assign_permission_model->clean([
						'pid' => ['in', $permission],
						'rid' => $role_id
					]); // Warning: 先删除原有数据 防止批量插入重复数据导致唯一约束报错
					if(!$result['status']) return ['status' => false, 'message' => '授权失败'];
				}
				$data = [];
				foreach($permission as $permission_id){
					$data[] = [
						'pid'      => $permission_id,
						'rid'      => $role_id,
						'creatime' => Time::getCurrentTime(),
						'creator'  => Session::getCurrentUser(),
						'type'     => 1
					];
				}
				$result = $role_assign_permission_model->addAll($data); // 授权
				return $result ? [
					'status'  => true,
					'message' => '授权成功'
				] : [
					'status'  => false,
					'message' => '授权失败'
				];
			}
			else return ['status' => false, 'message' => '参数错误'];
		}

		/**
		 * 收回权限
		 *
		 * @param int|string|array $permission 权限ID数据
		 * @param int              $role_id    角色ID
		 *
		 * @return array 执行结果
		 */
		public function revokePermission($permission, $role_id){
			if(is_numeric($permission) || is_string($permission)){ // 逐项收回授权
				$permission = [$permission];
			}
			elseif(is_array($permission) && count($permission)>0){ // 批量收回授权
			}
			else return ['status' => false, 'message' => '参数错误'];
			/** @var \General\Model\RoleAssignPermissionModel $role_assign_permission_model */
			$role_assign_permission_model = D('General/RoleAssignPermission');
			$result                       = $role_assign_permission_model->clean([
				'pid' => ['in', $permission],
				'rid' => $role_id
			]); // 收回授权
			return $result;
		}
	}