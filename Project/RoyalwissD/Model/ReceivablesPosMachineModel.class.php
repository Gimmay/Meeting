<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-16
	 * Time: 9:42
	 */
	namespace RoyalwissD\Model;

	use Exception;
	use General\Model\GeneralModel;
	use General\Model\UserModel;

	class ReceivablesPosMachineModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'receivables_pos_machine';
		const TABLE_NAME = 'receivables_pos_machine';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = ['meetingID' => 'mid'];

		/**
		 * 创建POS机
		 *
		 * @param array $data POS机信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建POS机成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建POS机失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		public function getList($control = []){
			$table_user        = UserModel::TABLE_NAME;
			$table_pos_machine = self::TABLE_NAME;
			$common_database   = GeneralModel::DATABASE_NAME;
			$this_database     = self::DATABASE_NAME;
			$keyword           = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order             = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status            = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id        = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$where             = ' WHERE 0 = 0 ';
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
		rpm.id,
		rpm.mid,
		rpm.name,
		rpm.name_pinyin,
		rpm.status,
		rpm.comment,
		rpm.creatime,
		rpm.creator creator_code,
		u1.name creator
	FROM $this_database.$table_pos_machine rpm
	LEFT JOIN $common_database.$table_user u1 ON u1.id = rpm.creator AND u1.status <> 2
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
			return $this->where([
				'status' => 1,
				'mid'    => $meeting_id
			])->field('id value, name html, concat(name,\',\',name_pinyin) keyword')->select();
		}
	}