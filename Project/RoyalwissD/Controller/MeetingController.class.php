<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-9
	 * Time: 10:13
	 */
	namespace RoyalwissD\Controller;

	use CMS\Logic\MeetingLogic as CMSMeetingLogic;
	use CMS\Logic\PageLogic;
	use CMS\Logic\Session;
	use CMS\Logic\UserLogic;
	use CMS\Model\CMSModel;
	use General\Logic\MeetingLogic as GeneralMeetingLogic;
	use General\Logic\PermissionLogic;
	use RoyalwissD\Logic\MeetingLogic;
	use RoyalwissD\Model\MeetingModel;
	use Think\Page;

	class MeetingController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
			$this->_updateMeetingStatus();
		}

		/**
		 * 自动更新会议状态
		 */
		private function _updateMeetingStatus(){
			/** @var \RoyalwissD\Model\MeetingModel $meeting_model */
			$meeting_model         = D('RoyalwissD/Meeting');
			$general_meeting_logic = new GeneralMeetingLogic();
			$meeting_list          = $meeting_model->getList([
				CMSModel::CONTROL_COLUMN_PARAMETER['status']        => ['<>', 2],
				MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['user'] => Session::getCurrentUser(),
				MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['type'] => $general_meeting_logic->getTypeByModule(MODULE_NAME)
			]);
			$current_time          = time();
			$update_data           = [
				1 => [],
				2 => [],
				3 => [],
				4 => []
			];
			foreach($meeting_list as $meeting){
				$start_time = strtotime($meeting['start_time']);
				$end_time   = strtotime($meeting['end_time']);
				if($start_time>$current_time && $meeting['process_status'] != 2) $update_data[1][] = $meeting['id'];
				elseif($end_time<$current_time) $update_data[4][] = $meeting['id'];
				elseif($start_time<=$current_time && $end_time>=$current_time) $update_data[3][] = $meeting['id'];
			}
			if(count($update_data[1])>0) $meeting_model->where([
				'id' => [
					'in',
					$update_data[1]
				]
			])->save(['process_status' => 1]);
			if(count($update_data[3])>0) $meeting_model->where([
				'id' => [
					'in',
					$update_data[3]
				]
			])->save(['process_status' => 3]);
			if(count($update_data[4])>0) $meeting_model->where([
				'id' => [
					'in',
					$update_data[4]
				]
			])->save(['process_status' => 4]);
		}

		public function manage(){
			$meeting_logic = new MeetingLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $meeting_logic->handlerRequest($type, ['curUserHighestRoleLevel' => $this->userHighestRoleLevel]);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			if(!UserLogic::isPermitted('SEVERAL-MEETING.VIEW')) $this->error('您没有查看会议的权限');
			// 获取搜索字段
			/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
			$meeting_column_control_model = D('General/MeetingColumnControl');
			$general_meeting_logic                = new GeneralMeetingLogic();
			$meeting_type = $general_meeting_logic->getTypeByModule(MODULE_NAME);
			$column_list_search = $meeting_column_control_model->getMeetingSearchColumn($meeting_type);
			$search_column_name = $meeting_logic->setData('column_setting:search', $column_list_search);
			$this->assign('column_list_search', $column_list_search);
			$this->assign('search_column_name', $search_column_name);
			// 获取读取字段
			$column_list       = $meeting_column_control_model->getMeetingControlledColumn($meeting_type, $meeting_column_control_model::ACTION_READ);
			$this->assign('column_list', $column_list);
			/** @var \RoyalwissD\Model\MeetingModel $meeting_model */
			$meeting_model         = D('RoyalwissD/Meeting');
			$cms_meeting_logic     = new CMSMeetingLogic();
			$general_meeting_logic = new GeneralMeetingLogic();
			$model_control_column  = $this->getModelControl();
			// 处理会议列表URL参数\\\\\\\\\\\\\
			$type_status = I('get.process', '');
			if($type_status) $type_status = $cms_meeting_logic->getStatusByParam($type_status);
			$process_status = $type_status ? [
				MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['processStatus'] => [
					'in',
					$type_status
				]
			] : [];
			// 处理会议列表URL参数/////////////
			$total = $list        = $meeting_model->getList(array_merge($model_control_column, $process_status, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']        => ['<>', 2],
				MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['user'] => Session::getCurrentUser(),
				MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['type'] => $general_meeting_logic->getTypeByModule(MODULE_NAME)
			]));
			$list        = $meeting_logic->setData('manage', ['list' => $list, 'urlParam' => I('get.')]);
			// 获取统计数据
			$statistics = $meeting_logic->setData('manage:statistics', [
				'list'     => $list,
				'total'    => $total,
				'urlParam' => I('get.')
			]);
			$page_object = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$pagination = $page_object->show();
			$this->assign('list', $list);
			$this->assign('statistics', $statistics);
			$this->assign('pagination', $pagination);
			// 获取配置列表
			/** @var \General\Model\ApiConfigureModel $api_configure_model */
			$api_configure_model = D('General/ApiConfigure');
			$api_configure_list  = $api_configure_model->getSelectedList($meeting_type);
			$this->assign('api_configure_list', $api_configure_list);
			$this->display();
		}

		public function create(){
			if(IS_POST){
				$meeting_logic = new MeetingLogic();
				$type          = strtolower(I('post.requestType', ''));
				$result        = $meeting_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			if(!UserLogic::isPermitted('SEVERAL-MEETING.CREATE')) $this->error('您没有创建会议的权限');
			/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
			$meeting_column_control_model = D('General/MeetingColumnControl');
			$meeting_logic                = new GeneralMeetingLogic();
			$column_list                  = $meeting_column_control_model->getMeetingControlledColumn($meeting_logic->getTypeByModule(MODULE_NAME), $meeting_column_control_model::ACTION_WRITE);
			$this->assign('column_list', $column_list);
			$this->display();
		}

		public function columnSetting(){
			$meeting_logic = new MeetingLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $meeting_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			if(!UserLogic::isPermitted('SEVERAL-MEETING.MANAGE_COLUMN')) $this->error('您没有自定义字段的权限');
			/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
			$meeting_column_control_model = D('General/MeetingColumnControl');
			$general_meeting_logic        = new GeneralMeetingLogic();
			$column_list                  = $meeting_column_control_model->getMeetingControlledColumn($general_meeting_logic->getTypeByModule(MODULE_NAME), $meeting_column_control_model::ACTION_WRITE);
			$column_list                  = $meeting_logic->setData('field_setting', $column_list);
			$this->assign('column_list', $column_list);
			$this->display();
		}

		public function index(){
			$this->display();
		}

		public function configure(){
			$meeting_logic = new MeetingLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $meeting_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			if($meeting_configure_model->fetch(['mid' => I('get.mid', 0, 'int')])){
				$this->display();
			}
			else $this->error('找不到记录');
		}

		public function modify(){
			$meeting_logic = new MeetingLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $meeting_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			if(!UserLogic::isPermitted('SEVERAL-MEETING.MODIFY')) $this->error('您没有修改会议的权限');
			// 获取控制字段
			/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
			$meeting_column_control_model = D('General/MeetingColumnControl');
			$meeting_logic                = new GeneralMeetingLogic();
			$column_list                  = $meeting_column_control_model->getMeetingControlledColumn($meeting_logic->getTypeByModule(MODULE_NAME), $meeting_column_control_model::ACTION_WRITE);
			$this->assign('column_list', $column_list);
			// 获取会议数据
			$meeting_id = I('get.mid', 0, 'int');
			/** @var \RoyalwissD\Model\MeetingModel $meeting_model */
			$meeting_model = D('RoyalwissD/Meeting');
			$meeting       = $meeting_model->getList([
				MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => ['=', $meeting_id],
				MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['user']      => Session::getCurrentUser(),
			]);
			$meeting       = $meeting[0];
			$this->assign('meeting', $meeting);
			$this->display();
		}
	}