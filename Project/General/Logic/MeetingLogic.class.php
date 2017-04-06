<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-4
	 * Time: 14:59
	 */
	namespace General\Logic;

	use CMS\Logic\Session;
	use Exception;

	class MeetingLogic extends GeneralLogic{
		public function _initialize(){
			parent::_initialize();
		}

		const TYPE = [
			0  => '全局',
			1  => '公司内部相关会议',
			2  => '瑞丽斯-成交会',
			3  => '瑞丽斯-特训营',
			4  => '瑞丽斯-招商会',
			5  => '瑞丽斯-女子优雅课程',
			6  => '韦恩-教育',
			7  => '韦恩-大区培训',
			8  => '韦恩-招商会',
			9  => '韦恩-纹绣大会',
			10 => '盖亚'
		];
		private $_moduleMapTable = [
			0  => ['module' => 'General', 'permission' => 'GENERAL'],
			1  => ['module' => 'GimmayGroup', 'permission' => 'GENERAL-MEETING_TYPE.GIMMAY_GROUP'],
			2  => ['module' => 'RoyalwissD', 'permission' => 'GENERAL-MEETING_TYPE.ROYALWISS_DEAL'],
			3  => ['module' => 'RoyalwissT', 'permission' => 'GENERAL-MEETING_TYPE.ROYALWISS_TRAINING_CAMP'],
			4  => ['module' => 'RoyalwissI', 'permission' => 'GENERAL-MEETING_TYPE.ROYALWISS_INVESTMENT'],
			5  => ['module' => 'RoyalwissL', 'permission' => 'GENERAL-MEETING_TYPE.ROYALWISS_LADY_GRACE'],
			6  => ['module' => 'WayneE', 'permission' => 'GENERAL-MEETING_TYPE.WAYNE_ONLY_EDUCATION'],
			7  => ['module' => 'WayneA', 'permission' => 'GENERAL-MEETING_TYPE.WAYNE_ONLY_AREA_TRAIN'],
			8  => ['module' => 'WayneI', 'permission' => 'GENERAL-MEETING_TYPE.WAYNE_ONLY_INVESTMENT'],
			9  => ['module' => 'WayneT', 'permission' => 'GENERAL-MEETING_TYPE.WAYNE_ONLY_TATTOO'],
			10 => ['module' => 'Gaea', 'permission' => 'GENERAL-MEETING_TYPE.GAEA'],
		];

		/**
		 * 获取用户可见的会议类型
		 *
		 * @param null|int $user_id 用户ID
		 *
		 * @return array
		 */
		public function getViewedMeetingTypeList($user_id = null){
			if($user_id === null) $user_id = Session::getCurrentUser();
			/** @var \General\Model\UserModel $user_model */
			$user_model      = D('General/User');
			$permission_list = $user_model->getPermission($user_id);
			$result          = $assigned_arr = [];
			foreach($permission_list as $permission){
				foreach($this->_moduleMapTable as $key => $module){
					if($permission['code'] == $module['permission'] && !in_array($key, $assigned_arr)){
						$assigned_arr[] = $key;
						$result[]       = [
							'module' => $module['module'],
							'name'   => self::TYPE[$key],
							'type'   => $key
						];
					}
				}
			}

			return $result;
		}

		/**
		 * 通过模板查找会议类型
		 *
		 * @param $module
		 *
		 * @return int|null|string
		 */
		public function getTypeByModule($module){
			foreach($this->_moduleMapTable as $key => $val){
				if($val['module'] == $module) return $key;
			}

			return null;
		}
	}