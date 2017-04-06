<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-16
	 * Time: 17:56
	 */
	namespace RoyalwissD\Logic;

	class MeetingConfigureLogic extends RoyalwissDLogic{
		/**
		 * 处理POST/GET请求
		 *
		 * @param string $type 请求类型
		 * @param array  $opt  相关参数
		 *
		 * @return mixed
		 */
		public function handlerRequest($type, $opt = []){
		}

		/**
		 * 设定额外数据
		 *
		 * @param string $type 操作类型
		 * @param mixed  $data 处理数据
		 *
		 * @return mixed
		 */
		public function setData($type, $data){
		}

		/**
		 * 构造客户信息重复判定模式码
		 *
		 * @param string $client_name   客户名称
		 * @param string $client_unit   客户会所
		 * @param string $client_mobile 客户手机号
		 *
		 * @return int
		 */
		public function encodeClientRepeatMode($client_name, $client_unit, $client_mobile){
			$client_name   = (isset($client_name) && $client_name) ? 1 : 0;
			$client_unit   = (isset($client_unit) && $client_unit) ? 1 : 0;
			$client_mobile = (isset($client_mobile) && $client_mobile) ? 1 : 0;

			return bindec("$client_name$client_unit$client_mobile");
		}

		/**
		 * 解析客户信息重复判定模式码
		 *
		 * @param int $mode 客户信息重复判定模式码
		 *
		 * @return array
		 */
		public function decodeClientRepeatMode($mode){
			$temp_mode = sprintf("%03d", decbin($mode));

			return [
				'clientName'   => $temp_mode[0],
				'clientUnit'   => $temp_mode[1],
				'clientMobile' => $temp_mode[2]
			];
		}

		/**
		 * 构造消息发送模式码
		 *
		 * @param int $sms               短信
		 * @param int $wechat_enterprise 微信企业号
		 * @param int $wechat_official   微信公众号
		 * @param int $email             邮件
		 *
		 * @return array
		 */
		public function encodeMessageMode($sms, $wechat_enterprise, $wechat_official, $email){
			$sms               = (isset($sms) && $sms) ? 1 : 0;
			$wechat_enterprise = (isset($wechat_enterprise) && $wechat_enterprise) ? 1 : 0;
			$wechat_official   = (isset($wechat_official) && $wechat_official) ? 1 : 0;
			$email             = (isset($email) && $email) ? 1 : 0;

			return bindec("$sms$wechat_enterprise$wechat_official$email");
		}

		/**
		 * 解析消息发送模式
		 *
		 * @param int $mode 消息发送模式码
		 *
		 * @return array
		 */
		public function decodeMessageMode($mode){
			$temp_mode = sprintf("%04d", decbin($mode));

			return [
				'sms'              => $temp_mode[0],
				'wechatEnterprise' => $temp_mode[1],
				'wechatOfficial'   => $temp_mode[2],
				'email'            => $temp_mode[3],
			];
		}
	}