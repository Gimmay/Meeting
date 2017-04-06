<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-10
	 * Time: 15:03
	 */
	namespace General\Model;

	use Exception;

	class MeetingManagerModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'meeting_manager';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';

		/**
		 * 创建会议管理者
		 *
		 * @param array $data 记录数据
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '分配成功', 'id' => $result] : [
					'status'  => false,
					'message' => '分配失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
		
		
	}