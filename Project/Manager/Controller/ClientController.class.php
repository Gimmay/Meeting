<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:54
	 */
	namespace Manager\Controller;

	use Core\Logic\WxCorpLogic;
	use Manager\Logic\ClientLogic;
	use Manager\Logic\ExcelLogic;
	use Manager\Logic\JoinLogic;
	use Think\Page;

	class ClientController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$logic = new ClientLogic();
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
			$mid = I('get.mid', 0, 'int');
			/** @var \Core\Model\ClientModel $core_model */
			$core_model = D('Core/Client');
			$options    = [];
			if(isset($_GET['signed'])) $options['sign_status'] = I('get.signed', 0, 'int') == 1 ? 1 : 0;
			if(isset($_GET['reviewed'])) $options['review_status'] = I('get.reviewed', 0, 'int') == 1 ? 1 : 0;
			$list_total = $core_model->listClient(0, array_merge([
				'keyword' => I('get.keyword', ''),
				'status'  => 'not deleted',
				'mid'     => $mid
			], $options));
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $core_model->listClient(2, array_merge([
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'main.creatime').' '.I('get.sort', 'desc'),
				'status'  => 'not deleted',
				'mid'     => $mid
			], $options));
			$client_list = $logic->setExtendColumnForManage($client_list);
			/* 统计数据 */
			$signed_count   = $core_model->listClient(0, [
				'mid'         => $mid,
				'sign_status' => 1,
				'status'      => 'not deleted'
			]);
			$reviewed_count = $core_model->listClient(0, [
				'mid'           => $mid,
				'review_status' => 1,
				'status'        => 'not deleted'
			]);
			$all_count      = $core_model->listClient(0, [
				'mid'    => $mid,
				'status' => 'not deleted'
			]);
			$this->assign('statistics', [
				'signed'       => $signed_count,
				'not_signed'   => $all_count-$signed_count,
				'reviewed'     => $reviewed_count,
				'not_reviewed' => $all_count-$reviewed_count,
				'total'        => $all_count,
			]);
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
			$logic        = new ExcelLogic();
			$logic->exportCustomData($header, [
				'fileName'    => '导入客户数据模板',
				'title'       => '导入客户数据模板',
				'subject'     => '导入客户数据模板',
				'description' => '吉美会议系统导入客户数据模板',
				'company'     => '吉美集团',
				'hasHead'     => true
			]);
		}

		public function createClientTest(){
			$logic            = new ClientLogic();
			$upload_record_id = 75;
			//$upload_record_id = I('post.id', 0, 'int');
			//$map    = I('post.map', '');
			$map    = [
				'data'   => [15, 13],
				'column' => [20, 21]
			];
			$result = $logic->createClientFromExcel($upload_record_id, $map);
			print_r($result);
			exit;
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