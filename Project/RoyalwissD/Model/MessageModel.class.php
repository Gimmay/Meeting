<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-31
	 * Time: 17:35
	 */
	namespace RoyalwissD\Model;

	use CMS\Logic\Session;
	use Exception;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use General\Model\UserModel;

	class MessageModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'message';
		const TABLE_NAME = 'message';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid',
			'type'      => 'type'
		];
		/** 模板消息类型 */
		const TYPE = [
			1 => '短信',
			2 => '微信企业号',
			3 => '微信公众号',
			4 => '邮箱'
		];

		/**
		 * 创建消息模板
		 *
		 * @param array $data 消息模板信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建消息模板成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建消息模板失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		public function getList($control = []){
			$table_message             = $this->tableName;
			$table_user                = UserModel::TABLE_NAME;
			$table_message_correlation = MessageCorrelationModel::TABLE_NAME;
			$common_database           = GeneralModel::DATABASE_NAME;
			$this_database             = self::DATABASE_NAME;
			$keyword                   = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order                     = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status                    = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id                = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$type                      = $control[self::CONTROL_COLUMN_PARAMETER_SELF['type']];
			$where                     = ' WHERE 0 = 0 ';
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
			if(isset($type) && isset($type[0]) && isset($type[1])) $where .= " and type $type[0] $type[1] ";
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			$sql    = "
SELECT * FROM (
	SELECT
		m.id,
		m.mid,
		m.name,
		m.name_pinyin,
		m.type,
		m.context,
		m.status,
		m.comment,
		m.creator creator_code,
		m.creatime,
		(SELECT group_concat(mc.action) FROM $this_database.$table_message_correlation mc WHERE mc.message_id = m.id AND mc.status = 1 limit 1) action,
		u1.name creator
	FROM $this_database.$table_message m
	LEFT JOIN $common_database.$table_user u1 ON u1.id = m.creator AND u1.status <> 2
) tab
$where
$order";
			$result = $this->query($sql);

			return $result;
		}

		/**
		 * 使用到对应的系统动作
		 *
		 * @param int $meeting_id 会议ID
		 * @param int $message_id 消息ID
		 * @param int $action     动作ID
		 *
		 * @return array
		 */
		public function useTo($meeting_id, $message_id, $action){
			$table_message             = $this->tableName;
			$table_message_correlation = MessageCorrelationModel::TABLE_NAME;
			$this_database             = self::DATABASE_NAME;
			if(is_null($action) || $action == '' || !$action) $action = [];
			elseif(is_numeric($action)) $action = [$action];
			elseif(is_string($action)) $action = explode(',', $action);
			elseif(is_array($action)) ;
			else return ['status' => false, 'message' => '参数类型错误'];
			// 先将其他可能已经分配了该动作的消息模板取消使用
			if(count($action)>0){
				$action_str = implode(',', $action);
				if(!$this->fetch(['id' => $message_id])) return ['status' => false, 'message' => '找不到消息信息'];
				$current_message = $this->getObject();
				$sql             = "
UPDATE $this_database.$table_message_correlation mc SET mc.status = 0
WHERE mc.status = 1 AND mc.mid = $meeting_id
AND mc.action in ($action_str)
AND mc.message_id in (
	SELECT m.id FROM $this_database.$table_message m
	WHERE m.status = 1 AND m.mid = $meeting_id AND m.type = $current_message[type]
)
";
				$this->execute($sql);
			}
			// 取消该消息模板所有使用的动作
			$sql = "
UPDATE $this_database.$table_message_correlation mc SET mc.status = 0
WHERE mc.status = 1 AND mc.mid = $meeting_id
AND mc.message_id = $message_id
";
			$this->execute($sql);
			// 保存设定的动作
			/** @var \RoyalwissD\Model\MessageCorrelationModel $message_correlation_model */
			$message_correlation_model = D('RoyalwissD/MessageCorrelation');
			$save_data                 = [];
			foreach($action as $val) $save_data[] = [
				'mid'        => $meeting_id,
				'message_id' => $message_id,
				'action'     => $val,
				'creator'    => Session::getCurrentUser(),
				'creatime'   => Time::getCurrentTime()
			];
			if(count($save_data)>0){
				$result = $message_correlation_model->addAll($save_data);

				return $result ? ['status' => true, 'message' => '保存成功'] : ['status' => false, 'message' => '保存失败'];
			}

			return ['status' => true, 'message' => '保存成功'];
		}

		/**
		 * 获取消息模板
		 *
		 * @param int $meeting_id 会议ID
		 * @param int $type       消息类型ID
		 * @param int $action     动作ID
		 *
		 * @return null|string
		 */
		public function getMessage($meeting_id, $type, $action){
			$table_message             = $this->tableName;
			$table_message_correlation = MessageCorrelationModel::TABLE_NAME;
			$this_database             = self::DATABASE_NAME;
			$sql                       = "
SELECT m.context, m.id FROM $this_database.$table_message_correlation mc
JOIN $this_database.$table_message m ON m.id = mc.message_id AND m.status = 1
WHERE m.mid = $meeting_id AND mc.mid = $meeting_id AND mc.status = 1 AND m.type = $type AND mc.action = $action
LIMIT 1
			";
			$result                    = $this->query($sql);
			if(isset($result[0])) return [
				'id'      => $result[0]['id'],
				'context' => $result[0]['context']
			];
			else return ['id' => 0, 'context' => null];
		}
	}