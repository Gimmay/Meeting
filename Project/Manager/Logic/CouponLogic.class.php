<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-8
	 * Time: 17:20
	 */
	namespace Manager\Logic;

	use Core\Logic\LogLogic;

	class CouponLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'batch_create':
					if($this->permissionList['COUPON.CREATE']){
						$data               = I('post.');//表单数据
						$creator            = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$data['start_time'] = strtotime(I('post.start_time', ''));
						$data['end_time']   = strtotime(I('post.end_time', ''));
						$data['mid']        = I('get.mid', 0, 'int');
						$data['creator']    = $creator;//当前创建者
						$data['creatime']   = time();//当前时间
						/** @var \Core\Model\CouponItemModel $coupon_item_model */
						$coupon_item_model = D('Core/CouponItem');//实例化表
						/** @var \Core\Model\CouponModel $coupon_model */
						$coupon_model = D('Core/Coupon');//实例化表
						$result1      = $coupon_model->createCoupon($data); //插入到数据库
						$code         = I('post.coupon_area'); //代金券码
						$request      = explode(',', $code);    //打散代金券码
						$mid          = I('get.mid', 0, 'int');
						C('TOKEN_ON', false);            //令牌
						$data = [];
						foreach($request as $v){
							$data[] = [
								'coupon_id' => $result1['id'], // 代金券ID
								'mid'       => $mid, // 会议ID
								'code'      => $v, // 代金券码
								'creator'   => $creator, // 创建者
								'creatime'  => time(), // 创建时间
							];
						}
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_coupon_item&workflow_coupon',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '创建代金券',
							'type'     => 'create'
						]);
						$result2 = $coupon_item_model->createMultiRecord($data); //插入到数据库
						return array_merge($result2, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建券的权限', '__ajax__' => false];
				break;
				case 'delete';
					if($this->permissionList['COUPON.DELETE']){
						/** @var \Core\Model\CouponModel $model */
						$id        = I('post.id', '');
						$model     = D('Core/Coupon');
						$result    = $model->deleteCoupon($id);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_coupon',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '删除代金券',
							'type'     => 'delete'
						]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有删除券的权限', '__ajax__' => false];
				break;
				case 'alter';
					if($this->permissionList['COUPON.ALTER']){
						/** @var \Core\Model\CouponModel $model */
						$model              = D('Core/Coupon');
						$data               = I('post.', '');
						$data['start_time'] = strtotime(I('post.start_time', ''));
						$data['end_time']   = strtotime(I('post.end_time', ''));
						$result             = $model->alterCoupon(['id' => I('post.id', 0, 'int')], $data);
						$log_logic          = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_coupon',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '修改代金券',
							'type'     => 'modify'
						]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有修改券的权限', '__ajax__' => false];
				break;
				case 'create';
					if($this->permissionList['COUPON.CREATE']){
						C('TOKEN_ON', false);            //令牌
						/** @var \Core\Model\CouponModel $model */
						$model            = D('Core/Coupon');
						$data             = I('post.', '');
						$data['mid']      = I('get.mid', 0, 'int');
						$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$data['creatime'] = time();//当前时间
						$result           = $model->createCoupon($data);
						if(I('post.coupon_c')){
							/** @var \Core\Model\CouponItemModel $coupon_item_model */
							$coupon_item_model  = D('Core/CouponItem');
							$info['coupon_id']  = $result['id'];
							$info['mid']        = I('get.mid', '');
							$info['code']       = I('post.coupon_c', '');
							$info['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
							$info['creatime']   = time();//当前时间
							$result_coupon_item = $coupon_item_model->createRecord($info);
						}
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_coupon&workflow_coupon_item',
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
					return (['status' => false, 'message' => '参数错误']);
				break;
			}
		}

		public function setExtendColumnForManage($list){
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			/** @var \Core\Model\MeetingModel $meeting_model */
			$coupon_item_model = D('Core/CouponItem');
			$meeting_model     = D('Core/Meeting');
			foreach($list as $k1 => $v1){
				$coupon_list        = $coupon_item_model->findRecord(2, [
					'coupon_id' => $v1['id'],
					'status'    => 0
				]);
				$list[$k1]['count'] = count($coupon_list);
				$meeting_arr        = [
					'id'   => [],
					'name' => []
				];
				foreach($coupon_list as $k2 => $v2){
					if(in_array($v2['mid'], $meeting_arr['id'])) continue;
					else{
						array_push($meeting_arr['id'], $v2['mid']);
						$meeting_record = $meeting_model->findMeeting(1, ['id' => $v2['mid']]);
						$url            = U('couponlist', [
							'mid' => $meeting_record['id'],
							'id'  => $coupon_list[$k2]['coupon_id']
						]);
						array_push($meeting_arr['name'], "<a href='$url'>$meeting_record[name]</a>");
					}
				}
				if(isset($list[$k1]['meeting'])) $list[$k1]['meeting'] = implode(',', $meeting_arr['name']);
				else{
					$list[$k1]['meeting'] = '';
					$list[$k1]['meeting'] = implode(',', $meeting_arr['name']);
				}
			}

			return $list;
		}
	}