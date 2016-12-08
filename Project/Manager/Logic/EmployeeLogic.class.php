<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 14:04
	 */
	namespace Manager\Logic;

	use Core\Logic\SystemMessageLogic;
	use Core\Logic\WxCorpLogic;
	use Quasar\StringPlus;

	class EmployeeLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function isLogin(){
			return isset($_SESSION['MANAGER_EMPLOYEE_ID']) && session('MANAGER_EMPLOYEE_ID') ? true : false;
		}

		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'create':
					$data = I('post.');
					/** @var \Core\Model\EmployeeModel $model */
					$model   = D('Core/Employee');
					$str_obj = new StringPlus();
					//					$exist_flag = false;
					//					if(!empty($data['mobile'])){
					//						$exist_record = $model->findEmployee(1, ['mobile' => $data['mobile']]);
					//						$exist_flag   = true;
					//					}
					//					elseif(!empty($data['code'])){
					//						$exist_record = $model->findEmployee(1, ['code' => $data['code']]);
					//						$exist_flag   = true;
					//					}
					$data['status']      = $data['status'] == 1 ? 0 : (($data['status'] == 0) ? 1 : 1);
					$data['creatime']    = time();
					$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
					$data['password']    = $str_obj->makePassword($data['password'], $data['code']);
					$data['birthday']    = date('Y-m-d', strtotime($data['birthday']));
					//					if($exist_flag && isset($exist_record)){
					//						$result = $model->alterEmployee(['id' => $exist_record['id']], $data);
					//						if($result['status']) $employee_id = $exist_record['id'];
					//					}
					//					else{
					$result = $model->createEmployee($data);
					if($result['status']) $employee_id = $result['id'];
					//					}
					if($result['status'] && isset($employee_id)){
						$result['message'] = '创建成功';
						/** @var \Core\Model\WechatModel $wechat_model */
						$wechat_model = D('Core/Wechat');
						$logic        = new WxCorpLogic();
						$wx_list      = $logic->getAllUserList(); //查出wx接口获取的所有用户信息
						foreach($wx_list as $k1 => $v1){
							if($v1['mobile'] == $data['mobile'] && $v1['status'] != 4){
								C('TOKEN_ON', false);
								$department = '';
								foreach($v1['department'] as $v3) $department .= $v3.',';
								$department         = trim($department, ',');
								$data               = [];
								$data['otype']      = 0;    //对象类型
								$data['wtype']      = 1;    //微信ID类型 企业号
								$data['oid']        = $employee_id;    //对象ID
								$data['department'] = $department;    //部门ID
								$data['wid']  = $v1['userid'];    //微信ID
								$data['mobile']     = $v1['mobile'];    //手机号码
								$data['avatar']     = $v1['avatar'];    //头像地址
								$data['gender']     = $v1['gender'];    //性别
								$data['is_follow']  = $v1['status'];    //是否关注
								$data['nickname']   = $v1['name'];    //昵称
								$data['creatime']   = time();    //创建时间
								$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
								$wechat_model->createRecord($data);    //插入数据
							}
						}
					}

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'alter':
					/** @var \Core\Model\EmployeeModel $model */
					$id     = I('get.id', 0, 'int');
					$model  = D('Core/Employee');
					$result = $model->alterEmployee(['id' => $id], I('post.'));

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get_assigned_role':
					if($this->permissionList['EMPLOYEE.VIEW-ASSIGNED-ROLE']){
						/** @var \Core\Model\RoleModel $role_model */
						$role_model = D('Core/Role');
						$result     = $role_model->getRoleOfEmployee(I('post.id', 0, 'int'), null);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有查询已分配的角色的权限', '__ajax__' => true];
				break;
				case 'get_unassigned_role':
					if($this->permissionList['EMPLOYEE.VIEW-UNASSIGNED-ROLE']){
						/** @var \Core\Model\RoleModel $role_model */
						$role_model = D('Core/Role');
						$result     = $role_model->getRoleOfEmployee(I('post.id', 0, 'int'), null, true, I('post.keyword', ''));

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有查询未分配角色的权限', '__ajax__' => true];
				break;
				case 'assign_role':
					if($this->permissionList['EMPLOYEE.ASSIGN-ROLE']){
						$assign_role_logic = new AssignRoleLogic();
						$result            = $assign_role_logic->assignRole(I('post.rid', 0, 'int'), I('post.id', 0, 'int'), 0);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有分配角色的权限', '__ajax__' => true];
				break;
				case 'anti_assign_role':
					if($this->permissionList['EMPLOYEE.ANTI-ASSIGN-ROLE']){
						$assign_role_logic = new AssignRoleLogic();
						$result            = $assign_role_logic->antiAssignRole(I('post.rid', 0, 'int'), I('post.id', 0, 'int'), 0);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有收回角色的权限', '__ajax__' => true];
				break;
				case 'get_assigned_permission':
					if($this->permissionList['EMPLOYEE.VIEW-ASSIGNED-PERMISSION']){
						/** @var \Core\Model\PermissionModel $permission_model */
						$permission_model = D('Core/Permission');
						$result           = $permission_model->getPermissionOfEmployee(I('post.id', 0, 'int'), null);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有查询已分配权限的权限', '__ajax__' => true];
				break;
				case 'get_unassigned_permission':
					if($this->permissionList['EMPLOYEE.VIEW-UNASSIGNED-PERMISSION']){
						/** @var \Core\Model\PermissionModel $permission_model */
						$permission_model = D('Core/Permission');
						$result           = $permission_model->getPermissionOfEmployee(I('post.id', 0, 'int'), null, true, I('post.keyword', ''));

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有查询未分配权限的权限', '__ajax__' => true];
				break;
				case 'assign_permission':
					if($this->permissionList['EMPLOYEE.ASSIGN-PERMISSION']){
						$assign_permission_logic = new AssignPermissionLogic();
						$result                  = $assign_permission_logic->assignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 1);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有分配权限的权限', '__ajax__' => true];
				break;
				case 'anti_assign_permission':
					if($this->permissionList['EMPLOYEE.ANTI-ASSIGN-PERMISSION']){
						$type                    = I('post.type', 1, 'int');
						$result                  = ['status' => false, '参数错误'];
						$assign_permission_logic = new AssignPermissionLogic();
						if($type == 0) return ['status' => false, 'message' => '不能取消角色授权', '__ajax__' => true];
						if($type == 1) $result = $assign_permission_logic->antiAssignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 1);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有收回权限的权限', '__ajax__' => true];
				break;
				case 'delete':
					if($this->permissionList['EMPLOYEE.DELETE']){
						/** @var \Core\Model\EmployeeModel $model */
						$model  = D('Core/Employee');
						$result = $model->deleteEmployee(I('post.id'));

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有删除员工的权限', '__ajax__' => false];
				break;
				case 'reset_password':
					if($this->permissionList['EMPLOYEE.RESET-PASSWORD']){
						$str = new StringPlus();
						/** @var \Core\Model\EmployeeModel $model */
						$model = D('Core/Employee');
						$data  = I('post.');
						$id    = I('post.id');           // 账户ID
						//$new_password     = I('post.password'); // 新密码
						$new_password     = C('DEFAULT_EMPLOYEE_PASSWORD');
						$tmp              = $model->findEmployee(1, ['id' => $id]); //查询出这个ID的数据
						$password         = $str->makePassword($new_password, $tmp['code']);    //新密码加密
						$data['password'] = $password;
						$result           = $model->alterEmployee(['id' => $id], $data); //成功后就更新到数据库
						if($result['status']){
							$system_message_logic = new SystemMessageLogic();
							$system_message_logic->sendMessage($id, 'alterPasswordWhenEmptyPassword');
						}

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有重置密码的权限', '__ajax__' => false];
				break;
				case 'alter_password':
					if($this->permissionList['EMPLOYEE.ALTER-PASSWORD']){
						$str = new StringPlus();
						/** @var \Core\Model\EmployeeModel $model */
						$model          = D('Core/Employee');
						$data           = I('post.');
						$id             = I('post.id', 0, 'int');           // 账户ID
						$old_password   = I('post.old_password'); // 旧密码
						$new_password   = I('post.new_password'); // 新密码
						$input_password = $new_password;
						$info           = $model->findEmployee(1, ['id' => $id]); //查询出这个ID的数据
						$old_password   = $str->makePassword($old_password, $info['code']);    //旧密码加密
						$new_password   = $str->makePassword($new_password, $info['code']);    //新密码加密
						if($info['password'] == $old_password){    //判断加密过后的密码和数据库的密码是否匹配
							$data['password'] = $new_password;
							/** @var \Core\Model\EmployeeModel $model */
							$model  = D('Core/Employee');
							$result = $model->alterEmployee(['id' => $id], $data); //成功后就更新到数据库
							if($result['status'] && $input_password == ''){
								$system_message_logic = new SystemMessageLogic();
								$system_message_logic->sendMessage($id, 'alterPasswordWhenEmptyPassword');
							}

							return array_merge($result, ['__ajax__' => false]);
						}
						else return ['status' => false, 'message' => '密码错误', '__ajax__' => false];
					}
					else return ['status' => false, 'message' => '您没有修改密码的权限', '__ajax__' => false];
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function writeExtendInformation($list, $single = false){
			/** @var \Core\Model\AssignRoleModel $assign_role_model */
			$assign_role_model = D('Core/AssignRole');
			/** @var \Core\Model\DepartmentModel $department_model */
			$department_model = D('Core/Department');
			if($single){
				$role               = $assign_role_model->getRoleByUser($list['id']);
				$list['role']       = $role;
				$department         = $department_model->findDepartment(1, ['id' => $list['did']]);
				$list['department'] = $department['name'];
			}
			else{
				foreach($list as $key => $val){
					$role                     = $assign_role_model->getRoleByUser($val['id']);
					$list[$key]['role']       = $role;
					$department               = $department_model->findDepartment(1, ['id' => $val['did']]);
					$list[$key]['department'] = $department['name'];
				}
			}

			return $list;
		}

		public function setMaxRoleLevel($list){
			$result = [];
			/** @var \Core\Model\RoleModel $model */
			$model = D('Core/Role');
			foreach($list as $val){
				$val['roleLevel'] = $model->getMaxRoleLevel($val['id']);
				$result[]         = $val;
			}

			return $result;
		}

		public function findEmployee(){
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			/** @var \Core\Model\DepartmentModel $department_model */
			$department_model = D('Core/Department');
			$employee_result  = $employee_model->findEmployee(1, ['id' => (session('MANAGER_EMPLOYEE_ID'))]);
			foreach($employee_result as $k => $v){
				$department_result                  = $department_model->findDepartment(1, ['id' => $employee_result['did']]);
				$employee_result['department_name'] = $department_result['name'];
			}

			return $employee_result;
		}
	}