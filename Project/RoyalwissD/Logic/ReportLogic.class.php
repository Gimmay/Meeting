<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-7
	 * Time: 16:04
	 */
	namespace RoyalwissD\Logic;

	use CMS\Controller\CMS;
	use CMS\Logic\Session;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use RoyalwissD\Model\AttendeeModel;
	use RoyalwissD\Model\ClientModel;
	use RoyalwissD\Model\ReceivablesDetailModel;
	use RoyalwissD\Model\ReceivablesOrderModel;
	use RoyalwissD\Model\UnitModel;

	class ReportLogic extends RoyalwissDLogic{
		/**
		 * 处理POST/GET请求
		 *
		 * @param string $type 请求类型
		 * @param array  $opt  相关参数
		 *
		 * @return mixed
		 */
		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'reset_and_order_column':
					/** @var \RoyalwissD\Model\ReportColumnControlModel $report_column_control_model */
					$report_column_control_model = D('RoyalwissD/ReportColumnControl');
					$meeting_id                  = I('get.mid', 0, 'int');
					$post                        = I('post.');
					// 锁表
					$report_column_control_model->lock('read');
					$report_column_control_model->lock('write');
					// 删除旧数据
					$report_column_control_model->where([
						'mid'    => $meeting_id,
						'action' => $report_column_control_model::ACTION_READ,
						'type'   => $report_column_control_model::TYPE_CLIENT
					])->delete();
					// 写入数据
					$data = [];
					foreach($post['code'] as $key => $val){
						$data[] = [
							'code'     => $post['code'][$key],
							'name'     => $post['name'][$key],
							'form'     => $post['form'][$key],
							'view'     => $post['view'][$key],
							'must'     => $post['must'][$key],
							'table'    => $post['table'][$key],
							'mid'      => $meeting_id,
							'action'   => $report_column_control_model::ACTION_READ,
							'creator'  => Session::getCurrentUser(),
							'creatime' => Time::getCurrentTime(),
						];
					}
					$result = $report_column_control_model->addAll($data, [
						'mid'    => $meeting_id,
						'action' => $report_column_control_model::ACTION_READ
					], true);
					// 解锁
					$report_column_control_model->unlock();

					return $result ? [
						'status'   => true,
						'message'  => '设置成功',
						'__ajax__' => true
					] : [
						'status'   => false,
						'message'  => '设置失败',
						'__ajax__' => true
					];
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true];
				break;
			}
		}

		/**
		 * 设定额外数据
		 *
		 * @param string $type 操作类型
		 * @param mixed  $data 处理数据
		 *
		 * @return mixed
		 */
		public function setData($type, $data){
			switch($type){
				case 'column_setting:search':
					$result = '';
					foreach($data as $val){
						if($val['search'] == 1) $result .= "$val[name] / ";
					}

					return trim($result, ' / ');
				break;
				case 'client:set_data':
					$list = [];
					$get  = $data['urlParam'];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					// 若指定了签到状态码的情况
					if(isset($get['isSigned'])) $is_signed = $get['isSigned'];
					// 若指定了性别的情况
					if(isset($get['gender'])) $gender = $get['gender'];
					// 若指定了新老客的情况
					if(isset($get['isNew'])) $is_new = $get['isNew'];
					// 若指定了团队的情况
					if(isset($get['team'])) $team = $get['team'];
					// 若指定了客户类型的情况
					if(isset($get['type'])) $client_type = $get['type'];
					// 若指定了区域的情况
					if(isset($get['unitArea'])) $unit_area = $get['unitArea'];
					// 若指定了是否新店的情况
					if(isset($get['unitIsNew'])) $unit_is_new = $get['unitIsNew'];
					foreach($data['list'] as $index => $client){
						// 1、筛选数据
						if(isset($keyword)){
							/** @var \RoyalwissD\Model\ReportColumnControlModel $report_column_control_model */
							$report_column_control_model = D('RoyalwissD/ReportColumnControl');
							$search_list                 = $report_column_control_model->getClientSearchColumn($get['mid'], true);
							$found                       = 0;
							foreach($search_list as $value){
								if($found == 0 && stripos($client[$value['form']], $keyword) !== false) $found = 1;
							}
							if(count($search_list) == 0) $found = 1;
							if($found == 0) continue;
						}
						if(isset($is_signed)){
							if($is_signed == 0 && in_array($client['sign_status'], [1])) continue;
							if($is_signed == 1 && in_array($client['sign_status'], [0, 2])) continue;
						}
						if(isset($gender)){
							if($gender != $client['gender']) continue;
						}
						if(isset($is_new)){
							if($is_new != $client['is_new']) continue;
						}
						if(isset($team)){
							if($team != $client['team']) continue;
						}
						if(isset($client_type)){
							if($client_type != $client['type']) continue;
						}
						if(isset($unit_area)){
							if($unit_area != $client['unit_area']) continue;
						}
						if(isset($unit_is_new)){
							if($unit_is_new != $client['unit_is_new']) continue;
						}
						// 2、映射替换
						$client['register_type']      = AttendeeModel::REGISTER_TYPE[$client['register_type']];
						$client['review_status_code'] = $client['review_status'];
						$client['review_status']      = AttendeeModel::REVIEW_STATUS[$client['review_status']];
						$client['sign_status_code']   = $client['sign_status'];
						$client['sign_status']        = AttendeeModel::SIGN_STATUS[$client['sign_status']];
						$client['sign_type']          = AttendeeModel::SIGN_TYPE[$client['sign_type']];
						$client['print_status_code']  = $client['print_status'];
						$client['print_status']       = AttendeeModel::PRINT_STATUS[$client['print_status']];
						$client['gift_status_code']   = $client['gift_status'];
						$client['gift_status']        = AttendeeModel::GIFT_STATUS[$client['gift_status']];
						$client['status_code']        = $client['status'];
						$client['status']             = GeneralModel::STATUS[$client['status']];
						$client['gender_code']        = $client['gender'];
						$client['gender']             = ClientModel::GENDER[$client['gender']];
						$client['is_new_code']        = $client['is_new'];
						$client['is_new']             = ClientModel::IS_NEW[$client['is_new']];
						$client['unit_is_new_code']   = $client['unit_is_new'];
						$client['unit_is_new']        = UnitModel::IS_NEW[$client['unit_is_new']];
						$list[]                       = $client;
					}

					return $list;
				break;
				case 'client:statistics':
					$report_template = [
						'total'       => 0,
						'signed'      => 0,
						'consumption' => 0,
						'receivables' => 0
					];
					//
					$statistics = [
						'team'      => [
						],
						'type'      => [
						],
						'unitIsNew' => [
							0 => $report_template,
							1 => $report_template
						],
						'isNew'     => [
							0 => $report_template,
							1 => $report_template
						],
						'area'      => [],
						'total'     => $report_template,
					];
					foreach($data as $client){
						if(!isset($statistics['area'][$client['unit_area']])) $statistics['area'][$client['unit_area']] = $report_template;
						if(!isset($statistics['team'][$client['team']])) $statistics['team'][$client['team']] = $report_template;
						if(in_array($client['type'], ClientModel::TYPE)){
							if(!isset($statistics['type'][$client['type']])) $statistics['type'][$client['type']] = $report_template;
							// 新老客判定
							if($client['is_new_code'] == 1){
								$statistics['isNew'][1]['total']++;
								$statistics['isNew'][1]['consumption'] += $client['consumption'];
								$statistics['isNew'][1]['receivables'] += $client['receivables'];
								if($client['sign_status_code'] == 1) $statistics['isNew'][1]['signed']++;
							}
							if($client['is_new_code'] == 0){
								$statistics['isNew'][0]['total']++;
								$statistics['isNew'][0]['consumption'] += $client['consumption'];
								$statistics['isNew'][0]['receivables'] += $client['receivables'];
								if($client['sign_status_code'] == 1) $statistics['isNew'][0]['signed']++;
							}
							// 新老店判定
							if($client['unit_is_new_code'] == 1){
								$statistics['unitIsNew'][1]['total']++;
								$statistics['unitIsNew'][1]['consumption'] += $client['consumption'];
								$statistics['unitIsNew'][1]['receivables'] += $client['receivables'];
								if($client['sign_status_code'] == 1) $statistics['unitIsNew'][1]['signed']++;
							}
							if($client['unit_is_new_code'] == 0){
								$statistics['unitIsNew'][0]['total']++;
								$statistics['unitIsNew'][0]['consumption'] += $client['consumption'];
								$statistics['unitIsNew'][0]['receivables'] += $client['receivables'];
								if($client['sign_status_code'] == 1) $statistics['unitIsNew'][0]['signed']++;
							}
							// 区域判定
							$statistics['area'][$client['unit_area']]['total']++;
							$statistics['area'][$client['unit_area']]['consumption'] += $client['consumption'];
							$statistics['area'][$client['unit_area']]['receivables'] += $client['receivables'];
							if($client['sign_status_code'] == 1) $statistics['area'][$client['unit_area']]['signed']++;
							// 团队判定
							$statistics['team'][$client['team']]['total']++;
							$statistics['team'][$client['team']]['consumption'] += $client['consumption'];
							$statistics['team'][$client['team']]['receivables'] += $client['receivables'];
							if($client['sign_status_code'] == 1) $statistics['team'][$client['team']]['signed']++;
							// 总判定
							$statistics['total']['total']++;
							$statistics['total']['consumption'] += $client['consumption'];
							$statistics['total']['receivables'] += $client['receivables'];
							if($client['sign_status_code'] == 1) $statistics['total']['signed']++;
							// 客户性质判定
							$statistics['type'][$client['type']]['total']++;
							$statistics['type'][$client['type']]['consumption'] += $client['consumption'];
							$statistics['type'][$client['type']]['receivables'] += $client['receivables'];
							if($client['sign_status_code'] == 1) $statistics['type'][$client['type']]['signed']++;
						}
						else{
							if(!isset($statistics['type']["×$client[type]"])) $statistics['type']["×$client[type]"] = $report_template;
							// 客户性质判定
							$statistics['type']["×$client[type]"]['total']++;
							if($client['sign_status_code'] == 1) $statistics['type']["×$client[type]"]['signed']++;
						}
					}

					return $statistics;
				break;
				case 'unit:set_data':
					$list = [];
					$get  = $data['urlParam'];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					// 若指定了签到状态码的情况
					if(isset($get['isSigned'])) $is_signed = $get['isSigned'];
					// 若指定了新老店的情况
					if(isset($get['isNew'])) $is_new = $get['isNew'];
					// 若指定了区域的情况
					if(isset($get['area'])) $area = $get['area'];
					foreach($data['list'] as $index => $unit){
						// 1、筛选数据
						if(isset($keyword)){
							$found = 0;
							if($found == 0 && stripos($unit['name'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($unit['name_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						if(isset($is_signed)){
							if($is_signed != $unit['is_signed']) continue;
						}
						if(isset($is_new)){
							if($is_new != $unit['is_new']) continue;
						}
						if(isset($area)){
							if($area != $unit['unit_area']) continue;
						}
						// 2、映射替换
						$unit['is_signed_code'] = $unit['is_signed'];
						$unit['is_signed']      = UnitModel::IS_SIGNED[$unit['is_signed']];
						$unit['status_code']    = $unit['status'];
						$unit['status']         = GeneralModel::STATUS[$unit['status']];
						$unit['is_new_code']    = $unit['is_new'];
						$unit['is_new']         = UnitModel::IS_NEW[$unit['is_new']];
						$list[]                 = $unit;
					}

					return $list;
				break;
				case 'unit:statistics':
					$report_template = [
						'total'  => 0,
						'signed' => 0
					];
					//
					$statistics = [
						'isNew' => [
							0 => $report_template,
							1 => $report_template
						],
						'area'  => [],
						'total' => $report_template,
					];
					foreach($data as $unit){
						if(!isset($statistics['area'][$unit['area']])) $statistics['area'][$unit['area']] = $report_template;
						// 新老店判定
						if($unit['is_new_code'] == 1){
							$statistics['isNew'][1]['total']++;
							if($unit['is_signed_code'] == 1) $statistics['isNew'][1]['signed']++;
						}
						if($unit['is_new_code'] == 0){
							$statistics['isNew'][0]['total']++;
							if($unit['is_signed_code'] == 1) $statistics['isNew'][0]['signed']++;
						}
						// 区域判定
						$statistics['area'][$unit['area']]['total']++;
						if($unit['is_signed_code'] == 1) $statistics['area'][$unit['area']]['signed']++;
						// 总判定
						$statistics['total']['total']++;
						if($unit['is_signed_code'] == 1) $statistics['total']['signed']++;
					}

					return $statistics;
				break;
				case 'receivables:set_data':
					$result = [];
					// 合并单据号的映射变量
					$order_reflect = [
						'orderID' => [], // 已存在的单据号ID数组
						'index'   => [] // 映射表
					];
					$get           = $data['urlParam'];
					$original_data = $data = $data['list'];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					if(isset($get['projectType'])) $project_type = $get['projectType'];
					if(isset($get['project'])) $project = $get['project'];
					if(isset($get['payMethod'])) $pay_method = $get['payMethod'];
					if(isset($get['posMachine'])) $pos_machine = $get['posMachine'];
					if(isset($get['source'])) $source = $get['source'];
					// 1、合并单据号
					//					for($i = 0, $key = 0; $i<count($data); $i++){
					//						// 1、筛选数据
					//						if(isset($keyword)){
					//							// todo 获取筛选配置
					//							$found = 0;
					//							if($found == 0 && stripos($data[$i]['client'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['client_pinyin'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['unit'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['unit_pinyin'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['project'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['project_pinyin'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['project_type'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['project_type_pinyin'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['payee'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['payee_pinyin'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['pay_method'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['pay_method_pinyin'], $keyword) !== false) $found = 1;
					//							if($found == 0 && stripos($data[$i]['order_number'], $keyword) !== false) $found = 1;
					//							if($found == 0) continue;
					//						}
					//						if(isset($project_type) && $data[$i]['project_type_code'] != $project_type) continue;
					//						if(isset($project) && $data[$i]['project_code'] != $project) continue;
					//						if(isset($pay_method) && $data[$i]['pay_method_code'] != $pay_method) continue;
					//						if(isset($pos_machine) && $data[$i]['pos_machine_code'] != $pos_machine) continue;
					//						if(isset($source) && $data[$i]['source'] != $source) continue;
					//						// 合并
					//						$order_id   = $data[$i]['id'];
					//						$project_id = $data[$i]['project_code'];
					//						if(!in_array($data[$i]['id'], $order_reflect['orderID'])){
					//							$order_reflect['orderID'][]                        = $order_id;
					//							$order_reflect['index'][$order_id]['i']            = $key++;
					//							$order_reflect['index'][$order_id]['project']      = [];
					//							$order_reflect['index'][$order_id]['projectIndex'] = [];
					//						}
					//						$index = $order_reflect['index'][$order_id]['i'];
					//						// 统计同一个单据下的项目数
					//						if(!in_array($project_id, $order_reflect['index'][$order_id]['project'])){
					//							$order_reflect['index'][$order_id]['project'][] = $project_id;
					//							if(!isset($result[$index]['projectCount'])){
					//								$result[$index]['projectCount'] = 0;
					//							}
					//							// 如果是新的项目 则自增统计数
					//							$result[$index]['projectCount']++;
					//							// 初始化项目列表的合并数
					//							$data[$i]['_merge_column'] = 1;
					//							// 构建项目合并判定的回溯映射表 可由项目ID映射到data列表的数字下标
					//							$order_reflect['index'][$order_id]['projectIndex'][$project_id] = $i;
					//						}
					//						else{
					//							$merge_first_project_index = $order_reflect['index'][$order_id]['projectIndex'][$project_id];
					//							$data[$merge_first_project_index]['_merge_column']++;
					//						}
					//						// 统计项目相同合并计数
					//						// 统计同一个收据下的总金额
					//						if(!isset($result[$index]['price'])) $result[$index]['price'] = 0;
					//						$result[$index]['price'] += $data[$i]['price'];
					//						$data[$i]['source_code']              = $data[$i]['source'];
					//						$data[$i]['source']                   = ReceivablesDetailModel::RECEIVABLES_SOURCE[$data[$i]['source']];
					//						$result[$index]['list'][]             = &$data[$i];
					//						$result[$index]['id']                 = $data[$i]['id'];
					//						$result[$index]['order_number']       = $data[$i]['order_number'];
					//						$result[$index]['client_name']        = $data[$i]['client'];
					//						$result[$index]['client_id'] = $data[$i]['cid'];
					//						$result[$index]['payee']              = $data[$i]['payee'];
					//						$result[$index]['place']              = $data[$i]['place'];
					//						$result[$index]['time']               = $data[$i]['time'];
					//						$result[$index]['unit']               = $data[$i]['unit'];
					//						$result[$index]['review_status_code'] = $data[$i]['review_status'];
					//						$result[$index]['review_status']      = ReceivablesOrderModel::REVIEW_STATUS[$data[$i]['review_status']];
					//						$result[$index]['status_code']        = $data[$i]['status'];
					//						$result[$index]['status']             = GeneralModel::STATUS[$data[$i]['status']];
					//						$result[$index]['creatime']           = $data[$i]['creatime'];
					//						$result[$index]['creator']            = $data[$i]['creator'];
					//					}
					// 2、合并客户
					$result2 = [];
					// 合并单据号的映射变量
					$client_reflect = [
						'clientID' => [], // 已存在的客户ID数组
						'index'    => [] // 映射表
					];
					for($i = 0, $key = 0; $i<count($original_data); $i++){
						// 1、筛选数据
						if(isset($keyword)){
							// todo 获取筛选配置
							$found = 0;
							if($found == 0 && stripos($original_data[$i]['client'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['client_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['unit'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['unit_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['project'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['project_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['project_type'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['project_type_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['payee'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['payee_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['pay_method'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['pay_method_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($original_data[$i]['order_number'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						if(isset($project_type) && $original_data[$i]['project_type_code'] != $project_type) continue;
						if(isset($project) && $original_data[$i]['project_code'] != $project) continue;
						if(isset($pay_method) && $original_data[$i]['pay_method_code'] != $pay_method) continue;
						if(isset($pos_machine) && $original_data[$i]['pos_machine_code'] != $pos_machine) continue;
						if(isset($source) && $original_data[$i]['source'] != $source) continue;
						$client_id = $original_data[$i]['cid'];
						if(!in_array($client_id, $client_reflect['clientID'])){
							$client_reflect['clientID'][]        = $client_id;
							$client_reflect['index'][$client_id] = $key++;
						}
						$index                          = $client_reflect['index'][$client_id];
						$result2[$index]['list'][]      = $original_data[$i];
						$result2[$index]['client_id']   = $original_data[$i]['cid'];
						$result2[$index]['client_name'] = $original_data[$i]['client'];
						$result2[$index]['unit']        = $original_data[$i]['unit'];
						$result2[$index]['price'] += $original_data[$i]['price'];
						if(!in_array($original_data[$i]['project_type_code'], $result2[$index]['project_type_code'])){
							$result2[$index]['project_type_code'][] = $original_data[$i]['project_type_code'];
							$result2[$index]['project_type'][]      = $original_data[$i]['project_type'];
						}
						if(!in_array($original_data[$i]['project_code'], $result2[$index]['project_code'])){
							$result2[$index]['project_code'][] = $original_data[$i]['project_code'];
							$result2[$index]['project'][]      = $original_data[$i]['project'];
						}
						if(!in_array($original_data[$i]['pay_method_code'], $result2[$index]['pay_method_code'])){
							$result2[$index]['pay_method_code'][] = $original_data[$i]['pay_method_code'];
							$result2[$index]['pay_method'][]      = $original_data[$i]['pay_method'];
						}
						if(!in_array($original_data[$i]['pos_machine_code'], $result2[$index]['pos_machine_code']) && $original_data[$i]['pos_machine_code'] != 0){
							$result2[$index]['pos_machine_code'][] = $original_data[$i]['pos_machine_code'];
							$result2[$index]['pos_machine'][]      = $original_data[$i]['pos_machine'];
						}
					}

					return $result2;
				break;
				case 'receivables:statistics':
					$report_template = [
						'price'       => 0,
						'client'      => 0,
						'_clientList' => []
					];
					$statistics      = [
						'projectType' => [],
						'project'     => [],
						'payMethod'   => [],
						'total'       => $report_template
					];
					foreach($data as $order){
						foreach($order['list'] as $detail){
							if(!isset($statistics['projectType'][$detail['project_type']])) $statistics['projectType'][$detail['project_type']] = $report_template;
							if(!isset($statistics['project'][$detail['project']])) $statistics['project'][$detail['project']] = $report_template;
							if(!isset($statistics['payMethod'][$detail['pay_method']])) $statistics['payMethod'][$detail['pay_method']] = $report_template;
							// 项目类型
							$statistics['projectType'][$detail['project_type']]['price'] += $detail['price'];
							if(!in_array($detail['cid'], $statistics['projectType'][$detail['project_type']]['_clientList'])){
								$statistics['projectType'][$detail['project_type']]['_clientList'][] = $detail['cid'];
								$statistics['projectType'][$detail['project_type']]['client']++;
							}
							// 项目
							$statistics['project'][$detail['project']]['price'] += $detail['price'];
							if(!in_array($detail['cid'], $statistics['project'][$detail['project']]['_clientList'])){
								$statistics['project'][$detail['project']]['_clientList'][] = $detail['cid'];
								$statistics['project'][$detail['project']]['client']++;
							}
							// 支付方式
							$statistics['payMethod'][$detail['pay_method']]['price'] += $detail['price'];
							if(!in_array($detail['cid'], $statistics['payMethod'][$detail['pay_method']]['_clientList'])){
								$statistics['payMethod'][$detail['pay_method']]['_clientList'][] = $detail['cid'];
								$statistics['payMethod'][$detail['pay_method']]['client']++;
							}
							// 汇总
							$statistics['total']['price'] += $detail['price'];
							if(!in_array($detail['cid'], $statistics['total']['_clientList'])){
								$statistics['total']['_clientList'][] = $detail['cid'];
								$statistics['total']['client']++;
							}
						}
					}

					return $statistics;
				break;
				default:
					return $data;
				break;
			}
		}
	}