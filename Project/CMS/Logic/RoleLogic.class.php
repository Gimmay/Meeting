<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-6
	 * Time: 17:19
	 */
	namespace CMS\Logic;

	use CMS\Controller\CMS;
	use CMS\Model\UserModel;
	use General\Model\GeneralModel;
	use CMS\Model\RoleModel;
	use Quasar\Utility\StringPlus;

	class RoleLogic extends CMSLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'get_detail':
					$id = I('post.id', 0, 'int');
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					if(!$role_model->fetch(['id' => $id])) return ['__ajax__' => true];
					$role = $role_model->getObject();

					return array_merge($role, ['__ajax__' => true]);
				break;
				case 'modify':
					if(!UserLogic::isPermitted('GENERAL-ROLE.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改角色的权限',
						'__ajax__' => true
					];
					/** @var \General\Model\RoleModel $role_model */
					$role_model          = D('General/Role');
					$str_obj             = new StringPlus();
					$role_id             = I('post.id', 0, 'int');
					$data                = I('post.');
					$data['name_pinyin'] = $str_obj->getPinyin($data['name'], true, '');
					$result              = $role_model->modifyInformation(['id' => $role_id], $data);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'create':
					// 新增角色
					if(!UserLogic::isPermitted('GENERAL-ROLE.CREATE')) return [
						'status'   => false,
						'message'  => '您没有创建角色的权限',
						'__ajax__' => true
					];
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					$str_obj    = new StringPlus();
					$data       = I('post.');
					$result     = $role_model->create(array_merge($data, [
						'name_pinyin' => $str_obj->getPinyin(I('post.name', ''), true, ''),
						'creatime'    => date('Y-m-d H:i:s', time()),
						'creator'     => Session::getCurrentUser()
					]));

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete':
					if(!UserLogic::isPermitted('GENERAL-ROLE.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除角色的权限',
						'__ajax__' => true
					];
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					$id_str     = I('post.id', '');
					$id         = explode(',', $id_str);
					$result     = $role_model->drop(['id' => ['in', $id]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable':
					if(!UserLogic::isPermitted('GENERAL-ROLE.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用角色的权限',
						'__ajax__' => true
					];
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					$id_str     = I('post.id', '');
					$id         = explode(',', $id_str);
					$result     = $role_model->enable(['id' => ['in', $id]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable':
					if(!UserLogic::isPermitted('GENERAL-ROLE.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用角色的权限',
						'__ajax__' => true
					];
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					$id_str     = I('post.id', '');
					$id         = explode(',', $id_str);
					$result     = $role_model->disable(['id' => ['in', $id]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_assigned_permission':
					$role_id = I('post.id', 0, 'int');
					$keyword = I('post.keyword', '');
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					$list       = $role_model->getPermission($role_id, $keyword);

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'get_unassigned_permission':
					$role_id = I('post.id', 0, 'int');
					$keyword = I('post.keyword', '');
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					$list       = $role_model->getPermission($role_id, $keyword, false);

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'grant_permission':
					if(!UserLogic::isPermitted('GENERAL-ROLE.GRANT_PERMISSION')) return [
						'status'   => false,
						'message'  => '您没有授权的权限',
						'__ajax__' => true
					];
					$role_id       = I('post.id', 0, 'int');
					$permission_id = I('post.pid', 0, 'int');
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					C('TOKEN_ON', false);
					$result = $role_model->grantPermission($permission_id, $role_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'revoke_permission':
					if(!UserLogic::isPermitted('GENERAL-ROLE.GRANT_PERMISSION')) return [
						'status'   => false,
						'message'  => '您没有授权的权限',
						'__ajax__' => true
					];
					$role_id       = I('post.id', 0, 'int');
					$permission_id = I('post.pid', 0, 'int');
					/** @var \General\Model\RoleModel $role_model */
					$role_model = D('General/Role');
					C('TOKEN_ON', false);
					$result = $role_model->revokePermission($permission_id, $role_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'grant_permission_module':
					if(!UserLogic::isPermitted('GENERAL-ROLE.GRANT_PERMISSION')) return [
						'status'   => false,
						'message'  => '您没有授权的权限',
						'__ajax__' => true
					];
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
				case 'revoke_permission_module':
					if(!UserLogic::isPermitted('GENERAL-ROLE.GRANT_PERMISSION')) return [
						'status'   => false,
						'message'  => '您没有授权的权限',
						'__ajax__' => true
					];
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
					$list = [];
					$get  = $data['urlParam'];
					$data = $data['list'];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					// 若指定了角色ID的情况
					if(isset($get[RoleModel::CONTROL_COLUMN_PARAMETER_SELF['roleID']])) $role_id = $get[RoleModel::CONTROL_COLUMN_PARAMETER_SELF['roleID']];
					foreach($data as $key => $role){
						// 1、筛选数据
						if(isset($keyword)){
							$found = 0;
							if($found == 0 && stripos($role['name'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($role['name_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						if(isset($role_id)){
							if($role['id'] != $role_id) continue;
						}
						$role['status_code'] = $role['status'];
						$role['status']      = GeneralModel::STATUS[$role['status']];
						$list[]              = $role;
					}

					return $list;
				break;
				default:
					return $data;
				break;
			}
		}
	}