<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-11
	 * Time: 16:01
	 */
	namespace RoyalwissD\Controller;

	use CMS\Controller\CMS;
	use CMS\Logic\ExcelLogic;
	use CMS\Logic\PageLogic;
	use CMS\Model\CMSModel;
	use RoyalwissD\Logic\ClientLogic;
	use RoyalwissD\Logic\MeetingConfigureLogic;
	use RoyalwissD\Model\ClientColumnControlModel;
	use RoyalwissD\Model\ClientModel;
	use RoyalwissD\Model\UnitModel;
	use Think\Page;

	class ClientController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
			$this->initMeetingID();
		}

		public function manage(){
			$client_logic = new ClientLogic();
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
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
			$client_column_control_model = D('RoyalwissD/ClientColumnControl');
			// 获取客户模块列表字段
			$column_list       = $client_column_control_model->getClientControlledColumn($this->meetingID, $client_column_control_model::ACTION_READ);
			$column_list_write = $client_column_control_model->getClientControlledColumn($this->meetingID, $client_column_control_model::ACTION_WRITE);
			$column_list       = $client_logic->setData('column_setting:read', $column_list);
			$this->assign('column_list', $column_list);
			$this->assign('column_list_write', $column_list_write);
			// 获取搜索字段
			$column_list_search = $client_column_control_model->getClientSearchColumn($this->meetingID);
			$search_column_name       = $client_logic->setData('column_setting:search', $column_list_search);
			$this->assign('column_list_search', $column_list_search);
			$this->assign('search_column_name', $search_column_name);
			// 获取列表数据
			$model_control_column = $this->getModelControl();
			$total                = $list = $client_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']              => ['!=', 2],
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			// 设定额外数据并筛选数据
			$list = $client_logic->setData('manage:set_data', [
				'list'     => $list,
				'urlParam' => I('get.')
			]);
			// 获取统计数据
			$statistics = $client_logic->setData('manage:statistics', [
				'list'     => $list,
				'total'    => $total,
				'urlParam' => I('get.')
			]);
			$this->assign('statistics', $statistics);
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
			$this->assign('group_list', $group_list);
			$this->assign('unit_is_new_list', UnitModel::IS_NEW);
			$this->assign('gender_list', ClientModel::GENDER);
			$this->assign('is_new_list', ClientModel::IS_NEW);
			$this->assign('type_list', ClientModel::TYPE);
			$this->assign('default_order_column', I('get.'.CMS::URL_CONTROL_PARAMETER['orderColumn'], CMS::DEFAULT_ORDER_COLUMN));
			$this->assign('default_order_method', I('get.'.CMS::URL_CONTROL_PARAMETER['orderMethod'], CMS::DEFAULT_ORDER_METHOD));
			$this->display();
		}

		/**
		 * 创建参会人员
		 */
		public function create(){
			if(IS_POST){
				$meeting_logic = new ClientLogic();
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
			/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
			$client_column_control_model = D('RoyalwissD/ClientColumnControl');
			$column_list                 = $client_column_control_model->getClientControlledColumn($this->meetingID, $client_column_control_model::ACTION_WRITE);
			$this->assign('column_list', $column_list);
			$this->assign('gender_list', ClientModel::GENDER);
			$this->assign('is_new_list', ClientModel::IS_NEW);
			$this->assign('type_list', ClientModel::TYPE);
			$this->assign('unit_is_new_list', UnitModel::IS_NEW);
			$this->display();
		}

		public function fieldSetting(){
			$client_logic = new ClientLogic();
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
			/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
			$client_column_control_model = D('RoyalwissD/ClientColumnControl');
			$column_list                 = $client_column_control_model->getClientControlledColumn($this->meetingID, $client_column_control_model::ACTION_WRITE);

			$column_list                 = $client_logic->setData('column_setting', $column_list);
			$this->assign('column_list', $column_list);
			$this->display();
		}

		public function import(){
			$client_logic = new ClientLogic();
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
			$this->display();
		}

		public function fieldContrast(){
			$client_logic = new ClientLogic();
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
			/** @var \General\Model\UploadLogModel $upload_log_model */
			$upload_log_model = D('General/UploadLog');
			$log_id           = I('get.logID', 0, 'int');
			if($upload_log_model->fetch(['id' => $log_id])){
				$upload_record = $upload_log_model->getObject();
				// 获取可控制的客户写入字段
				/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
				$client_column_control_model = D('RoyalwissD/ClientColumnControl');
				$column_list                 = $client_column_control_model->getClientControlledColumn($this->meetingID, $client_column_control_model::ACTION_WRITE);
				// 获取导入的Excel数据
				$file_path   = trim($upload_record['save_path'], '/');
				$excel_logic = new ExcelLogic();
				$read_result = $excel_logic->readCustomData($file_path);
				$this->assign('data_head', $read_result['data']['head']);
				$this->assign('data_body', $read_result['data']['body']);
				$this->assign('column_list', $column_list);
				$this->display();
			}
			else{
				$this->error('缺少上传日志参数');
				exit;
			}
		}

		public function importResult(){
			$result_id = I('get.resultID', 0, 'int');
			/** @var \RoyalwissD\Model\ClientImportResultModel $client_import_result_model */
			$client_import_result_model = D('RoyalwissD/ClientImportResult');
			if($client_import_result_model->fetch(['id' => $result_id])){
				$record = $client_import_result_model->getObject();
				$this->assign('result', $record);
				$this->display();
			}
			else{
				$this->error('缺少导入结果参数');
				exit;
			}
		}

		public function modify(){
			if(IS_POST){
				$meeting_logic = new ClientLogic();
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
			$client_id = I('get.id', 0, 'int');
			if($client_id == 0) $this->error('缺少必要参数');
			// 获取客户资料
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			$client       = $client_model->getList([
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['clientID']  => ['=', $client_id],
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]);
			if(count($client)>0) $client = $client[0];
			// 获取字段控制信息
			/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
			$client_column_control_model = D('RoyalwissD/ClientColumnControl');
			$column_list                 = $client_column_control_model->getClientControlledColumn($this->meetingID, $client_column_control_model::ACTION_WRITE);
			$this->assign('column_list', $column_list);
			$this->assign('gender_list', ClientModel::GENDER);
			$this->assign('is_new_list', ClientModel::IS_NEW);
			$this->assign('type_list', ClientModel::TYPE);
			$this->assign('unit_is_new_list', UnitModel::IS_NEW);
			$this->assign('info', $client);
			$this->display();
		}

		public function exportTemplate(){
			/** @var \General\Model\MeetingModel $meeting_model */
			$meeting_model = D('General/Meeting');
			if(!($meeting_model->fetch(['id' => $this->meetingID]))){
				$this->error('找不到会议');
				exit;
			}
			$meeting      = $meeting_model->getObject();
			$meeting_name = $meeting['name'];
			$excel_logic  = new ExcelLogic();
			/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control */
			$client_column_control = D('RoyalwissD/ClientColumnControl');
			$column_list           = $client_column_control->getClientControlledColumn($this->meetingID, $client_column_control::ACTION_WRITE);
			$column_head           = [
				0 => []
			];
			foreach($column_list as $column){
				if($column['view'] == 1) $column_head[0][] = $column['name'];
			}
			$excel_logic->writeCustomData($column_head, [
				'fileName'     => "[$meeting_name]-客户数据导入模板",
				'creator'      => 'Quasar',
				'lastModifier' => 'Quasar',
				'title'        => "$meeting_name 会议客户数据模板",
				'subject'      => "客户数据导入模板",
				'keyword'      => "自动导出, PHPExcel, Quasar, $meeting_name",
				'company'      => '吉美集团-瑞辉医疗',
				'hasHead'      => true,
				'download'     => true,
			]);
		}

		public function exportData(){
			/** @var \General\Model\MeetingModel $meeting_model */
			$meeting_model = D('General/Meeting');
			if(!($meeting_model->fetch(['id' => $this->meetingID]))){
				$this->error('找不到会议');
				exit;
			}
			$meeting      = $meeting_model->getObject();
			$meeting_name = $meeting['name'];
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
			$client_column_control_model = D('RoyalwissD/ClientColumnControl');
			$client_logic                = new ClientLogic();
			$excel_logic                 = new ExcelLogic();
			$column_list                 = $client_column_control_model->getClientControlledColumn($this->meetingID, $client_column_control_model::ACTION_READ);
			$column_head                 = $column_name_list = [];
			// 获取列表数据
			$model_control_column = $this->getModelControl();
			$list                 = $client_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']              => ['!=', 2],
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			foreach($column_list as $column){
				if(!$column['view']) continue;
				$column_head[]      = $column['name'];
				$column_name_list[] = $column['form'];
			}
			$list = $client_logic->setData('downloadData', [
				'dataList'    => $list,
				'columnValue' => $column_name_list,
				'columnName'  => $column_head,
				'urlParam'    => I('get.')
			]);
			$excel_logic->writeCustomData($list, [
				'fileName'     => "[$meeting_name]-客户数据导入模板",
				'creator'      => 'Quasar',
				'lastModifier' => 'Quasar',
				'title'        => "$meeting_name 会议客户数据模板",
				'subject'      => "客户数据导入模板",
				'keyword'      => "自动导出, PHPExcel, Quasar, $meeting_name",
				'company'      => '吉美集团-瑞辉医疗',
				'hasHead'      => true,
				'download'     => true,
			]);
		}

		public function test(){
			$client_logic = new ClientLogic();
			$result       = $client_logic->sign("220,219", 30, false);
			print_r($result);
			exit;
		}
	}