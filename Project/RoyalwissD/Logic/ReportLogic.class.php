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
				case '':
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
				case 'fieldSetting':
					$result = [];
					$client_logic = new ClientLogic();
					foreach($data as $val){
						$val['is_custom'] = ($client_logic->isCustomColumn($val['form'])) ? 1 : 0;
						if($val['is_custom'] == 1 && $val['view'] == 1) $val['can_modified'] = 1;
						elseif($val['is_custom'] == 0 && $val['view'] == 1 && $val['table'] == 'client') $val['can_modified'] = 1;
						else $val['can_modified'] = 0;
						$result[] = $val;
					}
					return $result;
				break;
				case 'manage:set_data':
					$list = [];
					$get  = $data['urlParam'];
					$client_logic = new ClientLogic();
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					// 若指定了状态码的情况
					if(isset($get[ClientModel::CONTROL_COLUMN_PARAMETER['status']])) $status = $get[ClientModel::CONTROL_COLUMN_PARAMETER['status']];
					// 若指定了固定的ClientID
					if(isset($get[ClientModel::CONTROL_COLUMN_PARAMETER_SELF['clientID']])) $client_id = $get[ClientModel::CONTROL_COLUMN_PARAMETER_SELF['clientID']];
					// 若指定了签到状态码的情况
					if(isset($get[ClientModel::CONTROL_COLUMN_PARAMETER_SELF['signStatus']])) $sign_status = $get[ClientModel::CONTROL_COLUMN_PARAMETER_SELF['signStatus']];
					// 若指定了审核状态码的情况
					if(isset($get[ClientModel::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus']])) $review_status = $get[ClientModel::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus']];
					foreach($data['list'] as $index => $client){
						// 1、筛选数据
						if(isset($keyword)){
							//todo 获取筛选配置
							$found = 0;
							if($found == 0 && strpos($client['name'], $keyword) !== false) $found = 1;
							if($found == 0 && strpos($client['name_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0 && strpos($client['unit'], $keyword) !== false) $found = 1;
							if($found == 0 && strpos($client['unit_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0 && strpos($client['mobile'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						if(isset($client_id) && $client_id != $client['cid']) continue;
						if(isset($status) && $status != $client['status']) continue;
						if(isset($sign_status)){
							if($sign_status == 0 && in_array($client['sign_status'], [1])) continue;
							if($sign_status == 1 && in_array($client['sign_status'], [0, 2])) continue;
						}
						if(isset($review_status)){
							if($review_status == 0 && in_array($client['review_status'], [1])) continue;
							if($review_status == 1 && in_array($client['review_status'], [0, 2])) continue;
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
						if(strtotime($client['creatime'])>(time()-$client_logic::NEW_DATA_TIME)) $client['new_data'] = true;
						else $client['new_data'] = false;
						$list[] = $client;
					}

					return $list;
				break;
				default:
					return $data;
				break;
			}
		}
	}