<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-1
	 * Time: 11:12
	 */
	namespace Mobile\Logic;

	class ManagerLogic extends MobileLogic{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 获取访问者的微信ID
		 *
		 * @param int|string $weixin_id 微信ID
		 * @param int        $wtype     微信ID类型 0：公众号OPENID 1：企业号USERID
		 *
		 * @return int|[]|null
		 */
		public function getVisitorID($weixin_id, $wtype = 1){
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			$visitor      = $weixin_model->findRecord(1, [
				'weixin_id' => $weixin_id,
				'wtype'     => $wtype,
				'otype'     => 0
			]);
			if($visitor['oid']){
				session('MOBILE_EMPLOYEE_ID', $visitor['oid']);

				return $visitor['oid'];
			}
			else return 0;
		}

		/**
		 * @param int $eid 用户ID
		 * @param int $mid 会议ID
		 *
		 * @return array
		 */
		public function getEmployeeInformation($eid, $mid){
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$record         = $employee_model->findEmployee(1, ['id' => $eid]);
			if(!$record) return [];
			$weixin_record                = $weixin_model->findRecord(1, ['oid' => $eid, 'otype' => 0]);
			$meeting_record               = $meeting_model->findMeeting(1, ['id' => $mid]);
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

		public function handlerRequest($type, $option = []){
			switch($type){
				case 'manage:sign':

					//return array
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}
	}