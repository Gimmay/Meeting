<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-28
	 * Time: 10:08
	 */

	namespace General\Model;

	class SystemConfigureModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'system_configure';
		const TABLE_NAME = 'system_configure';
		protected $autoCheckFields = true;
		protected $connection      = "DB_CONFIG_COMMON";
		/** 选取微信公众号 */
		const WECHAT_MODE_OFFICIAL = 1;
		/** 选取微信企业号 */
		const WECHAT_MODE_ENTERPRISE = 2;
		/** 配置数据 */
		const CONFIGURE = [
			'WECHAT_ENTERPRISE_CONFIGURE' => ['name' => '全局微信企业号配置', 'value' => 0],
			'WECHAT_OFFICIAL_CONFIGURE'   => ['name' => '全局微信公众号配置', 'value' => 0],
			'WECHAT_MODE'                 => ['name' => '默认选择的微信类型', 'value' => 1]
		];

		/**
		 * 保存配置
		 *
		 * @param array $data 配置数据
		 *
		 * @return array
		 */
		public function setConfigure($data){
			$this_database = self::DATABASE_NAME;
			$this_table    = self::TABLE_NAME;
			$replace_char  = '#%VALUE%#';
			$sql_main      = "
UPDATE $this_database.$this_table t
SET t.value =  CASE code $replace_char END
";
			$sql_sub       = "WHERE code IN ($replace_char) ";
			foreach($data as $key => $value){
				$key      = strtoupper($key);
				$sql_sub  = str_replace($replace_char, "'$key',$replace_char", $sql_sub);
				$sql_main = str_replace("$replace_char", " WHEN '$key' THEN '$value'$replace_char", $sql_main);
			}
			$sql    = "$sql_main $sql_sub";
			$sql    = str_replace(",$replace_char", '', $sql);
			$sql    = str_replace("$replace_char", '', $sql);
			$result = $this->execute($sql);
			if($result) return [
				'status'  => true,
				'message' => "配置成功"
			];
			else return [
				'status'  => false,
				'message' => "配置失败"
			];
		}

		/**
		 * 获取配置
		 *
		 * @return array
		 */
		public function getConfigure(){
			$list   = $this->select();
			$result = [];
			foreach($list as $value){
				$key          = strtolower($value['code']);
				$result[$key] = $value;
			}

			return $result;
		}

		/**
		 * 获取微信公众号的接口配置信息
		 *
		 * @return array
		 */
		public function getWechatOfficialConfigure(){
			$configure = $this->where(['code' => 'WECHAT_OFFICIAL_CONFIGURE'])->find();
			/** @var \General\Model\ApiConfigureModel $api_configure_model */
			$api_configure_model = D('General/ApiConfigure');
			if($configure['value']){
				if(!$api_configure_model->fetch([
					'id'     => $configure['value'],
					'status' => ['neq', 2]
				])
				) return [
					'status'  => false,
					'message' => '缺少系统微信公众号接口配置信息'
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
				'message' => '没有系统配置微信公众号接口'
			];
		}

		/**
		 * 获取微信企业号的接口配置信息
		 *
		 * @return array
		 */
		public function getWechatEnterpriseConfigure(){
			$configure = $this->where(['code' => 'WECHAT_ENTERPRISE_CONFIGURE'])->find();
			/** @var \General\Model\ApiConfigureModel $api_configure_model */
			$api_configure_model = D('General/ApiConfigure');
			if($configure['value']){
				if(!$api_configure_model->fetch([
					'id'     => $configure['value'],
					'status' => ['neq', 2]
				])
				) return [
					'status'  => false,
					'message' => '缺少微信企业号接口配置信息'
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
	}