<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 15:08
	 */
	namespace Manager\Logic;

	use Quasar\StringPlus;

	class RoleLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'create':
					if($this->permissionList['ROLE.CREATE']){
						/** @var \Core\Model\RoleModel $model */
						$model               = D('Core/Role');
						$str_obj             = new StringPlus();
						$data                = I('post.');
						$data['pinyin_code'] = $str_obj->makePinyinCode(I('post.name', ''));
						$data['creatime']    = time();
						$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$result              = $model->createRole($data);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建角色的权限', '__ajax__' => false];
				break;
				case 'get_assigned_permission':
					if($this->permissionList['ROLE.VIEW-ASSIGNED-PERMISSION']){
						/** @var \Core\Model\PermissionModel $permission_model */
						$permission_model = D('Core/Permission');
						$result           = $permission_model->getPermissionOfRole(I('post.id', 0, 'int'), null);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有查看已分配权限的权限', '__ajax__' => true];
				break;
				case 'get_unassigned_permission':
					if($this->permissionList['ROLE.VIEW-UNASSIGNED-PERMISSION']){
						/** @var \Core\Model\PermissionModel $permission_model */
						$permission_model = D('Core/Permission');
						$result           = $permission_model->getPermissionOfRole(I('post.id', 0, 'int'), null, true, I('post.keyword', ''));

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有查看未分配权限的权限', '__ajax__' => true];
				break;
				case 'assign_permission':
					if($this->permissionList['ROLE.ASSIGN-PERMISSION']){
						$assign_permission_logic = new AssignPermissionLogic();
						$result                  = $assign_permission_logic->assignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 0);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有授权的权限', '__ajax__' => true];
				break;
				case 'anti_assign_permission':
					if($this->permissionList['ROLE.ANTI-ASSIGN-PERMISSION']){
						$assign_permission_logic = new AssignPermissionLogic();
						$result                  = $assign_permission_logic->antiAssignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 0);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有收回权限的权限', '__ajax__' => true];
				break;
				case 'delete':
					if($this->permissionList['ROLE.DELETE']){
						/** @var \Core\Model\RoleModel $model */
						$model  = D('Core/Role');
						$result = $model->deleteRole([I('post.id', 0, 'int')]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有删除角色的权限', '__ajax__' => false];
				break;
				case 'alter':
					if($this->permissionList['ROLE.ALTER']){
						/** @var \Core\Model\RoleModel $model */
						$model  = D('Core/Role');
						$result = $model->alterRole(['id'=>I('post.id', 0, 'int')], I('post.')); //传值到model里面操作
						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有修改角色的权限', '__ajax__' => false];
				break;
				case 'get_role':
					if($this->permissionList['ROLE.VIEW']){
						/** @var \Core\Model\RoleModel $model */
						$model       = D('Core/Role');
						$role_record = $model->findRole(1, ['id' => I('post.id', 0, 'int')]);

						return (array_merge($role_record, ['__ajax__' => true]));
					}
					else return ['status' => false, 'message' => '您没有查看角色信息的权限', '__ajax__' => true];
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}
	}