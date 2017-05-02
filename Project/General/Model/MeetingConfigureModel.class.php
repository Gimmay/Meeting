<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-27
	 * Time: 9:32
	 */

	namespace General\Model;

	interface MeetingConfigureModel{
		/** 选取微信公众号 */
		const WECHAT_MODE_OFFICIAL = 1;
		/** 选取微信企业号 */
		const WECHAT_MODE_ENTERPRISE = 2;

		/**
		 * 获取微信公众号的接口配置信息
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getWechatOfficialConfigure($meeting_id);

		/**
		 * 获取微信企业号的接口配置信息
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getWechatEnterpriseConfigure($meeting_id);

		/**
		 * 获取首易SMS的接口配置信息
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getSMSMobsetConfigure($meeting_id);

		/**
		 * 获取邮件的接口配置信息
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getEmailConfigure($meeting_id);
	}