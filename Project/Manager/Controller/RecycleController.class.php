<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-6
	 * Time: 10:13
	 */
	namespace Manager\Controller;

	use Think\Page;

	class RecycleController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function client(){
			/** @var \Core\Model\RecycleModel $recycle_model */
			$recycle_model = D('Core/Recycle');

			$list_total = $recycle_model->findClient(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 2,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $recycle_model->findClient(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery = $recycle_model->recoveryClient($client_id);
				if($recovery['status']) $this->success($recovery['message'],U('client'));
				else $this->error($recovery['message']);
				exit;
			}
			$this->assign('list',$client_list);
			$this->assign('page_list',$page_show);
			$this->display();
		}

		public function employee(){
			/** @var \Core\Model\RecycleModel $employee_model */
			$employee_model = D('Core/Recycle');

			$list_total = $employee_model->findEmployee(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 2,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $employee_model->findEmployee(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery = $employee_model->recoveryEmployee($client_id);
				if($recovery['status']) $this->success($recovery['message'],U('employee'));
				else $this->error($recovery['message']);
				exit;
			}
			$this->assign('list',$client_list);
			$this->assign('page_list',$page_show);
			$this->display();
		}

		public function meeting(){
			/** @var \Core\Model\RecycleModel $recycle_model */
			$meeting_model = D('Core/Recycle');

			$list_total = $meeting_model->findMeeting(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 2,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $meeting_model->findMeeting(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery = $meeting_model->recoveryClient($client_id);
				if($recovery['status']) $this->success($recovery['message'],U('meeting'));
				else $this->error($recovery['message']);
				exit;
			}
			foreach($client_list as $k => $v){
				$aa['id'] = $client_list[$k]['id'];
			}
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/employee');
			$employee_direactor = $employee_model->findEmployee(2,$aa);
			
			$this->assign('list',$client_list);
			$this->assign('page_list',$page_show);
			$this->display();
		}

		public function role(){
			/** @var \Core\Model\RecycleModel $recycle_model */
			$role_model = D('Core/Recycle');

			$list_total = $role_model->findRole(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 2,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $role_model->findRole(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery = $role_model->recoveryRole($client_id);
				if($recovery['status']) $this->success($recovery['message'],U('Role'));
				else $this->error($recovery['message']);
				exit;
			}
			$this->assign('list',$client_list);
			$this->assign('page_list',$page_show);
			$this->display();
		}
	}