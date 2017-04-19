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
			// 会议类型
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
			// 用户
			[
				'module' => '用户',
				'name'   => '创建',
				'code'   => 'GENERAL-USER.CREATE'
			],
			[
				'module' => '用户',
				'name'   => '修改',
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
				'name'   => '重置密码',
				'code'   => 'GENERAL-USER.RESET_PASSWORD'
			],
			// 角色
			[
				'module' => '角色',
				'name'   => '创建',
				'code'   => 'GENERAL-ROLE.CREATE'
			],
			[
				'module' => '角色',
				'name'   => '修改',
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
				'name'   => '权限控制',
				'code'   => 'GENERAL-ROLE.GRANT_PERMISSION'
			],
			// 接口配置
			[
				'module' => '接口配置',
				'name'   => '创建',
				'code'   => 'GENERAL-API_CONFIGURE.CREATE'
			],
			[
				'module' => '接口配置',
				'name'   => '修改',
				'code'   => 'GENERAL-API_CONFIGURE.MODIFY'
			],
			[
				'module' => '接口配置',
				'name'   => '查看',
				'code'   => 'GENERAL-API_CONFIGURE.VIEW'
			],
			[
				'module' => '接口配置',
				'name'   => '检索',
				'code'   => 'GENERAL-API_CONFIGURE.SEARCH'
			],
			[
				'module' => '接口配置',
				'name'   => '删除',
				'code'   => 'GENERAL-API_CONFIGURE.DELETE'
			],
			[
				'module' => '接口配置',
				'name'   => '禁用',
				'code'   => 'GENERAL-API_CONFIGURE.DISABLE'
			],
			[
				'module' => '接口配置',
				'name'   => '启用',
				'code'   => 'GENERAL-API_CONFIGURE.ENABLE'
			],
			// 会议
			[
				'module' => '会议',
				'name'   => '所有会议可见',
				'code'   => 'SEVERAL-MEETING.VIEW_ALL'
			],
			[
				'module' => '会议',
				'name'   => '创建',
				'code'   => 'SEVERAL-MEETING.CREATE'
			],
			[
				'module' => '会议',
				'name'   => '修改',
				'code'   => 'SEVERAL-MEETING.MODIFY'
			],
			[
				'module' => '会议',
				'name'   => '查看',
				'code'   => 'SEVERAL-MEETING.VIEW'
			],
			[
				'module' => '会议',
				'name'   => '检索',
				'code'   => 'SEVERAL-MEETING.SEARCH'
			],
			[
				'module' => '会议',
				'name'   => '删除',
				'code'   => 'SEVERAL-MEETING.DELETE'
			],
			[
				'module' => '会议',
				'name'   => '禁用',
				'code'   => 'SEVERAL-MEETING.DISABLE'
			],
			[
				'module' => '会议',
				'name'   => '启用',
				'code'   => 'SEVERAL-MEETING.ENABLE'
			],
			[
				'module' => '会议',
				'name'   => '发布',
				'code'   => 'SEVERAL-MEETING.RELEASE'
			],
			[
				'module' => '会议',
				'name'   => '取消发布',
				'code'   => 'SEVERAL-MEETING.CANCEL_RELEASE'
			],
			[
				'module' => '会议',
				'name'   => '会务人员管理',
				'code'   => 'SEVERAL-MEETING.MEETING_MANAGER'
			],
			[
				'module' => '会议',
				'name'   => '字段控制',
				'code'   => 'SEVERAL-MEETING.MANAGE_COLUMN'
			],
			// 消息
			[
				'module' => '消息',
				'name'   => '创建',
				'code'   => 'SEVERAL-MESSAGE.CREATE'
			],
			[
				'module' => '消息',
				'name'   => '修改',
				'code'   => 'SEVERAL-MESSAGE.MODIFY'
			],
			[
				'module' => '消息',
				'name'   => '查看',
				'code'   => 'SEVERAL-MESSAGE.VIEW'
			],
			[
				'module' => '消息',
				'name'   => '检索',
				'code'   => 'SEVERAL-MESSAGE.SEARCH'
			],
			[
				'module' => '消息',
				'name'   => '删除',
				'code'   => 'SEVERAL-MESSAGE.DELETE'
			],
			[
				'module' => '消息',
				'name'   => '禁用',
				'code'   => 'SEVERAL-MESSAGE.DISABLE'
			],
			[
				'module' => '消息',
				'name'   => '启用',
				'code'   => 'SEVERAL-MESSAGE.ENABLE'
			],
			[
				'module' => '消息',
				'name'   => '使用到',
				'code'   => 'SEVERAL-MESSAGE.USE_TO'
			],
			[
				'module' => '消息',
				'name'   => '配置',
				'code'   => 'SEVERAL-MESSAGE.CONFIGURE'
			],
			[
				'module' => '消息',
				'name'   => '发送记录-查看',
				'code'   => 'SEVERAL-MESSAGE.SEND_HISTORY-VIEW'
			],
			[
				'module' => '消息',
				'name'   => '发送记录-检索',
				'code'   => 'SEVERAL-MESSAGE.SEND_HISTORY-SEARCH'
			],
			[
				'module' => '消息',
				'name'   => '发送记录-获取短信发送状态',
				'code'   => 'SEVERAL-MESSAGE.SEND_HISTORY-GET_SMS_SEND_STATUS'
			],
			// 胸卡
			[
				'module' => '胸卡',
				'name'   => '查看',
				'code'   => 'SEVERAL-BADGE.VIEW'
			],
			// 项目类型
			[
				'module' => '项目类型',
				'name'   => '创建',
				'code'   => 'SEVERAL-PROJECT_TYPE.CREATE'
			],
			[
				'module' => '项目类型',
				'name'   => '修改',
				'code'   => 'SEVERAL-PROJECT_TYPE.MODIFY'
			],
			[
				'module' => '项目类型',
				'name'   => '查看',
				'code'   => 'SEVERAL-PROJECT_TYPE.VIEW'
			],
			[
				'module' => '项目类型',
				'name'   => '检索',
				'code'   => 'SEVERAL-PROJECT_TYPE.SEARCH'
			],
			[
				'module' => '项目类型',
				'name'   => '删除',
				'code'   => 'SEVERAL-PROJECT_TYPE.DELETE'
			],
			[
				'module' => '项目类型',
				'name'   => '禁用',
				'code'   => 'SEVERAL-PROJECT_TYPE.DISABLE'
			],
			[
				'module' => '项目类型',
				'name'   => '启用',
				'code'   => 'SEVERAL-PROJECT_TYPE.ENABLE'
			],
			// 项目
			[
				'module' => '项目',
				'name'   => '创建',
				'code'   => 'SEVERAL-PROJECT.CREATE'
			],
			[
				'module' => '项目',
				'name'   => '修改',
				'code'   => 'SEVERAL-PROJECT.MODIFY'
			],
			[
				'module' => '项目',
				'name'   => '查看',
				'code'   => 'SEVERAL-PROJECT.VIEW'
			],
			[
				'module' => '项目',
				'name'   => '检索',
				'code'   => 'SEVERAL-PROJECT.SEARCH'
			],
			[
				'module' => '项目',
				'name'   => '删除',
				'code'   => 'SEVERAL-PROJECT.DELETE'
			],
			[
				'module' => '项目',
				'name'   => '禁用',
				'code'   => 'SEVERAL-PROJECT.DISABLE'
			],
			[
				'module' => '项目',
				'name'   => '启用',
				'code'   => 'SEVERAL-PROJECT.ENABLE'
			],
			[
				'module' => '项目',
				'name'   => '更新库存',
				'code'   => 'SEVERAL-PROJECT.UPDATE_STOCK'
			],
			[
				'module' => '项目',
				'name'   => '查看出入库记录',
				'code'   => 'SEVERAL-PROJECT.VIEW_STOCK_HISTORY'
			],
			// 支付方式
			[
				'module' => '支付方式',
				'name'   => '创建',
				'code'   => 'SEVERAL-PAY_METHOD.CREATE'
			],
			[
				'module' => '支付方式',
				'name'   => '修改',
				'code'   => 'SEVERAL-PAY_METHOD.MODIFY'
			],
			[
				'module' => '支付方式',
				'name'   => '查看',
				'code'   => 'SEVERAL-PAY_METHOD.VIEW'
			],
			[
				'module' => '支付方式',
				'name'   => '检索',
				'code'   => 'SEVERAL-PAY_METHOD.SEARCH'
			],
			[
				'module' => '支付方式',
				'name'   => '删除',
				'code'   => 'SEVERAL-PAY_METHOD.DELETE'
			],
			[
				'module' => '支付方式',
				'name'   => '禁用',
				'code'   => 'SEVERAL-PAY_METHOD.DISABLE'
			],
			[
				'module' => '支付方式',
				'name'   => '启用',
				'code'   => 'SEVERAL-PAY_METHOD.ENABLE'
			],
			// POS机
			[
				'module' => 'POS机',
				'name'   => '创建',
				'code'   => 'SEVERAL-POS_MACHINE.CREATE'
			],
			[
				'module' => 'POS机',
				'name'   => '修改',
				'code'   => 'SEVERAL-POS_MACHINE.MODIFY'
			],
			[
				'module' => 'POS机',
				'name'   => '查看',
				'code'   => 'SEVERAL-POS_MACHINE.VIEW'
			],
			[
				'module' => 'POS机',
				'name'   => '检索',
				'code'   => 'SEVERAL-POS_MACHINE.SEARCH'
			],
			[
				'module' => 'POS机',
				'name'   => '删除',
				'code'   => 'SEVERAL-POS_MACHINE.DELETE'
			],
			[
				'module' => 'POS机',
				'name'   => '禁用',
				'code'   => 'SEVERAL-POS_MACHINE.DISABLE'
			],
			[
				'module' => 'POS机',
				'name'   => '启用',
				'code'   => 'SEVERAL-POS_MACHINE.ENABLE'
			],
			// 客户
			[
				'module' => '客户',
				'name'   => '创建',
				'code'   => 'SEVERAL-CLIENT.CREATE'
			],
			[
				'module' => '客户',
				'name'   => '修改',
				'code'   => 'SEVERAL-CLIENT.MODIFY'
			],
			[
				'module' => '客户',
				'name'   => '查看',
				'code'   => 'SEVERAL-CLIENT.VIEW'
			],
			[
				'module' => '客户',
				'name'   => '检索',
				'code'   => 'SEVERAL-CLIENT.SEARCH'
			],
			[
				'module' => '客户',
				'name'   => '筛选',
				'code'   => 'SEVERAL-CLIENT.FILTER'
			],
			[
				'module' => '客户',
				'name'   => '删除',
				'code'   => 'SEVERAL-CLIENT.DELETE'
			],
			[
				'module' => '客户',
				'name'   => '禁用',
				'code'   => 'SEVERAL-CLIENT.DISABLE'
			],
			[
				'module' => '客户',
				'name'   => '启用',
				'code'   => 'SEVERAL-CLIENT.ENABLE'
			],
			[
				'module' => '客户',
				'name'   => '批量创建',
				'code'   => 'SEVERAL-CLIENT.MULTI_CREATE'
			],
			[
				'module' => '客户',
				'name'   => '导入',
				'code'   => 'SEVERAL-CLIENT.IMPORT'
			],
			[
				'module' => '客户',
				'name'   => '下载导入模板',
				'code'   => 'SEVERAL-CLIENT.DOWNLOAD_IMPORT_TEMPLATE'
			],
			[
				'module' => '客户',
				'name'   => '导出',
				'code'   => 'SEVERAL-CLIENT.EXPORT'
			],
			[
				'module' => '客户',
				'name'   => '审核',
				'code'   => 'SEVERAL-CLIENT.REVIEW'
			],
			[
				'module' => '客户',
				'name'   => '取消审核',
				'code'   => 'SEVERAL-CLIENT.CANCEL_REVIEW'
			],
			[
				'module' => '客户',
				'name'   => '签到',
				'code'   => 'SEVERAL-CLIENT.SIGN'
			],
			[
				'module' => '客户',
				'name'   => '取消签到',
				'code'   => 'SEVERAL-CLIENT.CANCEL_SIGN'
			],
			[
				'module' => '客户',
				'name'   => '字段控制',
				'code'   => 'SEVERAL-CLIENT.MANAGE_COLUMN'
			],
			[
				'module' => '客户',
				'name'   => '列表字段控制',
				'code'   => 'SEVERAL-CLIENT.MANAGE_LIST_COLUMN'
			],
			[
				'module' => '客户',
				'name'   => '搜索字段控制',
				'code'   => 'SEVERAL-CLIENT.MANAGE_SEARCH_COLUMN'
			],
			[
				'module' => '客户',
				'name'   => '重复记录配置',
				'code'   => 'SEVERAL-CLIENT.REPEAT_CONFIGURE'
			],
			[
				'module' => '客户',
				'name'   => '同步微信数据',
				'code'   => 'SEVERAL-CLIENT.SYNCHRONIZE_WECHAT_DATA'
			],
			[
				'module' => '客户',
				'name'   => '发送邀请',
				'code'   => 'SEVERAL-CLIENT.SEND_INVITATION'
			],
			[
				'module' => '客户',
				'name'   => '批量分房',
				'code'   => 'SEVERAL-CLIENT.MULTI_CHECK_IN_ROOM'
			],
			[
				'module' => '客户',
				'name'   => '批量分组',
				'code'   => 'SEVERAL-CLIENT.MULTI_ASSIGN_GROUP'
			],
			[
				'module' => '客户',
				'name'   => '批量打印胸卡',
				'code'   => 'SEVERAL-CLIENT.MULTI_PRINT_BADGE'
			],
			[
				'module' => '客户',
				'name'   => '领取礼品',
				'code'   => 'SEVERAL-CLIENT.GIFT'
			],
			[
				'module' => '客户',
				'name'   => '退还礼品',
				'code'   => 'SEVERAL-CLIENT.REFUND_GIFT'
			],
			[
				'module' => '客户',
				'name'   => '复制',
				'code'   => 'SEVERAL-CLIENT.COPY'
			],
			// 会所
			[
				'module' => '会所',
				'name'   => '修改',
				'code'   => 'SEVERAL-UNIT.MODIFY'
			],
			[
				'module' => '会所',
				'name'   => '查看',
				'code'   => 'SEVERAL-UNIT.VIEW'
			],
			[
				'module' => '会所',
				'name'   => '检索',
				'code'   => 'SEVERAL-UNIT.SEARCH'
			],
			[
				'module' => '会所',
				'name'   => '禁用',
				'code'   => 'SEVERAL-UNIT.DISABLE'
			],
			[
				'module' => '会所',
				'name'   => '启用',
				'code'   => 'SEVERAL-UNIT.ENABLE'
			],
			// 分组
			[
				'module' => '分组',
				'name'   => '创建',
				'code'   => 'SEVERAL-GROUPING.CREATE'
			],
			[
				'module' => '分组',
				'name'   => '修改',
				'code'   => 'SEVERAL-GROUPING.MODIFY'
			],
			[
				'module' => '分组',
				'name'   => '查看',
				'code'   => 'SEVERAL-GROUPING.VIEW'
			],
			[
				'module' => '分组',
				'name'   => '检索',
				'code'   => 'SEVERAL-GROUPING.SEARCH'
			],
			[
				'module' => '分组',
				'name'   => '删除',
				'code'   => 'SEVERAL-GROUPING.DELETE'
			],
			[
				'module' => '分组',
				'name'   => '禁用',
				'code'   => 'SEVERAL-GROUPING.DISABLE'
			],
			[
				'module' => '分组',
				'name'   => '启用',
				'code'   => 'SEVERAL-GROUPING.ENABLE'
			],
			[
				'module' => '分组',
				'name'   => '组员管理',
				'code'   => 'SEVERAL-GROUPING.MANAGE_MEMBER'
			],
			[
				'module' => '分组',
				'name'   => '组员管理-添加组员',
				'code'   => 'SEVERAL-GROUPING.ADD_MEMBER'
			],
			[
				'module' => '分组',
				'name'   => '组员管理-清空组员',
				'code'   => 'SEVERAL-GROUPING.CLEAN_MEMBER'
			],
			// 收款
			[
				'module' => '收款',
				'name'   => '创建',
				'code'   => 'SEVERAL-RECEIVABLES.CREATE'
			],
			[
				'module' => '收款',
				'name'   => '修改',
				'code'   => 'SEVERAL-RECEIVABLES.MODIFY'
			],
			[
				'module' => '收款',
				'name'   => '查看',
				'code'   => 'SEVERAL-RECEIVABLES.VIEW'
			],
			[
				'module' => '收款',
				'name'   => '检索',
				'code'   => 'SEVERAL-RECEIVABLES.SEARCH'
			],
			[
				'module' => '收款',
				'name'   => '筛选',
				'code'   => 'SEVERAL-RECEIVABLES.FILTER'
			],
			[
				'module' => '收款',
				'name'   => '删除',
				'code'   => 'SEVERAL-RECEIVABLES.DELETE'
			],
			[
				'module' => '收款',
				'name'   => '禁用',
				'code'   => 'SEVERAL-RECEIVABLES.DISABLE'
			],
			[
				'module' => '收款',
				'name'   => '启用',
				'code'   => 'SEVERAL-RECEIVABLES.ENABLE'
			],
			[
				'module' => '收款',
				'name'   => '导出',
				'code'   => 'SEVERAL-RECEIVABLES.EXPORT'
			],
			[
				'module' => '收款',
				'name'   => '审核',
				'code'   => 'SEVERAL-RECEIVABLES.REVIEW'
			],
			[
				'module' => '收款',
				'name'   => '取消审核',
				'code'   => 'SEVERAL-RECEIVABLES.CANCEL_REVIEW'
			],
			[
				'module' => '收款',
				'name'   => '配置',
				'code'   => 'SEVERAL-RECEIVABLES.CONFIGURE'
			],
			[
				'module' => '收款',
				'name'   => '复制',
				'code'   => 'SEVERAL-RECEIVABLES.COPY'
			],
			[
				'module' => '收款',
				'name'   => '打印',
				'code'   => 'SEVERAL-RECEIVABLES.PRINT'
			],
			[
				'module' => '收款',
				'name'   => '创建客户',
				'code'   => 'SEVERAL-RECEIVABLES.CREATE_CLIENT'
			],
			[
				'module' => '收款',
				'name'   => '列表字段控制',
				'code'   => 'SEVERAL-RECEIVABLES.MANAGE_LIST_COLUMN'
			],
			[
				'module' => '收款',
				'name'   => '搜索字段控制',
				'code'   => 'SEVERAL-RECEIVABLES.MANAGE_SEARCH_COLUMN'
			],
			// 酒店
			[
				'module' => '酒店',
				'name'   => '创建',
				'code'   => 'SEVERAL-HOTEL.CREATE'
			],
			[
				'module' => '酒店',
				'name'   => '修改',
				'code'   => 'SEVERAL-HOTEL.MODIFY'
			],
			[
				'module' => '酒店',
				'name'   => '查看',
				'code'   => 'SEVERAL-HOTEL.VIEW'
			],
			[
				'module' => '酒店',
				'name'   => '检索',
				'code'   => 'SEVERAL-HOTEL.SEARCH'
			],
			[
				'module' => '酒店',
				'name'   => '删除',
				'code'   => 'SEVERAL-HOTEL.DELETE'
			],
			[
				'module' => '酒店',
				'name'   => '禁用',
				'code'   => 'SEVERAL-HOTEL.DISABLE'
			],
			[
				'module' => '酒店',
				'name'   => '启用',
				'code'   => 'SEVERAL-HOTEL.ENABLE'
			],
			// 房间类型
			[
				'module' => '房间类型',
				'name'   => '创建',
				'code'   => 'SEVERAL-ROOM_TYPE.CREATE'
			],
			[
				'module' => '房间类型',
				'name'   => '修改',
				'code'   => 'SEVERAL-ROOM_TYPE.MODIFY'
			],
			[
				'module' => '房间类型',
				'name'   => '查看',
				'code'   => 'SEVERAL-ROOM_TYPE.VIEW'
			],
			[
				'module' => '房间类型',
				'name'   => '检索',
				'code'   => 'SEVERAL-ROOM_TYPE.SEARCH'
			],
			[
				'module' => '房间类型',
				'name'   => '删除',
				'code'   => 'SEVERAL-ROOM_TYPE.DELETE'
			],
			[
				'module' => '房间类型',
				'name'   => '禁用',
				'code'   => 'SEVERAL-ROOM_TYPE.DISABLE'
			],
			[
				'module' => '房间类型',
				'name'   => '启用',
				'code'   => 'SEVERAL-ROOM_TYPE.ENABLE'
			],
			// 房间
			[
				'module' => '房间',
				'name'   => '创建',
				'code'   => 'SEVERAL-ROOM.CREATE'
			],
			[
				'module' => '房间',
				'name'   => '修改',
				'code'   => 'SEVERAL-ROOM.MODIFY'
			],
			[
				'module' => '房间',
				'name'   => '查看',
				'code'   => 'SEVERAL-ROOM.VIEW'
			],
			[
				'module' => '房间',
				'name'   => '检索',
				'code'   => 'SEVERAL-ROOM.SEARCH'
			],
			[
				'module' => '房间',
				'name'   => '删除',
				'code'   => 'SEVERAL-ROOM.DELETE'
			],
			[
				'module' => '房间',
				'name'   => '禁用',
				'code'   => 'SEVERAL-ROOM.DISABLE'
			],
			[
				'module' => '房间',
				'name'   => '启用',
				'code'   => 'SEVERAL-ROOM.ENABLE'
			],
			[
				'module' => '房间',
				'name'   => '下载导入模板',
				'code'   => 'SEVERAL-ROOM.DOWNLOAD_IMPORT_TEMPLATE'
			],
			[
				'module' => '房间',
				'name'   => '管理&详情',
				'code'   => 'SEVERAL-ROOM.MANAGE_DETAIL'
			],
			// 报表-客户
			[
				'module' => '报表-客户',
				'name'   => '查看',
				'code'   => 'SEVERAL-REPORT_CLIENT.VIEW'
			],
			[
				'module' => '报表-客户',
				'name'   => '筛选',
				'code'   => 'SEVERAL-REPORT_CLIENT.FILTER'
			],
			[
				'module' => '报表-客户',
				'name'   => '列表字段控制',
				'code'   => 'SEVERAL-REPORT_CLIENT.MANAGE_LIST_COLUMN'
			],
			// 报表-会所
			[
				'module' => '报表-会所',
				'name'   => '查看',
				'code'   => 'SEVERAL-REPORT_UNIT.VIEW'
			],
			[
				'module' => '报表-会所',
				'name'   => '筛选',
				'code'   => 'SEVERAL-REPORT_UNIT.FILTER'
			],
			// 报表-收款
			[
				'module' => '报表-收款',
				'name'   => '查看',
				'code'   => 'SEVERAL-REPORT_RECEIVABLES.VIEW'
			],
		];
	}