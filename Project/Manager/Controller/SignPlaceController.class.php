<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 10:13
	 */
	namespace Manager\Controller;

	use Manager\Logic\SignPlaceLogic;
	use Think\Page;

	class SignPlaceController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function create(){
			if(IS_POST){
				$logic  = new SignPlaceLogic();
				$result = $logic->create(I('post.'));
				if($result['status']) $this->success('添加签到点成功');
				else $this->error($result['message']);
				exit;
			}
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$meeting_list   = $meeting_model->getMeetingForSelect();
			$employee_list  = $employee_model->getEmployeeSelectList();
			$this->assign('meeting_list', $meeting_list);
			$this->assign('employee_list', $employee_list);
			$this->display();
		}

		public function manage(){
			if(IS_POST){
				exit;
			}
			$logic = new SignPlaceLogic();
			/** @var \Core\Model\SignPlaceModel $model */
			$model = D('Core/SignPlace');
			$list_total  = $model->findRecord(0, ['keyword' => I('get.keyword', ''), 'status' => 'not deleted']);
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show   = $page_object->show();
			$record_list = $model->findRecord(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'id').' '.I('get.sort', 'desc'),
				'status'  => 'not deleted'
			]);
			$record_list = $logic->setExtendColumn($record_list);
			$this->assign('page_show', $page_show);
			$this->assign('list', $record_list);
			$this->display();
		}
	}