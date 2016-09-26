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

		public function create($data){
			/** @var \Core\Model\RoleModel $model */
			$model               = D('Core/Role');
			$str_obj             = new StringPlus();
			$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
			$data['creatime']    = time();
			$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');

			return $model->createRole($data);
		}

		public function handlerRequest($type){
			switch($type){
				case 'create':
					return $this->create(I('post.'));
				break;
				case 'get_assigned_permission':
					/** @var \Core\Model\PermissionModel $permission_model */
					$permission_model = D('Core/Permission');
					$result           = $permission_model->getPermissionOfRole(I('post.id', 0, 'int'), null);
					echo json_encode($result);

					return -1;
				break;
				case 'get_unassigned_permission':
					/** @var \Core\Model\PermissionModel $permission_model */
					$permission_model = D('Core/Permission');
					$result           = $permission_model->getPermissionOfRole(I('post.id', 0, 'int'), null, true, I('post.keyword', ''));
					echo json_encode($result);

					return -1;
				break;
				case 'assign_permission':
					$assign_permission_logic = new AssignPermissionLogic();
					$result                  = $assign_permission_logic->assignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 0);
					echo json_encode($result);

					return -1;
				break;
				case 'anti_assign_permission':
					$assign_permission_logic = new AssignPermissionLogic();
					$result                  = $assign_permission_logic->antiAssignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 0);
					echo json_encode($result);

					return -1;
				break;
				case 'delete':
					/** @var \Core\Model\RoleModel $model */
					$model = D('Core/Role');

					return $model->deleteRole(I('post.id'));
				break;
				default:
					echo json_encode(['status' => false, 'message' => '参数错误']);

					return -1;
				break;
			}
		}
	}