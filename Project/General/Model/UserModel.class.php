<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-2
	 * Time: 15:23
	 */
	namespace General\Model;

	use CMS\Logic\Session;
	use Exception;
	use General\Logic\Time;
	use General\Logic\UserLogic;

	class UserModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $connection       = 'DB_CONFIG_COMMON';
		protected $tableName        = 'user';
		protected $autoCheckFields  = true;
		private   $_defaultPassword = '';

		/**
		 * 获取用户的授权信息
		 *
		 * @param int  $user_id  用户ID
		 * @param bool $assigned 是否输出授权的权限
		 *
		 * @return array
		 */
		public function getPermission($user_id = 0, $assigned = true){
			$sql = $assigned ? "
SELECT DISTINCT permission.id i, permission.*, LEFT(code, LOCATE('.', code)-1) module_code FROM USER
JOIN user_assign_role ON user_assign_role.uid = USER.id
JOIN role ON user_assign_role.rid = role.id AND role.STATUS = 1
JOIN role_assign_permission ON role_assign_permission.rid = role.id
JOIN permission ON role_assign_permission.pid = permission.id
WHERE USER.id = $user_id AND permission.code like 'GENERAL-%'" : "
SELECT DISTINCT permission.id i, permission.*, LEFT(code, LOCATE('.', code)-1) module_code FROM permission
WHERE id NOT IN (
	SELECT permission.id
	FROM USER
	JOIN user_assign_role ON user_assign_role.uid = USER.id
	JOIN role ON user_assign_role.rid = role.id AND role.STATUS = 1
	JOIN role_assign_permission ON role_assign_permission.rid = role.id
	JOIN permission ON role_assign_permission.pid = permission.id
	WHERE USER.id = $user_id AND permission.code like 'GENERAL-%'
)";

			return $this->query($sql);
		}

		/**
		 * 获取用户分配的角色
		 *
		 * @param int  $user_id  用户ID
		 * @param bool $assigned 是否输出分配的角色
		 *
		 * @return array
		 */
		public function getRole($user_id, $assigned = true){
			$sql = $assigned ? "
SELECT role.* FROM USER
JOIN user_assign_role ON user_assign_role.uid = USER .id
JOIN role ON user_assign_role.rid = role.id
AND role.STATUS = 1
WHERE USER.id = $user_id" : "
SELECT * FROM role
WHERE id NOT IN (
	SELECT role.id FROM USER
	JOIN user_assign_role ON user_assign_role.uid = USER .id
	JOIN role ON user_assign_role.rid = role.id AND role.STATUS = 1
	WHERE USER .id = $user_id
)";

			return $this->query($sql);
		}

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
		 * 修改密码
		 *
		 * @param array  $filter   筛选条件
		 * @param string $password 密码
		 * @param bool   $is_reset 是否是重制密码
		 *
		 * @return array
		 */
		public function modifyPassword($filter, $password, $is_reset = false){
			$user_logic = new UserLogic();
			$record     = $this->where($filter)->find();
			if($record){
				if($is_reset){
					$password       = $this->_defaultPassword;
					$success_prompt = '重置成功';
					$failure_prompt = '重置失败';
				}
				else{
					$success_prompt = '修改成功';
					$failure_prompt = '修改失败';
				}
				$result = $this->where($filter)->save(['password' => $user_logic->makePassword($password, $record['name'])]);

				return $result ? ['status' => true, 'message' => $success_prompt] : [
					'status'  => false,
					'message' => $failure_prompt
				];
			}
			else return ['status' => false, 'message' => '找不到该用户'];
		}

		/**
		 * 获取默认密码
		 *
		 * @return string
		 */
		public function getDefaultPassword(){
			return $this->_defaultPassword === '' ? '空' : $this->_defaultPassword;
		}

		/**
		 * 创建用户
		 *
		 * @param array $data 用户数据
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建用户成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建用户失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 分配角色
		 *
		 * @param int|array|string $role       角色ID数据
		 * @param int              $user_id    用户ID
		 * @param int              $meeting_id 会议ID
		 *
		 * @return array 执行结果
		 */
		public function assignRole($role, $user_id, $meeting_id = 0){
			if(is_numeric($role) || is_string($role)){ // 逐项分配角色
				/** @var \General\Model\UserAssignRoleModel $user_assign_role_model */
				$user_assign_role_model = D('General/UserAssignRole');
				/** @var \General\Model\UserAssignRoleLogModel $user_assign_role_log_model */
				$user_assign_role_log_model = D('General/UserAssignRoleLog');
				$data                       = [
					'rid'      => $role,
					'uid'      => $user_id,
					'creatime' => Time::getCurrentTime(),
					'creator'  => Session::getCurrentUser(),
					'type'     => 1,
					'mid'      => $meeting_id
				];
				$result                     = $user_assign_role_model->create($data); // 分配角色
				if($result['status']) $user_assign_role_log_model->create($data); // 分配角色日志
				return $result;
			}
			elseif(is_array($role)){ // 批量分配角色
				/** @var \General\Model\UserAssignRoleModel $user_assign_role_model */
				$user_assign_role_model = D('General/UserAssignRole');
				/** @var \General\Model\UserAssignRoleLogModel $user_assign_role_log_model */
				$user_assign_role_log_model = D('General/UserAssignRoleLog');
				$exist_record_count         = $user_assign_role_model->tally([
					'rid' => ['in', $role],
					'uid' => $user_id
				]);// Warning: 先查找有否重复数据
				if($exist_record_count>=count($role)) return ['status' => false, 'message' => '已分配角色无需重复分配'];
				if($exist_record_count>0 && $exist_record_count<count($role)){
					$result = $user_assign_role_model->clean([
						'rid' => ['in', $role],
						'uid' => $user_id
					]); // Warning: 先删除原有数据 防止批量插入重复数据导致唯一约束报错
					if(!$result['status']) return ['status' => false, 'message' => '分配角色失败'];
				}
				$data = [];
				foreach($role as $role_id){
					$data[] = [
						'rid'      => $role_id,
						'uid'      => $user_id,
						'creatime' => Time::getCurrentTime(),
						'creator'  => Session::getCurrentUser(),
						'type'     => 1,
						'mid'      => $meeting_id
					];
				}
				$result = $user_assign_role_model->addAll($data); // 分配角色
				if($result){
					$user_assign_role_log_model->addAll($data);

					return ['status' => true, 'message' => '分配角色成功'];
				} // 分配角色日志
				else return ['status' => false, 'message' => '分配角色失败'];
			}
			else return ['status' => false, 'message' => '参数错误'];
		}

		/**
		 * 取消分配角色
		 *
		 * @param int|array|string $role       角色ID数据
		 * @param int              $user_id    用户ID
		 * @param int              $meeting_id 会议ID
		 *
		 * @return array 执行结果
		 */
		public function cancelRole($role, $user_id, $meeting_id = 0){
			if(is_numeric($role) || is_string($role)){ // 逐项取消分配角色
				/** @var \General\Model\UserAssignRoleModel $user_assign_role_model */
				$user_assign_role_model = D('General/UserAssignRole');
				/** @var \General\Model\UserAssignRoleLogModel $user_assign_role_log_model */
				$user_assign_role_log_model = D('General/UserAssignRoleLog');
				$data                       = [
					'rid'      => $role,
					'uid'      => $user_id,
					'creatime' => Time::getCurrentTime(),
					'creator'  => Session::getCurrentUser(),
					'type'     => 0,
					'mid'      => $meeting_id
				];
				$user_assign_role_log_model->create($data); // 取消分配角色日志
				return $user_assign_role_model->clean(['rid' => $role, 'uid' => $user_id]); // 取消分配角色
			}
			elseif(is_array($role) && count($role)>0){ // 批量取消分配角色
				/** @var \General\Model\UserAssignRoleModel $user_assign_role_model */
				$user_assign_role_model = D('General/UserAssignRole');
				/** @var \General\Model\UserAssignRoleLogModel $user_assign_role_log_model */
				$user_assign_role_log_model = D('General/UserAssignRoleLog');
				$data                       = [];
				foreach($role as $role_id){
					$data[] = [
						'rid'      => $role_id,
						'uid'      => $user_id,
						'creatime' => Time::getCurrentTime(),
						'creator'  => Session::getCurrentUser(),
						'type'     => 0,
						'mid'      => $meeting_id
					];
				}
				$where  = [
					'rid' => ['in', [$role]],
					'uid' => $user_id
				];
				$result = $user_assign_role_model->clean($where); // 取消分配角色
				$user_assign_role_log_model->addAll($data); // 取消分配角色日志
				return $result ? ['status' => true, 'message' => '取消分配角色成功'] : [
					'status'  => false,
					'message' => '取消分配角色失败'
				];
			}
			else return ['status' => false, 'message' => '参数错误'];
		}

		/**
		 * 获取用户最高的角色等级
		 *
		 * @param $user_id
		 *
		 * @return string
		 */
		public function getUserHighestLevel($user_id){
			$sql    = "SELECT min(role.level) level FROM USER
JOIN user_assign_role ON user_assign_role.uid = user.id
JOIN role ON user_assign_role.rid = role.id
AND role.status = 1
WHERE USER.id = $user_id";
			$result = $this->query($sql);

			return (isset($result[0]['level']) && $result[0]['level']) ? $result[0]['level'] : RoleModel::LOWEST_LEVEL;
		}
	}