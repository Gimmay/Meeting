<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-11
	 * Time: 17:08
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class UnitModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'unit';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = ['area' => 'area', 'meetingID' => 'mid'];
		/** 是否新店 */
		const IS_NEW = [
			0 => '否',
			1 => '是'
		];

		public function getList($control = []){
			$keyword    = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order      = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status     = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$area       = $control[self::CONTROL_COLUMN_PARAMETER_SELF['area']];
			$meeting_id = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$where      = ' WHERE 0 = 0 ';
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
	SELECT c1.unit FROM meeting_royalwiss_deal.client c1
	JOIN meeting_royalwiss_deal.attendee a1 ON a1.cid = c1.id
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
		u2.name creator
	FROM meeting_royalwiss_deal.unit u1
	LEFT JOIN meeting_common.user u2 ON u2.id = u1.creator AND u1.status <> 2
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
				$result = $this->add($data);

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
	}