<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-14
	 * Time: 18:12
	 */
	namespace RoyalwissD\Controller;

	use CMS\Logic\Session;
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
			$meeting_id = 1;
			$module                           = 'RoyalwissD';
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
	}