<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-9
	 * Time: 10:35
	 */

	namespace RoyalwissD\Logic;

	use CMS\Controller\CMS;
	use CMS\Logic\MeetingLogic as CMSMeetingLogic;
	use CMS\Logic\Session;
	use CMS\Logic\UploadLogic;
	use CMS\Model\CMSModel;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use General\Model\MeetingColumnControlModel;
	use General\Model\MeetingModel;
	use RoyalwissD\Model\MeetingConfigureModel;
	use General\Logic\MeetingLogic as GeneralMeetingLogic;
	use RoyalwissD\Model\ReportColumnControlModel;

	class MeetingLogic extends RoyalwissDLogic{
		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'create': // 创建会议
					if(!in_array('SEVERAL-MEETING.CREATE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有创建会议的权限',
						'__ajax__' => true
					];
					/**
					 * 初始化控制字段记录<br>
					 * 包括客户读写检索控制记录
					 * 收款检索记录
					 *
					 * @param int $meeting_id 会议ID
					 *
					 * @return array
					 */
					$initControlledColumnRecord = function ($meeting_id){
						$setClientNecessaryColumn   = function ($column_name){
							$list = [
								'name',
								'unit',
								'mobile'
							];

							return in_array($column_name, $list) ? 1 : 0;
						};
						$setClientViewedColumn      = function ($column_name){
							$list = [
								'name',
								'unit',
								'mobile',
								'review_status',
								'sign_status',
								'creator',
								'creatime'
							];

							return in_array($column_name, $list) ? 1 : 0;
						};
						$setClientSearchColumn      = function ($column_name){
							$list = [
								'name',
								'name_pinyin',
								'unit',
								'unit_pinyin',
								'mobile'
							];

							return in_array($column_name, $list) ? 1 : 0;
						};
						$setReceivablesSearchColumn = function ($column_name){
							$list = [
								'client',
								'client_pinyin',
								'unit',
								'unit_pinyin'
							];

							return in_array($column_name, $list) ? 1 : 0;
						};
						$setReceivablesViewedColumn = function ($column_name){
							$except_list = [
								'review_director',
								'review_time',
								'place',
								'creatime',
								'creator'
							];

							return !in_array($column_name, $except_list) ? 1 : 0;
						};
						/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
						$client_column_control_model = D('RoyalwissD/ClientColumnControl');
						/** @var \RoyalwissD\Model\ReportColumnControlModel $report_column_control_model */
						$report_column_control_model = D('RoyalwissD/ReportColumnControl');
						/** @var \RoyalwissD\Model\ReceivablesColumnControlModel $receivables_column_control_model */
						$receivables_column_control_model = D('RoyalwissD/ReceivablesColumnControl');
						$client_logic                     = new ClientLogic();
						$module                           = 'RoyalwissD';
						$client_column_list_write         = $client_logic->getControlledColumn(true); // 获取客户的写入控制字段
						$client_column_list_read          = $client_logic->getControlledColumn(false); // 获取客户的列表控制字段
						$client_search_column_list        = $client_logic->getSearchColumn(); // 获取客户的检索字段
						$receivables_column_list_read     = $receivables_column_control_model->getDatabaseColumn($receivables_column_control_model::ACTION_READ); // 获取收款的读取字段
						$receivables_column_list_search   = $receivables_column_control_model->getDatabaseColumn($receivables_column_control_model::ACTION_SEARCH); // 获取收款的检索字段
						$data_client_write                = $data_client_read = $data_client_report_read = $data_client_search = $data_client_report_search = [];
						$data_receivables_search          = $data_receivables_read = [];
						foreach($client_column_list_write as $value){
							$data_client_write[] = [
								'action'   => $client_column_control_model::ACTION_WRITE,
								'mid'      => $meeting_id,
								'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
								'form'     => $value['column_name'],
								'table'    => $value['table_name'],
								'name'     => $value['column_comment'],
								'must'     => $setClientNecessaryColumn($value['column_name']),
								'view'     => $setClientViewedColumn($value['column_name']),
								'creatime' => Time::getCurrentTime(),
								'creator'  => Session::getCurrentUser()
							];
						}
						foreach($client_column_list_read as $value){
							$data_client_read[]        = [
								'action'   => $client_column_control_model::ACTION_READ,
								'mid'      => $meeting_id,
								'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
								'form'     => $value['column_name'],
								'table'    => $value['table_name'],
								'name'     => $value['column_comment'],
								'must'     => 0,
								'view'     => $setClientViewedColumn($value['column_name']),
								'creatime' => Time::getCurrentTime(),
								'creator'  => Session::getCurrentUser()
							];
							$data_client_report_read[] = [
								'type'     => $report_column_control_model::TYPE_CLIENT,
								'action'   => $report_column_control_model::ACTION_READ,
								'mid'      => $meeting_id,
								'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
								'form'     => $value['column_name'],
								'table'    => $value['table_name'],
								'name'     => $value['column_comment'],
								'must'     => 0,
								'view'     => $setClientViewedColumn($value['column_name']),
								'creatime' => Time::getCurrentTime(),
								'creator'  => Session::getCurrentUser()
							];
						}
						foreach($client_search_column_list as $value){
							$data_client_search[]        = [
								'action'   => $client_column_control_model::ACTION_SEARCH,
								'mid'      => $meeting_id,
								'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
								'form'     => $value['column_name'],
								'table'    => $value['table_name'],
								'name'     => $value['column_comment'],
								'search'   => $setClientSearchColumn($value['column_name']),
								'view'     => $setClientViewedColumn($value['column_name']),
								'creatime' => Time::getCurrentTime(),
								'creator'  => Session::getCurrentUser()
							];
							$data_client_report_search[] = [
								'type'     => $report_column_control_model::TYPE_CLIENT,
								'action'   => $report_column_control_model::ACTION_SEARCH,
								'mid'      => $meeting_id,
								'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
								'form'     => $value['column_name'],
								'table'    => $value['table_name'],
								'name'     => $value['column_comment'],
								'search'   => $setClientSearchColumn($value['column_name']),
								'view'     => $setClientViewedColumn($value['column_name']),
								'creatime' => Time::getCurrentTime(),
								'creator'  => Session::getCurrentUser()
							];
						}
						foreach($receivables_column_list_search as $value){
							$data_receivables_search[] = [
								'action'   => $receivables_column_control_model::ACTION_SEARCH,
								'mid'      => $meeting_id,
								'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
								'form'     => $value['column_name'],
								'table'    => $value['table_name'],
								'name'     => $value['column_comment'],
								'search'   => $setReceivablesSearchColumn($value['column_name']),
								'creatime' => Time::getCurrentTime(),
								'creator'  => Session::getCurrentUser()
							];
						}
						foreach($receivables_column_list_read as $value){
							$data_receivables_read[] = [
								'action'   => $receivables_column_control_model::ACTION_READ,
								'mid'      => $meeting_id,
								'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
								'form'     => $value['column_name'],
								'table'    => $value['table_name'],
								'name'     => $value['column_comment'],
								'view'     => $setReceivablesViewedColumn($value['column_name']),
								'creatime' => Time::getCurrentTime(),
								'creator'  => Session::getCurrentUser()
							];
						}
						$result1 = $client_column_control_model->addAll($data_client_read);
						$result2 = $client_column_control_model->addAll($data_client_write);
						$result3 = $client_column_control_model->addAll($data_client_search);
						$result4 = $report_column_control_model->addAll($data_client_report_read);
						$result5 = $report_column_control_model->addAll($data_client_report_search);
						$result6 = $receivables_column_control_model->addAll($data_receivables_search);
						$result7 = $receivables_column_control_model->addAll($data_receivables_read);
						if($result1 && $result2 && $result3 && $result4 && $result5 && $result6 && $result7) return [
							'status'  => true,
							'message' => '初始化字段控制数据成功'
						];
						else return ['status' => false, 'message' => '初始化字段控制数据失败'];
					};
					$meeting_logic              = new CMSMeetingLogic();
					$data                       = I('post.');
					$result                     = $meeting_logic->handlerRequest('create', [
						'data'         => $data,
						'originalPost' => $_POST
					]);
					if($result['status']){
						// 创建会议配置信息
						/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
						$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
						C('TOKEN_ON', false);
						$result2 = $meeting_configure_model->create(array_merge($data, [
							'mid'                  => $result['id'],
							'creator'              => Session::getCurrentUser(),
							'creatime'             => Time::getCurrentTime(),
							'client_repeat_mode'   => $meeting_configure_model::CLIENT_REPEAT_MODE,
							'client_repeat_action' => $meeting_configure_model::CLIENT_REPEAT_ACTION_OVERRIDE,
							'message_mode'         => $meeting_configure_model::MESSAGE_MODE,
							'wechat_mode'          => $meeting_configure_model::WECHAT_MODE_ENTERPRISE
						]));
						if(!$result2['status']) array_merge($result2, [
							'__ajax__' => true,
							'nextPage' => U('manage')
						]);
						// 初始化控制字段记录
						$result3 = $initControlledColumnRecord($result['id']);
						if(!$result3['status']) array_merge($result3, [
							'__ajax__' => true,
							'nextPage' => U('manage')
						]);
					}

					return array_merge($result, ['__ajax__' => true, 'nextPage' => U('manage')]);
				break;
				case 'modify':
					if(!in_array('SEVERAL-MEETING.MODIFY', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有修改会议的权限',
						'__ajax__' => true
					];
					$meeting_id    = I('get.mid', 0, 'int');
					$meeting_logic = new CMSMeetingLogic();
					$data          = I('post.');
					$result        = $meeting_logic->handlerRequest('modify', [
						'data'         => $data,
						'originalPost' => $_POST,
						'meetingID'    => $meeting_id
					]);
					if($result['status']){
						// 创建会议配置信息
						/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
						$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
						C('TOKEN_ON', false);
						$result2 = $meeting_configure_model->modify(['mid' => $meeting_id], $data);
						if(!$result2['status']) array_merge($result2, [
							'__ajax__' => true,
							'nextPage' => U('manage')
						]);
					}

					return array_merge($result, ['__ajax__' => true, 'nextPage' => U('manage')]);
				break;
				case 'delete': // 删除项目
					if(!in_array('SEVERAL-MEETING.DELETE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有删除会议的权限',
						'__ajax__' => true
					];
					$id_str        = I('post.id', '');
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('delete', [
						'id' => $id_str,
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable': // 启用项目
					if(!in_array('SEVERAL-MEETING.ENABLE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有启用会议的权限',
						'__ajax__' => true
					];
					$id_str        = I('post.id', '');
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('enable', [
						'id' => $id_str,
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用项目
					if(!in_array('SEVERAL-MEETING.DISABLE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有禁用会议的权限',
						'__ajax__' => true
					];
					$id_str        = I('post.id', '');
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('disable', [
						'id' => $id_str,
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_role': // 分配会务人员获取角色列表
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('get_role', ['curUserHighestRoleLevel' => $opt['curUserHighestRoleLevel']]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_meeting_manager': // 获取角色下已分配的会务人员
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('get_meeting_manager');

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete_meeting_manager': // 删除角色下的会务人员
					if(!in_array('SEVERAL-MEETING.MEETING_MANAGER', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有管理会务人员的权限',
						'__ajax__' => true
					];
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('delete_meeting_manager');

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_user': // 获取未分配到会务人员的人员列表
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('get_user');

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'assign_meeting_manager': // 分配会务人员
					if(!in_array('SEVERAL-MEETING.MEETING_MANAGER', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有管理会务人员的权限',
						'__ajax__' => true
					];
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('assign_meeting_manager');

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'release': // 发布会议
					if(!in_array('SEVERAL-MEETING.RELEASE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有发布会议的权限',
						'__ajax__' => true
					];
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('release');

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'cancel_release': // 取消发布会议
					if(!in_array('SEVERAL-MEETING.CANCEL_RELEASE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有取消发布会议的权限',
						'__ajax__' => true
					];
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('cancel_release');

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_detail': // 获取会议详情
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('get_detail');

					return array_merge($result, ['__ajax__' => true]);
				break;
				// 字段控制请求
				case 'show_table_column': // 显示字段
					if(!in_array('SEVERAL-MEETING.MANAGE_COLUMN', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有管理字段的权限',
						'__ajax__' => true
					];
					$meeting_logic = new GeneralMeetingLogic();
					/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
					$meeting_column_control_model = D('General/MeetingColumnControl');
					$meeting_type                 = $meeting_logic->getTypeByModule(MODULE_NAME);
					$column_name                  = I('post.name', '');
					$result                       = $meeting_column_control_model->modify([
						'mtype'  => $meeting_type,
						'action' => $meeting_column_control_model::ACTION_WRITE,
						'form'   => $column_name
					], ['view' => 1]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'hide_table_column': // 隐藏字段
					if(!in_array('SEVERAL-MEETING.MANAGE_COLUMN', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有管理字段的权限',
						'__ajax__' => true
					];
					$meeting_logic = new GeneralMeetingLogic();
					/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
					$meeting_column_control_model = D('General/MeetingColumnControl');
					$meeting_type                 = $meeting_logic->getTypeByModule(MODULE_NAME);
					$column_name                  = I('post.name', '');
					$result                       = $meeting_column_control_model->modify([
						'mtype'  => $meeting_type,
						'action' => $meeting_column_control_model::ACTION_WRITE,
						'form'   => $column_name
					], ['view' => 0]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify_table_column': // 是否必填项
					if(!in_array('SEVERAL-MEETING.MANAGE_COLUMN', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有管理字段的权限',
						'__ajax__' => true
					];
					$meeting_logic = new GeneralMeetingLogic();
					/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
					$meeting_column_control_model = D('General/MeetingColumnControl');
					$meeting_type                 = $meeting_logic->getTypeByModule(MODULE_NAME);
					$column_name                  = I('post.name', '');
					$must                         = isset($_POST['is_necessary']) && $_POST['is_necessary'] ? 1 : 0;
					$result                       = $meeting_column_control_model->modify([
						'mtype'  => $meeting_type,
						'action' => $meeting_column_control_model::ACTION_WRITE,
						'form'   => $column_name
					], ['must' => $must]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'create_table_column': // 新增自定义字段
					if(!in_array('SEVERAL-MEETING.MANAGE_COLUMN', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有管理字段的权限',
						'__ajax__' => true
					];
					/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
					$meeting_column_control_model = D('General/MeetingColumnControl');
					$meeting_logic                = new GeneralMeetingLogic();
					$post                         = I('post.');
					$meeting_type                 = $meeting_logic->getTypeByModule(MODULE_NAME);
					$data                         = [
						'type'     => addslashes($post['field_type']),
						'typeSize' => (int)$post['field_size'],
						'comment'  => addslashes($post['field_name'])
					];
					/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
					$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
					$result                  = $meeting_configure_model->addColumn($data);
					if($result['status']){
						$last_column_index = $meeting_configure_model->getLastCustomColumnIndex();
						$table_name        = 'meeting_configure';
						$data              = [
							'mtype'    => $meeting_type,
							'code'     => strtoupper(MODULE_NAME."-$table_name-".$meeting_configure_model::CUSTOM_COLUMN.$last_column_index),
							'form'     => $meeting_configure_model::CUSTOM_COLUMN.$last_column_index,
							'table'    => $table_name,
							'name'     => $post['field_name'],
							'view'     => 0,
							'creatime' => Time::getCurrentTime(),
							'creator'  => Session::getCurrentUser()
						];
						$result2           = $meeting_column_control_model->create(array_merge($data, [
							'action' => $meeting_column_control_model::ACTION_WRITE,
							'must'   => 0
						]));
						if(!$result2['status']) $result['message'] = $result2['message'];
						$result3 = $meeting_column_control_model->create(array_merge($data, [
							'action' => $meeting_column_control_model::ACTION_SEARCH,
							'search' => 0
						]));
						if(!$result3['status']) $result['message'] = $result3['message'];
					}

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'upload_logo':
					$meeting_id   = I('get.mid', 0, 'int');
					$upload_logic = new UploadLogic($meeting_id);
					$result       = $upload_logic->upload($_FILES, '/Logo/');

					return array_merge($result, ['__ajax__' => true,]);
				break;
				case 'reset_and_order_column':
					$meeting_logic         = new CMSMeetingLogic();
					$general_meeting_logic = new GeneralMeetingLogic();
					$result                = $meeting_logic->handlerRequest('reset_and_order_column', [
						'meetingType' => $general_meeting_logic->getTypeByModule(MODULE_NAME),
						'post'        => I('post.'),
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'set_search_configure':
					$meeting_logic         = new CMSMeetingLogic();
					$general_meeting_logic = new GeneralMeetingLogic();
					$result                = $meeting_logic->handlerRequest('set_search_configure', [
						'meetingType' => $general_meeting_logic->getTypeByModule(MODULE_NAME),
						'post'        => I('post.'),
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_configure':
					$meeting_id = I('post.mid', 0, 'int');
					/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
					$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
					if(!$meeting_configure_model->fetch(['mid' => $meeting_id])) return ['__ajax__' => true];
					$meeting_configure = $meeting_configure_model->getObject();

					return array_merge($meeting_configure, ['__ajax__' => true]);
				break;
				case 'set_configure':
					if(!in_array('SEVERAL-MEETING.CONFIGURE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有配置会议的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
					$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
					$meeting_id              = I('post.id', 0, 'int');
					$post                    = I('post.');
					$result                  = $meeting_configure_model->modify(['mid' => $meeting_id], [
						'wechat_official_configure'   => $post['wechat_official_configure'],
						'wechat_enterprise_configure' => $post['wechat_enterprise_configure'],
						'sms_mobset_configure'        => $post['sms_mobset_configure'],
						'email_configure'             => $post['email_configure'],
						'wechat_mode'                 => $post['wechat_mode']
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true];
				break;
			}
		}

		public function setData($type, $data){
			switch($type){
				case 'manage':
					$list = [];
					$get  = $data['urlParam'];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					// 若指定了状态码的情况
					if(isset($get[CMSModel::CONTROL_COLUMN_PARAMETER['status']])) $status = $get[CMSModel::CONTROL_COLUMN_PARAMETER['status']];
					foreach($data['list'] as $key => $meeting){
						// 1、筛选数据
						if(isset($keyword)){
							/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
							$meeting_column_control_model = D('General/MeetingColumnControl');
							$general_meeting_logic        = new GeneralMeetingLogic();
							$search_list                  = $meeting_column_control_model->getMeetingSearchColumn($general_meeting_logic->getTypeByModule(MODULE_NAME), true);
							$found                        = 0;
							foreach($search_list as $value){
								if($found == 0 && stripos($meeting[$value['form']], $keyword) !== false) $found = 1;
							}
							if(count($search_list) == 0) $found = 1;
							if($found == 0) continue;
						}
						if(isset($status) && $status != $meeting['status']) continue;
						$meeting['status_code']         = $meeting['status'];
						$meeting['status']              = GeneralModel::STATUS[$meeting['status_code']];
						$meeting['process_status_code'] = $meeting['process_status'];
						$meeting['process_status']      = MeetingModel::PROCESS_STATUS[$meeting['process_status_code']];
						$list[]                         = $meeting;
					}

					return $list;
				break;
				case 'get_detail':
					$list        = [];
					$column_list = $data['columnValue'];
					$column_name = $data['columnName'];
					foreach($data['dataList'] as $key => $meeting){
						$temp_head = $temp_body = [];
						foreach($meeting as $k => $val){
							if(!in_array($k, $column_list)) continue;
							$i             = array_search($k, $column_list);
							$temp_head[$k] = $column_name[$i];
							switch($k){
								case 'status':
									$val = GeneralModel::STATUS[$val];
								break;
								case 'process_status':
									$val = MeetingModel::PROCESS_STATUS[$val];
								break;
							}
							$temp_body[$k] = $val;
						}
						if(count($list) == 0) $list[] = $temp_head;
						$list[] = $temp_body;
					}

					return $list;
				break;
				case 'field_setting':
					$result = [];
					foreach($data as $val){
						if($this->isCustomColumn($val['form'])) $result[] = array_merge($val, ['is_custom' => 1]);
						else $result[] = array_merge($val, ['is_custom' => 0]);
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
						'total' => count($data['total']),
						'list'  => count($data['list'])
					];

					return $statistics;
				break;
				default:
					return $data;
				break;
			}
		}

		/**
		 * 获取可控制的字段列表
		 *
		 * @param int $action 操作类型
		 *
		 * @return array
		 */
		public function getControlledColumn($action){
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			/** @var \General\Model\MeetingModel $meeting_model */
			$meeting_model = D('General/Meeting');
			$result        = [];
			foreach($meeting_model->getColumnList() as $val){
				if($action == MeetingColumnControlModel::ACTION_READ) $data = [
					'id',
					'name_pinyin'
				];
				elseif($action == MeetingColumnControlModel::ACTION_WRITE) $data = [
					'creator',
					'status',
					'creatime',
					'id',
					'type',
					'process_status',
					'name_pinyin'
				];
				else $data = [];
				if(!in_array($val['column_name'], $data)) $result[] = $val;
			}
			foreach($meeting_configure_model->getColumnList(true) as $val){
				// 排除不必要的字段
				if(!in_array($val['column_name'], [
					'creator',
					'status',
					'creatime',
					'id'
				])
				) $result[] = $val;
			}

			return $result;
		}

		/**
		 * 获取检索的字段列表
		 *
		 * @return array
		 */
		public function getSearchColumn(){
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			/** @var \General\Model\MeetingModel $meeting_model */
			$meeting_model = D('General/Meeting');
			$result        = [];
			foreach($meeting_model->getColumnList() as $val){
				if(in_array($val['column_name'], [
					'name',
					'name_pinyin',
					'host',
					'plan',
					'place',
					'brief'
				])) $result[] = $val;
			}
			foreach($meeting_configure_model->getColumnList(true) as $val){
				// 排除不必要的字段
				if(!in_array($val['column_name'], [
					'creator',
					'status',
					'creatime',
					'id'
				])
				) $result[] = $val;
			}

			return $result;
		}

		/**
		 * 是否是可自定义的字段
		 *
		 * @param string $column_name 字段名称
		 *
		 * @return bool
		 */
		public function isCustomColumn($column_name){
			return preg_match('/'.MeetingConfigureModel::CUSTOM_COLUMN.'(\d)+/', $column_name) ? true : false;
		}
	}