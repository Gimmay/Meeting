<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-8
	 * Time: 17:20
	 */
	namespace Manager\Logic;

	use Core\Logic\SMSLogic;

	class ReceivablesLogic extends ManagerLogic{

		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'message':
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					C('TOKEN_ON', false);
					$data               = I('post.', '');
					$data['mid']        = I('get.mid', 0, 'int');
					$data['cid']        = I('get.id', 0, 'int');
					$data['payee_id']   = I('post.payeeName');
					$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['time']       = time();
					$data['creatime']   = time();
					$data['coupon_ids'] = I('post.coupon_code', '');
					$receivables_result = $receivables_model->createRecord($data);
					if($receivables_result['status'] == 1){
						/** @var \Core\Model\CouponItemModel $coupon_item_model */
						$coupon_item_model = D('Core/Coupon_item');
						$code_id           = explode(',', $data['coupon_ids']);
						foreach($code_id as $k => $v){
							$coupon_item_result = $coupon_item_model->alterCouponItem($v, [
								'status' => 1,
								'cid'    => $data['cid']
							]);
						}
					}

					return ['status' => true, 'message' => '收款成功', '__ajax__' => false];
				break;
				case 'search';
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');
					/** @var \Core\Model\clientModel $client_model */
					$client_model = D('Core/client');
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model  = D('Core/Receivables');
					$mid                = I('post.type', '');
					$cid                = I('post.client_name', '');
					$receivables_result = $receivables_model->findRecord(2, [
						'mid' => $mid,
						'cid' => $cid
					]);
					foreach($receivables_result as $k => $v){
						$meeting_result                = $meeting_model->findMeeting(1, ['id' => $receivables_result[$k]['mid']]);
						$client_result                 = $client_model->findClient(1, ['id' => $receivables_result[$k]['cid']]);
						$receivables_result[$k]['mid'] = $meeting_result['name'];
						$receivables_result[$k]['cid'] = $client_result['name'];
						$code_id                       = explode(',', $receivables_result[$k]['coupon_ids']);
						$coupon_item_code              = '';
						foreach($code_id as $kk => $vv){
							$coupon_item_result = $coupon_item_model->findCouponItem(1, ['id' => $vv]);
							$coupon_item_code .= $coupon_item_result['code'].',';  //点连接两个数据
						}
						$receivables_result[$k]['coupon_code'] = trim($coupon_item_code, ',');
					}

					return $receivables_result;
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function findCouponItem(){
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			$coupon_item_model = D('Core/CouponItem');
			$result            = $coupon_item_model->findCouponItem(2, [
				'mid'    => I('get.mid', 0, 'int'),
				'status' => 0
			]);

			return $result;
		}

		public function findMeetingClient(){
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model         = D('Core/Join');
			$join_result        = $join_model->findRecord(1, [
				'mid' => I('get.mid', 0, 'int'),
				'cid' => I('get.cid', 0, 'int')
			]);
			$meeting_result     = $meeting_model->findMeeting(1, ['id' => I('get.mid', 0, 'int')]);
			$join_result['mid'] = $meeting_result['name'];

			return $join_result;
		}

		public function findReceivables(){
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			$coupon_item_model = D('Core/CouponItem');
			/** @var \Core\Model\clientModel $client_model */
			$client_model = D('Core/client');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model  = D('Core/Receivables');
			$mid                = I('get.mid', 0, 'int');
			$cid                = I('get.cid', 0, 'int');
			$receivables_result = $receivables_model->findRecord(2, [
				'mid' => $mid,
				'cid' => $cid
			]);
			foreach($receivables_result as $k => $v){
				$meeting_result                = $meeting_model->findMeeting(1, ['id' => $receivables_result[$k]['mid']]);
				$client_result                 = $client_model->findClient(1, ['id' => $receivables_result[$k]['cid']]);
				$receivables_result[$k]['mid'] = $meeting_result['name'];
				$receivables_result[$k]['cid'] = $client_result['name'];
				$code_id                       = explode(',', $receivables_result[$k]['coupon_ids']);
				$coupon_item_code              = '';
				foreach($code_id as $kk => $vv){
					$coupon_item_result = $coupon_item_model->findCouponItem(1, ['id' => $vv]);
					$coupon_item_code .= $coupon_item_result['code'].',';  //点连接两个数据
				}
				$receivables_result[$k]['coupon_code'] = trim($coupon_item_code, ',');
			}
			if(IS_POST){
				$sms = new MessageLogic();
				$sms->send($mid, 3, [$cid]);
			}

			return $receivables_result;
		}
	}