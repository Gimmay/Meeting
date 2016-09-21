<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-19
	 * Time: 10:45
	 */
	namespace Manager\Model;

	use Quasar\StringPlus;

	class ClientModel extends ManagerModel{
		protected $tableName   = 'client';
		protected $tablePrefix = 'user_';

		public function _initialize(){
			parent::_initialize();
		}

		public function getColumn($just_desc = false){
			$result = $this->query('SELECT
	COLUMN_NAME `NAME`,
	(CASE WHEN COLUMN_NAME = \'gender\' THEN \'性别\' ELSE COLUMN_COMMENT END) `DESC`,
	COLUMN_TYPE `TYPE`
FROM information_schema.`COLUMNS`
WHERE TABLE_SCHEMA = \'gimmay_meeting\'
AND TABLE_NAME = \'user_client\'
AND COLUMN_NAME NOT IN (\'id\', \'password\', \'status\', \'creatime\', \'creator\', \'pinyin_code\')');
			if($just_desc){
				$list[0] = [];
				foreach($result as $val) array_push($list[0], $val['desc']);

				return $list;
			}
			else return $result;
		}

		public function isExist($mobile, $name){
			$result = $this->where(['mobile' => $mobile, 'name' => $name])->find();

			return $result ? true : false;
		}

		public function createClientFromExcelData($excel_data, $map){
			$str_obj      = new StringPlus();
			$table_column = $this->getColumn();
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
					$tmp['creator']     = I('session.MANAGER_USER_ID', 0, 'int');
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
				if($this->isExist($mobile, $name)) continue;
				else $data_list[] = $tmp;
				$data_list[] = $tmp;
			}
			if(!$data_list) return ['status' => false, 'message' => '重复数据无需导入'];
			$result = $this->addAll($data_list);
			if($result) return ['status' => true, 'message' => '导入成功'];
			else return ['status' => false, 'message' => $this->getError()];
		}
	}