<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-8
	 * Time: 9:52
	 */
	namespace Core\Logic;

	use Quasar\Type;
	use SoapClient;

	class SMSLogic extends CoreLogic{
		private $_config = [
			'corpID'  => 122503, // 企业ID
			'user'    => 'Ser01', // 登入用户
			'pass'    => 'Ur763825', // 登入密码
			'soapUrl' => 'http://sms.mobset.com:8080/Api?wsdl' // WebService接口地址
		];

		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 获取短信剩余条数
		 *
		 * @return number
		 */
		public function getBalance(){
			$time     = $this->_getTimestamp();
			$password = $this->_getPassword($time);
			$client   = new SoapClient($this->_config['soapUrl']);
			/** @noinspection PhpUndefinedMethodInspection */
			$result = $client->Sms_GetBalance([
				'CorpID'    => $this->_config['corpID'],
				'LoginName' => $this->_config['user'],
				'Password'  => $password,
				'TimeStamp' => $time,
			]);

			return $result->Balance;
		}

		/**
		 * 发送短信
		 *
		 * @param string $message  发送的短信信息
		 * @param array  $receiver 接收者的手机号
		 * @param bool   $long     是否为长短信
		 *
		 * @return array data索引的数据为Sms_ID数组
		 */
		public function send($message, $receiver = [], $long = false){
			/**
			 * 用于生成MobileListGroup对象的闭包函数
			 *
			 * @param array      $list        接收短信的电话号码的数组
			 * @param SoapClient $soap_client Soap对象
			 *
			 * @return array 封装的MobileListGroup数组
			 */
			$makeMobileListGroup = function ($list, $soap_client){
				/** @noinspection PhpUndefinedFieldInspection */
				$mobile_list = $soap_client->ArrayOfMobileList[1];
				foreach($list as $key => $val){
					/** @noinspection PhpUndefinedFieldInspection */
					$mobile_list[$key]         = $soap_client->MobileListGroup;
					$mobile_list[$key]->Mobile = $val;
				}

				return $mobile_list;
			};
			$makeSmsIDList       = function ($result){
				$sms_id_list = [];
				foreach($result['SmsIDList']['SmsIDGroup'] as $val) array_push($sms_id_list, $val['SmsID']);

				return $sms_id_list;
			};
			$time                = $this->_getTimestamp();
			$password            = $this->_getPassword($time);
			$client              = new SoapClient($this->_config['soapUrl']);
			/** @noinspection PhpUndefinedMethodInspection */
			$result   = $client->Sms_Send([
				'Content'    => $message,
				'CorpID'     => $this->_config['corpID'],
				'LongSms'    => $long ? 1 : 0,
				'LoginName'  => $this->_config['user'],
				'Password'   => $password,
				'TimeStamp'  => $time,
				'MobileList' => $makeMobileListGroup($receiver, $client)
			]);
			$type_obj = new Type();
			$result   = $type_obj->parseObjectToArray($result);

			return [
				'status'  => $result['Count']>0 ? true : false,
				'message' => $result['ErrMsg'],
				'data'    => $makeSmsIDList($result)
			];
		}

		/**
		 * 生成密码
		 *
		 * @param string $time 时间戳
		 *
		 * @return string
		 */
		private function _getPassword($time){
			return md5($this->_config['corpID'].$this->_config['pass'].$time);
		}

		/**
		 * 生成时间戳
		 *
		 * @return string
		 */
		private function _getTimestamp(){
			return date('mdHid');
		}
	}