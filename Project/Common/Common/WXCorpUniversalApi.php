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
		private $WXCorpConfigs = ['corpid' => null, 'corpsecret' => null];

		/**
		 * 手动设置corpid
		 *
		 * @param string $corpid 企业号的corpid
		 */
		public function setCorpid($corpid){
			$this->WXCorpConfigs['corpid'] = $corpid;
		}

		/**
		 * 手动设置corpsecret
		 *
		 * @param string $corpsecret 企业号的appsercet
		 */
		public function setCorpsercet($corpsecret){
			$this->WXCorpConfigs['corpsecret'] = $corpsecret;
		}

		/**
		 * 返回企业号的基本参数配置
		 *
		 * @return array 格式为['corpid'=>string, 'corpsecret'=>string]
		 */
		public function getConfigs(){
			return $this->WXCorpConfigs;
		}

		/**
		 * WXCorpUniversalApi constructor.
		 * 用于初始化公众号的基本配置信息
		 *
		 * @param null|string $corpid     企业号的appid
		 * @param null|string $corpsecret 企业号的appsecret
		 */
		public function __construct($corpid = null, $corpsecret = null){
			if($corpid != null) $this->WXCorpConfigs['corpid'] = $corpid;
			if($corpsecret != null) $this->WXCorpConfigs['corpsecret'] = $corpsecret;
		}

		/**
		 * 获取AccessToken
		 * TODO 检测是否超过限制
		 *
		 * @return string|array 成功则返回AccessToken，否则返回错误信息
		 */
		public function getAccessToken(){
			$cdobj = new WXCorpLocalCache($this->WXCorpConfigs['corpid'], 'ac', getcwd().'/Project/Runtime/Wxcorp-Cache');
			if($cdobj->isExpired()){
				$response = $this->wxcapi_AccessToken($this->WXCorpConfigs['corpid'], $this->WXCorpConfigs['corpsecret']);
				if($response['status']){
					$cdobj->saveCacheData(json_encode([
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
				return $cdobj->getCacheData();
			}
		}

		/**
		 * 获取JSPI_Ticket
		 * TODO 检测是否超过限制
		 *
		 * @param string|null $accesstoken
		 *
		 * @return array|string 成功则返回JSPI_Ticket，否则返回错误信息
		 */
		public function getJSApiTicket($accesstoken = null){
			$cdobj       = new WXCorpLocalCache($this->WXCorpConfigs['corpid'], 'jt', getcwd().'/Project/Runtime/Wxcorp-Cache');
			$accesstoken = $accesstoken == null ? $this->getAccessToken() : $accesstoken;
			if($cdobj->isExpired()){
				$response = $this->wxcapi_JSApiTicket($accesstoken);
				if($response['status']){
					$cdobj->saveCacheData(json_encode([
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
				return $cdobj->getCacheData();
			}
		}

		/**
		 * 获取Code
		 * *这里做了页面的定向跳转，且需要被调用的页面在微信中打开！
		 * *回调地址中将会携带code参数
		 *
		 * @param string $url    回调地址
		 * @param string $data   附带数据
		 * @param string $corpid 企业号的appid
		 */
		public function getCode($url, $data = '', $corpid = null){
			if($corpid === null) $corpid = $this->WXCorpConfigs['corpid'];
			$url  = urlencode($url);
			$data = urlencode((string)$data);
			header('Location:'.$this->wxcapi_GetCode($corpid, $url, $data));
		}

		/**
		 * 获取用户ID
		 * *有是否为企业成员的区别
		 *
		 * @param string      $code        换取用户信息的Code
		 * @param null|string $accesstoken 接口调用凭证
		 *
		 * @return array 若成功则返回以type索引的类型值【1表示企业成员，0表示非企业成员】，以id索引的用户ID值
		 *               否则返回错误信息
		 */
		public function getUserID($code, $accesstoken = null){
			$accesstoken = $accesstoken == null ? $this->getAccessToken() : $accesstoken;
			$response    = $this->wxcapi_GetUserID($accesstoken, $code);
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
		 * @param int|string  $id          UserID或OpenID
		 * @param int         $type        转换类型【1表示通过UserID获取OpenID，0表示通过OpenID获取UserID】
		 * @param null|int    $agentid     需要发送红包的应用ID，若只是使用微信支付和企业转账，则无需该参数
		 * @param null|string $accesstoken 接口调用凭证
		 *
		 * @return bool|string|int|array 若成功则返回相应的ID值，否则返回错误信息或者$type参数赋值不正确直接返回false
		 */
		public function parseID($id, $type, $agentid = null, $accesstoken){
			$accesstoken = $accesstoken == null ? $this->getAccessToken() : $accesstoken;
			switch($type){
				case 0:
					$response = $this->wxcapi_parseOpenIDToUserID($accesstoken, $id);
					if($response['status']) return $response['data']['userid'];
					else return $response['data'];
				break;
				case 1:
					$response = $this->wxcapi_parseUserIDToOpenID($accesstoken, $id, $agentid);
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
		 * @param null|string $corpid 企业号的corpid
		 *
		 * @return string|null 若成功则返回UserID，否则返回null
		 */
		public function getID($corpid = null){
			if(isset($_GET['code'])){
				$code     = I('get.code');
				$response = $this->getUserID($code);
				if($response['type'] == 1) return $response['id'];
				else return null;
			}
			else{
				if($corpid === null) $corpid = $this->WXCorpConfigs['corpid'];
				$redirecturl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
				header('Location:'.$this->wxcapi_GetCode($corpid, $redirecturl, 'getID'));
				exit;
			}
		}

		/**
		 * 进行二次验证
		 *
		 * @param int         $userid      企业用户ID
		 * @param null|string $accesstoken 接口调用凭证
		 *
		 * @return array 返回接口调用结果
		 */
		public function check($userid, $accesstoken = null){
			$accesstoken = $accesstoken == null ? $this->getAccessToken() : $accesstoken;
			$response    = $this->wxcapi_UserCheck($accesstoken, $userid);

			return $response['data'];
		}

		/**
		 * 获取成员信息
		 *
		 * @param int         $userid      企业用户ID
		 * @param null|string $accesstoken 接口调用凭证
		 *
		 * @return array 返回成员信息和接口调用结果
		 */
		public function getUserInfo($userid, $accesstoken = null){
			$accesstoken = $accesstoken == null ? $this->getAccessToken() : $accesstoken;
			$response    = $this->wxcapi_GetUserInfo($accesstoken, $userid);

			return $response['data'];
		}

		/**
		 * 发送消息
		 *
		 * @param string      $type        消息类型，枚举为['text','image','voice','file','mpnews','news','video','mpnews_r']
		 * @param array       $data        传递的数据，具体参照对应的消息类型所需的数据
		 * @param int         $agentid     应用ID
		 * @param array       $receiver    接收者信息
		 *                                 可指定的键值如下：
		 *                                 [
		 *                                 'user'=>[string, string, string, ...],
		 *                                 'dept'=>[string, string, string, ...],
		 *                                 'tag'=>[string, string, string, ...]
		 *                                 ]
		 * @param int         $safe        是否保密消息，枚举为0|1
		 * @param null|string $accesstoken 接口调用凭证
		 *
		 * @return array 返回调用结果
		 */
		public function sendMessage($type, $data, $agentid, $receiver, $safe = 0, $accesstoken = null){
			$accesstoken = $accesstoken == null ? $this->getAccessToken() : $accesstoken;

			return $this->wxcapi_SendMessage($accesstoken, $type, $data, $agentid, $receiver, $safe);
		}

		/**
		 * 获取部门列表
		 *
		 * @param int         $id          部门ID号（根部门为1）
		 * @param null|string $accesstoken 接口调用凭证
		 *
		 * @return array 返回部门列表数据和接口调用结果
		 */
		public function getDepartmentList($id = 1, $accesstoken = null){
			$accesstoken = $accesstoken == null ? $this->getAccessToken() : $accesstoken;
			$response    = $this->wxcapi_GetDepartmentList($accesstoken, $id);

			return $response['data'];
		}

		/**
		 * 获取部门下的用户列表
		 *
		 * @param int         $id          部门ID号（根部门为1）
		 * @param int         $fetchchild  是否递归获取子部门下面的成员。枚举为[0,1]
		 * @param int         $status      0获取全部成员
		 *                                 1获取已关注成员列表
		 *                                 2获取禁用成员列表
		 *                                 4获取未关注成员列表
		 * @param null|string $accesstoken 接口调用凭证
		 *
		 * @return mixed
		 */
		public function getUserList($id, $fetchchild = 1, $status = 0, $accesstoken = null){
			$accesstoken = $accesstoken == null ? $this->getAccessToken() : $accesstoken;
			$response    = $this->wxcapi_GetUserList($accesstoken, $id, $fetchchild, $status);

			return $response['data'];
		}

		/**
		 * 更新成员信息
		 *
		 * @param int         $userid      企业用户ID
		 * @param array       $data        需要保存到用户信息的数据
		 *                                 可指定的键值如下：
		 *                                 [
		 *                                 "name": "李四", //成员名称。长度为0~64个字节
		 *                                 "department"=>[1,2,3], //成员所属部门id列表，不超过20个
		 *                                 "position"=> "后台工程师", //职位信息。长度为0~64个字节
		 *                                 "mobile"=> "15913215421", //手机号码。企业内必须唯一，mobile/weixinid/email三者不能同时为空
		 *                                 "gender"=> "1", //性别。1表示男性，2表示女性
		 *                                 "email"=> "zhangsan@gzdev.com", //邮箱。长度为0~64个字节。企业内必须唯一
		 *                                 "weixinid"=> "lisifordev", //微信号。企业内必须唯一。（注意：是微信号，不是微信的名字）
		 *                                 "enable": 1, //启用/禁用成员。1表示启用成员，0表示禁用成员
		 *                                 "avatar_mediaid": "2-G6nrLmr5EC3MNb_-zL1dDdzkd0p7cNliYu9V5w7o8K0",
		 *                                 //成员头像的mediaid，通过多媒体接口上传图片获得的mediaid
		 *                                 "extattr":
		 *                                 {"attrs":[{"name":"爱好","value":"旅游"},{"name":"卡号","value":"1234567234"}]}
		 *                                 //扩展属性。扩展属性需要在WEB管理端创建后才生效，否则忽略未知属性的赋值
		 *                                 ]
		 * @param null|string $accesstoken 接口调用凭证
		 *
		 * @return mixed
		 */
		public function saveUser($userid, $data, $accesstoken = null){
			$accesstoken = $accesstoken == null ? $this->getAccessToken() : $accesstoken;
			$response    = $this->wxcapi_SaveUser($accesstoken, $userid, $data);

			return $response['data'];
		}

		/**
		 * 获取签名字符串
		 *
		 * @param string      $randomstr 随机字符串
		 * @param int         $curtime   当前时间戳
		 * @param null|string $url       当前网页的URL
		 *
		 * @return string
		 */
		public function getSignature($randomstr, $curtime, $url = null){
			$url         = $url == null ? "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" : $url;
			$jsapiticket = $this->getJSApiTicket();
			$config      = [
				'noncestr' => $randomstr,
				'jsapi_ticket' => $jsapiticket,
				'timestamp' => $curtime,
				'url' => $url
			];
			ksort($config);
			$str = '';
			foreach($config as $key => $val) $str .= $key.'='.$val.'&';

			return sha1(trim($str, "&"));
		}

		/**
		 * 获取媒体文件
		 *
		 * @param string      $mediaid     媒体文件ID
		 * @param int         $mode        返回数据形式 枚举为[1, 2, 0]，分别代表包含头部和相应体、只有头部和只有相应体
		 * @param string|null $accesstoken 接口调用凭证
		 *
		 * @return mixed
		 */
		public function getMedia($mediaid, $mode = 1, $accesstoken = null){
			$accesstoken = $accesstoken == null ? $this->getAccessToken() : $accesstoken;
			$response    = $this->wxcapi_GetMedia($accesstoken, $mediaid, $mode);

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
			$varobj = new Type();
			$datarr = $varobj->parseObjectToArray($data);
			if(isset($datarr['errcode'])){
				if($datarr['errcode'] == 0) return true;
				else return false;
			}
			else{
				return true;
			}
		}

		/**
		 * 统一方法的调用输入模板
		 *
		 * @param bool   $status    接口调用结果状态
		 * @param mixed  $data      格式化后的接口返回数据
		 * @param string $nativestr 接口返回原始数据
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的返回结果数据和native_str索引的原始返回字符串
		 */
		private function _makeResultTemplate($status, $data, $nativestr){
			return [
				'status' => $status,
				'data' => $data,
				'native_str' => $nativestr
			];
		}

		/**
		 * *获取接口调用凭证(AccessToken)接口的实现
		 *
		 * @param string $corpid     企业号的corpid
		 * @param string $corpsecret 企业号的corpsecret
		 *
		 * @return array 返回以data索引的格式化数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_AccessToken($corpid, $corpsecret){
			$apiurl    = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$corpid&corpsecret=$corpsecret";
			$curlobj   = new Curl();
			$valobj    = new Type();
			$nativestr = $curlobj->sendRequest($apiurl, 'GET');
			$result    = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $valobj->parseObjectToArray($result), $nativestr);
		}

		/**
		 * *获取JSAPI调用临时凭据(JSAPI_Ticket)接口的实现
		 *
		 * @param string $accesstoken 接口调用凭证
		 *
		 * @return array 返回以data索引的格式化数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_JSApiTicket($accesstoken){
			$apiurl    = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accesstoken";
			$curlobj   = new Curl();
			$valobj    = new Type();
			$nativestr = $curlobj->sendRequest($apiurl, 'GET');
			$result    = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $valobj->parseObjectToArray($result), $nativestr);
		}

		/**
		 * *获取Code的接口实现
		 *
		 * @param string $corpid 企业号的corpid
		 * @param string $url    回调地址
		 * @param string $optstr
		 *
		 * @return string 返回接口调用地址
		 */
		protected function wxcapi_GetCode($corpid, $url, $optstr){
			return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$corpid&redirect_uri=$url&response_type=code&scope=snsapi_base&state=$optstr#wechat_redirect";
		}

		/**
		 * *获取用户ID的接口实现
		 *
		 * @param string $accesstoken 接口调用凭证
		 * @param string $code        换取用户信息的Code
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_GetUserID($accesstoken, $code){
			$apiurl    = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$accesstoken&code=$code";
			$curlobj   = new Curl();
			$varobj    = new Type();
			$nativestr = $curlobj->sendRequest($apiurl, 'GET');
			$result    = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $varobj->parseObjectToArray($result), $nativestr);
		}

		/**
		 * *获取用户信息的接口实现
		 *
		 * @param string $accesstoken 接口调用凭证
		 * @param string $userid      企业成员ID
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_GetUserInfo($accesstoken, $userid){
			$apiurl    = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=$accesstoken&userid=$userid";
			$curlobj   = new Curl();
			$varobj    = new Type();
			$nativestr = $curlobj->sendRequest($apiurl, 'GET');
			$result    = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $varobj->parseObjectToArray($result), $nativestr);
		}

		/**
		 * *用户二次验证的接口实现
		 *
		 * @param string $accesstoken 接口调用凭证
		 * @param string $userid      企业成员ID
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_UserCheck($accesstoken, $userid){
			$apiurl    = "https://qyapi.weixin.qq.com/cgi-bin/user/authsucc?access_token=$accesstoken&userid=$userid";
			$curlobj   = new Curl();
			$varobj    = new Type();
			$nativestr = $curlobj->sendRequest($apiurl, 'GET');
			$result    = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $varobj->parseObjectToArray($result), $nativestr);
		}

		/**
		 * *通过OpenID获取UserID的接口实现
		 *
		 * @param string $accesstoken 接口调用凭证
		 * @param string $openid      用户的OpenID
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_parseOpenIDToUserID($accesstoken, $openid){
			$apiurl    = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid?access_token=$accesstoken";
			$curlobj   = new Curl();
			$varobj    = new Type();
			$nativestr = $curlobj->sendRequest($apiurl, 'POST', json_encode(['openid' => $openid]));
			$result    = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $varobj->parseObjectToArray($result), $nativestr);
		}

		/**
		 * *通过UserID获取OpenID的接口实现
		 *
		 * @param string   $accesstoken 接口调用凭证
		 * @param string   $userid      用户的OpenID
		 * @param null|int $agentid     需要发送红包的应用ID，若只是使用微信支付和企业转账，则无需该参数
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_parseUserIDToOpenID($accesstoken, $userid, $agentid = null){
			$apiurl  = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=$accesstoken";
			$curlobj = new Curl();
			$varobj  = new Type();
			if($agentid == null) $nativestr = $curlobj->sendRequest($apiurl, 'POST', json_encode(['userid' => $userid]));
			else $nativestr = $curlobj->sendRequest($apiurl, 'POST', json_encode([
				'userid' => $userid,
				'agentid' => $agentid
			]));
			$result = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $varobj->parseObjectToArray($result), $nativestr);
		}

		/**
		 * *获取部门ID下的子部门列表接口的实现
		 *
		 * @param string $accesstoken 接口调用凭证
		 * @param int    $id          部门ID，注：根部门ID为1
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的部门数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_GetDepartmentList($accesstoken, $id = 1){
			$apiurl    = "https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=$accesstoken&id=$id";
			$curlobj   = new Curl();
			$varobj    = new Type();
			$nativestr = $curlobj->sendRequest($apiurl, 'GET');
			$result    = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $varobj->parseObjectToArray($result->department), $nativestr);
		}

		/**
		 * *获取部门ID下的成员列表接口的实现
		 *
		 * @param string $accesstoken 接口调用凭证
		 * @param int    $did         部门ID，注：根部门ID为1
		 *
		 * @param int    $fetchchild  是否递归获取子部门下面的成员。枚举为[0,1]
		 * @param int    $status      0获取全部成员
		 *                            1获取已关注成员列表
		 *                            2获取禁用成员列表
		 *                            4获取未关注成员列表
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_GetUserList($accesstoken, $did, $fetchchild = 1, $status = 0){
			$apiurl    = "https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=$accesstoken&department_id=$did&fetch_child=$fetchchild&status=$status";
			$curlobj   = new Curl();
			$varobj    = new Type();
			$nativestr = $curlobj->sendRequest($apiurl, 'GET');
			$result    = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $varobj->parseObjectToArray($result->userlist), $nativestr);
		}

		/**
		 * *更新成员信息接口的实现
		 *
		 * @param string $accesstoken  接口调用凭证
		 * @param int    $userid       企业用户ID
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
		protected function wxcapi_SaveUser($accesstoken, $userid, $data){
			$apiurl         = "https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token=$accesstoken";
			$data['userid'] = $userid;
			$curlobj        = new Curl();
			$varobj         = new Type();
			$nativestr      = $curlobj->post($apiurl, [
				'post' => true,
				'data' => json_encode($data),
			]);
			$result         = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $varobj->parseObjectToArray($result), $nativestr);
		}

		/**
		 * *发送消息接口实现
		 *
		 * @param string $accesstoken 接口调用凭证
		 * @param string $type        消息类型，枚举为['text','image','voice','file','mpnews','news','video','mpnews_r']
		 * @param array  $data        传递的数据，具体参照对应的消息类型所需的数据
		 * @param int    $agentid     应用ID
		 * @param array  $receiver    接收者信息
		 *                            枚举为[
		 *                            'user'=>[string, string, string, ...],
		 *                            'dept'=>[string, string, string, ...],
		 *                            'tag'=>[string, string, string, ...]
		 *                            ]
		 * @param int    $safe        是否保密消息，枚举为0|1
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_SendMessage($accesstoken, $type, $data, $agentid, $receiver, $safe = 0){
			$apiurl   = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=$accesstoken";
			$curlobj  = new Curl();
			$varobj   = new Type();
			$maindata = [
				'agentid' => $agentid
			];
			if($receiver['user']){
				if(gettype($receiver['user']) == 'array') $maindata = array_merge($maindata, ['touser' => implode('|', $receiver['user'])]);
				if(gettype($receiver['user']) == 'string') $maindata = array_merge($maindata, ['touser' => $receiver['user']]);
			}
			if($receiver['dept']){
				if(gettype($receiver['dept']) == 'array') $maindata = array_merge($maindata, ['toparty' => implode('|', $receiver['dept'])]);
				if(gettype($receiver['dept']) == 'string') $maindata = array_merge($maindata, ['toparty' => $receiver['dept']]);
			}
			if($receiver['tag']){
				if(gettype($receiver['tag']) == 'array') $maindata = array_merge($maindata, ['totag' => implode('|', $receiver['tag'])]);
				if(gettype($receiver['tag']) == 'string') $maindata = array_merge($maindata, ['totag' => $receiver['tag']]);
			}
			switch(strtolower($type)){
				case 'text':
					$postdata = array_merge($maindata, [
						'msgtype' => 'text',
						'text' => ['content' => $data],
						'safe' => $safe
					]);
				break;
				case 'image':
				case 'voice':
				case 'file':
				case 'mpnews':
					$postdata = array_merge($maindata, [
						'msgtype' => $type,
						$type => ['media_id' => $data['media_id']],
						'safe' => $safe
					]);
				break;
				case 'video':
					$postdata = array_merge($maindata, [
						'msgtype' => 'video',
						'voice' => [
							'media_id' => $data['media_id'],
							'title' => $data['title'],
							'description' => $data['description']
						],
						'safe' => $safe
					]);
				break;
				case 'news':
					$articlelist = [];
					for($i = 0; $i<count($data); $i++){
						array_push($articlelist, [
							'title' => $data[$i]['title'],
							'description' => $data[$i]['description'],
							'url' => $data[$i]['url'],
							'picurl' => $data[$i]['picurl']
						]);
					}
					$postdata = array_merge($maindata, [
						'msgtype' => 'news',
						'news' => ['articles' => $articlelist]
					]);
				break;
				case 'mpnews_r':
					$articlelist = [];
					for($i = 0; $i<count($data); $i++){
						array_push($articlelist, [
							'title' => $data[$i]['title'],
							'thumb_media_id' => $data[$i]['thumb_media_id'],
							'author' => $data[$i]['author'],
							'content_source_url' => $data[$i]['content_source_url'],
							'content' => $data[$i]['content'],
							'digest' => $data[$i]['digest'],
							'show_cover_pic' => $data[$i]['show_cover_pic']
						]);
					}
					$postdata = array_merge($maindata, [
						'msgtype' => 'mpnews',
						'mpnews' => ['articles' => $articlelist],
						'safe' => $safe
					]);
				break;
				default:
					return null;
				break;
			}
			$nativestr = $curlobj->sendRequest($apiurl, 'POST', json_encode($postdata, JSON_UNESCAPED_UNICODE));
			$result    = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $varobj->parseObjectToArray($result), $nativestr);
		}

		/**
		 * *下载媒体文件的接口实现（事先得完成媒体文件的上传获得media_id）
		 *
		 * @param string $accesstoken 接口调用凭证
		 * @param string $mediaid     媒体ID
		 * @param int    $mode        返回数据形式 枚举为[1, 2, 0]，分别代表包含头部和相应体、只有头部和只有相应体
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和native_str索引的原始返回字符串
		 */
		protected function wxcapi_GetMedia($accesstoken, $mediaid, $mode = 1){
			$apiurl  = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=$accesstoken&media_id=$mediaid";
			$curlobj = new Curl();
			switch($mode){
				case 1:
					$nativestr = $curlobj->get($apiurl, [
						'get_header' => true
					]);
				break;
				case 2:
					$nativestr = $curlobj->get($apiurl, [
						'get_header' => true,
						'no_body' => true
					]);
				break;
				case 0:
				default:
					$nativestr = $curlobj->get($apiurl);
				break;
			}
			$result = json_decode($nativestr);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $result, $nativestr);
		}
	}

	/**
	 * Class WXCorpLocalCache
	 * 用于提供检测和维护本地JSApiTicket、AccessToken凭据的处理类
	 */
	class WXCorpLocalCache{
		private $Config = [
			'expired_time' => null,
			'data' => null,
			'file_name' => '',
			'_file_name_ac_' => '/access_token_',
			'_file_name_jt_' => '/jsapi_ticket_',
			'suffix' => '.json',
			'cache_folder' => './wxcorp-cache',
			'active_time' => 7000,
			'type' => ''// ac,jt
		];

		/**
		 * 类构造函数，需要提供公众号的Appid作为参数传入
		 *
		 * @param string      $corpid       企业号的corpid
		 * @param string      $type         凭据类型[ac,jt]
		 * @param null|string $cachedirpath 缓存文件夹的路径
		 * @param string      $opt          文件名扩展串
		 */
		public function __construct($corpid, $type, $cachedirpath = null, $opt = ''){
			if($cachedirpath != null) $this->Config['cache_folder'] = $cachedirpath;
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
			$this->Config['file_name'] .= $corpid.$opt.$this->Config['suffix'];
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
