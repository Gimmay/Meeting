<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-6
	 * Time: 10:13
	 */
	namespace Manager\Controller;

	use Manager\Logic\RecycleLogic;
	use Think\Page;

	class RecycleController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function client(){
			/** @var \Core\Model\RecycleModel $model */
			$model = D('Core/Recycle');

			$list_total = $model->findClient(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 2,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $model->findClient(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery = $model->recoveryClient($client_id);
				if($recovery['status']) $this->success($recovery['message'],U('client'));
				else $this->error($recovery['message']);
				exit;
			}
			$this->assign('list',$client_list);
			$this->assign('page_list',$page_show);
			$this->display();
		}

		public function employee(){
			/** @var \Core\Model\RecycleModel $model */
			$model = D('Core/Recycle');

			$list_total = $model->findEmployee(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 2,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $model->findEmployee(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery = $model->recoveryEmployee($client_id);
				if($recovery['status']) $this->success($recovery['message'],U('employee'));
				else $this->error($recovery['message']);
				exit;
			}
			$this->assign('list',$client_list);
			$this->assign('page_list',$page_show);
			$this->display();
		}

		public function meeting(){
			/** @var \Core\Model\RecycleModel $model */
			$model = D('Core/Recycle');

			$list_total = $model->findMeeting(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 2,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $model->findMeeting(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 4,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery = $model->recoveryClient($client_id);
				if($recovery['status']) $this->success($recovery['message'],U('meeting'));
				else $this->error($recovery['message']);
				exit;
			}
			foreach($client_list as $k => $v){
				$aa['id'] = $client_list[$k]['id'];
			}
			$recycle_logic = new RecycleLogic();
			$client_list = $recycle_logic->FindEmployee($client_list);	
			$this->assign('list',$client_list);
			$this->assign('page_list',$page_show);
			$this->display();
		}

		public function role(){
			/** @var \Core\Model\RecycleModel $model */
			$model = D('Core/Recycle');

			$list_total = $model->findRole(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 2,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $model->findRole(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery = $model->recoveryRole($client_id);
				if($recovery['status']) $this->success($recovery['message'],U('Role'));
				else $this->error($recovery['message']);
				exit;
			}
			$this->assign('list',$client_list);
			$this->assign('page_list',$page_show);
			$this->display();
		}
	}