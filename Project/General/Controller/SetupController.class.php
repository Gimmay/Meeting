<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-2
	 * Time: 17:57
	 */
	namespace General\Controller;

	use CMS\Logic\MeetingLogic as CMSMeetingLogic;
	use CMS\Logic\Session;
	use General\Logic\MeetingLogic;
	use General\Logic\PermissionLogic;
	use General\Logic\Time;
	use General\Logic\UserLogic;
	use Quasar\Utility\StringPlus;
	use Think\Model;

	class SetupController{
		// 默认管理员信息
		const ADMIN_NAME     = 'admin';
		const ADMIN_PASSWORD = '123654abc';
		const ADMIN_NICKNAME = '管理员';
		// 默认管理员角色信息
		const ADMIN_ROLE_NAME  = '系统管理员';
		const ADMIN_ROLE_LEVEL = 1;

		public function initData(){
			// 初始化数据开始
			echo '======================初始化数据开始======================';
			echo '<br>';
			$str_obj = new StringPlus();
			// 1、创建权限表
			/** @var \General\Model\PermissionModel $permission_model */
			$permission_model = D('General/Permission');
			$permission_list  = $permission_model->find();
			if($permission_list) echo '1、权限记录已存在';
			else{
				$data = [];
				foreach(PermissionLogic::ALL_PERMISSION as $permission){
					$permission['name_pinyin']   = $str_obj->getPinyin($permission['name'], true, '');
					$permission['module_pinyin'] = $str_obj->getPinyin($permission['module'], true, '');
					$permission['creatime']      = Time::getCurrentTime();
					$data[]                      = $permission;
				}
				$result = $permission_model->addAll($data);
				if($result) echo '1、已创建权限'.count($data).'个权限记录';
				else echo '1、创建权限列表失败';
			}
			$permission_list = $permission_model->select();
			echo '<br>';
			// 2、创建管理员账号
			/** @var \General\Model\UserModel $user_model */
			$user_model = D('General/User');
			if($user_model->fetch(['name' => 'admin'])){
				echo '2、['.self::ADMIN_NICKNAME.']已存在';
				$exist_admin_user = $user_model->getObject();
				$admin_user_id    = $exist_admin_user['id'];
			}
			else{
				$user_logic = new UserLogic();
				$password   = $user_logic->makePassword(self::ADMIN_PASSWORD, self::ADMIN_NAME);
				C('TOKEN_ON', false);
				$result = $user_model->create([
					'name'            => self::ADMIN_NAME,
					'password'        => $password,
					'nickname'        => self::ADMIN_NICKNAME,
					'parent_id'       => 0,
					'creatime'        => Time::getCurrentTime(),
					'creator'         => 1,
					'email'           => 'admin@admin.com',
					'mobile'          => '0',
					'name_pinyin'     => $str_obj->getPinyin(self::ADMIN_NAME, true, ''),
					'nickname_pinyin' => $str_obj->getPinyin(self::ADMIN_NICKNAME, true, '')
				]);
				if($result['status']) echo '2、创建用户['.self::ADMIN_NICKNAME.']成功 登入密码为'.self::ADMIN_PASSWORD;
				else echo "2、创建用户[".self::ADMIN_NICKNAME."]失败: $result[message]";
				$admin_user_id = $result['id'];
			}
			session(Session::LOGIN_USER_ID, $admin_user_id);
			echo '<br>';
			// 3、创建管理员角色
			/** @var \General\Model\RoleModel $role_model */
			$role_model = D('General/Role');
			if($role_model->fetch([
				'name'  => self::ADMIN_ROLE_NAME,
				'level' => self::ADMIN_ROLE_LEVEL
			])
			){
				echo '3、['.self::ADMIN_ROLE_NAME.']已存在';
				$exist_admin_role = $role_model->getObject();
				$admin_role_id    = $exist_admin_role['id'];
			}
			else{
				C('TOKEN_ON', false);
				$result = $role_model->create([
					'name'        => self::ADMIN_ROLE_NAME,
					'name_pinyin' => $str_obj->getPinyin(self::ADMIN_ROLE_NAME, true, ''),
					'creatime'    => Time::getCurrentTime(),
					'creator'     => $admin_user_id,
					'level'       => self::ADMIN_ROLE_LEVEL
				]);
				if($result['status']) echo '3、创建角色['.self::ADMIN_NICKNAME.']成功';
				else echo "3、创建角色[".self::ADMIN_NICKNAME."]失败: $result[message]";
				$admin_role_id = $result['id'];
			}
			echo '<br>';
			// 4、绑定管理员和管理员角色
			C('TOKEN_ON', false);
			$result = $user_model->assignRole($admin_role_id, $admin_user_id);
			if($result['status']) echo '4、为['.self::ADMIN_NICKNAME.']分配['.self::ADMIN_ROLE_NAME.']成功';
			else echo '4、为['.self::ADMIN_NICKNAME.']分配['.self::ADMIN_ROLE_NAME."]失败: $result[message]";
			echo '<br>';
			// 5、为管理员角色分配所有权限
			$permission_arr = [];
			foreach($permission_list as $permission) $permission_arr[] = $permission['id'];
			$result = $role_model->grantPermission($permission_arr, $admin_role_id);
			if($result['status']) echo '5、为['.self::ADMIN_ROLE_NAME.']授予了'.count($permission_arr).'项权限';
			else echo '5、没有为['.self::ADMIN_ROLE_NAME."]授予任何权限: $result[message]";
			echo '<br>';
			// 6、创建会议类型
			/** @var \General\Model\MeetingTypeModel $meeting_type_model */
			$meeting_type_model = D('General/MeetingType');
			$meeting_type_model->where('0 = 0')->delete();
			$data = [];
			foreach(MeetingLogic::TYPE as $key => $type){
				if($key == 0) continue;
				$data[] = [
					'type'     => $key,
					'name'     => $type,
					'creatime' => Time::getCurrentTime()
				];
			}
			$result = $meeting_type_model->addAll($data);
			if($result) echo "6、已创建".count($data)."个会议类型";
			else echo "6、创建会议类型失败";
			echo '<br>';
			// 7、创建会议字段记录
			$meeting_column_control_logic = new CMSMeetingLogic();
			$result                       = $meeting_column_control_logic->initMeetingColumnControlRecord();
			echo "7、$result[message]";
			echo '<br>';
			// 初始化数据结束
			Session::cleanAll();
			echo '======================初始化数据结束======================';
		}
	}