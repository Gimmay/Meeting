<?php
	namespace Manager\Controller;

	use Manager\Logic\CouponItemLogic;
	use Manager\Logic\CouponLogic;
	use Think\Controller;
	use Think\Page;

	class CouponController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function manage(){
			$coupon_logic = new CouponLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $coupon_logic->handlerRequest($type);
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
			/** @var \Core\Model\CouponModel $coupon_model */
			$coupon_model = D('Core/Coupon');
			$coupon_find  = $coupon_model->findCoupon(2, ['status' => 1]);
			foreach($coupon_find as $k => $v){
				$data['id'] = $coupon_find[$k]['id'];
			}
			/* 获取当前条件下员工记录数 */
			$list_total = $coupon_model->findCoupon(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 'not deleted',
			]);
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			$page_show   = $page_object->show();
			/* 当前页的员工记录列表 */
			$coupon_list = $coupon_model->findCoupon(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
				'status'  => 'not deleted',
			]);
			$coupon_list = $coupon_logic->setExtendColumnForManage($coupon_list);
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			$meeting_list  = $meeting_model->getMeetingForSelect();
			$this->assign('meeting_list', $meeting_list);
			$this->assign('coupon_list', $coupon_list);
			$this->assign('page_show', $page_show);
			$this->display();
		}

		public function details(){
			$coupon_item_logic = new CouponItemLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $coupon_item_logic->handlerRequest($type);
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
			/** @var \Core\Model\CouponItemModel $model */
			$model = D('Core/CouponItem');
			$id    = I('get.id', 0, 'int');
			if(isset($_GET['status'])){
				$result = $model->findCouponItem(0, ['coupon_id' => $id, 'status' => I('get.status', 0, 'int')]);
			}
			else{
				$result = $model->findCouponItem(0, ['coupon_id' => $id, 'status' => 'not deleted']);
			}
			$page_object = new Page($result, 10);
			$page_show   = $page_object->show();
			if(isset($_GET['status'])){
				$info = $model->findCouponItem(2, [
					'keyword'   => I('get.keyword', ''),
					'_limit'    => $page_object->firstRow.','.$page_object->listRows,
					'_order'    => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
					'status'    => I('get.status', 0, 'int'),
					'coupon_id' => I('get.id', 0, 'int'),
				]);
			}
			else{
				$info = $model->findCouponItem(2, [
					'keyword'   => I('get.keyword', ''),
					'_limit'    => $page_object->firstRow.','.$page_object->listRows,
					'_order'    => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
					'status'    => 'not deleted',
					'coupon_id' => I('get.id', 0, 'int'),
				]);
			}
			$count_status0 = $model->findCouponItem(0, ['status' => 0, 'coupon_id' => I('get.id', 0, 'int')]);
			$count_status1 = $model->findCouponItem(0, ['status' => 1, 'coupon_id' => I('get.id', 0, 'int')]);
			$count_status2 = $model->findCouponItem(0, ['status' => 2, 'coupon_id' => I('get.id', 0, 'int')]);
			$this->assign('count_status0', $count_status0);
			$this->assign('count_status1', $count_status1);
			$this->assign('count_status2', $count_status2);
			$info = $coupon_item_logic->setExtendColumnForManage($info);
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			$meeting_list  = $meeting_model->getMeetingForSelect();
			/** @var \Core\Model\CouponModel $coupon_model */
			$coupon_model  = D('Core/Coupon');
			$coupon_result = $coupon_model->findCoupon(1, ['id' => I('get.id', 0, 'int')]);
			$this->assign('coupon', $coupon_result);
			$this->assign('meeting_list', $meeting_list);
			$this->assign('info', $info);
			$this->assign('page_show', $page_show);
			$this->display();
		}

		public function couponList(){
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			$coupon_item_model = D('Core/CouponItem');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\CouponModel $coupon_model */
			$coupon_model = D('Core/Coupon');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model      = D('Core/Client');
			$coupon_item_count = $coupon_item_model->findCouponItem(0, [
				'mid'       => $this->meetingID,
				'coupon_id' => I('get.id', 0, 'int'),
				'keyword'   => I('get.keyword', '')
			]);
			/* 分页设置 */
			$page_object        = new Page($coupon_item_count, C('PAGE_RECORD_COUNT'));
			$page_show          = $page_object->show();
			$coupon_item_result = $coupon_item_model->findCouponItem(2, [
				'keyword'   => I('get.keyword', ''),
				'_limit'    => $page_object->firstRow.','.$page_object->listRows,
				'_order'    => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'mid'       => $this->meetingID,
				'coupon_id' => I('get.id', 0, 'int'),
				'status'    => 'not deleted'
			]);
			foreach($coupon_item_result as $k => $v){
				$coupon_result  = $coupon_model->findCoupon(1, ['id' => $coupon_item_result[$k]['coupon_id']]);
				$meeting_result = $meeting_model->findMeeting(1, ['id' => $coupon_item_result[$k]['mid']]);
				if($coupon_item_result[$k]['cid']){
					$client_result                         = $client_model->findClient(1, ['id' => $coupon_item_result[$k]['cid']]);
					$coupon_item_result[$k]['client_name'] = $client_result['name'];
				}
				$coupon_item_result[$k]['coupon_name']  = $coupon_result['name'];
				$coupon_item_result[$k]['meeting_name'] = $meeting_result['name'];
			}
			$this->assign('coupon', $coupon_item_result);
			$this->assign('page_show', $page_show);
			$this->display();
		}

	}