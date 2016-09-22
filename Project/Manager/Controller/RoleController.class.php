<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-9
	 * Time: 16:56
	 */
	namespace Manager\Controller;

	use Think\Page;

	class RoleController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function create(){
			$this->display();
		}

		public function manage(){
			$setEmployeeCount = function ($list) use (&$model){
				$result = [];
				/** @var \Core\Model\RoleModel $model */
				foreach($list as $val){
					$tmp          = $model->getUserOfRole($val['id']);
					$val['count'] = count($tmp);
					$result[]     = $val;
				}

				return $result;
			};
			/** @var \Core\Model\RoleModel $model */
			$model = D('Core/Role');
			/** @var \Core\Model\PermissionModel $permission_model */
			$permission_model = D('Core/Permission');
			/** @var \Core\Model\AssignPermissionModel $assign_permission_model */
			$assign_permission_model = D('Core/AssignPermission');
			if(IS_POST){
				$type = strtolower(I('post.requestType', ''));
				switch($type){
					case 'create':
						$result = $model->createRole(I('post.'));
						if($result['status']) $this->success($result['message']);
						else $this->error($result['message'], '', 3);
					break;
					case 'get_assigned_permission':
						$result = $permission_model->getPermissionOfRole(I('post.id', 0, 'int'), null);
						echo json_encode($result);
					break;
					case 'get_unassigned_permission':
						$result = $permission_model->getPermissionOfRole(I('post.id', 0, 'int'), null, true, I('post.keyword', ''));
						echo json_encode($result);
					break;
					case 'assign_permission':
						$result = $assign_permission_model->assignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 0);
						echo json_encode($result);
					break;
					case 'anti_assign_permission':
						$result = $assign_permission_model->antiAssignPermission(I('post.pid', 0, 'int'), I('post.id', 0, 'int'), 0);
						echo json_encode($result);
					break;
					default:
					break;
				}
				exit;
			}
			$max_role_level = $model->getMaxRoleLevel(I('session.MANAGER_USER_ID', 0, 'int'));
			$list_total     = $model->findRole(0, ['keyword' => I('get.keyword', '')]);
			$page_object    = new Page($list_total, 10);
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			$role_list = $model->findRole(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'order'   => I('get.column', 'id').' '.I('get.sort', 'desc')
			]);
			$role_list = $setEmployeeCount($role_list);
			$this->assign('role_list', $role_list);
			$this->assign('page_show', $page_show);
			$this->assign('max_role_level', $max_role_level ? $max_role_level : 5);
			$this->display();
		}

		public function userListOfRole(){
			$role_id = I('get.id', 0, 'int');
			/** @var \Core\Model\RoleModel $model */
			$model = D('Core/Role');
			$list  = $model->getUserOfRole($role_id);
		}

	}