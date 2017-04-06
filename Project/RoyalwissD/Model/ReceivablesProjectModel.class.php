<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-15
	 * Time: 10:49
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class ReceivablesProjectModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'receivables_project';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		/** 项目类型 */
		const TYPE = [
			1 => '项目',
			2 => '代金券'
		];

		/**
		 * 创建收款项目数据
		 *
		 * @param array $data 收款项目数据
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建收款项目数据成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建收款项目数据失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
	}