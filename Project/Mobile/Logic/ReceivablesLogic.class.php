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
					$data               = I('post.', '');
					$mid                = I('get.mid', 0, 'int');
					$data['mid']        = $mid;
					$cid                = I('post.cid', 0, 'int');
					$data['cid']        = $cid;
					$data['coupon_ids'] = I('post.coupon_code', '');
					$data['time']       = strtotime(I('post.receivables_time', ''));
					$data['creatime']   = time();    //创建时间
					$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');
					/** @var \Core\Model\ClientModel $client_model */
					$client_model = D('Core/Client');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model     = D('Core/Employee');
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
						'__return__' => U('Receivables/Manage', ['mid' => $mid])
					];
				break;
			}
		}

		//比对出收款与未收款的数据
		public function SelectReceivablesType($join_result){
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
					$val['receivables']        = 1;
					$new_list['receivables'][] = $val;
				}
				else{
					$val['receivables']           = 0;
					$new_list['notReceivables'][] = $val;
				}
			}

			return $new_list;
		}

		//查出对应id的支付类型和收款类型
		public function SelectReceivablesMethod($data){
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
			$pay_method_result       = $pay_method_model->findPayMethod(1, ['id' => $data['method']]);
			$receivables_type_result = $receivables_type_model->findReceivablesType(1, ['id' => $data['type']]);
			$employee_result         = $employee_model->findEmployee(1, ['id' => $data['payee_id']]);
			$client_result           = $client_model->findClient(1, ['id' => $data['cid']]);
			$ids                     = [];
			$ids_name                = '';
			foreach($coupon_item_ids as $k => $v){
				$ids[] = $coupon_item_model->findCouponItem(1, ['id' => $v]);
				$ids_name .= $ids[$k]['code'].',';
			}
			$data['method_name']   = $pay_method_result['name'];
			$data['type_name']     = $receivables_type_result['name'];
			$data['name']          = $client_result['name'];
			$data['employee_name'] = $employee_result['name'];
			$data['coupon_name']   = $ids_name;

			return $data;
		}
	}