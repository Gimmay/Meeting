<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-9
	 * Time: 11:25
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class MeetingConfigureModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'meeting_configure';
		const TABLE_NAME = 'meeting_configure';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		/** 自定义字段的名称 */
		const CUSTOM_COLUMN = 'column';
		/** 客户重复记录判定模式 */
		const CLIENT_REPEAT_MODE = 0;
		/** 客户重复记录动作-覆盖 */
		const CLIENT_REPEAT_ACTION_OVERRIDE = 1;
		/** 客户重复记录动作-覆盖 */
		const CLIENT_REPEAT_ACTION_SKIP = 2;
		/** 消息发送模式 */
		const MESSAGE_MODE = 0;

		/**
		 * 创建会议配置记录
		 *
		 * @param array $data 记录信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建会议配置信息成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建会议配置信息失败'
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
			$this_database           = self::DATABASE_NAME;
			$table_meeting_configure = $this->tableName;
			$last_column_index       = $this->getLastCustomColumnIndex();
			$type                    = " $add_column_data[type]($add_column_data[typeSize]) ";
			$comment                 = " $add_column_data[comment]";
			$last_column             = $last_column_index == 0 ? 'mid' : self::CUSTOM_COLUMN.$last_column_index;
			$column                  = self::CUSTOM_COLUMN.(++$last_column_index);
			$sql                     = "ALTER TABLE $this_database.$table_meeting_configure
ADD COLUMN `$column` $type NULL COMMENT '$comment' AFTER `$last_column`";
			$result                  = $this->execute($sql);
			if($result === false) return ['status' => false, 'message' => '创建失败'];
			else return ['status' => true, 'message' => '创建成功'];
		}

		/**
		 * 获取最后一个自定义字段的编号
		 *
		 * @return int
		 */
		public function getLastCustomColumnIndex(){
			$table_meeting_configure = $this->tableName;
			$this_database           = self::DATABASE_NAME;
			$list = $this->query("SELECT c.*
FROM information_schema.`TABLES` t
JOIN information_schema.`COLUMNS` c ON c.TABLE_NAME = t.TABLE_NAME
WHERE t.TABLE_SCHEMA = '$this_database'
AND t.TABLE_NAME = '$table_meeting_configure'
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
		 * 获取字段列表
		 *
		 * @param bool $just_custom_column 是否只显示自定义字段
		 *
		 * @return array
		 */
		public function getColumnList($just_custom_column = false){
			$table_meeting_configure = $this->tableName;
			$this_database           = self::DATABASE_NAME;
			$sql_custom              = "
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
AND t.TABLE_NAME = '$table_meeting_configure' AND COLUMN_NAME LIKE '".self::CUSTOM_COLUMN.'%\'';
			$sql_fixed               = "
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
AND t.TABLE_NAME = '$table_meeting_configure' AND COLUMN_NAME NOT LIKE '".self::CUSTOM_COLUMN.'%\'';
			$sql                     = $just_custom_column ? $sql_custom : "$sql_custom UNION $sql_fixed";
			$list                    = $this->query($sql);

			return $list;
		}

		/**
		 * 获取微信公众号的接口配置信息
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getWechatOfficialConfigure($meeting_id){
			if(!$this->fetch(['mid' => $meeting_id])) return [
				'status'  => false,
				'message' => '找不到会议配置信息'
			];
			$meeting_configure = $this->getObject();
			/** @var \General\Model\ApiConfigureModel $api_configure_model */
			$api_configure_model = D('General/ApiConfigure');
			if($meeting_configure['wechat_official_configure']){
				if(!$api_configure_model->fetch([
					'id'     => $meeting_configure['wechat_official_configure'],
					'status' => ['neq', 2]
				])
				) return [
					'status'  => false,
					'message' => '找不到微信公众号接口配置信息'
				];
				$api_configure = $api_configure_model->getObject();

				return [
					'status'  => true,
					'message' => '获取配置成功',
					'data'    => [
						'appID'     => $api_configure['wechat_official_appid'],
						'appSecret' => $api_configure['wechat_official_appsecret'],
					]
				];
			}
			else return [
				'status'  => false,
				'message' => '没有配置微信公众号接口'
			];
		}

		/**
		 * 获取微信企业号的接口配置信息
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getWechatEnterpriseConfigure($meeting_id){
			if(!$this->fetch(['mid' => $meeting_id])) return [
				'status'  => false,
				'message' => '找不到会议配置信息'
			];
			$meeting_configure = $this->getObject();
			/** @var \General\Model\ApiConfigureModel $api_configure_model */
			$api_configure_model = D('General/ApiConfigure');
			if($meeting_configure['wechat_enterprise_configure']){
				if(!$api_configure_model->fetch([
					'id'     => $meeting_configure['wechat_enterprise_configure'],
					'status' => ['neq', 2]
				])
				) return [
					'status'  => false,
					'message' => '找不到微信企业号接口配置信息'
				];
				$api_configure = $api_configure_model->getObject();

				return [
					'status'  => true,
					'message' => '获取配置成功',
					'data'    => [
						'corpID'     => $api_configure['wechat_enterprise_corpid'],
						'corpSecret' => $api_configure['wechat_enterprise_corpsecret'],
						'appID'      => $api_configure['wechat_enterprise_appid']
					]
				];
			}
			else return [
				'status'  => false,
				'message' => '没有配置微信企业号接口'
			];
		}

		/**
		 * 获取首易SMS的接口配置信息
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getSMSMobsetConfigure($meeting_id){
			if(!$this->fetch(['mid' => $meeting_id])) return [
				'status'  => false,
				'message' => '找不到会议配置信息'
			];
			$meeting_configure = $this->getObject();
			/** @var \General\Model\ApiConfigureModel $api_configure_model */
			$api_configure_model = D('General/ApiConfigure');
			if($meeting_configure['sms_mobset_configure']){
				if(!$api_configure_model->fetch([
					'id'     => $meeting_configure['sms_mobset_configure'],
					'status' => ['neq', 2]
				])
				) return [
					'status'  => false,
					'message' => '找不到首页SMS接口配置信息'
				];
				$api_configure = $api_configure_model->getObject();

				return [
					'status'  => true,
					'message' => '获取配置成功',
					'data'    => [
						'url'    => $api_configure['sms_mobset_url'],
						'corpID' => $api_configure['sms_mobset_corpid'],
						'user'   => $api_configure['sms_mobset_user'],
						'pass'   => $api_configure['sms_mobset_pass']
					]
				];
			}
			else return [
				'status'  => false,
				'message' => '没有配置首易SMS接口'
			];
		}

		public function getEmailConfigure($meeting_id){
			// todo
		}
	}