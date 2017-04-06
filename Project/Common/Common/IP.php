<?php
	/*
	 * 更新日志
	 *
	 * Version 1.00 2017-02-27 11:43
	 * 初始版本
	 */
	namespace Quasar\Utility;

	/**
	 * 封装了IP地址获取/设定的方法
	 * <br>
	 * CreateTime: 2017-02-27 11:43<br>
	 * ModifyTime: 2017-02-27 11:43<br>
	 *
	 * @author  Quasar (lelouchcctony@163.com)
	 * @version 1.00
	 */
	class IP{
		/** @var string 客户端IP地址 */
		private $_clientIP;
		/** @var string 服务端IP地址 */
		private $_serverIP;
		/** @var string 使用了代理的客户端IP地址 */
		private $_agentIP;

		public function __construct(){
			$this->_setClientIP();
			$this->_setServerIP();
			$this->_setAgentIP();
		}

		/**
		 * 获取客户端IP地址
		 *
		 * @return string
		 */
		public function getClientIP(){
			return $this->_clientIP;
		}

		/**
		 * 获取服务端IP地址
		 *
		 * @return string
		 */
		public function getServerIP(){
			return $this->_serverIP;
		}

		/**
		 * 获取客户端代理IP地址
		 *
		 * @return string
		 */
		public function getAgentIP(){
			return $this->_agentIP;
		}

		private function _setClientIP(){
			$ai              = $this->_checkAgent();
			$this->_clientIP = $ai['client'];
		}

		private function _setAgentIP(){
			$ai             = $this->_checkAgent();
			$this->_agentIP = $ai['agent'];
		}

		private function _setServerIP(){
			$this->_serverIP = $_SERVER['SERVER_ADDR'];
		}

		private function _checkAgent(){
			$ip1    = getenv('HTTP_VIA');
			$ip2    = getenv('HTTP_CLIENT_IP');
			$ip3    = getenv('HTTP_X_FORWARDED_FOR');
			$ip4    = getenv('REMOTE_ADDR');
			$result = ['agent' => null, 'client' => null];
			if($ip1){
				$result['client'] = $ip1;
				$result['agent']  = $ip4;
			}
			elseif($ip2){
				$result['client'] = $ip2;
				$result['agent']  = $ip4;
			}
			elseif($ip3){
				$result['client'] = $ip3;
				$result['agent']  = $ip4;
			}
			else{
				$result['client'] = $ip4;
				$result['agent']  = null;
			}

			return $result;
		}
	}

	?>