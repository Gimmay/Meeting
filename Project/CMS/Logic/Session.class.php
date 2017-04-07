<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 11:43
	 */
	namespace CMS\Logic;

	class Session{
		/** 登入成功写入的用户ID的SESSION名称 */
		const LOGIN_USER_ID = 'CMS_USER_ID';
		/** 登入成功写入的用户名称的SESSION名称 */
		const LOGIN_USER_NAME = 'CMS_USER_NAME';
		/** 登入成功写入的用户昵称的SESSION名称 */
		const LOGIN_USER_NICKNAME = 'CMS_USER_NICKNAME';
		/** 登入用户的权限列表列表 */
		const LOGIN_USER_PERMISSION_LIST = 'CMS_USER_PERMISSION_LIST';
		/** 当前会议的名称 */
		const MEETING_NAME = 'CMS_MEETING_NAME';
		/** 当前会议的类型 */
		const MEETING_TYPE = 'CMS_MEETING_TYPE';
		/** 当前会议ID */
		const MEETING_ID = 'CMS_MEETING_ID';
		/** 必须修改密码（密码为空时） */
		const MUST_MODIFY_PASSWORD = 'CMS_MODIFY_PASSWORD';

		/**
		 * 获取当前登入用户的ID
		 *
		 * @return int
		 */
		public static function getCurrentUser(){
			return I('session.'.self::LOGIN_USER_ID, 0, 'int');
		}

		/**
		 * 清除所有Session数据
		 */
		public static function cleanAll(){
			session_destroy();
			session_unset();
		}
	}