<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-2
	 * Time: 16:41
	 */
	namespace CMS\Controller;

	use CMS\Logic\PageLogic;
	use CMS\Logic\UserLogic;
	use CMS\Model\CMSModel;
	use Think\Page;

	class UserController extends CMS{
		public function _initialize(){
			parent::_initialize();
		}

		public function login(){
			if(IS_POST){
				$user_logic = new UserLogic();
				$type       = strtolower(I('post.requestType', ''));
				$result     = $user_logic->handlerRequest($type, [
					'username' => I('post.username', ''),
					'password' => I('post.password', '')
				]);
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
			$this->display();
		}

		public function manage(){
			$user_logic = new UserLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $user_logic->handlerRequest($type);
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
			if(!UserLogic::isPermitted('GENERAL-USER.VIEW')) $this->error('您没有查看用户的权限');
			/** @var \CMS\Model\UserModel $user_model */
			$user_model           = D('CMS/User');
			$model_control_column = $this->getModelControl();
			$list                 = $user_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status'] => ['<>', 2]
			]));
			$list                 = $user_logic->setData('manage', ['list' => $list, 'urlParam' => I('get.')]);
			$page_object          = new Page(count($list), $this->getPageRecordCount()); // 实例化分页类 传入总记录数和每页显示的记录数
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$pagination = $page_object->show();// 分页显示输出
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
			$this->display();
		}

		public function create(){
			$role_logic = new UserLogic();
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
			if(!UserLogic::isPermitted('GENERAL-USER.CREATE')) $this->error('您没有创建用户的权限');
			/** @var \General\Model\UserModel $user_model */
			$user_model = D('General/User');
			$this->assign('default_password', $user_model->getDefaultPassword());
			$this->display();
		}

		/**
		 *  修改用户信息
		 */
		public function modify(){
			$logic = new UserLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
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
			if(!UserLogic::isPermitted('GENERAL-USER.MODIFY')) $this->error('您没有修改用户的权限');
			$user_id = I('get.id', 0, 'int');
			/** @var \General\Model\UserModel $user_model */
			$user_model = D('General/User');
			$user_model->fetch(['id' => $user_id]);
			$user = $user_model->getObject();
			$this->assign('user', $user);
			$this->display();
		}
	}
