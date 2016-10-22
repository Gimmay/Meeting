<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:54
	 */
	namespace Manager\Controller;

	use Manager\Logic\ClientLogic;
	use Manager\Logic\ExcelLogic;
	use stdClass;
	use Think\Page;

	class ClientController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
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
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if(!isset($_GET['mid']) && !isset($_GET['sid'])) $this->error('URL参数错误');
			$mid = I('get.mid', 0, 'int');
			/** @var \Manager\Model\SignPlaceModel $sign_place_model */
			$sign_place_model = D('Manager/SignPlace');
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			$coupon_item_model = D('Core/Coupon_item');
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
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
				$options['mid'] = $mid;
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
			$page_object = new Page(count($total_list), C('PAGE_RECORD_COUNT'));
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
			if(isset($_GET['mid'])) $options['mid'] = $mid;
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
				$client_list = $logic->getReceivablesList($client_list, I('get.receivables', 1, 'int'));
				if(I('get.receivables', 1, 'int') == 1){
					$receivables_count     = count($total_list);
					$not_receivables_count = ($all_count-count($total_list));
				}
				if(I('get.receivables', 1, 'int') == 0){
					$not_receivables_count = count($total_list);
					$receivables_count     = ($all_count-count($total_list));
				}
			}
			else{
				$options = [];
				if(isset($_GET['mid'])){
					/** @var \Core\Model\JoinModel $main_model */
					$main_model     = D('Core/Join');
					$options['mid'] = $mid;
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
				$client_list           = $logic->getReceivablesList($total_list, 1, false);
			}
			/* 会议对应的券记录 */
			$coupon_item_result = $coupon_item_model->findCouponItem(2, ['mid' => $mid, 'status' => 0]);
			/* 收款类型列表(for select component) */
			$receivables_type_list = $receivables_model->getReceivablesTypeSelectList();
			/* 员工列表(for select component) */
			$employee_list = $employee_model->getEmployeeSelectList();
			/* 获取签到点列表(for select component) */
			$sign_place_list = $sign_place_model->getRecordSelectList($mid);
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
			$this->assign('employee_list', $employee_list);
			$this->assign('coupon_code_list', $coupon_item_result);
			$this->assign('list', $client_list);
			$this->assign('page_show', $page_show);
			$this->display();
		}

		public function create(){
			if(IS_POST){
				$logic  = new ClientLogic();
				$result = $logic->create(I('post.'));
				if($result['status']) $this->success($result['message'], U('manage', ['mid' => I('get.mid')]));
				else $this->error($result['message']);
				exit;
			}
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
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
			$main_model  = D('Core/Join');
			$excel_logic = new ExcelLogic();
			$logic       = new ClientLogic();
			$options     = [];
			$mid         = I('get.mid', 0, 'int');
			/* 处理URL参数 */
			if(isset($_GET['signed'])) $options['sign_status'] = I('get.signed', 0, 'int') == 1 ? 1 : 'not signed';
			if(isset($_GET['reviewed'])) $options['review_status'] = I('get.reviewed', 0, 'int') == 1 ? 1 : 'not reviewed';
			if(isset($_GET['mid'])) $options['mid'] = $mid;
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
				'creatime'
			]);
			$excel_logic->exportCustomData($client_list, [
				'fileName'    => '参会人员客户列表',
				'title'       => '参会人员客户列表',
				'subject'     => '参会人员客户列表',
				'description' => '吉美会议系统导出客户数据',
				'company'     => '吉美集团',
				'hasHead'     => true
			]);
		}

		public function alter(){
			if(IS_POST){
				$logic  = new ClientLogic();
				$result = $logic->alter(I('get.id', 0, 'int'), I('post.'));
				if($result['status']) $this->success($result['message'], U('manage', ['mid' => I('get.mid')]));
				else $this->error($result['message']);
				exit;
			}
			/** @var \Core\Model\ClientModel $model */
			$model      = D('Core/Client');
			$logic      = new ClientLogic();
			$data['id'] = I('get.id', 0, 'int');
			$info       = $model->findClient(1, $data);
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
			$info           = $logic->setExtendColumnForAlter($info);
			$this->assign('employee_list', $employee_list);
			$this->assign('info', $info);
			$this->display();
		}
	}