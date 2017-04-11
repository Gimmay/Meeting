<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-15
	 * Time: 10:54
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class ReceivablesDetailModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'receivables_detail';
		const TABLE_NAME = 'receivables_detail';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		/** 收款来源 */
		const RECEIVABLES_SOURCE = [
			1 => '会前收款',
			2 => '会中收款',
			3 => '会后收款'
		];

		/**
		 * 创建收款明细记录
		 *
		 * @param array $data 收款明细数据
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建收款明细成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建收款明细失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 计算总金额
		 *
		 * @param int $meeting 会议ID
		 *
		 * @return mixed
		 */
		public function sumPrice($meeting){
			$table_receivables_detail  = $this->tableName;
			$table_receivables_project = ReceivablesProjectModel::TABLE_NAME;
			$table_receivables_order   = ReceivablesOrderModel::TABLE_NAME;
			$this_database             = self::DATABASE_NAME;
			$sql                       = "
SELECT sum(rd.price) price
FROM $this_database.$table_receivables_order ro
JOIN $this_database.$table_receivables_project rp ON ro.id = rp.oid
JOIN $this_database.$table_receivables_detail rd ON rp.id = rd.pid
WHERE ro.mid = $meeting AND ro.status = 1 AND rp.status = 1 AND rd.status = 1 AND ro.review_status = 1
";
			$result                    = $this->query($sql);

			return $result[0]['price'];
		}
	}