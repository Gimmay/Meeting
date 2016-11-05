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
			$model      = D('Core/Recycle');
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
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery  = $model->recoveryClient($client_id);
				if($recovery['status']) $this->success($recovery['message'], U('client'));
				else $this->error($recovery['message']);
				exit;
			}
			$this->assign('list', $client_list);
			$this->assign('page_list', $page_show);
			$this->display();
		}

		public function employee(){
			/** @var \Core\Model\RecycleModel $model */
			$model      = D('Core/Recycle');
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
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery  = $model->recoveryEmployee($client_id);
				if($recovery['status']) $this->success($recovery['message'], U('employee'));
				else $this->error($recovery['message']);
				exit;
			}
			$this->assign('list', $client_list);
			$this->assign('page_list', $page_show);
			$this->display();
		}

		public function meeting(){
			/** @var \Core\Model\RecycleModel $model */
			$model      = D('Core/Recycle');
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
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
				'status'  => 5,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery  = $model->recoveryClient($client_id);
				if($recovery['status']) $this->success($recovery['message'], U('meeting'));
				else $this->error($recovery['message']);
				exit;
			}
			foreach($client_list as $k => $v){
				$aa['id'] = $client_list[$k]['id'];
			}
			$recycle_logic = new RecycleLogic();
			$client_list   = $recycle_logic->FindEmployee($client_list);
			$this->assign('list', $client_list);
			$this->assign('page_list', $page_show);
			$this->display();
		}

		public function role(){
			/** @var \Core\Model\RecycleModel $model */
			$model      = D('Core/Recycle');
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
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery  = $model->recoveryRole($client_id);
				if($recovery['status']) $this->success($recovery['message'], U('Role'));
				else $this->error($recovery['message']);
				exit;
			}
			$this->assign('list', $client_list);
			$this->assign('page_list', $page_show);
			$this->display();
		}

		public function coupon(){
			/** @var \Core\Model\RecycleModel $coupon_model */
			$coupon_model = D('Core/Recycle');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$list_total = $coupon_model->findCoupon(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 2,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $coupon_model->findCoupon(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
				'status'  => 2,
			]);
			foreach($client_list as $k => $v){
				$employee_result            = $employee_model->findEmployee(1, ['id' => $client_list[$k]['creator']]);
				$client_list[$k]['creator'] = $employee_result['name'];
			}
			if(IS_POST){
				$client_id = I('post.id');
				$recovery  = $coupon_model->recoveryRole($client_id);
				if($recovery['status']) $this->success($recovery['message'], U('Coupon'));
				else $this->error($recovery['message']);
				exit;
			}
			$this->assign('coupon', $client_list);
			$this->assign('page_list', $page_show);
			$this->display();
		}

		public function couponItem(){
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			/** @var \Core\Model\CouponModel $coupon_model */
			$coupon_model = D('Core/Coupon');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/meeting');
			/** @var \Core\Model\RecycleModel $coupon_item_model */
			$coupon_item_model  = D('Core/Recycle');
			$list_total = $coupon_item_model->findCouponItem(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 3,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$coupon_item_result = $coupon_item_model->findCouponItem(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
				'status'  => 3,
			]);
			foreach($coupon_item_result as $k => $v){
				$meeting_result                      = $meeting_model->findMeeting(1, ['id' => $coupon_item_result[$k]['mid']]);
				$coupon_result                       = $coupon_model->findCoupon(1, ['id' => $meeting_result[$k]['coupon_id']]);
				$employee_result                     = $employee_model->findEmployee(1, ['id' => $coupon_item_result[$k]['creator']]);
				$coupon_item_result[$k]['mid']       = $meeting_result['name'];
				$coupon_item_result[$k]['coupon_id'] = $coupon_result['name'];
				$coupon_item_result[$k]['creator']   = $employee_result['name'];
			}
			$this->assign('coupon_item', $coupon_item_result);
			$this->assign('page_show',$page_show);
			$this->display();
		}

		public function message(){
			/** @var \Core\Model\MessageModel $message_model */
			$message_model = D('Core/Message');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$message_result = $message_model->findMessage(2, ['status' => 2]);
			foreach($message_result as $k => $v){
				$employee_result               = $employee_model->findEmployee(1, ['id' => $message_result[$k]['creator']]);
				$message_result[$k]['creator'] = $employee_result['name'];
			}
			$this->assign('message', $message_result);
			$this->display();
		}
	}