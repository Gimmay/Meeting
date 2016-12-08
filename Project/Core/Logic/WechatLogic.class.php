<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-12-5
	 * Time: 9:26
	 */
	namespace Core\Logic;

	use Quasar\Wechat\WechatPAApi;
	use Quasar\Wechat\WechatEAApi;

	class WechatLogic extends CoreLogic{
		private $_mainConfig = [
			'id'     => '', // appid或corpid
			'secret' => '' // appsecret或corpsecret
		];
		private $_payConfig  = [
			'mchID'    => '', // 商户id
			'apiKey'   => '', // api key
			'certPath' => '', // 证书1路径
			'keyPath'  => '' // 证书2路径
		];
		private $_wechatType = -1; // 微信类型
		private $_appID      = -1; // 应用id
		private $_meetingID  = 0; // 会议id
		private $_cachePath  = ''; // 凭证缓存路径
		const PA_CACHE_PATH = RUNTIME_PATH.'/WechatPA-Cache'; // 公众号的凭证缓存路径
		const EA_CACHE_PATH = RUNTIME_PATH.'/WechatEA-Cache'; // 企业号的凭证缓存路径

		public function _initialize(){
			parent::_initialize();
			$this->_meetingID = I('get.mid', 0, 'int');
			$this->_initConfig();
		}

		private function _initConfig(){
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\ConfigWechatModel $config_wechat_model */
			$config_wechat_model          = D('Core/ConfigWechat');
			$meeting                      = $meeting_model->findMeeting(1, ['id' => $this->_meetingID, 'status' => 1]);
			$config_wechat                = $config_wechat_model->findRecord(1, [
				'id'     => $meeting['config_wechat'],
				'status' => 1
			]);
			$this->_mainConfig['id']      = $config_wechat['acid'];
			$this->_mainConfig['secret']  = $config_wechat['acsecret'];
			$this->_payConfig['mchID']    = $config_wechat['mchid'];
			$this->_payConfig['apiKey']   = $config_wechat['api_key'];
			$this->_payConfig['certPath'] = $config_wechat['cert_path'];
			$this->_payConfig['keyPath']  = $config_wechat['key_path'];
			$this->_wechatType            = $config_wechat['type'];
			$this->_appID                 = $config_wechat['c_appid'];
			if($this->_wechatType == 0){
				$this->_cachePath = self::PA_CACHE_PATH;
			}
			if($this->_wechatType == 1){
				$this->_cachePath = self::EA_CACHE_PATH;
			}
		}

		public function getUserList(){
			if($this->_wechatType == 1){
				$wechat_object = new WechatEAApi($this->_mainConfig['id'], $this->_mainConfig['secret']);

				return $wechat_object->getUserList(1);
			}
			if($this->_wechatType == 0){
				$wechat_object = new WechatPAApi($this->_mainConfig['id'], $this->_mainConfig['secret']);

				return $wechat_object->getOpenIDList();
			}

			return [];
		}

		public function sendMessage($type, $data, $receiver){
			if($this->_wechatType == 1){
				$wechat_object = new WechatEAApi($this->_mainConfig['id'], $this->_mainConfig['secret']);

				return $wechat_object->sendMessage($type, $data, $this->_appID, $receiver);
			}
			if($this->_wechatType == 0){
				$wechat_object = new WechatPAApi($this->_mainConfig['id'], $this->_mainConfig['secret']);
			}

			return ['status' => false, 'message' => '发送失败'];
		}

		public function getUserID(){
			if($this->_wechatType == 1){
				$wechat_object = new WechatEAApi($this->_mainConfig['id'], $this->_mainConfig['secret']);

				return $wechat_object->getID();
			}
			if($this->_wechatType == 0){
				$wechat_object = new WechatPAApi($this->_mainConfig['id'], $this->_mainConfig['secret']);

				return $wechat_object->getUserFullSet(0);
			}

			return '';
		}

		public function getSignature($random_str, $time, $url = null){
			if($this->_wechatType == 1) $wechat_object = new WechatEAApi($this->_mainConfig['id'], $this->_mainConfig['secret']);
			if($this->_wechatType == 0) $wechat_object = new WechatPAApi($this->_mainConfig['id'], $this->_mainConfig['secret']);
			if(isset($wechat_object)) return $wechat_object->getSignature($random_str, $time, $url);
			else return '';
		}

		public function saveUserInfo($list, $type, $acid){
			$save_data = [];
			foreach($list as $key=>$val){
				$save_data[] = [
					''
				];
			}
		}

		public function webPay($option = []){
			$config = $this->_getWxpayConfig($option['mid']);
			if($config){
				vendor('Wxpay.Api');
				vendor('Wxpay.JsApiPay');
				$wx_jsapi_obj = new JsApiPay();
				$wx_api_obj   = new WxPayApi();
				$wx_order_obj = new WxPayUnifiedOrder();
				// 设置公众号或企业号的配置信息
				$wx_api_obj->setConfig('NOTIFY_URL', $option['notifyUrl']);
				$wx_api_obj->setConfig('APP_ID', $config['acid']);
				$wx_api_obj->setConfig('APP_SECRET', $config['acsecret']);
				$wx_api_obj->setConfig('MCH_ID', $config['mchid']);
				$wx_api_obj->setConfig('API_KEY', $config['api_key']);
				$wx_api_obj->setConfig('SSLCERT_PATH', $config['cert_path']);
				$wx_api_obj->setConfig('SSLKEY_PATH', $config['key_path']);
				$wx_order_obj->SetBody($option['title']);
				$wx_order_obj->SetDetail($option['detail']);
				$wx_order_obj->SetAttach($option['attach']);
				$wx_order_obj->SetOut_trade_no($option['orderID']);
				$wx_order_obj->SetTotal_fee($option['price']);
				$wx_order_obj->SetTime_start(date("YmdHis"));
				$wx_order_obj->SetTime_expire(date("YmdHis", time()+600));
				$wx_order_obj->SetGoods_tag($option['tag']);
				$wx_order_obj->SetNotify_url($option['notifyUrl']);
				$wx_order_obj->SetTrade_type("JSAPI");
				$wx_order_obj->SetOpenid($option['openid']);
				$order = $wx_api_obj->unifiedOrder($wx_order_obj);
				print_r($order);exit;
				$jsApiParameters = $wx_jsapi_obj->GetJsApiParameters($order, $config['api_key']);
				$editAddress = $wx_jsapi_obj->GetEditAddressParameters();
				print_r(['jsApiParameters' => $jsApiParameters, 'editAddress' => $editAddress]);
				exit;

				return ['jsApiParameters' => $jsApiParameters, 'editAddress' => $editAddress];
			}
		}

	}