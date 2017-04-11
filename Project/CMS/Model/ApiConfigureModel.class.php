<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-31
	 * Time: 12:01
	 */
	namespace CMS\Model;

	use General\Model\UserModel;

	class ApiConfigureModel extends CMSModel{
		protected $tableName = 'api_configure';
		const TABLE_NAME = 'api_configure';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';
		const CONTROL_COLUMN_PARAMETER_SELF = ['meetingType' => 'mtype'];

		public function getList($control = []){
			$table_api_configure  = $this->tableName;
			$table_user           = UserModel::TABLE_NAME;
			$common_database = self::DATABASE_NAME;
			$keyword              = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order                = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status               = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_type         = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingType']];
			$where                = ' WHERE 0 = 0 ';
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
			if(isset($meeting_type)) $where .= " and mtype $meeting_type[0] $meeting_type[1] ";
			$sql = "
SELECT * FROM (
	SELECT
		ac.id,
		ac.name,
		ac.name_pinyin,
		ac.mtype,
		ac.sms_mobset_url,
		ac.sms_mobset_user,
		ac.sms_mobset_pass,
		ac.sms_mobset_corpid,
		ac.wechat_official_appid,
		ac.wechat_official_appsecret,
		ac.wechat_enterprise_corpid,
		ac.wechat_enterprise_corpsecret,
		ac.wechat_enterprise_appid,
		ac.alipay_name,
		ac.alipay_pid,
		ac.alipay_key,
		ac.wxpay_mchid,
		ac.wxpay_key,
		ac.wxpay_sslcert_path,
		ac.wxpay_sslkey_path,
		ac.status,
		ac.comment,
		ac.creatime,
		u1.name creator
	FROM $common_database.$table_api_configure ac
	LEFT JOIN $common_database.$table_user u1 ON u1.id = ac.creator AND u1.status <> 2
) tab
$where
$order
";

			return $this->query($sql);
		}
	}