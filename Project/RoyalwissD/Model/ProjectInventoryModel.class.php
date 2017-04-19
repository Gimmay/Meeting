<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-15
	 * Time: 10:43
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class ProjectInventoryModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'project_inventory';
		const TABLE_NAME = 'project_inventory';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const TYPE       = [
			0 => '出库',
			1 => '入库',
			2 => '清空'
		];
		const TYPE_IN    = 1;
		const TYPE_OUT   = 0;
		const TYPE_CLEAN = 2;

		/**
		 * 创建项目出入库明细表
		 *
		 * @param array $data 项目出入库明细数据
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建出入库明细数据成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建出入库明细数据失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
	}