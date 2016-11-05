<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-1
	 * Time: 11:12
	 */
	namespace Mobile\Logic;
	use Manager\Logic\JoinLogic;
	use Core\Logic\MeetingRoleLogic;
	use Core\Logic\PermissionLogic;

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
		 *
		 * @return array
		 */
		public function getEmployeeInformation($eid){
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$record         = $employee_model->findEmployee(1, ['id' => $eid]);
			if(!$record) return [];
			$weixin_record    = $weixin_model->findRecord(1, ['oid' => $eid, 'otype' => 0]);
			$record['avatar'] = $weixin_record['avatar'];

			return $record;
		}

		public function handlerRequest($type, $option = []){
			switch($type){
				case 'client:sign':
					//return array
				break;
				case 'client:review':
				break;
				case 'examine':
					C('TOKEN_ON', false);
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$client_id = I('get.cid', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					$join_result = $join_model->alterRecord([
						'mid' =>$meeting_id,
						'cid' => $client_id
					],['review_status'=>1]);
					if(!$join_result['status']) return array_merge($join_result, ['__ajax__' => true]);
					$join_logic = new JoinLogic();
					$result2    = $join_logic->makeQRCode([$client_id], ['mid' => $meeting_id]);
					return $result2;
				break;
				case 'sign':
					C('TOKEN_ON', false);
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$join_result = $join_model->alterRecord([
						'mid' => I('get.mid', 0, 'int'),
						'cid' => I('get.cid', 0, 'int')
					], ['sign_status' => 1]);

					return $join_result;
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setData($type, $data){
			switch($type){
				case 'meetingList:set_meeting_list':
					$meeting_role_logic = new MeetingRoleLogic();
					$permission_logic   = new PermissionLogic();
					$meetingViewList    = $meeting_role_logic->getMeetingView();
					$new_list           = [];
					$i                  = 0;
					$condition_2        = $permission_logic->hasPermission('MEETING.VIEW-ALL', I('session.MOBILE_EMPLOYEE_ID', 0, 'int'), 1);
					foreach($data as $key => $val){
						$condition_1 = in_array($val['id'], $meetingViewList);
						if($condition_1 || $condition_2){
							$new_list[] = $val;
							$i++;
						}
						else continue;
					}

					return $new_list;
				break;
				case 'meeting:set_extend_column':
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model   = D('Core/Employee');
					$director         = $employee_model->findEmployee(1, [
						'id'     => $data['director_id'],
						'status' => 'not deleted'
					]);
					$data['director'] = $director['name'];

					return $data;
				break;
				case 'meeting:set_statistics_data':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model     = D('Core/Join');
					$mid            = I('get.mid', 0, 'int');
					$sign_count     = $join_model->findRecord(0, [
						'mid'         => $mid,
						'sign_status' => 1,
						'status'      => 'not deleted'
					]);
					$not_sign_count = $join_model->findRecord(0, [
						'mid'         => $mid,
						'sign_status' => 'not signed',
						'status'      => 'not deleted'
					]);
					$total          = $join_model->findRecord(0, ['mid' => $mid, 'status' => 'not deleted']);

					return ['total' => $total, 'signCount' => $sign_count, 'notSignCount' => $not_sign_count];
				break;
				default:
					return $data;
				break;
			}
		}

		public function findData($type, $opt = []){
			switch($type){
				case 'clientList:find_client_list':
					/** @var \Core\Model\JoinModel $join_model */
					$join_model    = D('Core/Join');
					$option_fitler = [];
					if(isset($_GET['sign'])){
						$sign_status = I('get.sign', 0, 'int');
						if($sign_status == 1) $option_fitler['sign_status'] = 1;
						elseif($sign_status == 0) $option_fitler['sign_status'] = 'not signed';
					}

					return $join_model->findRecord(2, array_merge([
						'mid'     => $opt['mid'],
						'status'  => 'not deleted',
						'keyword' => I('get.keyword', ''),
					], $option_fitler));
				break;
				case '':
				break;
			}
		}
	}