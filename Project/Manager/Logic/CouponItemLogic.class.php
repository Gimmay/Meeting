<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-8
	 * Time: 17:20
	 */
	namespace Manager\Logic;

	use Core\Logic\LogLogic;

	class CouponItemLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'batch_create':
					if($this->permissionList['COUPON.CREATE']){
						/** @var \Core\Model\CouponItemModel $coupon_item_model */
						$coupon_item_model = D('Core/CouponItem');//实例化表
						/** @var \Core\Model\CouponModel $coupon_model */
						$code    = I('post.hide_coupon_area'); //代金券码
						$request = explode(',', $code);    //打散代金券码
						foreach($request as $v){
							C('TOKEN_ON', false);            //令牌
							$date['coupon_id']  = I('get.id', 0, 'int');  //代金券ID
							$date['mid']        = I('get.mid', 0, 'int');   //会议ID
							$date['code']       = $v;                        //代金券码
							$date['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int'); //创建者
							$date['creatime']   = time();//创建时间
							$coupon_item_result = $coupon_item_model->createRecord($date); //插入到数据库
						}
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_coupon_item',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '创建代金券',
							'type'     => 'create'
						]);

						return array_merge($coupon_item_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建券的权限', '__ajax__' => false];
				break;
				case 'alter_coupon':
					if($this->permissionList['COUPON.ALTER']){
						$id = I('post.id', '');
						/** @var \Core\Model\MeetingModel $meeting_model */
						$meeting_model = D('Core/Meeting');
						/** @var \Core\Model\CouponItemModel $coupon_item_model */
						$coupon_item_model  = D('Core/CouponItem');
						$coupon_item_result = $coupon_item_model->findRecord(1, ['id' => $id]);
						$meeting_result     = $meeting_model->findMeeting(1, ['id' => $coupon_item_result['mid']]);
						$log_logic          = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_coupon_item',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '修改代金券',
							'type'     => 'modify'
						]);

						return array_merge($meeting_result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有修改券的权限', '__ajax__' => true];
				break;
				case 'delete';
					if($this->permissionList['COUPON.ALTER']){
						$id = I('post.id', '');
						/** @var \Core\Model\CouponItemModel $model */
						$model     = D('Core/CouponItem');
						$result    = $model->deleteRecord($id);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_coupon_item',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '删除代金券',
							'type'     => 'delete'
						]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有删除券的权限', '__ajax__' => false];
				break;
				case 'multi_alter';
					if($this->permissionList['COUPON.ALTER']){
						/** @var \Core\Model\CouponItemModel $coupon_item_model */
						$coupon_item_model = D('Core/CouponItem');
						C('TOKEN_ON', false);
						$mid = I('post.meeting_name_a', '');
						$ids = I('post.id', '');
						$id  = explode(',', $ids);
						foreach($id as $v) $coupon_item_result = $coupon_item_model->alterRecord(['id' => $v], ['mid' => $mid]);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_coupon_item',
							'dbColumn' => 'mid',
							'extend'   => 'PC',
							'action'   => '分配代金券',
							'type'     => 'modify'
						]);

						return array_merge($coupon_item_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有修改券的权限', '__ajax__' => false];
				break;
				case 'alter';
					if($this->permissionList['COUPON.ALTER']){
						$id['id'] = I('post.id', '');
						C('TOKEN_ON', false);            //令牌
						$mid['mid'] = I('post.meeting_name_a', '');
						/** @var \Core\Model\CouponItemModel $model */
						$model     = D('Core/CouponItem');
						$result    = $model->alterRecord(['id' => $id], $mid);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_coupon_item',
							'dbColumn' => 'mid',
							'extend'   => 'PC',
							'action'   => '分配代金券',
							'type'     => 'modify'
						]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有修改券的权限', '__ajax__' => true];
				break;
				case 'create';
					if($this->permissionList['COUPON.CREATE']){
						/** @var \Core\Model\CouponItemModel $model */
						$model = D('Core/CouponItem');
						C('TOKEN_ON', false);            //令牌
						$data['coupon_id'] = I('get.id', '');
						$data['mid']       = I('get.mid', 0, 'int');
						$data['code']      = I('post.hide_coupon_area', '');
						$data['creator']   = I('session.MANAGER_EMPLOYEE_ID', 0, 'int'); //创建者
						$data['creatime']  = time();//创建时间
						$result            = $model->createRecord($data);
						$log_logic         = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_coupon_item',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '创建代金券',
							'type'     => 'create'
						]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建券的权限', '__ajax__' => false];
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
			$client_model  = D('Core/Client');
			$meeting_model = D('Core/Meeting');
			$coupon_model  = D('Core/Coupon');
			foreach($list as $key => $val){
				$meeting_record = $meeting_model->findMeeting(1, ['id' => $val['mid']]);
				$coupon_record  = $coupon_model->findCoupon(1, ['id' => $val['coupon_id']]);
				if($val['cid']){
					$client_result     = $client_model->findClient(1, ['id' => $val['cid']]);
					$list[$key]['cid'] = $client_result['name'];
				}
				$list[$key]['meetingName'] = $meeting_record['name'];
				$list[$key]['couponName']  = $coupon_record['name'];
			}

			return $list;
		}
	}