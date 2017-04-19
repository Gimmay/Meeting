<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-13
	 * Time: 9:36
	 */
	namespace RoyalwissD\Model;

	use Exception;
	use General\Model\UserModel;

	class ReceivablesColumnControlModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'receivables_column_control';
		const TABLE_NAME = 'receivables_column_control';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid'
		];
		/** 操作-读 */
		const ACTION_READ = 0;
		/** 操作-搜索 */
		const ACTION_SEARCH = 1;

		/**
		 * 创建收款字段控制记录
		 *
		 * @param array $data 收款控制字段数据
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建收款控制字段成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建收款控制字段失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 获取收款控制字段的信息
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getControlledColumn($meeting_id){
			$result = $this->where([
				'mid'    => $meeting_id,
				'action' => self::ACTION_READ
			])->select();

			return $result;
		}

		/**
		 * 获取可检索的收款控制字段
		 *
		 * @param int  $meeting_id    会议ID
		 * @param bool $just_selected 只输出被选中的检索字段
		 *
		 * @return array
		 */
		public function getSearchColumn($meeting_id, $just_selected = false){
			$option = [];
			if($just_selected) $option['search'] = 1;
			$result = $this->where(array_merge($option, [
				'mid'    => $meeting_id,
				'action' => self::ACTION_SEARCH
			]))->select();

			return $result;
		}

		/**
		 * 获取收款数据库的字段
		 *
		 * @param int $type 操作类型
		 *
		 * @return array
		 */
		public function getDatabaseColumn($type){
			$this_database            = self::DATABASE_NAME;
			$table_receivables_order  = ReceivablesOrderModel::TABLE_NAME;
			$table_project            = ProjectModel::TABLE_NAME;
			$table_project_type       = ProjectTypeModel::TABLE_NAME;
			$table_pay_method         = ReceivablesPayMethodModel::TABLE_NAME;
			$table_pos_machine        = ReceivablesPosMachineModel::TABLE_NAME;
			$table_receivables_detail = ReceivablesDetailModel::TABLE_NAME;
			$table_client             = ClientModel::TABLE_NAME;
			$table_user              = UserModel::TABLE_NAME;
			switch($type){
				case self::ACTION_READ:
					$sql = "
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	c.COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_receivables_order'
AND COLUMN_NAME IN ('order_number', 'payee', 'place', 'time', 'review_status', 'status', 'creatime', 'creator', 'review_time', 'review_director')
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	'client' COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	'客户' COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_client'
AND COLUMN_NAME = 'name'
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	'unit' COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	'会所' COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_client'
AND COLUMN_NAME = 'unit'
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	'project' COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_project'
AND COLUMN_NAME = 'name'
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	'project_type' COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_project_type'
AND COLUMN_NAME = 'name'
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	'pay_method' COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_pay_method'
AND COLUMN_NAME = 'name'
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	'pos_machine' COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_pos_machine'
AND COLUMN_NAME = 'name'
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	c.COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_receivables_detail'
AND COLUMN_NAME in ('source', 'comment', 'price')
";
				break;
				case self::ACTION_SEARCH:
					$sql = "
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	c.COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_receivables_order'
AND COLUMN_NAME IN ('order_number', 'place')
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	c.COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_client'
AND COLUMN_NAME in ('unit', 'unit_pinyin')
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	replace(c.COLUMN_NAME, 'name', 'payee') COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	replace(c.COLUMN_COMMENT, '用户名', '收款人') COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_user'
AND COLUMN_NAME in ('name', 'name_pinyin')
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	replace(c.COLUMN_NAME, 'name', 'client') COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	replace(c.COLUMN_COMMENT, '姓名', '客户') COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_client'
AND COLUMN_NAME in ('name', 'name_pinyin')
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	replace(c.COLUMN_NAME, 'name', 'project') COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_project'
AND COLUMN_NAME in ('name', 'name_pinyin')
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	replace(c.COLUMN_NAME, 'name', 'project_type') COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_project_type'
AND COLUMN_NAME in ('name', 'name_pinyin')
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	replace(c.COLUMN_NAME, 'name', 'pay_method') COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_pay_method'
AND COLUMN_NAME in ('name', 'name_pinyin')
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	replace(c.COLUMN_NAME, 'name', 'pos_machine') COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_pos_machine'
AND COLUMN_NAME in ('name', 'name_pinyin')
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	c.COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_receivables_detail'
AND COLUMN_NAME in ('comment')
";
				break;
			};
			if(isset($sql)) return $this->query($sql);
			else return [];
		}
	}