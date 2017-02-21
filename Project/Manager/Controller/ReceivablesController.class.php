<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-10
	 * Time: 16:45
	 */
	namespace Manager\Controller;

	use Manager\Logic\ExcelLogic;
	use Manager\Logic\ReceivablesLogic;
	use Manager\Model\ClientModel;
	use Manager\Model\EmployeeModel;
	use Think\Page;

	class ReceivablesController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function create(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if(isset($result['__return__'])) $url = $result['__return__'];
					else $url = '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['RECEIVABLES.CREATE']){
				$getMeetingType = function ($meeting){
					$cur_time   = time();
					$start_time = strtotime($meeting['sign_start_time']);
					$end_time   = strtotime($meeting['sign_end_time']);
					if($cur_time<$start_time) return 0;
					elseif($cur_time>$end_time) return 2;
					else return 1;
				};
				/** @var \Core\Model\EmployeeModel $employee_model */
				$employee_model = D('Core/Employee');
				/** @var \Core\Model\ClientModel $client_model */
				$client_model = D('Core/Client');
				/** @var \Core\Model\PayMethodModel $pay_method_model */
				$pay_method_model = D('Core/PayMethod');
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model     = D('Core/Meeting');
				$receivables_logic = new \Core\Logic\ReceivablesLogic();
				$order_number      = $receivables_logic->makeOrderNumber();
				$meeting_result    = $meeting_model->findMeeting(1, ['id' => $this->meetingID]);
				$pay_method_result = $pay_method_model->findRecord(2, ['status' => '1']);
				/** @var \Core\Model\PosMachineModel $pos_machine_model */
				$pos_machine_model  = D('Core/PosMachine');
				$pos_machine_result = $pos_machine_model->findRecord(2, ['status' => '1', 'mid' => $this->meetingID]);
				$client_logic       = new ClientModel();
				$client             = $client_logic->getClientSelectList($this->meetingID, true);
				$employee_logic     = new EmployeeModel();
				$employee           = $employee_logic->getEmployeeSelectList();
				$client_result      = $client_model->findClient(1, ['id' => I('get.cid', 0, 'int')]);
				$employee_result    = $employee_model->findEmployee(1, ['id' => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')]);
				$this->assign('order_number', $order_number);
				$this->assign('meeting', $meeting_result);
				$this->assign('client', $client);//遍历当前会议的所有的参会人员
				$this->assign('employee', $employee);//遍历当前会议的所有的工作人员
				$this->assign('pos', $pos_machine_result);//遍历pos机
				$this->assign('pay', $pay_method_result); //遍例支付方式
				$this->assign('client_single', $client_result);
				$this->assign('employee_single', $employee_result);
				$this->assign('meeting_status', $getMeetingType($meeting_result));
				$this->display();
			}
			else $this->error('您没有添加收款的权限');
		}

		public function createUnit(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if(isset($result['__return__'])) $url = $result['__return__'];
					else $url = '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['RECEIVABLES.CREATE']){
				$getMeetingType = function ($meeting){
					$cur_time   = time();
					$start_time = strtotime($meeting['sign_start_time']);
					$end_time   = strtotime($meeting['sign_end_time']);
					if($cur_time<$start_time) return 0;
					elseif($cur_time>$end_time) return 2;
					else return 1;
				};
				/** @var \Core\Model\EmployeeModel $employee_model */
				$employee_model = D('Core/Employee');
				/** @var \Core\Model\ClientModel $client_model */
				$client_model = D('Core/Client');
				/** @var \Core\Model\PayMethodModel $pay_method_model */
				$pay_method_model = D('Core/PayMethod');
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model     = D('Core/Meeting');
				$receivables_logic = new \Core\Logic\ReceivablesLogic();
				$order_number      = $receivables_logic->makeOrderNumber();
				$meeting_result    = $meeting_model->findMeeting(1, ['id' => $this->meetingID]);
				$pay_method_result = $pay_method_model->findRecord(2, ['status' => '1']);
				/** @var \Core\Model\PosMachineModel $pos_machine_model */
				$pos_machine_model  = D('Core/PosMachine');
				$pos_machine_result = $pos_machine_model->findRecord(2, ['status' => '1', 'mid' => $this->meetingID]);
				$client_logic       = new ClientModel();
				$client             = $client_logic->getClientSelectList($this->meetingID, null, ' and type = \'会所\'');
				$employee_logic     = new EmployeeModel();
				$employee           = $employee_logic->getEmployeeSelectList();
				$client_result      = $client_model->findClientAll(1, ['id' => I('get.cid', 0, 'int')]);
				$employee_result    = $employee_model->findEmployee(1, ['id' => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')]);
				$this->assign('order_number', $order_number);
				$this->assign('meeting', $meeting_result);
				$this->assign('client', $client);//遍历当前会议的所有的参会人员
				$this->assign('employee', $employee);//遍历当前会议的所有的工作人员
				$this->assign('pos', $pos_machine_result);//遍历pos机
				$this->assign('pay', $pay_method_result); //遍例支付方式
				$this->assign('client_single', $client_result);
				$this->assign('employee_single', $employee_result);
				$this->assign('meeting_status', $getMeetingType($meeting_result));
				$this->display();
			}
			else $this->error('您没有添加收款的权限');
		}

		//		public function details(){
		//			$receivables_logic = new ReceivablesLogic();
		//			if(IS_POST){
		//				$type   = I('post.requestType');
		//				$result = $receivables_logic->handlerRequest($type);
		//				if($result['__ajax__']){
		//					unset($result['__ajax__']);
		//					echo json_encode($result);
		//				}
		//				else{
		//					unset($result['__ajax__']);
		//					if($result['status']) $this->success($result['message']);
		//					else $this->error($result['message'], '', 3);
		//				}
		//				exit;
		//			}
		//			if($this->permissionList['RECEIVABLES.VIEW']){
		//				/** @var \Core\Model\ReceivablesModel $receivables_model */
		//				$receivables_model = D('Core/Receivables');
		//				$option            = [];
		//				$keyword           = I('get.keyword', '');
		//				if(isset($_GET['cid'])) $option['cid'] = I('get.cid', 0, 'int');
		//				$receivables_total = $receivables_model->findRecord(0, array_merge([
		//					'mid'     => $this->meetingID,
		//					'status'  => 'not deleted',
		//					'keyword' => $keyword
		//				], $option));
		//				/* 分页设置 */
		//				$page_object = new Page(count($receivables_total), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
		//				\ThinkPHP\Quasar\Page\setTheme1($page_object);
		//				$receivables_list = $receivables_model->findRecord(2, array_merge([
		//					'status'  => 1,
		//					'mid'     => $this->meetingID,
		//					'keyword' => $keyword,
		//					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
		//					'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
		//				], $option));
		//				$receivables_list = $receivables_logic->setData('details:set_column', $receivables_list);
		//				$this->assign('list', $receivables_list);
		//				$this->display();
		//			}
		//			else $this->error('您没有查看收款记录的权限');
		//		}
		/**
		 * 改版后的收款详情页
		 */
		public function details(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['RECEIVABLES.VIEW']){
				$orderRecord = function ($list, $order_column, $order_method){
					$arr_sort     = [];
					$order_method = $order_method == 'desc' ? SORT_DESC : SORT_ASC;
					foreach($list as $key => $val){
						$arr_sort[$key] = iconv("UTF-8", "GBK", $val[$order_column]);
					}
					array_multisort($arr_sort, $order_method, $list);

					return $list;
				};
				$mergeRecord = function ($list){
					$new_list = $price_list = [];
					$flag_arr = [];
					foreach($list as $key => $val){
						$last_time = 0;
						$cid       = $val['client_id'];
						if(!isset($new_list[$val['client_id']])) $new_list[$cid] = ['list' => []];
						$new_list[$cid]['client_id']   = $val['client_id'];
						$new_list[$cid]['client_name'] = $val['client_name'];
						$new_list[$cid]['unit']        = $val['unit'];
						foreach($list as $key2 => $val2){
							$order_number = $val2['order_number'];
							if($val2['client_id'] == $cid && !in_array($key2, $flag_arr)){
								if(!isset($new_list[$cid]['list'][$order_number])) $new_list[$cid]['list'][$order_number] = ['list' => []];
								$coupon_name = '';
								$coupon_code = '';
								foreach($val2['coupon_list'] as $coupon){
									if($coupon['name'] != $coupon_name) $coupon_name = $coupon['name'];
									$coupon_code .= "$coupon[code], ";
								}
								$new_list[$cid]['list'][$order_number]['list'][]       = array_merge(['coupon' => $coupon_name.($coupon_code == ', ' ? '' : ' ('.trim($coupon_code, ', ').')')], $val2);
								$new_list[$cid]['list'][$order_number]['order_number'] = $order_number;
								$flag_arr[]                                            = $key2;
								if((int)$val['time']>=$last_time) $last_time = (int)$val['time'];
							}
						}
						if($last_time>=$new_list[$cid]['last_time']) $new_list[$cid]['last_time'] = $last_time;
					}
					$total_all = 0;
					$total_count = 0;
					foreach($new_list as $key => $val){
						$price1 = 0;
						$count1 = 0;
						$total1 = 0;
						foreach($val['list'] as $key2 => $val2){
							$price2 = 0;
							$count2 = 0;
							foreach($val2['list'] as $val3){
								$price2 += $val3['price'];
								$count2++;
								$total1++;
							}
							$new_list[$key]['list'][$key2]['price'] = $price2;
							$new_list[$key]['list'][$key2]['count'] = $count2;
							$price1 += $price2;
							$count1++;
						}
						$new_list[$key]['price'] = $price1;
						$new_list[$key]['count'] = $count1;
						$new_list[$key]['total'] = $total1;
						$total_all += $price1;
						$total_count += $total1;
					}

					return ['list' => $new_list, 'total' => $total_all, 'count'=>$total_count];
				};
				/** @var \Core\Model\ReceivablesModel $receivables_model */
				$receivables_model = D('Core/Receivables');
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model = D('Employee');
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				/** @var \Manager\Model\PosMachineModel $pos_machine_model */
				$pos_machine_model = D('PosMachine');
				/** @var \Manager\Model\PayMethodModel $pay_method_model */
				$pay_method_model = D('PayMethod');
				$option           = [];
				$keyword          = I('get.keyword', '');
				if(isset($_GET['cid'])) $option['cid'] = I('get.cid', 0, 'int');
				$receivables_total = $receivables_model->getData(array_merge([
					'mid'     => $this->meetingID,
					'status'  => 'not deleted',
					'keyword' => $keyword,
					'_order'  => 'creatime desc',
				], $option));
				$receivables_total = $mergeRecord($receivables_total);
				$all_price_total   = $receivables_total['total'];
				$all_count_total   = $receivables_total['count'];
				$receivables_total = $receivables_total['list'];
				$receivables_total = $orderRecord($receivables_total, I('get._orderColumn', 'last_time'), I('get._orderMethod', 'desc'));
				/* 分页设置 */
				$page_object = new Page(count($receivables_total), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show            = $page_object->show();
				$receivables_list     = array_slice($receivables_total, $page_object->firstRow, $page_object->listRows);
				$cur_page_price_total = 0;
				foreach($receivables_list as $val) $cur_page_price_total += $val['price'];
				$meeting_info     = $meeting_model->findMeeting(1, ['id' => $this->meetingID]);
				$payee_list       = $employee_model->getEmployeeNameSelectList();
				$pos_machine_list = $pos_machine_model->getPosMachineSelectList($this->meetingID);
				$pay_method_list  = $pay_method_model->getPayMethodSelectList();
				$this->assign('pos_machine', $pos_machine_list);
				$this->assign('pay_method', $pay_method_list);
				$this->assign('list', $receivables_list);
				$this->assign('page_show', $page_show);
				$this->assign('meeting', $meeting_info);
				$this->assign('employee_list', $payee_list);
				$this->assign('default_order_column', I('get._orderColumn', 'creatime'));
				$this->assign('default_order_method', I('get._orderMethod', 'desc'));
				$this->assign('cur_page_price_total', $cur_page_price_total);
				$this->assign('all_price_total', $all_price_total);
				$this->assign('all_count_total', $all_count_total);
				$this->display();
			}
			else $this->error('您没有查看收款记录的权限');
		}

		public function manage(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['RECEIVABLES.VIEW']){
				/** @var \Core\Model\JoinModel $join_model */
				$join_model            = D('Core/Join');
				$keyword               = I('get.keyword', '');
				$receivables_url_param = I('get.receivables', 0, 'int');
				$join_total            = $join_model->findRecord(2, [
					'status'  => 1,
					'mid'     => $this->meetingID,
					'keyword' => $keyword,
					'type'    => 'not employee'
				]);
				$total                 = count($join_total);
				if(isset($_GET['receivables'])){
					$join_total = $receivables_logic->setData('manage:set_price', $join_total, [
						'mid'         => $this->meetingID,
						'receivables' => $receivables_url_param
					]);
					/* 分页设置 */
					$page_object = new Page(count($join_total), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
					\ThinkPHP\Quasar\Page\setTheme1($page_object);
					$join_client = $receivables_logic->setData('manage:pagination', $join_total, [
						'pagination' => [
							$page_object->firstRow,
							$page_object->listRows
						]
					]);
				}
				else{
					/* 分页设置 */
					$page_object = new Page(count($join_total), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
					\ThinkPHP\Quasar\Page\setTheme1($page_object);
					$join_client = $join_model->findRecord(2, [
						'type'    => 'not employee',
						'status'  => 1,
						'mid'     => $this->meetingID,
						'keyword' => $keyword,
						'_limit'  => $page_object->firstRow.','.$page_object->listRows,
						//				'_order'  => I('get._column', 'main.creatime').' '.I('get._sort', 'desc'),
					]);
					$join_client = $receivables_logic->setData('manage:set_price', $join_client, [
						'mid'         => $this->meetingID,
						'receivables' => false
					]);
				}
				$statistics = [
					'receivables'     => count($receivables_logic->setData('manage:set_price', $join_total, [
						'mid'         => $this->meetingID,
						'receivables' => 1
					])),
					'not_receivables' => count($receivables_logic->setData('manage:set_price', $join_total, [
						'mid'         => $this->meetingID,
						'receivables' => 0
					])),
					'total'           => $total
				];
				$page_show  = $page_object->show();
				$this->assign('list', $join_client);
				$this->assign('statistics', $statistics);
				$this->assign('page_show', $page_show);//分页
				$this->display();
			}
			else $this->error('您没有查看收款记录的权限');
		}

		public function manageUnit(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['RECEIVABLES.VIEW']){
				/** @var \Core\Model\JoinModel $join_model */
				$join_model            = D('Core/Join');
				$keyword               = I('get.keyword', '');
				$receivables_url_param = I('get.receivables', 0, 'int');
				$join_total            = $join_model->findRecordAll(2, [
					'status'  => 1,
					'mid'     => $this->meetingID,
					'keyword' => $keyword,
					'type'    => '会所'
				]);
				$total                 = count($join_total);
				if(isset($_GET['receivables'])){
					$join_total = $receivables_logic->setData('manage:set_price', $join_total, [
						'mid'         => $this->meetingID,
						'receivables' => $receivables_url_param
					]);
					/* 分页设置 */
					$page_object = new Page(count($join_total), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
					\ThinkPHP\Quasar\Page\setTheme1($page_object);
					$join_client = $receivables_logic->setData('manage:pagination', $join_total, [
						'pagination' => [
							$page_object->firstRow,
							$page_object->listRows
						]
					]);
				}
				else{
					/* 分页设置 */
					$page_object = new Page(count($join_total), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
					\ThinkPHP\Quasar\Page\setTheme1($page_object);
					$join_client = $join_model->findRecordAll(2, [
						'type'    => '会所',
						'status'  => 1,
						'mid'     => $this->meetingID,
						'keyword' => $keyword,
						'_limit'  => $page_object->firstRow.','.$page_object->listRows,
						//				'_order'  => I('get._column', 'main.creatime').' '.I('get._sort', 'desc'),
					]);
					$join_client = $receivables_logic->setData('manage:set_price', $join_client, [
						'mid'         => $this->meetingID,
						'receivables' => false
					]);
				}
				$statistics = [
					'receivables'     => count($receivables_logic->setData('manage:set_price', $join_total, [
						'mid'         => $this->meetingID,
						'receivables' => 1
					])),
					'not_receivables' => count($receivables_logic->setData('manage:set_price', $join_total, [
						'mid'         => $this->meetingID,
						'receivables' => 0
					])),
					'total'           => $total
				];
				$page_show  = $page_object->show();
				$this->assign('list', $join_client);
				$this->assign('statistics', $statistics);
				$this->assign('page_show', $page_show);//分页
				$this->display();
			}
			else $this->error('您没有查看收款记录的权限');
		}

		public function payMethod(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['PAY_METHOD.VIEW']){
				/** @var \Core\Model\PayMethodModel $pay_method_model */
				$pay_method_model  = D('Core/PayMethod');
				$pay_method_result = $pay_method_model->findRecord(2, [
					'status'  => 'not deleted',
					'keyword' => I('get.keyword', '')
				]);
				$this->assign('pay', $pay_method_result);
				$this->display();
			}
			else $this->error('您没有查看支付方式的权限');
		}

		public function receivablesType(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['RECEIVABLES_TYPE.VIEW']){
				/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
				$receivables_type_model  = D('Core/ReceivablesType');
				$receivables_type_result = $receivables_type_model->findRecord(2, [
					'status'  => 'not deleted',
					'keyword' => I('get.keyword')
				]);
				$this->assign('type_info', $receivables_type_result);
				$this->display();
			}
			else $this->error('您没有查看收款类型的权限');
		}

		public function posMachine(){
			$pos_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $pos_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['POS_MACHINE.VIEW']){
				/** @var \Core\Model\PosMachineModel $pos_machine_model */
				$pos_machine_model  = D('Core/PosMachine');
				$pos_machine_result = $pos_machine_model->findRecord(2, [
					'status'  => 'not deleted',
					'mid'     => I('get.mid', 0, 'int'),
					'keyword' => I('get.keyword', '')
				]);
				$this->assign('pos', $pos_machine_result);
				$this->display();
			}
			else $this->error('您没有查看POS机的权限');
		}

		public function exportReceivablesData(){
			if($this->permissionList['RECEIVABLES.EXPORT-EXCEL']){
				/** @var \Manager\Model\ReceivablesModel $receivables_model */
				$receivables_model = D('Receivables');
				//				/** @var \Core\Model\ReceivablesModel $receivables_model */
				//				$receivables_model = D('Core/Receivables');
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model          = D('Core/Meeting');
				$excel_logic            = new ExcelLogic();
				$core_receivables_logic = new \Core\Logic\ReceivablesLogic();
				//				$logic              = new ReceivablesLogic();
				//				$receivables_result = $receivables_model->findRecord(2, [
				//					'mid'    => $this->meetingID,
				//					'status' => 'not deleted'
				//				]);
				//				$receivables_result = $logic->setData('excel_data', $receivables_result);
				$receivables_result = $receivables_model->getReceivablesDetail($this->meetingID);
				foreach($receivables_result as $key => $val){
					if($key != 0) $receivables_result[$key]['type'] = $core_receivables_logic->getReceivablesType($val['type']);
				}
				$meeting = $meeting_model->findMeeting(1, ['id' => $this->meetingID]);
				$excel_logic->exportCustomData($receivables_result, [
					'fileName'    => "[$meeting[name]]收款信息",
					'title'       => "$meeting[name]",
					'subject'     => '收款列表',
					'description' => '吉美会议系统导出收款数据',
					'company'     => '吉美集团',
					'hasHead'     => true
				]);
			}
			else $this->error('您没有导出EXCEL收款数据的权限');
		}

		public function detailsProject(){
			$getProjectList = function ($mid, $column, $method){
				$sql    = "select * from (select *,
(select SUM(workflow_receivables_option.price)
from workflow_coupon_item
join workflow_receivables on workflow_receivables.mid = workflow_coupon_item.mid
join workflow_receivables_option on workflow_receivables_option.rid = workflow_receivables.id
where workflow_coupon_item.coupon_id = project_list.id and workflow_receivables.mid = $mid and workflow_receivables.status = 1 and workflow_coupon_item.id like workflow_receivables.coupon_ids) price,

(select count(*)
from workflow_coupon_item
join workflow_receivables on workflow_receivables.mid = workflow_coupon_item.mid
join workflow_receivables_option on workflow_receivables_option.rid = workflow_receivables.id
where workflow_coupon_item.coupon_id = project_list.id and workflow_receivables.mid = $mid and workflow_receivables.status = 1 and workflow_coupon_item.id like workflow_receivables.coupon_ids) total

from (select
workflow_coupon.id, workflow_coupon.name
from workflow_coupon
where workflow_coupon.mid = $mid and workflow_coupon.status = 1 
 -- and workflow_coupon.id in (152,153,150,151,157,154,155)
) project_list
) tt
order by price desc";
				$result = M()->query($sql);

				return $result;
			};
			$getDetails     = function ($mid){
					$sql    = "
select * from (
select
					workflow_receivables.id rid,
					workflow_receivables_option.id roid,
					user_client.id client_id,
					user_client.name client_name,
					user_client.unit,
					user_client.pinyin_code,
					order_number,
					workflow_receivables.time,
					payee_id,
					(select name from user_employee where id = payee_id) payee_name,
					workflow_receivables.place,
					workflow_receivables_option.price,
					workflow_receivables.type,
					workflow_receivables.coupon_ids,
					pay_method pay_method_id,
					(select name from workflow_pay_method where id = pay_method) pay_method,
					pos_machine pos_machine_id,
					(select name from workflow_pos_machine where id = pos_machine) pos_machine,
					workflow_receivables_option.type source_type,
					workflow_receivables_option.comment,
					workflow_receivables.status,
					workflow_receivables.creatime,
					(select workflow_coupon_item.coupon_id from workflow_coupon_item where workflow_receivables.coupon_ids = workflow_coupon_item.id) coupon_id
					from workflow_receivables
join workflow_receivables_option on workflow_receivables_option.rid = workflow_receivables.id
join user_client on user_client.id = workflow_receivables.cid
where workflow_receivables.mid = $mid and workflow_receivables.status = 1) tt 
";
					$result = M()->query($sql);
				return $result;
			};
			$mergeResult = function($list, $detail){
				$total_price = 0;
				$total_count = 0;
				foreach($list as $key1=>$val1){
					$list[$key1]['list'] = [];
					$last_time           = 0;
					foreach($detail as $key2 => $val2){
						if($val1['id'] === $val2['coupon_id']){
							$list[$key1]['list'][] = $val2;
							if($val2['time']>=$last_time) $last_time = $val2['time'];
						}
					}
					$list[$key1]['last_time'] = $last_time;
					$total_price += $val1['price'];
					$total_count += $val1['total'];
				}
				return ['list'=>$list, 'price'=>$total_price, 'total'=>$total_count];
			};
			$orderRecord = function ($list, $order_column, $order_method){
				$arr_sort     = [];
				$order_method = $order_method == 'desc' ? SORT_DESC : SORT_ASC;
				foreach($list as $key => $val){
					$arr_sort[$key] = iconv("UTF-8", "GBK", $val[$order_column]);
				}
				array_multisort($arr_sort, $order_method, $list);

				return $list;
			};
			$mid = I('get.mid', 0, 'int');
			$column = I('get._orderColumn', 'name');
			$method = I('get._orderMethod', 'asc');
			$list = $getProjectList($mid);
			$detail = $getDetails($mid);
			$result = $mergeResult($list, $detail);
			$all_price = $result['price'];
			$all_total = $result['total'];
			$result = $result['list'];
			$result = $orderRecord($result, $column, $method);
			$this->assign('default_order_column', $column);
			$this->assign('default_order_method', $method);
			$this->assign('list', $result);
			$this->assign('price', $all_price);
			$this->assign('total', $all_total);
			$this->display();
		}

		public function exportReceivablesDataTemplate(){
			if($this->permissionList['RECEIVABLES.DOWNLOAD-IMPORT-EXCEL-TEMPLATE']){
				/** @var \Manager\Model\ReceivablesModel $receivables_model */
				$receivables_model = D('Receivables');
				$header            = $receivables_model->getColumn(true, true);
				$excel_logic       = new ExcelLogic();
				$excel_logic->exportCustomData($header, [
					'fileName'    => '导入收款数据模板',
					'title'       => '导入收款数据模板',
					'subject'     => '导入收款数据模板',
					'description' => '吉美会议系统导入收款数据模板',
					'company'     => '吉美集团',
					'hasHead'     => true
				]);
			}
			else $this->error('您没有下载导入模板的权限');
		}

		public function printClient(){
		}

		public function printDetail(){
		}
	}