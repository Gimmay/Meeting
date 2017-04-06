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
	use CMS\Model\CMSModel;
	use General\Logic\MeetingLogic as GeneralMeetingLogic;
	use RoyalwissD\Logic\MeetingLogic;
	use RoyalwissD\Model\MeetingModel;
	use Think\Page;

	class MeetingController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$meeting_logic = new MeetingLogic();
			if(IS_POST){
				$type          = strtolower(I('post.requestType', ''));
				$result        = $meeting_logic->handlerRequest($type, ['curUserHighestRoleLevel' => $this->userHighestRoleLevel]);
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
			/** @var \RoyalwissD\Model\MeetingModel $meeting_model */
			$meeting_model         = D('RoyalwissD/Meeting');
			$cms_meeting_logic         = new CMSMeetingLogic();
			$general_meeting_logic = new GeneralMeetingLogic();
			$model_control_column  = $this->getModelControl();
			// 处理会议列表URL参数\\\\\\\\\\\\\
			$type_status = I('get.type', '');
			if($type_status) $type_status = $cms_meeting_logic->getStatusByParam($type_status);
			$process_status = $type_status ? [
				MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['processStatus'] => [
					'in',
					$type_status
				]
			] : [];
			// 处理会议列表URL参数/////////////
			$list        = $meeting_model->getList(array_merge($model_control_column, $process_status, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']        => ['<>', 2],
				MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['user'] => Session::getCurrentUser(),
				MeetingModel::CONTROL_COLUMN_PARAMETER_SELF['type'] => $general_meeting_logic->getTypeByModule(MODULE_NAME)
			]));
			$page_object = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list          = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$list          = $meeting_logic->setData('manage', $list);
			$pagination    = $page_object->show();
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
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
			/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
			$meeting_column_control_model = D('General/MeetingColumnControl');
			$meeting_logic                = new GeneralMeetingLogic();
			$column_list                  = $meeting_column_control_model->getMeetingControlledColumn($meeting_logic->getTypeByModule(MODULE_NAME));
			$this->assign('column_list', $column_list);
			$this->display();
		}

		public function fieldSetting(){
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
			/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
			$meeting_column_control_model = D('General/MeetingColumnControl');
			$general_meeting_logic        = new GeneralMeetingLogic();
			$column_list                  = $meeting_column_control_model->getMeetingControlledColumn($general_meeting_logic->getTypeByModule(MODULE_NAME));
			$column_list                  = $meeting_logic->setData('fieldSetting', $column_list);
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
	}