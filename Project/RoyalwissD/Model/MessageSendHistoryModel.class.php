<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-2
	 * Time: 11:36
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class MessageSendHistoryModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'message_send_history';
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

		public function getList($control){
			$keyword    = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order      = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status     = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$action     = $control[self::CONTROL_COLUMN_PARAMETER_SELF['action']];
			$message_id = $control[self::CONTROL_COLUMN_PARAMETER_SELF['messageID']];
			$type       = $control[self::CONTROL_COLUMN_PARAMETER_SELF['type']];
			$where      = ' WHERE 0 = 0 ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				and (
					message like '%$keyword%'
				)";
			}
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($message_id) && isset($message_id[0]) && isset($message_id[1])) $where .= " and status $message_id[0] $message_id[1] ";
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			if(isset($action)) $where .= " and action = $action ";
			if(isset($type)) $where .= " and type = $type ";
			$sql    = "
SELECT * FROM (
	SELECT
		msh.id,
		msh.mid,
		msh.message_id message_code,
		m.name message,
		msh.context,
		msh.type,
		msh.action,
		msh.sms_id,
		msh.send_status,
		msh.status,
		msh.creator creator_code,
		u1.name creator
	FROM meeting_royalwiss_deal.message_send_history msh
	LEFT JOIN meeting_common.user u1 ON u1.id = msh.creator AND u1.status <> 2
	JOIN meeting_royalwiss_deal.message m ON m.id = msh.message_id AND m.status <> 2
) tab
$where
$order";
			$result = $this->query($sql);

			return $result;
		}
	}