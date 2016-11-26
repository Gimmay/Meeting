<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:35
	 */
	namespace Mobile\Logic;

	use Core\Logic\PermissionLogic;
	use Manager\Logic\MessageLogic;

	class ReceivablesLogic extends MobileLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			$permission_logic = new PermissionLogic();
			$employee_id      = I('session.MOBILE_EMPLOYEE_ID', 0, 'int');
			switch($type){
				case 'create_receivables':
					if($permission_logic->hasPermission('WEIXIN.CLIENT.RECEIVABLES', $employee_id)){
						/** @var \Core\Model\CouponItemModel $coupon_item_model */
						$coupon_item_model = D('Core/CouponItem');
						$data              = I('post.', '');
						$mid               = I('get.mid', 0, 'int');
						$data['mid']       = $mid;
						$cid               = I('get.cid', 0, 'int');
						$coupon_item_code  = explode(',', $data['coupon_ids']);
						C('TOKEN_ON', false);
						foreach($coupon_item_code as $k => $v){
							$coupon_item_record = $coupon_item_model->findRecord(1, [
								'id'     => $v,
								'status' => 'not deleted'
							]);
							if($coupon_item_record['status'] == 0){
								$coupon_item_result = $coupon_item_model->alterRecord(['id' => $v], [
									'status' => 1,
									'cid'    => $cid
								]);
							}
							else{
								return [
									'status'   => false,
									'message'  => '您选择的代金券已使用',
									'__ajax__' => false,
								];
							}
						}
						$login = new \Core\Logic\ReceivablesLogic();
						/** @var \Core\Model\ReceivablesModel $receivables_model */
						$receivables_model = D('Core/Receivables');
						/** @var \Core\Model\ClientModel $client_model */
						$client_model = D('Core/Client');
						/** @var \Core\Model\EmployeeModel $employee_model */
						$employee_model       = D('Core/Employee');
						$data['cid']          = $cid;
						$data['coupon_ids']   = I('post.coupon_id', '');
						$data['payee_id']     = I('session.MOBILE_EMPLOYEE_ID', 0, 'int');
						$data['time']         = time();
						$data['creatime']     = time();    //创建时间
						$data['creator']      = I('session.MOBILE_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$data['order_number'] = $login->makeOrderNumber();
						$receivables_result   = $receivables_model->createRecord($data);
						//查出开拓顾问
						$client_result   = $client_model->findClient(1, ['id' => $cid]);
						$employee_result = $employee_model->findEmployee(1, ['keyword' => $client_result['develop_consultant']]);
						if($employee_result){
							$message_logic = new MessageLogic();
							$sms_send      = $message_logic->send($mid, C('AUTO_SEND_TYPE'), 0, 3, [$employee_result['id']]);
						}

						return array_merge($receivables_result, [
							'__ajax__'   => true,
							'__return__' => U('Receivables/client', ['mid' => $mid, 'cid' => $cid])
						]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有微信收款的权限',
						'__ajax__' => true
					];
				break;
				case 'search':
					$keyword = urlencode(I('post.keyword', ''));
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');
					/** @var \Core\Model\CouponModel $coupon_model */
					$coupon_model       = D('Core/Coupon');
					$coupon_item_result = $coupon_item_model->findRecord(2, [
						'mid'    => I('get.mid', 0, 'int'),
						'status' => '0'
					]);
					$new_list           = [];
					foreach($coupon_item_result as $k => $v){
						$coupon_result = $coupon_model->findCoupon(1, [
							'id'     => $v['coupon_id'],
							'status' => 'not deleted'
						]);
						$condition_1   = strpos(urlencode($coupon_result['name']), $keyword);
						$condition_2   = strpos(urlencode($v['code']), $keyword);
						if($keyword == ''){
							$v['coupon_name']      = $coupon_result['name'];
							$group                 = $v['coupon_name'];
							$new_list["$group "][] = $v;//空格识别数字健
						}
						else{
							if($condition_1>=0){
								$v['coupon_name']      = $coupon_result['name'];
								$group                 = $v['coupon_name'];
								$new_list["$group "][] = $v;//空格识别数字健
							}
							elseif($condition_2>=0){
								$v['coupon_name']      = $coupon_result['name'];
								$group                 = $v['coupon_name'];
								$new_list["$group "][] = $v;//空格识别数字健
							}
						}
					}

					return array_merge($new_list, ['__ajax__' => true]);
				break;
			}
		}

		//查出对应id的支付类型和收款类型
		public function selectReceivablesMethod($data){
			/** @var \Core\Model\PayMethodModel $pay_method_model */
			$pay_method_model = D('Core/PayMethod');
			/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
			$receivables_type_model = D('Core/ReceivablesType');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			$coupon_item_model       = D('Core/CouponItem');
			$coupon_item_ids         = explode(',', $data['coupon_ids']);
			$pay_method_result       = $pay_method_model->findRecord(1, ['id' => $data['method']]);
			$receivables_type_result = $receivables_type_model->findRecord(1, ['id' => $data['type']]);
			$employee_result         = $employee_model->findEmployee(1, ['id' => $data['payee_id']]);
			$client_result           = $client_model->findClient(1, ['id' => $data['cid']]);
			$ids                     = [];
			$ids_name                = '';
			foreach($coupon_item_ids as $k => $v){
				$ids[] = $coupon_item_model->findRecord(1, ['id' => $v]);
				$ids_name[] .= $ids[$k]['code'].' ';
			}
			$data['method_name']   = $pay_method_result['name'];
			$data['type_name']     = $receivables_type_result['name'];
			$data['name']          = $client_result['name'];
			$data['employee_name'] = $employee_result['name'];
			$data['coupon_item']   = $ids_name;
			$data['coupon_name']   = '';
			foreach($data['coupon_item'] as $k => $v){
				$data['coupon_name'] .= $v;
			}

			return $data;
		}

		//比对出收款与未收款的数据
		public function selectReceivablesType($join_result){
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model  = D('Core/Receivables');
			$receivables_result = $receivables_model->findRecord(2, [
				'mid'    => I('get.mid', 0, 'int'),
				'status' => 'not deleted'
			]);
			$receivables_cid    = [];
			foreach($receivables_result as $key => $val){
				$receivables_cid[] = $val['cid'];
			}
			$new_list = [
				'receivables'    => [],
				'notReceivables' => []
			];
			foreach($join_result as $key => $val){
				if(in_array($val['cid'], $receivables_cid)){
					$group                                = $val['unit'];
					$val['receivables']                   = 1;
					$new_list['receivables']["$group "][] = $val; //空格识别数字健
				}
				else{
					$group                                   = $val['unit'];
					$val['receivables']                      = 0;
					$new_list['notReceivables']["$group "][] = $val;//空格识别数字健
				}
			}

			return $new_list;
		}

		public function selectCouponItemType(){
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			$coupon_item_model = D('Core/CouponItem');
			/** @var \Core\Model\CouponModel $coupon_model */
			$coupon_model       = D('Core/Coupon');
			$coupon_item_result = $coupon_item_model->findRecord(2, [
				'mid'    => I('get.mid', 0, 'int'),
				'status' => 0
			]);
			$new_list           = [];
			foreach($coupon_item_result as $k => $v){
				$coupon_result         = $coupon_model->findCoupon(1, [
					'id'     => $v['coupon_id'],
					'status' => 'not deleted',
					'type'=>2
				]);
				if($coupon_result['type']==2){
					$v['coupon_name']                    = $coupon_result['name'];
					$new_list["$coupon_result[name] "][] = $v;//空格识别数字健
				}
			}

			return $new_list;
		}

		public function selectClientType(){
			/** @var \Core\Model\JoinModel $join_model */
			$join_model  = D('Core/Join');
			$join_result = $join_model->findRecord(2, ['status' => 'not deleted', 'mid' => I('get.mid', 0, 'int')]);
		}

		public function selectReceivablesList($eid){
			/** @var \Mobile\Model\ReceivablesModel $receivables_list_model */
			$receivables_list_model = D('Receivables');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			$mid          = I('get.mid', 0, 'int');
			$keyword      = I('get.keyword', '');
			$list         = $receivables_list_model->getClientReceivables($mid, $eid);
			$new_list     = [];
			foreach($list as $k => $v){
				$client_result = $client_model->findClient(1, ['id' => $v['cid']]);
				$condition_1   = strpos($client_result['name'], $keyword);
				$condition_2   = strpos($client_result['unit'], $keyword);
				if($condition_1 === 0 || $condition_1) $found = true;
				elseif($condition_2 === 0 || $condition_2) $found = true;
				elseif($keyword == '') $found = true;
				else $found = false;
				if($found){
					$v['c_name'] = $client_result['name'];
					$v['unit']   = $client_result['unit'];
					$new_list[]  = $v;
				}
			}

			return $new_list;
		}

		public function selectReceivablesListAll(){
			/** @var \Mobile\Model\ReceivablesModel $receivables_list_model */
			$receivables_list_model = D('Receivables');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$mid            = I('get.mid', 0, 'int');
			$keyword        = I('get.keyword', '');
			$list           = $receivables_list_model->getClientReceivablesAll($mid);
			$new_list       = [];
			foreach($list as $k => $v){
				$client_result   = $client_model->findClient(1, ['id' => $v['cid']]);
				$employee_result = $employee_model->findEmployee(1, ['id' => $v['payee_id']]);
				$condition_1     = strpos($client_result['name'], $keyword);
				$condition_2     = strpos($client_result['unit'], $keyword);
				$condition_3     = strpos($employee_result['name'], $keyword);
				if($condition_1 === 0 || $condition_1) $found = true;
				elseif($condition_2 === 0 || $condition_2) $found = true;
				elseif($condition_3 === 0 || $condition_3) $found = true;
				elseif($keyword == '') $found = true;
				else $found = false;
				if($found){
					$v['c_name'] = $client_result['name'];
					$v['unit']   = $client_result['unit'];
					$v['e_name'] = $employee_result['name'];
					$new_list[]  = $v;
				}
			}

			return $new_list;
		}
	}