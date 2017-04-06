<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-18
	 * Time: 15:13
	 */
	namespace General\Model;

	use Exception;

	class UploadLogModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $connection      = 'DB_CONFIG_COMMON';
		protected $tableName       = 'upload_log';
		protected $autoCheckFields = true;

		/**
		 * 创建上传日志
		 *
		 * @param array $data 上传日志数据
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建上传日志成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建上传日志'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
	}