<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-8
	 * Time: 17:20
	 */
	namespace Manager\Logic;

	class CouponItemLogic extends ManagerLogic{

		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'create':
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');//实例化表
					/** @var \Core\Model\CouponModel $coupon_model */
					$code   = I('post.coupon_area'); //代金券码
					$request = explode(',',$code);    //打散代金券码

					foreach($request as $v){
						C('TOKEN_ON', false);            //令牌
						$date['coupon_id'] = I('get.id','');  //代金券ID
						$date['mid']       = I('post.meeting_name');   //会议ID
						$date['code']      = $v;                        //代金券码
						$date['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int'); //创建者
						$date['creatime'] = time();//创建时间
						$coupon_item_model->createCouponItem($date); //插入到数据库
					}
					return['status' => true, 'message' => '创建代金券成功'];
				break;
				case'delete';
					$id = I('post.id','');
					/** @var \Core\Model\CouponItemModel $model */
					$model = D('Core/CouponItem');
					$result = $model->deleteCouponItem($id);
					return $result;
				break;

				case'alter';
					$id['id'] = I('post.id','');
					C('TOKEN_ON', false);            //令牌
					$mid['mid'] = I('post.meeting_name_a','');
					/** @var \Core\Model\CouponItemModel $model */
					$model = D('Core/CouponItem');

					$result = $model->alterCouponItem($id,$mid);
					return $result;
				break;

				default:
					echo json_encode(['status' => false, 'message' => '参数错误']);

					return -1;
				break;
			}
		}
		
		public function setExtendColumnForManage($list){
			/** @var \Core\Model\MeetingModel $meeting_model */
			/** @var \Core\Model\CouponModel $coupon_model */
			$meeting_model = D('Core/Meeting');
			$coupon_model = D('Core/Coupon');
			foreach($list as $key => $val){
				$meeting_record = $meeting_model->findMeeting(1, ['id'=>$val['mid']]);
				$coupon_record = $coupon_model->findCoupon(1, ['id'=>$val['coupon_id']]);
				$list[$key]['meetingName'] = $meeting_record['name'];
				$list[$key]['couponName'] = $coupon_record['name'];
			}
			return $list;
		}

	}