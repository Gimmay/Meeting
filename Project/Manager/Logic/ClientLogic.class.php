<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 15:50
	 */
	namespace Manager\Logic;

	use Quasar\StringPlus;

	class ClientLogic extends ManagerLogic{

		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'import_excel':
					echo 123;
					exit;
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

		public function createClientFromExcelData($excel_data, $map){
			$str_obj = new StringPlus();
			/** @var \Manager\Model\ClientModel $model */
			$model        = D('Client');
			/** @var \Core\Model\ClientModel $core_model */
			$core_model = D('Core/Client');
			$table_column = $model->getColumn();
			$data_list    = [];
			foreach($excel_data as $key1 => $line){
				$tmp    = [];
				$mobile = '';
				$name   = '';
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
							$val = 1;
						break;
						case 'mobile':
							$mobile = $val;
						break;
						case 'name':
							$name = $val;
						break;
					}
					// 指定特殊列的值
					$tmp['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$tmp['creatime']    = time();
					$tmp['password']    = $str_obj->makePassword('123456', $mobile);
					$tmp['pinyin_code'] = $str_obj->makePinyinCode($name);
					if($column_index === null) $tmp[$table_column[$key2]['name']] = $val; // 按顺序指定缺省值
					else{ // 设定映射字段
						$tmp[$table_column[$key2]['name']]         = $val;
						$tmp[$table_column[$column_index]['name']] = $val;
					}
				}
				// 判定是否存在该客户
				if($core_model->isExist($mobile, $name)) continue;
				else $data_list[] = $tmp;
				$data_list[] = $tmp;
			}
			if(!$data_list) return ['status' => false, 'message' => '重复数据无需导入'];
			return $core_model->createMultiClient($data_list);
		}

	}