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

	class DepartmentModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $connection = 'DB_CONFIG_COMMON';
		protected $tableName  = 'department';
		const TABLE_NAME = 'department';
		protected $autoCheckFields = true;

		/**
		 * 创建部门
		 *
		 * @param array $data 部门数据
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建部门成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建部门失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 获取部门下的成员用户
		 * *注根部门为0
		 *
		 * @param int $department_id 部门ID
		 *
		 * @return array
		 */
		public function getUser($department_id = 0){
			$table_user       = UserModel::TABLE_NAME;
			$table_department = DepartmentModel::TABLE_NAME;
			$this_database    = self::DATABASE_NAME;
			$sql              = "
SELECT
	u.*
FROM $this_database.$table_user u
JOIN $this_database.$table_department d ON d.id = u.did
WHERE u.status <> 2 AND d.status <> 2 AND d.id = $department_id
";
			$result           = $this->query($sql);

			return $result;
		}
	}