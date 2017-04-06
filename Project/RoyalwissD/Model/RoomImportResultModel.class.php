<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-20
	 * Time: 15:01
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class RoomImportResultModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'room_import_result';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';

		/**
		 * 创建结果记录
		 *
		 * @param array $data 记录信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建结果记录成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建结果记录失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
	}