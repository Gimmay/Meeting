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
	use Manager\Logic\JoinLogic;

	class ClientController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			if(IS_POST){
				$logic  = new ClientLogic();
				$type   = strtolower(I('post.type', ''));
				$result = $logic->handlerRequest($type);
				if($result === -1){
				}
				else{
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$this->display();
		}

		public function create(){
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

		public function importClientDataTest(){
			if(IS_POST){
				$logic  = new ExcelLogic();
				$result = $logic->importClientData($_FILES);
				echo json_encode($result);
				exit;
			}
			echo "<form action='' method='post' enctype='multipart/form-data'>
	<input type='file' name='excel'>
	<button type='submit'>提交</button>
</form>";
		}

		public function createClientTest(){
			$logic            = new ClientLogic();
			$upload_record_id = 5;
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

		public function qrcodeTest(){
			$logic       = new JoinLogic();
			$client_list = [
				['id' => 21],
				['id' => 22]
			];
			$data        = [
				'mid'               => 34,
				'registration_time' => time()-34534,
				'invitor_id'        => 1
			];
			$result      = $logic->makeQRCode($client_list, $data);
			print_r($result);
			exit;
		}
	}