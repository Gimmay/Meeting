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
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			/** @var \Core\Model\RecycleModel $model */
			$model      = D('Core/Recycle');
			$list_total = $join_model->findRecord(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 2,
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的客户记录列表 */
			$client_list = $join_model->findRecord(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
				'status'  => 2,
			]);
			if(IS_POST){
				$client_id = I('post.id');
				$recovery  = $model->recoveryClient($client_id);
				if($recovery['status']) $this->success($recovery['message'], U('client', ['mid' => I('get.mid', 0, 'int')]));
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
				if($recovery['status']) $this->success($recovery['message'], U('employee', ['mid' => I('get.mid', 0, 'int')]));
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
				if($recovery['status']) $this->success($recovery['message'], U('meeting', ['mid' => I('get.mid', 0, 'int')]));
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
				if($recovery['status']) $this->success($recovery['message'], U('Role', ['mid' => I('get.mid', 0, 'int')]));
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
			$list_total     = $coupon_model->findCoupon(0, [
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
				if($recovery['status']) $this->success($recovery['message'], U('Coupon', ['mid' => I('get.mid', 0, 'int')]));
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
			$coupon_item_model = D('Core/Recycle');
			$list_total        = $coupon_item_model->findCouponItem(0, [
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
			$this->assign('page_show', $page_show);
			$this->display();
		}

		public function message(){
			/** @var \Core\Model\MessageModel $message_model */
			$message_model = D('Core/Message');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$message_result = $message_model->findMessage(2, ['status' => 2, 'keyword' => I('get.keyword', '')]);
			foreach($message_result as $k => $v){
				$employee_result               = $employee_model->findEmployee(1, ['id' => $message_result[$k]['creator']]);
				$message_result[$k]['creator'] = $employee_result['name'];
			}
			$this->assign('message', $message_result);
			$this->display();
		}

		public function group(){
			C('TOKEN_ON', false);
			/** @var \Core\Model\GroupModel $group_model */
			$group_model = D('Core/Group');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$group_result   = $group_model->findRecord(2, [
				'mid'     => I('get.mid', 0, 'int'),
				'status'  => 2,
				'keyword' => I('get.keyword', '')
			]);
			foreach($group_result as $k => $v){
				$employee_result                   = $employee_model->findEmployee(1, ['id' => $v['creator']]);
				$group_result[$k]['employee_name'] = $employee_result['name'];
			}
			$id = I('post.id', 0, 'int');
			if(IS_POST){
				$group_recovery = $group_model->alterRecord(['id' => $id], ['status' => 1]);
				if($group_recovery['status']) $this->success($group_recovery['message'], U('Coupon', ['mid' => I('get.mid', 0, 'int')]));
				else $this->error($group_recovery['message']);
				exit;
			}
			$this->assign('group', $group_result);
			$this->display();
		}

		public function groupMember(){
			/** @var \Core\Model\GroupMemberModel $group_member_model */
			$group_member_model = D('Core/GroupMember');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model        = D('Core/Client');
			$group_member_result = $group_member_model->findRecord(2, [
				'status'  => 2,
				'mid'     => I('get.mid', 0, 'int'),
				'keyword' => I('get.keyword', '')
			]);
			foreach($group_member_result as $k => $v){
				$employee_result                          = $employee_model->findEmployee(1, ['id' => $v['creator']]);
				$group_member_result[$k]['employee_name'] = $employee_result['name'];
				$client_result                            = $client_model->findClient(1, ['id' => $v['cid']]);
				$group_member_result[$k]['client_name']   = $client_result['name'];
			}
			$id = I('post.id', 0, 'int');
			if(IS_POST){
				$group_member_recovery = $group_member_model->alterRecord(['id' => $id], ['status' => 1]);
				if($group_member_recovery['status']) $this->success($group_member_recovery['message'], U('Coupon', ['mid' => I('get.mid', 0, 'int')]));
				else $this->error($group_member_recovery['message']);
				exit;
			}
			$this->assign('group_member', $group_member_result);
			$this->display();
		}

		public function hotel(){
			/** @var \Core\Model\HotelModel $hotel_model */
			$hotel_model = D('Core/Hotel');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$hotel_result   = $hotel_model->findHotel(2, ['status' => 2]);
			foreach($hotel_result as $k => $v){
				$employee_result                   = $employee_model->findEmployee(1, [
					'id'      => $v['creator'],
					'keyword' => I('get.keyword', '')
				]);
				$hotel_result[$k]['employee_name'] = $employee_result['name'];
			}
			$id = I('post.id', 0, 'int');
			if(IS_POST){
				$hotel_recovery = $hotel_model->alterHotel(['id' => $id], ['status' => 1]);
				if($hotel_recovery['status']) $this->success($hotel_recovery['message'], U('Coupon', ['mid' => I('get.mid', 0, 'int')]));
				else $this->error($hotel_recovery['message']);
				exit;
			}
			$this->assign('hotel', $hotel_result);
			$this->display();
		}

		public function room(){
			/** @var \Core\Model\HotelModel $hotel_model */
			$hotel_model = D('Core/Hotel');
			/** @var \Core\Model\RoomModel $room_model */
			$room_model = D('Core/Room');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$room_result    = $room_model->findRoom(2, ['status' => 3]);
			$id             = I('post.id', 0, 'int');
			if(IS_POST){
				$room_recovery = $room_model->alterRoom(['id' => $id], ['status' => 1]);
				if($room_recovery['status']) $this->success($room_recovery['message'], U('Coupon', ['mid' => I('get.mid', 0, 'int')]));
				else $this->error($room_recovery['message']);
				exit;
			}
			foreach($room_result as $k => $v){
				$employee_result                  = $employee_model->findEmployee(1, ['id' => $v['creator']]);
				$room_result[$k]['employee_name'] = $employee_result['name'];
			}
			$this->assign('room', $room_result);
			$this->display();
		}
	}