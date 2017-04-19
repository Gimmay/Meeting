<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-15
	 * Time: 11:01
	 */
	namespace RoyalwissD\Logic;

	use CMS\Controller\CMS;
	use CMS\Logic\QRCodeLogic;
	use CMS\Logic\Session;
	use CMS\Logic\UploadLogic;
	use CMS\Logic\UserLogic;
	use General\Logic\ClientLogic;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;
	use RoyalwissD\Model\ReceivablesDetailModel;
	use RoyalwissD\Model\ReceivablesOrderModel;

	class ReceivablesLogic extends RoyalwissDLogic{
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
				case 'create':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.CREATE')) return [
						'status'   => false,
						'message'  => '您没有创建收款的权限',
						'__ajax__' => true
					];
					$post       = I('post.');
					$meeting_id = I('get.mid', 0, 'int');
					// 扣减项目库存
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model  = D('RoyalwissD/Project');
					$sell_parameter = ['id' => [], 'number' => []];
					foreach($post['name'] as $project_id){
						$sell_parameter['id'][]     = $project_id;
						$sell_parameter['number'][] = 1;
					}
					$result = $project_model->sellSafely($sell_parameter);
					if(!$result['status']) return array_merge($result, ['__ajax__' => true]);
					// 保存收款单据信息
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					$result2                 = $receivables_order_model->create([
						'order_number' => $post['orderNumber'],
						'mid'          => $meeting_id,
						'cid'          => $post['client'],
						'payee'        => $post['payeeID'],
						'place'        => $post['place'],
						'price'        => $post['totalAmount'] ? 0 : $post['totalAmount'],
						'time'         => $post['time'],
						'creatime'     => Time::getCurrentTime(),
						'creator'      => Session::getCurrentUser()
					]);
					// 保存收款项目信息
					/** @var \RoyalwissD\Model\ReceivablesProjectModel $receivables_project_model */
					$receivables_project_model = D('RoyalwissD/ReceivablesProject');
					$str_obj                   = new StringPlus();
					$batch_id                  = $str_obj->makeRandomString(32);
					$project_data              = [];
					$project_id_list           = [];
					foreach($post['name'] as $key => $project_id){
						if(!$project_id) continue;
						$project_id_list[] = $project_id;
						$project_data[]    = [
							'_batch_save_id'   => $batch_id,
							'_batch_column_id' => $key,
							'oid'              => $result2['id'],
							'mid'              => $meeting_id,
							'project_id'       => $project_id,
							'type'             => 1,
							'creatime'         => Time::getCurrentTime(),
							'creator'          => Session::getCurrentUser()
						];
					}
					$result3 = $receivables_project_model->addAll($project_data);
					if(!$result3) return ['status' => false, 'message' => '保存收款项目信息失败', '__ajax__' => true];
					// 获取刚才保存的项目收款信息
					$receivables_project_list = $receivables_project_model->where("_batch_save_id = '$batch_id' and mid = $meeting_id and oid = $result2[id]")->select();
					// 保存收款详情信息
					/** @var \RoyalwissD\Model\ReceivablesDetailModel $receivables_detail_model */
					$receivables_detail_model = D('RoyalwissD/ReceivablesDetail');
					$detail_data              = [];
					foreach($receivables_project_list as $receivables_project){
						$index = $receivables_project['_batch_column_id']+1;
						foreach($post["payMethod$index"] as $key => $val){
							$detail_data[] = [
								'mid'         => $meeting_id,
								'pid'         => $receivables_project['id'],
								'price'       => $post["price$index"][$key] == '' ? 0 : $post["price$index"][$key],
								'pay_method'  => $post["payMethod$index"][$key],
								'pos_machine' => $post["posMachine$index"][$key],
								'source'      => $post["source$index"][$key],
								'comment'     => $post["comment$index"][$key],
								'creatime'    => Time::getCurrentTime(),
								'creator'     => Session::getCurrentUser()
							];
						}
					}
					$result4 = $receivables_detail_model->addAll($detail_data);
					if(!$result4) return ['status' => false, 'message' => '保存收款详情信息失败', '__ajax__' => true];

					return [
						'status'   => true,
						'message'  => '收款成功',
						'__ajax__' => true,
						'nextPage' => U('manage', ['mid' => $meeting_id, 'id' => $result2['id']])
					];
				break;
				case 'create_client':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.CREATE_CLIENT')) return [
						'status'   => false,
						'message'  => '您没有创建客户的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
					$attendee_model       = D('RoyalwissD/Attendee');
					$general_client_logic = new ClientLogic();
					$str_obj              = new StringPlus();
					$qrcode_logic         = new QRCodeLogic();
					$post                 = I('post.');
					$meeting_id           = I('get.mid', 0, 'int');
					// 创建客户信息
					/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
					$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
					// 获取客户创建时的配置
					// 包含重复字段的判定条件 以及 发现重复记录的动作
					if($meeting_configure_model->fetch(['mid' => $meeting_id])){
						$meeting_configure_logic = new MeetingConfigureLogic();
						$meeting_configure       = $meeting_configure_model->getObject();
						$client_repeat_mode      = $meeting_configure_logic->decodeClientRepeatMode($meeting_configure['client_repeat_mode']);
						$client_repeat_action    = $meeting_configure['client_repeat_action'];
					}
					else{
						$client_repeat_action = $meeting_configure_model::CLIENT_REPEAT_ACTION_OVERRIDE; // 默认动作选择覆盖
					}
					$condition = [];
					if(isset($client_repeat_mode['clientName']) && $client_repeat_mode['clientName'] == 1) $condition['name'] = $post['name'];
					if(isset($client_repeat_mode['clientUnit']) && $client_repeat_mode['clientUnit'] == 1) $condition['unit'] = $post['unit'];
					if(isset($client_repeat_mode['clientMobile']) && $client_repeat_mode['clientMobile'] == 1) $condition['mobile'] = $post['mobile'];
					if($condition && $client_model->fetch($condition)){ // 已发现重复的用户数据
						$client_id = $client_model->getObjectID();
						$result    = ['status' => false, 'message' => '创建客户失败'];
						if($client_repeat_action == $meeting_configure_model::CLIENT_REPEAT_ACTION_OVERRIDE){ // 覆盖旧数据
							$result = $client_model->modify(['id' => $client_id], array_merge($post, [
								'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
								'unit_pinyin' => $str_obj->getPinyin($post['unit'], true, ''),
								'birthday'    => Time::isTimeFormat($post['birthday']),
								'password'    => $general_client_logic->makePassword($client_model->getDefaultPassword(), $post['mobile']),
								'comment'     => "(财务创建) $post[comment]"
							]));
							if($result['status']) $result['message'] = '该客户已存在并覆盖旧数据';
						}
						elseif($client_repeat_action == $meeting_configure_model::CLIENT_REPEAT_ACTION_SKIP){
							$result['message'] = '该客户已存在并跳过创建步骤';
						}
					}
					else{ // 创建用户基本数据
						$result    = $client_model->create(array_merge($post, [
							'creator'     => Session::getCurrentUser(),
							'creatime'    => Time::getCurrentTime(),
							'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
							'unit_pinyin' => $str_obj->getPinyin($post['unit'], true, ''),
							'birthday'    => Time::isTimeFormat($post['birthday']),
							'password'    => $general_client_logic->makePassword($client_model->getDefaultPassword(), $post['mobile']),
							'comment'     => "(财务创建) $post[comment]"
						]));
						$client_id = $result['id'];
					}
					if(!$result['status']) return array_merge($result, ['__ajax__' => true]);
					else{
						if($client_repeat_action == $meeting_configure_model::CLIENT_REPEAT_ACTION_OVERRIDE){
							// 构建会所数据
							/** @var \RoyalwissD\Model\UnitModel $unit_model */
							$unit_model = D('RoyalwissD/Unit');
							$option     = [];
							if(isset($_POST['unit_area'])) $option['area'] = $post['unit_area'];
							if(isset($_POST['unit_is_new'])) $option['is_new'] = $post['unit_is_new'];
							$result1 = $unit_model->create(array_merge($option, [
								'name'        => $post['unit'],
								'name_pinyin' => $str_obj->getPinyin($post['unit'], true, ''),
								'creatime'    => Time::getCurrentTime(),
								'creator'     => Session::getCurrentUser()
							]));
							if(!$result1['status']) return array_merge($result1, ['__ajax__' => true]);
						}
					}
					// 创建参会信息
					// todo 写入二维码页面
					$sign_qrcode   = U('', [
						'mid' => $meeting_id,
						'cid' => $client_id
					]);
					$sign_code     = $str_obj->makeRandomString(8);
					$attendee_data = array_merge($post, [
						'register_type'    => 1,
						'review_director'  => Session::getCurrentUser(),
						'review_status'    => 1,
						'review_time'      => Time::getCurrentTime(),
						'sign_qrcode'      => $qrcode_logic->make($sign_qrcode),
						'sign_code'        => $sign_code,
						'sign_code_qrcode' => $qrcode_logic->make($sign_code),
						'sign_status'      => 0,
						'sign_time'        => null,
						'sign_type'        => 0,
						'sign_director'    => null,
						'print_status'     => 0,
						'print_time'       => null,
						'gift_status'      => 0,
						'gift_time'        => null,
						'status'           => 1,
						'creatime'         => Time::getCurrentTime(),
						'creator'          => Session::getCurrentUser(),
						'cid'              => $client_id,
						'mid'              => $meeting_id
					]);
					$result2       = ['status' => false, 'message' => '创建参会记录失败'];
					// 根据配置决定重复数据是覆盖还是跳过
					if($attendee_model->fetch(['cid' => $client_id, 'mid' => $meeting_id])){
						if($client_repeat_action == $meeting_configure_model::CLIENT_REPEAT_ACTION_OVERRIDE){
							C('TOKEN_ON', false);
							$result2 = $attendee_model->create($attendee_data, true);
							if($result2['status']) $result2['message'] = '创建成功';
						}
						elseif($client_repeat_action == $meeting_configure_model::CLIENT_REPEAT_ACTION_SKIP){
							$result2['message'] = '该客户已存在并跳过创建步骤';
						}
					}
					else{
						$result2 = $attendee_model->create($attendee_data, true);
						if($result2['status']) $result2['message'] = '创建成功';
					}
					if(!$result2['status']) return array_merge($result2, ['__ajax__' => true]);

					return array_merge($result, [
						'__ajax__' => true,
						'nextPage' => U('', ['mid' => $meeting_id, 'cid' => $client_id])
					]);
				break;
				case 'get_project':
					$project_type_id = I('post.projectType', 0, 'int');
					$meeting_id      = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$record        = $project_model->getSelectedList($meeting_id, true, $project_type_id);

					return array_merge($record, ['__ajax__' => true]);
				break;
				case 'review':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.REVIEW')) return [
						'status'   => false,
						'message'  => '您没有审核收款的权限',
						'__ajax__' => true
					];
					$receivables_order_id_str = I('post.id', '');
					$receivables_order_id     = explode(',', $receivables_order_id_str);
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					$result                  = $receivables_order_model->modify([
						'id' => [
							'in',
							$receivables_order_id
						]
					], [
						'review_status'   => 1,
						'review_time'     => Time::getCurrentTime(),
						'review_director' => Session::getCurrentUser()
					]);
					if($result['status']) $result['message'] = '审核成功';
					else $result['message'] = '审核失败';

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'cancel_review':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.CANCEL_REVIEW')) return [
						'status'   => false,
						'message'  => '您没有取消审核收款的权限',
						'__ajax__' => true
					];
					$receivables_order_id_str = I('post.id', '');
					$receivables_order_id     = explode(',', $receivables_order_id_str);
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					$result                  = $receivables_order_model->modify([
						'id' => [
							'in',
							$receivables_order_id
						]
					], [
						'review_status'   => 2,
						'review_time'     => null,
						'review_director' => null
					]);
					if($result['status']) $result['message'] = '取消审核成功';
					else $result['message'] = '取消审核失败';

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用收款的权限',
						'__ajax__' => true
					];
					$receivables_order_id_str = I('post.id', '');
					$receivables_order_id     = explode(',', $receivables_order_id_str);
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					$result                  = $receivables_order_model->enable([
						'id' => [
							'in',
							$receivables_order_id
						]
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用收款的权限',
						'__ajax__' => true
					];
					$receivables_order_id_str = I('post.id', '');
					$receivables_order_id     = explode(',', $receivables_order_id_str);
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					$result                  = $receivables_order_model->disable([
						'id' => [
							'in',
							$receivables_order_id
						]
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除收款的权限',
						'__ajax__' => true
					];
					$receivables_order_id_str = I('post.id', '');
					$meeting_id               = I('get.mid', 0, 'int');
					$receivables_order_id     = explode(',', $receivables_order_id_str);
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					/** @var \RoyalwissD\Model\ReceivablesProjectModel $receivables_project_model */
					$receivables_project_model = D('RoyalwissD/ReceivablesProject');
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$project_list  = $receivables_project_model->where([
						'mid' => $meeting_id,
						'oid' => ['in', $receivables_order_id]
					])->select();
					$project_data  = [];
					foreach($project_list as $project_record){
						$project_data['id'][]     = $project_record['project_id'];
						$project_data['number'][] = 1;
					}
					$result0 = $project_model->refund($project_data);
					if(!$result0['status']) return array_merge($result0, ['__ajax__' => true]);
					$result = $receivables_order_model->drop(['id' => ['in', $receivables_order_id]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_order':
					$receivables_order_id = I('post.id', 0, 'int');
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					if(!$receivables_order_model->fetch(['id' => $receivables_order_id])) return [
						'status'   => false,
						'message'  => '找不到收款单据信息',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					$result       = $receivables_order_model->getObject();
					if(!$client_model->fetch(['id' => $result['cid']])) return [
						'status'   => false,
						'message'  => '找不到客户信息',
						'__ajax__' => true
					];
					$client_record = $client_model->getObject();
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					if(!$user_model->fetch(['id' => $result['payee']])) return [
						'status'   => false,
						'message'  => '找不到收款人信息',
						'__ajax__' => true
					];
					$payee_record         = $user_model->getObject();
					$result['client']     = $client_record['name'];
					$result['payee_code'] = $result['payee'];
					$result['payee']      = $payee_record['name'];

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify_order':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改收款的权限',
						'__ajax__' => true
					];
					$post                 = I('post.');
					$receivables_order_id = I('post.id', 0, 'int');
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					if(!$receivables_order_model->fetch(['id' => $receivables_order_id])) return [
						'status'   => false,
						'message'  => '找不到收款单据信息',
						'__ajax__' => true
					];
					unset($post['id']);
					$result = $receivables_order_model->modify(['id' => $receivables_order_id], $post);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_detail':
					$receivables_detail_id = I('post.id', 0, 'int');
					$meeting_id            = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					$receivables_detail      = $receivables_order_model->getList([
						$receivables_order_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $meeting_id,
						$receivables_order_model::CONTROL_COLUMN_PARAMETER_SELF['detailID']  => [
							'=',
							$receivables_detail_id
						]
					]);

					return array_merge($receivables_detail[0], ['__ajax__' => true]);
				break;
				case 'modify_detail':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改收款的权限',
						'__ajax__' => true
					];
					$post                  = I('post.');
					$receivables_detail_id = I('post.id', 0, 'int');
					/** @var \RoyalwissD\Model\ReceivablesProjectModel $receivables_project_model */
					$receivables_project_model = D('RoyalwissD/ReceivablesProject');
					/** @var \RoyalwissD\Model\ReceivablesDetailModel $receivables_detail_model */
					$receivables_detail_model = D('RoyalwissD/ReceivablesDetail');
					if(!$receivables_detail_model->fetch(['id' => $receivables_detail_id])) return [
						'status'   => false,
						'message'  => '找不到收款详情信息',
						'__ajax__' => true
					];
					$detail_record = $receivables_detail_model->getObject();
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					// 归还旧项目
					if(!$receivables_project_model->fetch(['id' => $detail_record['pid']])) return [
						'status'   => false,
						'message'  => '获取收款数据出错',
						'__ajax__' => true
					];
					$receivables_project = $receivables_project_model->getObject();
					$project_data        = [
						'id'     => [$receivables_project['project_id']],
						'number' => [1]
					];
					$result0             = $project_model->refund($project_data);
					if(!$result0) return array_merge($result0, ['__ajax__' => true]);
					// 扣减新项目
					$result0 = $project_model->sell($post['project_id']);
					if(!$result0) return array_merge($result0, ['__ajax__' => true]);
					// 修改信息
					$result = $receivables_project_model->modify(['id' => $detail_record['pid']], ['project_id' => $post['project_id']]);
					unset($post['id']);
					$result2 = $receivables_detail_model->modify(['id' => $receivables_detail_id], $post);
					if(!$result['status'] && !$result2['status']) return [
						'status'   => false,
						'message'  => '未做修改',
						'__ajax__' => true
					];
					else return [
						'status'   => true,
						'message'  => '修改成功',
						'__ajax__' => true
					];
				break;
				case 'get_print_data':
					$receivables_order_id = I('post.id', 0, 'int');
					$meeting_id           = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					$str_obj                 = new StringPlus();
					$receivables_detail_list = $receivables_order_model->getList([
						$receivables_order_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $meeting_id,
						$receivables_order_model::CONTROL_COLUMN_PARAMETER_SELF['orderID']   => [
							'=',
							$receivables_order_id
						]
					]);
					$result                  = [];
					$reflect                 = [
						'projectList'    => [],
						'projectIndex'   => [],
						'payMethodList'  => [],
						'payMethodIndex' => []
					];
					$total_price             = 0;
					$pay_method_count        = 0;
					$project_count           = 0;
					foreach($receivables_detail_list as $key => $detail){
						if($key == 0){
							$result['client']      = $detail['client'];
							$result['payee']       = $detail['payee'];
							$result['time']        = $detail['time'];
							$result['unit']        = $detail['unit'];
							$result['orderNumber'] = $detail['order_number'];
						}
						if(!in_array($detail['project_code'], $reflect['projectList'])){
							$reflect['projectList'][]                         = $detail['project_code'];
							$reflect['projectIndex'][$detail['project_code']] = $project_count++;
						}
						if(!in_array($detail['pay_method_code'], $reflect['payMethodList'])){
							$reflect['payMethodList'][]                            = $detail['pay_method_code'];
							$reflect['payMethodIndex'][$detail['pay_method_code']] = $pay_method_count++;
						}
						// 根据映射索引构建项目数据
						$project_i                     = $reflect['projectIndex'][$detail['project_code']];
						$project_comment               = $result['project'][$project_i]['comment'] == '' ? '' : $result['project'][$project_i]['comment'].', ';
						$project_price                 = $result['project'][$project_i]['price'];
						$result['project'][$project_i] = [
							'name'    => $detail['project'],
							'comment' => trim($project_comment.$detail['comment'], ', '),
							'price'   => ($project_price ? $project_price : 0)+$detail['price'],
							'type'    => $detail['project_type']
						];
						// 根据映射索引构建支付方式数据
						$pay_method_i                       = $reflect['payMethodIndex'][$detail['pay_method_code']];
						$pay_method_price                   = $result['payMethod'][$pay_method_i]['price'];
						$result['payMethod'][$pay_method_i] = [
							'name'  => $detail['pay_method'],
							'price' => ($pay_method_price ? $pay_method_price : 0)+$detail['price']
						];
						$total_price += $detail['price'];
					}
					$result['price']      = $total_price;
					$result['price_word'] = $str_obj->parseNumberToUpperWord($total_price);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'upload_receivables_order_logo':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.CONFIGURE')) return [
						'status'   => false,
						'message'  => '您没有配置收款的权限',
						'__ajax__' => true
					];
					$meeting_id   = I('get.mid', 0, 'int');
					$upload_logic = new UploadLogic($meeting_id);
					$result       = $upload_logic->upload($_FILES, '/Logo/');
					if(!$result['status']) return ['status' => false, 'message' => '上传失败', '__ajax__' => true];
					/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
					$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
					$result2                 = $meeting_configure_model->modify(['mid' => $meeting_id], ['receivables_order_logo' => $result['data']['filePath']]);
					if(!$result2['status']) return ['status' => false, 'message' => '保存失败', '__ajax__' => true];

					return array_merge($result, ['__ajax__' => true,]);
				break;
				case 'refund_project':
					// todo 完善？
					$post              = I('post.');
					$order_id          = I('post.oid', 0, 'int');
					$project_record_id = I('post.pid', 0, 'int');
					$meeting_id        = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\ReceivablesProjectModel $receivables_project_model */
					$receivables_project_model = D('RoyalwissD/ReceivablesProject');
					$project_record_list       = $receivables_project_model->where([
						'oid' => $order_id,
						'mid' => $meeting_id
					])->select();
					$project_data              = [];
					foreach($project_record_list as $project_record){
						$project_data['id'][]     = $project_record['project_id'];
						$project_data['number'][] = 1;
					}
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$result        = $project_model->refund($project_data);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'copy':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.COPY')) return [
						'status'   => false,
						'message'  => '您没有复制收款的权限',
						'__ajax__' => true
					];
					$order_id   = I('post.id', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
					$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
					if(!$receivables_order_model->fetch([
						'mid' => $meeting_id,
						'id'  => $order_id
					])
					) return [
						'status'   => false,
						'message'  => '找不到收款单据信息',
						'__ajax__' => true
					];
					$order_record = $receivables_order_model->getObject();
					/** @var \RoyalwissD\Model\ReceivablesProjectModel $receivables_project_model */
					$receivables_project_model  = D('RoyalwissD/ReceivablesProject');
					$receivables_project_list   = $receivables_project_model->where([
						'oid' => $order_id,
						'mid' => $meeting_id
					])->select();
					$sell_parameter             = ['id' => [], 'number' => []];
					$receivables_project_id_arr = [];
					foreach($receivables_project_list as $val){
						$sell_parameter['id'][]       = $val['project_id'];
						$sell_parameter['number'][]   = 1;
						$receivables_project_id_arr[] = $val['id'];
					}
					/** @var \RoyalwissD\Model\ReceivablesDetailModel $receivables_detail_model */
					$receivables_detail_model = D('RoyalwissD/ReceivablesDetail');
					$receivables_detail_list  = $receivables_detail_model->where([
						'mid' => $meeting_id,
						'pid' => ['in', $receivables_project_id_arr]
					])->select();
					// 扣减库存
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$result        = $project_model->sellSafely($sell_parameter);
					if(!$result['status']) return array_merge($result, ['__ajax__' => true]);
					// 保存单据信息
					unset($order_record['id']);
					$order_record['order_number']  = "$order_record[order_number] (copy)";
					$order_record['review_status'] = 0;
					$order_record['creatime']      = Time::getCurrentTime();
					$order_record['creator']       = Session::getCurrentUser();
					$result2                       = $receivables_order_model->create($order_record);
					if(!$result2['status']) return array_merge($result2, ['__ajax__' => true]);
					// 保存收款项目信息
					$str_obj             = new StringPlus();
					$project_record_list = [];
					$batch_id            = $str_obj->makeRandomString(32);
					foreach($receivables_project_list as $key => $project_record){
						foreach($receivables_detail_list as $k => $detail_record){
							if($detail_record['pid'] == $project_record['id']) $receivables_detail_list[$k]['_copy_column_id'] = $key;
						}
						unset($project_record['id']);
						$project_record['oid']              = $result2['id'];
						$project_record['creator']          = Session::getCurrentUser();
						$project_record['creatime']         = Time::getCurrentTime();
						$project_record['_batch_save_id']   = $batch_id;
						$project_record['_batch_column_id'] = $key;
						$project_record_list[]              = $project_record;
					}
					$result3 = $receivables_project_model->addAll($project_record_list);
					if(!$result3) return ['status' => false, 'message' => '复制收款项目记录失败'];
					// 保存收款详情信息
					$saved_project_record_list = $receivables_project_model->where("_batch_save_id = '$batch_id' and mid = $meeting_id and oid = $result2[id]")->select();
					$detail_record_list        = [];
					foreach($receivables_detail_list as $detail_record){
						foreach($saved_project_record_list as $project_record){
							if($project_record['_batch_column_id'] == $detail_record['_copy_column_id']){
								$detail_record['pid'] = $project_record['id'];
								break;
							}
						}
						unset($detail_record['id']);
						$detail_record['creator']  = Session::getCurrentUser();
						$detail_record['creatime'] = Time::getCurrentTime();
						$detail_record_list[]      = $detail_record;
					}
					$result4 = $receivables_detail_model->addAll($detail_record_list);
					if(!$result4) return ['status' => false, 'message' => '复制收款详情记录失败'];

					return [
						'status'   => true,
						'message'  => '复制成功',
						'__ajax__' => true
					];
				break;
				case 'reset_and_order_column':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.MANAGE_LIST_COLUMN')) return [
						'status'   => false,
						'message'  => '您没有控制列表字段的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesColumnControlModel $receivables_column_control_model */
					$receivables_column_control_model = D('RoyalwissD/ReceivablesColumnControl');
					$meeting_id                       = I('get.mid', 0, 'int');
					$post                             = I('post.');
					// 锁表
					$receivables_column_control_model->lock('read');
					$receivables_column_control_model->lock('write');
					// 删除旧数据
					$receivables_column_control_model->where([
						'mid'    => $meeting_id,
						'action' => $receivables_column_control_model::ACTION_READ
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
							'action'   => $receivables_column_control_model::ACTION_READ,
							'creator'  => Session::getCurrentUser(),
							'creatime' => Time::getCurrentTime(),
						];
					}
					$result = $receivables_column_control_model->addAll($data, [
						'mid'    => $meeting_id,
						'action' => $receivables_column_control_model::ACTION_READ
					], true);
					// 解锁
					$receivables_column_control_model->unlock();

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
				case 'set_search_configure':
					if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.MANAGE_SEARCH_COLUMN')) return [
						'status'   => false,
						'message'  => '您没有管理搜索字段的权限',
						'__ajax__' => true
					];
					$column_str = I('post.column', '');
					$column     = explode(',', $column_str);
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\ReceivablesColumnControlModel $receivables_column_control_model */
					$receivables_column_control_model = D('RoyalwissD/ReceivablesColumnControl');
					$receivables_column_control_model->where([
						'mid'    => $meeting_id,
						'action' => $receivables_column_control_model::ACTION_SEARCH
					])->save(['search' => 0]);
					if($column_str != ''){
						$result = $receivables_column_control_model->where([
							'mid'    => $meeting_id,
							'action' => $receivables_column_control_model::ACTION_SEARCH,
							'form'   => ['in', $column]
						])->save(['search' => 1]);

						return $result ? [
							'status'   => true,
							'message'  => '设定成功',
							'__ajax__' => true
						] : [
							'status'   => false,
							'message'  => '设定失败',
							'__ajax__' => true
						];
					}
					else return [
						'status'   => true,
						'message'  => '设定成功',
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
				case 'manage':
					$result = [];
					// 合并单据号的映射变量
					$order_reflect = [
						'orderID' => [], // 已存在的单据号ID数组
						'index'   => [] // 映射表
					];
					$get           = $data['urlParam'];
					$data          = $data['list'];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					// 若指定了客户ID的情况
					if(isset($get[ReceivablesOrderModel::CONTROL_COLUMN_PARAMETER_SELF['clientID']])) $client_id = $get[ReceivablesOrderModel::CONTROL_COLUMN_PARAMETER_SELF['clientID']];
					// 若指定了收据详情ID的情况
					if(isset($get[ReceivablesOrderModel::CONTROL_COLUMN_PARAMETER_SELF['detailID']])) $detail_id = $get[ReceivablesOrderModel::CONTROL_COLUMN_PARAMETER_SELF['detailID']];
					// 若指定了收据订单ID的情况
					if(isset($get[ReceivablesOrderModel::CONTROL_COLUMN_PARAMETER_SELF['orderID']])) $url_order_id = $get[ReceivablesOrderModel::CONTROL_COLUMN_PARAMETER_SELF['orderID']];
					// 若指定了收据审核状态的情况
					if(isset($get[ReceivablesOrderModel::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus']])) $review_status = $get[ReceivablesOrderModel::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus']];
					for($i = 0, $key = 0; $i<count($data); $i++){
						// 1、筛选数据
						if(isset($keyword)){
							/** @var \RoyalwissD\Model\ReceivablesColumnControlModel $receivables_column_control_model */
							$receivables_column_control_model = D('RoyalwissD/ReceivablesColumnControl');
							$search_list                      = $receivables_column_control_model->getSearchColumn($get['mid'], true);
							$found                            = 0;
							foreach($search_list as $value){
								if($found == 0 && stripos($data[$i][$value['form']], $keyword) !== false) $found = 1;
							}
							if(count($search_list) == 0) $found = 1;
							if($found == 0) continue;
						}
						if(isset($client_id) && $client_id != $data[$i]['cid']) continue;
						if(isset($detail_id) && $detail_id != $data[$i]['did']) continue;
						if(isset($url_order_id) && $url_order_id != $data[$i]['oid']) continue;
						if(isset($review_status) && $review_status != $data[$i]['review_status']) continue;
						$order_id   = $data[$i]['id'];
						$project_id = $data[$i]['project_code'];
						if(!in_array($data[$i]['id'], $order_reflect['orderID'])){
							$order_reflect['orderID'][]                        = $order_id;
							$order_reflect['index'][$order_id]['i']            = $key;
							$order_reflect['index'][$order_id]['project']      = [];
							$order_reflect['index'][$order_id]['projectIndex'] = [];
							$key++;
						}
						$index = $order_reflect['index'][$order_id]['i'];
						// 统计同一个单据下的项目数
						if(!in_array($project_id, $order_reflect['index'][$order_id]['project'])){
							$order_reflect['index'][$order_id]['project'][] = $project_id;
							if(!isset($result[$index]['projectCount'])){
								$result[$index]['projectCount'] = 0;
							}
							// 如果是新的项目 则自增统计数
							$result[$index]['projectCount']++;
							// 初始化项目列表的合并数
							$data[$i]['_merge_column'] = 1;
							// 构建项目合并判定的回溯映射表 可由项目ID映射到data列表的数字下标
							$order_reflect['index'][$order_id]['projectIndex'][$project_id] = $i;
						}
						else{
							$merge_first_project_index = $order_reflect['index'][$order_id]['projectIndex'][$project_id];
							$data[$merge_first_project_index]['_merge_column']++;
						}
						// 统计项目相同合并计数
						// 统计同一个收据下的总金额
						if(!isset($result[$index]['price'])) $result[$index]['price'] = 0;
						$result[$index]['price'] += $data[$i]['price'];
						$data[$i]['source_code']              = $data[$i]['source'];
						$data[$i]['source']                   = ReceivablesDetailModel::RECEIVABLES_SOURCE[$data[$i]['source']];
						$result[$index]['list'][]             = &$data[$i];
						$result[$index]['id']                 = $data[$i]['id'];
						$result[$index]['order_number']       = $data[$i]['order_number'];
						$result[$index]['client']             = $data[$i]['client'];
						$result[$index]['cid']                = $data[$i]['cid'];
						$result[$index]['payee']              = $data[$i]['payee'];
						$result[$index]['place']              = $data[$i]['place'];
						$result[$index]['time']               = $data[$i]['time'];
						$result[$index]['unit']               = $data[$i]['unit'];
						$result[$index]['review_status_code'] = $data[$i]['review_status'];
						$result[$index]['review_time']        = $data[$i]['review_time'];
						$result[$index]['review_director']    = $data[$i]['review_director'];
						$result[$index]['review_status']      = ReceivablesOrderModel::REVIEW_STATUS[$data[$i]['review_status']];
						$result[$index]['status_code']        = $data[$i]['status'];
						$result[$index]['status']             = GeneralModel::STATUS[$data[$i]['status']];
						$result[$index]['creatime']           = $data[$i]['creatime'];
						$result[$index]['creator']            = $data[$i]['creator'];
					}

					return $result;
				break;
				case 'manage:statistics':
					$result       = [
						'totalPrice'       => 0,
						'pagePrice'        => 0,
						'pageCount'        => count($data['list']),
						'totalCount'       => count($data['totalList']),
						'reviewedCount'    => 0,
						'notReviewedCount' => 0,
					];
					$handler_list = [];
					foreach($data['totalList'] as $val){
						if($val['review_status'] == 1) $result['totalPrice'] += $val['price'];
						if(!in_array($val['id'], $handler_list)){
							if($val['review_status'] == 1) $result['reviewedCount']++;
							else $result['notReviewedCount']++;
							$handler_list[] = $val['id'];
						}
					}
					foreach($data['list'] as $val){
						if($val['review_status_code'] == 1) $result['pagePrice'] += $val['price'];
					}
					$result['totalCount'] = count($handler_list);

					return $result;
				break;
				case 'column_setting:search':
					$result = '';
					foreach($data as $val){
						if($val['search'] == 1) $result .= "$val[name] / ";
					}

					return trim($result, ' / ');
				break;
				default:
					return $data;
				break;
			}
		}
	}