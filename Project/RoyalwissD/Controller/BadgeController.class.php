<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-14
	 * Time: 18:12
	 */
	namespace RoyalwissD\Controller;

	use CMS\Logic\Session;
	use General\Logic\MeetingLogic;
	use General\Logic\Time;

	class BadgeController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$this->display();
		}

		public function asd(){
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
				];

				return !in_array($column_name, $except_list) ? 1 : 0;
			};
			$meeting_id                 = 1;
			$module                     = 'RoyalwissD';
			/** @var \RoyalwissD\Model\ReceivablesColumnControlModel $receivables_column_control_model */
			$receivables_column_control_model = D('RoyalwissD/ReceivablesColumnControl');
			$receivables_column_list_read     = $receivables_column_control_model->getDatabaseColumn($receivables_column_control_model::ACTION_READ); // 获取收款的读取字段
			$receivables_column_list_search   = $receivables_column_control_model->getDatabaseColumn($receivables_column_control_model::ACTION_SEARCH); // 获取收款的检索字段
			$data_receivables_search          = $data_receivables_read = [];
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
			//			$result6 = $receivables_column_control_model->addAll($data_receivables_search);
			//			$result7 = $receivables_column_control_model->addAll($data_receivables_read);
			//			echo $result6;
			//			echo $result7;
		}

		public function asdf(){
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
			$general_meeting_logic = new MeetingLogic();
			// 2、瑞丽斯成交会
			/** @var \General\Model\MeetingColumnControlModel $meeting_column_control_model */
			$meeting_column_control_model = D('General/MeetingColumnControl');
			//$meeting_column_control_model->where('0 = 0')->delete(); // 先清除旧数据
			$meeting_logic              = new \RoyalwissD\Logic\MeetingLogic();
			$module                     = 'RoyalwissD';
			$meeting_type               = $general_meeting_logic->getTypeByModule($module);
			$meeting_search_column_list = $meeting_logic->getSearchColumn();
			$data_write                 = $data_search = [];
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
			$result2 = $meeting_column_control_model->addAll($data_search);

			return ($result2) ? [
				'status'  => true,
				'message' => '已初始化会议字段记录 ('.count($meeting_search_column_list).')'
			] : [
				'status'  => false,
				'message' => '初始化会议字段记录失败'
			];
		}
	}