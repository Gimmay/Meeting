<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-12-13
	 * Time: 14:29
	 */
	namespace Open\Logic;

	class ComputerLogic extends OpenLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $option = []){
			switch($type){
				case 'signResult:get_sign_info':
					/** @var \Core\Model\SignResultModel $sign_result_model */
					$sign_result_model = D('Core/SignResult');
					$result            = $sign_result_model->findRecord(1, [
						'mid'       => $option['mid'],
						'status'    => 0,
						'_order'    => 'sign_time'
					]);
					if($result){
						C('TOKEN_ON', false);
						$sign_result_model->alterRecord(['id' => $result['id']], ['status' => 1]);
						/** @var \Core\Model\ClientModel $client_model */
						$client_model = D('Core/Client');
						$client       = $client_model->findClient(1, ['id' => $result['cid'], 'status' => 1]);
						$result       = [
							'signOrder'  => $result['order'],
							'clientName' => $client['name'],
							'unit'       => $client['unit'],
							'found'      => true
						];

						return array_merge($result, ['__ajax__' => true]);
					}
					else return [
						'found'    => false,
						'__ajax__' => true
					];
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数'];
				break;
			}
		}
	}