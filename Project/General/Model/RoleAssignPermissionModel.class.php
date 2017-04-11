<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-4
	 * Time: 9:53
	 */
	namespace General\Model;

	use Exception;

	class RoleAssignPermissionModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'role_assign_permission';
		const TABLE_NAME = 'role_assign_permission';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';

		/**
		 * 创建授权记录
		 *
		 * @param array $data 记录数据
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '授权成功', 'id' => $result] : [
					'status'  => false,
					'message' => '授权失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 删除授权记录
		 *
		 * @param array|string $condition 查询条件
		 *
		 * @return array 执行结果
		 */
		public function clean($condition){
			$result = $this->where($condition)->delete();

			return $result ? ['status' => true, 'message' => '取消授权成功'] : ['status' => false, 'message' => '取消授权失败'];
		}
	}