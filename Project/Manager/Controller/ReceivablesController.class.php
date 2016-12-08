<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-10
	 * Time: 16:45
	 */
	namespace Manager\Controller;

	use Manager\Logic\EmployeeLogic;
	use Manager\Logic\ExcelLogic;
	use Manager\Logic\ReceivablesLogic;
	use Manager\Model\ClientModel;
	use Manager\Model\EmployeeModel;
	use Think\Page;

	class ReceivablesController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function create(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if(isset($result['__return__'])) $url = $result['__return__'];
					else $url = '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			/** @var \Core\Model\PayMethodModel $pay_method_model */
			$pay_method_model  = D('Core/PayMethod');
			$pay_method_result = $pay_method_model->findRecord(2, ['status' => '1']);
			/** @var \Core\Model\PosMachineModel $pos_machine_model */
			$pos_machine_model  = D('Core/PosMachine');
			$pos_machine_result = $pos_machine_model->findRecord(2, ['status' => '1', 'mid' => $this->meetingID]);
			$client_logic       = new ClientModel();
			$client             = $client_logic->getClientSelectUnit($this->meetingID);
			$employee_logic     = new EmployeeModel();
			$employee           = $employee_logic->getEmployeeSelectList();
			$client_result      = $client_model->findClient(1, ['id' => I('get.cid', 0, 'int')]);
			$employee_result    = $employee_model->findEmployee(1, ['id' => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')]);
			$this->assign('client', $client);//遍历当前会议的所有的参会人员
			$this->assign('employee', $employee);//遍历当前会议的所有的工作人员
			$this->assign('pos', $pos_machine_result);//遍历pos机
			$this->assign('pay', $pay_method_result); //遍例支付方式
			$this->assign('client_single', $client_result);
			$this->assign('employee_single', $employee_result);
			$this->display();
		}

		public function details(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model = D('Core/Receivables');
			$option            = [];
			$keyword           = I('get.keyword', '');
			if(isset($_GET['cid'])) $option['cid'] = I('get.cid', 0, 'int');
			$receivables_total = $receivables_model->findRecord(0, array_merge([
				'mid'     => $this->meetingID,
				'status'  => 'not deleted',
				'keyword' => $keyword
			], $option));
			/* 分页设置 */
			$page_object = new Page(count($receivables_total), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$receivables_list = $receivables_model->findRecord(2, array_merge([
				'status'  => 1,
				'mid'     => $this->meetingID,
				'keyword' => $keyword,
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
			], $option));
			$receivables_list = $receivables_logic->setData('details:set_column', $receivables_list);
			$this->assign('list', $receivables_list);
			$this->display();
		}

		public function manage(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\JoinModel $join_model */
			$join_model            = D('Core/Join');
			$keyword               = I('get.keyword', '');
			$receivables_url_param = I('get.receivables', 0, 'int');
			$join_total            = $join_model->findRecord(2, [
				'status'  => 1,
				'mid'     => $this->meetingID,
				'keyword' => $keyword
			]);
			$total                 = count($join_total);
			if(isset($_GET['receivables'])){
				$join_total = $receivables_logic->setData('manage:set_price', $join_total, [
					'mid'         => $this->meetingID,
					'receivables' => $receivables_url_param
				]);
				/* 分页设置 */
				$page_object = new Page(count($join_total), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$join_client = $receivables_logic->setData('manage:pagination', $join_total, [
					'pagination' => [
						$page_object->firstRow,
						$page_object->listRows
					]
				]);
			}
			else{
				/* 分页设置 */
				$page_object = new Page(count($join_total), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$join_client = $join_model->findRecord(2, [
					'status'  => 1,
					'mid'     => $this->meetingID,
					'keyword' => $keyword,
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					//				'_order'  => I('get._column', 'main.creatime').' '.I('get._sort', 'desc'),
				]);
				$join_client = $receivables_logic->setData('manage:set_price', $join_client, [
					'mid'         => $this->meetingID,
					'receivables' => false
				]);
			}
			$statistics = [
				'receivables'     => count($receivables_logic->setData('manage:set_price', $join_total, [
					'mid'         => $this->meetingID,
					'receivables' => 1
				])),
				'not_receivables' => count($receivables_logic->setData('manage:set_price', $join_total, [
					'mid'         => $this->meetingID,
					'receivables' => 0
				])),
				'total'           => $total
			];
			$page_show  = $page_object->show();
			$this->assign('list', $join_client);
			$this->assign('statistics', $statistics);
			$this->assign('page_show', $page_show);//分页
			$this->display();
		}

		public function payMethod(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\PayMethodModel $pay_method_model */
			$pay_method_model  = D('Core/PayMethod');
			$pay_method_result = $pay_method_model->findRecord(2, [
				'status'  => 'not deleted',
				'keyword' => I('get.keyword', '')
			]);
			$this->assign('pay', $pay_method_result);
			$this->display();
		}

		public function receivablesType(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
			$receivables_type_model  = D('Core/ReceivablesType');
			$receivables_type_result = $receivables_type_model->findRecord(2, [
				'status'  => 'not deleted',
				'keyword' => I('get.keyword')
			]);
			$this->assign('type_info', $receivables_type_result);
			$this->display();
		}

		public function posMachine(){
			$pos_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $pos_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\PosMachineModel $pos_machine_model */
			$pos_machine_model  = D('Core/PosMachine');
			$pos_machine_result = $pos_machine_model->findRecord(2, [
				'status'  => 'not deleted',
				'mid'     => I('get.mid', 0, 'int'),
				'keyword' => I('get.keyword', '')
			]);
			$this->assign('pos', $pos_machine_result);
			$this->display();
		}

		public function exportReceivablesData(){
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model = D('Core/Receivables');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model      = D('Core/Meeting');
			$excel_logic        = new ExcelLogic();
			$logic              = new ReceivablesLogic();
			$receivables_result = $receivables_model->findRecord(2, [
				'mid'    => $this->meetingID,
				'status' => 'not deleted'
			]);
			$receivables_result = $logic->setData('excel_data', $receivables_result);
			$meeting            = $meeting_model->findMeeting(1, ['id' => $this->meetingID]);
			$excel_logic->exportCustomData($receivables_result, [
				'fileName'    => "[$meeting[name]]收款信息",
				'title'       => "$meeting[name]",
				'subject'     => '收款列表',
				'description' => '吉美会议系统导出收款数据',
				'company'     => '吉美集团',
				'hasHead'     => true
			]);
		}

		public function manageList(){
			/** @var \Mobile\Model\ReceivablesModel $receivables_model */
			$receivables_model  = D('Mobile/Receivables');
			$receivables_single = $receivables_model->getClientReceivablesAll($this->meetingID);
			$receivables_logic  = new ReceivablesLogic();
			$keyword            = I('get.keyword', '');
			$receivables_result = $receivables_logic->getClientReceivables($receivables_single, $keyword);
			$this->assign('info', $receivables_result);
			$this->display();
		}

		public function exportReceivablesDataTemplate(){
			if($this->permissionList['CLIENT.DOWNLOAD-IMPORT-EXCEL-TEMPLATE']){ // todo
				/** @var \Manager\Model\ReceivablesModel $receivables_model */
				$receivables_model = D('Receivables');
				$header            = $receivables_model->getColumn(true, true);
				$excel_logic       = new ExcelLogic();
				$excel_logic->exportCustomData($header, [
					'fileName'    => '导入收款数据模板',
					'title'       => '导入收款数据模板',
					'subject'     => '导入收款数据模板',
					'description' => '吉美会议系统导入收款数据模板',
					'company'     => '吉美集团',
					'hasHead'     => true
				]);
			}
			else $this->error('您没有下载导入模板的权限');
		}
	}