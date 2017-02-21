<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-12-7
	 * Time: 15:02
	 */
	namespace Core\Logic;

	use Exception;
	use Manager\Logic\MessageLogic;

	class ClientLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function sign($option){
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			$meeting    = $meeting_model->findMeeting(1, ['id' => $option['mid']]);
			if(time()>=strtotime($meeting['sign_start_time']) && time()<=strtotime($meeting['sign_end_time'])){
				$join_record = $join_model->findRecord(1, [
					'cid'    => $option['cid'],
					'mid'    => $option['mid'],
					'status' => 1
				]);
				$cur_time    = time();
				if($join_record && $join_record['review_status'] == 1){
					C('TOKEN_ON', false);
					$result = $join_model->alterRecord(['id' => $join_record['id']], [
						'sign_status'      => 1,
						'sign_time'        => $cur_time,
						'sign_director_id' => $option['eid'],
						'sign_type'        => $option['type']
					]);
					if($result['status']){
						$message_logic = new MessageLogic();
						$message_logic->send($option['mid'], 1, 1, [$option['cid']]);
						/** @var \Core\Model\SignResultModel $sign_result_model */
						$sign_result_model = D('Core/SignResult');
						$signed_count      = $join_model->findRecord(0, [
							'mid'         => $option['mid'],
							'sign_status' => 1,
							'status'      => 1
						]);
						C('TOKEN_ON', false);
						$sign_result_model->createRecord([
							'mid'       => $option['mid'],
							'cid'       => $option['cid'],
							'sign_time' => $cur_time,
							'creatime'  => $cur_time,
							'creator'   => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'),
							'order'     => $signed_count+1
						]);
						// OSUDNFOUIRHGIOR(H*&WEHFWE(FW
						/** @var \Core\Model\ClientModel $client_model */
						$client_model = D('Core/Client');
						$client       = $client_model->findClient(1, ['id' => $option['cid']]);
						if(in_array($client['type'], ['内部员工'])){
							$openid = sha1("$client[name]$client[unit]");
							try{
								M('weixin_flag')->add([
									'openid'      => $openid,
									'fakeid'      => $openid,
									'flag'        => 2,
									'status'      => 1,
									'othid'       => 0,
									'cjstatu'     => 2,
									'a_code'      => '',
									'a_name'      => $client['name'],
									'a_gender'    => $client['gender'],
									'a_mobile'    => $client['mobile'],
									'a_unit'      => $client['unit'],
									'a_position'  => $client['position'],
									'a_avatar'    => '',
									'a_type'      => $client['type'],
									'a_join_time' => '2016-01-01',
									'a_mid'       => $option['mid']
								]);
							}catch(Exception $error){
							}
						}
						/** @var \Core\Model\EmployeeModel $employee_model */
						$employee_model = D('Core/Employee');
						$director       = $employee_model->findEmployee(1, ['id' => $option['eid']]);

						return array_merge($result, [
							'data' => [
								'sign_status'      => 1,
								'sign_time'        => $cur_time,
								'sign_director_id' => $option['eid'],
								'sign_director'    => $director['name'],
								'sign_type'        => $option['type']
							]
						]);
					}

					return $result;
				}
				else return [
					'status'  => false,
					'message' => '此客户信息还没有被审核',
				];
			}
			else return ['status' => false, 'message' => '会议还没开始呢'];
		}
	}