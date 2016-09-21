<?php
	/**
	 * Created by PhpStorm.
	 * User: Quasar
	 * Date: 2016/8/7
	 * Time: 17:34
	 */
	namespace Quasar;

	class SliderCaptcha{
		private $_config = [
			'flag'       => 'QUASAR_SESSION_SLIDER_CAPTCHA_OF_',
			'hash'       => 'QUASAR_SESSION_SLIDER_CAPTCHA_HASH_OF_',
			'cookie'     => 'QUASAR_COOKIE_SLIDER_CAPTCHA_OF_',
			'expireTime' => 7*24*60*60
		];

		public function __construct($name, $new = 1){
			$this->init($name, $new);
		}

		public function init($name, $new = 1){
			$conf    = &$this->_config;
			$name    = strtoupper($name);
			$_format = function ($name) use (&$conf){
				if(isset($_COOKIE["$conf[cookie]$name"]) && $_COOKIE["$conf[cookie]$name"] == 1){
					$conf['flag']   = "$conf[flag]$name";
					$conf['hash']   = "$conf[hash]$name";
					$conf['cookie'] = "$conf[cookie]$name";

					return false;
				}
				if(isset($_SESSION)){
					unset($_SESSION["$conf[flag]"]);
					unset($_SESSION["$conf[hash]"]);
					setcookie("$conf[cookie]", 0, time()-3600);
				}
				$conf['flag']            = "$conf[flag]$name";
				$conf['hash']            = "$conf[hash]$name";
				$conf['cookie']          = "$conf[cookie]$name";
				$_SESSION["$conf[hash]"] = sha1(rand().date('YmdHis', time()));
				$_SESSION["$conf[flag]"] = 0;
				setcookie("$conf[cookie]", 0, $conf['expireTime']+time(), '/');

				return true;
			};
			$_resume = function ($name) use (&$conf){
				$conf['flag']   = "$conf[flag]$name";
				$conf['hash']   = "$conf[hash]$name";
				$conf['cookie'] = "$conf[cookie]$name";

				return true;
			};
			switch($new){
				case 1: // new
				default:
					$_format($name);
				break;
				case 0: // keep on
					$_resume($name);
				break;
			}
		}

		public function clean(){
			unset($_SESSION[$this->_config['flag']]);
			unset($_SESSION[$this->_config['hash']]);
			setcookie($this->_config['cookie'], 0, time()-3600);
		}

		public function check($hash, $flag = false){
			if(isset($_SESSION[$this->_config['hash']]) && $hash == $_SESSION[$this->_config['hash']]){
				$_SESSION[$this->_config['flag']] = 1;
				if($flag) setcookie($this->_config['cookie'], 1, $this->_config['expireTime']+time(), '/');

				return true;
			}
			else return false;
		}

		public function getHash(){
			return $_SESSION[$this->_config['hash']];
		}

		public function getStatus(){
			$status = (isset($_SESSION[$this->_config['flag']]) && $_SESSION[$this->_config['flag']] == 1) || (isset($_COOKIE[$this->_config['cookie']]) && $_COOKIE[$this->_config['cookie']] == 1);
			if($status){
				$_SESSION[$this->_config['flag']] = 1;

				return true;
			}
			else return false;
		}
	}
