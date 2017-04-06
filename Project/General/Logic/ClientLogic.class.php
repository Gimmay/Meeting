<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-11
	 * Time: 17:30
	 */
	namespace General\Logic;

	use Exception;
	use Quasar\Utility\StringPlus;

	class ClientLogic extends GeneralLogic{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 密码加壳
		 * <br>注：这里传入的密码参数为base64加密过后的字符串
		 *
		 * @param string $password 密码
		 * @param string $mobile   客户手机号
		 *
		 * @return string
		 * @throws Exception
		 */
		public function makePassword($password, $mobile){
			$str_obj = new StringPlus();
			$tmp_str = $str_obj->hash("quasar$password$mobile", 'md5');
			$tmp_str = substr($tmp_str, 4, 13);
			$tmp_str = $str_obj->hash("zero$tmp_str$mobile$password", 'sha1');

			return $str_obj->crypt($tmp_str, false, $str_obj::CRYPT_MODE['md5']);
		}

		/**
		 * 验证密码正确性
		 * <br>注：这里传入的密码参数为base64加密过后的字符串
		 *
		 * @param string $password    密码
		 * @param string $mobile      客户手机号
		 * @param string $cipher_text 密文
		 *
		 * @return bool
		 */
		public function verifyPassword($password, $mobile, $cipher_text){
			$str_obj = new StringPlus();
			$tmp_str = $str_obj->hash("quasar$password$mobile", 'md5');
			$tmp_str = substr($tmp_str, 4, 13);
			$tmp_str = $str_obj->hash("zero$tmp_str$mobile$password", 'sha1');

			return $str_obj->verifyCrypt($tmp_str, $cipher_text);
		}
	}