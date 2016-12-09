<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-1
	 * Time: 11:12
	 */
	namespace Mobile\Logic;

	use Core\Logic\ClientLogic;
	use Core\Logic\JoinLogic;
	use Core\Logic\WxCorpLogic;
	use Core\Logic\MeetingRoleLogic;
	use Core\Logic\PermissionLogic;
	use Quasar\StringPlus;

	class ManagerLogic extends MobileLogic{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 获取访问者的微信ID
		 *
		 * @param int|string $wechat_id 微信ID
		 * @param int        $wtype     微信ID类型 0：公众号OPENID 1：企业号USERID
		 *
		 * @return int|[]|null
		 */
		public function getVisitorID($wechat_id, $wtype = 1){
			/** @var \Core\Model\WechatModel $wechat_model */
			$wechat_model = D('Core/Wechat');
			$visitor      = $wechat_model->findRecord(1, [
				'wechat_id' => $wechat_id,
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
			/** @var \Core\Model\WechatModel $wechat_model */
			$wechat_model = D('Core/Wechat');
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			/** @var \Core\Model\DepartmentModel $department_model */
			$department_model = D('Core/Department');
			$record           = $employee_model->findEmployee(1, ['id' => $eid]);
			if(!$record) return [];
			$department           = $department_model->findDepartment(1, ['id' => $record['did']]);
			$record['department'] = $department['name'];
			$wechat_record        = $wechat_model->findRecord(1, ['oid' => $eid, 'otype' => 0]);
			$record['avatar']     = $wechat_record['avatar'];

			return $record;
		}

		public function handlerRequest($type, $option = []){
			$permission_logic = new PermissionLogic();
			$employee_id      = I('session.MOBILE_EMPLOYEE_ID', 0, 'int');
			switch($type){
				case 'auditing'; //审核
					if($permission_logic->hasPermission('WECHAT.CLIENT.REVIEW', $employee_id)){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model = D('Core/Join');
						$client_id  = I('get.cid', 0, 'int');
						$meeting_id = I('get.mid', 0, 'int');
						C('TOKEN_ON', false);
						$join_result = $join_model->alterRecord([
							'mid' => $meeting_id,
							'cid' => $client_id
						], ['review_status' => 1, 'review_time' => time()]);
						if(!$join_result['status']) return array_merge($join_result, ['__ajax__' => true]);
						$join_logic = new JoinLogic();
						$result2    = $join_logic->makeQRCodeForSign([$client_id], ['mid' => $meeting_id]);

						return array_merge($result2, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有微信审核参会人员的权限',
						'__ajax__' => true
					];
				break;
				case 'cancel_auditing'; //取消审核
					if($permission_logic->hasPermission('WECHAT.CLIENT.ANTI-REVIEW', $employee_id)){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model  = D('Core/Join');
						$client_id   = I('get.cid', 0, 'int');
						$meeting_id  = I('get.mid', 0, 'int');
						$join_record = $join_model->findRecord(1, [
							'mid' => $meeting_id,
							'cid' => $client_id
						]);
						C('TOKEN_ON', false);
						$result = $join_model->alterRecord(['id' => $join_record['id']], [
							'review_status' => 2,
							'sign_status'   => 0,
							'sign_time'     => null,
							'sign_type'     => 0
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有微信取消审核参会人员的权限',
						'__ajax__' => true
					];
				break;
				case 'sign': //签到
					if($permission_logic->hasPermission('WECHAT.CLIENT.SIGN', $employee_id)){
						$core_client_logic = new ClientLogic();
						$result = $core_client_logic->sign([
							'mid'  => I('get.mid', 0, 'int'),
							'cid'  => I('get.cid', 0, 'int'),
							'type' => 3,
							'eid'  => $employee_id
						]);
						return array_merge($result, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有微信签到的权限',
						'__ajax__' => true
					];
				break;
				case 'cancel_sign'; //取消签到
					if($permission_logic->hasPermission('WECHAT.CLIENT.ANTI-SIGN', $employee_id)){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model = D('Core/Join');
						C('TOKEN_ON', false);
						$join_result = $join_model->alterRecord([
							'mid' => I('get.mid', 0, 'int'),
							'cid' => I('get.cid', 0, 'int')
						], ['sign_status' => 2, 'sign_type' => 0]);

						return array_merge($join_result, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有微信取消签到的权限',
						'__ajax__' => true
					];
				break;
				case 'addClient:create':
					if($permission_logic->hasPermission('WECHAT.CLIENT.CREATE', $employee_id)){
						$data = I('post.');
						/* 1.创建参会人员 */
						/** @var \Core\Model\ClientModel $model */
						$model        = D('Core/Client');
						$exist_client = $model->findClient(1, ['mobile' => $data['mobile']]);
						if($exist_client){
							$client_id           = $exist_client['id'];
							$str_obj             = new StringPlus();
							$data['status']      = 1;
							$data['is_new']      = 0;
							$data['creatime']    = time();
							$data['creator']     = $option['employeeID'];
							$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
							$model->alterClient(['id' => $client_id], $data);
						}
						else{
							$str_obj             = new StringPlus();
							$data['creatime']    = time();
							$data['creator']     = $option['employeeID'];
							$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
							$data['password']    = $str_obj->makePassword(C('DEFAULT_CLIENT_PASSWORD'), $data['mobile']);
							$result1             = $model->createClient($data);
							if($result1['status']) $client_id = $result1['id'];
							else return $result1;
						}
						/* 2.创建参会记录 */
						$mid = I('get.mid', 0, 'int');
						/** @var \Core\Model\JoinModel $join_model */
						$join_model  = D('Core/Join');
						$join_record = $join_model->findRecord(1, [
							'mid' => $mid,
							'cid' => $client_id
						]);
						$mobile      = $data['mobile'];
						if($join_record){
							if($join_record['join_status'] != 1){
								C('TOKEN_ON', false);
								$join_result = $join_model->alterRecord(['id' => $join_record['id']], ['status' => 1]);
								if($join_result['status']) $result = [
									'status'  => true,
									'message' => '创建成功'
								];
								else $result = $join_result;
							}
							else $result = [
								'status'  => true,
								'message' => '创建成功'
							];
						}
						else{
							$data             = [];
							$data['cid']      = $client_id;
							$data['mid']      = $mid;
							$data['creatime'] = time();
							$data['creator']  = $option['employeeID'];
							C('TOKEN_ON', false);
							$result = $join_model->createRecord($data);
						}
						/* 3.试图根据手机号创建微信用户记录 */
						$wechat_logic     = new WxCorpLogic();
						$wechat_user_list = $wechat_logic->getAllUserList();
						foreach($wechat_user_list as $val){
							if($val['mobile'] == $mobile && $val['status'] != 4){
								/** @var \Core\Model\WechatModel $wechat_model */
								$wechat_model = D('Core/Wechat');
								$department   = '';
								foreach($val['department'] as $val2) $department .= $val2.',';
								$department         = trim($department, ',');
								$data               = [];
								$data['otype']      = 1;// 对象类型 这里为客户(参会人员)
								$data['oid']        = $client_id; // 对象ID
								$data['wtype']      = 1; // 微信ID类型 企业号
								$data['wid']  = $val['userid']; // 微信ID
								$data['department'] = $department; // 部门ID
								$data['mobile']     = $val['mobile']; // 手机号码
								$data['avatar']     = $val['avatar']; // 头像地址
								$data['gender']     = $val['gender']; // 性别
								$data['is_follow']  = $val['status'];    //是否关注
								$data['nickname']   = $val['name']; // 昵称
								$data['creatime']   = time(); // 创建时间
								$data['creator']    = $option['employeeID']; // 当前创建者
								C('TOKEN_ON', false);
								$wechat_model->createRecord($data); // 插入数据
								break;
							}
						}

						return array_merge($result, ['__ajax__' => true]);
					}
					else return [
						'status'   => false,
						'message'  => '您没有微信创建参会人员的权限',
						'__ajax__' => true
					];
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
					$meeting_view_list  = $meeting_role_logic->getMeetingView(I('session.MOBILE_EMPLOYEE_ID', 0, 'int'));
					$new_list           = [
						'ing' => [],
						'fin' => []
					];
					$i                  = 0;
					$condition_2        = $permission_logic->hasPermission('WECHAT.MEETING.VIEW-ALL', I('session.MOBILE_EMPLOYEE_ID', 0, 'int'), 1);
					foreach($data as $key => $val){
						$condition_1 = in_array($val['id'], $meeting_view_list);
						if($condition_1 || $condition_2){
							if($val['status'] == 4){
								$new_list['fin'][] = $val;
								$i++;
							}
							elseif(in_array($val['status'], [1, 2, 3])){
								$new_list['ing'][] = $val;
								$i++;
							}
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
						'status'      => 1
					]);
					$not_sign_count = $join_model->findRecord(0, [
						'mid'         => $mid,
						'sign_status' => 'not signed',
						'status'      => 1
					]);
					$total          = $join_model->findRecord(0, ['mid' => $mid, 'status' => 1]);

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
					$option_filter = [];
					if(isset($_GET['sign'])){
						$sign_status = I('get.sign', 0, 'int');
						if($sign_status == 1) $option_filter['sign_status'] = 1;
						elseif($sign_status == 0) $option_filter['sign_status'] = 'not signed';
					}
					$list     = $join_model->findRecord(2, array_merge([
						'mid'     => $opt['mid'],
						'status'  => 'not deleted',
						'keyword' => I('get.keyword', ''),
					], $option_filter));
					$new_list = [];
					foreach($list as $val){
						$group = $val['unit'];
						if(isset($new_list[$group])) $new_list[$group][] = $val;
						else{
							$new_list[$group]   = [];
							$new_list[$group][] = $val;
						}
					}

					return $new_list;
				break;
				default:
					return [];
				break;
			}
		}
	}