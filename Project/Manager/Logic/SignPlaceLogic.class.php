<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 11:41
	 */
	namespace Manager\Logic;

	use Core\Logic\ReceivablesLogic;

	class SignPlaceLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'delete':
					if($this->permissionList['SIGN_PLACE.DELETE']){
						/** @var \Core\Model\SignPlaceModel $model */
						$model  = D('Core/SignPlace');
						$data   = I('post.id');
						$result = $model->deleteRecord($data);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有删除签到点的权限', '__ajax__' => false];
				break;
				case 'create':
					if($this->permissionList['SIGN_PLACE.CREATE']){
						/** @var \Core\Model\SignPlaceModel $model */
						$model            = D('Core/SignPlace');
						$data             = I('post.');
						$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$data['mid']      = I('get.mid', 0, 'int');
						$data['creatime'] = time();

						return array_merge($model->createRecord($data), ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建签到点的权限', '__ajax__' => false];
				break;
				case 'alter':
					if($this->permissionList['SIGN_PLACE.ALTER']){
						/** @var \Core\Model\SignPlaceModel $model */
						$model = D('Core/SignPlace');
						$data  = I('post.');
						$id    = I('get.id', 0, 'int');

						return array_merge($model->alterRecord(['id' => $id], $data), ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有修改签到点的权限', '__ajax__' => false];
				break;
				case 'sign':
					if($this->permissionList['SIGN_PLACE-CLIENT.SIGN']){
						/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
						$join_sign_place_model = D('Core/JoinSignPlace');
						$cid                   = I('post.cid', 0, 'int');
						$mid                   = I('get.mid', 0, 'int');
						$sid                   = I('get.sid', 0, 'int');
						$join_record           = $join_sign_place_model->findRecord(1, [
							'cid'    => $cid,
							'mid'    => $mid,
							'sid'    => $sid,
							'status' => 'not deleted'
						]);
						if($join_record['review_status'] == 1){
							C('TOKEN_ON', false);
							$result = $join_sign_place_model->alterRecord(['id' => $join_record['id']], [
								'sign_status' => 1,
								'sign_time'   => time(),
								'sign_type'   => 1
							]);

							return array_merge($result, ['__ajax__' => true]);
						}
						else return [
							'status'   => false,
							'message'  => '此客户信息还没有被审核',
							'__ajax__' => true
						];
					}
					else return ['status' => false, 'message' => '您没有为签到点的参会人员签到的权限', '__ajax__' => true];
				break;
				case 'review':
					if($this->permissionList['SIGN_PLACE-CLIENT.REVIEW']){
						/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
						$join_sign_place_model = D('Core/JoinSignPlace');
						$cid                   = I('post.cid', 0, 'int');
						$mid                   = I('get.mid', 0, 'int');
						$sid                   = I('get.sid', 0, 'int');
						$join_record           = $join_sign_place_model->findRecord(1, [
							'mid'    => $mid,
							'sid'    => $sid,
							'cid'    => $cid,
							'status' => 'not deleted'
						]);
						C('TOKEN_ON', false);
						$result = $join_sign_place_model->alterRecord(['id' => $join_record['id']], [
							'review_status' => 1,
							'review_time'   => time()
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有为签到点的参会人员审核的权限', '__ajax__' => true];
				break;
				case 'anti_sign':
					if($this->permissionList['SIGN_PLACE-CLIENT.ANTI-SIGN']){
						/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
						$join_sign_place_model = D('Core/JoinSignPlace');
						$cid                   = I('post.cid', 0, 'int');
						$mid                   = I('get.mid', 0, 'int');
						$sid                   = I('get.sid', 0, 'int');
						$join_record           = $join_sign_place_model->findRecord(1, [
							'cid'    => $cid,
							'sid'    => $sid,
							'mid'    => $mid,
							'status' => 'not deleted'
						]);
						C('TOKEN_ON', false);
						$result = $join_sign_place_model->alterRecord(['id' => $join_record['id']], [
							'sign_status' => 2,
							'sign_time'   => null,
							'sign_type'   => 0
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有为签到点的参会人员取消签到的权限', '__ajax__' => true];
				break;
				case 'anti_review':
					if($this->permissionList['SIGN_PLACE-CLIENT.ANTI-REVIEW']){
						/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
						$join_sign_place_model = D('Core/JoinSignPlace');
						$cid                   = I('post.cid', 0, 'int');
						$mid                   = I('post.mid', 0, 'int');
						$sid                   = I('get.sid', 0, 'int');
						$join_record           = $join_sign_place_model->findRecord(1, [
							'mid'    => $mid,
							'cid'    => $cid,
							'sid'    => $sid,
							'status' => 'not deleted'
						]);
						C('TOKEN_ON', false);
						$result = $join_sign_place_model->alterRecord(['id' => $join_record['id']], [
							'review_status' => 2,
							'sign_status'   => 0,
							'sign_time'     => null
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有为签到点的参会人员取消审核的权限', '__ajax__' => true];
				break;
				case 'multi_review':
					if($this->permissionList['SIGN_PLACE-CLIENT.REVIEW']){
						/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
						$join_sign_place_model = D('Core/JoinSignPlace');
						$mid                   = I('get.mid');
						$sid                   = I('get.sid');
						$client_id_arr         = explode(',', I('post.cid', ''));
						$join_id               = [];
						foreach($client_id_arr as $v){
							$join_record = $join_sign_place_model->findRecord(1, [
								'cid'    => $v,
								'mid'    => $mid,
								'sid'    => $sid,
								'status' => 'not deleted'
							]);
							$join_id[]   = $join_record['id'];
						}
						C('TOKEN_ON', false);
						$result = $join_sign_place_model->alterRecord(['id' => ['in', $join_id]], [
							'review_status' => 1,
							'review_time'   => time()
						]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有为签到点的参会人员审核的权限', '__ajax__' => false];
				break;
				case 'multi_sign':
					if($this->permissionList['SIGN_PLACE-CLIENT.SIGN']){
						/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
						$join_sign_place_model = D('Core/JoinSignPlace');
						$mid                   = I('get.mid');
						$sid                   = I('get.sid');
						$client_id_arr         = explode(',', I('post.cid', ''));
						$join_id               = [];
						foreach($client_id_arr as $v){
							$join_record = $join_sign_place_model->findRecord(1, [
								'cid'    => $v,
								'mid'    => $mid,
								'sid'    => $sid,
								'status' => 'not deleted'
							]);
							$join_id[]   = $join_record['id'];
						}
						C('TOKEN_ON', false);
						$result = $join_sign_place_model->alterRecord(['id' => ['in', $join_id]], [
							'sign_status' => 1,
							'sign_time'   => time(),
							'sign_type'   => 1
						]);
						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有为签到点的参会人员签到的权限', '__ajax__' => false];
				break;
				case 'multi_anti_review':
					if($this->permissionList['SIGN_PLACE-CLIENT.ANTI-REVIEW']){
						/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
						$join_sign_place_model = D('Core/JoinSignPlace');
						$mid                   = I('get.mid');
						$sid                   = I('get.sid');
						$client_id_arr         = explode(',', I('post.cid', ''));
						$join_id               = [];
						foreach($client_id_arr as $v){
							$join_record = $join_sign_place_model->findRecord(1, [
								'cid'    => $v,
								'mid'    => $mid,
								'sid'    => $sid,
								'status' => 'not deleted'
							]);
							$join_id[]   = $join_record['id'];
						}
						C('TOKEN_ON', false);
						$result = $join_sign_place_model->alterRecord(['id' => ['in', $join_id]], [
							'review_status' => 2,
							'sign_status'   => 0,
							'sign_time'     => null
						]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有为签到点的参会人员取消审核的权限', '__ajax__' => false];
				break;
				case 'multi_anti_sign':
					if($this->permissionList['SIGN_PLACE-CLIENT.ANTI-SIGN']){
						/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
						$join_sign_place_model = D('Core/JoinSignPlace');
						$mid                   = I('get.mid');
						$sid                   = I('get.sid');
						$client_id_arr         = explode(',', I('post.cid', ''));
						$join_id               = [];
						foreach($client_id_arr as $v){
							$join_record = $join_sign_place_model->findRecord(1, [
								'cid'    => $v,
								'mid'    => $mid,
								'sid'    => $sid,
								'status' => 'not deleted'
							]);
							$join_id[]   = $join_record['id'];
						}
						C('TOKEN_ON', false);
						$result = $join_sign_place_model->alterRecord(['id' => ['in', $join_id]], [
							'sign_status' => 2,
							'sign_time'   => null,
							'sign_type'   => 0
						]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有为签到点的参会人员取消签到的权限', '__ajax__' => false];
				break;
				case 'create_receivables':
					if($this->permissionList['SIGN_PLACE-CLIENT.EARN-PAYMENT']){
						/** @var \Core\Model\CouponItemModel $coupon_item_model */
						$coupon_item_model = D('Core/CouponItem');
						$data = I('post.', '');
						$mid  = I('get.mid', 0, 'int');
						$cid  = I('post.cid', 0, 'int');
						$coupon_item_code = explode(',', $data['coupon_ids']);
						C('TOKEN_ON', false);
						foreach($coupon_item_code as $k => $v){
							$coupon_item_record = $coupon_item_model->findRecord(1,['id' =>$v, 'status' =>'not deleted']);
							if($coupon_item_record['status']==0){
								$coupon_item_result = $coupon_item_model->alterRecord(['id' =>$v], [
									'status' => 1,
									'cid'    => $cid
								]);
							}else{
								return [
									'status'=>false,
									'message'=>'您选择的代金券已使用',
									'__ajax__'   => false,
								];
							}
						}
						/** @var \Core\Model\ReceivablesModel $receivables_model */
						$receivables_model = D('Core/Receivables');
						/** @var \Core\Model\ClientModel $client_model */
						$client_model = D('Core/Client');
						/** @var \Core\Model\EmployeeModel $employee_model */
						$employee_model       = D('Core/Employee');
						$logic                = new ReceivablesLogic();
						$data['mid']          = $mid;
						$data['cid']          = $cid;
						$data['coupon_ids']   = I('post.coupon_code', '');
						$data['time']         = strtotime(I('post.receivables_time', ''));
						$data['creatime']     = time();    //创建时间
						$data['creator']      = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$data['order_number'] = $logic->makeOrderNumber();
						$receivables_result = $receivables_model->createRecord($data);
						//查出开拓顾问
						$client_result   = $client_model->findClient(1, [
							'id'     => $cid,
							'status' => 'not deleted'
						]);
						$employee_result = $employee_model->findEmployee(1, [
							'keyword' => $client_result['develop_consultant'],
							'status'  => 'not deleted'
						]);
						if($employee_result){
							$message_logic = new MessageLogic();
							$sms_send      = $message_logic->send($mid, 0, 3, [$employee_result['id']]);
						}

						return array_merge($receivables_result, [
							'__ajax__'   => false,
							'__return__' => U('Receivables/Manage', ['mid' => $mid])
						]);
					}
					else return ['status' => false, 'message' => '您没有为签到点的参会人员收款的权限', '__ajax__' => false];
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setData($type, $data){
			switch($type){
				case 'alter:get_extend_column':
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model        = D('Core/Employee');
					$meeting               = $meeting_model->findMeeting(1, ['id' => $data['mid']]);
					$director              = $employee_model->findEmployee(1, ['id' => $data['director_id']]);
					$sign_director         = $employee_model->findEmployee(1, ['id' => $data['sign_director_id']]);
					$data['meeting']       = $meeting['name'];
					$data['director']      = $director['name'];
					$data['sign_director'] = $sign_director['name'];

					return $data;
				break;
				case 'manage:get_extend_column':
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					foreach($data as $key => $val){
						$meeting                     = $meeting_model->findMeeting(1, [
							'id'     => $val['mid'],
							'status' => 'not deleted'
						]);
						$director                    = $employee_model->findEmployee(1, [
							'id'     => $val['director_id'],
							'status' => 'not deleted'
						]);
						$sign_director               = $employee_model->findEmployee(1, [
							'id'     => $val['sign_director_id'],
							'status' => 'not deleted'
						]);
						$data[$key]['meeting']       = $meeting['name'];
						$data[$key]['director']      = $director['name'];
						$data[$key]['sign_director'] = $sign_director['name'];
					}

					return $data;
				default:
					return $data;
				break;
			}
		}
	}