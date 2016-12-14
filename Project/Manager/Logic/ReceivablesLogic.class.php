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
	use Think\Page;

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
							$coupon_item_result = $coupon_item_model->alterRecord(['id' => $v], [
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
					$coupon_item_result = $coupon_item_model->findRecord(2, ['mid' => I('get.mid', 0, 'int')]);
					$receivables_result = $receivables_model->findRecord(1, ['id' => $id]);
					$data               = explode(',', $receivables_result['coupon_ids']);
					$coupon_item_ids    = [];
					foreach($data as $k => $v){
						$coupon_item_ids[] = $coupon_item_model->findRecord(1, [
							'id'     => $v,
							'status' => 'not deleted'
						]);
					}
					$coupon_item_result_not = $coupon_item_model->listRecord(2, [
						'main.status' => 0,
						'sub.status'  => 1,
						'mid'         => I('get.mid', 0, 'int')
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
					$coupon_item_model->alterRecord(['id' => ['in', $old_coupon_code]], [
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
					$coupon_item_model->alterRecord(['id' => ['in', $new_coupon_id]], [
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
						$message_logic->send($mid, 0, 3, [$employee_result['id']]);
					}

					return array_merge($receivables_result, [
						'__ajax__'   => false,
						'__return__' => I('post.redirectUrl', '')
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
					$data['mid']        = I('get.mid', 0, 'int');
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
					/** @var \Core\Model\ReceivablesModel $model */
					$model = D('Core/Receivables');
					/** @var \Core\Model\ReceivablesOptionModel $receivables_option_model */
					$receivables_option_model = D('Core/ReceivablesOption');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					/** @var \Core\Model\ClientModel $client_model */
					$client_model = D('Core/Client');
					/** @var \Core\Model\PayMethodModel $pay_method_model */
					$pay_method_model       = D('Core/PayMethod');
					$str_obj                = new StringPlus();
					$core_receivables_logic = new \Core\Logic\ReceivablesLogic();
					$id                     = I('post.id', 0, 'int');
					$mid                    = I('get.mid', 0, 'int');
					$record                 = $model->findRecord(1, ['id' => $id, 'status' => 1]);
					if($record){
						$receivables_option_list = $receivables_option_model->findRecord(2, ['rid' => $record['id']]);
						$pay_method_arr          = [];
						$price                   = 0;
						foreach($receivables_option_list as $key => $val){
							$pay_method                                   = $pay_method_model->findRecord(1, [
								'id'     => $val['pay_method'],
								'status' => 1
							]);
							$receivables_option_list[$key]['source_type'] = $core_receivables_logic->getReceivablesSourceType($val['type']);
							$receivables_option_list[$key]['pay_method']  = $pay_method['name'];
							$price += $val['price'];
						}
						$coupon_id_arr = explode(',', $record['coupon_ids']);
						if($coupon_id_arr){
							/** @var \Core\Model\CouponModel $coupon_model */
							$coupon_model = D('Core/Coupon');
							/** @var \Core\Model\CouponItemModel $coupon_item_model */
							$coupon_item_model = D('Core/CouponItem');
							$coupon_name       = '';
							foreach($coupon_id_arr as $coupon){
								$coupon_item_record = $coupon_item_model->findRecord(1, ['id' => $coupon]);
								$coupon_record      = $coupon_model->findCoupon(1, ['id' => $coupon_item_record['coupon_id']]);
								$coupon_name .= "$coupon_record[name], ";
							}
							$coupon_name = trim($coupon_name, ',');
						}
						else $coupon_name = '';
						$client                              = $client_model->findClient(1, [
							'status' => 1,
							'id'     => $record['cid']
						]);
						$payee                               = $employee_model->findEmployee(1, [
							'status' => 1,
							'id'     => $record['payee_id']
						]);
						$record['option']                    = $receivables_option_list;
						$record['option']['pay_method_list'] = $pay_method_arr;
						$record['receivables_type']          = $core_receivables_logic->getReceivablesType($record['type']);
						$record['client']                    = $client['name'];
						$record['payee']                     = $payee['name'];
						$record['price']                     = $price;
						$record['coupon_name']               = $coupon_name;
						$record['unit']                      = $client['unit'];
						$record['price_word']                = $str_obj->parseNumberToUpperWord($price);
					}

					return array_merge($record, ['__ajax__' => true]);
				break;
				case 'import_excel':
					if($this->permissionList['RECEIVABLES.IMPORT-EXCEL']){
						$excel_logic = new ExcelLogic();
						$result1     = $excel_logic->importReceivablesData($_FILES);
						$result2     = $this->saveReceivablesFromExcelData($result1['data']['content']);

						return array_merge($result2, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有导入收款的权限',
						'__ajax__' => true
					];
				break;
				case 'ticket': //门票
					/** @var \Core\Model\CouponModel $coupon_model */
					$coupon_model  = D('Core/Coupon');
					$coupon_result = $coupon_model->findCoupon(2, [
						'status' => 'not deleted',
						'type'   => 1,
						'mid'    => I('get.mid', 0, 'int')
					]);
					$data          = [];
					foreach($coupon_result as $k => $v){
						$data[$k]['id']    = $v['id'];
						$data[$k]['name']  = $v['name'];
						$data[$k]['price'] = $v['price'];
						$data[$k]['type']  = $v['type'];
					}

					return array_merge($data, ['__ajax__' => true]);
				break;
				case 'coupon':
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');
					/** @var \Core\Model\CouponModel $coupon_model */
					$coupon_model  = D('Core/Coupon');
					$coupon_result = $coupon_model->findCoupon(2, [
						'status' => 1,
						'mid'    => I('get.mid', 0, 'int'),
						'type'   => 2
					]);
					//					foreach($coupon_result as $k => $v){
					//						$coupon_item_result = $coupon_item_model->findRecord(2, [
					//							'status'    => 0,
					//							'coupon_id' => $v['id'],
					//							'mid'       => I('get.mid', 0, 'int')
					//						]);
					//						if(!$coupon_item_result){
					//							$coupon_result = [];
					//						}
					//					}
					//					$coupon_item_result = $coupon_item_model->findRecord(2, [
					//						'mid'    => I('get.mid', 0, 'int'),
					//						'status' => 0
					//					]);
					//					$new_list           = [];
					//					foreach($coupon_item_result as $k => $v){
					//						$coupon_result = $coupon_model->findCoupon(1, [
					//							'id'     => $v['coupon_id'],
					//							'status' => 'not deleted',
					//							'type'   => 2
					//						]);
					//						if($coupon_result['type'] == 2){
					//							$v['coupon_name']                    = $coupon_result['name'];
					//							$new_list["$coupon_result[name] "][] = $v;//空格识别数字健
					//						}
					//					}
					return array_merge($coupon_result, ['__ajax__' => true]);
				break;
				case 'product': //产品
					/** @var \Core\Model\CouponModel $coupon_model */
					$coupon_model  = D('Core/Coupon');
					$coupon_result = $coupon_model->findCoupon(2, [
						'status' => 'not deleted',
						'type'   => 3,
						'mid'    => I('get.mid', 0, 'int')
					]);
					$data          = [];
					foreach($coupon_result as $k => $v){
						$data[$k]['id']    = $v['id'];
						$data[$k]['name']  = $v['name'];
						$data[$k]['price'] = $v['price'];
						$data[$k]['type']  = $v['type'];
					}

					return array_merge($data, ['__ajax__' => true]);
				break;
				case 'other':// 其他
					/** @var \Core\Model\CouponModel $coupon_model */
					$coupon_model  = D('Core/Coupon');
					$coupon_result = $coupon_model->findCoupon(2, [
						'status' => 'not deleted',
						'type'   => 4,
						'mid'    => I('get.mid', 0, 'int')
					]);
					$data          = [];
					foreach($coupon_result as $k => $v){
						$data[$k]['id']    = $v['id'];
						$data[$k]['name']  = $v['name'];
						$data[$k]['price'] = $v['price'];
						$data[$k]['type']  = $v['type'];
					}

					return array_merge($data, ['__ajax__' => true]);
				break;
				//				case 'coupon':
				//					/** @var \Core\Model\CouponModel $coupon_model */
				//					$coupon_model = D('Core/Coupon');
				//					/** @var \Core\Model\CouponItemModel $coupon_item_model */
				//					$coupon_item_model = D('Core/CouponItem');
				//					$coupon_result     = $coupon_model->findCoupon(2, [
				//						'mid'    => I('get.mid', 0, 'int'),
				//						'type'   => "代金券",
				//						'status' => 'not deleted'
				//					]);
				//					$coupon_item_ids   = [];
				//					foreach($coupon_result as $k => $v){
				//						$coupon_item_ids[] = $coupon_item_model->findRecord(2, [
				//							'coupon_id' => $v['id'],
				//							'status'    => 0
				//						]);
				//					}
				//
				//
				//					return array_merge($coupon_result, ['__ajax__' => true]);
				//				break;
				case 'create_list':
					//					$type = I('post.type');
					//					$data = I('post.');
					//
					//					foreach ($type as $k=>$v){
					//						if($data['name'][$k] == 0) continue;
					//
					//					}
					//					exit;
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');
					/** @var \Core\Model\ReceivablesOptionModel $receivables_option_model */
					$receivables_option_model = D('Core/ReceivablesOption');
					$type                     = I('post.type', '');
					$mid                      = I('get.mid', 0, 'int');
					$cid                      = I('post.client_name', 0, 'int');
					$data                     = I('post.');
					$payee_id                 = I('post.payee_id', 0, 'int');
					$creator                  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['coupon_code']      = I('post.coupon_code');
					$data['coupon_ids']       = I('post.select_code');
					$place                    = I('post.place', '');
					C('TOKEN_ON', false);
					$option = [];
					foreach($type as $k => $v){
						if($data['name'][$k] == 0) continue;
						$temp = [
							'cid'          => $cid,
							'mid'          => $mid,
							'time'         => time(),
							'type'         => $data['type'][$k],
							'payee_id'     => $payee_id,
							'order_number' => $data['order_number'],
							'coupon_code'  => $data['name'][$k],
							'creatime'     => time(),    //创建时间
							'creator'      => $creator,    //当前创建者
							'place'        => $place,
							'coupon_ids'   => $data['select_code'][$k]
						];//获取post 过来的数据.
						if($v == 2){//判断是否代金券收款
							$receivables_result = $receivables_model->createRecord($temp);  //把post过来的数据插入到数据库.
							$coupon_item_list   = explode(',', $data['select_code'][$k]);
							foreach($coupon_item_list as $kk => $vv){//把代金券给对应的客户使用.
								$coupon_item_id = $coupon_item_model->findRecord(1, ['id' => $vv]);
								$coupon_item_model->alterRecord(['id' => $coupon_item_id['id']], [
									'cid'    => $cid,
									'status' => 1
								]);
							}
							foreach($data['payMethod'] as $k1 => $v1){
								$option[] = [
									'rid'         => $receivables_result['id'],
									'price'       => $data['price'][$k],
									'pay_method'  => $v1,
									'pos_machine' => $data['pos'][$k],
									'type'        => $data['source_type'][$k],
									'comment'     => $data['comment'][$k],
									'creatime'    => time(),    //创建时间
									'creator'     => $creator,    //当前创建者
								];
							}
							$receivables_option_model->createMultiRecord($option);
						}
						if($v != 2){//判断非代金券收款
							$receivables_result = $receivables_model->createRecord($temp);  //把post过来的数据插入到数据库.
							$coupon_item_result = $coupon_item_model->createRecord([
								//到项目类型里新增一条对应的收款类型
								'coupon_id' => $temp['coupon_code'],
								'mid'       => $mid,
								'cid'       => $cid,
								'status'    => 1,
								'creatime'  => time(),    //创建时间
								'creator'   => $creator    //当前创建者
							]);
							$receivables_model->alterRecord(['id' => $receivables_result['id']], ['coupon_ids' => $coupon_item_result['id']]); //把项目类型的id 插入到对应收款的coupon_ids
							foreach($data["price$k"] as $k1 => $v1){
								$option = [
									'rid'         => $receivables_result['id'],
									'price'       => $v1,
									'pay_method'  => $data["payMethod$k"][$k1],
									'pos_machine' => $data["pos$k"][$k1],
									'type'        => $data["source_type$k"][$k1],
									'comment'     => $data["comment$k"][$k1],
									'creatime'    => time(),    //创建时间
									'creator'     => $creator,    //当前创建者
								];
								$receivables_option_model->createRecord($option);
							}
						}
						//						if($data['type'][$k] != 2){
						//
						//							$coupon_item_project = [
						//								'mid'       => $mid,
						//								'coupon_id' => $data['name'][$k],
						//								'cid'       => $cid,
						//								'creatime'  => time(),
						//								'creator'   => $creator,
						//								'status'    => 1
						//							];
						//
						//							$result1             = $coupon_item_model->createRecord($coupon_item_project);
						//							$temp['coupon_ids']  = $result1['id'];
						//
						//						}
						//						elseif($data['type'][$k] == 2){
						//							$coupon_id = $data['coupon_code'];
						//							foreach($coupon_id as $k1 => $v1) $coupon_item_code[] = $v1;
						//							$temp['coupon_ids'] = $data['coupon_ids'][$k];
						//						}
					}

					return array_merge($receivables_result, [
						'__ajax__'   => false,
						'__return__' => I('post.redirectUrl', '')
					]);
				break;
				case 'get_coupon_code':
					$coupon_id = I('post.id', 0, 'int');
					$mid       = I('get.mid', 0, 'int');
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model  = D('Core/CouponItem');
					$coupon_item_result = $coupon_item_model->findRecord(2, [
						'status'    => 0,
						'coupon_id' => $coupon_id,
						'mid'       => $mid
					]);
					$data               = [];
					foreach($coupon_item_result as $k => $v){
						$data[] = ['id' => $v['id'], 'name' => $v['code']];
					}

					return array_merge($data, ['__ajax__' => true]);
				break;
				//				case 'get_receivables':
				//					$cid = I('post.id', 0, 'int');
				//					$mid = I('get.mid', 0, 'int');
				//					/** @var \Core\Model\ReceivablesModel $receivables_model */
				//					$receivables_model = D('Core/Receivables');
				//					/** @var \Core\Model\ClientModel $client_model */
				//					$client_model = D('Core/Client');
				//					/** @var \Core\Model\EmployeeModel $employee_model */
				//					$employee_model = D('Core/Employee');
				//					/** @var \Core\Model\PayMethodModel $pay_method_model */
				//					$pay_method_model = D('Core/PayMethod');
				//					/** @var \Core\Model\PosMachineModel $pos_machine_model */
				//					$pos_machine_model = D('Core/PosMachine');
				//					/** @var \Core\Model\CouponItemModel $coupon_item_model */
				//					$coupon_item_model = D('Core/CouponItem');
				//					/** @var \Core\Model\CouponModel $coupon_model */
				//					$coupon_model       = D('Core/Coupon');
				//					$receivables_result = $receivables_model->findRecord(2, [
				//						'mid'    => $mid,
				//						'cid'    => $cid,
				//						'status' => 1
				//					]);
				//					$data               = [];
				//					foreach($receivables_result as $k => $v){
				//						$client_result      = $client_model->findClient(1, ['id' => $cid, 'status' => 1]);
				//						$employee_result    = $employee_model->findEmployee(1, ['id' => $v['payee_id'], 'status' => 1]);
				//						$pay_method_result  = $pay_method_model->findRecord(1, ['id' => $v['method'], 'status' => 1]);
				//						$pos_machine_result = $pos_machine_model->findRecord(1, ['id' => $v['pos_id'], 'status' => 1]);
				//						$coupon_item_result = $coupon_item_model->findRecord(1, [
				//							'id'     => $v['coupon_ids'],
				//							'status' => 1
				//						]);
				//						$coupon_result      = $coupon_model->findCoupon(1, [
				//							'id'     => $coupon_item_result['coupon_id'],
				//							'status' => 1
				//						]);
				//						$data[]             = [
				//							'id'            => $v['id'],
				//							'client_name'   => $client_result['name'],
				//							'order_number'  => $v['order_number'],
				//							'price'         => $v['price'],
				//							'type'          => $v['type'],
				//							'name'          => $coupon_result['name'],
				//							'source_type'   => $v['source_type'],
				//							'pos_name'      => $pos_machine_result['name'],
				//							'unit'          => $client_result['unit'],
				//							'method_name'   => $pay_method_result['name'],
				//							'employee_name' => $employee_result['name'],
				//							'coupon_name'   => $coupon_item_result['code'],
				//							'comment'       => $v['comment'],
				//							'place'         => $v['place'],
				//							'time'          => $v['time'],
				//						];
				//					}
				//
				//					return array_merge($data, ['__ajax__' => true]);
				//				break;
				//				//				case 'create_coupon_item':
				//				//					$coupon_id = explode(',',I('post.coupon_id'));
				//				//					/** @var \Core\Model\ReceivablesModel $receivables_model */
				//				//					$receivables_model = D('Core/Receivables');
				//				//					/** @var \Core\Model\CouponItemModel $coupon_item_model */
				//				//					$coupon_item_model  = D('Core/CouponItem');
				//				//
				//				//				break;
				case 'get_receivables':
					/** @var \Core\Model\ReceivablesModel $model */
					$model = D('Core/Receivables');
					/** @var \Core\Model\ReceivablesOptionModel $receivables_option_model */
					$receivables_option_model = D('Core/ReceivablesOption');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					/** @var \Core\Model\ClientModel $client_model */
					$client_model = D('Core/Client');
					/** @var \Core\Model\PayMethodModel $pay_method_model */
					$pay_method_model       = D('Core/PayMethod');
					$str_obj                = new StringPlus();
					$core_receivables_logic = new \Core\Logic\ReceivablesLogic();
					$id                     = I('post.id', 0, 'int');
					$mid                    = I('get.mid', 0, 'int');
					$record                 = $model->findRecord(1, ['id' => $id, 'status' => 1]);
					if($record){
						$receivables_option_list = $receivables_option_model->findRecord(2, ['rid' => $record['id']]);
						$price                   = 0;
						$pay_method_i            = 0;
						$pay_method_arr          = $pay_method_ids = $pay_method_index = [];
						foreach($receivables_option_list as $key => $val){
							$pay_method = $pay_method_model->findRecord(1, ['id' => $val['pay_method'], 'status' => 1]);
							$price += $val['price'];
							$receivables_option_list[$key]['source_type'] = $core_receivables_logic->getReceivablesSourceType($val['type']);
							$receivables_option_list[$key]['pay_method']  = $pay_method['name'];
							if(!in_array($val['pay_method'], $pay_method_ids)){
								$pay_method_ids[$pay_method_i]        = $val['pay_method'];
								$pay_method_index[$val['pay_method']] = $pay_method_i;
								$pay_method_arr[$pay_method_i]        = [
									'name'  => $pay_method['name'],
									'price' => $val['price']
								];
								$pay_method_i++;
							}
							else{
								$i = $pay_method_index[$val['pay_method']];
								$pay_method_arr[$i]['price'] += $val['price'];
							}
						}
						$client                              = $client_model->findClient(1, [
							'status' => 1,
							'id'     => $record['cid']
						]);
						$payee                               = $employee_model->findEmployee(1, [
							'status' => 1,
							'id'     => $record['payee_id']
						]);
						$record['option']                    = $receivables_option_list;
						$record['option']['pay_method_list'] = $pay_method_arr;
						$record['receivables_type']          = $core_receivables_logic->getReceivablesType($record['type']);
						$record['client']                    = $client['name'];
						$record['payee']                     = $payee['name'];
						$record['price']                     = $price;
						$record['unit']                      = $client['unit'];
						$record['price_word']                = $str_obj->parseNumberToUpperWord($price);
					}

					return array_merge($record, ['__ajax__' => true]);
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		// 导入excel收款数据
		private function saveReceivablesFromExcelData($data){
			/** @var \Manager\Model\ReceivablesModel $self_model */
			$self_model = D('Receivables');
			/** @var \Core\Model\ReceivablesModel $model */
			$model = D('Core/Receivables');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			/** @var \Core\Model\PayMethodModel $pay_method_model */
			$pay_method_model = D('Core/PayMethod');
			/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
			$receivables_type_model = D('Core/ReceivablesType');
			/** @var \Core\Model\PosMachineModel $pos_machine_model */
			$pos_machine_model = D('Core/PosMachine');
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			$coupon_item_model = D('Core/CouponItem');
			$receivables_logic = new \Core\Logic\ReceivablesLogic();
			$table_column      = $self_model->getColumn();
			$count             = 0;
			$error_msg         = '';
			$mid               = I('get.mid', 0, 'int');
			$creator           = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			foreach($data as $key1 => $val1){
				$save_data   = [];
				$client_name = '';
				foreach($val1 as $key2 => $val2){
					switch($table_column[$key2]['name']){
						case 'cid': // 根据参会人员名称反查ID
							$client_name = $val2;
							$client      = $client_model->findClient(1, ['name' => $val2, 'status' => 'not deleted']);
							if($client) $val2 = $client['id'];
							else $val2 = null;
						break;
						case 'payee_id': // 根据员工名称反查ID
							$payee = $employee_model->findEmployee(1, ['name' => $val2, 'status' => 'not deleted']);
							if($payee) $val2 = $payee['id'];
							else $val2 = null;
						break;
						case 'method': // 根据支付方式名称反查ID 存在则选择不存在则创建
							$pay_method = $pay_method_model->findRecord(1, [
								'name'   => $val2,
								'status' => 'not deleted'
							]);
							if($pay_method) $val2 = $pay_method['id'];
							else{
								$result = $pay_method_model->createRecord([
									'name'     => $val2,
									'creatime' => time(),
									'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')
								]);
								if($result['status']) $val2 = $result['id'];
								else $val2 = null;
							}
						break;
						case 'type': // 根据收款类型名称反查ID 存在则选择不存在则创建 todo 删除
							$receivables_type = $receivables_type_model->findRecord(1, [
								'name'   => $val2,
								'status' => 'not deleted'
							]);
							if($receivables_type) $val2 = $receivables_type['id'];
							else{
								$result = $receivables_type_model->createRecord([
									'name'     => $val2,
									'creatime' => time(),
									'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')
								]);
								if($result['status']) $val2 = $result['id'];
								else $val2 = null;
							}
						break;
						case 'pos_id': // 根据POS机名称反查ID 存在则选择不存在则创建
							$pos_machine = $pos_machine_model->findRecord(1, [
								'name'   => $val2,
								'status' => 'not deleted'
							]);
							if($pos_machine) $val2 = $pos_machine['id'];
							else{
								$result = $pos_machine_model->createRecord([
									'name'     => $val2,
									'creatime' => time(),
									'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')
								]);
								if($result['status']) $val2 = $result['id'];
								else $val2 = null;
							}
						break;
						case 'coupon_ids':
							$coupon_item = $coupon_item_model->findRecord(1, [
								'code'   => $val2,
								'status' => 'not deleted'
							]);
							if($coupon_item) $val2 = $coupon_item['id'];
							else $val2 = null;
						break;
						case 'source_type':
							if(stripos($val2, '会中') === false){
								$val2    = 0;
								$handler = true;
							}
							else{
								$val2    = 1;
								$handler = true;
							}
							if(!$handler){
								if(stripos($val2, '会前') === false){
									$val2    = 0;
									$handler = true;
								}
								else{
									$val2    = 0;
									$handler = true;
								}
							}
							if(!$handler){
								if(stripos($val2, '会后') === false){
									$val2 = 0;
									//$handler = true;
								}
								else{
									$val2 = 2;
									//$handler = true;
								}
							}
						break;
						case 'time':
							$val2 = strtotime($val2);
						break;
					}
					$save_data[$table_column[$key2]['name']] = $val2;
				}
				$save_data['mid']          = $mid;
				$save_data['order_number'] = $receivables_logic->makeOrderNumber();
				$save_data['creator']      = $creator;
				$save_data['creatime']     = time();
				$result                    = $model->createRecord($save_data);
				if($result['status']) $count++;
				else $error_msg .= "$client_name, ";
			}
			$error_msg = trim($error_msg, ', ');
			if($count == count($data)) return ['status' => true, 'message' => '收款数据全部导入成功'];
			elseif($count == 0) return ['status' => false, 'message' => '没有导入任何收款数据'];
			else return ['status' => true, 'message' => "部分客户收款数据未导入: <br>$error_msg"];
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
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model    = D('Core/Employee');
			$receivables_count = $receivables_model->findRecord(0, [
				'status' => 'not deleted',
				'mid'    => I('get.mid', 0, 'int')
			]);
			/* 分页设置 */
			$page_object = new Page(count($receivables_count), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show          = $page_object->show();
			$pos_machine_model  = D('Core/PosMachine');
			$mid                = I('get.mid', 0, 'int');
			$cid                = I('get.cid', 0, 'int');
			$keyword            = I('get.keyword', '');
			$receivables_result = $receivables_model->findRecord(2, [
				'_limit' => $page_object->firstRow.','.$page_object->listRows,
				'mid'    => $mid,
				'cid'    => $cid,
				'status' => 'not deleted'
			]);
			$new_list           = [];
			foreach($receivables_result as $k => $v){
				if($v['method']) $method_result = $pay_method_model->findRecord(1, [
					'id'     => $v['method'],
					'status' => 'not deleted'
				]);
				else $method_result['name'] = '';
				if($v['type']) $receivables_type_result = $receivables_type_model->findRecord(1, [
					'id'     => $v['type'],
					'status' => 'not deleted'
				]);
				else $receivables_type_result['name'] = '';
				if($v['pos_id']) $pos_machine_result = $pos_machine_model->findRecord(1, [
					'id'     => $v['pos_id'],
					'status' => 'not deleted'
				]);
				else $pos_machine_result['name'] = '';
				$meeting_result                         = $meeting_model->findMeeting(1, [
					'id'     => $receivables_result[$k]['mid'],
					'status' => 'not deleted'
				]);
				$client_result                          = $client_model->findClient(1, [
					'id'     => $receivables_result[$k]['cid'],
					'status' => 'not deleted'
				]);
				$employee_result                        = $employee_model->findEmployee(1, [
					'id'     => $v['payee_id'],
					'status' => 1
				]);
				$receivables_result[$k]['meeting_name'] = $meeting_result['name'];
				$receivables_result[$k]['client_name']  = $client_result['name'];
				$receivables_result[$k]['unit']         = $client_result['unit'];
				$receivables_result[$k]['method_name']  = $method_result['name'];
				$receivables_result[$k]['type_name']    = $receivables_type_result['name'];
				$receivables_result[$k]['pos_name']     = $pos_machine_result['name'];
				$receivables_result[$k]['payee_name']   = $employee_result['name'];
				$code_id                                = explode(',', $receivables_result[$k]['coupon_ids']);
				$coupon_item_code                       = '';
				foreach($code_id as $kk => $vv){
					$coupon_item_result = $coupon_item_model->findRecord(1, [
						'id'     => $vv,
						'status' => 'not deleted'
					]);
					$coupon_item_code .= $coupon_item_result['code'].',';  //点连接两个数据
				}
				$receivables_result[$k]['coupon_code'] = trim($coupon_item_code, ',');
				if(strpos($client_result['name'], $keyword) === false && $keyword != '') ;
				else $new_list[] = $receivables_result[$k];
			}

			return $new_list;
		}

		public function getClientReceivables($data, $keyword){
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			$new_list     = [];
			foreach($data as $k => $v){
				$client_result           = $client_model->findClient(1, ['id' => $v['cid']]);
				$data[$k]['client_name'] = $client_result['name'];
				$data[$k]['unit']        = $client_result['unit'];
				if(strpos($client_result['name'], $keyword) === false && $keyword != '') ;
				else $new_list[] = $data[$k];
			}

			return ($new_list);
		}

		public function setData($type, $data, $option = []){
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
							$coupon_item_result = $coupon_item_model->findRecord(1, ['id' => $v]);
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
				case 'manage:set_price':
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					/** @var \Core\Model\ReceivablesOptionModel $receivables_option_model */
					$receivables_option_model = D('Core/ReceivablesOption');
					$receivables_option_list  = $receivables_option_model->findRecord(2);
					$new_list                 = [];
					foreach($data as $key => $join){
						$receivables_list   = $receivables_model->findRecord(2, [
							'status' => 1,
							'mid'    => $option['mid'],
							'cid'    => $join['cid']
						]);
						$receivables_id_arr = [];
						foreach($receivables_list as $val) $receivables_id_arr[] = $val['id'];
						if($receivables_list){
							$price = 0;
							foreach($receivables_option_list as $val){
								if(in_array($val['rid'], $receivables_id_arr)) $price += $val['price'];
							}
							$url           = U('details', ['cid' => $join['cid'], 'mid' => $option['mid']]);
							$join['price'] = "<a href='$url' style='font-weight: bold; color: red'>$price</a>";
						}
						else{
							$price         = '未收款';
							$join['price'] = $price;
						}
						if($option['receivables'] === false) $new_list[] = $join;
						elseif($option['receivables'] === 1){
							if($receivables_list) $new_list[] = $join;
						}
						elseif($option['receivables'] === 0){
							if(!$receivables_list) $new_list[] = $join;
						}
					}

					return $new_list;
				break;
				//				case 'details:set_column':
				//					/** @var \Core\Model\EmployeeModel $employee_model */
				//					$employee_model = D('Core/Employee');
				//					/** @var \Core\Model\ClientModel $client_model */
				//					$client_model = D('Core/Client');
				//					/** @var \Core\Model\PayMethodModel $pay_method_model */
				//					$pay_method_model = D('Core/PayMethod');
				//					/** @var \Core\Model\PosMachineModel $pos_machine_model */
				//					$pos_machine_model = D('Core/PosMachine');
				//					/** @var \Core\Model\CouponItemModel $coupon_item_model */
				//					$coupon_item_model = D('Core/CouponItem');
				//					foreach($data as $key => $val){
				//						$client          = $client_model->findClient(1, ['status' => 1, 'id' => $val['cid']]);
				//						$payee           = $employee_model->findEmployee(1, [
				//							'status' => 1,
				//							'id'     => $val['payee_id']
				//						]);
				//						$pay_method      = $pay_method_model->findRecord(1, [
				//							'status' => 1,
				//							'id'     => $val['method']
				//						]);
				//						$pos_machine     = $pos_machine_model->findRecord(1, [
				//							'status' => 1,
				//							'id'     => $val['pos_id']
				//						]);
				//						$coupon_item_arr = explode(',', $val['coupon_ids']);
				//						$coupon_item_str = '';
				//						foreach($coupon_item_arr as $coupon){
				//							$coupon_item_record = $coupon_item_model->findRecord(1, [
				//								'id'     => $coupon,
				//								'status' => 'not deleted'
				//							]);
				//							$coupon_item_str .= "$coupon_item_record[code], ";
				//						}
				//						$coupon_item_str            = trim($coupon_item_str, ', ');
				//						$data[$key]['client_name']  = $client['name'];
				//						$data[$key]['unit']         = $client['unit'];
				//						$data[$key]['payee_name']   = $payee['name'];
				//						$data[$key]['method_name']  = $pay_method['name'];
				//						$data[$key]['pos_machine']  = $pos_machine['name'];
				//						$data[$key]['coupon_codes'] = $coupon_item_str;
				//					}
				//
				//					return $data;
				//				break;
				case 'details:set_column':
					/** @var \Core\Model\ReceivablesOptionModel $receivables_option_model */
					$receivables_option_model = D('Core/ReceivablesOption');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					/** @var \Core\Model\ClientModel $client_model */
					$client_model = D('Core/Client');
					/** @var \Core\Model\CouponItemModel $coupon_item_model */
					$coupon_item_model = D('Core/CouponItem');
					/** @var \Core\Model\CouponModel $coupon_model */
					$coupon_model = D('Core/Coupon');
					/** @var \Core\Model\PayMethodModel $pay_method_model */
					$pay_method_model = D('Core/PayMethod');
					/** @var \Core\Model\PosMachineModel $pos_machine_model */
					$pos_machine_model       = D('Core/PosMachine');
					$core_receivables_logic  = new \Core\Logic\ReceivablesLogic();
					$receivables_option_list = $receivables_option_model->findRecord(2);
					/** 收款信息和收款数据的映射表 */
					$index_list   = [];
					$index_prefix = 'index';
					foreach($receivables_option_list as $key => $val){
						if(!is_array($index_list["$index_prefix$val[rid]"])) $index_list["$index_prefix$val[rid]"] = [];
						$index_list["$index_prefix$val[rid]"][]            = $key;
						$pay_method                                        = $pay_method_model->findRecord(1, [
							'status' => 1,
							'id'     => $val['pay_method']
						]);
						$pos_machine                                       = $pos_machine_model->findRecord(1, [
							'status' => 1,
							'id'     => $val['pos_machine']
						]);
						$receivables_option_list[$key]['pay_method_name']  = $pay_method['name'] ? $pay_method['name'] : '未指定';
						$receivables_option_list[$key]['pos_machine_name'] = $pos_machine['name'] ? $pos_machine['name'] : '未使用';
						$receivables_option_list[$key]['source_type_name'] = $core_receivables_logic->getReceivablesSourceType($val['type']);
					}
					foreach($data as $key => $val){
						$price = 0;
						foreach($index_list["$index_prefix$val[id]"] as $option_id){
							$data[$key]['option'][] = $receivables_option_list[$option_id];
							$price += $receivables_option_list[$option_id]['price'];
						}
						$client          = $client_model->findClient(1, ['status' => 1, 'id' => $val['cid']]);
						$payee           = $employee_model->findEmployee(1, [
							'status' => 1,
							'id'     => $val['payee_id']
						]);
						$coupon_item_arr = explode(',', $val['coupon_ids']);
						$coupon_item_str = '';
						$coupon_name     = '';
						foreach($coupon_item_arr as $coupon){
							$coupon_item_record = $coupon_item_model->findRecord(1, [
								'id'     => $coupon,
								'status' => 'not deleted'
							]);
							if($coupon_item_record){
								$coupon_result = $coupon_model->findCoupon(1, [
									'id'     => $coupon_item_record['coupon_id'],
									'status' => 1
								]);
								$coupon_name   = $coupon_result['name'];
							}
							$coupon_item_str .= "$coupon_item_record[code], ";
						}
						$coupon_item_str                = trim($coupon_item_str, ', ');
						$data[$key]['client_name']      = $client['name'];
						$data[$key]['unit']             = $client['unit'];
						$data[$key]['payee_name']       = $payee['name'];
						$data[$key]['coupon_code']      = $coupon_item_str != '' ? "($coupon_item_str)" : '';
						$data[$key]['coupon_name']      = $coupon_name;
						$data[$key]['price']            = $price;
						$data[$key]['receivables_type'] = $core_receivables_logic->getReceivablesType($val['type']);
					}

					return $data;
				break;
				case 'manage:pagination':
					return array_splice($data, $option['pagination'][0], $option['pagination'][1]);
				break;
				default:
					return $data;
				break;
			}
		}
	}