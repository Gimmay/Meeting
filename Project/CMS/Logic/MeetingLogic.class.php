<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-10
	 * Time: 11:07
	 */
	namespace CMS\Logic;

	use CMS\Model\CMSModel;
	use Exception;
	use General\Logic\MeetingLogic as GeneralMeetingLogic;
	use General\Logic\Time;
	use Quasar\Utility\StringPlus;
	use RoyalwissD\Logic\MeetingLogic as RoyalwissDMeetingLogic;
	use RoyalwissD\Model\MeetingModel;

	class MeetingLogic extends CMSLogic{
		/** 会议管理页面URL控制参数 */
		const URL_PARAMETER_STATUS = [
			['status' => 1, 'param' => 'ing'],
			['status' => 2, 'param' => 'ing'],
			['status' => 3, 'param' => 'ing'],
			['status' => 4, 'param' => 'fin']
		];

		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'create':
					if(!in_array('SEVERAL-MEETING.CREATE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有创建会议的权限',
						'__ajax__' => true
					];
					$module        = MODULE_NAME;
					$meeting_logic = new GeneralMeetingLogic();
					$meeting_type  = $meeting_logic->getTypeByModule($module);
					$str_obj       = new StringPlus();
					$data          = $opt['data'];
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					// 创建会议记录
					unset($data['brief']);
					$result = $meeting_model->create(array_merge($data, [
						'name_pinyin'     => $str_obj->getPinyin($data['name'], true, ''),
						'type'            => $meeting_type,
						'creator'         => Session::getCurrentUser(),
						'creatime'        => Time::getCurrentTime(),
						'brief'           => $opt['originalPost']['brief'],
						'start_time'      => Time::isTimeFormat($data['start_time']),
						'end_time'        => Time::isTimeFormat($data['end_time']),
						'sign_start_time' => Time::isTimeFormat($data['sign_start_time']),
						'sign_end_time'   => Time::isTimeFormat($data['sign_end_time'])
					]));
					if($result['status']){
						$meeting_manager_logic = new MeetingManagerLogic();
						$result2               = $meeting_manager_logic->create($result['id'], Session::getCurrentUser(), 0);
						if(!$result2['status']) return ['status' => false, 'message' => '初始化会务人员失败'];
					}

					return $result;
				break;
				case 'get_role': // 分配会务人员时获取角色列表
					/** @var \CMS\Model\RoleModel $role_model */
					$role_model = D('CMS/Role');
					$list       = $role_model->getList([
						CMSModel::CONTROL_COLUMN_PARAMETER['status']        => ['=', 1],
						CMSModel::CONTROL_COLUMN_PARAMETER['order']         => ' name asc ',
						$role_model::CONTROL_COLUMN_PARAMETER_SELF['level'] => $opt['curUserHighestRoleLevel'],
					]);

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'get_meeting_manager': // 分配会务人员时查看角色下的用户
					/** @var \CMS\Model\MeetingManagerModel $meeting_manager_model */
					$meeting_manager_model = D('CMS/MeetingManager');
					$list                  = $meeting_manager_model->getList([
						$meeting_manager_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => I('post.mid', 0, 'int'),
						$meeting_manager_model::CONTROL_COLUMN_PARAMETER_SELF['roleID']    => I('post.rid', 0, 'int'),
						CMSModel::CONTROL_COLUMN_PARAMETER['order']                        => ' name asc ',
					]);

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'get_user':
					/** @var \CMS\Model\MeetingManagerModel $meeting_manager_model */
					$meeting_manager_model = D('CMS/MeetingManager');
					$list                  = $meeting_manager_model->getUnassignedUser([
						$meeting_manager_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => I('post.mid', 0, 'int'),
						$meeting_manager_model::CONTROL_COLUMN_PARAMETER_SELF['roleID']    => I('post.rid', 0, 'int'),
						CMSModel::CONTROL_COLUMN_PARAMETER['order']                        => ' name asc '
					]);

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'delete_meeting_manager': // 删除会务人员
					if(!in_array('SEVERAL-MEETING.MEETING_MANAGER', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有管理会务人员的权限',
						'__ajax__' => true
					];
					$meeting_manager_logic = new MeetingManagerLogic();
					$user_id               = I('post.uid', 0, 'int');
					$meeting_id            = I('post.mid', 0, 'int');
					$role_id               = I('post.rid', 0, 'int');
					$result                = $meeting_manager_logic->delete($meeting_id, $user_id, $role_id);
					if($result['status']){
						/** @var \General\Model\UserModel $user_model */
						$user_model = D('General/User');
						C('TOKEN_ON', false);
						$result2 = $user_model->cancelRole($role_id, $user_id, $meeting_id);
						if(!$result2['status']) $result = $result2;
					}

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'assign_meeting_manager':
					if(!in_array('SEVERAL-MEETING.MEETING_MANAGER', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有管理会务人员的权限',
						'__ajax__' => true
					];
					$role_id               = I('post.rid', 0, 'int');
					$meeting_id            = I('post.mid', 0, 'int');
					$user_id_arr           = I('post.uid');
					$meeting_manager_logic = new MeetingManagerLogic();
					$count                 = 0;
					foreach($user_id_arr as $user_id){
						$result = $meeting_manager_logic->create($meeting_id, $user_id, $role_id);
						if($result['status']){
							// 分配角色
							/** @var \General\Model\UserModel $user_model */
							$user_model = D('General/User');
							$result2    = $user_model->assignRole($role_id, $user_id, $meeting_id);
							if($result2['status']) $count++;
						}
					}
					if($count>=count($user_id_arr)) return ['status' => true, 'message' => '分配成功', '__ajax__' => true];
					elseif($count == 0) return ['status' => false, 'message' => '分配失败', '__ajax__' => true];
					else return ['status' => true, 'message' => "部分分配成功 ($count)", '__ajax__' => true];
				break;
				case 'release':
					if(!in_array('SEVERAL-MEETING.RELEASE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有发布会议的权限',
						'__ajax__' => true
					];
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					$meeting_id    = I('post.id', 0, 'int');
					$result        = $meeting_model->modify(['id' => $meeting_id], ['process_status' => 2]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'cancel_release':
					if(!in_array('SEVERAL-MEETING.CANCEL_RELEASE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有取消发布会议的权限',
						'__ajax__' => true
					];
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					$meeting_id    = I('post.id', 0, 'int');
					$result        = $meeting_model->modify(['id' => $meeting_id], ['process_status' => 1]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_detail':



					/** @var \RoyalwissD\Model\MeetingModel $meeting_model */
					$meeting_model = D('RoyalwissD/Meeting');
					/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
					$meeting_column_control_model = D('General/MeetingColumnControl');
					$general_meeting_logic = new GeneralMeetingLogic();
					$meeting_logic = new RoyalwissDMeetingLogic();
					$meeting_id                  = I('post.id', 0, 'int');
					$column_list                 = $meeting_column_control_model->getMeetingControlledColumn($general_meeting_logic->getTypeByModule(MODULE_NAME), $meeting_column_control_model::ACTION_READ);
					$column_head                 = $column_name_list = [];
					$list                        = $meeting_model->getList(array_merge([
						MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => ['=', $meeting_id],
						MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['user'] => Session::getCurrentUser(),
						MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['type'] => $general_meeting_logic->getTypeByModule(MODULE_NAME)
					]));
					foreach($column_list as $column){
						if(!$column['view']) continue;
						$column_head[]      = $column['name'];
						$column_name_list[] = $column['form'];
					}
					$list = $meeting_logic->setData('get_detail', [
						'dataList'    => $list,
						'columnValue' => $column_name_list,
						'columnName'  => $column_head
					]);

					return array_merge($list, ['__ajax__' => true]);

				break;
				case 'modify':
					if(!in_array('SEVERAL-MEETING.MODIFY', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有修改会议的权限',
						'__ajax__' => true
					];
					$str_obj = new StringPlus();
					$data    = $opt['data'];
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					// 创建会议记录
					unset($data['brief']);
					$result = $meeting_model->modify(['id' => $opt['meetingID']], array_merge($data, [
						'name_pinyin'     => $str_obj->getPinyin($data['name'], true, ''),
						'brief'           => $opt['originalPost']['brief'],
						'start_time'      => Time::isTimeFormat($data['start_time']),
						'end_time'        => Time::isTimeFormat($data['end_time']),
						'sign_start_time' => Time::isTimeFormat($data['sign_start_time']),
						'sign_end_time'   => Time::isTimeFormat($data['sign_end_time'])
					]));

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'delete': // 删除项目
					if(!in_array('SEVERAL-MEETING.DELETE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有删除会议的权限',
						'__ajax__' => true
					];
					$id_arr = explode(',', $opt['id']);
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					$result        = $meeting_model->drop(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable': // 启用项目
					if(!in_array('SEVERAL-MEETING.ENABLE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有启用会议的权限',
						'__ajax__' => true
					];
					$id_arr = explode(',', $opt['id']);
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					$result        = $meeting_model->enable(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用项目
					if(!in_array('SEVERAL-MEETING.DISABLE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有禁用会议的权限',
						'__ajax__' => true
					];
					$id_arr = explode(',', $opt['id']);
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					$result        = $meeting_model->disable(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'reset_and_order_column':
					if(!UserLogic::isPermitted('SEVERAL-MEETING.MANAGE_LIST_COLUMN')) return [
						'status'   => false,
						'message'  => '您没有控制列表字段的权限',
						'__ajax__' => true
					];
					/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
					$meeting_column_control_model = D('General/MeetingColumnControl');
					$meeting_type                 = $opt['meetingType'];
					$post                         = $opt['post'];
					// 锁表
					$meeting_column_control_model->lock('read');
					$meeting_column_control_model->lock('write');
					// 删除旧数据
					$meeting_column_control_model->where([
						'mtype'  => $meeting_type,
						'action' => $meeting_column_control_model::ACTION_READ
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
							'mtype'    => $meeting_type,
							'action'   => $meeting_column_control_model::ACTION_READ,
							'creator'  => Session::getCurrentUser(),
							'creatime' => Time::getCurrentTime()
						];
					}
					$result = $meeting_column_control_model->addAll($data, [
						'mtype'  => $meeting_type,
						'action' => $meeting_column_control_model::ACTION_READ
					], true);
					// 解锁
					$meeting_column_control_model->unlock();

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
					if(!UserLogic::isPermitted('SEVERAL-MEETING.MANAGE_SEARCH_COLUMN')) return [
						'status'   => false,
						'message'  => '您没有管理搜索字段的权限',
						'__ajax__' => true
					];
					$post = $opt['post'];
					$column_str = $post['column'];
					$column     = explode(',', $column_str);
					$meeting_type                 = $opt['meetingType'];
					/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
					$meeting_column_control_model = D('General/MeetingColumnControl');
					$meeting_column_control_model->where([
						'mtype'    => $meeting_type,
						'action' => $meeting_column_control_model::ACTION_SEARCH
					])->save(['search' => 0]);
					if($column_str != ''){
						$result = $meeting_column_control_model->where([
							'mtype'    => $meeting_type,
							'action' => $meeting_column_control_model::ACTION_SEARCH,
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
				case '':
				break;
				default:
					break;
			}
		}

		/**
		 * 通过URL参数获取会议记录的状态码
		 *
		 * @param string $param      URL参数
		 * @param bool   $output_str 是否输出字符串结果
		 *
		 * @return array|string
		 * @throws Exception
		 */
		public function getStatusByParam($param, $output_str = true){
			$result     = [];
			$result_str = '(';
			$found      = 0;
			foreach(self::URL_PARAMETER_STATUS as $val){
				if($param == $val['param']){
					$result[] = $val['status'];
					$result_str .= "$val[status], ";
					$found = 1;
				}
			}
			$result_str = trim($result_str, ', ');
			$result_str .= ')';
			if($output_str && $found) return $result_str;
			elseif(!$output_str) return $result;
			else throw new Exception('错误的会议参数');
		}

		/**
		 * 通过会议记录的状态码获取URL参数
		 *
		 * @param int $status 会议记录的状态码
		 *
		 * @return string
		 */
		public function getParamByType($status){
			foreach(self::URL_PARAMETER_STATUS as $val){
				if($status == $val['status']) return $val['type'];
			}

			return '';
		}

		public function initMeetingColumnControlRecord(){
			$setNecessaryColumn    = function ($column_name){
				$list = [
					'sign_start_time',
					'sign_end_time',
					'start_time',
					'end_time',
					'name'
				];

				return in_array($column_name, $list) ? 1 : 0;
			};
			$setSearchColumn       = function ($column_name){
				$list = [
					'name',
					'name_pinyin'
				];

				return in_array($column_name, $list) ? 1 : 0;
			};
			$general_meeting_logic = new GeneralMeetingLogic();
			// 2、瑞丽斯成交会
			/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
			$meeting_column_control_model = D('General/MeetingColumnControl');
			$meeting_column_control_model->where('0 = 0')->delete(); // 先清除旧数据
			$meeting_logic              = new RoyalwissDMeetingLogic();
			$module                     = 'RoyalwissD';
			$meeting_type               = $general_meeting_logic->getTypeByModule($module);
			$meeting_write_column_list        = $meeting_logic->getControlledColumn($meeting_column_control_model::ACTION_WRITE);
			$meeting_read_column_list        = $meeting_logic->getControlledColumn($meeting_column_control_model::ACTION_READ);
			$meeting_search_column_list = $meeting_logic->getSearchColumn();
			$data_write                 = $data_search = $data_read = [];
			foreach($meeting_write_column_list as $value){
				$data_write[] = [
					'mtype'    => $meeting_type,
					'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
					'form'     => $value['column_name'],
					'table'    => $value['table_name'],
					'name'     => $value['column_comment'],
					'must'     => $setNecessaryColumn($value['column_name']),
					'view'     => 1,
					'creatime' => Time::getCurrentTime(),
					'creator'  => Session::getCurrentUser(),
					'action'   => $meeting_column_control_model::ACTION_WRITE
				];
			}
			foreach($meeting_search_column_list as $value){
				$data_search[] = [
					'mtype'    => $meeting_type,
					'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
					'form'     => $value['column_name'],
					'table'    => $value['table_name'],
					'name'     => $value['column_comment'],
					'view'     => 1,
					'search'   => $setSearchColumn($value['column_name']),
					'creatime' => Time::getCurrentTime(),
					'creator'  => Session::getCurrentUser(),
					'action'   => $meeting_column_control_model::ACTION_SEARCH
				];
			}
			foreach($meeting_read_column_list as $value){
				$data_read[] = [
					'mtype'    => $meeting_type,
					'code'     => strtoupper("$module-$value[table_name]-$value[column_name]"),
					'form'     => $value['column_name'],
					'table'    => $value['table_name'],
					'name'     => $value['column_comment'],
					'view'     => 1,
					'creatime' => Time::getCurrentTime(),
					'creator'  => Session::getCurrentUser(),
					'action'   => $meeting_column_control_model::ACTION_READ
				];
			}

			$result1 = $meeting_column_control_model->addAll($data_write);
			$result2 = $meeting_column_control_model->addAll($data_search);
			$result3 = $meeting_column_control_model->addAll($data_read);

			return ($result1 && $result2 && $result3) ? [
				'status'  => true,
				'message' => '已初始化会议字段记录 ('.count($meeting_write_column_list).')'
			] : [
				'status'  => false,
				'message' => '初始化会议字段记录失败'
			];
		}
	}