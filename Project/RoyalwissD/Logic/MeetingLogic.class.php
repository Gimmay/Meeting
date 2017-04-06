<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-9
	 * Time: 10:35
	 */
	namespace RoyalwissD\Logic;

	use CMS\Logic\MeetingLogic as CMSMeetingLogic;
	use CMS\Logic\Session;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use General\Model\MeetingModel;
	use RoyalwissD\Model\MeetingConfigureModel;
	use General\Logic\MeetingLogic as GeneralMeetingLogic;

	class MeetingLogic extends RoyalwissDLogic{
		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'create': // 创建会议
					/**
					 * 初始化客户控制字段记录
					 *
					 * @param int $meeting_id 会议ID
					 *
					 * @return array
					 */
					$initControlledColumnRecord = function ($meeting_id){
						$setNecessaryColumn = function ($column_name){
							$list = [
								'name',
								'unit',
								'mobile'
							];

							return in_array($column_name, $list);
						};
						$setViewedColumn    = function ($column_name){
							$list = [
								'name',
								'unit',
								'mobile',
								'review_status',
								'sign_status',
								'creator',
								'creatime'
							];

							return in_array($column_name, $list);
						};
						/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
						$client_column_control_model = D('RoyalwissD/ClientColumnControl');
						$client_logic                = new ClientLogic();
						$module                      = 'RoyalwissD';
						$client_column_list_write    = $client_logic->getControlledColumn(true);
						$client_column_list_read     = $client_logic->getControlledColumn(false);
						$data1                       = $data2 = [];
						foreach($client_column_list_write as $value){
							$data1[] = [
								'action'   => $client_column_control_model::ACTION_WRITE,
								'mid'      => $meeting_id,
								'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
								'form'     => $value['column_name'],
								'table'    => $value['table_name'],
								'name'     => $value['column_comment'],
								'must'     => $setNecessaryColumn($value['column_name']),
								'view'     => 1,
								'creatime' => Time::getCurrentTime(),
								'creator'  => Session::getCurrentUser()
							];
						}
						foreach($client_column_list_read as $value){
							$data2[] = [
								'action'   => $client_column_control_model::ACTION_READ,
								'mid'      => $meeting_id,
								'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
								'form'     => $value['column_name'],
								'table'    => $value['table_name'],
								'name'     => $value['column_comment'],
								'must'     => 0,
								'view'     => $setViewedColumn($value['column_name']),
								'creatime' => Time::getCurrentTime(),
								'creator'  => Session::getCurrentUser()
							];
						}
						$result1 = $client_column_control_model->addAll($data1);
						$result2 = $client_column_control_model->addAll($data2);
						if($result1 && $result2) return ['status' => true, 'message' => '初始化字段控制数据成功'];
						else return ['status' => false, 'message' => '初始化字段控制数据失败'];
					};
					$meeting_logic              = new CMSMeetingLogic();
					$data                       = I('post.');
					$result                     = $meeting_logic->handlerRequest('create', ['data'         => $data,
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
							'message_mode'         => $meeting_configure_model::MESSAGE_MODE
						]));
						if(!$result2['status']) array_merge($result2, [
							'__ajax__'   => false,
							'__return__' => U('manage')
						]);
						// 初始化客户控制字段记录
						$result3 = $initControlledColumnRecord($result['id']);
						if(!$result3['status']) array_merge($result3, [
							'__ajax__'   => false,
							'__return__' => U('manage')
						]);
					}

					return array_merge($result, ['__ajax__' => false, '__return__' => U('manage')]);
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
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('assign_meeting_manager');

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'release': // 发布会议
					$meeting_logic = new CMSMeetingLogic();
					$result        = $meeting_logic->handlerRequest('release');

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'cancel_release': // 取消发布会议
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
				case 'field_extend_select': // 显示字段
					$meeting_logic = new GeneralMeetingLogic();
					/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
					$meeting_column_control_model = D('General/MeetingColumnControl');
					$meeting_type                 = $meeting_logic->getTypeByModule(MODULE_NAME);
					$column_name                  = I('post.name', '');
					$result                       = $meeting_column_control_model->modify([
						'mtype' => $meeting_type,
						'form'  => $column_name
					], ['view' => 1]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete_selected_field': // 隐藏字段
					$meeting_logic = new GeneralMeetingLogic();
					/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
					$meeting_column_control_model = D('General/MeetingColumnControl');
					$meeting_type                 = $meeting_logic->getTypeByModule(MODULE_NAME);
					$column_name                  = I('post.name', '');
					$result                       = $meeting_column_control_model->modify([
						'mtype' => $meeting_type,
						'form'  => $column_name
					], ['view' => 0]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'selected_field_edit': // 是否必填项
					$meeting_logic = new GeneralMeetingLogic();
					/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
					$meeting_column_control_model = D('General/MeetingColumnControl');
					$meeting_type                 = $meeting_logic->getTypeByModule(MODULE_NAME);
					$column_name                  = I('post.name', '');
					$must                         = isset($_POST['is_necessary']) && $_POST['is_necessary'] ? 1 : 0;
					$result                       = $meeting_column_control_model->modify([
						'mtype' => $meeting_type,
						'form'  => $column_name
					], ['must' => $must]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'add_field': // 新增自定义字段
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
						C('TOKEN_ON', false);
						$table_name = 'meeting_configure';
						$result2    = $meeting_column_control_model->create([
							'mtype'    => $meeting_type,
							'code'     => strtoupper(MODULE_NAME."-$table_name-".$meeting_configure_model::CUSTOM_COLUMN.$last_column_index),
							'form'     => $meeting_configure_model::CUSTOM_COLUMN.$last_column_index,
							'table'    => $table_name,
							'name'     => $post['field_name'],
							'view'     => 0,
							'must'     => 0,
							'creatime' => Time::getCurrentTime(),
							'creator'  => Session::getCurrentUser()
						]);
						if(!$result2['status']) $result['message'] = $result2['message'];
					}

					return array_merge($result, ['__ajax__' => true]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数'];
				break;
			}
		}

		public function setData($type, $data){
			switch($type){
				case 'manage':
					foreach($data as $key => $meeting){
						$data[$key]['status_code']         = $data[$key]['status'];
						$data[$key]['status']              = GeneralModel::STATUS[$data[$key]['status_code']];
						$data[$key]['process_status_code'] = $data[$key]['process_status'];
						$data[$key]['process_status']      = MeetingModel::PROCESS_STATUS[$data[$key]['process_status_code']];
					}

					return $data;
				break;
				case 'fieldSetting':
					$result = [];
					foreach($data as $val){
						if($this->isCustomColumn($val['form'])) $result[] = array_merge($val, ['is_custom' => 1]);
						else $result[] = array_merge($val, ['is_custom' => 0]);
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
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			/** @var \General\Model\MeetingModel $meeting_model */
			$meeting_model = D('General/Meeting');
			$result        = [];
			foreach($meeting_model->getColumnList() as $val){
				if(!in_array($val['column_name'], [
					'creator',
					'status',
					'creatime',
					'id',
					'type',
					'process_status',
					'name_pinyin'
				])
				) $result[] = $val;
			}
			foreach($meeting_configure_model->getColumnList($just_include_custom_column) as $val){
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