<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 14:30
	 */
	namespace General\Model;

	use Exception;

	class MeetingModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'meeting';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';
		/** 流程状态 */
		const PROCESS_STATUS = [
			1 => '新建',
			2 => '发布',
			3 => '进行中',
			4 => '结束'
		];

		/**
		 * 创建会议
		 *
		 * @param array $data 会议数据
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建会议成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建会议失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 获取字段列表
		 *
		 * @return array
		 */
		public function getColumnList(){
			$list = $this->query("
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	c.COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT,
	'fixed' TYPE
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = 'meeting_common'
AND t.TABLE_NAME = 'meeting'
");

			return $list;
		}
	}