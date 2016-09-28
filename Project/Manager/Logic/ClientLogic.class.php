<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 15:50
	 */
	namespace Manager\Logic;

	use Core\Logic\WxCorpLogic;
	use Quasar\StringPlus;

	class ClientLogic extends ManagerLogic{

		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'import_excel':
					$excel_logic = new ExcelLogic();
					$result      = $excel_logic->importClientData($_FILES);
					if(isset($result['data']['dbIndex'])){
						/** @var \Manager\Model\ClientModel $model */
						$model                    = D('Client');
						$table_head               = $model->getSelfColumn();
						$result['data']['dbHead'] = $table_head;
					}
					echo json_encode($result);

					return -1;
				break;
				case 'review':
					/** @var \Core\Model\ClientModel $model */
					$model        = D('Core/Client');
					$join_logic   = new JoinLogic();
					$wxcorp_logic = new WxCorpLogic();
					/** @var \Core\Model\WeixinIDModel $weixin_model */
					$weixin_model = D('Core/WeixinID');
					$client_id    = I('post.id', 0, 'int');
					$meeting_id   = I('post.mid', 0, 'int');
					C('TOKEN_ON', false);
					$result1 = $model->alterClient([$client_id], ['audit_status' => 1]);
					if(!$result1['status']){
						echo json_encode($result1);

						return -1;
					}
					$result2 = $join_logic->makeQRCode([
						['id' => $client_id]
					], [
						'mid' => $meeting_id
					]);
					if(!$result2['status']){
						echo json_encode($result2);

						return -1;
					}
					$weixin_record = $weixin_model->findRecord(1, ['otype' => 1, 'oid' => $client_id]);
					$result3       = $wxcorp_logic->sendMessage('news', [
						[
							'title'       => '会议信息提醒',
							'description' => '123',
							'url'         => 'www.baidu.com'
						]
					], ['user' => [$weixin_record['weixin_id']]]);
					print_r($result3);
					exit;

					return -1;
				break;
				case 'anti_review':
				break;
				case 'multi_review':
				break;
				case 'multi_anti_review':
				break;
				default:
					echo json_encode(['status' => false, 'message' => '参数错误']);

					return -1;
				break;
			}
		}

		public function createClientFromExcel($upload_record_id, $map){
			/** @var \Core\Model\UploadModel $upload_model */
			$upload_model  = D('Core/Upload');
			$excel_logic   = new ExcelLogic();
			$upload_record = $upload_model->findRecord(1, ['id' => $upload_record_id]);
			$excel_content = $excel_logic->readClientData($upload_record['save_path']);

			return $this->createClientFromExcelData($excel_content['body'], $map);
		}

		public function create($data){
			$str_obj = new StringPlus();
			/** @var \Core\Model\ClientModel $model */
			$model               = D('Core/Client');
			$data['status']      = $data['status'] == 1 ? 0 : (($data['status'] == 0) ? 1 : 1);
			$data['creatime']    = time();
			$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
			$data['password']    = $str_obj->makePassword($data['mobile'], '123456');
			$data['birthday']    = date('Y-m-d', strtotime($data['birthday']));

			return $model->createClient($data);
		}

		//		public function createClientFromExcelData($excel_data, $map){
		//			$str_obj = new StringPlus();
		//			/** @var \Manager\Model\ClientModel $model */
		//			$model        = D('Client');
		//			/** @var \Core\Model\ClientModel $core_model */
		//			$core_model = D('Core/Client');
		//			$table_column = $model->getColumn();
		//			$data_list    = [];
		//			foreach($excel_data as $key1 => $line){
		//				$tmp    = [];
		//				$mobile = '';
		//				$name   = '';
		//				foreach($line as $key2 => $val){
		//					$column_index = null;
		//					// 设定映射关系
		//					if(in_array($key2, $map['data'])){
		//						$map_index    = array_search($key2, $map['data']);
		//						$column_index = $map['column'][$map_index];
		//					}
		//					// 过滤数据
		//					switch(strtolower($table_column[$key2]['name'])){
		//						case 'birthday':
		//							$val = date('Y-m-d', strtotime($val));
		//						break;
		//						case 'gender':
		//							$val = $val == '男' ? 1 : ($val == '女' ? 2 : 0);
		//						break;
		//						case 'develop_consultant':
		//						case 'service_consultant':
		//							$val = 1;
		//						break;
		//						case 'mobile':
		//							$mobile = $val;
		//						break;
		//						case 'name':
		//							$name = $val;
		//						break;
		//					}
		//					// 指定特殊列的值
		//					$tmp['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
		//					$tmp['creatime']    = time();
		//					$tmp['password']    = $str_obj->makePassword('123456', $mobile);
		//					$tmp['pinyin_code'] = $str_obj->makePinyinCode($name);
		//					if($column_index === null) $tmp[$table_column[$key2]['name']] = $val; // 按顺序指定缺省值
		//					else{ // 设定映射字段
		//						$tmp[$table_column[$key2]['name']]         = $val;
		//						$tmp[$table_column[$column_index]['name']] = $val;
		//					}
		//				}
		//				// 判定是否存在该客户
		//				if($core_model->isExist($mobile, $name)) continue;
		//				else $data_list[] = $tmp;
		//			}
		//			if(!$data_list) return ['status' => false, 'message' => '重复数据无需导入'];
		//			return $core_model->createMultiClient($data_list);
		//		}
		public function createClientFromExcelData($excel_data, $map){
			$str_obj = new StringPlus();
			/** @var \Manager\Model\ClientModel $model */
			$model = D('Client');
			/** @var \Core\Model\ClientModel $core_model */
			$core_model = D('Core/Client');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model   = D('Core/Join');
			$table_column = $model->getColumn();
			$count        = 0;
			foreach($excel_data as $key1 => $line){
				$client_data       = [];
				$mobile            = '';
				$name              = '';
				$registration_time = '';
				$invitor           = '';
				foreach($line as $key2 => $val){
					$column_index = null;
					// 设定映射关系
					if(in_array($key2, $map['data'])){
						$map_index    = array_search($key2, $map['data']);
						$column_index = $map['column'][$map_index];
					}
					// 过滤数据
					switch(strtolower($table_column[$key2]['name'])){
						case 'birthday':
							$val = date('Y-m-d', strtotime($val));
						break;
						case 'gender':
							$val = $val == '男' ? 1 : ($val == '女' ? 2 : 0);
						break;
						case 'develop_consultant':
						case 'service_consultant':
							$val = 1; // todo
						break;
						case 'mobile':
							$mobile = $val;
						break;
						case 'name':
							$name = $val;
						break;
						case 'registration_time':
							$registration_time = date('Y-m-d', strtotime($val));
						break;
						case 'inviter':
							// todo
						break;
					}
					// 指定特殊列的值
					$client_data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$client_data['creatime']    = time();
					$client_data['password']    = $str_obj->makePassword('123456', $mobile);
					$client_data['pinyin_code'] = $str_obj->makePinyinCode($name);
					if($column_index === null){
						if(!in_array($table_column[$key2]['name'], ['registration_time'])) $client_data[$table_column[$key2]['name']] = $val;
					} // 按顺序指定缺省值
					else{ // 设定映射字段
						if(!in_array($table_column[$key2]['name'], ['registration_time'])) $client_data[$table_column[$key2]['name']] = $val;
						if(!in_array($table_column[$column_index]['name'], ['registration_time'])) $client_data[$table_column[$column_index]['name']] = $val;
					}
				}
				// 判定是否存在该客户
				$exist_client                   = $core_model->isExist($mobile, $name);
				$join_data['creator']           = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
				$join_data['creatime']          = time();
				$join_data['registration_time'] = $registration_time;
				$join_data['mid']               = I('get.mid', 0, 'int');
				if($exist_client){
					C('TOKEN_ON', false);
					$join_data['cid'] = $exist_client['id'];
					$join_result      = $join_model->createRecord($join_data);
					if($join_result['status']) $count++;
				}
				else{
					C('TOKEN_ON', false);
					$client_result = $core_model->createClient($client_data);
					if($client_result['status']){
						$join_data['cid'] = $client_result['id'];
						$join_result      = $join_model->createRecord($join_data);
						if($join_result['status']) $count++;
					}
				}
			}
			if($count === 0) return ['status' => false, 'message' => '无需导入任何数据'];
			if($count == count($excel_data)) return ['status' => true, 'message' => '全部导入成功'];
			else return ['status' => true, 'message' => '部分数据无需导入'];
		}

	}