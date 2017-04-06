<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-6
	 * Time: 17:19
	 */
	namespace CMS\Logic;

	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;

	class RoleLogic extends CMSLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'get_role_ajax':
					if(UserLogic::isPermitted('GENERAL-ROLE.VIEW')){
						/** @var \General\Model\RoleModel $model */
						$model       = D('General/Role');
						$role_record = $model->findRole(1, ['id' => I('post.id', 0, 'int')]);

						return (array_merge($role_record, ['__ajax__' => true]));
					}
					else{
						return ['status' => false, 'message' => '您没有查看角色信息的权限', '__ajax__' => true];
					}
				break;
				case 'modify_role':
					if(UserLogic::isPermitted('GENERAL-ROLE.MODIFY')){
						/** @var \General\Model\RoleModel $role_model */
						$role_model          = D('General/Role');
						$filter              = ['id' => I('post.id', 0, 'int')];
						$data                = I('post.');
						$str_obj             = new StringPlus();
						$data['name_pinyin'] = $str_obj->getPinyin($data['name'], true, '');
						$result              = $role_model->modifyRole($filter, $data); //传值到model里面操作
						return array_merge($result, ['__ajax__' => false]);
					}
					else{
						return ['status' => false, 'message' => '您没有修改角色的权限', '__ajax__' => false];
					}
				break;
				case 'create': //新增角色
					if(UserLogic::isPermitted('GENERAL-ROLE.CREATE')){ // 创建系统角色
						$id = Session::getCurrentUser(); //当前用户id
						/** @var \General\Model\RoleModel $role_model */
						$role_model          = D('General/Role');
						$str_obj             = new StringPlus();
						$data                = I('post.');
						$data['name_pinyin'] = $str_obj->getPinyin(I('post.name', ''), true, '');
						$data['creatime']    = date('Y-m-d H:i:s', time());
						$data['creator']     = $id;
						$result              = $role_model->createRole($data);

						return array_merge($result, ['__ajax__' => false]);
					}
					else{
						return ['status' => false, 'message' => '您没有创建角色的权限', '__ajax__' => false];
					}
				break;
				case 'get_assigned_permission':
					$role_id = I('post.id', 0, 'int');
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					$list       = $role_model->getPermission($role_id);

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'get_unassigned_permission':
					$role_id = I('post.id', 0, 'int');
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					$list       = $role_model->getPermission($role_id, false);

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'assign_permission':
					$role_id       = I('post.id', 0, 'int');
					$permission_id = I('post.pid', 0, 'int');
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					C('TOKEN_ON', false);
					$result = $role_model->grantPermission($permission_id, $role_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'anti_assign_permission':
					$role_id       = I('post.id', 0, 'int');
					$permission_id = I('post.pid', 0, 'int');
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					C('TOKEN_ON', false);
					$result = $role_model->revokePermission($permission_id, $role_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'assign_permission_module':
					$role_id = I('post.id', 0, 'int');
					$module  = I('post.module', '');
					/** @var \General\Model\PermissionModel $permission_model */
					$permission_model = D('General/Permission');
					/** @var \General\Model\RoleModel $role_model */
					$role_model      = D('General/Role');
					$permission_list = $permission_model->where("code like '$module%'")->select();
					$permission_arr  = [];
					foreach($permission_list as $permission) $permission_arr[] = $permission['id'];
					$result = $role_model->grantPermission($permission_arr, $role_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'anti_assign_permission_module':
					$role_id = I('post.id', 0, 'int');
					$module  = I('post.module', '');
					/** @var \General\Model\PermissionModel $permission_model */
					$permission_model = D('General/Permission');
					/** @var \General\Model\RoleModel $role_model */
					$role_model      = D('General/Role');
					$permission_list = $permission_model->where("code like '$module%'")->select();
					$permission_arr  = [];
					foreach($permission_list as $permission) $permission_arr[] = $permission['id'];
					$result = $role_model->revokePermission($permission_arr, $role_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => false];
				break;
			}
		}

		public function setData($type, $data){
			switch($type){
				case 'manage':
					foreach($data as $key => $role){
						$data[$key]['status_code'] = $role['status'];
						$data[$key]['status']      = GeneralModel::STATUS[$role['status']];
					}

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}
	}