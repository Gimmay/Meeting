<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-26
	 * Time: 17:57
	 */

	namespace Mobile\Logic;

	class Session{
		/** 访问者的微信open_id或user_id */
		const VISITOR_WECHAT_ID = 'MOBILE_WECHAT_ID';
		/** 访问者的客户信息ID */
		const VISITOR_CLIENT_ID = 'MOBILE_CLIENT_ID';
		/** 访问者的用户信息ID */
		const VISITOR_USER_ID = 'MOBILE_USER_ID';
		/** 访问者的身份类型：客户 */
		const CLIENT = 'MOBILE_VISITOR_TYPE:CLIENT';
		/** 访问者的身份类型：用户 */
		const USER = 'MOBILE_VISITOR_TYPE:USER';

		/**
		 * 获取访问者在系统中的记录ID
		 *
		 * @param string $visitor_type 访问者身份类型
		 *
		 * @return int|null
		 */
		public static function getCurrentVisitor($visitor_type = self::VISITOR_CLIENT_ID){
			switch($visitor_type){
				case self::CLIENT:
					return I('session.'.self::VISITOR_CLIENT_ID, 0, 'int');
				break;
				case self::USER:
					return I('session.'.self::VISITOR_USER_ID, 0, 'int');
				break;
				default:
					return null;
				break;
			}
		}

		/**
		 * 清除所有Session数据
		 */
		public static function cleanAll(){
			session_destroy();
			session_unset();
		}
	}