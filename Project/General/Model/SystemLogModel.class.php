<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 14:54
	 */
	namespace General\Model;

	use Exception;

	class SystemLogModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'system_log';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';
		const ACTION = [
			'MODIFY_PASSWORD_BY_SELF' => '自行修改密码',
			'MODIFY_PASSWORD'         => '修改密码',
			'RESET_PASSWORD'          => '重置密码'
		];

		/**
		 * 记录日志
		 *
		 * @param array $data 日志数据
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建日志成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建日志失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
	}