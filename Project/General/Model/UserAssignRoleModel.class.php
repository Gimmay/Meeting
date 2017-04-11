<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 14:54
	 */
	namespace General\Model;

	use Exception;

	class UserAssignRoleModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'user_assign_role';
		const TABLE_NAME = 'user_assign_role';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';

		/**
		 * 创建角色分配记录
		 *
		 * @param array $data 记录数据
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '分配角色成功', 'id' => $result] : [
					'status'  => false,
					'message' => '分配角色失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 删除角色分配记录
		 *
		 * @param array|string $condition 查询条件
		 *
		 * @return array 执行结果
		 */
		public function clean($condition){
			$result = $this->where($condition)->delete();

			return $result ? ['status' => true, 'message' => '取消分配角色成功'] : ['status' => false, 'message' => '取消分配角色失败'];
		}
	}