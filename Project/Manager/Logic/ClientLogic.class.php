<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 15:50
	 */
	namespace Manager\Logic;

	use Core\Logic\ColumnControlLogic;
	use Core\Logic\JoinLogic;
	use Core\Logic\LogLogic;
	use Core\Logic\ReceivablesLogic;
	use Core\Logic\WxCorpLogic;
	use Quasar\StringPlus;

	class ClientLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function setColumn($type, $data, $opt = []){
			switch($type){
				case 'manage:client_list':
					/** @var \Core\Model\GroupMemberModel $group_member_model */
					$group_member_model = D('Core/GroupMember');
					foreach($data as $key => $val){
						$group = $group_member_model->findRecord(1, [
							'mid'    => $opt['mid'],
							'cid'    => $val['cid'],
							'status' => 1
						]);
						if($group) $data[$key]['group'] = $group['code'];
						else $data[$key]['group'] = null;
					}

					return $data;
				break;
				case 'alter:client_info':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model                = D('Core/Join');
					$join_record               = $join_model->findRecord(1, [
						'mid'    => $opt['mid'],
						'cid'    => $opt['cid'],
						'status' => 'not deleted'
					]);
					$data['registration_date'] = $join_record['registration_date'];

					return $data;
				break;
				case 'excel:set_data':
					foreach($data as $key1 => $val1){
						// 排除字段
						foreach($opt['exceptColumn'] as $val2) unset($data[$key1][$val2]);
						// 性别
						switch($val1['gender']){
							case 0:
							default:
								$data[$key1]['gender'] = '未指定';
							break;
							case 1:
								$data[$key1]['gender'] = '男';
							break;
							case 2:
								$data[$key1]['gender'] = '女';
							break;
						}
						// 签到状态
						switch($val1['sign_status']){
							case 0:
								$data[$key1]['sign_status'] = '未签到';
							break;
							case 1:
								$data[$key1]['sign_status'] = '已签到';
							break;
							case 2:
								$data[$key1]['sign_status'] = '取消签到';
							break;
						}
						// 审核状态
						switch($val1['review_status']){
							case 0:
								$data[$key1]['review_status'] = '未审核';
							break;
							case 1:
								$data[$key1]['review_status'] = '已审核';
							break;
							case 2:
								$data[$key1]['review_status'] = '取消审核';
							break;
						}
						// 审核时间
						$data[$key1]['review_time'] = $val1['review_time'] ? date('Y-m-d H:i:s', $val1['review_time']) : '';
						// 签到时间
						$data[$key1]['sign_time'] = $val1['sign_time'] ? date('Y-m-d H:i:s', $val1['sign_time']) : '';
						// 签到类型
						switch($val1['sign_type']){
							case 0:
							default:
								$data[$key1]['sign_type'] = '未签到';
							break;
							case 1:
								$data[$key1]['sign_type'] = 'PC后台签到';
							break;
							case 2:
								$data[$key1]['sign_type'] = '微信自主签到';
							break;
							case 3:
								$data[$key1]['sign_type'] = '微信后台签到';
							break;
						}
						// 签到打印状态
						switch($val1['print_status']){
							case 0:
							default:
								$data[$key1]['print_status'] = '未打印';
							break;
							case 1:
								$data[$key1]['print_status'] = '已打印';
							break;
						}
						// 签到打印状态
						switch($val1['is_new']){
							case 0:
								$data[$key1]['is_new'] = '老客';
							break;
							case 1:
								$data[$key1]['is_new'] = '新客';
							break;
						}
					}
					$data = array_merge([
						[
							'name'               => '姓名',
							'gender'             => '性别',
							'mobile'             => '电话',
							'unit'               => '单位',
							'birthday'           => '生日',
							'email'              => '邮箱',
							'title'              => '职务',
							'position'           => '职称',
							'address'            => '地址',
							'id_card_number'     => '身份证号',
							'develop_consultant' => '开拓顾问',
							'service_consultant' => '服务顾问',
							'is_new'             => '是否新客',
							'team'               => '团队',
							'type'               => '类型',
							'comment'            => '备注',
							'column1'            => '备选字段1',
							//					'column2'            => '备选字段2',
							//					'column3'            => '备选字段3',
							//					'column4'            => '备选字段4',
							//					'column5'            => '备选字段5',
							//					'column6'            => '备选字段6',
							//					'column7'            => '备选字段7',
							//					'column8'            => '备选字段8',
							'registration_date'  => '报名时间',
							'registration_type'  => '报名类型',
							'review_status'      => '审核状态',
							'review_time'        => '审核时间',
							'sign_status'        => '签到状态',
							'sign_time'          => '签到时间',
							'sign_type'          => '签到类型',
							'sign_code'          => '签到码',
							'print_status'       => '打印状态',
							'print_times'        => '打印时间'
						]
					], $data);

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}

		public function alterColumnForExportExcel($list, $except_column = []){
			foreach($list as $key1 => $val1){
				// 排除字段
				foreach($except_column as $val2) unset($list[$key1][$val2]);
				// 性别
				switch($val1['gender']){
					case 0:
					default:
						$list[$key1]['gender'] = '未指定';
					break;
					case 1:
						$list[$key1]['gender'] = '男';
					break;
					case 2:
						$list[$key1]['gender'] = '女';
					break;
				}
				// 签到状态
				switch($val1['sign_status']){
					case 0:
						$list[$key1]['sign_status'] = '未签到';
					break;
					case 1:
						$list[$key1]['sign_status'] = '已签到';
					break;
					case 2:
						$list[$key1]['sign_status'] = '取消签到';
					break;
				}
				// 审核状态
				switch($val1['review_status']){
					case 0:
						$list[$key1]['review_status'] = '未审核';
					break;
					case 1:
						$list[$key1]['review_status'] = '已审核';
					break;
					case 2:
						$list[$key1]['review_status'] = '取消审核';
					break;
				}
				// 审核时间
				$list[$key1]['review_time'] = $val1['review_time'] ? date('Y-m-d H:i:s', $val1['review_time']) : '';
				// 签到时间
				$list[$key1]['sign_time'] = $val1['sign_time'] ? date('Y-m-d H:i:s', $val1['sign_time']) : '';
				// 签到类型
				switch($val1['sign_type']){
					case 0:
					default:
						$list[$key1]['sign_type'] = '未签到';
					break;
					case 1:
						$list[$key1]['sign_type'] = 'PC后台签到';
					break;
					case 2:
						$list[$key1]['sign_type'] = '微信自主签到';
					break;
					case 3:
						$list[$key1]['sign_type'] = '微信后台签到';
					break;
				}
				// 签到打印状态
				switch($val1['print_status']){
					case 0:
					default:
						$list[$key1]['print_status'] = '未打印';
					break;
					case 1:
						$list[$key1]['print_status'] = '已打印';
					break;
				}
				// 签到打印状态
				switch($val1['is_new']){
					case 0:
						$list[$key1]['is_new'] = '老客';
					break;
					case 1:
					default:
						$list[$key1]['is_new'] = '新客';
					break;
				}
			}
			$list = array_merge([
				[
					'name'               => '姓名',
					'gender'             => '性别',
					'mobile'             => '电话',
					'unit'               => '单位',
					'birthday'           => '生日',
					'email'              => '邮箱',
					'title'              => '职务',
					'position'           => '职称',
					'address'            => '地址',
					'id_card_number'     => '身份证号',
					'develop_consultant' => '开拓顾问',
					'service_consultant' => '服务顾问',
					'is_new'             => '是否新客',
					'team'               => '团队',
					'type'               => '类型',
					'comment'            => '备注',
					'column1'            => '备选字段1',
					//					'column2'            => '备选字段2',
					//					'column3'            => '备选字段3',
					//					'column4'            => '备选字段4',
					//					'column5'            => '备选字段5',
					//					'column6'            => '备选字段6',
					//					'column7'            => '备选字段7',
					//					'column8'            => '备选字段8',
					'registration_date'  => '报名时间',
					'registration_type'  => '报名类型',
					'review_status'      => '审核状态',
					'review_time'        => '审核时间',
					'sign_status'        => '签到状态',
					'sign_time'          => '签到时间',
					'sign_type'          => '签到类型',
					'sign_code'          => '签到码',
					'print_status'       => '打印状态',
					'print_times'        => '打印时间'
				]
			], $list);

			return $list;
		}

		public function getReceivablesList($list, $receivables = 1, $new = true, $pagination = []){
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model = D('Core/Receivables');
			$new_list          = [];
			$mid               = I('get.mid', 0, 'int');
			foreach($list as $key => $val){
				$receivables_record = $receivables_model->findRecord(2, [
					'cid'    => $val['cid'],
					'mid'    => $mid,
					'status' => 'not deleted'
				]);
				$receivables_price  = 0;
				foreach($receivables_record as $val2) $receivables_price += $val2['price'];
				if($receivables == 1 && $receivables_price){
					if($val['receivables']) $val['receivables'] += $receivables_price;
					else{
						$val['receivables'] = 0;
						$val['receivables'] += $receivables_price;
					}
					if($new) $new_list[] = $val;
					else{
						if($list[$key]['receivables']) $val['receivables'] += $receivables_price;
						else{
							$list[$key]['receivables'] = 0;
							$list[$key]['receivables'] += $receivables_price;
						}
					}
				}
				if($receivables == 0 && !$receivables_record){
					$new_list[] = $val;
				}
			}

			return $new ? ($pagination ? array_splice($new_list, $pagination[0], $pagination[1]) : $new_list) : $list;
		}

		public function handlerRequest($type){
			switch($type){
				case 'create':
					if($this->permissionList['CLIENT.CREATE']){
						$data = I('post.');
						/* 1.创建参会人员 */
						/** @var \Core\Model\ClientModel $model */
						$model        = D('Core/Client');
						$exist_client = $model->findClient(1, [
							'mobile' => $data['mobile'],
							'name'   => $data['name'],
							'status' => 'not deleted'
						]);
						$creator      = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$mid          = I('get.mid', 0, 'int');
						if($exist_client){
							$cid                 = $exist_client['id'];
							$str_obj             = new StringPlus();
							$data['status']      = 1;
							$data['is_new']      = 0;
							$data['creatime']    = time();
							$data['creator']     = $creator;
							$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
							$model->alterClient(['id' => $cid], $data);
						}
						else{
							$str_obj             = new StringPlus();
							$data['creatime']    = time();
							$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
							$data['birthday']    = $data['birthday'] ? $data['birthday'] : null;
							$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
							$data['password']    = $str_obj->makePassword(C('DEFAULT_CLIENT_PASSWORD'), $data['mobile']);
							$result1             = $model->createClient($data);
							if($result1['status']) $cid = $result1['id'];
							else return $result1;
						}
						/* 2.创建参会记录 */
						/** @var \Core\Model\JoinModel $join_model */
						$join_model        = D('Core/Join');
						$join_record       = $join_model->findRecord(1, [
							'mid'    => $mid,
							'cid'    => $cid,
							'status' => 'not deleted'
						]);
						$mobile            = $data['mobile'];
						$registration_date = I('post.registration_date');
						$registration_date = $registration_date ? $registration_date : date('Y-m-d');
						if($join_record){
							if($join_record['join_status'] != 1){
								C('TOKEN_ON', false);
								$join_result = $join_model->alterRecord(['id' => $join_record['id']], [
									'status'            => 1,
									'creator'           => $creator,
									'creatime'          => time(),
									'registration_date' => $registration_date,
									'column1'           => $data['column1'],
									'column2'           => $data['column2'],
									'column3'           => $data['column3'],
									'column4'           => $data['column4'],
									'column5'           => $data['column5'],
									'column6'           => $data['column6'],
									'column7'           => $data['column7'],
									'column8'           => $data['column8']
								]);
								if($join_result['status']) $result = [
									'status'  => true,
									'message' => '创建成功'
								];
								else $result = $join_result;
							}
							else $result = [
								'status'  => true,
								'message' => '创建成功'
							];
						}
						else{
							$post                      = $data;
							$data                      = [];
							$data['cid']               = $cid;
							$data['mid']               = $mid;
							$data['column1']           = $post['column1'];
							$data['column2']           = $post['column2'];
							$data['column3']           = $post['column3'];
							$data['column4']           = $post['column4'];
							$data['column5']           = $post['column5'];
							$data['column6']           = $post['column6'];
							$data['column7']           = $post['column7'];
							$data['column8']           = $post['column8'];
							$data['creatime']          = time();
							$data['registration_date'] = $registration_date;
							$data['creator']           = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
							C('TOKEN_ON', false);
							$result = $join_model->createRecord($data);
						}
						/* 3.试图根据手机号创建微信用户记录 */
						$wechat_logic     = new WxCorpLogic();
						$wechat_user_list = $wechat_logic->getAllUserList();
						foreach($wechat_user_list as $val){
							if($val['mobile'] == $mobile && $val['status'] != 4){
								/** @var \Core\Model\WechatModel $wechat_model */
								$wechat_model = D('Core/Wechat');
								C('TOKEN_ON', false);
								$wechat_model->deleteRecord([
									'wid'   => $val['userid'],
									'otype' => 1,
									'oid'   => $val['id'],
									'wtype' => 1
								]);
								$department = '';
								foreach($val['department'] as $val2) $department .= $val2.',';
								$department         = trim($department, ',');
								$data               = [];
								$data['otype']      = 1;// 对象类型 这里为客户(参会人员)
								$data['oid']        = $cid; // 对象ID
								$data['wtype']      = 1; // 微信ID类型 企业号
								$data['wid']        = $val['userid']; // 微信ID
								$data['department'] = $department; // 部门ID
								$data['mobile']     = $val['mobile']; // 手机号码
								$data['avatar']     = $val['avatar']; // 头像地址
								$data['gender']     = $val['gender']; // 性别
								$data['is_follow']  = $val['status'];    //是否关注
								$data['nickname']   = $val['name']; // 昵称
								$data['creatime']   = time(); // 创建时间
								$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int'); // 当前创建者
								$wechat_model->createRecord($data); // 插入数据
								break;
							}
						}
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'user_client&workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '创建参会人员',
							'type'     => 'create'
						]);
						$result['cid'] = $cid;

						return array_merge($result, [
							'__ajax__'   => false,
							'__return__' => U('manage', ['mid' => $mid])
						]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有创建参会人员的权限',
						'__ajax__' => false
					];
				break;
				case 'alter':
					if($this->permissionList['CLIENT.ALTER']){
						/** @var \Core\Model\ClientModel $model */
						$model = D('Core/Client');
						/** @var \Core\Model\JoinModel $join_model */
						$join_model = D('Core/Join');
						$str_obj    = new StringPlus();
						$data       = I('post.');
						$id         = I('get.id', 0, 'int');
						//						$exist_client = $model->findClient(1, ['mobile' => $data['mobile']]);
						//						if($exist_client && $exist_client['id']!=$id) return [
						//							'status'   => false,
						//							'message'  => '该手机号已存在',
						//							'__ajax__' => false
						//						];
						$data['pinyin_code']        = $str_obj->makePinyinCode($data['name']);
						$data['birthday']           = $data['birthday'] ? $data['birthday'] : null;
						$result1                    = $model->alterClient(['id' => $id], $data);
						$join_record                = $join_model->findRecord(1, [
							'cid'    => $id,
							'mid'    => I('get.mid', 0, 'int'),
							'status' => 'not deleted'
						]);
						$data2['registration_date'] = $data['registration_date'];
						$data2['column1']           = $data['column1'];
						$data2['column2']           = $data['column2'];
						$data2['column3']           = $data['column3'];
						$data2['column4']           = $data['column4'];
						$data2['column5']           = $data['column5'];
						$data2['column6']           = $data['column6'];
						$data2['column7']           = $data['column7'];
						$data2['column8']           = $data['column8'];
						C('TOKEN_ON', false);
						$result2   = $join_model->alterRecord(['id' => $join_record['id']], $data2);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'user_client',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '修改参会人员',
							'type'     => 'modify'
						]);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'user_client&workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '修改参会人员',
							'type'     => 'modify'
						]);

						return ($result1['status'] || $result2['status']) ? [
							'status'  => true,
							'message' => '修改成功'
						] : [
							'status'  => false,
							'message' => '未做任何修改'
						];
					}
					else return [
						'status'   => false,
						'message'  => '您没有修改参会人员的权限',
						'__ajax__' => false
					];
				break;
				case 'import_excel':
					if($this->permissionList['CLIENT.IMPORT-EXCEL']){
						$excel_logic = new ExcelLogic();
						$result      = $excel_logic->importClientData($_FILES);
						if(isset($result['data']['dbIndex'])){
							/** @var \Manager\Model\ClientModel $model */
							//$model                    = D('Client');
							//$table_head               = $model->getSelfColumn();
							/** @var \Core\Model\ColumnControlModel $column_control_model */
							$column_control_model = D('Core/ColumnControl');
							$column_list          = $column_control_model->findRecord(2, [
								'mid'  => I('get.mid', 0, 'int'),
								'view' => 1
							]);
							$table_head           = [];
							foreach($column_list as $col){
								$table_head[] = ['name' => $col['form'], 'desc' => $col['name']];
							}
							$result['data']['dbHead'] = $table_head;
						}
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'system_upload',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '保存参会人员EXCEL文件',
							'type'     => 'create'
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有导入参会人员的权限',
						'__ajax__' => false
					];
				break;
				case 'save_excel_data':
					if($this->permissionList['CLIENT.IMPORT-EXCEL']){
						$upload_record_id = I('post.dbIndex', 0, 'int');
						$excel_header     = I('post.excel', '');
						$table_column     = I('post.table', '');
						$map              = [
							'data'   => $excel_header ? explode(',', $excel_header) : [],
							'column' => $table_column ? explode(',', $table_column) : []
						];
						/** @var \Core\Model\UploadModel $upload_model */
						$upload_model  = D('Core/Upload');
						$excel_logic   = new ExcelLogic();
						$upload_record = $upload_model->findRecord(1, [
							'id'     => $upload_record_id,
							'status' => 'not deleted'
						]);
						$file_path     = trim($upload_record['save_path'], '/');
						$excel_content = $excel_logic->readClientData($file_path);
						$result        = $this->createClientFromExcelData($excel_content['body'], $map);
						$log_logic     = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'user_client&workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '导入参会人员EXCEL数据',
							'type'     => 'create'
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有导入参会人员的权限',
						'__ajax__' => false
					];
				break;
				case 'review':
					if($this->permissionList['CLIENT.REVIEW']){
						$cid = I('post.id', 0, 'int');
						$mid = I('post.mid', 0, 'int');
						/** @var \Core\Model\JoinModel $join_model */
						$join_model  = D('Core/Join');
						$join_record = $join_model->findRecord(1, [
							'mid'    => $mid,
							'cid'    => $cid,
							'status' => 'not deleted'
						]);
						C('TOKEN_ON', false);
						$result1 = $join_model->alterRecord(['id' => $join_record['id']], [
							'review_status' => 1,
							'review_time'   => time()
						]);
						if(!$result1['status']) return array_merge($result1, ['__ajax__' => true]);
						$join_logic = new JoinLogic();
						$result2    = $join_logic->makeQRCodeForSign([$cid], ['mid' => $mid]);
						$log_logic  = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '审核参会人员',
							'type'     => 'modify'
						]);

						return array_merge($result2, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有审核参会人员的权限',
						'__ajax__' => false
					];
				break;
				case 'anti_review':
					if($this->permissionList['CLIENT.ANTI-REVIEW']){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model  = D('Core/Join');
						$cid         = I('post.id', 0, 'int');
						$mid         = I('post.mid', 0, 'int');
						$join_record = $join_model->findRecord(1, [
							'mid'    => $mid,
							'cid'    => $cid,
							'status' => 'not deleted'
						]);
						C('TOKEN_ON', false);
						$result    = $join_model->alterRecord(['id' => $join_record['id']], [
							'review_status' => 2,
							'sign_status'   => 0,
							'sign_time'     => null
						]);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '取消审核参会人员',
							'type'     => 'modify'
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有取消审核参会人员的权限',
						'__ajax__' => false
					];
				break;
				case 'multi_review': // 批量审核
					if($this->permissionList['CLIENT.REVIEW']){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model    = D('Core/Join');
						$mid           = I('get.mid');
						$client_id_arr = explode(',', I('post.id', ''));
						$join_id       = [];
						foreach($client_id_arr as $v){
							$join_record = $join_model->findRecord(1, [
								'cid'    => $v,
								'mid'    => $mid,
								'status' => 'not deleted'
							]);
							$join_id[]   = $join_record['id'];
						}
						C('TOKEN_ON', false);
						$result1 = $join_model->alterRecord(['id' => ['in', $join_id]], [
							'review_status' => 1,
							'review_time'   => time()
						]);
						if(!$result1['status']) return array_merge($result1, ['__ajax__' => false]);
						$join_logic = new JoinLogic();
						$result2    = $join_logic->makeQRCodeForSign($client_id_arr, ['mid' => $mid]);
						$log_logic  = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '批量审核参会人员',
							'type'     => 'modify'
						]);

						return array_merge($result2, ['__ajax__' => false]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有审核参会人员的权限',
						'__ajax__' => false
					];
				break;
				case 'multi_anti_review': // 批量反审核
					if($this->permissionList['CLIENT.ANTI-REVIEW']){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model    = D('Core/Join');
						$data['id']    = I('post.id', '');
						$mid           = I('get.mid', 0, 'int');
						$client_id_arr = explode(',', $data['id']);
						$join_id       = [];
						foreach($client_id_arr as $v){
							$join_record = $join_model->findRecord(1, [
								'cid'    => $v,
								'mid'    => $mid,
								'status' => 'not deleted'
							]);
							$join_id[]   = $join_record['id'];
						}
						C('TOKEN_ON', false);
						$result    = $join_model->alterRecord(['id' => ['in', $join_id]], [
							'review_status' => 2,
							'sign_status'   => 0,
							'sign_time'     => null
						]);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '批量取消审核参会人员',
							'type'     => 'modify'
						]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有取消审核参会人员的权限',
						'__ajax__' => false
					];
				break;
				case 'sign': // 签到
					if($this->permissionList['CLIENT.SIGN']){
						$core_client_logic = new \Core\Logic\ClientLogic();
						$result            = $core_client_logic->sign([
							'mid'  => I('post.mid', 0, 'int'),
							'cid'  => I('post.id', 0, 'int'),
							'type' => 1,
							'eid'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')
						]);
						$log_logic         = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '参会人员签到',
							'type'     => 'modify'
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有签到的权限',
						'__ajax__' => true
					];
				break; // 取消签到
				case 'anti_sign':
					if($this->permissionList['CLIENT.ANTI-SIGN']){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model  = D('Core/Join');
						$id          = I('post.id', 0, 'int');
						$mid         = I('post.mid', 0, 'int');
						$join_record = $join_model->findRecord(1, [
							'cid'    => $id,
							'mid'    => $mid,
							'status' => 'not deleted'
						]);
						C('TOKEN_ON', false);
						$result = $join_model->alterRecord(['id' => $join_record['id']], [
							'sign_status' => 2,
							'sign_time'   => null,
							'sign_type'   => 0
						]);
						if($result['status']){
							$message_logic = new MessageLogic();
							$message_logic->send($mid, 1, 2, [$id]);
						}
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '参会人员取消签到',
							'type'     => 'modify'
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有取消签到的权限',
						'__ajax__' => false
					];
				break;
				case 'multi_sign': // 批量签到
					if($this->permissionList['CLIENT.SIGN']){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model    = D('Core/Join');
						$message_logic = new MessageLogic();
						$id_arr        = explode(',', I('post.id', ''));
						$mid           = I('get.mid', 0, 'int');
						$sign_director = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$count         = 0;
						$send_list     = [];
						foreach($id_arr as $val){
							$join_record = $join_model->findRecord(1, [
								'cid'    => $val,
								'mid'    => $mid,
								'status' => 'not deleted'
							]);
							if($join_record['review_status'] == 1){
								C('TOKEN_ON', false);
								$result = $join_model->alterRecord(['id' => $join_record['id']], [
									'sign_status'      => 1,
									'sign_time'        => time(),
									'sign_director_id' => $sign_director,
									'sign_type'        => 1
								]);
								if($result['status']){
									$send_list[] = $val;
									/** @var \Core\Model\SignResultModel $sign_result_model */
									$sign_result_model = D('Core/SignResult');
									$signed_count      = $join_model->findRecord(0, [
										'mid'         => $mid,
										'sign_status' => 1,
										'status'      => 1
									]);
									C('TOKEN_ON', false);
									$sign_result_model->createRecord([
										'mid'       => $mid,
										'cid'       => $val,
										'sign_time' => time(),
										'creatime'  => time(),
										'creator'   => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
										'order'     => $signed_count+1
									]);
									$count++;
								}
							}
							if($count%50 == 0 && $count != 0){
								$message_logic->send($mid, 1, 1, $send_list);
								$send_list = [];
							}
						};
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '参会人员批量签到',
							'type'     => 'modify'
						]);
						if($count<50 && $count>0) $message_logic->send($mid, 1, 1, $send_list);
						if($count == 0) return [
							'status'   => false,
							'message'  => '批量签到失败',
							'__ajax__' => false
						];
						elseif($count == count($id_arr)) return [
							'status'   => true,
							'message'  => '批量签到成功',
							'__ajax__' => false
						];
						else return [
							'status'   => true,
							'message'  => '部分批量签到成功',
							'__ajax__' => false
						];
					}
					else return [
						'status'   => false,
						'message'  => '您没有签到的权限',
						'__ajax__' => false
					];
				break;
				case 'multi_anti_sign':
					if($this->permissionList['CLIENT.ANTI-SIGN']){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model    = D('Core/Join');
						$message_logic = new MessageLogic();
						$id_arr        = explode(',', I('post.id', ''));
						$mid           = I('get.mid', 0, 'int');
						$count         = 0;
						$send_list     = [];
						foreach($id_arr as $val){
							$join_record = $join_model->findRecord(1, [
								'cid'    => $val,
								'mid'    => $mid,
								'status' => 'not deleted'
							]);
							C('TOKEN_ON', false);
							$result = $join_model->alterRecord(['id' => $join_record['id']], [
								'sign_status' => 2,
								'sign_time'   => null,
								'sign_type'   => 0
							]);
							if($result['status']){
								$send_list[] = $val;
								$count++;
							}
							if($count%50 == 0 && $count != 0){
								$message_logic->send($mid, 1, 2, $send_list);
								$send_list = [];
							}
						}
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '参会人员批量取消签到',
							'type'     => 'modify'
						]);
						if($count<50 && $count>0) $message_logic->send($mid, 1, 2, $send_list);
						if($count == 0) return [
							'status'   => false,
							'message'  => '批量取消签到失败',
							'__ajax__' => false
						];
						elseif($count == count($id_arr)) return [
							'status'   => true,
							'message'  => '批量取消签到成功',
							'__ajax__' => false
						];
						else return [
							'status'   => true,
							'message'  => '部分批量取消签到成功',
							'__ajax__' => false
						];
					}
					else return [
						'status'   => false,
						'message'  => '您没有取消签到的权限',
						'__ajax__' => false
					];
				break;
				case 'delete':
					if($this->permissionList['CLIENT.DELETE']){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model = D('Core/Join');
						$id_arr     = explode(',', I('post.id'));
						//$join_id_arr = explode(',', I('post.jid'));
						/* 监测是否已审核 */
						$delete_id_arr = [];
						foreach($id_arr as $val){
							$record = $join_model->findRecord(1, [
								'cid'    => $val,
								'mid'    => I('get.mid', 0, 'int'),
								'status' => 'not deleted'
							]);
							if($record['review_status'] == 1) continue;
							$delete_id_arr[] = $record['id'];
						}
						if(!$delete_id_arr) return [
							'status'   => false,
							'message'  => '客户已审核不能删除',
							'__ajax__' => false
						];
						C('TOKEN_ON', false);
						$result    = $join_model->deleteRecord($delete_id_arr);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '参会人员批量取消签到',
							'type'     => 'modify'
						]);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有删除参会人员的权限',
						'__ajax__' => false
					];
				break;
				case 'send_message':
					if($this->permissionList['CLIENT.SEND-INVITATION']){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model    = D('Core/Join');
						$message_logic = new MessageLogic();
						$cid           = I('post.id', 0, 'int');
						$mid           = I('post.mid', 0, 'int');
						$record        = $join_model->findRecord(1, [
							'cid'    => $cid,
							'mid'    => $mid,
							'status' => 'not deleted'
						]);
						if($record['review_status']) $result = $message_logic->send($mid, 1, 4, [$record['cid']]);
						else $result = ['status' => false, 'message' => '该客户未审核'];
						$log_logic = new LogLogic();
						$log_logic->create([
							'extend' => 'PC',
							'action' => '发送邀请',
							'type'   => '-'
						]);

						return array_merge($result, [
							'__ajax__' => true
						]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有发送邀请的权限',
						'__ajax__' => true
					];
				break;
				case 'multi_send_message':
					if($this->permissionList['CLIENT.SEND-INVITATION']){
						$message_logic = new MessageLogic();
						/** @var \Core\Model\JoinModel $join_model */
						$join_model = D('Core/Join');
						$cid        = I('post.id', '');
						$mid        = I('get.mid', 0, 'int');
						$client_arr = explode(',', $cid);
						$count      = 0;
						foreach($client_arr as $v){
							$record = $join_model->findRecord(1, [
								'cid'    => $v,
								'mid'    => $mid,
								'status' => 'not deleted'
							]);
							if($record['review_status']) $result = $message_logic->send($mid, 1, 4, [$record['cid']]);
							else $result = ['status' => false, 'message' => '该客户未审核'];
							if($result['status']) $count++;
						}
						$log_logic = new LogLogic();
						$log_logic->create([
							'extend' => 'PC',
							'action' => '批量发送邀请',
							'type'   => '-'
						]);
						if($count == count($client_arr)) return [
							'status'   => true,
							'message'  => '全部发送成功',
							'__ajax__' => false
						];
						elseif($count == 0) return [
							'status'   => false,
							'message'  => '发送失败',
							'__ajax__' => false
						];
						else return [
							'status'   => true,
							'message'  => '部分发送成功',
							'__ajax__' => false
						];
					}
					else return [
						'status'   => false,
						'message'  => '您没有发送邀请的权限',
						'__ajax__' => false
					];
				break;
				case 'get_assign_sign_place':
					/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
					$join_sign_place_model = D('Core/JoinSignPlace');
					/** @var \Core\Model\SignPlaceModel $sign_place_model */
					$sign_place_model       = D('Core/SignPlace');
					$cid                    = I('post.cid', 0, 'int');
					$mid                    = I('get.mid', 0, 'int');
					$assigned_sign_place    = $join_sign_place_model->findRecord(2, [
						'mid'    => $mid,
						'cid'    => $cid,
						'status' => 'not deleted'
					]);
					$sign_place             = $sign_place_model->findRecord(2, [
						'mid'    => $mid,
						'status' => 'not deleted'
					]);
					$assigned_sign_place_id = [];
					foreach($assigned_sign_place as $val) $assigned_sign_place_id[] = $val['sid'];
					foreach($sign_place as $key => $val){
						if(in_array($val['id'], $assigned_sign_place_id)) $sign_place[$key]['assign_status'] = 1;
						else $sign_place[$key]['assign_status'] = 0;
					}

					return ['data' => $sign_place, '__ajax__' => true];
				break;
				case 'assign_sign_place':
					if($this->permissionList['CLIENT.ASSIGN-SIGN_PLACE']){
						/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
						$join_sign_place_model = D('Core/JoinSignPlace');
						$cid                   = I('post.cid', 0, 'int');
						$mid                   = I('get.mid', 0, 'int');
						$sid_str               = I('post.sign_place', '');
						$sid_arr               = explode(',', $sid_str);
						$data                  = [];
						// 删除所有分配记录
						$join_sign_place_model->deleteRecord(['mid' => $mid, 'cid' => $cid]);
						// 清除已分配的记录
						C('TOKEN_ON', false);
						$join_sign_place_model->wipeOutRecord([
							'mid' => $mid,
							'cid' => $cid,
							'sid' => ['in', $sid_arr]
						]);
						// 添加分配记录
						foreach($sid_arr as $key => $val) $data[] = [
							'mid'      => $mid,
							'cid'      => $cid,
							'sid'      => $val,
							'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
							'creatime' => time()
						];
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join_sign_place',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '分配签到点',
							'type'     => 'delete&create'
						]);
						$result = $join_sign_place_model->createMultiRecord($data);
						if($result['status']) return [
							'status'   => true,
							'message'  => $result['message'],
							'__ajax__' => false
						];
						else return [
							'status'   => false,
							'message'  => $result['message'],
							'__ajax__' => false
						];
					}
					else return [
						'status'   => true,
						'message'  => '您没有分配签到点的权限',
						'__ajax__' => false
					];
				break;
				case 'batch_assign_sign_place':
					if($this->permissionList['CLIENT.ASSIGN-SIGN_PLACE']){
						/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
						$join_sign_place_model = D('Core/JoinSignPlace');
						$cid_str               = I('post.cid', '');
						$sid_str               = I('post.sign_place', '');
						$cid_arr               = explode(',', $cid_str);
						$sid_arr               = explode(',', $sid_str);
						$mid                   = I('get.mid', 0, 'int');
						C('TOKEN_ON', false);
						$count = 0;
						foreach($cid_arr as $c_val){
							foreach($sid_arr as $s_val){
								$result = $join_sign_place_model->createRecord([
									'mid'      => $mid,
									'cid'      => $c_val,
									'sid'      => $s_val,
									'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
									'creatime' => time()
								]);
								if($result['status']) $count++;
							}
						}
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_join_sign_place',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '批量分配签到点',
							'type'     => 'create'
						]);
						if($count == count($cid_arr)*count($sid_arr)) return [
							'status'   => true,
							'message'  => '分配成功',
							'__ajax__' => false
						];
						elseif($count == 0) return [
							'status'   => false,
							'message'  => '分配失败',
							'__ajax__' => false
						];
						else return [
							'status'   => true,
							'message'  => '部分分配成功',
							'__ajax__' => false
						];
					}
					else return [
						'status'   => true,
						'message'  => '您没有分配签到点的权限',
						'__ajax__' => false
					];
				break;
				case 'get_receivables':
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					$record         = $receivables_model->findRecord(2, [
						'cid'    => I('post.cid', 0, 'int'),
						'mid'    => I('post.mid', 0, 'int'),
						'status' => 'not deleted'
					]);
					foreach($record as $key => $val){
						$employee_record       = $employee_model->findEmployee(1, [
							'id'     => $val['payee_id'],
							'status' => 'not deleted'
						]);
						$record[$key]['payee'] = $employee_record['name'];
					}

					return [
						'data'     => $record,
						'__ajax__' => true
					];
				break;
				case 'create_receivables':
					if($this->permissionList['CLIENT.EARN-PAYMENT']){
						/** @var \Core\Model\CouponItemModel $coupon_item_model */
						$coupon_item_model = D('Core/CouponItem');
						$data              = I('post.', '');
						$mid               = I('get.mid', 0, 'int');
						$cid               = I('post.cid', 0, 'int');
						$coupon_item_list  = explode(',', $data['coupon_ids']);
						C('TOKEN_ON', false);
						foreach($coupon_item_list as $k => $v){
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
						/** @var \Core\Model\ReceivablesModel $receivables_model */
						$receivables_model = D('Core/Receivables');
						/** @var \Core\Model\ClientModel $client_model */
						$client_model = D('Core/Client');
						/** @var \Core\Model\EmployeeModel $employee_model */
						$employee_model       = D('Core/Employee');
						$logic                = new ReceivablesLogic();
						$data['mid']          = $mid;
						$data['cid']          = $cid;
						$data['coupon_ids']   = I('post.coupon_code', '');
						$data['time']         = strtotime(I('post.receivables_time', ''));
						$data['creatime']     = time();    //创建时间
						$data['creator']      = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
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
							$sms_send      = $message_logic->send($mid, 0, 3, [$employee_result['id']]);
						}

						return array_merge($receivables_result, [
							'__ajax__'   => false,
							'__return__' => U('Receivables/Manage', ['mid' => $mid])
						]);
					}
					else return [
						'status'   => true,
						'message'  => '您没有收款的权限',
						'__ajax__' => false
					];
				break;
				case 'gift':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model = D('Core/Join');
					C('TOKEN_ON', false);
					$id        = I('post.id', 0, 'int');
					$join_list = $join_model->findRecord(1, [
						'cid'    => $id,
						'status' => 1,
						'mid'    => I('get.mid', 0, 'int')
					]);
					//					if($join_list['gift_status'] == 1){
					//						return array_merge(['message' => '礼品已经领取', 'status' => false], [
					//							'__ajax__' => false
					//						]);
					//					}else{
					//					}
					$join_result = $join_model->alterRecord(['id' => $join_list['id']], ['gift_status' => 1]);

					return array_merge($join_result, [
						'__ajax__' => false
					]);
				break;
				case 'get_client':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$mid         = I('get.mid', 0, 'int');
					$cid         = I('post.id', 0, 'int');
					$join_result = $join_model->findRecord(1, ['cid' => $cid, 'mid' => $mid]);

					return array_merge($join_result, ['__ajax__' => true]);
				break;
				case 'assign_group':
					$cid_arr  = explode(',', I('post.cid', ''));
					$mid      = I('get.mid', 0, 'int');
					$group_id = I('post.group', 0, 'int');
					/** @var \Core\Model\GroupMemberModel $group_member_model */
					$group_member_model = D('Core/GroupMember');
					$group_member_model->dropRecord(['cid', ['in', $cid_arr], 'mid' => $mid]);
					$data = [];
					foreach($cid_arr as $client_id){
						$data[] = [
							'mid'      => $mid,
							'gid'      => $group_id,
							'cid'      => $client_id,
							'time'     => 1,
							'status'   => 1,
							'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
							'creatime' => time()
						];
					}
					$result = $group_member_model->createMultiMember($data);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'multi_alter_column':
					$cid_arr = explode(',', I('post.cid', ''));
					$mid     = I('get.mid', 0, 'int');
					$data    = I('post.');
					unset($data['cid']);
					/** @var \Core\Model\JoinModel $join_model */
					$join_model = D('Core/Join');
					$result     = $join_model->alterRecord(['mid' => $mid, 'cid' => ['in', $cid_arr]], $data);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'batch_gift':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model = D('Core/Join');
					C('TOKEN_ON', false);
					$cid_arr     = explode(',', I('post.id', ''));
					$join_result = $join_model->alterRecord([
						'cid' => ['in', $cid_arr],
						'mid' => I('get.mid', 0, 'int')
					], ['gift_status' => 1]);

					return array_merge($join_result, [
						'__ajax__' => false
					]);
				break;
				default:
					return [
						'status'  => false,
						'message' => '参数错误'
					];
				break;
			}
		}

		public function createClientFromExcelData($excel_data, $map){
			$str_obj = new StringPlus();
			/** @var \Manager\Model\ClientModel $model */
			//$model = D('Client');
			/** @var \Core\Model\ClientModel $core_model */
			$core_model = D('Core/Client');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			//$table_column = $model->getColumn();
			/** @var \Core\Model\ColumnControlModel $column_control_model */
			$column_control_model = D('Core/ColumnControl');
			$column_list          = $column_control_model->findRecord(2, [
				'mid'  => I('get.mid', 0, 'int'),
				'view' => 1
			]);
			$table_column         = [];
			foreach($column_list as $col){
				$table_column[] = ['name' => $col['form'], 'desc' => $col['name']];
			}
			$count           = 0;
			$mid             = I('get.mid', 0, 'int');
			$cur_employee_id = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			$wechat_opt      = [];
			$error_str       = ''; // 记录出错的记录的参会人员名称
			// 遍历数据列表
			foreach($excel_data as $key1 => $line){
				$client_data        = []; // 参会人员创建数据
				$join_data          = [];
				$mobile             = '';
				$name               = '';
				$registration_date  = '';
				$service_consultant = null;
				$develop_consultant = null;
				$traffic_method     = '';
				// 遍历单条数据的字段
				foreach($line as $key2 => $val){
					$column_index = null;
					// 设定映射关系
					if(in_array($key2, $map['data'])){
						$map_index    = array_search($key2, $map['data']);
						$column_index = $map['column'][$map_index];
					}
					// 过滤数据
					switch(strtolower($table_column[$key2]['name'])){
						case 'type':
							$val = in_array($val, ['终端', '内部员工', '嘉宾', '陪同', '老总', '其他']) ? $val : '终端';
						break;
						case 'birthday':
							$val = $val ? date('Y-m-d', strtotime($val)) : null;
						break;
						case 'gender':
							$val = $val == '男' ? 1 : ($val == '女' ? 2 : 0);
						break;
						case 'name':
							$name = $val;
						break;
						case 'registration_date':
							$registration_date = $val;
						break;
						//						case 'develop_consultant':
						//							$service_consultant = $val;
						//							// 试图通过名称去查找员工表
						//							if($service_consultant){
						//								$record = $employee_model->findEmployee(1, [
						//									'keyword' => $service_consultant,
						//									'status'  => 1
						//								]);
						//								if($record){
						//									$service_consultant = $record['id'];
						//									$val                = $service_consultant;
						//								}
						//								else{
						//									$service_consultant = null;
						//									$val                = null;
						//								}
						//							}
						//						break;
						//						case 'service_consultant':
						//							$service_consultant = $val;
						//							// 试图通过名称去查找员工表
						//							if($service_consultant){
						//								$record = $employee_model->findEmployee(1, [
						//									'keyword' => $service_consultant,
						//									'status'  => 1
						//								]);
						//								if($record){
						//									$service_consultant = $record['id'];
						//									$val                = $service_consultant;
						//								}
						//								else{
						//									$service_consultant = null;
						//									$val                = null;
						//								}
						//							}
						//						break;
						case 'traffic_method':
							$traffic_method = $val;
						break;
						case 'mobile':
							$mobile = trim($val);
							$val    = $mobile;
						break;
						case 'is_new':
							if(!stripos($val, '新客') === -1) $val = 1;
							elseif(!stripos($val, '是') === -1) $val = 1;
							elseif(!stripos($val, '老客') === -1) $val = 0;
							elseif(!stripos($val, '否') === -1) $val = 0;
							else $val = '';
						break;
						case 'column1':
							$join_data['column1'] = $val;
						break;
						case 'column2':
							$join_data['column2'] = $val;
						break;
						case 'column3':
							$join_data['column3'] = $val;
						break;
						case 'column4':
							$join_data['column4'] = $val;
						break;
						case 'column5':
							$join_data['column5'] = $val;
						break;
						case 'column6':
							$join_data['column6'] = $val;
						break;
						case 'column7':
							$join_data['column7'] = $val;
						break;
						case 'column8':
							$join_data['column8'] = $val;
						break;
					}
					// 指定特殊列的值
					$client_data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$client_data['creatime']    = time();
					$client_data['password']    = $str_obj->makePassword(C('DEFAULT_CLIENT_PASSWORD'), $mobile);
					$client_data['pinyin_code'] = $str_obj->makePinyinCode($name);
					if($column_index === null){
						$client_data[$table_column[$key2]['name']] = $val;
						$join_data[$table_column[$key2]['name']]   = $val;
					}
					else{ // 存在映射规则的情况下字段一一对应后再存储映射的规则
						$client_data[$table_column[$column_index]['name']] = $val;
						$join_data[$table_column[$column_index]['name']]   = $val;
					}
				}
				// if($mobile == '' && !$mobile) continue; // 若手机号未填则略过该条数据
				// 判定是否存在该客户
				$exist_client                   = $core_model->isExist($mobile, $name);
				$join_data['mid']               = $mid;
				$join_data['creator']           = $cur_employee_id;
				$join_data['creatime']          = time();
				$join_data['registration_date'] = $registration_date ? date('Y-m-d', strtotime($registration_date)) : null;
				$join_data['traffic_method']    = $traffic_method;
				$join_data['registration_type'] = '线下';
				$join_data['status']            = 1;
				C('TOKEN_ON', false);
				// 若存在则用新数据覆盖旧数据同时设为老客
				if($exist_client){
					$core_model->alterClient(['id' => $exist_client['id']], array_merge($client_data, [
						'is_new' => 0,
						'status' => 1
					]));
					$join_data['cid'] = $exist_client['id'];
					$wechat_opt[]     = [
						'id'     => $exist_client['id'],
						'mobile' => $mobile
					];
				}
				// 若不存在则创建
				else{
					$client_result = $core_model->createClient($client_data);
					if($client_result['status']){
						$join_data['cid'] = $client_result['id'];
						$wechat_opt[]     = [
							'id'     => $client_result['id'],
							'mobile' => $mobile
						];
					}
				}
				if($join_data['cid']){
					// 判断是否有参会（可能被删除的情况）
					$exist_join_record = $join_model->isJoin($mid, $join_data['cid']);
					if($exist_join_record){
						// 若该场会议此人已经参会 则更新参会数据
						$alter_join_result = $join_model->alterRecord(['id' => $exist_join_record['id']], $join_data);
						if($alter_join_result['status']){
							$count++;
						}
						else $error_str .= "$client_data[name],";
					}
					else{
						// 若该场会议此人未参会 则创建参会记录
						$join_result = $join_model->createRecord($join_data);
						if($join_result['status']){
							$count++;
						}
						else $error_str .= "$client_data[name],";
					}
				}
			}
			$this->_asyncWechatInfo($wechat_opt);
			if($count === 0) return [
				'status'  => false,
				'message' => '没有导入任何数据'
			];
			elseif($count == count($excel_data)) return [
				'status'  => true,
				'message' => '全部导入成功'
			];
			else{
				$error_str = trim($error_str, ',');

				return [
					'status'  => true,
					'message' => "部分数据无需导入<br>
已导入：$count<br>
未导入：".(count($excel_data)-$count)." $error_str"
				];
			}
		}

		private function _asyncWechatInfo($client_list){
			set_time_limit(0);
			ignore_user_abort();
			$wechat_logic = new WxCorpLogic();
			$wx_list      = $wechat_logic->getAllUserList(); //查出wx接口获取的所有用户信息
			/** @var \Core\Model\WechatModel $wechat_model */
			$wechat_model = D('Core/Wechat');
			foreach($client_list as $key => $val){
				foreach($wx_list as $key2 => $val2){
					if($val['mobile'] == $val2['mobile'] && $val2['status'] != 4){
						$wechat_model->deleteRecord([
							'wid'   => $val2['userid'],
							'otype' => 1,
							'oid'   => $val['id'],
							'wtype' => 1
						]);
						$department = '';
						foreach($val2['department'] as $v3) $department .= $v3.',';
						$department         = trim($department, ',');
						$data               = [];
						$data['otype']      = 1;    //对象类型
						$data['oid']        = $val['id'];    //对象ID
						$data['wtype']      = 1;    //微信ID类型 企业号
						$data['department'] = $department;    //部门ID
						$data['wid']        = $val2['userid'];    //微信ID
						$data['mobile']     = $val2['mobile'];    //手机号码
						$data['avatar']     = $val2['avatar'];    //头像地址
						$data['gender']     = $val2['gender'];    //性别
						$data['is_follow']  = $val2['status'];    //是否关注
						$data['nickname']   = $val2['name'];    //昵称
						$data['creatime']   = time();    //创建时间
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$wechat_model->createRecord($data);    //插入数据
						break;
					}
				}
			}
		}
	}