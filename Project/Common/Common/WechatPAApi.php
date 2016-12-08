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
	 * Class WechatPAApi
	 * 用于提供实现微信公众号API的操作类
	 *
	 * @see Type
	 * @see Curl
	 */
	class WechatPAApi extends WechatPANativeApi{
		private $_config = [
			'appID'           => null, // 公众号appid
			'appSecret'       => null, // 公众号appsecret
			'cacheActiveTime' => 7000 // 凭据缓存的生效时间
		];

		/**
		 * WechatPAApi constructor.
		 * 用于初始化公众号的基本配置信息
		 *
		 * @param null|string $app_id     公众号的appid
		 * @param null|string $app_secret 公众号的appsecret
		 */
		public function __construct($app_id = null, $app_secret = null){
			if($app_id != null) $this->_config['appID'] = $app_id;
			if($app_secret != null) $this->_config['appSecret'] = $app_secret;
		}
		/*
		 ************************************************************************************************
		 ************************************************************************************************
			通用
		 ************************************************************************************************
		 ************************************************************************************************
		 */
		/**
		 * 返回公众号的基本参数配置
		 *
		 * @return array 格式为['appID'=>string, 'appSecret'=>string]
		 */
		public function getConfigs(){
			return $this->_config;
		}

		/**
		 * 手动设置appid
		 *
		 * @param string $app_id 公众号的appid
		 */
		public function setAppID($app_id){
			$this->_config['appID'] = $app_id;
		}

		/**
		 * 手动设置appsercet
		 *
		 * @param string $app_secret 公众号的appsercet
		 */
		public function setAppSecret($app_secret){
			$this->_config['appSecret'] = $app_secret;
		}

		/**
		 * 获取accesstoken
		 *
		 * @param null|string $cache_path 凭据缓存路径
		 *
		 * @return string 返回AccessToken
		 */
		public function getAccessToken($cache_path = null){
			$cd_obj = new WechatPACache($this->_config['appID'], 'ac', $cache_path);
			if($cd_obj->isExpired()){
				$response = parent::native_GetAccessToken($this->_config['appID'], $this->_config['appSecret']);
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
			用户
		 ************************************************************************************************
		 ************************************************************************************************
		 */
		/**
		 * 获取粉丝openid列表
		 * *当公众号关注者数量超过10000时，可通过填写next_openid的值，从而多次拉取列表的方式来满足需求。
		 * *具体而言，就是在调用接口时，将上一次调用得到的返回中的next_openid值，作为下一次调用中的next_openid值。
		 *
		 * @param null|string $access_token 接口调用凭证
		 * @param null|string $next_openid  下一批的首个openid
		 *
		 * @return array 返回调用结果和openid列表
		 */
		public function getOpenIDList($access_token = null, $next_openid = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_GetOpenIDList($access_token, $next_openid);

			return $response['status'] ? [
				'list'  => $response['data']['data']['openid'],
				'total' => $response['data']['total'],
				'count' => $response['data']['count'],
				'next'  => $response['data']['next_openid']
			] : $response['data'];
		}

		/**
		 * 批量获取粉丝用户信息的列表
		 *
		 * @param array       $openid_list  需要获取信息的openid列表
		 * @param string      $lang         地区语言
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回调用结果和openid对应的用户信息
		 */
		public function getBatchUserList($openid_list, $lang = 'zh-CN', $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_GetBatchUserList($access_token, $openid_list, $lang);

			return $response['status'] ? $response['data']['user_info_list'] : $response['data'];
		}

		/**
		 * 根据openid获取粉丝信息（UnionID机制）
		 *
		 * @param string      $openid       需要获取信息的openid
		 * @param string      $lang         地区语言
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回调用结果和openid对应的用户信息
		 */
		public function getUserInformationOfUnionID($openid, $lang = 'zh-CN', $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_GetUserInformationOfUnionID($access_token, $openid, $lang);

			return $response['data'];
		}
		/*
		 ************************************************************************************************
		 ************************************************************************************************
			素材
		 ************************************************************************************************
		 ************************************************************************************************
		 */
		/**
		 * 获取素材列表
		 *
		 * @param string      $type         素材类型
		 * @param int         $offset       从全部素材的该偏移位置开始返回
		 * @param int         $count        返回数量
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回调用结果和素材列表信息
		 */
		public function getMediaList($type, $offset, $count, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			if(!in_array($type, ['image', 'video', 'voice', 'news'])) return ['errcode' => 40004, "errmsg" => '类型错误'];
			$response = parent::native_GetMediaList($access_token, [
				'type'   => $type,
				'offset' => $offset,
				'count'  => $count
			]);

			return $response['data'];
		}

		/**
		 * 获取素材
		 *
		 * @param string      $media_id     素材id
		 * @param bool        $download     是否下载文件
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回调用结果和素材列表信息
		 */
		public function getMedia($media_id, $download = false, $access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_GetMedia($access_token, $media_id, $download);
			if($download){
				preg_match('/filename=\"(\S+)\"/', $response['header'], $match_arr);
				header("Content-Disposition: attachment; filename=\"$match_arr[1]\"");
				echo $response['body'];

				return $response['body'];
			}
			else return $response['data'];
		}
		/*
		 ************************************************************************************************
		 ************************************************************************************************
			自定义菜单
		 ************************************************************************************************
		 ************************************************************************************************
		 */
		/**
		 * 获取自定义菜单
		 *
		 * @param null|string $access_token 接口调用凭证
		 *
		 * @return array 返回调用结果和自定义菜单信息
		 */
		public function getMenu($access_token = null){
			$access_token = $access_token == null ? $this->getAccessToken() : $access_token;
			$response     = parent::native_GetMenu($access_token);

			return $response['data'];
		}

		/*
		 ************************************************************************************************
		 ************************************************************************************************
			网页授权
		 ************************************************************************************************
		 ************************************************************************************************
		 */
		/**
		 * 获取code
		 * *这里做了页面的定向跳转，且需要被调用的页面在微信中打开！
		 * *回调地址中将会携带code参数
		 *
		 * @param string $url     回调地址
		 * @param int    $mode    模式 可选值包括0或1
		 *                        0：不弹出授权页面，直接跳转，只能获取用户openid
		 *                        1：弹出授权页面，可获取用户相信信息，并且即使在未关注的情况下，只要用户授权，也能获取其信息
		 * @param string $data    附带数据
		 * @param string $app_id  公众号的appid
		 */
		public function getCode($url, $mode = 0, $data = '', $app_id = null){
			if($app_id === null) $app_id = $this->_config['appID'];
			$url  = urlencode($url);
			$data = urlencode((string)$data);
			header('Location:'.parent::native_GetCode($app_id, $url, $mode, $data));
		}

		/**
		 * 通过code获取accesstoken或者openid
		 *
		 * @param string      $code       用户授权code码
		 * @param null|string $app_id     公众号的appid
		 * @param null|string $app_secret 公众号的appsecret
		 *
		 * @return array 返回调用结果
		 */
		public function useCode($code, $app_id = null, $app_secret = null){
			if($app_id === null) $app_id = $this->_config['appID'];
			if($app_secret === null) $app_secret = $this->_config['appSecret'];
			$response = parent::native_UseCode($app_id, $app_secret, $code);

			return $response['data'];
		}

		/**
		 * 根据openid获取粉丝信息
		 *
		 * @param string $openid       需要获取信息的openid
		 * @param string $access_token 接口调用凭证
		 * @param string $lang         地区语言
		 *
		 * @return array 返回调用结果和openid对应的用户信息
		 */
		public function getUserInformation($access_token, $openid, $lang = 'zh-CN'){
			$response = parent::native_GetUserInformation($access_token, $openid, $lang);

			return $response['data'];
		}

		/**
		 * 直接获取当前微信账号的信息
		 *
		 * @param int         $mode       模式 0：只获取openid 1：获取用户信息
		 * @param null|string $app_id     公众号的appid
		 * @param null|string $app_secret 公众号的appsecret
		 *
		 * @return null|string 若成功则返回相关信息，否则返回null
		 */
		public function getUserFullSet($mode = 0, $app_id = null, $app_secret = null){
			if(isset($_GET['code'])){
				$code     = $_GET['code'];
				$response = $this->useCode($code, $app_id, $app_secret);
				if(isset($response['openid'])){
					if($mode == 0) return $response['openid'];
					elseif($mode == 1) return $this->getUserInformation($response['access_token'], $response['openid']);
					else return null;
				}
				else return null;
			}
			else{
				if($app_id === null) $app_id = $this->_config['appID'];
				$redirect_url = urlencode("$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
				header('Location:'.parent::native_GetCode($app_id, $redirect_url, $mode == 0 ? 0 : ($mode == 1 ? 1 : 0)));
				exit;
			}
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
		 * @return string 返回签名字符串
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
			$cd_obj       = new WechatPACache($this->_config['appID'], 'jt', $cache_path);
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
	}

	class WechatPANativeApi{
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
			$var_type_obj = new Type();
			$data_arr     = $var_type_obj->parseObjectToArray($data);
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
		 * @param string $app_id     公众号的appid
		 * @param string $app_secret 企业号的appsecret
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的格式化数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetAccessToken($app_id, $app_secret){
			$api_url      = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$app_id&secret=$app_secret";
			$curl_obj     = new Curl();
			$var_type_obj = new Type();
			$native_str   = $curl_obj->get($api_url);
			$result       = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_type_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取JS-SDK调用临时凭据(jsapi_ticket)接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的格式化数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetJSApiTicket($access_token){
			$api_url      = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$access_token&type=jsapi";
			$curl_obj     = new Curl();
			$var_type_obj = new Type();
			$native_str   = $curl_obj->get($api_url);
			$result       = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_type_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取粉丝openid列表接口的实现
		 *
		 * @param string      $access_token 接口调用凭证
		 * @param null|string $next_openid  下个粉丝openid
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的格式化数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetOpenIDList($access_token, $next_openid = null){
			$api_url      = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token".($next_openid ? "&next_openid=$next_openid" : '');
			$curl_obj     = new Curl();
			$var_type_obj = new Type();
			$native_str   = $curl_obj->get($api_url);
			$result       = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_type_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *批量获取粉丝用户信息列表接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param array  $openid_list  需要获取的openid列表
		 * @param string $lang         地区语言
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的格式化数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetBatchUserList($access_token, $openid_list, $lang = 'zh-CN'){
			$api_url      = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=$access_token";
			$curl_obj     = new Curl();
			$var_type_obj = new Type();
			$data         = ['user_list' => []];
			foreach($openid_list as $val) $data['user_list'][] = [
				'openid' => $val,
				'lang'   => $lang
			];
			$native_str = $curl_obj->post($api_url, [
				'data' => json_encode($data)
			]);
			$result     = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_type_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *根据openid获取粉丝用户信息接口的实现（UnionID机制）
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $openid       需要获取的openid
		 * @param string $lang         地区语言
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的格式化数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetUserInformationOfUnionID($access_token, $openid, $lang = 'zh-CN'){
			$api_url      = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=$lang";
			$curl_obj     = new Curl();
			$var_type_obj = new Type();
			$native_str   = $curl_obj->get($api_url);
			$result       = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_type_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *下载媒体文件的接口实现（事先得完成媒体文件的上传获得media_id）
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $media_id     媒体ID
		 * @param bool   $download     是否为文件下载
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetMedia($access_token, $media_id, $download = false){
			$api_url      = "https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=$access_token";
			$curl_obj     = new Curl();
			$var_type_obj = new Type();
			$option       = ['data' => json_encode(['media_id' => $media_id])];
			if($download) $option = array_merge($option, ['getHeader' => true]);
			$native_result = $curl_obj->post($api_url, $option);
			if(!$download){
				$result = json_decode($native_result);

				return $this->_makeResultTemplate($this->_getResultStatus($result), $var_type_obj->parseObjectToArray($result), $native_result);
			}
			else return $native_result;
		}

		/**
		 * *获取自定义菜单的接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetMenu($access_token){
			$api_url      = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=$access_token";
			$curl_obj     = new Curl();
			$var_type_obj = new Type();
			$native_str   = $curl_obj->get($api_url);
			$result       = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_type_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取素材列表的接口实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param array  $option       ['type'=>string, 'offset'=>int, 'count'=>int] 指定类型、偏移量和计数
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的结果数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetMediaList($access_token, $option){
			$api_url      = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=$access_token";
			$curl_obj     = new Curl();
			$var_type_obj = new Type();
			$native_str   = $curl_obj->post($api_url, [
				'data' => json_encode([
					'type'   => $option['type'],
					'offset' => $option['offset'],
					'count'  => $option['count']
				])
			]);
			$result       = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_type_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *获取code的接口链接
		 *
		 * @param string      $app_id  公众号的appid
		 * @param string      $url     回调地址
		 * @param int         $mode    模式 0：只获取openid 1：获取用户信息
		 * @param null|string $opt_str 额外参数
		 *
		 * @return string 返回接口调用地址
		 */
		protected function native_GetCode($app_id, $url, $mode = 0, $opt_str = ''){
			if($mode == 1) $scope = 'snsapi_userinfo';
			else $scope = 'snsapi_base';

			return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$app_id&redirect_uri=$url&response_type=code&scope=$scope&state=$opt_str#wechat_redirect";
		}

		/**
		 * *通过code获取accesstoken和openid的接口实现
		 *
		 * @param string $app_id     公众号的appid
		 * @param string $app_secret 公众号的appsecret
		 * @param string $code       用户授权code码
		 *
		 * @return array
		 */
		protected function native_UseCode($app_id, $app_secret, $code){
			$api_url      = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$app_id&secret=$app_secret&code=$code&grant_type=authorization_code";
			$curl_obj     = new Curl();
			$var_type_obj = new Type();
			$native_str   = $curl_obj->get($api_url);
			$result       = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_type_obj->parseObjectToArray($result), $native_str);
		}

		/**
		 * *根据openid获取粉丝用户信息接口的实现
		 *
		 * @param string $access_token 接口调用凭证
		 * @param string $openid       需要获取的openid
		 * @param string $lang         地区语言
		 *
		 * @return array 返回以status索引的接口调用结果，以data索引的格式化数据和nativeStr索引的原始返回字符串
		 */
		protected function native_GetUserInformation($access_token, $openid, $lang = 'zh-CN'){
			$api_url      = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=$lang";
			$curl_obj     = new Curl();
			$var_type_obj = new Type();
			$native_str   = $curl_obj->get($api_url);
			$result       = json_decode($native_str);

			return $this->_makeResultTemplate($this->_getResultStatus($result), $var_type_obj->parseObjectToArray($result), $native_str);
		}
	}

	/**
	 * Class WechatPACache
	 * 用于提供检测和维护本地jsapi_ticket、accesstoken凭据的处理类
	 */
	class WechatPACache{
		private $_config = [
			'expiredTime' => null,
			'data'        => null,
			'fileName'    => '',
			'_fileNameAC' => '/access_token_',
			'_fileNameJT' => '/jsapi_ticket_',
			'suffix'      => '.json',
			'cacheFolder' => './wx-cache',
			'type'        => ''// ac,jt
		];

		/**
		 * 类构造函数，需要提供公众号的appid作为参数传入
		 *
		 * @param string      $app_id     公众号的appid
		 * @param string      $type       凭据类型[ac,jt]
		 * @param string|null $cache_path 缓存文件夹的路径
		 * @param string      $ext        文件名扩展串
		 */
		public function __construct($app_id, $type, $cache_path = null, $ext = ''){
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
			$this->_config['fileName'] .= $app_id.$ext.$this->_config['suffix'];
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