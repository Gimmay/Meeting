<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 15:50
	 */
	namespace Manager\Logic;

	use Core\Logic\SMSLogic;
	use Core\Logic\WxCorpLogic;
	use Quasar\StringPlus;

	class ClientLogic extends ManagerLogic{

		public function _initialize(){
			parent::_initialize();
		}

		public function create($data){
			/** @var \Core\Model\ClientModel $model */
			/** @var \Core\Model\JoinModel $join_model */
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			/* 1.创建参会人员 */
			$str_obj             = new StringPlus();
			$model               = D('Core/Client');
			$data['status']      = $data['status'] == 1 ? 0 : (($data['status'] == 0) ? 1 : 1);
			$data['creatime']    = time();
			$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
			$data['password']    = $str_obj->makePassword(C('DEFAULT_CLIENT_PASSWORD'), $data['mobile']);
			$data['birthday']    = date('Y-m-d', strtotime($data['birthday']));
			if(I('post.city')) $data['address'] = I('post.province', '')."-".I('post.city', '')."-".I('post.area', '')."-".I('post.address_detail', '');
			else $data['address'] = I('post.province', '')."-".I('post.area', '')."-".I('post.address.detail', '');
			$result1 = $model->createClient($data);
			if(!$result1['status']){
				$exist_client = $model->findClient(1, ['name' => $data['name'], 'mobile' => $data['mobile']]);
				if(!$exist_client) return $result1;
				else $client_id = $exist_client['id'];
			}
			else $client_id = $result1['id'];
			/* 2.创建参会记录 */

			$mid               = I('get.mid', 0, 'int');
			$join_model                = D('Core/Join');
			$join_record = $join_model->findRecord(1,['mid'=>$mid,'cid'=>$client_id]);
			if($join_record){
				C('TOKEN_ON',false);
				$join_result=$join_model->alterRecord([$join_record['id']],['status'=>1]);
				if($join_result['status']){
					return ['status'=>true,'message'=>'创建成功'];
				}else{
					return ['status'=>false,'message'=>'创建失败'];
				}
			}else{
				$mobile                    = $data['mobile'];
				$registration_date         = date('Y-m-d', strtotime($data['registration_date']));
				$data                      = [];
				$data['cid']               = $client_id;
				$data['mid']               = $mid;
				$data['creatime']          = time();
				$data['creator']           = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
				$data['registration_date'] = $registration_date;
				C('TOKEN_ON', false);
				$result2 = $join_model->createRecord($data);
				if(!$result2['status']) return $result2;
				/* 3.试图根据手机号创建微信用户记录 */
				$weixin_logic     = new WxCorpLogic();
				$weixin_user_list = $weixin_logic->getAllUserList();
				foreach($weixin_user_list as $val){
					if($val['mobile'] == $mobile){
						$weixin_model = D('Core/WeixinID');
						$department   = '';
						foreach($val['department'] as $val2) $department .= $val2.',';
						$department         = trim($department, ',');
						$data               = [];
						$data['otype']      = 1;// 对象类型 这里为客户(参会人员)
						$data['oid']        = $client_id; // 对象ID
						$data['wtype']      = 1; // 微信ID类型 企业号
						$data['weixin_id']  = $val['userid']; // 微信ID
						$data['department'] = $department; // 部门ID
						$data['mobile']     = $val['mobile']; // 手机号码
						$data['avatar']     = $val['avatar']; // 头像地址
						$data['gender']     = $val['gender']; // 性别
						$data['nickname']   = $val['name']; // 昵称
						$data['creatime']   = time(); // 创建时间
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int'); // 当前创建者
						$weixin_model->createRecord($data); // 插入数据
						break;
					}
				}

				return $result2;
			}
		}

		public function alter($id, $data){
			/** @var \Core\Model\ClientModel $model */
			$model = D('Core/Client');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model          = D('Core/Join');
			$str_obj             = new StringPlus();
			$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
			if(I('post.city')) $data['address'] = I('post.province', '')."-".I('post.city', '')."-".I('post.area', '')."-".I('post.address_detail', '');
			else $data['address'] = I('post.province', '')."-".I('post.area', '')."-".I('post.address.detail', '');
			$result1                    = $model->alterClient([$id], $data);
			$join_record                = $join_model->findRecord(1, ['cid' => $id, 'mid' => I('get.mid', 0, 'int')]);
			$registration_date          = date('Y-m-d', strtotime($data['registration_date']));
			$data2['registration_date'] = $registration_date;
			C('TOKEN_ON', false);
			$result2 = $join_model->alterRecord([$join_record['id']], $data2);

			return ($result1['status'] || $result2['status']) ? [
				'status'  => true,
				'message' => '修改成功'
			] : ['status' => false, 'message' => '未做任何修改'];
		}

		public function setExtendColumnForAlter($info){
			/** @var \Core\Model\EmployeeModel $employee_model */
			/** @var \Core\Model\JoinModel $join_model */
			$meeting_id                      = I('get.mid', 0, 'int');
			$id                              = I('get.id', 0, 'int');
			$employee_model                  = D('Core/Employee');
			$join_model                      = D('Core/Join');
			$join_record                     = $join_model->findRecord(1, ['mid' => $meeting_id, 'cid' => $id]);
			$info['registration_date']       = $join_record['registration_date'];

			return $info;
		}

		public function alterColumnForExportExcel($list, $except_column = []){
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
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
				$list[$key1]['review_time'] = $val1['review_time'] ? date('Y-m-d H:i:s', $val1['review_time']) : 0;
				// 签到时间
				$list[$key1]['sign_time'] = $val1['sign_time'] ? date('Y-m-d H:i:s', $val1['sign_time']) : 0;
				// 签到类型
				switch($val1['sign_type']){
					case 0:
					default:
						$list[$key1]['sign_type'] = '未签到';
					break;
					case 1:
						$list[$key1]['sign_type'] = '手动签到';
					break;
					case 2:
						$list[$key1]['sign_type'] = '微信扫码签到';
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
			}
			$list = array_merge([
				[
					'name'               => '姓名',
					'gender'             => '性别',
					'mobile'             => '电话',
					'club'               => '会所',
					'birthday'           => '生日',
					'email'              => '邮箱',
					'title'              => '职务',
					'position'           => '职称',
					'address'            => '地址',
					'id_card_number'     => '身份证号',
					'develop_consultant' => '开拓顾问',
					'service_consultant' => '服务顾问',
					'accompany'          => '陪同',
					'accompany_mobile'   => '陪同手机',
					'comment'            => '备注',
					'column1'            => '备选字段1',
					'column2'            => '备选字段2',
					'column3'            => '备选字段3',
					'column4'            => '备选字段4',
					'column5'            => '备选字段5',
					'column6'            => '备选字段6',
					'column7'            => '备选字段7',
					'column8'            => '备选字段8',
					'registration_date'  => '报名时间',
					'review_status'      => '审核状态',
					'review_time'        => '审核时间',
					'sign_code'          => '签到码',
					'sign_time'          => '签到时间',
					'sign_status'        => '签到状态',
					'sign_type'          => '签到类型',
					'print_status'       => '打印状态',
					'print_times'        => '打印时间'
				]
			], $list);

			return $list;
		}

		public function getReceivablesList($list, $receivables = 1, $new = true){
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model = D('Core/Receivables');
			$new_list          = [];
			$mid               = I('get.mid', 0, 'int');
			foreach($list as $key => $val){
				$receivables_record = $receivables_model->findRecord(1, ['cid' => $val['cid'], 'mid' => $mid]);
				if($receivables == 1 && $receivables_record){
					if($val['receivables']) $val['receivables'] += $receivables_record['price'];
					else{
						$val['receivables'] = 0;
						$val['receivables'] += $receivables_record['price'];
					}
					if($new) $new_list[] = $val;
					else{
						if($list[$key]['receivables']) $val['receivables'] += $receivables_record['price'];
						else{
							$list[$key]['receivables'] = 0;
							$list[$key]['receivables'] += $receivables_record['price'];
						}
					}
				}
				if($receivables == 0 && !$receivables_record){
					$new_list[] = $val;
				}
			}

			return $new ? $new_list : $list;
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

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'save_excel_data':
					$upload_record_id = I('post.dbIndex', 0, 'int');
					$logic            = new ClientLogic();
					$map              = [
						'data'   => explode(',', I('post.excel')),
						'column' => explode(',', I('post.table'))
					];
					$result           = $logic->createClientFromExcel($upload_record_id, $map);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'review':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$client_id   = I('post.id', 0, 'int');
					$meeting_id  = I('post.mid', 0, 'int');
					$join_record = $join_model->findRecord(1, ['mid' => $meeting_id, 'cid' => $client_id]);
					C('TOKEN_ON', false);
					$result1 = $join_model->alterRecord([$join_record['id']], [
						'review_status' => 1,
						'review_time'   => time()
					]);
					if(!$result1['status']) return array_merge($result1, ['__ajax__' => true]);
					$join_logic = new JoinLogic();
					$result2    = $join_logic->makeQRCode([$client_id], ['mid' => $meeting_id]);

					return array_merge($result2, ['__ajax__' => true]);
				break;
				case 'anti_review':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$client_id   = I('post.id', 0, 'int');
					$meeting_id  = I('post.mid', 0, 'int');
					$join_record = $join_model->findRecord(1, ['mid' => $meeting_id, 'cid' => $client_id]);
					C('TOKEN_ON', false);
					$result = $join_model->alterRecord([$join_record['id']], ['review_status' => 2]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'multi_review': // 批量审核
					/** @var \Core\Model\JoinModel $join_model */
					$join_model    = D('Core/Join');
					$meeting_id    = I('get.mid');
					$client_id_arr = explode(',', I('post.id', ''));
					$join_id       = [];
					foreach($client_id_arr as $v){
						$join_record = $join_model->findRecord(1, ['cid' => $v, 'mid' => $meeting_id]);
						$join_id[]   = $join_record['id'];
					}
					C('TOKEN_ON', false);
					$result1 = $join_model->alterRecord($join_id, ['review_status' => 1, 'review_time' => time()]);
					if(!$result1['status']) return array_merge($result1, ['__ajax__' => false]);
					$join_logic = new JoinLogic();
					$result2    = $join_logic->makeQRCode($client_id_arr, ['mid' => $meeting_id]);

					return array_merge($result2, ['__ajax__' => false]);
				break;
				case 'multi_anti_review': // 批量反审核
					/** @var \Core\Model\JoinModel $join_model */
					$join_model    = D('Core/Join');
					$data['id']    = I('post.id', '');
					$meeting_id    = I('get.mid', 0, 'int');
					$client_id_arr = explode(',', $data['id']);
					$join_id       = [];
					foreach($client_id_arr as $v){
						$join_record = $join_model->findRecord(1, ['cid' => $v, 'mid' => $meeting_id]);
						$join_id[]   = $join_record['id'];
					}
					C('TOKEN_ON', false);
					$result = $join_model->alterRecord($join_id, ['review_status' => 2]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'sign': // 签到
					/** @var \Core\Model\JoinModel $join_model */
					$join_model    = D('Core/Join');
					$id            = I('post.id', 0, 'int');
					$meeting_id    = I('post.mid', 0, 'int');
					$sign_director = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$join_record   = $join_model->findRecord(1, [
						'cid' => $id,
						'mid' => $meeting_id
					]);
					if($join_record['review_status'] == 1){
						C('TOKEN_ON', false);
						$result = $join_model->alterRecord([$join_record['id']], [
							'sign_status'      => 1,
							'sign_time'        => time(),
							'sign_director_id' => $sign_director,
							'sign_type'        => 1
						]);
						if($result['status']){
							$message_logic = new MessageLogic();
							$message_logic->send($meeting_id, 1, [$id]);
						}

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '此客户信息还没有被审核', '__ajax__' => true];
				break; // 取消签到
				case 'anti_sign':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$id          = I('post.id', 0, 'int');
					$meeting_id  = I('post.mid', 0, 'int');
					$join_record = $join_model->findRecord(1, [
						'cid' => $id,
						'mid' => $meeting_id
					]);
					C('TOKEN_ON', false);
					$result = $join_model->alterRecord([$join_record['id']], [
						'sign_status' => 2,
						'sign_time'   => null,
						'sign_type'   => 0
					]);
					if($result['status']){
						$message_logic = new MessageLogic();
						$message_logic->send($meeting_id, 2, [$id]);
					}

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'multi_sign': // 批量签到
					/** @var \Core\Model\JoinModel $join_model */
					$join_model    = D('Core/Join');
					$message_logic = new MessageLogic();
					$id_arr        = explode(',', I('post.id', ''));
					$meeting_id    = I('get.mid', 0, 'int');
					$sign_director = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$count         = 0;
					$send_list     = [];
					foreach($id_arr as $val){
						$join_record = $join_model->findRecord(1, [
							'cid' => $val,
							'mid' => $meeting_id
						]);
						if($join_record['review_status'] == 1){
							C('TOKEN_ON', false);
							$result = $join_model->alterRecord([$join_record['id']], [
								'sign_status'      => 1,
								'sign_time'        => time(),
								'sign_director_id' => $sign_director,
								'sign_type'        => 1
							]);
							if($result['status']){
								$send_list[] = $val;
								$count++;
							}
						}
						if($count%50 == 0 && $count != 0){
							$message_logic->send($meeting_id, 1, $send_list);
							$send_list = [];
						}
					}
					if($count<50 && $count>0) $message_logic->send($meeting_id, 1, $send_list);
					if($count == 0) return ['status' => false, 'message' => '批量签到失败', '__ajax__' => false];
					elseif($count == count($id_arr)) return [
						'status'   => true,
						'message'  => '批量签到成功',
						'__ajax__' => false
					];
					else return ['status' => true, 'message' => '部分批量签到成功', '__ajax__' => false];
				break;
				case 'multi_anti_sign':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model    = D('Core/Join');
					$message_logic = new MessageLogic();
					$id_arr        = explode(',', I('post.id', ''));
					$meeting_id    = I('get.mid', 0, 'int');
					$count         = 0;
					$send_list     = [];
					foreach($id_arr as $val){
						$join_record = $join_model->findRecord(1, [
							'cid' => $val,
							'mid' => $meeting_id
						]);
						C('TOKEN_ON', false);
						$result = $join_model->alterRecord([$join_record['id']], [
							'sign_status' => 2,
							'sign_time'   => null,
							'sign_type'   => 0
						]);
						if($result['status']){
							$send_list[] = $val;
							$count++;
						}
						if($count%50 == 0 && $count != 0){
							$message_logic->send($meeting_id, 2, $send_list);
							$send_list = [];
						}
					}
					if($count<50 && $count>0) $message_logic->send($meeting_id, 1, $send_list);
					if($count == 0) return ['status' => false, 'message' => '批量取消签到失败', '__ajax__' => false];
					elseif($count == count($id_arr)) return [
						'status'   => true,
						'message'  => '批量取消签到成功',
						'__ajax__' => false
					];
					else return ['status' => true, 'message' => '部分批量取消签到成功', '__ajax__' => false];
				break;
				case 'delete';
					///** @var \Core\Model\ClientModel $model */
					//$model = D('Core/Client');
					/** @var \Core\Model\JoinModel $join_model */
					$join_model = D('Core/Join');
					//$id_arr     = explode(',', I('post.id'));
					$join_id_arr = explode(',', I('post.join_id'));
					//$result    = $model->deleteClient($id_arr);
					/* 监测是否已审核 */
					$delete_id_arr = [];
					foreach($join_id_arr as $val){
						$record = $join_model->findRecord(1, ['id' => $val]);
						if($record['review_status'] == 1) continue;
						$delete_id_arr[] = $val;
					}
					if(!$delete_id_arr) return ['status' => false, 'message' => '客户已审核不能删除', '__ajax__' => false];
					$result = $join_model->deleteRecord($delete_id_arr);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'send_message':
					$wxcorp_logic = new WxCorpLogic();
					/** @var \Core\Model\WeixinIDModel $weixin_model */
					/** @var \Core\Model\MeetingModel $meeting_model */
					/** @var \Core\Model\ClientModel $client_model */
					$client_id      = I('post.id', 0, 'int');
					$meeting_id     = I('post.mid', 0, 'int');
					$weixin_model   = D('Core/WeixinID');
					$meeting_model  = D('Core/Meeting');
					$client_model   = D('Core/Client');
					$sms_logic      = new SMSLogic();
					$record         = $client_model->findClient(1, ['id' => $client_id]);
					$weixin_record  = $weixin_model->findRecord(1, ['mobile' => $record['mobile']]);
					$meeting_record = $meeting_model->findMeeting(1, ['id' => $meeting_id]);
					$url            = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]/Mobile/Client/myCenter/id/$client_id/mid/$meeting_id";
					$result1        = $wxcorp_logic->sendMessage('news', [
						[
							'title'       => "$meeting_record[name]",
							'description' => "亲爱的$record[name]，请于$meeting_record[start_time]前点击\"查看全文\"后出示二维码提供给签到负责人进行签到",
							'url'         => $url
						]
					], ['user' => [$weixin_record['weixin_id']]]);
					$url            = getShortUrl($url); // 使用新浪的t.cn短地址
					$result2        = $sms_logic->send("亲爱的$record[name]，请于$meeting_record[start_time]前到$meeting_record[place]参加$meeting_record[name]。详情请查看$url", [$record['mobile']], true);
					if(!$result1['status']) return ['status' => false, 'message' => '微信推送信息失败', '__ajax__' => true];
					if(!$result2['status']) return ['status' => false, 'message' => '短信发送失败', '__ajax__' => true];

					return ['status' => true, 'message' => '发送消息成功', '__ajax__' => true];
				break;
				case 'multi_send_message':
					$wxcorp_logic = new WxCorpLogic();
					/** @var \Core\Model\WeixinIDModel $weixin_model */
					/** @var \Core\Model\MeetingModel $meeting_model */
					/** @var \Core\Model\ClientModel $client_model */
					$client_id     = I('post.id', '');
					$meeting_id    = I('get.mid', 0, 'int');
					$weixin_model  = D('Core/WeixinID');
					$meeting_model = D('Core/Meeting');
					$client_model  = D('Core/Client');
					$sms_logic     = new SMSLogic();
					$client_arr    = explode(',', $client_id);
					foreach($client_arr as $v){
						$record         = $client_model->findClient(1, ['id' => $v]);
						$weixin_record  = $weixin_model->findRecord(1, ['mobile' => $record['mobile']]);
						$meeting_record = $meeting_model->findMeeting(1, ['id' => $meeting_id]);
						$url            = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]/Mobile/Client/myCenter/id/$v/mid/$meeting_id";
						$wxcorp_logic->sendMessage('news', [
							[
								'title'       => "$meeting_record[name]", // todo just title
								'description' => "亲爱的$record[name]，请于$meeting_record[start_time]前点击\"查看全文\"后出示二维码提供给签到负责人进行签到",
								'url'         => $url
							]
						], ['user' => [$weixin_record['weixin_id']]]);
						$sms_logic->send("亲爱的$record[name]，请于$meeting_record[start_time]前到$meeting_record[place]参加$meeting_record[name]。详情请查看$url", [$record['mobile']], true);
					}

					return ['status' => true, 'massage' => '发送成功', '__ajax__' => false];
				break;
				case 'assign_sign_place':
					/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
					$join_sign_place_model = D('Core/JoinSignPlace');
					$cid                   = I('post.cid', 0, 'int');
					$sid_str               = I('post.sign_place', '');
					$sid_arr               = explode(',', $sid_str);
					$count                 = 0;
					C('TOKEN_ON', false);
					foreach($sid_arr as $key => $val){
						$result = $join_sign_place_model->createRecord([
							'cid'      => $cid,
							'sid'      => $val,
							'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
							'creatime' => time()
						]);
						if($result['status']) $count++;
						echo 1;
					}
					if($count == count($sid_arr)) return ['status' => true, 'message' => '分配成功', '__ajax__' => false];
					elseif($count == 0) return ['status' => false, 'message' => '分配失败', '__ajax__' => false];
					else return ['status' => true, 'message' => '部分分配成功', '__ajax__' => false];
				break;
				case 'batch_assign_sign_place':
					/** @var \Core\Model\JoinSignPlaceModel $join_sign_place_model */
					$join_sign_place_model = D('Core/JoinSignPlace');
					$cid_str               = I('post.cid', '');
					$sid_str               = I('post.sign_place', '');
					$cid_arr               = explode(',', $cid_str);
					$sid_arr               = explode(',', $sid_str);
					C('TOKEN_ON', false);
					$count = 0;
					foreach($cid_arr as $c_val){
						foreach($sid_arr as $s_val){
							$result = $join_sign_place_model->createRecord([
								'cid'      => $c_val,
								'sid'      => $s_val,
								'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
								'creatime' => time()
							]);
							if($result['status']) $count++;
						}
					}
					if($count == count($cid_arr)*count($sid_arr)) return [
						'status'   => true,
						'message'  => '分配成功',
						'__ajax__' => false
					];
					elseif($count == 0) return ['status' => false, 'message' => '分配失败', '__ajax__' => false];
					else return ['status' => true, 'message' => '部分分配成功', '__ajax__' => false];
				break;
				case 'get_receivables':
					/** @var \Core\Model\ReceivablesModel $receivables_model */
					$receivables_model = D('Core/Receivables');
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model = D('Core/Employee');
					$record         = $receivables_model->findRecord(2, [
						'cid' => I('post.cid', 0, 'int'),
						'mid' => I('post.mid', 0, 'int')
					]);
					foreach($record as $key => $val){
						$employee_record       = $employee_model->findEmployee(1, ['id' => $val['payee_id']]);
						$record[$key]['payee'] = $employee_record['name'];
					}

					return ['data' => $record, '__ajax__' => true];
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
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

		public function createClientFromExcelData($excel_data, $map){
			$str_obj = new StringPlus();
			/** @var \Manager\Model\ClientModel $model */
			$model = D('Client');
			/** @var \Core\Model\ClientModel $core_model */
			$core_model = D('Core/Client');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model = D('Core/Receivables');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model  = D('Core/Employee');
			$table_column    = $model->getColumn();
			$count           = 0;
			$mid             = I('get.mid', 0, 'int');
			$cur_employee_id = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			foreach($excel_data as $key1 => $line){
				$client_data        = [];
				$mobile             = '';
				$name               = '';
				$registration_date  = '';
				$invitor            = null;
				$service_consultant = null;
				$develop_consultant = null;
				$price              = 0;
				$traffic_method     = '';
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
						case 'mobile':
							$mobile = $val;
						break;
						case 'name':
							$name = $val;
						break;
						case 'registration_date':
							$registration_date = date('Y-m-d', strtotime($val));
						break;
						case 'inviter_id':
							$invitor = $val;
							if($invitor){
								$record = $employee_model->findEmployee(1, ['keyword' => $invitor]);
								if($record) $invitor = $record['id'];
								else $invitor = null;
							}
						break;
						case 'price':
							$price = $val;
						break;
						case 'traffic_method':
							$traffic_method = $val;
						break;
					}
					// 指定特殊列的值
					$client_data['creator']            = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$client_data['creatime']           = time();
					$client_data['password']           = $str_obj->makePassword(C('DEFAULT_CLIENT_PASSWORD'), $mobile);
					$client_data['pinyin_code']        = $str_obj->makePinyinCode($name);
					if($column_index === null){
						if(!in_array($table_column[$key2]['name'], ['registration_date'])) $client_data[$table_column[$key2]['name']] = $val;
					} // 按顺序指定缺省值
					else{ // 设定映射字段
						if(!in_array($table_column[$key2]['name'], ['registration_date'])) $client_data[$table_column[$key2]['name']] = $val;
						if(!in_array($table_column[$column_index]['name'], ['registration_date'])) $client_data[$table_column[$column_index]['name']] = $val;
					}
				}
				// 判定是否存在该客户
				$exist_client                   = $core_model->isExist($mobile, $name);
				$join_data                      = [];
				$join_data['creator']           = $cur_employee_id;
				$join_data['creatime']          = time();
				$join_data['registration_date'] = $registration_date;
				$join_data['traffic_method']    = $traffic_method;
				if($invitor != null) $join_data['inviter_id'] = $invitor;
				$join_data['mid'] = $mid;
				C('TOKEN_ON', false);
				if($exist_client) $join_data['cid'] = $exist_client['id'];
				else{
					$client_result = $core_model->createClient($client_data);
					if($client_result['status']) $join_data['cid'] = $client_result['id'];
				}
				if($join_data['cid']){
					$join_result = $join_model->createRecord($join_data);
					if($join_result['status']){
						$count++;
						if(((int)$price)>0) $receivables_model->createRecord([
							'cid'         => $join_data['cid'],
							'mid'         => $mid,
							'source_type' => 0,
							'creator'     => $cur_employee_id,
							'creatime'    => time(),
							'price'       => $price,
						]);
					}
				}
			}
			if($count === 0) return ['status' => false, 'message' => '没有导入任何数据'];
			elseif($count == count($excel_data)) return ['status' => true, 'message' => '全部导入成功'];
			else return [
				'status'  => true,
				'message' => "部分数据无需导入<br>
已导入：$count<br>
未导入：".(count($excel_data)-$count)
			];
		}
	}