<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-9
	 * Time: 16:56
	 */
	namespace Manager\Controller;

	use Manager\Logic\AssignPermissionLogic;
	use Manager\Logic\RoleLogic;
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
			$logic = new RoleLogic();
			if(IS_POST){
				$type = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result===-1){}
				else{
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$max_role_level = $model->getMaxRoleLevel(I('session.MANAGER_EMPLOYEE_ID', 0, 'int'));
			/* ↓↓↓↓↓ 获取列表数据 ↓↓↓↓↓ */
			$list_total     = $model->findRole(0, ['keyword' => I('get.keyword', ''), 'status' => 'not deleted']);
			$page_object    = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			$role_list = $model->findRole(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'   => I('get.column', 'id').' '.I('get.sort', 'desc'),
				'status'  => 'not deleted'
			]);
			$role_list = $setEmployeeCount($role_list);
			/* ↑↑↑↑↑ 获取列表数据 ↑↑↑↑↑ */
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
			$this->assign('user_list', $list);
		}

	}