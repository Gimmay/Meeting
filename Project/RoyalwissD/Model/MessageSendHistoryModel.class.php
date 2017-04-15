<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-2
	 * Time: 11:36
	 */
	namespace RoyalwissD\Model;

	use Exception;
	use General\Model\GeneralModel;
	use General\Model\UserModel;

	class MessageSendHistoryModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'message_send_history';
		const TABLE_NAME = 'message_send_history';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid',
			'action'    => 'action',
			'type'      => 'type',
			'messageID' => 'message_id'
		];

		/**
		 * 创建消息发送记录
		 *
		 * @param array $data 消息发送记录信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '保存消息发送记录成功', 'id' => $result] : [
					'status'  => false,
					'message' => '保存消息发送记录失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		public function getList($control = []){
			$table_message_send_history = $this->tableName;
			$table_user                 = UserModel::TABLE_NAME;
			$table_client = ClientModel::TABLE_NAME;
			$table_message              = MessageModel::TABLE_NAME;
			$common_database            = GeneralModel::DATABASE_NAME;
			$this_database              = self::DATABASE_NAME;
			$keyword                    = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order                      = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status                     = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id                 = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$action                     = $control[self::CONTROL_COLUMN_PARAMETER_SELF['action']];
			$message_id                 = $control[self::CONTROL_COLUMN_PARAMETER_SELF['messageID']];
			$type                       = $control[self::CONTROL_COLUMN_PARAMETER_SELF['type']];
			$where                      = ' WHERE 0 = 0 ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				and (
					message like '%$keyword%'
					context like '%$keyword%'
					client like '%$keyword%'
					client_pinyin like '%$keyword%'
				)";
			}
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($message_id) && isset($message_id[0]) && isset($message_id[1])) $where .= " and status $message_id[0] $message_id[1] ";
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			if(isset($action)) $where .= " and action = $action ";
			if(isset($type) && isset($type[0]) && isset($type[1])) $where .= " and type $type[0] $type[1] ";
			$sql    = "
SELECT * FROM (
	SELECT
		msh.id,
		msh.mid,
		m.id message_code,
		m.name message,
		m.name message_pinyin,
		msh.context,
		msh.type,
		msh.action,
		msh.sms_id,
		msh.send_status,
		msh.status,
		msh.creator creator_code,
		msh.creatime,
		c.id client_code,
		c.name client,
		c.name_pinyin client_pinyin,
		u1.name creator
	FROM $this_database.$table_message_send_history msh
	LEFT JOIN $common_database.$table_user u1 ON u1.id = msh.creator AND u1.status <> 2
	JOIN $this_database.$table_client c ON c.id = msh.cid AND c.status <> 2
	JOIN $this_database.$table_message m ON m.id = msh.message_id AND m.status <> 2
) tab
$where
$order";
			$result = $this->query($sql);

			return $result;
		}
	}