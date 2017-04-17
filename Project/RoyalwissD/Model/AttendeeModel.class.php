<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-13
	 * Time: 15:10
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class AttendeeModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'attendee';
		const TABLE_NAME = 'attendee';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		/** 自定义字段的名称 */
		const CUSTOM_COLUMN = 'column';
		/** 报名方式 */
		const REGISTER_TYPE = [
			0 => 'PC信息导入',
			1 => 'PC信息登记',
			2 => '微信信息登记',
			3 => '主动报名',
		];
		/** 审核状态 */
		const REVIEW_STATUS = [
			0 => '未审核',
			1 => '已审核',
			2 => '取消审核'
		];
		/** 签到状态 */
		const SIGN_STATUS = [
			0 => '未签到',
			1 => '已签到',
			2 => '取消签到'
		];
		/** 签到类型 */
		const SIGN_TYPE = [
			0 => '未签到',
			1 => '后台签到',
			2 => '微信自主签到',
			3 => '微信后台签到'
		];
		/** 打印状态 */
		const PRINT_STATUS = [
			0 => '未打印',
			1 => '已打印'
		];
		/** 打印状态 */
		const GIFT_STATUS = [
			0 => '未领取',
			1 => '已领取',
			2 => '已退还'
		];

		/**
		 * 创建参会记录
		 *
		 * @param array $data    参会记录信息
		 * @param bool  $replace 是否覆盖旧数据
		 *
		 * @return array
		 */
		public function create($data, $replace = false){
			try{
				$result = $replace ? $this->add($data, [], true) : $this->add($data);

				return $result ? ['status' => true, 'message' => '创建参会记录成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建参会记录失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 新增自定义字段
		 *
		 * @param array $add_column_data 字段数据<br>
		 *                               ['type'=>string, 'typeSize'=>int, 'comment'=>string]
		 *
		 * @return array
		 */
		public function addColumn($add_column_data){
			$table_attendee     = $this->tableName;
			$this_database = self::DATABASE_NAME;
			$last_column_index  = $this->getLastCustomColumnIndex();
			$type               = " $add_column_data[type]($add_column_data[typeSize]) ";
			$comment            = " $add_column_data[comment]";
			$last_column        = $last_column_index == 0 ? 'status' : self::CUSTOM_COLUMN.$last_column_index;
			$column             = self::CUSTOM_COLUMN.(++$last_column_index);
			$sql                = "ALTER TABLE $this_database.$table_attendee
ADD COLUMN `$column` $type NULL COMMENT '$comment' AFTER `$last_column`";
			$result             = $this->execute($sql);
			if($result === false) return ['status' => false, 'message' => '创建失败'];
			else return ['status' => true, 'message' => '创建成功'];
		}

		/**
		 * 获取最后一个自定义字段的编号
		 *
		 * @return int
		 */
		public function getLastCustomColumnIndex(){
			$table_attendee     = $this->tableName;
			$this_database = self::DATABASE_NAME;
			$list               = $this->query("SELECT c.*
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_attendee'
AND c.COLUMN_NAME LIKE '".self::CUSTOM_COLUMN."%'
ORDER BY c.COLUMN_NAME DESC
LIMIT 1");
			if($list && $list[0]){
				$last_custom_column = $list[0]['column_name'];

				return preg_match('/'.self::CUSTOM_COLUMN.'(\d)+/', $last_custom_column, $result) ? $result[1] : 0;
			}
			else return 0;
		}

		/**
		 * 获取自定义字段列表
		 *
		 * @param bool $just_custom_column 是否只显示自定义字段
		 *
		 * @return array
		 */
		public function getColumnList($just_custom_column = false){
			$table_attendee     = $this->tableName;
			$table_grouping     = GroupingModel::TABLE_NAME;
			$table_room         = RoomModel::TABLE_NAME;
			$this_database = self::DATABASE_NAME;
			$sql_special = "
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
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_attendee'
AND COLUMN_NAME IN ('receivables', 'consumption')
";
			$sql_custom         = "
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	c.COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT,
	'custom' TYPE
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_attendee' AND COLUMN_NAME LIKE '".self::CUSTOM_COLUMN.'%\'';
			$sql_fixed          = "
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	'group_name' COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT,
	'fixed' TYPE
FROM information_schema.TABLES t
JOIN information_schema.COLUMNS c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_grouping'
AND c.COLUMN_NAME = 'name'
UNION
SELECT
	c.TABLE_SCHEMA,
	c.TABLE_NAME,
	'hotel_room_name' COLUMN_NAME,
	c.DATA_TYPE,
	c.CHARACTER_MAXIMUM_LENGTH,
	c.COLUMN_TYPE,
	c.COLUMN_COMMENT,
	'fixed' TYPE
FROM information_schema.TABLES t
JOIN information_schema.COLUMNS c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_room'
AND c.COLUMN_NAME = 'name'
UNION
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
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_attendee' AND COLUMN_NAME NOT LIKE '".self::CUSTOM_COLUMN.'%\'';
			$sql                = $just_custom_column ? "$sql_custom UNION $sql_special": "$sql_fixed UNION $sql_custom";
			$list               = $this->query($sql);

			return $list;
		}
	}