<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-13
	 * Time: 17:06
	 */
	namespace General\Model;

	use Exception;

	class MeetingColumnControlModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'meeting_column_control';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';

		/**
		 * 创建会议字段记录
		 *
		 * @param array $data 记录信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建会议字段记录成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建会议字段记录失败'
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
		 * @param int $meeting_type 会议类型
		 *
		 * @return array
		 */
		public function getMeetingControlledColumn($meeting_type){
			return $this->where("mtype = $meeting_type")->select();
		}
	}