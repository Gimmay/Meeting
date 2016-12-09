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
			$this->meetingID = $this->initMeetingID($this);
		}

		public function create(){
			if(IS_POST){
				$logic  = new ReportLogic();
				$type   = I('post.requestType');
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['REPORT.CREATE']){
				$this->display();
			}
			else $this->error('您没有创建报表的权限');
		}

		public function manage(){
			if(IS_POST){
				$logic  = new ReportLogic();
				$type   = I('post.requestType');
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['REPORT.VIEW']){
				/** @var \Core\Model\ReportEntryModel $model */
				$model        = D('Core/ReportEntry');
				$report_logic = new ReportLogic();
				$list_count   = $model->findRecord(0, [
					'keyword' => I('get.keyword', ''),
					'status'  => 'not deleted',
					'mid'     => $this->meetingID
				]);
				/* 分页设置 */
				$page_object = new Page($list_count, C('PAGE_RECORD_COUNT'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show = $page_object->show();
				$list      = $model->findRecord(2, [
					'status'  => 'not deleted',
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
					'mid'     => $this->meetingID
				]);
				$list      = $report_logic->setData('manage:set_list', $list);
				$this->assign('list', $list);
				$this->assign('page_show', $page_show);
				$this->display();
			}
			else $this->error('您没有查看报表的权限');
		}

		public function alter(){
			$logic = new ReportLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['REPORT.VIEW']){
				/** @var \Core\Model\ReportEntryModel $model */
				$model = D('Core/ReportEntry');
				$info  = $model->findRecord(1, ['id' => I('get.id', 0, 'int'), 'mid' => I('get.mid', 0, 'int')]);
				$info  = $logic->setData('alter:set_column', $info);
				$this->assign('info', $info);
				$this->display();
			}
			else $this->error('您没有修改报表的权限');
		}

		public function detail(){
			if($this->permissionList['REPORT.VIEW']){
				/** @var \Core\Model\ReportEntryModel $model */
				$model = D('Core/ReportEntry');
				$info  = $model->findRecord(1, [
					'id'     => I('get.id', 0, 'int'),
					'mid'    => I('get.mid', 0, 'int'),
					'status' => 'not deleted'
				]);
				$this->assign('info', $info);
				$this->display();
			}
			else $this->error('您没有查看报表的权限');
		}

		public function fineReport(){
			if($this->permissionList['REPORT.VIEW']){
				$report_id = I('get.id', 0, 'int');
				/** @var \Core\Model\ReportEntryModel $model */
				$model = D('Core/ReportEntry');
				/** @var \Core\Model\AssignRoleModel $assign_role_model */
				$assign_role_model = D('Core/AssignRole');
				$report            = $model->findRecord(1, ['id' => $report_id]);
				if($report['status'] == 0) echo '该报表已被禁用';
				if($report['status'] == 2) echo '该报表已被删除';
				$role           = explode(',', $report['role']);
				$my_role        = $assign_role_model->getRoleByUser(I('session.MANAGER_EMPLOYEE_ID', 0, 'int'), 0, [
					'column' => 'id',
					'format' => 'array'
				]);
				$has_permission = false;
				foreach($role as $val){
					if(in_array($val, $my_role)){
						$has_permission = true;
						break;
					}
				}
				if($has_permission){
					$this->assign('url', $report['url']);
					$this->display();
					exit;
				}
				else $this->error('您没有查看该报表的权限');
			}
			else $this->error('您没有查看报表的权限');
		}

		public function joinReceivables(){
			/** @var \Manager\Model\ReportModel $model */
			$model   = D('Report');
			$options = [];
			if($_GET['mid'] != 0) $options['mid'] = $this->meetingID;
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
			$all_record        = $model->getJoinReceivablesList(2, array_merge([
				'status'  => 'not deleted',
				'keyword' => I('get.keyword', ''),
			], $options));
			$statistics        = [
				'price'       => 0,
				'join'        => count($all_record),
				'sign'        => 0,
				'receivables' => 0
			];
			foreach($all_record as $val){
				$statistics['price'] += $val['price'];
				if($val['price']) $statistics['receivables']++;
				if($val['sign_status'] == 1) $statistics['sign']++;
			}
			$this->assign('statistics', $statistics);
			$this->assign('list', $list);
			$this->assign('page_show', $page_show);
			$this->display();
		}

		public function receivablesDetail(){
			$cid = I('get.cid', 0, 'int');
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model = D('Core/Receivables');
			$self_logic        = new ReportLogic();
			$list              = $receivables_model->findRecord(2, [
				'cid'    => $cid,
				'mid'    => $this->meetingID,
				'status' => 'not deleted'
			]);
			$list              = $self_logic->setData('receivablesDetail:set_column', $list, ['keyword' => I('get.keyword', '')]);
			$this->assign('list', $list);
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
					$model = D('Report');
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					$logic         = new ReportLogic();
					$excel_logic   = new ExcelLogic();
					$options       = [];
					$meeting       = $meeting_model->findMeeting(1, [
						'id'     => $this->meetingID,
						'status' => 'not deleted'
					]);
					if(isset($_GET['mid'])) $options['mid'] = $this->meetingID;
					$list         = $model->getJoinReceivablesList(2, array_merge([
						'status'  => 'not deleted',
						'keyword' => I('get.keyword', ''),
						'_order'  => I('get._column', 'name').' '.I('get._sort', 'desc'),
					], $options));
					$list         = $logic->setData('exportExcel:joinReceivables', $list, [
						'exceptColumn' => [
							'cid',
							'mid',
							'id',
							'status'
						]
					]);
					$meeting_name = trim($meeting['name']);
					$excel_logic->exportCustomData($list, [
						'fileName'    => "[$meeting_name]_导出参会收款数据",
						'title'       => "[$meeting_name]_导出参会收款数据",
						'subject'     => '导出参会收款数据',
						'description' => '吉美会议系统导出参会收款数据',
						'company'     => '吉美集团',
						'hasHead'     => true
					]);
				break;
			}
		}
	}