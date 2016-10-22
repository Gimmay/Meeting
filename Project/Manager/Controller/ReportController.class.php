<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-20
	 * Time: 16:24
	 */
	namespace Manager\Controller;

	use Manager\Logic\ExcelLogic;
	use Manager\Logic\ReportLogic;
	use Think\Page;

	class ReportController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function joinReceivables(){
			/** @var \Manager\Model\ReportModel $model */
			$model   = D('Report');
			$mid     = I('get.mid', 0, 'int');
			$options = [];
			if($mid != 0) $options['mid'] = $mid;
			$total             = $model->getJoinReceivablesList(0, array_merge([
				'status'  => 'not deleted',
				'keyword' => I('get.keyword', ''),
			], $options));
			$page_record_count = isset($_GET['_page_count']) ? I('get._page_count', 20, 'int') : C('PAGE_RECORD_COUNT');
			$page_object       = new Page($total, $page_record_count);
			$page_show         = $page_object->show();
			$list              = $model->getJoinReceivablesList(2, array_merge([
				'status'  => 'not deleted',
				'keyword' => I('get.keyword', ''),
				'_order'  => I('get._column', 'name').' '.I('get._sort', 'desc'),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
			], $options));
			$all_count         = $model->getJoinReceivablesList(0, array_merge([
				'status'  => 'not deleted',
				'keyword' => I('get.keyword', ''),
			], $options));
			$this->assign('list', $list);
			$this->assign('total', $all_count);
			$this->assign('page_show', $page_show);
			$this->display();
		}

		/**
		 * 接收GET参数: type / mid / keyword / _column / _sort
		 */
		public function exportExcel(){
			$type = I('get.type', '');
			switch($type){
				case 'joinReceivables':
					/** @var \Manager\Model\ReportModel $model */
					$model       = D('Report');
					$logic       = new ReportLogic();
					$excel_logic = new ExcelLogic();
					$mid         = I('get.mid', 0, 'int');
					$options     = [];
					if($mid != 0) $options['mid'] = $mid;
					$list = $model->getJoinReceivablesList(2, array_merge([
						'status'  => 'not deleted',
						'keyword' => I('get.keyword', ''),
						'_order'  => I('get._column', 'name').' '.I('get._sort', 'desc'),
					], $options));
					$list = $logic->extendData('exportExcel:joinReceivables', $list, [
						'exceptColumn' => [
							'cid',
							'mid',
							'id',
							'status'
						]
					]);
					$excel_logic->exportCustomData($list, [
						'fileName'    => '导出参会收款数据',
						'title'       => '导出参会收款数据',
						'subject'     => '导出参会收款数据',
						'description' => '吉美会议系统导出参会收款数据',
						'company'     => '吉美集团',
						'hasHead'     => true
					]);
				break;
			}
		}
	}