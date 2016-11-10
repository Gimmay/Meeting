<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:54
	 */
	namespace Manager\Controller;

	use Core\Logic\SMSLogic;
	use Manager\Logic\ClientLogic;
	use Manager\Logic\ExcelLogic;
	use stdClass;
	use Think\Page;

	class ClientController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		
		public function manage(){
			$logic = new ClientLogic();
			/* 处理POST提交请求 */
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['__return__']) $url = $result['__return__'];
					else $url = '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Manager\Model\SignPlaceModel $sign_place_model */
			$sign_place_model = D('Manager/SignPlace');
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			$coupon_item_model = D('Core/Coupon_item');
			/** @var \Manager\Model\PayMethodModel $pay_method_model */
			$pay_method_model = D('PayMethod');
			/** @var \Manager\Model\ReceivablesTypeModel $receivables_type_model */
			$receivables_type_model = D('ReceivablesType');
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			/** @var \Core\Model\EmployeeModel $employee_personal_model */
			$employee_personal_model = D('Core/Employee');
			/** @var \Manager\Model\ReceivablesModel $receivables_model */
			$receivables_model = D('Receivables');
			$options           = [];
			$sid               = 0;
			$main_model        = new stdClass();
			/* 处理URL参数 */
			if(isset($_GET['signed'])) $options['sign_status'] = I('get.signed', 0, 'int') == 1 ? 1 : 'not signed';
			if(isset($_GET['reviewed'])) $options['review_status'] = I('get.reviewed', 0, 'int') == 1 ? 1 : 'not reviewed';
			if(isset($_GET['mid'])){
				/** @var \Core\Model\JoinModel $main_model */
				$main_model     = D('Core/Join');
				$options['mid'] = $this->meetingID;
			}
			if(isset($_GET['sid'])){
				$sid = I('get.sid', 0, 'int');
				/** @var \Core\Model\JoinSignPlaceModel $main_model */
				$main_model     = D('Core/JoinSignPlace');
				$options['sid'] = $sid;
			}
			/* 获取记录总数 */
			$total_list = $main_model->findRecord(2, array_merge([
				'keyword' => I('get.keyword', ''),
				'status'  => 'not deleted'
			], $options));
			/* 特殊处理收款列表和统计 */
			if(isset($_GET['receivables'])) $total_list = $logic->getReceivablesList($total_list, I('get.receivables', 1, 'int'));
			/* 分页设置 */
			$page_object = new Page(count($total_list), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页记录 */
			$client_list = $main_model->findRecord(2, array_merge([
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get._column', 'main.creatime').' '.I('get._sort', 'desc'),
				'status'  => 'not deleted'
			], $options));
			/* 设定额外字段 */
			$options = [];
			if(isset($_GET['mid'])) $options['mid'] = $this->meetingID;
			if(isset($_GET['sid'])) $options['sid'] = $sid;
			/* 统计数据 */
			$signed_count   = $main_model->findRecord(0, array_merge([
				'sign_status' => 1,
				'status'      => 'not deleted'
			], $options));
			$reviewed_count = $main_model->findRecord(0, array_merge([
				'review_status' => 1,
				'status'        => 'not deleted'
			], $options));
			$all_count      = $main_model->findRecord(0, array_merge([
				'status' => 'not deleted'
			], $options));
			/* 特殊处理收款列表和统计 */
			$receivables_count = $not_receivables_count = 0;
			if(isset($_GET['receivables'])){
				/* 获取记录总数 */
				$temp_total_list  = $main_model->findRecord(2, array_merge([
					'keyword' => I('get.keyword', ''),
					'status'  => 'not deleted'
				], $options));
				$temp_client_list = $logic->getReceivablesList($temp_total_list, I('get.receivables', 1, 'int'));
				if(I('get.receivables') == 1){
					$receivables_count     = count($temp_client_list);
					$not_receivables_count = ($all_count-count($temp_client_list));
				}
				if(I('get.receivables') == 0){
					$not_receivables_count = count($temp_client_list);
					$receivables_count     = ($all_count-count($temp_client_list));
				}
				$client_list = $logic->getReceivablesList($client_list, I('get.receivables', 1, 'int'));
			}
			else{
				$options = [];
				if(isset($_GET['mid'])){
					/** @var \Core\Model\JoinModel $main_model */
					$main_model     = D('Core/Join');
					$options['mid'] = $this->meetingID;
				}
				if(isset($_GET['sid'])){
					$sid = I('get.sid', 0, 'int');
					/** @var \Core\Model\JoinSignPlaceModel $main_model */
					$main_model     = D('Core/JoinSignPlace');
					$options['sid'] = $sid;
				}
				/* 获取记录总数 */
				$temp_total_list       = $main_model->findRecord(2, array_merge([
					'keyword' => I('get.keyword', ''),
					'status'  => 'not deleted'
				], $options));
				$receivables_count     = count($logic->getReceivablesList($temp_total_list, 1));
				$not_receivables_count = count($logic->getReceivablesList($temp_total_list, 0));
				$client_list           = $logic->getReceivablesList($client_list, 1, false);
			}
			//支付类型
			$pay_method_list = $pay_method_model->getPayMethodSelectList();
			//收款类型
			$receivables_type_result = $receivables_type_model->getReceivablesTypeSelectList();
			//当前收款人
			$employee_personal_result = $employee_personal_model->findEmployee(1,['id'=>I('session.MANAGER_EMPLOYEE_ID', 0, 'int')]);

			/* 会议对应的券记录 */
			$coupon_item_result = $coupon_item_model->findCouponItem(2, ['mid' => $this->meetingID, 'status' => 0]);
			/* 收款类型列表(for select component) */
			$receivables_type_list = $receivables_model->getReceivablesTypeSelectList();
			/* 员工列表(for select component) */
			$employee_list = $employee_model->getEmployeeSelectList();
			/* 获取签到点列表(for select component) */
			$sign_place_list = $sign_place_model->getRecordSelectList($this->meetingID);
			/* 向视图输出数据 */
			$this->assign('sign_place_list', $sign_place_list);
			$this->assign('receivables_type_list', $receivables_type_list);
			$this->assign('statistics', [
				'signed'          => $signed_count,
				'not_signed'      => $all_count-$signed_count,
				'reviewed'        => $reviewed_count,
				'not_reviewed'    => $all_count-$reviewed_count,
				'total'           => $all_count,
				'receivables'     => $receivables_count,
				'not_receivables' => $not_receivables_count
			]);
			$this->assign('employee_info',$employee_personal_result);
			$this->assign('pay_list',$pay_method_list);
			$this->assign('receivables_type_list',$receivables_type_result);
			$this->assign('employee_list', $employee_list);
			$this->assign('coupon_code_list', $coupon_item_result);
			$this->assign('list', $client_list);
			$this->assign('page_show', $page_show);
			$this->display();
		}

		public function create(){
			if(IS_POST){
				$logic  = new ClientLogic();
				$result = $logic->handlerRequest('create');
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], U('manage', ['mid' => $this->meetingID]));
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model     = D('Employee');
			$employee_name_list = $employee_model->getEmployeeNameSelectList();
			$employee_list      = $employee_model->getEmployeeSelectList();
			$this->assign('employee_name_list', $employee_name_list);
			$this->assign('employee_list', $employee_list);
			$this->display();
		}

		public function exportClientDataTemplate(){
			/** @var \Manager\Model\ClientModel $client_model */
			$client_model = D('Client');
			$header       = $client_model->getColumn(true);
			$excel_logic  = new ExcelLogic();
			$excel_logic->exportCustomData($header, [
				'fileName'    => '导入客户数据模板',
				'title'       => '导入客户数据模板',
				'subject'     => '导入客户数据模板',
				'description' => '吉美会议系统导入客户数据模板',
				'company'     => '吉美集团',
				'hasHead'     => true
			]);
		}

		public function exportClientData(){
			/** @var \Core\Model\JoinModel $main_model */
			$main_model = D('Core/Join');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$excel_logic   = new ExcelLogic();
			$logic         = new ClientLogic();
			$options       = [];
			$meeting       = $meeting_model->findMeeting(1, ['id' => $this->meetingID]);
			/* 处理URL参数 */
			if(isset($_GET['signed'])) $options['sign_status'] = I('get.signed', 0, 'int') == 1 ? 1 : 'not signed';
			if(isset($_GET['reviewed'])) $options['review_status'] = I('get.reviewed', 0, 'int') == 1 ? 1 : 'not reviewed';
			if(isset($_GET['mid'])) $options['mid'] = $this->meetingID;
			$client_list = $main_model->findRecord(2, array_merge([
				'keyword' => I('get.keyword', ''),
				'_order'  => I('get._column', 'main.creatime').' '.I('get._sort', 'desc'),
				'status'  => 'not deleted'
			], $options));
			$client_list = $logic->alterColumnForExportExcel($client_list, [
				'password',
				'mid',
				'id',
				'pinyin_code',
				'status',
				'creator',
				'sign_qrcode',
				'cid',
				'creatime',
				'join_status'
			]);
			$excel_logic->exportCustomData($client_list, [
				'fileName'    => "[$meeting[name]]参会人员客户列表",
				'title'       => "$meeting[name]",
				'subject'     => '参会人员客户列表',
				'description' => '吉美会议系统导出客户数据',
				'company'     => '吉美集团',
				'hasHead'     => true
			]);
		}

		public function alter(){
			if(IS_POST){
				$logic  = new ClientLogic();
				$result = $logic->handlerRequest('alter');
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], U('manage', ['mid' => $this->meetingID]));
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			/** @var \Core\Model\ClientModel $model */
			$model      = D('Core/Client');
			$logic      = new ClientLogic();
			$data['id'] = I('get.id', 0, 'int');
			$info       = $model->findClient(1, $data);
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model     = D('Employee');
			$info               = $logic->setExtendColumnForAlter($info);
			$employee_name_list = $employee_model->getEmployeeNameSelectList();
			$employee_list      = $employee_model->getEmployeeSelectList();
			$this->assign('employee_name_list', $employee_name_list);
			$this->assign('employee_list', $employee_list);
			$this->assign('info', $info);
			$this->display();
		}
	}