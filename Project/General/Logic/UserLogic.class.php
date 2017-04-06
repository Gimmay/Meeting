<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-6
	 * Time: 9:42
	 */
	namespace General\Logic;

	use Exception;
	use Quasar\Utility\StringPlus;

	class UserLogic extends GeneralLogic{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 密码加壳
		 * <br>注：这里传入的密码参数为base64加密过后的字符串
		 *
		 * @param string $password  密码
		 * @param string $user_name 用户名
		 *
		 * @return string
		 * @throws Exception
		 */
		public function makePassword($password, $user_name){
			$str_obj = new StringPlus();
			$tmp_str = $str_obj->hash("quasar$password$user_name", 'sha512');
			$tmp_str = substr($tmp_str, 13, 47);
			$tmp_str = $str_obj->hash("zero$tmp_str$user_name$password", 'sha256');

			return $str_obj->crypt($tmp_str, false, $str_obj::CRYPT_MODE['sha256']);
		}

		/**
		 * 验证密码正确性
		 * <br>注：这里传入的密码参数为base64加密过后的字符串
		 *
		 * @param string $password    密码
		 * @param string $user_name   用户名
		 * @param string $cipher_text 密文
		 *
		 * @return bool
		 */
		public function verifyPassword($password, $user_name, $cipher_text){
			$str_obj = new StringPlus();
			$tmp_str = $str_obj->hash("quasar$password$user_name", 'sha512');
			$tmp_str = substr($tmp_str, 13, 47);
			$tmp_str = $str_obj->hash("zero$tmp_str$user_name$password", 'sha256');

			return $str_obj->verifyCrypt($tmp_str, $cipher_text);
		}
	}