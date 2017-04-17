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
	use CMS\Logic\UserLogic;
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
			$report_logic = new ReportLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $report_logic->handlerRequest($type);
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
			if(!UserLogic::isPermitted('SEVERAL-REPORT_CLIENT.VIEW')) $this->error('您没有查看客户报表的权限');
			/** @var \RoyalwissD\Model\ReportModel $report_model */
			$report_model = D('RoyalwissD/Report');
			/** @var \RoyalwissD\Model\ReportColumnControlModel $report_column_control_model */
			$report_column_control_model = D('RoyalwissD/ReportColumnControl');
			// 获取列表控制字段
			$column_list = $report_column_control_model->getClientControlledColumn($this->meetingID);
			$this->assign('column_list', $column_list);
			// 获取检索字段
			$search_column_list = $report_column_control_model->getClientSearchColumn($this->meetingID);
			$search_column_name = $report_logic->setData('column_setting:search', $search_column_list);
			$this->assign('search_column_name', $search_column_name);
			// 获取列表数据
			$model_control_column = $this->getModelControl();
			$list = $report_model->getClientList(array_merge($model_control_column, [
				$report_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID']    => $this->meetingID,
				$report_model::CONTROL_COLUMN_PARAMETER_SELF['type']    => true
			]));
			// 设定额外数据并筛选数据
			$list = $report_logic->setData('client:set_data', [
				'list'     => $list,
				'urlParam' => I('get.')
			]);
			$statistics = $report_logic->setData('client:statistics', $list);
			$this->assign('statistics', $statistics);
			// 分页
			$page_object = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$pagination = $page_object->show();
			$this->assign('pagination', $pagination);
			$this->assign('list', $list);
			// 获取其他模块数据
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			$this->assign('team_list', $client_model->getTeamSelectedList($this->meetingID));
			/** @var \RoyalwissD\Model\UnitModel $unit_model */
			$unit_model = D('RoyalwissD/Unit');
			$this->assign('area_list', $unit_model->getUnitSelectedArea($this->meetingID));
			$this->assign('gender_list', $client_model::GENDER);
			$this->assign('is_new_list', $client_model::IS_NEW);
			$this->assign('type_list', $client_model::TYPE);
			$this->assign('sign_list', AttendeeModel::SIGN_STATUS);
			$this->assign('unit_is_new_list', UnitModel::IS_NEW);
			$this->assign('default_order_column', I('get.'.CMS::URL_CONTROL_PARAMETER['orderColumn'], CMS::DEFAULT_ORDER_COLUMN));
			$this->assign('default_order_method', I('get.'.CMS::URL_CONTROL_PARAMETER['orderMethod'], CMS::DEFAULT_ORDER_METHOD));
			$this->display();
		}

		public function unit(){
			$report_logic = new ReportLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $report_logic->handlerRequest($type);
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
			if(!UserLogic::isPermitted('SEVERAL-REPORT_UNIT.VIEW')) $this->error('您没有查看客户报表的权限');
			/** @var \RoyalwissD\Model\ReportModel $report_model */
			$report_model = D('RoyalwissD/Report');
			// 获取列表数据
			$model_control_column = $this->getModelControl();
			$list = $report_model->getUnitList(array_merge($model_control_column, [
				$report_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID']    => $this->meetingID,
				$report_model::CONTROL_COLUMN_PARAMETER_SELF['type']=>true
			]));
			// 设定额外数据并筛选数据
			$list = $report_logic->setData('unit:set_data', [
				'list'     => $list,
				'urlParam' => I('get.')
			]);
			$statistics = $report_logic->setData('unit:statistics', $list);
			$this->assign('statistics', $statistics);
			// 分页
			$page_object = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$pagination = $page_object->show();
			$this->assign('pagination', $pagination);
			$this->assign('list', $list);
			// 获取其他模块数据
			/** @var \RoyalwissD\Model\UnitModel $unit_model */
			$unit_model = D('RoyalwissD/Unit');
			$this->assign('area_list', $unit_model->getUnitSelectedArea($this->meetingID));
			$this->assign('sign_list', AttendeeModel::SIGN_STATUS);
			$this->assign('unit_is_new_list', UnitModel::IS_NEW);
			$this->assign('default_order_column', I('get.'.CMS::URL_CONTROL_PARAMETER['orderColumn'], CMS::DEFAULT_ORDER_COLUMN));
			$this->assign('default_order_method', I('get.'.CMS::URL_CONTROL_PARAMETER['orderMethod'], CMS::DEFAULT_ORDER_METHOD));
			$this->display();
		}
	}