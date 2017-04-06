<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-4
	 * Time: 9:20
	 */
	namespace General\Logic;

	class PermissionLogic extends GeneralLogic{
		public function _initialize(){
			parent::_initialize();
		}

		const ALL_PERMISSION = [
			[
				'module' => '会议类型',
				'name'   => '公司内部相关会议',
				'code'   => 'GENERAL-MEETING_TYPE.GIMMAY_GROUP'
			],
			[
				'module' => '会议类型',
				'name'   => '瑞丽斯-成交会',
				'code'   => 'GENERAL-MEETING_TYPE.ROYALWISS_DEAL'
			],
			[
				'module' => '会议类型',
				'name'   => '瑞丽斯-特训营',
				'code'   => 'GENERAL-MEETING_TYPE.ROYALWISS_TRAINING_CAMP'
			],
			[
				'module' => '会议类型',
				'name'   => '瑞丽斯-招商会',
				'code'   => 'GENERAL-MEETING_TYPE.ROYALWISS_INVESTMENT'
			],
			[
				'module' => '会议类型',
				'name'   => '瑞丽斯-女子优雅课程',
				'code'   => 'GENERAL-MEETING_TYPE.ROYALWISS_LADY_GRACE'
			],
			[
				'module' => '会议类型',
				'name'   => '韦恩-教育',
				'code'   => 'GENERAL-MEETING_TYPE.WAYNE_ONLY_EDUCATION'
			],
			[
				'module' => '会议类型',
				'name'   => '韦恩-大区培训',
				'code'   => 'GENERAL-MEETING_TYPE.WAYNE_ONLY_AREA_TRAIN'
			],
			[
				'module' => '会议类型',
				'name'   => '韦恩-招商会',
				'code'   => 'GENERAL-MEETING_TYPE.WAYNE_ONLY_INVESTMENT'
			],
			[
				'module' => '会议类型',
				'name'   => '韦恩-纹绣大会',
				'code'   => 'GENERAL-MEETING_TYPE.WAYNE_ONLY_TATTOO'
			],
			[
				'module' => '会议类型',
				'name'   => '盖亚',
				'code'   => 'GENERAL-MEETING_TYPE.GAEA'
			],
			[
				'module' => '用户',
				'name'   => '创建',
				'code'   => 'GENERAL-USER.CREATE'
			],
			[
				'module' => '用户',
				'name'   => '编辑',
				'code'   => 'GENERAL-USER.MODIFY'
			],
			[
				'module' => '用户',
				'name'   => '查看',
				'code'   => 'GENERAL-USER.VIEW'
			],
			[
				'module' => '用户',
				'name'   => '检索',
				'code'   => 'GENERAL-USER.SEARCH'
			],
			[
				'module' => '用户',
				'name'   => '删除',
				'code'   => 'GENERAL-USER.DELETE'
			],
			[
				'module' => '用户',
				'name'   => '分配角色',
				'code'   => 'GENERAL-USER.ASSIGN_ROLE'
			],
			[
				'module' => '用户',
				'name'   => '禁用',
				'code'   => 'GENERAL-USER.DISABLE'
			],
			[
				'module' => '用户',
				'name'   => '启用',
				'code'   => 'GENERAL-USER.ENABLE'
			],
			[
				'module' => '用户',
				'name'   => '修改密码',
				'code'   => 'GENERAL-USER.MODIFY_PASSWORD'
			],
			[
				'module' => '用户',
				'name'   => '重制密码',
				'code'   => 'GENERAL-USER.RESET_PASSWORD'
			],
			[
				'module' => '角色',
				'name'   => '创建',
				'code'   => 'GENERAL-ROLE.CREATE'
			],
			[
				'module' => '角色',
				'name'   => '编辑',
				'code'   => 'GENERAL-ROLE.MODIFY'
			],
			[
				'module' => '角色',
				'name'   => '查看',
				'code'   => 'GENERAL-ROLE.VIEW'
			],
			[
				'module' => '角色',
				'name'   => '检索',
				'code'   => 'GENERAL-ROLE.SEARCH'
			],
			[
				'module' => '角色',
				'name'   => '删除',
				'code'   => 'GENERAL-ROLE.DELETE'
			],
			[
				'module' => '角色',
				'name'   => '禁用',
				'code'   => 'GENERAL-ROLE.DISABLE'
			],
			[
				'module' => '角色',
				'name'   => '启用',
				'code'   => 'GENERAL-ROLE.ENABLE'
			],
			[
				'module' => '角色',
				'name'   => '授权',
				'code'   => 'GENERAL-ROLE.GRANT_PERMISSION'
			],
			[
				'module' => '角色',
				'name'   => '取消授权',
				'code'   => 'GENERAL-ROLE.REVOKE_PERMISSION'
			],
			[
				'module' => '会议',
				'name'   => '所有会议可见',
				'code'   => 'GENERAL-MEETING.VIEW_ALL'
			],
		];
	}