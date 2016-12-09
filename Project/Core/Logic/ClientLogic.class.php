<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-12-7
	 * Time: 15:02
	 */
	namespace Core\Logic;

	use Manager\Logic\MessageLogic;

	class ClientLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function sign($option){
			/** @var \Core\Model\JoinModel $join_model */
			$join_model  = D('Core/Join');
			$join_record = $join_model->findRecord(1, [
				'cid'    => $option['cid'],
				'mid'    => $option['mid'],
				'status' => 1
			]);
			if($join_record && $join_record['review_status'] == 1){
				C('TOKEN_ON', false);
				$result = $join_model->alterRecord(['id' => $join_record['id']], [
					'sign_status'      => 1,
					'sign_time'        => time(),
					'sign_director_id' => $option['eid'],
					'sign_type'        => $option['type']
				]);
				if($result['status']){
					$message_logic = new MessageLogic();
					$message_logic->send($option['mid'], 1, 1, [$option['cid']]);
				}

				return array_merge($result, [
					'data' => [
						'sign_status'      => 1,
						'sign_time'        => time(),
						'sign_director_id' => $option['eid'],
						'sign_type'        => $option['type']
					]
				]);
			}
			else return [
				'status'  => false,
				'message' => '此客户信息还没有被审核',
			];
		}
	}