<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-14
	 * Time: 18:05
	 */
	namespace RoyalwissD\Controller;

	use CMS\Controller\CMS;
	use CMS\Logic\PageLogic;
	use CMS\Model\CMSModel;
	use RoyalwissD\Logic\MeetingConfigureLogic;
	use RoyalwissD\Logic\ReportLogic;
	use RoyalwissD\Model\AttendeeModel;
	use RoyalwissD\Model\ClientModel;
	use RoyalwissD\Model\UnitModel;
	use Think\Page;

	class ReportController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
			$this->initMeetingID();
		}

		public function client(){
			$client_logic = new ReportLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $client_logic->handlerRequest($type);
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
			// 加载客户模块配置信息
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			if($meeting_configure_model->fetch(['mid' => $this->meetingID])){
				$meeting_configure_logic = new MeetingConfigureLogic();
				$configure               = $meeting_configure_model->getObject();
				$client_repeat_setting   = $meeting_configure_logic->decodeClientRepeatMode($configure['client_repeat_mode']);
				$this->assign('client_repeat_mode', $client_repeat_setting);
				$this->assign('client_repeat_action', $configure['client_repeat_action']);
			}
			/** @var \RoyalwissD\Model\ReportModel $report_model */
			$report_model = D('RoyalwissD/Report');
			/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
			$client_column_control_model = D('RoyalwissD/ClientColumnControl');
			// 获取客户模块列表字段
			$column_list       = $client_column_control_model->getClientControlledColumn($this->meetingID, $client_column_control_model::ACTION_READ);
			$column_list_write = $client_column_control_model->getClientControlledColumn($this->meetingID, $client_column_control_model::ACTION_WRITE);
			$column_list       = $client_logic->setData('fieldSetting', $column_list);
			$this->assign('column_list', $column_list);
			$this->assign('column_list_write', $column_list_write);
			// 获取列表数据
			$model_control_column = $this->getModelControl();
			$list                 = $report_model->getClientList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']              => ['!=', 2],
				$report_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			// 设定额外数据并筛选数据
			$list        = $client_logic->setData('manage:set_data', [
				'list'     => $list,
				'urlParam' => I('get.')
			]);
			$page_object = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			// 分页
			$list = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$this->assign('list', $list);
			$pagination = $page_object->show();
			$this->assign('pagination', $pagination);
			// 获取分组数据
			/** @var \RoyalwissD\Model\GroupingModel $group_model */
			$group_model = D('RoyalwissD/Grouping');
			$group_list  = $group_model->getSelectedList($this->meetingID);
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			$this->assign('group_list', $group_list);
			$this->assign('gender_list', $client_model::GENDER);
			$this->assign('is_new_list', $client_model::IS_NEW);
			$this->assign('type_list', $client_model::TYPE);
			$this->assign('sign_list', AttendeeModel::SIGN_STATUS);
			$this->assign('unit_is_new_list', UnitModel::IS_NEW);
			$this->assign('team_list', $client_model->getTeamSelectedList($this->meetingID));
			$this->assign('default_order_column', I('get.'.CMS::URL_CONTROL_PARAMETER['orderColumn'], CMS::DEFAULT_ORDER_COLUMN));
			$this->assign('default_order_method', I('get.'.CMS::URL_CONTROL_PARAMETER['orderMethod'], CMS::DEFAULT_ORDER_METHOD));
			$this->display();
		}
	}