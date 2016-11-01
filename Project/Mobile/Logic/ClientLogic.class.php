<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:35
	 */
	namespace Mobile\Logic;

	use Core\Logic\WxCorpLogic;
	use Manager\Logic\MessageLogic;

	class ClientLogic extends MobileLogic{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 获取访问者的微信ID
		 *
		 * @param int|string $weixin_id 微信ID
		 * @param int        $wtype     微信ID类型 0：公众号OPENID 1：企业号USERID
		 *
		 * @return int|null
		 */
		public function getVisitorID($weixin_id, $wtype = 1){
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			$visitor      = $weixin_model->findRecord(1, [
				'weixin_id' => $weixin_id,
				'wtype'     => $wtype,
				'otype'     => 1
			]);
			if($visitor) return $visitor['oid'];

			else return 0;
		}

		/**
		 * @param int $cid 用户ID
		 * @param int $mid 会议ID
		 *
		 * @return array
		 */
		public function getClientInformation($cid, $mid){
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			$record     = $join_model->findRecord(1, ['cid' => $cid, 'mid' => $mid]);
			if(!$record) return [];
			$weixin_record                = $weixin_model->findRecord(1, ['oid' => $cid, 'otype' => 1]);
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

		public function getMeeting($client_id){
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model  = D('Core/Meeting');
			$join_record    = $join_model->findRecord(2, ['cid' => $client_id]);
			$meeting_arr    = [];
			$meeting_list   = ['fin' => [], 'ing' => []];
			$meeting_record = $meeting_model->findMeeting(2, ['status' => 'not deleted']);
			foreach($join_record as $val) if(!in_array($val['mid'], $meeting_arr)) $meeting_arr[] = $val['mid'];
			foreach($meeting_record as $val) if(!in_array($val['id'], $meeting_arr)){
				if($val['status'] == 4) $meeting_list['fin'][] = $val;
				if($val['status'] == 3) $meeting_list['ing'][] = $val;
			}

			return $meeting_list;
		}

		public function handlerRequest($type, $option = []){
			switch($type){
				case 'manage:sign':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$id          = I('get.cid', 0, 'int');
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
							//'sign_place_id'    => $option['eid'],
							'sign_director_id' => $option['eid'],
							'sign_type'        => 2
						]);
						if($result['status']){
							$message_logic = new MessageLogic();
							$message_logic->send($meeting_id, 1, [$id]);
						}

						return array_merge($result, ['__ajax__' => true]);
					}

					return ['status' => false, 'message' => '此客户信息还没有被审核', '__ajax__' => true];
				break;
				case 'manage:anti_sign':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$id          = I('get.cid', 0, 'int');
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

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'myCenter:sign':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$id          = $option['cid'];
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
							'sign_director_id' => $option['cid'],
							'sign_type'        => 2
						]);
						if($result['status']){
							$message_logic = new MessageLogic();
							$message_logic->send($meeting_id, 1, [$id]);
						}

						return array_merge($result, ['__ajax__' => true]);
					}

					return ['status' => false, 'message' => '此客户信息还没有被审核', '__ajax__' => true];
				break;
				case 'check_sign':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$id          = $option['cid'];
					$mid         = I('get.mid', 0, 'int');
					$join_record = $join_model->findRecord(1, [
						'cid' => $id,
						'mid' => $mid
					]);

					return ['__ajax__' => true, 'data' => $join_record['sign_status']];
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setDate($type, $data){
			switch($type){
				case 'myMeeting:set_meeting_column':
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model   = D('Core/Employee');
					$director         = $employee_model->findEmployee(1, [
						'id'     => $data['director_id'],
						'status' => 'not deleted'
					]);
					$data['director'] = $director['name'];

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}

	}