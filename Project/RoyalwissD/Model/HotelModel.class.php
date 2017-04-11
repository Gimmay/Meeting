<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-28
	 * Time: 15:01
	 */
	namespace RoyalwissD\Model;

	use Exception;
	use General\Model\GeneralModel;
	use General\Model\UserModel;

	class HotelModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'hotel';
		const TABLE_NAME = 'hotel';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid'
		];

		/**
		 * 创建酒店
		 *
		 * @param array $data 酒店信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建酒店成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建酒店失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		public function getList($control = []){
			$table_hotel     = $this->tableName;
			$table_user      = UserModel::TABLE_NAME;
			$common_database = GeneralModel::DATABASE_NAME;
			$this_database = self::DATABASE_NAME;
			$keyword       = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order         = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status        = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id    = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$where         = ' WHERE 0 = 0 ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				and (
					name like '%$keyword%'
					or name_pinyin like '%$keyword%'
				)";
			}
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			$sql    = "
SELECT * FROM (
	SELECT
		h.id,
		h.mid,
		h.name,
		h.name_pinyin,
		h.level,
		h.type,
		h.address,
		h.contact,
		h.brief,
		h.status,
		h.comment,
		h.creator creator_code,
		h.creatime,
		u1.name creator
	FROM $this_database.$table_hotel h
	LEFT JOIN $common_database.$table_user u1 ON u1.id = h.creator AND u1.status <> 2
) tab
$where
$order";
			$result = $this->query($sql);

			return $result;
		}

		/**
		 * 获取Select插件的数据列表
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getSelectedList($meeting_id){
			$result = $this->where([
				'status' => 1,
				'mid'    => $meeting_id
			])->field('id value, name html, concat(name,\',\',name_pinyin) keyword')->select();

			return $result;
		}
	}