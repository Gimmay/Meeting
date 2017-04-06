<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-1
	 * Time: 10:58
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class MessageCorrelationModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'message_correlation';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid',
			'action'    => 'action'
		];
		/** 消息分配的动作 */
		const ACTION = [
			1 => '邀约',
			2 => '签到',
			3 => '取消签到'
		];

		/**
		 * 保存消息使用记录
		 *
		 * @param array $data 消息使用记录信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '保存消息使用记录成功', 'id' => $result] : [
					'status'  => false,
					'message' => '保存消息使用记录失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
	}