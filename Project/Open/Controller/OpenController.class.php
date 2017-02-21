<?php
	namespace Open\Controller;

	use Core\Controller\CoreController;

	class OpenController extends CoreController{
		public function _initialize(){
			parent::_initialize();
		}

		const DEV_ID = [
			'770369fb856fbe33ce1950bb997add2c'
		];

		const HOST = 'https://meeting.royalwiss.cn';

		private function isOutDeveloper($dev_id){
			return in_array($dev_id, self::DEV_ID);
		}

		public function getClientInfo(){
			$dev_id = I('get.dev_id', '');
			if($this->isOutDeveloper($dev_id)){
				$client_name   = I('post.clientName', null);
				$client_mobile = I('post.clientMobile', null);
				$mid           = I('post.meetingID', 0, '');
				if($mid == 0){
					echo json_encode([
						'status'  => false,
						'message' => '缺少会议参数',
						'code'    => 40001
					], JSON_UNESCAPED_UNICODE);
					exit;
				}
				if($client_mobile === null){
					echo json_encode([
						'status'  => false,
						'message' => '缺少手机参数',
						'code'    => 40002
					], JSON_UNESCAPED_UNICODE);
					exit;
				}
				if($client_name === null){
					echo json_encode([
						'status'  => false,
						'message' => '缺少姓名参数',
						'code'    => 40003
					], JSON_UNESCAPED_UNICODE);
					exit;
				}
				/** @var \Core\Model\ClientModel $client_model */
				$client_model = D('Core/Client');
				$client_list  = $client_model->findClientAll(2, ['name' => $client_name, 'mobile' => $client_mobile]);
				if(!$client_list){
					echo json_encode([
						'status'  => false,
						'message' => '找不到该用户信息',
						'code'    => 40004
					], JSON_UNESCAPED_UNICODE);
					exit;
				}
				/** @var \Core\Model\JoinModel $join_model */
				$join_model = D('Core/Join');
				$found      = false;
				$join_info  = [];
				foreach($client_list as $client){
					$join_info = $join_model->findRecordAll(1, ['cid' => $client['id'], 'mid' => $mid]);
					if($join_info){
						$found = true;
						break;
					}
				}
				if(!$found){
					echo json_encode([
						'status'  => false,
						'message' => '该用户未参会',
						'code'    => 40005
					], JSON_UNESCAPED_UNICODE);
					exit;
				}
				$host = self::HOST;
				echo json_encode([
					'status'  => true,
					'message' => '获取成功',
					'code'    => 0,
					'data'    => [
						'sign_code_qrcode' => "$host$join_info[sign_code_qrcode]",
						'sign_qrcode'      => "$host$join_info[sign_qrcode]"
					]
				], JSON_UNESCAPED_UNICODE);
			}
			else{
				echo json_encode(['status' => false, 'message' => '非法的开发者ID', 'code' => 10001], JSON_UNESCAPED_UNICODE);
			}
		}
	}