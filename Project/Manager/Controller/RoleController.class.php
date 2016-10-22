<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-9
	 * Time: 16:56
	 */
	namespace Manager\Controller;

	use Manager\Logic\AssignPermissionLogic;
	use Manager\Logic\MeetingLogic;
	use Manager\Logic\RoleLogic;
	use Think\Page;

	class RoleController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$setExtendColumn = function ($list) use (&$model){
				$result = [];
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				/** @var \Core\Model\RoleModel $model */
				foreach($list as $val){
					$tmp = $model->getUserOfRole($val['id']);
					if($val['effect'] != 0){
						$meeting_record = $meeting_model->findMeeting(1, ['id' => $val['effect']]);
						$val['meeting'] = $meeting_record['name'];
					}
					$val['count'] = count($tmp);
					$result[]     = $val;
				}

				return $result;
			};
			$logic           = new RoleLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['ROLE.VIEW']){
				/** @var \Core\Model\RoleModel $model */
				$model          = D('Core/Role');
				$meeting_logic  = new MeetingLogic();
				$max_role_level = $model->getMaxRoleLevel(I('session.MANAGER_EMPLOYEE_ID', 0, 'int'));
				/* ↓↓↓↓↓ 获取列表数据 ↓↓↓↓↓ */
				$list_total  = $model->findRole(0, ['keyword' => I('get.keyword', ''), 'status' => 'not deleted']);
				$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show = $page_object->show();
				$role_list = $model->findRole(2, [
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
					'status'  => 'not deleted'
				]);
				$role_list = $setExtendColumn($role_list);
				/* ↑↑↑↑↑ 获取列表数据 ↑↑↑↑↑ */
				$meeting_record = $meeting_logic->getSelectListForRole();
				$this->assign('meeting_list', $meeting_record);
				$this->assign('role_list', $role_list);
				$this->assign('page_show', $page_show);
				$this->assign('max_role_level', $max_role_level ? $max_role_level : 5);
				$this->display();
			}
			else{
				$this->error('您没有查看角色的权限');
			}
		}
	}