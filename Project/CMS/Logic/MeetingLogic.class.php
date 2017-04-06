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
						'name_pinyin' => $str_obj->getPinyin($data['name'], true, ''),
						'type'        => $meeting_type,
						'creator'     => Session::getCurrentUser(),
						'creatime'    => Time::getCurrentTime(),
						'brief'       => $opt['originalPost']['brief']
					]));

					return array_merge($result, ['__ajax__' => false, '__return__' => U('manage')]);
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
						CMSModel::CONTROL_COLUMN_PARAMETER['status']                       => ['=', 1],
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
							C('TOKEN_ON', false);
							$result2 = $user_model->assignRole($role_id, $user_id, $meeting_id);
							if($result2['status']) $count++;
						}
					}
					if($count>=count($user_id_arr)) return ['status' => true, 'message' => '分配成功', '__ajax__' => true];
					elseif($count == 0) return ['status' => false, 'message' => '分配失败', '__ajax__' => true];
					else return ['status' => true, 'message' => "部分分配成功 ($count)", '__ajax__' => true];
				break;
				case 'release':
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					$meeting_id    = I('post.id', 0, 'int');
					$result        = $meeting_model->modify(['id' => $meeting_id], ['process_status' => 2]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'cancel_release':
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					$meeting_id    = I('post.id', 0, 'int');
					$result        = $meeting_model->modify(['id' => $meeting_id], ['process_status' => 1]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_detail':
					/** @var \General\Model\MeetingModel $meeting_model */
					$meeting_model = D('General/Meeting');
					$meeting_id    = I('post.id', 0, 'int');
					if($meeting_model->fetch(['id' => $meeting_id])){
						$result = $meeting_model->getObject();

						return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => true, 'message' => '该会议不存在'];
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true];
				break;
			}
		}

		public function setData($type, $data){
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
					'start_time',
					'name'
				];

				return in_array($column_name, $list);
			};
			$general_meeting_logic = new GeneralMeetingLogic();
			$result_count          = 0;
			$total_count           = count($general_meeting_logic::TYPE)-1;
			// 2、瑞丽斯成交会
			/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
			$meeting_column_control_model = D('General/MeetingColumnControl');
			$meeting_column_control_model->where('0 = 0')->delete(); // 先清除旧数据
			$meeting_logic       = new RoyalwissDMeetingLogic();
			$module              = 'RoyalwissD';
			$meeting_type        = $general_meeting_logic->getTypeByModule($module);
			$meeting_column_list = $meeting_logic->getControlledColumn(true);
			$data                = [];
			foreach($meeting_column_list as $value){
				$data[] = [
					'mtype'    => $meeting_type,
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
			$result = $meeting_column_control_model->addAll($data);
			if($result) $result_count++;
			if($result_count == $total_count) return ['status' => true, 'message' => '已初始化会议字段记录'];
			elseif($result_count == 0) return ['status' => false, 'message' => '初始化会议字段记录失败'];
			else return ['status' => true, 'message' => "已初始化部分会议字段记录 ($result_count)"];
		}
	}