<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-11
	 * Time: 17:08
	 */
	namespace RoyalwissD\Model;

	use Exception;
	use General\Model\GeneralModel;
	use General\Model\UserModel;

	class UnitModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'unit';
		const TABLE_NAME = 'unit';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = ['area' => 'area', 'meetingID' => 'mid'];
		/** 是否新店 */
		const IS_NEW = [
			0 => '否',
			1 => '是'
		];
		/** 是否签到 */
		const IS_SIGNED = [
			0 => '未到',
			1 => '已到'
		];

		public function getList($control = []){
			$table_unit      = $this->tableName;
			$table_user      = UserModel::TABLE_NAME;
			$table_client    = ClientModel::TABLE_NAME;
			$table_attendee  = AttendeeModel::TABLE_NAME;
			$common_database = GeneralModel::DATABASE_NAME;
			$this_database   = self::DATABASE_NAME;
			$keyword         = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order           = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status          = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$area            = $control[self::CONTROL_COLUMN_PARAMETER_SELF['area']];
			$meeting_id      = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$where           = ' WHERE 0 = 0 ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				AND (
					name like '%$keyword%'
					OR name_pinyin like '%$keyword%'
				)";
			}
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($meeting_id)) $where .= " AND name IN (
	SELECT c1.unit FROM $this_database.$table_client c1
	JOIN $this_database.$table_attendee a1 ON a1.cid = c1.id
	WHERE c1.status = 1 AND a1.status = 1 AND a1.mid = $meeting_id AND a1.review_status = 1
) ";
			if(isset($area) && isset($area[0]) && isset($area[1])) $where .= " and area $area[0] $area[1] ";
			$sql = "
SELECT * FROM (
	SELECT
		u1.id,
		u1.name,
		u1.name_pinyin,
		u1.address,
		u1.area,
		u1.is_new,
		u1.comment,
		u1.status,
		u1.creator creator_code,
		u1.creatime,
		u2.name creator
	FROM $this_database.$table_unit u1
	LEFT JOIN $common_database.$table_user u2 ON u2.id = u1.creator AND u1.status <> 2
) tab
$where
$order
";

			return $this->query($sql);
		}

		/**
		 * 创建会所
		 *
		 * @param array $data 会所信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data, [], true);

				return $result ? ['status' => true, 'message' => '创建会所成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建会所失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 获取区域Select插件的数据列表（审核的客户所在的会所）
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getUnitSelectedArea($meeting_id){
			$table_unit     = $this->tableName;
			$table_attendee = AttendeeModel::TABLE_NAME;
			$table_client   = ClientModel::TABLE_NAME;
			$this_database  = self::DATABASE_NAME;
			$sql            = "
SELECT
	DISTINCT u.area value,
	u.area html,
	concat(u.area) keyword
FROM $this_database.$table_unit u
WHERE u.name in (
	SELECT
		c.unit
	FROM $this_database.$table_client c
	JOIN $this_database.$table_attendee a ON c.id = a.cid
	AND a.mid = $meeting_id
	WHERE a.status = 1 AND a.review_status = 1
)
GROUP BY u.area
";
			$result         = $this->query($sql);

			return $result;
		}
	}