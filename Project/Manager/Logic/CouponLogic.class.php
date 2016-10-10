<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-8
	 * Time: 17:20
	 */
	namespace Manager\Logic;

	class CouponLogic extends ManagerLogic{

		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'create':
					if(IS_POST){
						$data             = I('post.');//表单数据
						$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');//当前创建者
						$data['creatime'] = time();//当前时间
						/** @var \Core\Model\CouponItemModel $coupon_item_model */
						$coupon_item_model = D('Core/CouponItem');//实例化表
						/** @var \Core\Model\CouponModel $coupon_model */
						$coupon_model = D('Core/Coupon');//实例化表
						$result       = $coupon_model->createCoupon($data); //插入到数据库
						$code   = I('post.coupon_area'); //代金券码
						$request = explode(',',$code);    //打散代金券码
						foreach($request as $v){
							C('TOKEN_ON', false);            //令牌
							$date['coupon_id'] = $result['id'];  //代金券ID
							$date['mid']       = I('post.meeting_name');   //会议ID
							$date['code']      = $v;                        //代金券码
							$date['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int'); //创建者
							$date['creatime'] = time();//创建时间
							$coupon_item_model->createCoupon_item($date); //插入到数据库
						}
						return $result;
					}
					exit;
				break;
				default:
					echo json_encode(['status' => false, 'message' => '参数错误']);

					return -1;
				break;
			}
		}

	}