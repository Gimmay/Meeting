<?php
	/**
	 * Create by PhpStorm
	 * User: Quasar
	 * Time: 2016/3/30 10:52
	 */
	namespace Quasar\Wechat;

	use Exception;
	use Quasar\Curl;
	use Quasar\Type;

	header("Content-type:text/html; charset=utf-8");

	/**
	 * Class WechatEAApi
	 * 用于提供实现微信企业号API的操作类
	 *
	 * @see Type
	 * @see Curl
	 */
	class WechatEAApi extends WechatEANativeApi{
		private $_config = [
			'corpID'          => null, // 企业号corpid
			'corpSecret'      => null, //企业号corpsecret
			'cacheActiveTime' => 7000 // 凭据缓存的生效时间
		];

		/**
		 * WechatEAApi constructor.
		 * 用于初始化公众号的基本配置信息
		 *
		 * @param null|string $corp_id     企业号的corpid
		 * @param null|string $corp_secret 企业号的corpsecret
		 */
		public function __construct($corp_id = null, $corp_secret = null){
			if($corp_id != null) $this->_config['corpID'] = $corp_id;
			if($corp_secret != null) $this->_config['corpSecret'] = $corp_secret;
		}
		/*
		 ************************************************************************************************
		 ************************************************************************************************
			通用
		 ************************************************************************************************
		 ************************************************************************************************
		 */
		/**
		 * 返回企业号的基本参数配置
		 *
		 * @return array 格式为['corpID'=>string, 'corpSecret'=>string]
		 */
		public function getConfigs(){
			return $this->_config;
		}

		/**
		 * 手动设置corpid
		 *
		 * @param string $corp_id 企业号的corpid
		 */
		public function setCorpID($corp_id){
			$this->_config['corpID'] = $corp_id;
		}

		/**
		 * 手动设置corpsecret
		 *
		 * @param string $corp_secret 企业号的corpsercet
		 */
		public function setCorpSecret($corp_secret){
			$this->_config['corpSecret'] = $corp_secret;
		}

		/**
		 * 获取accesstoken
		 *
		 * @param null|string $cache_path 凭据缓存路径
		 *
		 * @return array|string 成功则返回accesstoken，否则返回错误信息
		 */
		public function getAccessToken($cache_path = null){
			$cd_obj = new WechatEACache($this->_config['corpID'], 'ac', $cache_path);
			if($cd_obj->isExpired()){
				$response = parent::native_GetAccessToken($this->_config['corpID'], $this->_config['corpSecret']);
				if($response['status']){
					$cd_obj->saveCacheData(json_encode([
						'accessToken' => $response['data']['access_token'],
						'expiredTime' => time()+$this->_config['cacheActiveTime']
					]));

					return $response['data']['access_token'];
				}
				else{
					if($cd_obj->isLimited($response['nativeStr'])) return $response['data']['errmsg'];
					else return $response['data'];
				}
			}
			else{
				return $cd_obj->getCacheData();
			}
		}
		/*
		 ************************************************************************************************
		 ************************************************************************************************
			网页
		 ************************************************************************************************
		 ************************************************************************************************
		 */
		/**
		 * 获取code
		 * *这里做了页面的定向跳转，且需要被调用的页面在微信中打开！
		 * *回调地址中将会携带code参数
		 *
		 * @param string $url     回调地址
		 * @param string $data    附带数据
		 * @param string $corp_id 企业号的corpid
		 */
		public function getCode($url, $data = '', $corp_id = null){
			if($corp_id === null) $corp_id = $this->_config['corpID'];
			$url  = urlencode($url);
			$data = urlencode((string)$data);
			header('Location:'.parent::native_GetCode($corp_id, $url, $data));
		}

		/**
		 * 获取成员id
		 * *有是否为企业成员的区别
		 *
		 * @param string      $code         换取用户信息的code
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 若成功则返回以type索引的类型值【1表示企业成员，0表示非企业成员】，以id索引的成员id值
		 *               否则返回错误信息
		 */
		public function getUserID($code, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_GetUserID($access_token, $code);
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
		 * 获取当前微信账号的成员id值
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
				if($corp_id === null) $corp_id = $this->_config['corpID'];
				$redirect_url = urlencode("$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
				header('Location:'.parent::native_GetCode($corp_id, $redirect_url, 'getID'));
				exit;
			}
		}

		/*
		 ************************************************************************************************
		 ************************************************************************************************
			消息
		 ************************************************************************************************
		 ************************************************************************************************
		 */
		/**
		 * 发送消息
		 *
		 * @param string       $type         消息类型，枚举为['text','image','voice','file','mpnews','news','video','mpnews_r']
		 * @param array|string $data         传递的数据，具体参照对应的消息类型所需的数据
		 * @param int          $agent_id     应用ID
		 * @param array        $receiver     接收者信息
		 *                                   枚举为[
		 *                                   'user'=>[string, string, string, ...],
		 *                                   'dept'=>[string, string, string, ...],
		 *                                   'tag'=>[string, string, string, ...]
		 *                                   ]
		 * @param int          $safe         是否保密消息，枚举为0|1
		 * @param null|string  $access_token 接口调用凭证
		 *
		 * @return array 返回调用结果
		 */
		public function sendMessage($type, $data, $agent_id, $receiver, $safe = 0, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$post         = [
				'agentid' => $agent_id
			];
			if(isset($receiver['user'])){
				if(gettype($receiver['user']) == 'array') $post = array_merge($post, ['touser' => implode('|', $receiver['user'])]);
				if(gettype($receiver['user']) == 'string') $post = array_merge($post, ['touser' => $receiver['user']]);
			}
			if(isset($receiver['dept'])){
				if(gettype($receiver['dept']) == 'array') $post = array_merge($post, ['toparty' => implode('|', $receiver['dept'])]);
				if(gettype($receiver['dept']) == 'string') $post = array_merge($post, ['toparty' => $receiver['dept']]);
			}
			if(isset($receiver['tag'])){
				if(gettype($receiver['tag']) == 'array') $post = array_merge($post, ['totag' => implode('|', $receiver['tag'])]);
				if(gettype($receiver['tag']) == 'string') $post = array_merge($post, ['totag' => $receiver['tag']]);
			}
			switch(strtolower($type)){
				case 'text':
					$post = array_merge($post, [
						'msgtype' => 'text',
						'text'    => ['content' => $data],
						'safe'    => $safe
					]);
				break;
				case 'image':
				case 'voice':
				case 'file':
				case 'mpnews':
					$post = array_merge($post, [
						'msgtype' => $type,
						$type     => ['media_id' => $data['media_id']],
						'safe'    => $safe
					]);
				break;
				case 'video':
					$post = array_merge($post, [
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
					$post = array_merge($post, [
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
					$post = array_merge($post, [
						'msgtype' => 'mpnews',
						'mpnews'  => ['articles' => $article_list],
						'safe'    => $safe
					]);
				break;
				default:
					return null;
				break;
			}

			return parent::native_SendMessage($access_token, $post);
		}
		/*
		 ************************************************************************************************
		 ************************************************************************************************
			部门&成员
		 ************************************************************************************************
		 ************************************************************************************************
		 */
		/**
		 * 获取部门列表
		 *
		 * @param int         $id           部门id号（根部门为1）
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回部门列表数据和接口调用结果
		 */
		public function getDepartmentList($id = 1, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_GetDepartmentList($access_token, $id);

			return $response['data'];
		}

		/**
		 * 更新成员信息
		 *
		 * @param int         $user_id      企业成员id
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
		 * @return array 返回调用结果
		 */
		public function saveUser($user_id, $data, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_SaveUser($access_token, $user_id, $data);

			return $response['data'];
		}

		/**
		 * 获取部门下的用户列表
		 *
		 * @param int         $id           部门id号（根部门为1）
		 * @param int         $fetch_child  是否递归获取子部门下面的成员。枚举为[0,1]
		 * @param int         $status       0获取全部成员
		 *                                  1获取已关注成员列表
		 *                                  2获取禁用成员列表
		 *                                  4获取未关注成员列表
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回调用结果和员工列表
		 */
		public function getUserList($id, $fetch_child = 1, $status = 0, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_GetUserList($access_token, $id, $fetch_child, $status);

			return $response['data'];
		}

		/**
		 * 获取成员信息
		 *
		 * @param int         $user_id      企业成员id
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回成员信息和接口调用结果
		 */
		public function getUserInfo($user_id, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_GetUserInfo($access_token, $user_id);

			return $response['data'];
		}

		/**
		 * 企业号成员id和微信openid的相互转换
		 * *若不是企业成员，使用openid获取userid会提示openid非法
		 *
		 * @param int|string  $id           userid或openid
		 * @param int         $type         转换类型【1表示通过userid获取openid，0表示通过openid获取userid】
		 * @param null|int    $agent_id     需要发送红包的应用id，若只是使用微信支付和企业转账，则无需该参数
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return bool|string|int|array 若成功则返回相应的id值，否则返回错误信息或者$type参数赋值不正确直接返回false
		 */
		public function parseID($id, $type, $agent_id = null, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			switch($type){
				case 0:
					$response = parent::native_ParseOpenIDToUserID($access_token, $id);
					if($response['status']) return $response['data']['userid'];
					else return $response['data'];
				break;
				case 1:
					$response = parent::native_ParseUserIDToOpenID($access_token, $id, $agent_id);
					if($response['status']) return $response['data']['openid'];
					else return $response['data'];
				break;
				default:
					return false;
			}
		}

		/**
		 * 进行二次验证
		 *
		 * @param int         $user_id      企业成员id
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回接口调用结果
		 */
		public function check($user_id, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_CheckUser($access_token, $user_id);

			return $response['data'];
		}
		/*
		 ************************************************************************************************
		 ************************************************************************************************
			JSApi
		 ************************************************************************************************
		 ************************************************************************************************
		 */
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
		 * 获取jsapi_ticket
		 *
		 * @param null|string $access_token 接口调用凭证
		 * @param null|string $cache_path   凭据缓存路径
		 *
		 * @return array|string 成功则返回jsapi_ticket，否则返回错误信息
		 */
		public function getJSApiTicket($access_token = null, $cache_path = null){
			$cd_obj       = new WechatEACache($this->_config['corpID'], 'jt', $cache_path);
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			if($cd_obj->isExpired()){
				$response = parent::native_GetJSApiTicket($access_token);
				if($response['status']){
					$cd_obj->saveCacheData(json_encode([
						'jsApiTicket' => $response['data']['ticket'],
						'expiredTime' => time()+$this->_config['cacheActiveTime']
					]));

					return $response['data']['ticket'];
				}
				else{
					if($cd_obj->isLimited($response['nativeStr'])) return $response['data']['errmsg'];
					else return $response['data'];
				}
			}
			else{
				return $cd_obj->getCacheData();
			}
		}
		/*
		 ************************************************************************************************
		 ************************************************************************************************
			素材
		 ************************************************************************************************
		 ************************************************************************************************
		 */
		/**
		 * 获取媒体文件
		 *
		 * @param string      $media_id     媒体文件ID
		 * @param int         $mode         返回数据形式 枚举为[1, 2, 0]，分别代表包含头部和相应体、只有头部和只有相应体
		 * @param string|null $access_token 接口调用凭证
		 *
		 * @return mixed
		 */
		public function getMedia($media_id, $mode = 1, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_GetMedia($access_token, $media_id, $mode);

			return $response['nativeStr'];
		}
	}

	class WechatEANativeApi{
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
		 * @return array 返回以status索引的接口调用结果，以data索引的返回结果数据和nativeStr索引的原始返回字符串
		 */
		private function _makeResultTemplate($status, $data, $native_str){
			return [
				'status'    => $status,
				'data'      => $data,
				'nativeStr' => $native_str
			];
		}

		/**
		 * *获取接口调用凭证(accesstoken)接口的实现
		 *
		 * @param string $corp_id     企业号的corpid
		 * @param string $corp_secret 企业号的corpsecret
		 *
		 * @return array 返回以data索引的格式化数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetAccessToken($corp_id, $corp_secret){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$corp_id&corpsecret=$corp_secret";
			$curl_obj   = new Curl();
			$val_obj    = new Type();
			$native_str = $curl_obj->get($api_url);
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $val_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取JS-SDK调用临时凭据(jsapi_ticket)接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 *
		 * @return array 返回以data索引的格式化数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetJSApiTicket($access_token){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$access_token";
			$curl_obj   = new Curl();
			$val_obj    = new Type();
			$native_str = $curl_obj->get($api_url);
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $val_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取code的接口链接
		 *
		 * @param string      $corp_id 企业号的corpid
		 * @param string      $url     回调地址
		 * @param null|string $opt_str 额外参数
		 *
		 * @return string 返回接口调用地址
		 */
		protected function native_GetCode($corp_id, $url, $opt_str = ''){
			return "https://open.weixin.qq.com/connect/oauth2/authorize?corpid=$corp_id&redirect_uri=$url&response_type=code&scope=snsapi_base&state=$opt_str#wechat_redirect";
		}

		/**
		 * *获取成员id的接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $code         换取用户信息的code
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetUserID($access_token, $code){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$access_token&code=$code";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->get($api_url);
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取用户信息的接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $user_id      企业成员id
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetUserInfo($access_token, $user_id){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&userid=$user_id";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->get($api_url);
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *用户二次验证的接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $user_id      企业成员id
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和nativeStr索引的原始返回字符串
		 */
		protected function native_CheckUser($access_token, $user_id){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/user/authsucc?access_token=$access_token&userid=$user_id";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->get($api_url);
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *通过openid获取userid的接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $openid       用户的OpenID
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和nativeStr索引的原始返回字符串
		 */
		protected function native_ParseOpenIDToUserID($access_token, $openid){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_userid?access_token=$access_token";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->post($api_url, ['data' => json_encode(['openid' => $openid])]);
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *通过userid获取openid的接口实现
		 *
		 * @param string   $access_token 接口调用凭证
		 * @param string   $user_id      用户的OpenID
		 * @param null|int $agent_id     需要发送红包的应用ID，若只是使用微信支付和企业转账，则无需该参数
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和nativeStr索引的原始返回字符串
		 */
		protected function native_ParseUserIDToOpenID($access_token, $user_id, $agent_id = null){
			$api_url  = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=$access_token";
			$curl_obj = new Curl();
			$var_obj  = new Type();
			if($agent_id == null) $native_str = $curl_obj->post($api_url, ['data' => json_encode(['userid' => $user_id])]);
			else $native_str = $curl_obj->post($api_url, [
				'data' => json_encode([
					'userid'  => $user_id,
					'agentid' => $agent_id
				])
			]);
			$result = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取部门id下的子部门列表接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param int    $id           部门id，注：根部门id为1
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的部门数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetDepartmentList($access_token, $id = 1){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=$access_token&id=$id";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->get($api_url);
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result->department), $native_str);
		}

		/**
		 * *获取部门id下的成员列表接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param int    $did          部门id，注：根部门id为1
		 * @param int    $fetch_child  是否递归获取子部门下面的成员。枚举为[0,1]
		 * @param int    $status       0获取全部成员
		 *                             1获取已关注成员列表
		 *                             2获取禁用成员列表
		 *                             4获取未关注成员列表
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的用户数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetUserList($access_token, $did, $fetch_child = 1, $status = 0){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=$access_token&department_id=$did&fetch_child=$fetch_child&status=$status";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->get($api_url);
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result->userlist), $native_str);
		}

		/**
		 * *更新成员信息接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param int    $user_id      企业成员id
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
		protected function native_SaveUser($access_token, $user_id, $data){
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
		 * @param array  $data         必要的参数数据
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和nativeStr索引的原始返回字符串
		 */
		protected function native_SendMessage($access_token, $data){
			$api_url    = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=$access_token";
			$curl_obj   = new Curl();
			$var_obj    = new Type();
			$native_str = $curl_obj->post($api_url, ['data' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *下载媒体文件的接口实现（事先得完成媒体文件的上传获得media_id）
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $media_id     媒体ID
		 * @param int    $mode         返回数据形式 枚举为[1, 2, 0]，分别代表包含头部和相应体、只有头部和只有相应体
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetMedia($access_token, $media_id, $mode = 1){
			$api_url  = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$media_id";
			$curl_obj = new Curl();
			switch($mode){
				case 1:
					$native_str = $curl_obj->get($api_url, [
						'getHeader' => true
					]);
				break;
				case 2:
					$native_str = $curl_obj->get($api_url, [
						'getHeader' => true,
						'noBody'    => true
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
	 * Class WechatEACache
	 * 用于提供检测和维护本地jsapi_ticket、access_token凭据的处理类
	 */
	class WechatEACache{
		private $_config = [
			'expiredTime' => null,
			'data'        => null,
			'fileName'    => '',
			'_fileNameAC' => '/access_token_',
			'_fileNameJT' => '/jsapi_ticket_',
			'suffix'      => '.json',
			'cacheFolder' => './wxcorp-cache',
			'type'        => ''// ac,jt
		];

		/**
		 * 类构造函数，需要提供公众号的Appid作为参数传入
		 *
		 * @param string      $corp_id    企业号的corpid
		 * @param string      $type       凭据类型[ac,jt]
		 * @param string|null $cache_path 缓存文件夹的路径
		 * @param string      $ext        文件名扩展串
		 */
		public function __construct($corp_id, $type, $cache_path = null, $ext = ''){
			if($cache_path != null) $this->_config['cacheFolder'] = $cache_path;
			switch(strtolower($type)){
				case 'ac':
				default:
					$this->_config['fileName'] = $this->_config['_fileNameAC'];
					$this->_config['type']     = 'ac';
				break;
				case 'jt':
					$this->_config['fileName'] = $this->_config['_fileNameJT'];
					$this->_config['type']     = 'jt';
				break;
			}
			$this->_config['fileName'] .= $corp_id.$ext.$this->_config['suffix'];
			$content = $this->_readFiles();
			if($content){
				$content = json_decode($content);
				if($this->_config['type'] == 'ac'){
					$this->_config['data']        = $content->accessToken;
					$this->_config['expiredTime'] = $content->expiredTime;
				}
				elseif($this->_config['type'] == 'jt'){
					$this->_config['data']        = $content->jsApiTicket;
					$this->_config['expiredTime'] = $content->expiredTime;
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
			if(file_exists($this->_config['cacheFolder'])){
				if(file_exists($this->_config['cacheFolder'].$this->_config['fileName'])) return 0;
				else{
					try{
						$file = fopen($this->_config['cacheFolder'].$this->_config['fileName'], 'w');
						fclose($file);

						return 1;
					}catch(Exception $Error){
						print_r($Error);

						return 1;
					}
				}
			}
			else{
				$r = mkdir($this->_config['cacheFolder']);
				if($r){
					try{
						$file = fopen($this->_config['cacheFolder'].$this->_config['fileName'], 'w');
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
			return $this->_config['data'];
		}

		/**
		 * 读取存储文件
		 *
		 * @return string 返回文件内容
		 */
		private function _readFiles(){
			return @file_get_contents($this->_config['cacheFolder'].$this->_config['fileName']);
		}

		/**
		 * 检测凭据是否过期（7200秒/个）
		 *
		 * @return bool 过期则返回true，不过期则返回false
		 */
		public function isExpired(){
			if($this->_config['expiredTime'] == null) return true;
			else{
				if($this->_config['expiredTime']<=time()) return true;
				else return false;
			}
		}

		/**
		 * 检测凭据是否超过每日限制（2000次/天）
		 * todo
		 *
		 * @param string $content 获取凭据接口返回的原始JSON字符串
		 *
		 * @return bool 超过则返回true，不超过则返回false
		 */
		public function isLimited($content){
			$content = json_decode($content);
			print_r($content);
			exit;
			if($content->errcode == '45009') return true;
			else return false;
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
				$file = fopen($this->_config['cacheFolder'].$this->_config['fileName'], 'w');
				fwrite($file, $content);
				fclose($file);
			}catch(Exception $error){
				return 0;
			}

			return 1;
		}
	}
