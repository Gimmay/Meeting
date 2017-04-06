<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-16
	 * Time: 11:51
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class ClientColumnControlModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'client_column_control';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		/** 操作-列表/读 */
		const ACTION_READ = 0;
		/** 操作-创建/写 */
		const ACTION_WRITE = 1;

		/**
		 * 创建客户字段记录
		 *
		 * @param array $data 记录信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建客户字段记录成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建客户字段记录失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 获取控制字段的信息
		 *
		 * @param int $meeting_id 会议类型
		 * @param int $action     操作 0：列表/读 1：创建/写
		 *
		 * @return array
		 */
		public function getClientControlledColumn($meeting_id, $action){
			if($action == 1 || $action) $action = self::ACTION_WRITE;
			else $action = self::ACTION_READ;

			return $this->where("mid = $meeting_id and action = $action")->select();
		}
	}