<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-8
	 * Time: 17:20
	 */
	namespace Manager\Logic;

	use Core\Logic\SMSLogic;
	use Quasar\StringPlus;

	class ReceivablesLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'create':
					$mid = isset($_GET['mid']) ? I('get.mid', 0, 'int') : ($_POST['mid'] ? I('post.mid', 0, 'int') : 0);
					$cid = isset($_GET['cid']) ? I('get.cid', 0, 'int') : ($_POST['cid'] ? I('post.cid', 0, 'int') : 0);
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model  = D('Core/Meeting');
					$meeting_result = $meeting_model->findMeeting(1, ['cid' => $mid]);
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					C('TOKEN_ON', false);
					$data               = I('post.', '');
					$data['mid']        = $mid;
					$data['cid']        = $cid;
					$data['payee_id']   = $_POST['payee_id'] ? I('post.payee_id', 0, 'int') : I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['time']       = strtotime(I('post.receivables_time'));
					$data['creatime']   = time();
					$data['coupon_ids'] = I('post.coupon_code', '');
					$receivables_result = $receivables_model->createRecord($data);
					if($receivables_result['status']){
						/** @var \Core\Model\CouponItemModel $coupon_item_model */
						$coupon_item_model = D('Core/CouponItem');
						$code_id           = explode(',', $data['coupon_ids']);
						foreach($code_id as $k => $v){
							$coupon_item_result = $coupon_item_model->alterCouponItem(['id' => $v], [
								'status' => 1,
								'cid'    => $data['cid']
							]);
						}
					}

					return array_merge($receivables_result, ['__ajax__' => false]);
				break;
				case 'alter_coupon':
					$id = I('post.id', '');
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model  = D('Core/CouponItem');
					$coupon_item_result = $coupon_item_model->findCouponItem(2, ['mid' => I('get.mid', 0, 'int')]);
					$receivables_result = $receivables_model->findRecord(1, ['id' => $id]);
					$data               = explode(',', $receivables_result['coupon_ids']);
					$coupon_item_ids    = [];
					foreach($data as $k => $v){
						$coupon_item_ids[] = $coupon_item_model->findCouponItem(1, [
							'id'     => $v,
							'status' => 'not deleted'
						]);
					}
					$coupon_item_result_not = $coupon_item_model->findCouponItem(2, [
						'status' => '0',
						'mid'    => I('get.mid', 0, 'int')
					]);
					if(!$coupon_item_result_not){
						$coupon_item_result_not[] = null;
					}

					return array_merge([
						'coupon_item_yes' => $coupon_item_ids,
						'coupon_item_not' => $coupon_item_result_not
					], ['__ajax__' => true]);
				break;
				case 'alter':
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model  = D('Core/CouponItem');
					$old_coupon_code    = explode(',', I('post.old_coupon_code', ''));
					$data               = I('post.', '');
					$data['coupon_ids'] = I('post.coupon_code', '');
					$id                 = I('post.id', '');
					$new_coupon_id      = explode(',', $data['coupon_ids']);
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model     = D('Core/Receivables');
					$receivables_result_id = $receivables_model->findRecord(1, ['id' => $id]);
					C('TOKEN_ON', false);
					$coupon_item_model->alterCouponItem(['id' => ['in', $old_coupon_code]], [
						'status' => 0,
						'cid'    => null
					]);
					//					foreach($old_coupon_code as $k => $v){
					//						$coupon_item_result = $coupon_item_model->alterCouponItem(['id' => $v], [
					//							'status' => 0,
					//							'cid'    => null
					//						]);
					//					}
					$receivables_result = $receivables_model->alterRecord(['id' => $id], $data);
					$coupon_item_model->alterCouponItem(['id' => ['in', $new_coupon_id]], [
						'cid'    => $receivables_result_id['cid'],
						'status' => 1
					]);
					//					foreach($new_coupon_id as $k => $v){
					//						$coupon_item_result = $coupon_item_model->alterCouponItem(['id'=>$v], [
					//							'cid'    => $receivables_result_id['cid'],
					//							'status' => 1
					//						]);
					//					}
					return array_merge($receivables_result, ['__ajax__' => false]);
				break;
				case 'delete':
					$id = I('post.id', '');
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					C('TOKEN_ON', false);
					$receivables_result = $receivables_model->deleteRecord($id);

					return array_merge($receivables_result, ['__ajax__' => false]);
				break;
				case 'create_receivables':
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');
					$data              = I('post.', '');
					$mid               = I('get.mid', 0, 'int');
					$cid               = I('post.cid', 0, 'int');
					$coupon_item_id    = explode(',', $data['coupon_code']);
					C('TOKEN_ON', false);
					foreach($coupon_item_id as $k => $v){
						$coupon_item_record = $coupon_item_model->findCouponItem(1, [
							'id'     => $v,
							'status' => 'not deleted'
						]);
						if($coupon_item_record['status'] == 0){
							$coupon_item_result = $coupon_item_model->alterCouponItem(['id' => $v], [
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
					$logic = new \Core\Logic\ReceivablesLogic();
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					/** @var \Core\Model\ClientModel $client_model */
					$client_model = D('Core/Client');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model       = D('Core/Employee');
					$data['mid']          = $mid;
					$data['cid']          = $cid;
					$data['coupon_ids']   = I('post.coupon_code', '');
					$data['time']         = strtotime(I('post.receivables_time', ''));
					$data['creatime']     = time();    //创建时间
					$data['creator']      = I('session.MANAGER_EMPLOYEE_ID', 0, 'int'); //当前创建者
					$data['order_number'] = $logic->makeOrderNumber();
					$receivables_result   = $receivables_model->createRecord($data);
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
						$sms_send      = $message_logic->send($mid, 0, 0, 3, [$employee_result['id']]);
					}

					return array_merge($receivables_result, [
						'__ajax__'   => false,
						'__return__' => U('Receivables/Manage', ['mid' => $mid])
					]);
				break;
				case 'create_pay':
					/** @var \Core\Model\PayMethodModel $pay_method_model */
					$pay_method_model = D('Core/PayMethod');
					C('TOKEN_ON', false);
					$data              = I('post.', '');
					$data['name']      = I('post.payMethod_name', '');
					$data['creatime']  = time();    //创建时间
					$data['creator']   = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					$pay_method_result = $pay_method_model->createRecord($data);

					return array_merge($pay_method_result, ['__ajax__' => false]);
				break;
				case 'create_pos':
					/** @var \Core\Model\PosMachineModel $pos_machine_model */
					$pos_machine_model = D('Core/PosMachine');
					C('TOKEN_ON', false);
					$data               = I('post.', '');
					$data['creatime']   = time();    //创建时间
					$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					$pos_machine_result = $pos_machine_model->createRecord($data);

					return array_merge($pos_machine_result, ['__ajax__' => false]);
				break;
				case 'delete_pay':
					/** @var \Core\Model\PayMethodModel $pay_method_model */
					$pay_method_model  = D('Core/PayMethod');
					$id                = I('post.id', 0, 'int');
					$pay_method_result = $pay_method_model->deleteRecord([$id]);

					return array_merge($pay_method_result, ['__ajax__' => false]);
				break;
				case 'delete_pos':
					/** @var \Core\Model\PosMachineModel $pos_machine_model */
					$pos_machine_model  = D('Core/PosMachine');
					$id                 = I('post.id', 0, 'int');
					$pos_machine_result = $pos_machine_model->deleteRecord([$id]);

					return array_merge($pos_machine_result, ['__ajax__' => false]);
				break;
				case 'alter_pos':
					/** @var \Core\Model\PosMachineModel $pos_machine_model */
					$pos_machine_model = D('Core/PosMachine');
					$id                = I('post.id', 0, 'int');
					C('TOKEN_ON', false);
					$data               = I('post.', '');
					$data['name']       = I('post.pos_machine', '');
					$data['creatime']   = time();    //创建时间
					$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					$pos_machine_result = $pos_machine_model->alterRecord(['id' => $id], $data);

					return array_merge($pos_machine_result, ['__ajax__' => false]);
				break;
				case 'alter_pay':
					/** @var \Core\Model\PayMethodModel $pay_method_model */
					$pay_method_model = D('Core/PayMethod');
					$id               = I('post.id', 0, 'int');
					C('TOKEN_ON', false);
					$data              = I('post.', '');
					$pay_method_result = $pay_method_model->alterRecord(['id' => $id], $data);

					return array_merge($pay_method_result, ['__ajax__' => false]);
				break;
				case 'delete_type':
					/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
					$receivables_type_model  = D('Core/ReceivablesType');
					$id                      = I('post.id', 0, 'int');
					$receivables_type_result = $receivables_type_model->deleteRecord([$id]);

					return array_merge($receivables_type_result, ['__ajax__' => false]);
				break;
				case 'alter_type':
					/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
					$receivables_type_model = D('Core/ReceivablesType');
					$id                     = I('post.id', 0, 'int');
					C('TOKEN_ON', false);
					$data              = I('post.', '');
					$data['name']      = I('post.receivablesType_name', '');
					$data['creatime']  = time();    //创建时间
					$data['creator']   = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					$pay_method_result = $receivables_type_model->alterRecord(['id' => $id], $data);

					return array_merge($pay_method_result, ['__ajax__' => false]);
				break;
				case 'create_type':
					/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
					$receivables_type_model = D('Core/ReceivablesType');
					C('TOKEN_ON', false);
					$data              = I('post.', '');
					$data['name']      = I('post.receivablesType_name', '');
					$data['creatime']  = time();    //创建时间
					$data['creator']   = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
					$pay_method_result = $receivables_type_model->createRecord($data);

					return array_merge($pay_method_result, ['__ajax__' => false]);
				break;
				case 'get_receivables_detail':
					$id = I('post.id', 0, 'int');
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					/** @var \Core\Model\ClientModel $client_model */
					$client_model                      = D('Core/Client');
					$receivables_result                = $receivables_model->findRecord(1, ['id' => $id]);
					$client_result                     = $client_model->findClient(1, ['id' => $receivables_result['cid']]);
					$receivables_result['client_name'] = $client_result['name'];

					return array_merge($receivables_result, ['__ajax__' => true]);
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
			$join_model                  = D('Core/Join');
			$join_result                 = $join_model->findRecord(1, [
				'mid' => I('get.mid', 0, 'int'),
				'cid' => I('get.cid', 0, 'int')
			]);
			$meeting_result              = $meeting_model->findMeeting(1, ['id' => I('get.mid', 0, 'int')]);
			$join_result['meeting_name'] = $meeting_result['name'];

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
			$receivables_model = D('Core/Receivables');
			/** @var \Core\Model\PayMethodModel $pay_method_model */
			$pay_method_model = D('Core/Pay_method');
			/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
			$receivables_type_model = D('Core/ReceivablesType');
			/** @var \Core\Model\PosMachineModel $pos_machine_model */
			$pos_machine_model  = D('Core/PosMachine');
			$mid                = I('get.mid', 0, 'int');
			$cid                = I('get.cid', 0, 'int');
			$keyword            = I('get.keyword', '');
			$receivables_result = $receivables_model->findRecord(2, [
				'mid'    => $mid,
				'cid'    => $cid,
				'status' => 'not deleted'
			]);
			$new_list           = [];
			foreach($receivables_result as $k => $v){
				$method_result                          = $pay_method_model->findRecord(1, ['id' => $v['method']]);
				$receivables_type_result                = $receivables_type_model->findRecord(1, ['id' => $v['type']]);
				$meeting_result                         = $meeting_model->findMeeting(1, [
					'id'     => $receivables_result[$k]['mid'],
					'status' => 'not deleted'
				]);
				$client_result                          = $client_model->findClient(1, [
					'id'     => $receivables_result[$k]['cid'],
					'status' => 'not deleted'
				]);
				$pos_machine_result                     = $pos_machine_model->findRecord(1, ['id' => $v['pos_id']]);
				$receivables_result[$k]['meeting_name'] = $meeting_result['name'];
				$receivables_result[$k]['client_name']  = $client_result['name'];
				$receivables_result[$k]['unit']         = $client_result['unit'];
				$receivables_result[$k]['method_name']  = $method_result['name'];
				$receivables_result[$k]['type_name']    = $receivables_type_result['name'];
				$receivables_result[$k]['pos_name']     = $pos_machine_result['name'];
				$code_id                                = explode(',', $receivables_result[$k]['coupon_ids']);
				$coupon_item_code                       = '';
				foreach($code_id as $kk => $vv){
					$coupon_item_result = $coupon_item_model->findCouponItem(1, [
						'id'     => $vv,
						'status' => 'not deleted'
					]);
					$coupon_item_code .= $coupon_item_result['code'].',';  //点连接两个数据
				}
				$receivables_result[$k]['coupon_code'] = trim($coupon_item_code, ',');
				if(strpos($client_result['name'], $keyword) === false && $keyword != '') ;
				else $new_list[] = $receivables_result[$k];
			}
			if(IS_POST){
				$sms = new MessageLogic();
				$sms->send($mid, 3, [$cid]);
			}

			return $new_list;
		}

		public function setData($type, $data){
			switch($type){
				case 'excel_data':
					$new_list = [];
					/** @var \Core\Model\ClientModel $client_model */
					$client_model = D('Core/Client');
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					/** @var \Core\Model\PayMethodModel $pay_method_model */
					$pay_method_model = D('Core/PayMethod');
					/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
					$receivables_type_model = D('Core/ReceivablesType');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');
					foreach($data as $key => $val){
						$temp = [];
						// 根据id查询具体记录名称
						$client_result        = $client_model->findClient(1, ['id' => $val['cid']]);
						$temp['client_name']  = $client_result['name'];
						$temp['order_number'] = $val['order_number'];
						$temp['price']        = $val['price'];
						$temp['time']         = date('Y-m-d H:i:s', $val['time']);
						$temp['place']        = $val['place'];
						switch($val['source_type']){
							case 0:
								$temp['source_type'] = '会前收款';
							break;
							case 1:
								$temp['source_type'] = '会中收款';
							break;
							case 2:
								$temp['source_type'] = '会后收款';
							break;
						}
						$meeting_result          = $meeting_model->findMeeting(1, ['id' => $val['mid']]);
						$temp['meeting_name']    = $meeting_result['name'];
						$pay_method_result       = $pay_method_model->findRecord(1, ['id' => $val['type']]);
						$temp['pay_name']        = $pay_method_result['name'];
						$receivables_type_result = $receivables_type_model->findRecord(1, ['id' => $val['method']]);
						$temp['type_name']       = $receivables_type_result['name'];
						$employee_result         = $employee_model->findEmployee(1, ['id' => $val['payee_id']]);
						$temp['employee_name']   = $employee_result['name'];
						$coupon_id               = explode(',', $val['coupon_ids']);
						foreach($coupon_id as $k => $v){
							$coupon_item_result = $coupon_item_model->findCouponItem(1, ['id' => $v]);
							$temp['coupon_name'] .= $coupon_item_result['code'].',';
						}
						$temp['comment'] = $val['comment'];
						//添加记录
						$new_list[] = $temp;
					}
					$new_list = array_merge([
						[
							'客户姓名',
							'收据号',
							'收款金额',
							'收款时间',
							'收款地点',
							'来源状态',
							'会议名称',
							'支付方式',
							'付款方式',
							'收款人',
							'代金券',
							'备注',
						]
					], $new_list);

					return $new_list;
				break;
				default:
					return $data;
				break;
			}
		}
	}