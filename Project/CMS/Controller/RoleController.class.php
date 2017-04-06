<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-6
	 * Time: 17:07
	 */
	namespace CMS\Controller;

	use CMS\Logic\PageLogic;
	use CMS\Logic\RoleLogic;
	use CMS\Logic\UserLogic;
	use Think\Page;

	class RoleController extends CMS{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$role_logic = new RoleLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $role_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			if(UserLogic::isPermitted('GENERAL-ROLE.VIEW')){
				/** @var \CMS\Model\RoleModel $role_model */
				$role_model           = D('CMS/Role');
				$model_control_column = $this->getModelControl();
				$list                 = $role_model->getList(array_merge($model_control_column, [
					'status' => [
						'<>',
						2
					]
				]));
				$page_object          = new Page(count($list), $this->getPageRecordCount()); // 实例化分页类 传入总记录数和每页显示的记录数
				PageLogic::setTheme1($page_object);
				$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
				$list       = $role_logic->setData('manage', $list);
				$pagination = $page_object->show();// 分页显示输出
				$this->assign('list', $list);
				$this->assign('pagination', $pagination);
				$this->display();
			}
			else{
				$this->error('您没有查看角色的权限');
			}
		}
	}