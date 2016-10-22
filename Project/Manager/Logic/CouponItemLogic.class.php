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
				case 'batch_create':
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');//实例化表
					/** @var \Core\Model\CouponModel $coupon_model */
					$code    = I('post.hide_coupon_area'); //代金券码
					$request = explode(',', $code);    //打散代金券码
					foreach($request as $v){
						C('TOKEN_ON', false);            //令牌
						$date['coupon_id'] = I('get.id', '');  //代金券ID
						$date['mid']       = I('post.meeting_name');   //会议ID
						$date['code']      = $v;                        //代金券码
						$date['creator']   = I('session.MANAGER_EMPLOYEE_ID', 0, 'int'); //创建者
						$date['creatime']  = time();//创建时间
						$coupon_item_model->createCouponItem($date); //插入到数据库
					}

					return ['status' => true, 'message' => '创建代金券成功', '__ajax__' => false];
				break;
				case 'alter_coupon':
					$id = I('post.id', '');
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model  = D('Core/CouponItem');
					$coupon_item_result = $coupon_item_model->findCouponItem(1, ['id' => $id]);
					$meeting_result     = $meeting_model->findMeeting(1, ['id' => $coupon_item_result['mid']]);

					return array_merge($meeting_result, ['__ajax__' => true]);
				break;
				case'delete';
					$id = I('post.id', '');
					/** @var \Core\Model\CouponItemModel $model */
					$model  = D('Core/CouponItem');
					$result = $model->deleteCouponItem($id);
					return array_merge($result, ['__ajax__' => false]);
				break;
				case'alter';
					$id['id'] = I('post.id', '');
					C('TOKEN_ON', false);            //令牌
					$mid['mid'] = I('post.meeting_name_a', '');
					/** @var \Core\Model\CouponItemModel $model */
					$model  = D('Core/CouponItem');
					$result = $model->alterCouponItem($id, $mid);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case'create';
					/** @var \Core\Model\CouponItemModel $model */
					$model = D('Core/Coupon_item');
					C('TOKEN_ON', false);            //令牌
					$data['coupon_id'] = I('get.id', '');
					$data['mid']       = I('post.meeting_name', '');
					$data['code']      = I('post.hide_coupon_area', '');
					$data['creator']   = I('session.MANAGER_EMPLOYEE_ID', 0, 'int'); //创建者
					$data['creatime']  = time();//创建时间
					$result            = $model->createCouponItem($data);

					return array_merge($result, ['__ajax__' => false]);
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setExtendColumnForManage($list){
			/** @var \Core\Model\MeetingModel $meeting_model */
			/** @var \Core\Model\CouponModel $coupon_model */
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			$meeting_model = D('Core/Meeting');
			$coupon_model  = D('Core/Coupon');
			foreach($list as $key => $val){
				$meeting_record            = $meeting_model->findMeeting(1, ['id' => $val['mid']]);
				$coupon_record             = $coupon_model->findCoupon(1, ['id' => $val['coupon_id']]);
				if($val['cid']){
					$client_result = $client_model->findClient(1,['id'=>$val['cid']]);
					$list[$key]['cid'] = $client_result['name'];
				}
				$list[$key]['meetingName'] = $meeting_record['name'];
				$list[$key]['couponName']  = $coupon_record['name'];


			}
			return $list;
		}

	}