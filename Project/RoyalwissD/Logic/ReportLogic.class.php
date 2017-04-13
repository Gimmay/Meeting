<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-7
	 * Time: 16:04
	 */
	namespace RoyalwissD\Logic;

	use CMS\Controller\CMS;
	use General\Model\GeneralModel;
	use RoyalwissD\Model\AttendeeModel;
	use RoyalwissD\Model\ClientModel;
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
					$list         = [];
					$get          = $data['urlParam'];
					$client_logic = new ClientLogic();
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
								if($found == 0 && strpos($client[$value['form']], $keyword) !== false) $found = 1;
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
						if(strtotime($client['creatime'])>(time()-$client_logic::NEW_DATA_TIME)) $client['new_data'] = true;
						else $client['new_data'] = false;
						$list[] = $client;
					}

					return $list;
				break;
				case 'client:statistics':
					$report_template = [
						'total'  => 0,
						'signed' => 0
					];
					//
					$statistics = [
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
						// 新老客判定
						if($client['is_new_code'] == 1){
							$statistics['isNew'][1]['total']++;
							if($client['sign_status_code'] == 1) $statistics['isNew'][1]['signed']++;
						}
						if($client['is_new_code'] == 0){
							$statistics['isNew'][0]['total']++;
							if($client['sign_status_code'] == 1) $statistics['isNew'][0]['signed']++;
						}
						// 新老店判定
						if($client['unit_is_new_code'] == 1){
							$statistics['unitIsNew'][1]['total']++;
							if($client['sign_status_code'] == 1) $statistics['unitIsNew'][1]['signed']++;
						}
						if($client['unit_is_new_code'] == 0){
							$statistics['unitIsNew'][0]['total']++;
							if($client['sign_status_code'] == 1) $statistics['unitIsNew'][0]['signed']++;
						}
						// 区域判定
						$statistics['area'][$client['unit_area']]['total']++;
						if($client['sign_status_code'] == 1) $statistics['area'][$client['unit_area']]['signed']++;
						// 总判定
						$statistics['total']['total']++;
						if($client['sign_status_code'] == 1) $statistics['total']['signed']++;
					}

					return $statistics;
				break;
				default:
					return $data;
				break;
			}
		}
	}