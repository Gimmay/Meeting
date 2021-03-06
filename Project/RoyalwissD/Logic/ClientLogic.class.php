<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-11
	 * Time: 16:58
	 */

	namespace RoyalwissD\Logic;

	use CMS\Controller\CMS;
	use CMS\Logic\ExcelLogic;
	use CMS\Logic\QRCodeLogic;
	use CMS\Logic\Session;
	use CMS\Logic\UploadLogic;
	use CMS\Logic\UserLogic;
	use General\Logic\ClientLogic as GeneralClientLogic;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\ExternalInterface\Wechat\EnterpriseAccountLibrary;
	use Quasar\Utility\StringPlus;
	use RoyalwissD\Model\AttendeeModel;
	use RoyalwissD\Model\ClientModel;
	use RoyalwissD\Model\UnitModel;

	class ClientLogic extends RoyalwissDLogic{
		/** 新纪录的识别时间 */
		const NEW_DATA_TIME = 300;

		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'multi_create':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MULTI_CREATE')) return [
						'status'   => false,
						'message'  => '您没批量创建客户的权限',
						'__ajax__' => true
					];
					$number = I('post.number', 0, 'int');
					if($number == 0) return [
						'status'   => false,
						'message'  => '数量错误',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
					$attendee_model       = D('RoyalwissD/Attendee');
					$general_client_logic = new GeneralClientLogic();
					$str_obj              = new StringPlus();
					$post                 = I('post.');
					$meeting_id           = I('get.mid', 0, 'int');
					$unit                 = $post['unit'];
					$unit_pinyin          = $str_obj->getPinyin($unit, true, '');
					$batch_id             = $str_obj->makeRandomString(64);
					$client_data          = $attendee_data = [];
					// 构建会所数据
					/** @var \RoyalwissD\Model\UnitModel $unit_model */
					$unit_model = D('RoyalwissD/Unit');
					$option     = [];
					if(isset($_POST['unit_area'])) $option['area'] = $post['unit_area'];
					if(isset($_POST['unit_is_new'])) $option['is_new'] = $post['unit_is_new'];
					$result1 = $unit_model->create(array_merge($option, [
						'name'        => $unit,
						'name_pinyin' => $unit_pinyin,
						'creatime'    => Time::getCurrentTime(),
						'creator'     => Session::getCurrentUser()
					]));
					if(!$result1['status']) return array_merge($result1, ['__ajax__' => true]);
					// 构建客户数据
					for($i = 1; $i<=$number; $i++){
						$mobile        = $str_obj->makeRandomString(13);
						$client_data[] = array_merge($post, [
							'creatime'       => Time::getCurrentTime(),
							'creator'        => Session::getCurrentUser(),
							'name'           => "$unit ($i)",
							'name_pinyin'    => $str_obj->getPinyin("$unit ($i)", true, ''),
							'unit_pinyin'    => $unit_pinyin,
							'mobile'         => $mobile,
							'password'       => $general_client_logic->makePassword($client_model->getDefaultPassword(), $mobile),
							'_batch_save_id' => $batch_id
						]);
					}
					// 保存数据
					$result = $client_model->addAll($client_data);
					if(!$result) return [
						'status'   => false,
						'message'  => '保存客户数据失败',
						'__ajax__' => true
					];
					/** @var array $saved_client_list 获取刚才插入的客户数据 */
					$saved_client_list = $client_model->where("_batch_save_id = '$batch_id'")->field('id, _batch_column_id, _batch_save_id')->select();
					foreach($saved_client_list as $saved_client){
						$attendee_data[] = array_merge($post, [
							'creatime' => Time::getCurrentTime(),
							'creator'  => Session::getCurrentUser(),
							'mid'      => $meeting_id,
							'cid'      => $saved_client['id']
						]);
					}
					// 开始保存参会数据
					$result2 = $attendee_model->addAll($attendee_data);
					if(!$result2) return [
						'status'   => false,
						'message'  => '保存参会数据失败',
						'__ajax__' => true
					];

					return [
						'status'   => true,
						'message'  => '创建成功',
						'__ajax__' => true,
						'nextPage' => U('', ['mid' => $meeting_id, urlencode(CMS::URL_CONTROL_PARAMETER['keyword'])])
					];
				break;
				case 'copy':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.COPY')) return [
						'status'   => false,
						'message'  => '您没复制客户的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$client_id  = I('post.cid', 0, 'int');
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
					$attendee_model       = D('RoyalwissD/Attendee');
					$str_obj              = new StringPlus();
					$general_client_logic = new GeneralClientLogic();
					if(!($client_model->fetch(['id' => $client_id]))) return [
						'status'   => false,
						'message'  => '找不到客户',
						'__ajax__' => true
					];
					$client = $client_model->getObject();
					if(!($attendee_model->fetch([
						'cid' => $client_id,
						'mid' => $meeting_id
					]))
					) return ['status' => false, 'message' => '找不到参会信息', '__ajax__' => true];
					$attendee              = $attendee_model->getObject();
					$client['name']        = "$client[name] (复制)";
					$client['name_pinyin'] = $str_obj->getPinyin($client['name'], true, '');
					$client['unit']        = "$client[unit]";
					$client['unit_pinyin'] = $str_obj->getPinyin($client['unit'], true, '');
					$client['mobile']      = "$client[mobile] (copy)";
					$client['creatime']    = Time::getCurrentTime();
					$client['creator']     = Session::getCurrentUser();
					$client['password']    = $general_client_logic->makePassword($client_model->getDefaultPassword(), $client['mobile']);
					unset($client['id']);
					$result = $client_model->create($client);
					if(!$result['status']) return array_merge($result, ['__ajax__' => true]);
					$attendee['register_type']    = 1;
					$attendee['review_director']  = null;
					$attendee['review_status']    = 0;
					$attendee['review_time']      = null;
					$attendee['sign_qrcode']      = null;
					$attendee['sign_code']        = null;
					$attendee['sign_code_qrcode'] = null;
					$attendee['sign_status']      = 0;
					$attendee['sign_time']        = null;
					$attendee['sign_type']        = 0;
					$attendee['sign_director']    = null;
					$attendee['print_status']     = 0;
					$attendee['print_time']       = null;
					$attendee['gift_status']      = 0;
					$attendee['gift_time']        = null;
					$attendee['status']           = 1;
					$attendee['creatime']         = Time::getCurrentTime();
					$attendee['creator']          = Session::getCurrentUser();
					$attendee['cid']              = $result['id'];
					unset($attendee['id']);
					$result2 = $attendee_model->create($attendee);
					if(!$result2['status']) return array_merge($result2, ['__ajax__' => true]);
					// 审核该客户
					$client_logic = new ClientLogic();
					$result3      = $client_logic->review($result['id'], $meeting_id);
					if(!$result3['status']) return array_merge($result3, ['__ajax__' => true]);

					return [
						'status'   => true,
						'message'  => '复制成功',
						'__ajax__' => true,
						'nextPage' => U('RoyalwissD/Client/modify', [
							'id'   => $result['id'],
							'mid'  => $meeting_id,
							'copy' => true
						])
					];
				break;
				case 'modify':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MODIFY')) return [
						'status'   => false,
						'message'  => '您没修改客户的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
					$attendee_model       = D('RoyalwissD/Attendee');
					$general_client_logic = new GeneralClientLogic();
					$str_obj              = new StringPlus();
					$post                 = I('post.');
					$meeting_id           = I('get.mid', 0, 'int');
					$client_id            = I('get.id', 0, 'int');
					/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
					$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
					// 获取客户创建时的配置
					// 包含重复字段的判定条件
					if($meeting_configure_model->fetch(['mid' => $meeting_id])){
						$meeting_configure_logic = new MeetingConfigureLogic();
						$meeting_configure       = $meeting_configure_model->getObject();
						$client_repeat_mode      = $meeting_configure_logic->decodeClientRepeatMode($meeting_configure['client_repeat_mode']);
						$client_repeat_action    = $meeting_configure['client_repeat_action'];
					}
					$condition = [];
					if(isset($client_repeat_mode['clientName']) && $client_repeat_mode['clientName'] == 1) $condition['name'] = $post['name'];
					if(isset($client_repeat_mode['clientUnit']) && $client_repeat_mode['clientUnit'] == 1) $condition['unit'] = $post['unit'];
					if(isset($client_repeat_mode['clientMobile']) && $client_repeat_mode['clientMobile'] == 1) $condition['mobile'] = $post['mobile'];
					if($condition && $client_model->fetch($condition)){ // 已发现重复的用户数据
						$other_client_id = $client_model->getObjectID();
						if($other_client_id != $client_id) return [
							'status'   => false,
							'message'  => '修改后的客户资料和其他客户信息重复了',
							'__ajax__' => true
						];
					}
					if(isset($client_repeat_action) && $client_repeat_action == $meeting_configure_model::CLIENT_REPEAT_ACTION_OVERRIDE){
						// 保存会所数据
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
					// 保存客户数据
					$result = $client_model->modify(['id' => $client_id], array_merge($post, [
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'unit_pinyin' => $str_obj->getPinyin($post['unit'], true, ''),
						'birthday'    => Time::isTimeFormat($post['birthday']),
						'password'    => $general_client_logic->makePassword($client_model->getDefaultPassword(), $post['mobile'])
					]));
					// 创建参会信息
					$result2      = $attendee_model->modify(['cid' => $client_id, 'mid' => $meeting_id], $post);
					$redirect_url = isset($_GET['copy']) ? U('RoyalwissD/Client/manage', [
						'mid' => $meeting_id,
						'cid' => $client_id
					]) : $_POST['redirect'];
					if(!$result2['status'] && !$result['status']) return [
						'status'   => false,
						'message'  => '未做修改',
						'__ajax__' => true,
						'nextPage' => $redirect_url
					];
					else return [
						'status'   => true,
						'message'  => '修改成功',
						'__ajax__' => true,
						'nextPage' => $redirect_url
					];
				break;
				case 'create':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.CREATE')) return [
						'status'   => false,
						'message'  => '您没创建客户的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
					$attendee_model       = D('RoyalwissD/Attendee');
					$general_client_logic = new GeneralClientLogic();
					$str_obj              = new StringPlus();
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
								'password'    => $general_client_logic->makePassword($client_model->getDefaultPassword(), $post['mobile'])
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
							'password'    => $general_client_logic->makePassword($client_model->getDefaultPassword(), $post['mobile'])
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
					$attendee_data = array_merge($post, [
						'register_type'    => 1,
						'review_director'  => null,
						'review_status'    => 0,
						'review_time'      => null,
						'sign_qrcode'      => null,
						'sign_code'        => null,
						'sign_code_qrcode' => null,
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

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'show_table_column': // 显示字段
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MANAGE_COLUMN')) return [
						'status'   => false,
						'message'  => '您没有管理字段的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
					$client_column_control_model = D('RoyalwissD/ClientColumnControl');
					$meeting_id                  = I('get.mid', 0, 'int');
					$column_name                 = I('post.name', '');
					$result                      = $client_column_control_model->modify([
						'mid'    => $meeting_id,
						'form'   => $column_name,
						'action' => $client_column_control_model::ACTION_WRITE
					], ['view' => 1]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'hide_table_column': // 隐藏字段
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MANAGE_COLUMN')) return [
						'status'   => false,
						'message'  => '您没有管理字段的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
					$client_column_control_model = D('RoyalwissD/ClientColumnControl');
					$meeting_id                  = I('get.mid', 0, 'int');
					$column_name                 = I('post.name', '');
					$result                      = $client_column_control_model->modify([
						'mid'    => $meeting_id,
						'form'   => $column_name,
						'action' => $client_column_control_model::ACTION_WRITE
					], ['view' => 0]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify_table_column': // 是否必填项
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MANAGE_COLUMN')) return [
						'status'   => false,
						'message'  => '您没有管理字段的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
					$client_column_control_model = D('RoyalwissD/ClientColumnControl');
					$meeting_id                  = I('get.mid', 0, 'int');
					$column_name                 = I('post.name', '');
					$must                        = isset($_POST['is_necessary']) && $_POST['is_necessary'] ? 1 : 0;
					$result                      = $client_column_control_model->modify([
						'mid'    => $meeting_id,
						'form'   => $column_name,
						'action' => $client_column_control_model::ACTION_WRITE
					], ['must' => $must]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'create_table_column': // 新增自定义字段
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MANAGE_COLUMN')) return [
						'status'   => false,
						'message'  => '您没有管理字段的权限',
						'__ajax__' => true
					];
					// 新增数据库字段
					/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
					$client_column_control_model = D('RoyalwissD/ClientColumnControl');
					$post                        = I('post.');
					$meeting_id                  = I('get.mid', 0, 'int');
					$data                        = [
						'type'     => addslashes($post['field_type']),
						'typeSize' => (int)$post['field_size'],
						'comment'  => addslashes($post['field_name'])
					];
					/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
					$attendee_model = D('RoyalwissD/Attendee');
					$result         = $attendee_model->addColumn($data);
					if($result['status']){
						$last_column_index = $attendee_model->getLastCustomColumnIndex();
						$table_name        = 'attendee';
						$data              = [
							'mid'      => $meeting_id,
							'code'     => strtoupper(MODULE_NAME."-$table_name-".$attendee_model::CUSTOM_COLUMN.$last_column_index),
							'form'     => $attendee_model::CUSTOM_COLUMN.$last_column_index,
							'table'    => $table_name,
							'name'     => $post['field_name'],
							'view'     => 0,
							'creatime' => Time::getCurrentTime(),
							'creator'  => Session::getCurrentUser()
						];
						$result2           = $client_column_control_model->create(array_merge($data, [
							'action' => $client_column_control_model::ACTION_WRITE,
							'must'   => 0
						]));
						if(!$result2['status']) $result['message'] = $result2['message'];
						$result3 = $client_column_control_model->create(array_merge($data, ['action' => $client_column_control_model::ACTION_READ]));
						if(!$result3['status']) $result['message'] = $result3['message'];
						$result4 = $client_column_control_model->create(array_merge($data, [
							'action' => $client_column_control_model::ACTION_SEARCH,
							'search' => 0
						]));
						if(!$result4['status']) $result['message'] = $result4['message'];
					}

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'set_configure': // 设定重复记录判定设定
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.REPEAT_CONFIGURE')) return [
						'status'   => false,
						'message'  => '您没有配置的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
					$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
					$meeting_configure_logic = new MeetingConfigureLogic();
					$meeting_id              = I('get.mid', 0, 'int');
					$post                    = I('post.');
					$result                  = $meeting_configure_model->modify(['mid' => $meeting_id], [
						'client_repeat_mode'   => $meeting_configure_logic->encodeClientRepeatMode($post['client_repeat_mode_name'], $post['client_repeat_mode_unit'], $post['client_repeat_mode_mobile']),
						'client_repeat_action' => $post['client_repeat_action']
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'reset_and_order_column':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MANAGE_LIST_COLUMN')) return [
						'status'   => false,
						'message'  => '您没有控制列表字段的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
					$client_column_control_model = D('RoyalwissD/ClientColumnControl');
					$meeting_id                  = I('get.mid', 0, 'int');
					$post                        = I('post.');
					// 锁表
					$client_column_control_model->lock('read');
					$client_column_control_model->lock('write');
					// 删除旧数据
					$client_column_control_model->where([
						'mid'    => $meeting_id,
						'action' => $client_column_control_model::ACTION_READ
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
							'action'   => $client_column_control_model::ACTION_READ,
							'creator'  => Session::getCurrentUser(),
							'creatime' => Time::getCurrentTime(),
						];
					}
					$result = $client_column_control_model->addAll($data, [
						'mid'    => $meeting_id,
						'action' => $client_column_control_model::ACTION_READ
					], true);
					// 解锁
					$client_column_control_model->unlock();

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
				case 'import_excel': // 上传Excel文件
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.IMPORT')) return [
						'status'   => false,
						'message'  => '您没有导入的权限',
						'__ajax__' => true
					];
					$meeting_id   = I('get.mid', 0, 'int');
					$upload_logic = new UploadLogic($meeting_id);
					$result       = $upload_logic->upload($_FILES, '/Excel/');

					return array_merge($result, [
						'__ajax__' => true,
						'nextPage' => U('importColumnContrast', [
							'mid'   => $meeting_id,
							'logID' => $result['data']['logID']
						])
					]);
				break;
				case 'save_excel_data': // 保存导入的Excel数据
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.IMPORT')) return [
						'status'   => false,
						'message'  => '您没有导入的权限',
						'__ajax__' => true
					];
					set_time_limit(0);
					$str_obj = new StringPlus();
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					$excel_logic  = new ExcelLogic();
					/** @var \General\Model\UploadLogModel $upload_log_model */
					$upload_log_model = D('General/UploadLog');
					$log_id           = I('get.logID', 0, 'int');
					$meeting_id       = I('get.mid', 0, 'int');
					$map              = I('post.map');
					$batch_id         = $str_obj->makeRandomString(64);
					/**
					 * 构建返回结果
					 *
					 * @param bool   $status               结果状态
					 * @param string $message              结果信息
					 * @param array  $excel_data           导入Excel数据
					 * @param array  $saved_client_list    已保存的客户数据列表
					 * @param array  $head_data            导入Excel的表头
					 * @param array  $original_repeat_data 重复客户数据
					 *
					 * @return array
					 */
					$makeResult = function ($status, $message, $excel_data, $saved_client_list, $head_data, $original_repeat_data) use ($str_obj, $excel_logic, $batch_id, $meeting_id){
						// 保存日志数据
						/** @var \RoyalwissD\Model\ClientImportResultModel $client_import_result_model */
						$client_import_result_model = D('RoyalwissD/ClientImportResult');
						$total_count                = count($excel_data);
						$success_count              = count($saved_client_list);
						$repeat_count               = count($original_repeat_data);
						$failure_count              = $total_count-$success_count-$repeat_count;
						$failure_count              = $failure_count<0 ? 0 : $failure_count;
						C('TOKEN_ON', false);
						if($repeat_count>0) $repeat_data_save_result = $excel_logic->writeCustomData(array_merge([$head_data], $original_repeat_data), [
							'fileName'    => $str_obj->makeRandomString(12),
							'title'       => '客户导入重复数据',
							'subject'     => '客户导入重复数据',
							'description' => '客户批量导入后的失败数据',
							'download'    => false,
							'savePath'    => UploadLogic::UPLOAD_PATH.'/Excel-Repeat/',
							'hasHead'     => true
						]);
						else $repeat_data_save_result = ['status' => false];
						if($failure_count>0) $failure_data_save_result = $excel_logic->writeCustomData([$head_data], [
							'fileName'    => $str_obj->makeRandomString(12),
							'title'       => '客户导入失败数据',
							'subject'     => '客户导入失败数据',
							'description' => '客户批量导入后的失败数据',
							'download'    => false,
							'savePath'    => UploadLogic::UPLOAD_PATH.'/Excel-Failure/',
							'hasHead'     => true
						]);
						else $failure_data_save_result = ['status' => false];
						$result3 = $client_import_result_model->create([
							'batch_save_id'          => $batch_id,
							'mid'                    => $meeting_id,
							'total_count'            => $total_count,
							'success_count'          => $success_count,
							'repeat_count'           => $repeat_count,
							'failure_count'          => $failure_count,
							'failure_data_file_path' => $failure_data_save_result['status'] ? $failure_data_save_result['filePath'] : null,
							'repeat_data_file_path'  => $repeat_data_save_result['status'] ? $repeat_data_save_result['filePath'] : null,
							'creatime'               => Time::getCurrentTime(),
							'creator'                => Session::getCurrentUser()
						]);
						if(!$result3['status']) return [
							'status'   => false,
							'message'  => $result3['message'],
							'__ajax__' => true
						];

						return [
							'status'   => $status,
							'message'  => $message,
							'resultID' => $result3['id'],
							'nextPage' => U('importResult', ['resultID' => $result3['id'], 'mid' => $meeting_id]),
							'__ajax__' => true
						];
					};
					if($upload_log_model->fetch(['id' => $log_id])){
						$upload_record = $upload_log_model->getObject();
						// 获取可控制的客户写入字段
						/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
						$client_column_control_model = D('RoyalwissD/ClientColumnControl');
						$column_list                 = $client_column_control_model->getClientControlledColumn($meeting_id, $client_column_control_model::ACTION_WRITE);
						// 获取导入的Excel数据
						$file_path   = trim($upload_record['save_path'], '/');
						$read_result = $excel_logic->readCustomData($file_path);
						$excel_data  = $read_result['data']['body'];
						// 获取数据库全表客户数据
						$database_client_list = $client_model->field(['name', 'unit', 'mobile', 'id'])->select();
						// 获取重复字段判定配置
						/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
						$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
						if($meeting_configure_model->fetch(['mid' => $meeting_id])){
							$meeting_configure_logic = new MeetingConfigureLogic();
							$meeting_configure       = $meeting_configure_model->getObject();
							$client_repeat_mode      = $meeting_configure_logic->decodeClientRepeatMode($meeting_configure['client_repeat_mode']);
							$client_repeat_action    = $meeting_configure['client_repeat_action'];
						}
						else{
							$client_repeat_action = $meeting_configure_model::CLIENT_REPEAT_ACTION_OVERRIDE; // 默认动作选择覆盖
						}
						// 构建客户数据
						/** @var array $client_data 批量插入的客户数据 */
						$client_data = [];
						/** @var array $repeat_data 重复的客户数据 */
						$repeat_data = $original_repeat_data = [];
						/** @var array $attendee_data 参会数据 */
						$attendee_data = [];
						/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
						$attendee_model = D('RoyalwissD/Attendee');
						$client_logic   = new ClientLogic();
						$unit_data      = [];
						foreach($excel_data as $index => $client){
							$temp_client_data   = [
								'_batch_save_id'   => $batch_id,
								'_batch_column_id' => $index,
								'creator'          => Session::getCurrentUser(),
								'creatime'         => Time::getCurrentTime(),
							];
							$temp_attendee_data = [
								'mid'              => $meeting_id,
								'creator'          => Session::getCurrentUser(),
								'creatime'         => Time::getCurrentTime(),
								'register_type'    => 0,
								'_batch_column_id' => $index
							];
							// 字段映射
							foreach($client as $key => $val){
								$save_column_name = $column_list[$map[$key]]['form']; // 字段映射后这列数据保存在数据库的列名
								if($save_column_name == null) continue;
								$kv = $this->_convertColumnValue($save_column_name, $val); // 特殊处理字段数据
								$kv ? $temp_client_data = array_merge($temp_client_data, $kv) : null; // 拼接数据
								if($client_logic->isCustomColumn($save_column_name)) $temp_attendee_data = array_merge($temp_attendee_data, $kv); // 自定义字段
								elseif(in_array($save_column_name, [
									'receivables',
									'consumption'
								])) $temp_attendee_data = array_merge($temp_attendee_data, $kv); // 特殊的收款/消耗字段
							}
							// 开始判定数据是否重复
							$is_repeat = false;
							/** @var array $repeat_flag 标识判定客户数据重复的字段状态 */
							foreach($database_client_list as $key => $val){
								$repeat_flag = [
									'name'   => 0,
									'unit'   => 0,
									'mobile' => 0,
								];
								if(isset($client_repeat_mode['clientName']) && $client_repeat_mode['clientName'] == 1){
									if($val['name'] === $temp_client_data['name']) $repeat_flag['name'] = 1;
								}
								if(isset($client_repeat_mode['clientUnit']) && $client_repeat_mode['clientUnit'] == 1){
									if($val['unit'] === $temp_client_data['unit']) $repeat_flag['unit'] = 1;
								}
								if(isset($client_repeat_mode['clientMobile']) && $client_repeat_mode['clientMobile'] == 1){
									if($val['mobile'] === $temp_client_data['mobile']) $repeat_flag['mobile'] = 1;
								}
								if(($repeat_flag['name'] == $client_repeat_mode['clientName']) && ($repeat_flag['unit'] == $client_repeat_mode['clientUnit']) && ($repeat_flag['mobile'] == $client_repeat_mode['clientMobile']) && !($client_repeat_mode['clientName'] == 0 && $client_repeat_mode['clientMobile'] == 0 && $client_repeat_mode['clientUnit'] == 0)){ // 必须满足重复数据的判定规则并且设定了判定字段
									$is_repeat              = true;
									$temp_client_data['id'] = $val['id']; // Warning：根据插入主键ID数据并根据此字段做覆盖操作！
									break;
								}
								else $temp_client_data['id'] = '_default';
							}
							// 重复了不过设定了覆盖配置：写入
							if($is_repeat && $client_repeat_action == $meeting_configure_model::CLIENT_REPEAT_ACTION_OVERRIDE){
								$client_data[]          = $temp_client_data;
								$repeat_data[]          = $temp_client_data;
								$attendee_data[]        = $temp_attendee_data;
								$original_repeat_data[] = $client;
								// 会所数据
								if(isset($client['unit'])){
									$temp_unit_data                = [];
									$temp_unit_data['name']        = $client['unit'];
									$temp_unit_data['name_pinyin'] = $str_obj->getPinyin($client['unit'], true, '');
									if(isset($client['unit_area'])) $temp_unit_data['area'] = $client['unit_area'];
									else $temp_unit_data['area'] = $client['unit_area'];
									if(isset($temp_client_data['unit_is_new'])){
										switch($temp_client_data['unit_is_new']){
											case '是':
											case '新店':
												$temp_client_data['unit_is_new'] = 1;
											break;
											case '否':
											case '不是':
											case '老店':
											default:
												$temp_client_data['unit_is_new'] = 9;
											break;
										}
										$temp_unit_data['is_new'] = $temp_client_data['unit_is_new'];
									}
									else $temp_unit_data['is_new'] = 9;
									$unit_data[] = array_merge($temp_unit_data, [
										'creator'  => Session::getCurrentUser(),
										'creatime' => Time::getCurrentTime()
									]);
								}
							}
							// 重复了并设定了跳过
							elseif($is_repeat && $client_repeat_action == $meeting_configure_model::CLIENT_REPEAT_ACTION_SKIP){
								$repeat_data[]          = $temp_client_data;
								$original_repeat_data[] = $client;
							}
							// 没有重复：正常写入
							elseif(!$is_repeat){
								$client_data[]   = $temp_client_data;
								$attendee_data[] = $temp_attendee_data;
								// 会所数据
								if(isset($temp_client_data['unit'])){
									$temp_unit_data                = [];
									$temp_unit_data['name']        = $temp_client_data['unit'];
									$temp_unit_data['name_pinyin'] = $str_obj->getPinyin($temp_client_data['unit'], true, '');
									if(isset($temp_client_data['unit_area'])) $temp_unit_data['area'] = $temp_client_data['unit_area'];
									if(isset($temp_client_data['unit_is_new'])){
										switch($temp_client_data['unit_is_new']){
											case '是':
											case '新店':
												$temp_client_data['unit_is_new'] = 1;
											break;
											case '否':
											case '不是':
											case '老店':
											default:
												$temp_client_data['unit_is_new'] = 0;
											break;
										}
										$temp_unit_data['is_new'] = $temp_client_data['unit_is_new'];
									}
									$unit_data[] = array_merge($temp_unit_data, [
										'creator'  => Session::getCurrentUser(),
										'creatime' => Time::getCurrentTime()
									]);
								}
							}
						}
						// 保存会所数据
						if($unit_data){
							/** @var \RoyalwissD\Model\UnitModel $unit_model */
							$unit_model = D('RoyalwissD/Unit');
							$result1    = $unit_model->addAll($unit_data, [], true);
							if(!$result1) return [
								'status'   => false,
								'message'  => '保存会所数据失败',
								'__ajax__' => true
							];
						}
						// 开始保存客户数据
						if(!$client_data && $repeat_data) return $makeResult(false, '数据全部重复并跳过创建步骤', $excel_data, [], $read_result['data']['head'], $original_repeat_data);
						$result = $client_model->addAll($client_data, [], true);
						if(!$result) return $makeResult(false, '保存客户数据失败', $excel_data, [], $read_result['data']['head'], $original_repeat_data);
						// 构建参会记录的数据
						/** @var array $saved_client_list 获取刚才插入的客户数据 */
						$saved_client_list = $client_model->where("_batch_save_id = '$batch_id'")->field('id, _batch_column_id, _batch_save_id')->select();
						/** @var array $true_attendee_data 真正需要保存的参会数据（有可能excel表中本身就存在重复记录的情况） */
						$true_attendee_data = [];
						foreach($saved_client_list as $saved_client){
							foreach($attendee_data as $key => $attendee){
								if($saved_client['_batch_column_id'] == $attendee['_batch_column_id'] && $saved_client['_batch_save_id'] == $batch_id){
									$attendee['cid']      = $saved_client['id'];
									$true_attendee_data[] = $attendee;
									break;
								}
							}
						}
						// 开始保存参会数据
						$result2 = $attendee_model->addAll($true_attendee_data, [], true);
						if(!$result2) return $makeResult(false, '保存参会数据失败', $excel_data, $saved_client_list, $read_result['data']['head'], $original_repeat_data);

						return $makeResult(true, '导入成功', $excel_data, $saved_client_list, $read_result['data']['head'], $original_repeat_data);
					}
					else{
						return [
							'status'   => false,
							'message'  => '找不到上传数据',
							'__ajax__' => true
						];
					}
				break;
				case 'delete':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除客户的权限',
						'__ajax__' => true
					];
					$meeting_id    = I('get.mid', 0, 'int');
					$client_id_str = I('post.id', '');
					$client_id     = explode(',', $client_id_str);
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					if(count($client_id)>0){
						$client_list = $client_model->getList([
							$client_model::CONTROL_COLUMN_PARAMETER_SELF['clientID']  => [
								'in',
								'('.implode(',', $client_id).')'
							],
							$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $meeting_id
						]);
						foreach($client_list as $client){
							if($client['sign_status'] == 1) return [
								'status'   => false,
								'message'  => '删除失败：存在已签到的客户',
								'__ajax__' => true
							];
							if($client['review_status'] == 1) return [
								'status'   => false,
								'message'  => '删除失败：存在已审核的客户',
								'__ajax__' => true
							];
						}
					}
					else return [
						'status'   => false,
						'message'  => '未选择任何客户',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
					$attendee_model = D('RoyalwissD/Attendee');
					$result         = $attendee_model->drop(['cid' => ['in', $client_id], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'sign':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.SIGN')) return [
						'status'   => false,
						'message'  => '您没有签到的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$client_id  = I('post.id', '');
					$result     = $this->sign($client_id, $meeting_id, 1);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'review':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.REVIEW')) return [
						'status'   => false,
						'message'  => '您没有审核的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$client_id  = I('post.id', '');
					$result     = $this->review($client_id, $meeting_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'cancel_review':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.CANCEL_REVIEW')) return [
						'status'   => false,
						'message'  => '您没有取消审核的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$client_id  = I('post.id', '');
					$result     = $this->review($client_id, $meeting_id, true);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'cancel_sign':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.CANCEL_SIGN')) return [
						'status'   => false,
						'message'  => '您没有取消签到的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$client_id  = I('post.id', '');
					$result     = $this->sign($client_id, $meeting_id, 0, true);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'gift':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.GIFT')) return [
						'status'   => false,
						'message'  => '您没有领取礼品的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$client_id  = I('post.id', '');
					$result     = $this->gift($client_id, $meeting_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'refund_gift':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.REFUND_GIFT')) return [
						'status'   => false,
						'message'  => '您没有退还礼品的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$client_id  = I('post.id', '');
					$result     = $this->gift($client_id, $meeting_id, true);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify_column':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改客户的权限',
						'__ajax__' => true
					];
					$client_id    = I('post.cid', 0, 'int');
					$meeting_id   = I('get.mid', 0, 'int');
					$table        = I('post._table', '');
					$column_name  = I('post._column', '');
					$column_value = I("post.$column_name", '');
					if($table == 'attendee'){
						/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
						$attendee_model = D('RoyalwissD/Attendee');
						$result         = $attendee_model->modify([
							'cid' => $client_id,
							'mid' => $meeting_id
						], [$column_name => $column_value]);
						$return_value   = $column_value;
					}
					elseif($table == 'client'){
						/** @var \RoyalwissD\Model\ClientModel $client_model */
						$client_model = D('RoyalwissD/Client');
						$kv           = $this->_convertColumnValue($column_name, $column_value, true);
						$str_obj      = new StringPlus();
						if($kv == null) return ['status' => false, 'message' => '字段名称错误', '__ajax__' => true];
						$return_value = $kv['data']['content'];
						// 去重判断-获取客户信息
						if(!($client_model->fetch(['id' => $client_id]))) return [
							'status'   => false,
							'message'  => '找不到该客户',
							'__ajax__' => true
						];
						$client = $client_model->getObject();
						// 获取客户创建时的配置
						// 包含重复字段的判定条件
						/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
						$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
						if($meeting_configure_model->fetch(['mid' => $meeting_id])){
							$meeting_configure_logic = new MeetingConfigureLogic();
							$meeting_configure       = $meeting_configure_model->getObject();
							$client_repeat_mode      = $meeting_configure_logic->decodeClientRepeatMode($meeting_configure['client_repeat_mode']);
						}
						$condition = [];
						if(isset($client_repeat_mode['clientName']) && $client_repeat_mode['clientName'] == 1){
							$condition['name'] = $client['name'];
							if($column_name == 'name') $condition['name'] = $column_value;
						}
						if(isset($client_repeat_mode['clientUnit']) && $client_repeat_mode['clientUnit'] == 1){
							$condition['unit'] = $client['unit'];
							if($column_name == 'unit') $condition['unit'] = $column_value;
						}
						if(isset($client_repeat_mode['clientMobile']) && $client_repeat_mode['clientMobile'] == 1){
							$condition['mobile'] = $client['mobile'];
							if($column_name == 'mobile') $condition['mobile'] = $column_value;
						}
						// 已发现重复的用户数据
						if($condition && $client_model->fetch($condition)){
							$other_client_id = $client_model->getObjectID();
							if($other_client_id != $client_id) return [
								'status'   => false,
								'message'  => '修改后的客户资料和其他客户信息重复了',
								'__ajax__' => true
							];
						}
						// 特殊处理会所字段
						if(isset($kv['save']['unit_is_new'])){
							/** @var \RoyalwissD\Model\UnitModel $unit_model */
							$unit_model = D('RoyalwissD/Unit');
							if($unit_model->fetch(['name' => $client['unit']])){
								$unit_data           = $unit_model->getObject();
								$unit_data['is_new'] = $kv['save']['unit_is_new'];
								$result1             = $unit_model->create($unit_data);
								if(!$result1['status']) return array_merge($result1, ['__ajax__' => true]);
							}
						}
						if(isset($kv['save']['unit_area'])){
							/** @var \RoyalwissD\Model\UnitModel $unit_model */
							$unit_model = D('RoyalwissD/Unit');
							if($unit_model->fetch(['name' => $client['unit']])){
								$unit_data         = $unit_model->getObject();
								$unit_data['area'] = $kv['save']['unit_area'];
								$result1           = $unit_model->create($unit_data);
								if(!$result1['status']) return array_merge($result1, ['__ajax__' => true]);
							}
						}
						if(isset($kv['save']['unit'])){
							/** @var \RoyalwissD\Model\UnitModel $unit_model */
							$unit_model = D('RoyalwissD/Unit');
							if($unit_model->fetch(['name' => $client['unit']])){
								$unit_data                = $unit_model->getObject();
								$unit_data['name']        = $kv['save']['unit'];
								$unit_data['name_pinyin'] = $str_obj->getPinyin($kv['save']['unit'], true, '');
							}
							else $unit_data = [
								'name'        => $kv['save']['unit'],
								'name_pinyin' => $str_obj->getPinyin($kv['save']['unit'], true, ''),
								'creatime'    => Time::getCurrentTime(),
								'creator'     => Session::getCurrentUser()
							];
							$result1 = $unit_model->create($unit_data);
							if(!$result1['status']) return array_merge($result1, ['__ajax__' => true]);
						}
						$result = $client_model->modify(['id' => $client_id], $kv['save']);
					}
					else return ['status' => false, 'message' => '参数错误', '__ajax__' => true];

					return array_merge($result, ['__ajax__' => true, 'value' => $return_value]);
				break;
				case 'disable':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用客户的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
					$attendee_model = D('RoyalwissD/Attendee');
					$meeting_id     = I('get.mid', 0, 'int');
					$client_id_str  = I('post.id', '');
					$client_id      = explode(',', $client_id_str);
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					if(count($client_id)>0){
						$client_list = $client_model->getList([
							$client_model::CONTROL_COLUMN_PARAMETER_SELF['clientID']  => [
								'in',
								'('.implode(',', $client_id).')'
							],
							$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $meeting_id
						]);
						foreach($client_list as $client){
							if($client['sign_status'] == 1) return [
								'status'   => false,
								'message'  => '禁用失败：存在已签到的客户',
								'__ajax__' => true
							];
							if($client['review_status'] == 1) return [
								'status'   => false,
								'message'  => '禁用失败：存在已审核的客户',
								'__ajax__' => true
							];
						}
					}
					else return [
						'status'   => false,
						'message'  => '未选择任何客户',
						'__ajax__' => true
					];
					$result = $attendee_model->disable(['cid' => ['in', $client_id], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用客户的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
					$attendee_model = D('RoyalwissD/Attendee');
					$meeting_id     = I('get.mid', 0, 'int');
					$client_id_str  = I('post.id', '');
					$client_id      = explode(',', $client_id_str);
					$result         = $attendee_model->enable(['cid' => ['in', $client_id], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_detail':
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
					$client_column_control_model = D('RoyalwissD/ClientColumnControl');
					$meeting_id                  = I('get.mid', 0, 'int');
					$client_id                   = I('post.id', 0, 'int');
					$column_list                 = $client_column_control_model->getClientControlledColumn($meeting_id, $client_column_control_model::ACTION_READ);
					$column_head                 = $column_name_list = [];
					$list                        = $client_model->getList(array_merge([
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $meeting_id,
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['clientID']  => ['=', $client_id]
					]));
					foreach($column_list as $column){
						if(!$column['view']) continue;
						$column_head[]      = $column['name'];
						$column_name_list[] = $column['form'];
					}
					$list = $this->setData('downloadData', [
						'dataList'    => $list,
						'columnValue' => $column_name_list,
						'columnName'  => $column_head
					]);

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'assign_group':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MULTI_ASSIGN_GROUP')) return [
						'status'   => false,
						'message'  => '您没有分配组员的权限',
						'__ajax__' => true
					];
					$meeting_id    = I('get.mid', 0, 'int');
					$group_id      = I('post.gid', 0, 'int');
					$client_id_str = I('post.cid', '');
					$client_id     = explode(',', $client_id_str);
					// 判断是否审核签到
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					$client_list  = $client_model->getList([
						$client_model::CONTROL_COLUMN_PARAMETER['status']            => ['=', 1],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus'] => ['=', 1],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['signStatus']   => ['=', 1],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['clientID']     => [
							'in',
							'('.implode(',', $client_id).')'
						],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID']    => $meeting_id,
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['type']         => true
					]);
					if(count($client_list)<count($client_id)) return [
						'status'   => false,
						'message'  => '分组失败：存在可能禁用/未审核/未签到的客户',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\GroupingModel $group_model */
					$group_model = D('RoyalwissD/Grouping');
					$result      = $group_model->addMember($meeting_id, $group_id, $client_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_hotel_list':
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\HotelModel $hotel_model */
					$hotel_model = D('RoyalwissD/Hotel');
					$hotel_list  = $hotel_model->getSelectedList($meeting_id);

					return array_merge($hotel_list, ['__ajax__' => true]);
				break;
				case 'get_room_list':
					$meeting_id = I('get.mid', 0, 'int');
					$hotel_id   = I('post.hid', 0, 'int');
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$room_list  = $room_model->getSelectedList($meeting_id, $hotel_id);

					return array_merge($room_list, ['__ajax__' => true]);
				break;
				case 'check_in':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MULTI_CHECK_IN_ROOM')) return [
						'status'   => false,
						'message'  => '您没有分配房间的权限',
						'__ajax__' => true
					];
					$meeting_id    = I('get.mid', 0, 'int');
					$room_id       = I('post.rid', 0, 'int');
					$client_id_str = I('post.cid', '');
					$client_id     = explode(',', $client_id_str);
					// 判断是否审核签到
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					$client_list  = $client_model->getList([
						$client_model::CONTROL_COLUMN_PARAMETER['status']            => ['=', 1],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus'] => ['=', 1],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['signStatus']   => ['=', 1],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['clientID']     => [
							'in',
							'('.implode(',', $client_id).')'
						],
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID']    => $meeting_id,
						$client_model::CONTROL_COLUMN_PARAMETER_SELF['type']         => true
					]);
					if(count($client_list)<count($client_id)) return [
						'status'   => false,
						'message'  => '分房失败：存在可能禁用/未审核/未签到的客户',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$result     = $room_model->checkIn($meeting_id, $room_id, $client_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'send_message':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.SEND_INVITATION')) return [
						'status'   => false,
						'message'  => '您没有发送邀请的权限',
						'__ajax__' => true
					];
					$meeting_id    = I('get.mid', 0, 'int');
					$client_id_str = I('post.id', '');
					$client_id     = explode(',', $client_id_str);
					$message_logic = new MessageLogic();
					$result        = $message_logic->sendMessageByAction($meeting_id, $client_id, 1);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'synchronize_wechat_information':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.SYNCHRONIZE_WECHAT_DATA')) return [
						'status'   => false,
						'message'  => '您没有同步微信数据的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
					$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
					// 1、若设定了微信企业号的接口
					$wechat_enterprise_configure = $meeting_configure_model->getWechatEnterpriseConfigure($meeting_id);
					if($wechat_enterprise_configure['status']){
						// 获取企业号用户
						$wechat_enterprise_configure = $wechat_enterprise_configure['data'];
						$wechat_enterprise_obj       = new EnterpriseAccountLibrary($wechat_enterprise_configure['corpID'], $wechat_enterprise_configure['corpSecret']);
						$user_list                   = $wechat_enterprise_obj->getUserList(1);
						if($user_list['errcode'] == 0){
							$user_list = $user_list['userlist'];
							// 获取系统该会议下的客户
							/** @var \RoyalwissD\Model\ClientModel $client_model */
							$client_model  = D('RoyalwissD/Client');
							$client_list   = $client_model->getList([
								$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $meeting_id,
								$client_model::CONTROL_COLUMN_PARAMETER['status']         => ['!=', 2]
							]);
							$count         = 0;
							$replace_char  = '#%ID%#';
							$this_database = $client_model::DATABASE_NAME;
							$this_table    = $client_model::TABLE_NAME;
							$sql_main      = "
UPDATE $this_database.$this_table
SET
	wechat_type = 2,
	wechat_userid = CASE id u$replace_char END,
	wechat_nickname = CASE id n$replace_char END,
	wechat_department = CASE id d$replace_char END,
	wechat_avatar = CASE id a$replace_char END,
	wechat_is_follow = CASE id f$replace_char END,
	wechat_position = CASE id p$replace_char END,
	wechat_mobile = CASE id m$replace_char END,
	wechat_gender = CASE id g$replace_char END,
	wechat_id = CASE id i$replace_char END,
	wechat_email = CASE id e$replace_char END
";
							$sql_where     = "WHERE id IN ($replace_char) ";
							foreach($user_list as $wechat_user){
								foreach($client_list as $client){
									if($client['mobile'] == $wechat_user['mobile']){
										$count++;
										$sql_where = str_replace($replace_char, "$client[cid],$replace_char", $sql_where);
										$sql_main  = str_replace("u$replace_char", " WHEN $client[cid] THEN '$wechat_user[userid]'u$replace_char", $sql_main);
										$sql_main  = str_replace("n$replace_char", " WHEN $client[cid] THEN '$wechat_user[name]'n$replace_char", $sql_main);
										$sql_main  = str_replace("d$replace_char", " WHEN $client[cid] THEN '".implode(',', $wechat_user['department'])."'d$replace_char", $sql_main);
										$sql_main  = str_replace("a$replace_char", " WHEN $client[cid] THEN '$wechat_user[avatar]'a$replace_char", $sql_main);
										$sql_main  = str_replace("f$replace_char", " WHEN $client[cid] THEN '$wechat_user[status]'f$replace_char", $sql_main);
										$sql_main  = str_replace("p$replace_char", " WHEN $client[cid] THEN '$wechat_user[position]'p$replace_char", $sql_main);
										$sql_main  = str_replace("m$replace_char", " WHEN $client[cid] THEN '$wechat_user[mobile]'m$replace_char", $sql_main);
										$sql_main  = str_replace("g$replace_char", " WHEN $client[cid] THEN '$wechat_user[gender]'g$replace_char", $sql_main);
										$sql_main  = str_replace("i$replace_char", " WHEN $client[cid] THEN '$wechat_user[weixinid]'i$replace_char", $sql_main);
										$sql_main  = str_replace("e$replace_char", " WHEN $client[cid] THEN '$wechat_user[email]'e$replace_char", $sql_main);
									}
								}
							}
							if($count>0){
								$sql    = "$sql_main $sql_where";
								$sql    = str_replace(",$replace_char", '', $sql);
								$sql    = str_replace("u$replace_char", '', $sql);
								$sql    = str_replace("n$replace_char", '', $sql);
								$sql    = str_replace("d$replace_char", '', $sql);
								$sql    = str_replace("a$replace_char", '', $sql);
								$sql    = str_replace("f$replace_char", '', $sql);
								$sql    = str_replace("p$replace_char", '', $sql);
								$sql    = str_replace("m$replace_char", '', $sql);
								$sql    = str_replace("g$replace_char", '', $sql);
								$sql    = str_replace("i$replace_char", '', $sql);
								$sql    = str_replace("e$replace_char", '', $sql);
								$result = $client_model->execute($sql);
								if($result) return [
									'status'   => true,
									'message'  => "同步数据成功：共同步[$count]条客户数据",
									'__ajax__' => true
								];
								else return [
									'status'   => false,
									'message'  => "同步失败：客户微信数据已经是最新的",
									'__ajax__' => true
								];
							}
							else{
								return [
									'status'   => false,
									'message'  => "未匹配到任何客户数据",
									'__ajax__' => true
								];
							}
						}
						else{
							return [
								'status'   => false,
								'message'  => "获取微信数据失败",
								'__ajax__' => true
							];
						}
					}
					else return array_merge($wechat_enterprise_configure, ['__ajax__' => true]);
				break;
				case 'set_search_configure':
					if(!UserLogic::isPermitted('SEVERAL-CLIENT.MANAGE_SEARCH_COLUMN')) return [
						'status'   => false,
						'message'  => '您没有管理搜索字段的权限',
						'__ajax__' => true
					];
					$column_str = I('post.column', '');
					$column     = explode(',', $column_str);
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
					$client_column_control_model = D('RoyalwissD/ClientColumnControl');
					$client_column_control_model->where([
						'mid'    => $meeting_id,
						'action' => $client_column_control_model::ACTION_SEARCH
					])->save(['search' => 0]);
					if($column_str != ''){
						$result = $client_column_control_model->where([
							'mid'    => $meeting_id,
							'action' => $client_column_control_model::ACTION_SEARCH,
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

		public function setData($type, $data){
			switch($type){
				case 'column_setting':
					$result = [];
					foreach($data as $val){
						$val['is_custom'] = ($this->isCustomColumn($val['form'])) ? 1 : 0;
						$result[]         = $val;
					}

					return $result;
				break;
				case 'column_setting:read':
					$result = [];
					foreach($data as $val){
						$val['is_custom'] = ($this->isCustomColumn($val['form'])) ? 1 : 0;
						if($val['is_custom'] == 1 && $val['view'] == 1) $val['can_modified'] = 1;
						elseif($val['is_custom'] == 0 && $val['view'] == 1 && $val['table'] == 'client') $val['can_modified'] = 1;
						elseif(in_array($val['code'], [
							'ROYALWISSD-ATTENDEE-RECEIVABLES',
							'ROYALWISSD-ATTENDEE-CONSUMPTION'
						])) $val['can_modified'] = 1;
						else $val['can_modified'] = 0;
						$result[] = $val;
					}

					return $result;
				break;
				case 'column_setting:search':
					$result = '';
					foreach($data as $val){
						if($val['search'] == 1) $result .= "$val[name] / ";
					}

					return trim($result, ' / ');
				break;
				case 'manage:statistics':
					$statistics = [
						'total'       => count($data['total']),
						'list'        => count($data['list']),
						'signed'      => 0,
						'notSigned'   => 0,
						'reviewed'    => 0,
						'notReviewed' => 0,
						'enabled'     => 0,
						'disabled'    => 0,
						'终端'          => 0,
						'陪同'          => 0
					];
					foreach($data['total'] as $value){
						if($value['review_status'] == 1) $statistics['reviewed']++;
						else $statistics['notReviewed']++;
						if($value['status'] == 1) $statistics['enabled']++;
						if($value['status'] == 0) $statistics['disabled']++;
						if($value['type'] == '终端') $statistics['终端']++;
						if($value['type'] == '陪同') $statistics['陪同']++;
						if(in_array($value['type'], ClientModel::TYPE)){
							if($value['sign_status'] == 1) $statistics['signed']++;
							else $statistics['notSigned']++;
						}
					}

					return $statistics;
				break;
				case 'manage:set_data':
					$list = [];
					$get  = $data['urlParam'];
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
					// 若指定了审核状态码的情况
					if(isset($get[ClientModel::CONTROL_COLUMN_PARAMETER_SELF['type']])) $client_type = $get[ClientModel::CONTROL_COLUMN_PARAMETER_SELF['type']];
					foreach($data['list'] as $index => $client){
						// 1、筛选数据
						if(isset($keyword)){
							/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
							$client_column_control_model = D('RoyalwissD/ClientColumnControl');
							$search_list                 = $client_column_control_model->getClientSearchColumn($get['mid'], true);
							$found                       = 0;
							foreach($search_list as $value){
								if($found == 0 && stripos($client[$value['form']], $keyword) !== false) $found = 1;
							}
							if(count($search_list) == 0) $found = 1;
							if($found == 0) continue;
						}
						if(isset($client_id) && $client_id != $client['cid']) continue;
						if(isset($status) && $status != $client['status']) continue;
						if(isset($client_type) && $client_type != $client['type']) continue;
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
						$client['unit_is_new_code']   = $client['unit_is_new'];
						$client['unit_is_new']        = UnitModel::IS_NEW[$client['unit_is_new']];
						if(strtotime($client['creatime'])>(time()-self::NEW_DATA_TIME)) $client['new_data'] = true;
						else $client['new_data'] = false;
						if(in_array($client['type'], ClientModel::TYPE_SPECIAL)) $client['special_type'] = true;
						else $client['special_type'] = false;
						$list[] = $client;
					}

					return $list;
				break;
				case 'get_detail':
					$column_list = $data['columnValue'];
					$column_name = $data['columnName'];
					$result      = [];
					foreach($data['dataList'] as $index => $client){
						$temp_head = $temp_body = [];
						foreach($client as $key => $val){
							if(!in_array($key, $column_list)) continue;
							$i               = array_search($key, $column_list);
							$temp_head[$key] = $column_name[$i];
							switch($key){
								case 'register_type':
									$val = AttendeeModel::REGISTER_TYPE[$val];
								break;
								case 'review_status':
									$val = AttendeeModel::REVIEW_STATUS[$val];
								break;
								case 'sign_status':
									$val = AttendeeModel::SIGN_STATUS[$val];
								break;
								case 'sign_type':
									$val = AttendeeModel::SIGN_TYPE[$val];
								break;
								case 'print_status':
									$val = AttendeeModel::PRINT_STATUS[$val];
								break;
								case 'gift_status':
									$val = AttendeeModel::GIFT_STATUS[$val];
								break;
								case 'status':
									$val = GeneralModel::STATUS[$val];
								break;
								case 'gender':
									$val = ClientModel::GENDER[$val];
								break;
								case 'is_new':
									$val = ClientModel::IS_NEW[$val];
								break;
							}
							$temp_body[$key] = $val;
						}
						if(count($result) == 0) $result[] = $temp_head;
						$result[] = $temp_body;
					}

					return $result;
				break;
				case 'downloadData':
					$column_list = $data['columnValue'];
					$column_name = $data['columnName'];
					$result      = [];
					$get         = $data['urlParam'];
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
					// 若指定了审核状态码的情况
					if(isset($get[ClientModel::CONTROL_COLUMN_PARAMETER_SELF['type']])) $client_type = $get[ClientModel::CONTROL_COLUMN_PARAMETER_SELF['type']];
					foreach($data['dataList'] as $index => $client){
						$temp_head = $temp_body = [];
						// 1、筛选数据
						if(isset($keyword)){
							/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
							$client_column_control_model = D('RoyalwissD/ClientColumnControl');
							$search_list                 = $client_column_control_model->getClientSearchColumn($get['mid'], true);
							$found                       = 0;
							foreach($search_list as $value){
								if($found == 0 && stripos($client[$value['form']], $keyword) !== false) $found = 1;
							}
							if(count($search_list) == 0) $found = 1;
							if($found == 0) continue;
						}
						if(isset($client_id) && $client_id != $client['cid']) continue;
						if(isset($status) && $status != $client['status']) continue;
						if(isset($client_type) && $client_type != $client['type']) continue;
						if(isset($sign_status)){
							if($sign_status == 0 && in_array($client['sign_status'], [1])) continue;
							if($sign_status == 1 && in_array($client['sign_status'], [0, 2])) continue;
						}
						if(isset($review_status)){
							if($review_status == 0 && in_array($client['review_status'], [1])) continue;
							if($review_status == 1 && in_array($client['review_status'], [0, 2])) continue;
						}
						foreach($client as $key => $val){
							if(!in_array($key, $column_list)) continue;
							$i               = array_search($key, $column_list);
							$temp_head[$key] = $column_name[$i];
							switch($key){
								case 'register_type':
									$val = AttendeeModel::REGISTER_TYPE[$val];
								break;
								case 'review_status':
									$val = AttendeeModel::REVIEW_STATUS[$val];
								break;
								case 'sign_status':
									$val = AttendeeModel::SIGN_STATUS[$val];
								break;
								case 'sign_type':
									$val = AttendeeModel::SIGN_TYPE[$val];
								break;
								case 'print_status':
									$val = AttendeeModel::PRINT_STATUS[$val];
								break;
								case 'gift_status':
									$val = AttendeeModel::GIFT_STATUS[$val];
								break;
								case 'status':
									$val = GeneralModel::STATUS[$val];
								break;
								case 'gender':
									$val = ClientModel::GENDER[$val];
								break;
								case 'is_new':
									$val = ClientModel::IS_NEW[$val];
								break;
							}
							$temp_body[$key] = $val;
						}
						if(count($result) == 0) $result[] = $temp_head;
						$result[] = $temp_body;
					}

					return $result;
				break;
				default:
					return $data;
				break;
			}
		}

		/**
		 * 获取可控制的字段列表
		 *
		 * @param bool $just_include_custom_column 只包含自定义字段
		 *
		 * @return array
		 */
		public function getControlledColumn($just_include_custom_column = false){
			/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
			$attendee_model = D('RoyalwissD/Attendee');
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			$result       = [];
			foreach($client_model->getColumnList() as $val){
				// 排除不必要的字段
				if(!in_array($val['column_name'], [
					'name_pinyin',
					'password',
					'unit_pinyin',
					'creator',
					'status',
					'creatime',
					'id',
					'wechat_type',
					'wechat_openid',
					'wechat_userid',
					'wechat_nickname',
					'wechat_mobile',
					'wechat_email',
					'wechat_gender',
					'wechat_lang',
					'wechat_country',
					'wechat_province',
					'wechat_city',
					'wechat_avatar',
					'wechat_is_follow',
					'wechat_department',
					'wechat_appid',
					'wechat_position',
					'wechat_id',
					'_batch_save_id',
					'_batch_column_id'
				])
				) $result[] = $val;
			}
			$attendee_except_column_list = $just_include_custom_column ? [
				'creator',
				'status',
				'creatime',
				'id',
				'cid',
				'mid',
			] : [
				'id',
				'cid',
				'mid'
			];
			foreach($attendee_model->getColumnList($just_include_custom_column) as $val){
				// 排除不必要的字段
				if(!in_array($val['column_name'], $attendee_except_column_list)) $result[] = $val;
			}

			return $result;
		}

		/**
		 * 获取搜索字段
		 *
		 * @return array
		 */
		public function getSearchColumn(){
			/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
			$attendee_model = D('RoyalwissD/Attendee');
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			$result       = [];
			foreach($client_model->getColumnList() as $val){
				// 排除不必要的字段
				if(!in_array($val['column_name'], [
					'unit_is_new',
					'is_new',
					'gender',
					'creator',
					'id',
					'password',
					'status',
					'creatime',
					'wechat_type',
					'wechat_openid',
					'wechat_userid',
					'wechat_nickname',
					'wechat_mobile',
					'wechat_email',
					'wechat_gender',
					'wechat_lang',
					'wechat_country',
					'wechat_province',
					'wechat_city',
					'wechat_avatar',
					'wechat_is_follow',
					'wechat_department',
					'wechat_appid',
					'wechat_position',
					'wechat_id',
					'_batch_save_id',
					'_batch_column_id'
				])
				) $result[] = $val;
			}
			foreach($attendee_model->getColumnList(true) as $val){
				// 排除不必要的字段
				if(!in_array($val['column_name'], [
					'id',
					'cid',
					'mid',
					'receivables',
					'consumption'
				])
				) $result[] = $val;
			}

			return $result;
		}

		/**
		 * 是否是自定义词字段
		 *
		 * @param string $column_name 字段名称
		 *
		 * @return mixed
		 */
		public function isCustomColumn($column_name){
			return preg_match('/'.AttendeeModel::CUSTOM_COLUMN.'(\d)+/', $column_name);
		}

		/**
		 * 签到
		 *
		 * @param string|int|array $client_id  客户ID
		 * @param int              $meeting_id 会议ID
		 * @param int              $sign_type  签到类型
		 * @param bool             $cancel     是否为取消操作
		 *
		 * @return array
		 */
		public function sign($client_id, $meeting_id, $sign_type, $cancel = false){
			set_time_limit(0);
			if(is_null($client_id) || $client_id == '' || !$client_id) return [
				'status'  => false,
				'message' => '没有选择任何客户'
			];
			elseif(is_numeric($client_id)) $client_arr = [$client_id];
			elseif(is_string($client_id)) $client_arr = explode(',', $client_id);
			elseif(is_array($client_id)) $client_arr = $client_id;
			else return ['status' => false, 'message' => '客户参数错误'];
			/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
			$attendee_model = D('RoyalwissD/Attendee');
			/** @var \General\Model\MeetingModel $meeting_model */
			$meeting_model = D('General/Meeting');
			if(!$meeting_model->fetch(['id' => $meeting_id])) return [
				'status'  => false,
				'message' => '找不到会议'
			];
			$meeting_record = $meeting_model->getObject();
			if(strtotime($meeting_record['sign_start_time'])>time()) return [
				'status'  => false,
				'message' => '会议还没开始签到'
			];
			if(strtotime($meeting_record['sign_end_time'])<time()) return [
				'status'  => false,
				'message' => '会议签到已经结束'
			];
			$attendee_list = $attendee_model->where(['mid' => $meeting_id, 'cid' => ['in', $client_arr]])->select();
			$action        = $cancel ? '取消签到' : '签到';
			foreach($attendee_list as $key => $attendee){
				if($attendee['status'] != 1) return ['status' => false, 'message' => '签到失败：存在禁用或删除的客户'];
				if(!$cancel){
					if($attendee['review_status'] != 1) return ['status' => false, 'message' => '签到失败：存在未审核的客户'];
					$attendee_list[$key]['sign_status']   = 1;
					$attendee_list[$key]['sign_time']     = Time::getCurrentTime();
					$attendee_list[$key]['sign_director'] = Session::getCurrentUser();
					$attendee_list[$key]['sign_type']     = $sign_type;
				}
				else{
					$attendee_list[$key]['sign_status']   = 2;
					$attendee_list[$key]['sign_time']     = null;
					$attendee_list[$key]['sign_director'] = null;
					$attendee_list[$key]['sign_type']     = 0;
				}
			}
			$result = $attendee_model->addAll($attendee_list, [], true);

			// todo 发送信息
			return $result ? [
				'status'  => true,
				'message' => $action.'成功'
			] : [
				'status'  => false,
				'message' => $action.'失败'
			];
		}

		/**
		 * 审核
		 *
		 * @param string|int|array $client_id  客户ID
		 * @param int              $meeting_id 会议ID
		 * @param bool             $cancel     是否为取消操作
		 *
		 * @return array
		 */
		public function review($client_id, $meeting_id, $cancel = false){
			set_time_limit(0);
			if(is_null($client_id) || $client_id == '' || !$client_id) return [
				'status'  => false,
				'message' => '没有选择任何客户'
			];
			elseif(is_numeric($client_id)) $client_arr = [$client_id];
			elseif(is_string($client_id)) $client_arr = explode(',', $client_id);
			elseif(is_array($client_id)) $client_arr = $client_id;
			else return ['status' => false, 'message' => '客户参数错误'];
			/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
			$attendee_model = D('RoyalwissD/Attendee');
			$qrcode_logic   = new QRCodeLogic();
			$str_obj        = new StringPlus();
			$attendee_list  = $attendee_model->where(['mid' => $meeting_id, 'cid' => ['in', $client_arr]])->select();
			$action         = $cancel ? '取消审核' : '审核';
			foreach($attendee_list as $key => $attendee){
				if($attendee['status'] != 1) return ['status' => false, 'message' => '取消审核失败：存在禁用或删除的客户'];
				if(!$cancel){
					// todo 写入二维码页面
					$sign_qrcode                             = U('', [
						'mid' => $attendee['mid'],
						'cid' => $attendee['cid']
					]);
					$sign_code                               = $str_obj->makeRandomString(8);
					$attendee_list[$key]['review_director']  = Session::getCurrentUser();
					$attendee_list[$key]['review_status']    = 1;
					$attendee_list[$key]['review_time']      = Time::getCurrentTime();
					$attendee_list[$key]['sign_qrcode']      = trim($qrcode_logic->make($sign_qrcode), '.');
					$attendee_list[$key]['sign_code']        = $sign_code;
					$attendee_list[$key]['sign_code_qrcode'] = trim($qrcode_logic->make($sign_code), '.');
				}
				else{
					if($attendee['sign_status'] == 1) return ['status' => false, 'message' => '取消审核失败：存在签到的客户'];
					$attendee_list[$key]['review_director']  = null;
					$attendee_list[$key]['review_status']    = 2;
					$attendee_list[$key]['review_time']      = null;
					$attendee_list[$key]['sign_qrcode']      = null;
					$attendee_list[$key]['sign_code']        = null;
					$attendee_list[$key]['sign_code_qrcode'] = null;
				}
			}
			$result = $attendee_model->addAll($attendee_list, [], true);

			return $result ? [
				'status'  => true,
				'message' => $action.'成功'
			] : [
				'status'  => false,
				'message' => $action.'失败'
			];
		}

		/**
		 * 领取礼品
		 *
		 * @param string|int|array $client_id  客户ID
		 * @param int              $meeting_id 会议ID
		 * @param bool             $cancel     是否为取消操作
		 *
		 * @return array
		 */
		public function gift($client_id, $meeting_id, $cancel = false){
			set_time_limit(0);
			if(is_null($client_id) || $client_id == '' || !$client_id) return [
				'status'  => false,
				'message' => '没有选择任何客户'
			];
			elseif(is_numeric($client_id)) $client_arr = [$client_id];
			elseif(is_string($client_id)) $client_arr = explode(',', $client_id);
			elseif(is_array($client_id)) $client_arr = $client_id;
			else return ['status' => false, 'message' => '客户参数错误'];
			/** @var \RoyalwissD\Model\AttendeeModel $attendee_model */
			$attendee_model = D('RoyalwissD/Attendee');
			$attendee_list  = $attendee_model->where(['mid' => $meeting_id, 'cid' => ['in', $client_arr]])->select();
			$action         = $cancel ? '退还礼品' : '领取礼品';
			foreach($attendee_list as $key => $attendee){
				if($attendee['status'] != 1) return ['status' => false, 'message' => '不能领取礼品：存在禁用或删除的客户'];
				if(!$cancel){
					if($attendee['review_status'] != 1) return ['status' => false, 'message' => '不能领取礼品：存在未审核的客户'];
					if($attendee['sign_status'] != 1) return ['status' => false, 'message' => '不能领取礼品：存在未签到的客户'];
					$attendee_list[$key]['gift_status'] = 1;
					$attendee_list[$key]['gift_time']   = Time::getCurrentTime();
				}
				else{
					$attendee_list[$key]['gift_status'] = 2;
					$attendee_list[$key]['gift_time']   = null;
				}
			}
			$result = $attendee_model->addAll($attendee_list, [], true);

			return $result ? [
				'status'  => true,
				'message' => $action.'成功'
			] : [
				'status'  => false,
				'message' => $action.'失败'
			];
		}

		/**
		 * 特殊处理字段数据
		 *
		 * @param string $column_name        字段名称
		 * @param mixed  $column_value       字段值
		 * @param bool   $return_full_result 是否返回完整字段值
		 *
		 * @return array|null
		 */
		private function _convertColumnValue($column_name, $column_value, $return_full_result = false){
			switch($column_name){
				case 'consumption':
				case 'receivables':
					if(is_numeric($column_value)) $val = $column_value;
					else $val = 0;

					return $return_full_result ? [
						'save' => [$column_name => $val],
						'data' => ['value' => $val, 'content' => $val]
					] : [$column_name => $val];
				break;
				case 'gender':
					switch($column_value){
						case '男':
						case '1':
							$content = '男';
						break;
						case '女':
						case '2':
							$content = '女';
						break;
						default:
							$content = '未指定';
						break;
					}
					$val = array_search($content, ClientModel::GENDER);

					return $return_full_result ? [
						'save' => [$column_name => $val],
						'data' => ['value' => $val, 'content' => $content]
					] : [$column_name => $val];
				break;
				case 'is_new':
					switch($column_value){
						case '是':
						case '新客':
						case '1':
							$content = '是';
						break;
						case '否':
						case '老客':
						case '0':
							$content = '否';
						break;
						default:
							$content = '';
						break;
					}
					$val = array_search($content, ClientModel::IS_NEW);

					return $return_full_result ? [
						'save' => [$column_name => $val],
						'data' => ['value' => $val, 'content' => $content]
					] : [$column_name => $val];
				break;
				case 'name':
					$column_value = str_replace("'", '', $column_value);
					$column_value = str_replace('"', '', $column_value);
					$column_value = str_replace('/', '', $column_value);
					$column_value = str_replace('\\', '', $column_value);
					$str_obj      = new StringPlus();
					$val          = $str_obj->getPinyin($column_value, true, '');

					return $return_full_result ? [
						'save' => ['name' => $column_value, 'name_pinyin' => $val],
						'data' => ['value' => $column_value, 'content' => $column_value]
					] : ['name' => $column_value, 'name_pinyin' => $val];
				break;
				case 'unit':
					$column_value = str_replace("'", '', $column_value);
					$column_value = str_replace('"', '', $column_value);
					$column_value = str_replace('/', '', $column_value);
					$column_value = str_replace('\\', '', $column_value);
					$str_obj      = new StringPlus();
					$val          = $str_obj->getPinyin($column_value, true, '');

					return $return_full_result ? [
						'save' => ['unit' => $column_value, 'unit_pinyin' => $val],
						'data' => ['value' => $column_value, 'content' => $column_value]
					] : ['unit' => $column_value, 'unit_pinyin' => $val];
				break;
				case 'birthday':
					$val = Time::isTimeFormat($column_value);

					return $return_full_result ? [
						'save' => [$column_name => $val],
						'data' => ['value' => $val, 'content' => $val]
					] : [$column_name => $val];
				break;
				case 'mobile':
					$general_client_logic = new GeneralClientLogic();
					/** @var \RoyalwissD\Model\ClientModel $client_model */
					$client_model = D('RoyalwissD/Client');
					$password     = $general_client_logic->makePassword($client_model->getDefaultPassword(), $column_value);

					return $return_full_result ? [
						'save' => [
							'mobile'   => $column_value,
							'password' => $password
						],
						'data' => ['value' => $column_value, 'content' => $column_value]
					] : [
						'mobile'   => $column_value,
						'password' => $password
					];
				break;
				case '':
					return null;
				break;
				default:
					return $return_full_result ? [
						'save' => [$column_name => $column_value],
						'data' => ['value' => $column_value, 'content' => $column_value]
					] : [$column_name => $column_value];
				break;
			}
		}
	}
