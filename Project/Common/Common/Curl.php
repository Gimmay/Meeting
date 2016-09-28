<?php
	/**
	 * Created by PhpStorm.
	 * User: Quasar
	 * Date: 2016/4/20
	 * Time: 10:52
	 */
	namespace Quasar;

	/**
	 * Class Curl
	 * *附录1：
	 *
	 * 请求函数配置数组支持如下
	 * key: post        val: boolean    是否需要post数据且若为true则必须设定data
	 * key: data        val: mixed        当post为true时设定的post数据
	 * key: return        val: boolean    是否返回数据（默认为true）
	 * key: get_header    val: boolean    是否返回header
	 * key: no_body        val: boolean    是否不返回响应体
	 * key: user_agent    val: string        设定userAgent
	 * key: set_header    val: string        设定header
	 * key: use_ssl        val: boolean    是否为传输做SSL加密且若为true则必须设定pem_path和key_path
	 * key: verify        val: boolean    是否开启服务器验证（默认为false）
	 * key: async        val: boolean    是否为异步请求
	 * key: debug        val: boolean    是否报告遇到的每一个错误
	 *
	 * @package Quasar
	 */
	class Curl{
		/**
		 * @param string $url         请求地址
		 * @param string $type        发送类型
		 * @param null   $data        请求发送的数据
		 * @param bool   $ssl         是否传递证书
		 * @param null   $certarray   证书地址（必须构造成['PEM_PATH'=>$PEM_PATH, 'KEY_PATH'=>$KEY_PATH]的数组形式）
		 * @param bool   $header      是否设定header
		 * @param null   $headerarray header数组
		 *
		 * @return mixed 返回请求结果
		 */
		public function sendRequest($url, $type = 'POST', $data = null, $ssl = false, $certarray = null, $header = false, $headerarray = null){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt($curl, CURLOPT_VERBOSE, 1);
			//curl_setopt($curl, CURLOPT_HEADER, 1);
			switch(strtoupper($type)){
				case 'POST':
				case 'GET':
				break;
				default:
					return null;
			}
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			if($ssl){
				curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
				curl_setopt($curl, CURLOPT_SSLCERT, $certarray['PEM_PATH']);
				curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
				curl_setopt($curl, CURLOPT_SSLKEY, $certarray['KEY_PATH']);
			}
			if($data){
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
			if($header){
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headerarray);
			}
			$requestdatas = curl_exec($curl);
			curl_close($curl);

			return $requestdatas;
		}

		/**
		 * post请求
		 *
		 * @param string $url 请求地址
		 * @param array  $opt 配置数组 键值参考附录1
		 *
		 * @return mixed
		 */
		public function post($url, $opt = []){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
			if(isset($opt['post']) && $opt['post'] == true && isset($opt['data'])){
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $opt['data']);
			}
			if(isset($opt['return']) && $opt['return'] == false) ;
			else curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			if(isset($opt['get_header']) && $opt['get_header'] == true) curl_setopt($curl, CURLOPT_HEADER, true);
			if(isset($opt['no_body']) && $opt['no_body'] == true) curl_setopt($curl, CURLOPT_NOBODY, true);
			if(isset($opt['user_agent'])) curl_setopt($curl, CURLOPT_USERAGENT, $opt['user_agent']);
			if(isset($opt['set_header'])) curl_setopt($curl, CURLOPT_HTTPHEADER, $opt['set_header']);
			if(isset($opt['use_ssl']) && $opt['use_ssl'] == true && isset($opt['key_path']) && $opt['pem_path']){
				curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
				curl_setopt($curl, CURLOPT_SSLCERT, $opt['pem_path']);
				curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
				curl_setopt($curl, CURLOPT_SSLKEY, $opt['key_path']);
			}
			if(isset($opt['verify']) && $opt['verify'] == true){
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
			}
			else{
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			}
			if(isset($opt['async']) && $opt['asysc'] == true) curl_setopt($curl, CURLOPT_TIMEOUT, 1);
			if(isset($opt['debug']) && isset($opt['debug']) == true) curl_setopt($curl, CURLOPT_VERBOSE, 1);
			$result = curl_exec($curl);
			if(!$result) $result = curl_error($curl);
			curl_close($curl);

			return $result;
		}

		/**
		 * get请求
		 *
		 * @param string $url 请求地址
		 * @param array  $opt 配置数组 键值参考附录1
		 *
		 * @return mixed
		 */
		public function get($url, $opt = []){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
			if(isset($opt['return']) && $opt['return'] == false) ;
			else curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			if(isset($opt['get_header']) && $opt['get_header'] == true) curl_setopt($curl, CURLOPT_HEADER, true);
			if(isset($opt['no_body']) && $opt['no_body'] == true) curl_setopt($curl, CURLOPT_NOBODY, true);
			if(isset($opt['user_agent'])) curl_setopt($curl, CURLOPT_USERAGENT, $opt['user_agent']);
			if(isset($opt['set_header'])) curl_setopt($curl, CURLOPT_HTTPHEADER, $opt['set_header']);
			if(isset($opt['use_ssl']) && $opt['use_ssl'] == true){
				curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
				curl_setopt($curl, CURLOPT_SSLCERT, $opt['pem_path']);
				curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
				curl_setopt($curl, CURLOPT_SSLKEY, $opt['key_path']);
			}
			if(isset($opt['verify']) && $opt['verify'] == true){
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
			}
			else{
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			}
			if(isset($opt['async']) && $opt['asysc'] == true) curl_setopt($curl, CURLOPT_TIMEOUT, 1);
			if(isset($opt['debug']) && isset($opt['debug']) == true) curl_setopt($curl, CURLOPT_VERBOSE, 1);
			$result = curl_exec($curl);
			if(!$result) $result = curl_error($curl);
			curl_close($curl);

			return $result;
		}
	}