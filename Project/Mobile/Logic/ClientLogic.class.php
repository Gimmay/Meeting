<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:35
	 */
	namespace Mobile\Logic;

	use Core\Logic\PermissionLogic;
	use Core\Logic\WxCorpLogic;

	class ClientLogic extends MobileLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function getInformation($id, $mid){
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model                   = D('Core/Join');
			$record                       = $join_model->findRecord(1, ['cid' => $id, 'mid' => $mid]);
			$weixin_record                = $weixin_model->findRecord(1, ['mobile' => $record['mobile']]);
			$meeting_record               = $meeting_model->findMeeting(1, ['id' => $record['mid']]);
			$record['avatar']             = $weixin_record['avatar'];
			$record['meeting_name']       = $meeting_record['name'];
			$record['meeting_host']       = $meeting_record['host'];
			$record['meeting_plan']       = $meeting_record['plan'];
			$record['meeting_place']      = $meeting_record['place'];
			$record['meeting_start_time'] = $meeting_record['start_time'];
			$record['meeting_end_time']   = $meeting_record['end_time'];
			$record['meeting_brief']      = $meeting_record['brief'];
			$record['meeting_comment']    = $meeting_record['comment'];

			return $record;
		}

		public function handlerRequest($type){
			switch($type){
				case 'sign':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$id          = I('get.id', 0, 'int');
					$meeting_id  = I('get.mid', 0, 'int');
					$join_record = $join_model->findRecord(1, [
						'cid' => $id,
						'mid' => $meeting_id
					]);
					if($join_record['review_status'] == 1){
						C('TOKEN_ON', false);
						$result = $join_model->alterRecord($join_record['id'], [
							'sign_time'        => time(),
							'sign_status'      => 1,
							//'sign_place_id'=>1,
							'sign_director_id' => I('session.MOBILE_EMPLOYEE_ID', 0, 'int'),
							'sign_type'        => 2
						]);
						if($result['status']){
							/** @var \Core\Model\ClientModel $model */
							/** @var \Core\Model\WeixinIDModel $weixin_model */
							/** @var \Core\Model\MeetingModel $meeting_model */
							$model          = D('Core/Client');
							$wxcorp_logic   = new WxCorpLogic();
							$weixin_model   = D('Core/WeixinID');
							$meeting_model  = D('Core/Meeting');
							$meeting_record = $meeting_model->findMeeting(1, ['id' => $meeting_id]);
							$record         = $model->findClient(1, ['id' => $id]);
							$weixin_record  = $weixin_model->findRecord(1, ['mobile' => $record['mobile']]);
							$time           = date('Y-m-d H:i:s');
							$wxcorp_logic->sendMessage('text', "您参加的<$meeting_record[name]>于[$time]成功签到", ['user' => [$weixin_record['weixin_id']]]);
						}
						echo json_encode($result);
					}
					else echo json_encode(['status' => false, 'message' => '此客户信息还没有被审核']);

					return -1;
				break;
				case 'anti_sign':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$id          = I('get.id', 0, 'int');
					$meeting_id  = I('get.mid', 0, 'int');
					$join_record = $join_model->findRecord(1, [
						'cid' => $id,
						'mid' => $meeting_id
					]);
					C('TOKEN_ON', false);
					$result = $join_model->alterRecord($join_record['id'], [
						'sign_status' => 0,
						'sign_time'   => null,
						'sign_type'   => 0
					]);
					if($result['status']){
						/** @var \Core\Model\ClientModel $model */
						/** @var \Core\Model\WeixinIDModel $weixin_model */
						/** @var \Core\Model\MeetingModel $meeting_model */
						$model          = D('Core/Client');
						$wxcorp_logic   = new WxCorpLogic();
						$weixin_model   = D('Core/WeixinID');
						$meeting_model  = D('Core/Meeting');
						$meeting_record = $meeting_model->findMeeting(1, ['id' => $meeting_id]);
						$record         = $model->findClient(1, ['id' => $id]);
						$weixin_record  = $weixin_model->findRecord(1, ['mobile' => $record['mobile']]);
						$time           = date('Y-m-d H:i:s');
						$wxcorp_logic->sendMessage('text', "您参加的<$meeting_record[name]>于[$time]取消签到", ['user' => [$weixin_record['weixin_id']]]);
					}
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

		public function isSigner($user_id){
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model     = D('Core/WeixinID');
			$permission_logic = new PermissionLogic();
			$weixin_record    = $weixin_model->findRecord(1, ['weixinID' => $user_id, 'otype' => 0, 'wtype' => 1]);
			if($weixin_record){
				session('MOBILE_EMPLOYEE_ID', $weixin_record['oid']);

				return $permission_logic->hasPermission('SIGN_CLIENT', $weixin_record['oid']);
			}
			else return false;
		}
	}