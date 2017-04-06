<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-4
	 * Time: 9:53
	 */
	namespace General\Model;

	use Exception;

	class RoleAssignPermissionLogModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'role_assign_permission_log';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';

		/**
		 * 创建角色分配权限记录日志
		 *
		 * @param array $data 日志数据
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '记录成功', 'id' => $result] : [
					'status'  => false,
					'message' => '记录失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
	}