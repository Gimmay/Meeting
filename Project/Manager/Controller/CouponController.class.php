<?php
	namespace Manager\Controller;

	use Core\Logic\WxCorpLogic;
	use Manager\Logic\CouponLogic;
	use Manager\Logic\EmployeeLogic;
	use Think\Controller;

	class CouponController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function Manage(){
			/** @var \Core\Model\CouponModel  $coupon_model */
			$coupon_model = D('Core/Coupon');
			$coupon_find = $coupon_model->findCoupon(2);
			$coupon_logic   = new CouponLogic();
			if(IS_POST){

				$type   = strtolower(I('post.requestType', ''));
				$result = $coupon_logic->handlerRequest($type);
				if($result === -1){
				}
				else{
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}

			/** @var \Manager\Model\MeetingModel $employee_model */
			$employee_model = D('Meeting');
			$employee_list  = $employee_model->getMeetingForSelect();
			$this->assign('meeting_list', $employee_list);
			$this->assign('coupon_list',$coupon_find);

			$this->display();
		}

		public function details (){
			/** @var \Core\Model\CouponItemModel $model */
			$model=D('Core/CouponItem');
			$id = I('get.id',0,'int');
			$result = $model->findCouponItem(2,['coupon_id'=>$id]);
			foreach ($result as $v){
				$data = $v;
			}
			$data['coupon_id']; //代金券id
			$data['mid'];	//会议id
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');//实例化表
			$result_meeting = $meeting_model->findMeeting(1,['id'=>$data['mid']]); //查出当前会议

			/** @var \Core\Model\CouponModel $coupon_model */
			$coupon_model = D('Core/Coupon');
			$result_coupon = $coupon_model->findCoupon(1,['id'=>$data['coupon_id']]);

			$this->assign('info',$result);
			$this->assign('meeting',$result_meeting);
			$this->assign('coupon',$result_coupon);
			$this->display();
		}

	}