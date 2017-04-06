<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-31
	 * Time: 17:35
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class MessageConfigureModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'message_configure';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid',
			'type'      => 'type'
		];
		/** 消息关联操作 */
		const ACTION = [
			1 => '邀约信息',
			2 => '签到提示',
			3 => '取消签到提示'
		];

		/**
		 * 创建消息关联记录
		 *
		 * @param array $data 消息关联信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '消息关联成功', 'id' => $result] : [
					'status'  => false,
					'message' => '消息关联失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
	}