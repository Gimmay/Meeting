<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:35
	 */
	namespace Mobile\Logic;

	use Manager\Logic\MessageLogic;

	class ReceivablesLogic extends MobileLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'create_receivables':
					C('TOKEN_ON', false);
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');
					/** @var \Core\Model\ClientModel $client_model */
					$client_model = D('Core/Client');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model     = D('Core/Employee');
					$data               = I('post.', '');
					$mid                = I('get.mid', 0, 'int');
					$data['mid']        = $mid;
					$cid                = I('get.cid', 0, 'int');
					$data['cid']        = $cid;
					$data['coupon_ids'] = I('post.coupon_id', '');
					$data['payee_id']   = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['time']       = strtotime(I('post.receivables_time', ''));
					$data['creatime']   = time();    //创建时间
					$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					$receivables_result = $receivables_model->createRecord($data);
					$coupon_item_code   = explode(',', $data['coupon_ids']);
					foreach($coupon_item_code as $k => $v){
						$coupon_item_result = $coupon_item_model->alterCouponItem($v, [
							'status' => 1,
							'cid'    => $cid
						]);
					}
					//查出开拓顾问
					$client_result   = $client_model->findClient(1, ['id' => $cid]);
					$employee_result = $employee_model->findEmployee(1, ['keyword' => $client_result['develop_consultant']]);
					if($employee_result){
						$message_logic = new MessageLogic();
						$sms_send      = $message_logic->send($mid, 0, 0, 3, [$employee_result['id']]);
					}

					return [
						'data'       => $receivables_result,
						'__ajax__'   => true,
						'__return__' => U('Receivables/client', ['mid' => $mid, 'cid' => $cid])
					];
				break;
				case 'search':
					$keyword = urlencode(I('post.keyword', ''));
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');
					/** @var \Core\Model\CouponModel $coupon_model */
					$coupon_model       = D('Core/Coupon');
					$coupon_item_result = $coupon_item_model->findCouponItem(2, [
						'mid'    => I('get.mid', 0, 'int'),
						'status' => '0'
					]);
					$new_list           = [];
					foreach($coupon_item_result as $k => $v){
						$coupon_result = $coupon_model->findCoupon(1, ['id' => $v['coupon_id'],'status'=>'not deleted']);
						$condition_1   = strpos(urlencode($coupon_result['name']), $keyword);
						$condition_2   = strpos(urlencode($v['code']), $keyword);
						if($keyword==''){
							$v['coupon_name']      = $coupon_result['name'];
							$group                 = $v['coupon_name'];
							$new_list["$group "][] = $v;//空格识别数字健
						}else{
							if($condition_1 >= 0){
								$v['coupon_name']      = $coupon_result['name'];
								$group                 = $v['coupon_name'];
								$new_list["$group "][] = $v;//空格识别数字健
							}
							elseif($condition_2 >= 0){
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
				$ids[] = $coupon_item_model->findCouponItem(1, ['id' => $v]);
				$ids_name[] .= $ids[$k]['code'].',';
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
			$coupon_item_result = $coupon_item_model->findCouponItem(2, [
				'mid'    => I('get.mid', 0, 'int'),
				'status' => '0'
			]);
			$new_list           = [];
			foreach($coupon_item_result as $k => $v){
				$coupon_result         = $coupon_model->findCoupon(1, ['id' => $v['coupon_id'],'status'=>'not deleted']);
				$v['coupon_name']      = $coupon_result['name'];
				$group                 = $v['coupon_name'];
				$new_list["$group "][] = $v;//空格识别数字健
			}

			return $new_list;
		}

		public function selectClientType(){
			/** @var \Core\Model\JoinModel $join_model */
			$join_model  = D('Core/Join');
			$join_result = $join_model->findRecord(2, ['status' => 'not deleted', 'mid' => I('get.mid', 0, 'int')]);
		}
	}