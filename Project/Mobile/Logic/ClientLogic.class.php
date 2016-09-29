<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:35
	 */
	namespace Mobile\Logic;

	class ClientLogic extends MobileLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function getInformation($id){
			/** @var \Core\Model\ClientModel $model */
			$model = D('Core/Client');
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model                = D('Core/Meeting');
			$record                       = $model->listClient(1, ['id' => $id]);
			$weixin_record                = $weixin_model->findRecord(1, ['oid' => $id, 'otype' => 1]);
			$meeting_record               = $meeting_model->findMeeting(1, ['id' => $record['mid']]);
			$record['avatar']             = $weixin_record['avatar'];
			$record['meeting_name']       = $meeting_record['name'];
			$record['meeting_host']       = $meeting_record['host'];
			$record['meeting_plan']       = $meeting_record['plan'];
			$record['meeting_place']      = $meeting_record['place'];
			$record['meeting_start_time'] = $meeting_record['start_time'];
			$record['meeting_end_time']   = $meeting_record['end_time'];
			$record['meeting_brief']      = $meeting_record['brief'];

			return $record;
		}

		public function handlerRequest($type){
			switch($type){
				case 'sign':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model = D('Core/Join');
					/** @var \Core\Model\ClientModel $model */
					$model       = D('Core/Client');
					$id          = I('get.id', 0, 'int');
					$mid         = I('get.mid', 0, 'int');
					$join_record = $join_model->findRecord(1, [
						'cid' => $id,
						'mid' => $mid
					]);
					$record      = $model->findClient(1, ['id' => $id]);
					if($record['audit_status']){
						C('TOKEN_ON', false);
						$result = $join_model->alterRecord($join_record['id'], [
							'sign_time'   => time(),
							'sign_status' => 1,
							//						'sign_place_id'=>1,
							//						'sign_director_id'=>1,
						]);
						echo json_encode($result);
					}
					else{
						echo json_encode(['status' => false, 'message' => '此客户信息还没有被审核']);
					}

					return -1;
				break;
				case 'anti_sign':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$id          = I('get.id', 0, 'int');
					$mid         = I('get.mid', 0, 'int');
					$join_record = $join_model->findRecord(1, [
						'cid' => $id,
						'mid' => $mid
					]);
					C('TOKEN_ON', false);
					$result = $join_model->alterRecord($join_record['id'], [
						'sign_status' => 0
					]);
					echo json_encode($result);

					return -1;
				break;
				case 'check_sign':
				/** @var \Core\Model\JoinModel $join_model */
				$join_model  = D('Core/Join');
				$id          = I('get.id', 0, 'int');
				$mid         = I('get.mid', 0, 'int');
				$join_record = $join_model->findRecord(1, [
					'cid' => $id,
					'mid' => $mid
				]);
				echo json_encode($join_record['sign_status']);

				return -1;
			break;
				default:
					echo json_encode(['status' => false, 'message' => '参数错误']);

					return -1;
				break;
			}
		}
	}