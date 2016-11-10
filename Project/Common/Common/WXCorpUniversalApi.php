<?php
	/**
	 * Create by PhpStorm
	 * User: Quasar
	 * Time: 2016/3/30 10:52
	 */
	namespace Quasar;

	use Exception;

	header("Content-type:text/html; charset=utf-8");

	/**
	 * Class WXCorpUniversalApi
	 * 用于提供实现微信企业号API的操作类
	 */
	class WXCorpUniversalApi extends WXCorpApiLib{
		/**
		 * @var array 企业号的基本配置信息
		 *            包括corpid和corpsecret
		 */
		private $_config = ['corpid' => null, 'corpsecret' => null];

		/**
		 * 手动设置corpid
		 *
		 * @param string $corp_id 企业号的corpid
		 */
		public function setCorpid($corp_id){
			$this->_config['corpid'] = $corp_id;
		}

		/**
		 * 手动设置corpsecret
		 *
		 * @param string $corp_secret 企业号的appsercet
		 */
		public function setCorpsercet($corp_secret){
			$this->_config['corpsecret'] = $corp_secret;
		}

		/**
		 * 返回企业号的基本参数配置
		 *
		 * @return array 格式为['corpid'=>string, 'corpsecret'=>string]
		 */
		public function getConfigs(){
			return $this->_config;
		}

		/**
		 * WXCorpUniversalApi constructor.
		 * 用于初始化公众号的基本配置信息
		 *
		 * @param null|string $corp_id     企业号的appid
		 * @param null|string $corp_secret 企业号的appsecret
		 */
		public function __construct($corp_id = null, $corp_secret = null){
			if($corp_id != null) $this->_config['corpid'] = $corp_id;
			if($corp_secret != null) $this->_config['corpsecret'] = $corp_secret;
		}

		/**
		 * 获取AccessToken
		 * TODO 检测是否超过限制
		 *
		 * @return string|array 成功则返回AccessToken，否则返回错误信息
		 */
		public function getAccessToken(){
			$cd_obj = new WXCorpLocalCache($this->_config['corpid'], 'ac', getcwd().'/Project/Runtime/Wxcorp-Cache');
			if($cd_obj->isExpired()){
				$response = $this->wxcapi_AccessToken($this->_config['corpid'], $this->_config['corpsecret']);
				if($response['status']){
					$cd_obj->saveCacheData(json_encode([
						'access_token' => $response['data']['access_token'],
						'expired_time' => time()+7000
					]));

					return $response['data']['access_token'];
				}
				else{
					return $response['data'];
				}
			}
			else{
				return $cd_obj->getCacheData();
			}
		}

		/**
		 * 获取JSPI_Ticket
		 * TODO 检测是否超过限制
		 *
		 * @param string|null $access_token
		 *
		 * @return array|string 成功则返回JSPI_Ticket，否则返回错误信息
		 */
		public function getJSApiTicket($access_token = null){
			$cd_obj       = new WXCorpLocalCache($this->_config['corpid'], 'jt', getcwd().'/Project/Runtime/Wxcorp-Cache');
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			if($cd_obj->isExpired()){
				$response = $this->wxcapi_JSApiTicket($access_token);
				if($response['status']){
					$cd_obj->saveCacheData(json_encode([
						'jsapi_ticket' => $response['data']['ticket'],
						'expired_time' => time()+7000
					]));

					return $response['data']['ticket'];
				}
				else{
					return $response['data'];
				}
			}
			else{
				return $cd_obj->getCacheData();
			}
		}

		/**
		 * 获取Code
		 * *这里做了页面的定向跳转，且需要被调用的页面在微信中打开！
		 * *回调地址中将会携带code参数
		 *
		 * @param string $url     回调地址
		 * @param string $data    附带数据
		 * @param string $corp_id 企业号的appid
		 */
		public function getCode($url, $data = '', $corp_id = null){
			if($corp_id === null) $corp_id = $this->_config['corpid'];
			$url  = urlencode($url);
			$data = urlencode((string)$data);
			header('Location:'.$this->wxcapi_GetCode($corp_id, $url, $data));
		}

		/**
		 * 获取用户ID
		 * *有是否为企业成员的区别
		 *
		 * @param string      $code         换取用户信息的Code
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 若成功则返回以type索引的类型值【1表示企业成员，0表示非企业成员】，以id索引的用户ID值
		 *               否则返回错误信息
		 */
		public function getUserID($code, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = $this->wxcapi_GetUserID($access_token, $code);
			if($response['status']){
				if(isset($response['data']['UserId'])) return ['type' => 1, 'id' => $response['data']['UserId']];
				if(isset($response['data']['OpenId'])) return ['type' => 0, 'id' => $response['data']['OpenId']];
			}
			else{
				return $response['data'];
			}

			return $response['data'];
		}

		/**
		 * 企业号成员ID和微信OPENID的相互转换
		 * *若不是企业成员，使用OpenID获取UserID会提示OpenID非法
		 *
		 * @param int|string  $id           UserID或OpenID
		 * @param int         $type         转换类型【1表示通过UserID获取OpenID，0表示通过OpenID获取UserID】
		 * @param null|int    $agent_id     需要发送红包的应用ID，若只是使用微信支付和企业转账，则无需该参数
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return bool|string|int|array 若成功则返回相应的ID值，否则返回错误信息或者$type参数赋值不正确直接返回false
		 */
		public function parseID($id, $type, $agent_id = null, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			switch($type){
				case 0:
					$response = $this->wxcapi_parseOpenIDToUserID($access_token, $id);
					if($response['status']) return $response['data']['userid'];
					else return $response['data'];
				break;
				case 1:
					$response = $this->wxcapi_parseUserIDToOpenID($access_token, $id, $agent_id);
					if($response['status']) return $response['data']['openid'];
					else return $response['data'];
				break;
				default:
					return false;
			}
		}

		/**
		 * 获取当前微信账号的成员ID值
		 *
		 * @param null|string $corp_id 企业号的corpid
		 *
		 * @return string|null 若成功则返回UserID，否则返回null
		 */
		public function getID($corp_id = null){
			if(isset($_GET['code'])){
				$code     = $_GET['code'];
				$response = $this->getUserID($code);
				if($response['type'] == 1) return $response['id'];
				else return null;
			}
			else{
				if($corp_id === null) $corp_id = $this->_config['corpid'];
				$redirect_url = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
				header('Location:'.$this->wxcapi_GetCode($corp_id, $redirect_url, 'getID'));
				exit;
			}
		}

		/**
		 * 进行二次验证
		 *
		 * @param int         $user_id      企业用户ID
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回接口调用结果
		 */
		public function check($user_id, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = $this->wxcapi_UserCheck($access_token, $user_id);

			return $response['data'];
		}

		/**
		 * 获取成员信息
		 *
		 * @param int         $user_id      企业用户ID
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回成员信息和接口调用结果
		 */
		public function getUserInfo($user_id, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = $this->wxcapi_GetUserInfo($access_token, $user_id);

			return $response['data'];
		}

		/**
		 * 发送消息
		 *
		 * @param string      $type         消息类型，枚举为['text','image','voice','file','mpnews','news','video','mpnews_r']
		 * @param array       $data         传递的数据，具体参照对应的消息类型所需的数据
		 * @param int         $agent_id     应用ID
		 * @param array       $receiver     接收者信息
		 *                                  可指定的键值如下：
		 *                                  [
		 *                                  'user'=>[string, string, string, ...],
		 *                                  'dept'=>[string, string, string, ...],
		 *                                  'tag'=>[string, string, string, ...]
		 *                                  ]
		 * @param int         $safe         是否保密消息，枚举为0|1
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回调用结果
		 */
		public function sendMessage($type, $data, $agent_id, $receiver, $safe = 0, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;

			return $this->wxcapi_SendMessage($access_token, $type, $data, $agent_id, $receiver, $safe);
		}

		/**
		 * 获取部门列表
		 *
		 * @param int         $id           部门ID号（根部门为1）
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回部门列表数据和接口调用结果
		 */
		public function getDepartmentList($id = 1, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = $this->wxcapi_GetDepartmentList($access_token, $id);

			return $response['data'];
		}

		/**
		 * 获取部门下的用户列表
		 *
		 * @param int         $id           部门ID号（根部门为1）
		 * @param int         $fetch_child  是否递归获取子部门下面的成员。枚举为[0,1]
		 * @param int         $status       0获取全部成员
		 *                                  1获取已关注成员列表
		 *                                  2获取禁用成员列表
		 *                                  4获取未关注成员列表
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return mixed
		 */
		public function getUserList($id, $fetch_child = 1, $status = 0, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = $this->wxcapi_GetUserList($access_token, $id, $fetch_child, $status);

			return $response['data'];
		}

		/**
		 * 更新成员信息
		 *
		 * @param int         $user_id      企业用户ID
		 * @param array       $data         需要保存到用户信息的数据
		 *                                  可指定的键值如下：
		 *                                  [
		 *                                  "name": "李四", //成员名称。长度为0~64个字节
		 *                                  "department"=>[1,2,3], //成员所属部门id列表，不超过20个
		 *                                  "position"=> "后台工程师", //职位信息。长度为0~64个字节
		 *                                  "mobile"=> "15913215421", //手机号码。企业内必须唯一，mobile/weixinid/email三者不能同时为空
		 *                                  "gender"=> "1", //性别。1表示男性，2表示女性
		 *                                  "email"=> "zhangsan@gzdev.com", //邮箱。长度为0~64个字节。企业内必须唯一
		 *                                  "weixinid"=> "lisifordev", //微信号。企业内必须唯一。（注意：是微信号，不是微信的名字）
		 *                                  "enable": 1, //启用/禁用成员。1表示启用成员，0表示禁用成员
		 *                                  "avatar_mediaid": "2-G6nrLmr5EC3MNb_-zL1dDdzkd0p7cNliYu9V5w7o8K0",
		 *                                  //成员头像的mediaid，通过多媒体接口上传图片获得的mediaid
		 *                                  "extattr":
		 *                                  {"attrs":[{"name":"爱好","value":"旅游"},{"name":"卡号","value":"1234567234"}]}
		 *                                  //扩展属性。扩展属性需要在WEB管理端创建后才生效，否则忽略未知属性的赋值
		 *                                  ]
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return mixed
		 */
		public function saveUser($user_id, $data, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = $this->wxcapi_SaveUser($access_token, $user_id, $data);

			return $response['data'];
		}

		/**
		 * 获取签名字符串
		 *
		 * @param string      $random_str 随机字符串
		 * @param int         $cur_time   当前时间戳
		 * @param null|string $url        当前网页的URL
		 *
		 * @return string
		 */
		public function getSignature($random_str, $cur_time, $url = null){
			$url          = $url == null ? "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" : $url;
			$jsapi_ticket = $this->getJSApiTicket();
			$config       = [
				'noncestr'     => $random_str,
				'jsapi_ticket' => $jsapi_ticket,
				'timestamp'    => $cur_time,
				'url'          => $url
			];
			ksort($config);
			$str = '';
			foreach($config as $key => $val) $str .= $key.'='.$val.'&';

			return sha1(trim($str, "&"));
		}

		/**
		 * 获取媒体文件
		 *
		 * @param string      $mediaid      媒体文件ID
		 * @param int         $mode         返回数据形式 枚举为[1, 2, 0]，分别代表包含头部和相应体、只有头部和只有相应体
		 * @param string|null $access_token 接口调用凭证
		 *
		 * @return mixed
		 */
		public function getMedia($mediaid, $mode = 1, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = $this->wxcapi_GetMedia($access_token, $mediaid, $mode);

			return $response['native_str'];
		}
	}

	class WXCorpApiLib{
		public function __construct(){
		}

		/**
		 * 获取接口调用结果状态
		 *
		 * @param object|string $data 接口调用后返回的结果集，可以为原始返回字符串或者进行对象化操作后PHP对象
		 *
		 * @return bool 若接口调用成功则返回true，否则false
		 */
		private function _getResultStatus($data){
			if(gettype($data) == 'string') $data = json_decode($data);
			$var_obj  = new Type();
			$data_arr = $var_obj->parseObjectToArray($data);
			if(isset($data_arr['errcode'])){
				if($data_arr['errcode'] == 0) return true;
				else return false;
			}
			else{
				return true;
			}
		}

		/**
		 * 统一方法的调用输入模板
		 *
		 * @param bool   $status     接口调用结果状态
		 * @param mixed  $data       格式化后的接口返回数据
		 * @param string $native_str 接口返回原始数据
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的返回结果数据和native_str索引的原始返回字符串
		 */
		private function _makeResultTemplate($status, $data, $native_str){
			return [
				'status'     => $status,
				'data'       => $data,
				'native_str' => $native_str
			];
		}

		/**
		 * *获取接口调用凭证(AccessToken)接口的实现
		 *
		 * @param string $corp_id     企业号的corpid
		 * @param string $corp_secret 企业号的corpsecret
		 *
		 * @return array 返回以data索引的格式化数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_AccessToken($corp_id, $corp_secret){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$corp_id&corpsecret=$corp_secret";
			$curl_obj   = new Curl();
			$val_obj    = new Type();
			$native_str = $curl_obj->sendRequest($api_url, 'GET');
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $val_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取JSAPI调用临时凭据(JSAPI_Ticket)接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 *
		 * @return array 返回以data索引的格式化数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_JSApiTicket($access_token){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$access_token";
			$curl_obj   = new Curl();
			$val_obj    = new Type();
			$native_str = $curl_obj->sendRequest($api_url, 'GET');
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $val_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取Code的接口实现
		 *
		 * @param string $corp_id 企业号的corpid
		 * @param string $url     回调地址
		 * @param string $opt_str
		 *
		 * @return string 返回接口调用地址
		 */
		protected function wxcapi_GetCode($corp_id, $url, $opt_str){
			return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$corp_id&redirect_uri=$url&response_type=code&scope=snsapi_base&state=$opt_str#wechat_redirect";
		}

		/**
		 * *获取用户ID的接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $code         换取用户信息的Code
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_GetUserID($access_token, $code){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$access_token&code=$code";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->sendRequest($api_url, 'GET');
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取用户信息的接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $user_id      企业成员ID
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_GetUserInfo($access_token, $user_id){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&userid=$user_id";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->sendRequest($api_url, 'GET');
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *用户二次验证的接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $user_id      企业成员ID
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_UserCheck($access_token, $user_id){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/user/authsucc?access_token=$access_token&userid=$user_id";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->sendRequest($api_url, 'GET');
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *通过OpenID获取UserID的接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $openid       用户的OpenID
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_parseOpenIDToUserID($access_token, $openid){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid?access_token=$access_token";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->sendRequest($api_url, 'POST', json_encode(['openid' => $openid]));
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *通过UserID获取OpenID的接口实现
		 *
		 * @param string   $access_token 接口调用凭证
		 * @param string   $user_id      用户的OpenID
		 * @param null|int $agent_id     需要发送红包的应用ID，若只是使用微信支付和企业转账，则无需该参数
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_parseUserIDToOpenID($access_token, $user_id, $agent_id = null){
			$api_url  = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=$access_token";
			$curl_obj = new Curl();
			$var_obj  = new Type();
			if($agent_id == null) $native_str = $curl_obj->sendRequest($api_url, 'POST', json_encode(['userid' => $user_id]));
			else $native_str = $curl_obj->sendRequest($api_url, 'POST', json_encode([
				'userid'  => $user_id,
				'agentid' => $agent_id
			]));
			$result = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取部门ID下的子部门列表接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param int    $id           部门ID，注：根部门ID为1
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的部门数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_GetDepartmentList($access_token, $id = 1){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=$access_token&id=$id";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->sendRequest($api_url, 'GET');
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result->department), $native_str);
		}

		/**
		 * *获取部门ID下的成员列表接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param int    $did          部门ID，注：根部门ID为1
		 * @param int    $fetch_child  是否递归获取子部门下面的成员。枚举为[0,1]
		 * @param int    $status       0获取全部成员
		 *                             1获取已关注成员列表
		 *                             2获取禁用成员列表
		 *                             4获取未关注成员列表
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_GetUserList($access_token, $did, $fetch_child = 1, $status = 0){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=$access_token&department_id=$did&fetch_child=$fetch_child&status=$status";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->sendRequest($api_url, 'GET');
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result->userlist), $native_str);
		}

		/**
		 * *更新成员信息接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param int    $user_id      企业用户ID
		 * @param array  $data         需要保存到用户信息的数据
		 *                             可指定的键值如下：
		 *                             [
		 *                             "name": "李四", //成员名称。长度为0~64个字节
		 *                             "department"=>[1,2,3], //成员所属部门id列表，不超过20个
		 *                             "position"=> "后台工程师", //职位信息。长度为0~64个字节
		 *                             "mobile"=> "15913215421", //手机号码。企业内必须唯一，mobile/weixinid/email三者不能同时为空
		 *                             "gender"=> "1", //性别。1表示男性，2表示女性
		 *                             "email"=> "zhangsan@gzdev.com", //邮箱。长度为0~64个字节。企业内必须唯一
		 *                             "weixinid"=> "lisifordev", //微信号。企业内必须唯一。（注意：是微信号，不是微信的名字）
		 *                             "enable": 1, //启用/禁用成员。1表示启用成员，0表示禁用成员
		 *                             "avatar_mediaid": "2-G6nrLmr5EC3MNb_-zL1dDdzkd0p7cNliYu9V5w7o8K0",
		 *                             //成员头像的mediaid，通过多媒体接口上传图片获得的mediaid
		 *                             "extattr":
		 *                             {"attrs":[{"name":"爱好","value":"旅游"},{"name":"卡号","value":"1234567234"}]}
		 *                             //扩展属性。扩展属性需要在WEB管理端创建后才生效，否则忽略未知属性的赋值
		 *                             ]
		 *
		 * @return array
		 */
		protected function wxcapi_SaveUser($access_token, $user_id, $data){
			$api_url        = "https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token=$access_token";
			$data['userid'] = $user_id;
			$curl_obj       = new Curl();
			$var_obj        = new Type();
			$native_str     = $curl_obj->post($api_url, [
				'post' => true,
				'data' => json_encode($data),
			]);
			$result         = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *发送消息接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $type         消息类型，枚举为['text','image','voice','file','mpnews','news','video','mpnews_r']
		 * @param array  $data         传递的数据，具体参照对应的消息类型所需的数据
		 * @param int    $agent_id     应用ID
		 * @param array  $receiver     接收者信息
		 *                             枚举为[
		 *                             'user'=>[string, string, string, ...],
		 *                             'dept'=>[string, string, string, ...],
		 *                             'tag'=>[string, string, string, ...]
		 *                             ]
		 * @param int    $safe         是否保密消息，枚举为0|1
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_SendMessage($access_token, $type, $data, $agent_id, $receiver, $safe = 0){
			$api_url   = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=$access_token";
			$curl_obj  = new Curl();
			$var_obj   = new Type();
			$main_data = [
				'agentid' => $agent_id
			];
			if($receiver['user']){
				if(gettype($receiver['user']) == 'array') $main_data = array_merge($main_data, ['touser' => implode('|', $receiver['user'])]);
				if(gettype($receiver['user']) == 'string') $main_data = array_merge($main_data, ['touser' => $receiver['user']]);
			}
			if($receiver['dept']){
				if(gettype($receiver['dept']) == 'array') $main_data = array_merge($main_data, ['toparty' => implode('|', $receiver['dept'])]);
				if(gettype($receiver['dept']) == 'string') $main_data = array_merge($main_data, ['toparty' => $receiver['dept']]);
			}
			if($receiver['tag']){
				if(gettype($receiver['tag']) == 'array') $main_data = array_merge($main_data, ['totag' => implode('|', $receiver['tag'])]);
				if(gettype($receiver['tag']) == 'string') $main_data = array_merge($main_data, ['totag' => $receiver['tag']]);
			}
			switch(strtolower($type)){
				case 'text':
					$post_data = array_merge($main_data, [
						'msgtype' => 'text',
						'text'    => ['content' => $data],
						'safe'    => $safe
					]);
				break;
				case 'image':
				case 'voice':
				case 'file':
				case 'mpnews':
					$post_data = array_merge($main_data, [
						'msgtype' => $type,
						$type     => ['media_id' => $data['media_id']],
						'safe'    => $safe
					]);
				break;
				case 'video':
					$post_data = array_merge($main_data, [
						'msgtype' => 'video',
						'voice'   => [
							'media_id'    => $data['media_id'],
							'title'       => $data['title'],
							'description' => $data['description']
						],
						'safe'    => $safe
					]);
				break;
				case 'news':
					$article_list = [];
					for($i = 0; $i<count($data); $i++){
						array_push($article_list, [
							'title'       => $data[$i]['title'],
							'description' => $data[$i]['description'],
							'url'         => $data[$i]['url'],
							'picurl'      => $data[$i]['picurl']
						]);
					}
					$post_data = array_merge($main_data, [
						'msgtype' => 'news',
						'news'    => ['articles' => $article_list]
					]);
				break;
				case 'mpnews_r':
					$article_list = [];
					for($i = 0; $i<count($data); $i++){
						array_push($article_list, [
							'title'              => $data[$i]['title'],
							'thumb_media_id'     => $data[$i]['thumb_media_id'],
							'author'             => $data[$i]['author'],
							'content_source_url' => $data[$i]['content_source_url'],
							'content'            => $data[$i]['content'],
							'digest'             => $data[$i]['digest'],
							'show_cover_pic'     => $data[$i]['show_cover_pic']
						]);
					}
					$post_data = array_merge($main_data, [
						'msgtype' => 'mpnews',
						'mpnews'  => ['articles' => $article_list],
						'safe'    => $safe
					]);
				break;
				default:
					return null;
				break;
			}
			$native_str = $curl_obj->sendRequest($api_url, 'POST', json_encode($post_data, JSON_UNESCAPED_UNICODE));
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *下载媒体文件的接口实现（事先得完成媒体文件的上传获得media_id）
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $mediaid      媒体ID
		 * @param int    $mode         返回数据形式 枚举为[1, 2, 0]，分别代表包含头部和相应体、只有头部和只有相应体
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_GetMedia($access_token, $mediaid, $mode = 1){
			$api_url  = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$mediaid";
			$curl_obj = new Curl();
			switch($mode){
				case 1:
					$native_str = $curl_obj->get($api_url, [
						'get_header' => true
					]);
				break;
				case 2:
					$native_str = $curl_obj->get($api_url, [
						'get_header' => true,
						'no_body'    => true
					]);
				break;
				case 0:
				default:
					$native_str = $curl_obj->get($api_url);
				break;
			}
			$result = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $result, $native_str);
		}
	}

	/**
	 * Class WXCorpLocalCache
	 * 用于提供检测和维护本地JSApiTicket、AccessToken凭据的处理类
	 */
	class WXCorpLocalCache{
		private $Config = [
			'expired_time'   => null,
			'data'           => null,
			'file_name'      => '',
			'_file_name_ac_' => '/access_token_',
			'_file_name_jt_' => '/jsapi_ticket_',
			'suffix'         => '.json',
			'cache_folder'   => './wxcorp-cache',
			'active_time'    => 7000,
			'type'           => ''// ac,jt
		];

		/**
		 * 类构造函数，需要提供公众号的Appid作为参数传入
		 *
		 * @param string      $corp_id        企业号的corpid
		 * @param string      $type           凭据类型[ac,jt]
		 * @param null|string $cache_dir_path 缓存文件夹的路径
		 * @param string      $opt            文件名扩展串
		 */
		public function __construct($corp_id, $type, $cache_dir_path = null, $opt = ''){
			if($cache_dir_path != null) $this->Config['cache_folder'] = $cache_dir_path;
			switch(strtolower($type)){
				case 'ac':
					$this->Config['file_name'] = $this->Config['_file_name_ac_'];
				break;
				case 'jt':
					$this->Config['file_name'] = $this->Config['_file_name_jt_'];
				break;
				default:
					$this->Config['file_name'] = $this->Config['_file_name_ac_'];
				break;
			}
			$this->Config['file_name'] .= $corp_id.$opt.$this->Config['suffix'];
			$content = $this->_readFiles();
			if($content){
				$content = json_decode($content);
				if($this->Config['type'] == 'ac'){
					$this->Config['data']         = $content->access_token;
					$this->Config['expired_time'] = $content->expired_time;
				}
				elseif($this->Config['type'] == 'jt'){
					$this->Config['data']         = $content->jsapi_ticket;
					$this->Config['expired_time'] = $content->expired_time;
				}
			}
			else $this->_createNewFile();
		}

		/**
		 * 创建凭据所需的缓存文件夹和文件
		 *
		 * @return int 创建结果状态值[0,1,2]，分别表示未创建、只创建了文件、同时创建了文件夹和文件
		 */
		private function _createNewFile(){
			if(file_exists($this->Config['cache_folder'])){
				if(file_exists($this->Config['cache_folder'].$this->Config['file_name'])) return 0;
				else{
					try{
						$file = fopen($this->Config['cache_folder'].$this->Config['file_name'], 'w');
						fclose($file);

						return 1;
					}catch(Exception $Error){
						print_r($Error);

						return 1;
					}
				}
			}
			else{
				$r = mkdir($this->Config['cache_folder']);
				if($r){
					try{
						$file = fopen($this->Config['cache_folder'].$this->Config['file_name'], 'w');
						fclose($file);

						return 2;
					}catch(Exception $Error){
						print_r($Error);

						return 1;
					}
				}
				else{
					return 0;
				}
			}
		}

		/**
		 * 获取凭据
		 *
		 * @return string 返回存储文件中的凭据
		 */
		public function getCacheData(){
			return $this->Config['data'];
		}

		/**
		 * 读取存储文件
		 *
		 * @return string 返回文件内容
		 */
		private function _readFiles(){
			return @file_get_contents($this->Config['cache_folder'].$this->Config['file_name']);
		}

		/**
		 * 检测凭据是否过期（7200秒/个）
		 *
		 * @return bool 过期则返回true，不过期则返回false
		 */
		public function isExpired(){
			if($this->Config['expired_time'] == null) return true;
			else{
				if($this->Config['expired_time']<=time()) return true;
				else return false;
			}
		}

		/**
		 * 检测凭据是否超过每日限制（2000次/天）
		 *todo
		 *
		 * @param string $content 获取凭据接口返回的原始JSON字符串
		 *
		 * @return bool 超过则返回true，不超过则返回false
		 */
		public function isLimited($content){
			$content = json_decode($content);
			if($content) ;

			return true;
		}

		/**
		 * 存储凭据至本地文件
		 *
		 * @param string $content 写入存储文件的内容
		 *
		 * @return int 存储成功返回1，否则返回0
		 */
		public function saveCacheData($content){
			try{
				$file = fopen($this->Config['cache_folder'].$this->Config['file_name'], 'w');
				fwrite($file, $content);
				fclose($file);
			}catch(Exception $error){
				return 0;
			}

			return 1;
		}
	}
