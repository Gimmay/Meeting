<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-1
	 * Time: 11:11
	 */
	namespace Mobile\Controller;

	use Core\Logic\MeetingLogic;
	use Core\Logic\PermissionLogic;
	use Core\Logic\WxCorpLogic;
	use Mobile\Logic\ClientLogic;
	use Mobile\Logic\ManagerLogic;

	class ManagerController extends MobileController{
		protected $wechatID   = 0;
		protected $employeeID = 0;
		protected $meetingID  = 0;

		public function _initialize(){
//			$_SESSION['MOBILE_WECHAT_ID']   = 1090;
//			$_SESSION['MOBILE_EMPLOYEE_ID'] = 2;
			parent::_initialize();
			$meeting_logic = new MeetingLogic();
			$meeting_logic->initializeStatus();
		}

		private function _getMeetingParam($redirect = true){
			if(!isset($_GET['mid'])){
				if($redirect) $this->redirect('Error/requireMeeting');

				return false;
			}
			else{
				$this->meetingID = I('get.mid', 0, 'int');

				return true;
			}
		}

		private function _getEmployeeID($redirect = true){
			$get              = function (){
				$logic = new ManagerLogic();

				return $logic->getVisitorID($this->wechatID);
			};
			$this->employeeID = isset($_SESSION['MOBILE_EMPLOYEE_ID']) ? I('session.MOBILE_EMPLOYEE_ID', 0, 'int') : $get();
			if($this->employeeID == 0){
				if($redirect) $this->redirect('Error/isNotRegister');

				return false;
			}
			else return true;
		}

		public function addClient(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$logic = new ManagerLogic();
			if(IS_POST){
				$type   = 'addClient:create';
				$result = $logic->handlerRequest($type, ['employeeID' => $this->employeeID]);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], U('clientList', ['mid' => I('get.mid', 0, 'int')]));
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.CLIENT.CREATE', $this->employeeID)){
				$getColumnStatus = function ($mid){
					/** @var \Core\Model\ColumnControlModel $column_control_model */
					$column_control_model = D('Core/ColumnControl');
					$column_list          = $column_control_model->findRecord(2, [
						'mid'   => $mid,
						'table' => 'user_client'
					]);
					$data                 = [];
					foreach($column_list as $val) $data[$val['code']] = $val;

					return $data;
				};
				$this->assign('column_status', $getColumnStatus($this->meetingID));
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.CLIENT.CREATE']);
		}

		public function client(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$logic = new ManagerLogic();
			if(IS_POST){
				$type   = (I('post.requestType', ''));
				$result = $logic->handlerRequest($type, ['eid' => $this->employeeID]);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.CLIENT.VIEW', $this->employeeID)){
				$client_logic = new ClientLogic();
				$cid          = I('get.cid', 0, 'int');
				$mid          = I('get.mid', 0, 'int');
				/** @var \Core\Model\JoinModel $join_model */
				$join_model = D('Core/Join');
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				$join_result   = $join_model->findRecord(1, ['cid' => $cid, 'mid' => $mid]);
				$info          = $client_logic->getClientInformation($cid);
				$meeting       = $meeting_model->findMeeting(1, ['id' => $mid]);
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->assign('list', $join_result);
				$this->assign('info', $info);
				$this->assign('meeting', $meeting);
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.CLIENT.VIEW']);
		}

		public function clientList(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$logic            = new ManagerLogic();
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.CLIENT.VIEW', $this->employeeID)){
				$client_list = $logic->findData('clientList:find_client_list', ['mid' => I('get.mid', 0, 'int')]);
				if(!isset($_GET['sign'])) $title = '所有参会人员';
				elseif(I('get.sign', 0, 'int') === 0) $title = '未签到人员';
				elseif(I('get.sign', 0, 'int') === 1) $title = '已签到人员';
				else $title = '参会人员';
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->assign('title', $title);
				$this->assign('client_list', $client_list);
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.CLIENT.VIEW']);
		}

		public function meetingList(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.MEETING.VIEW', $this->employeeID)){
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				$logic         = new ManagerLogic();
				$meeting_list  = $meeting_model->findMeeting(2, ['status' => 'not deleted']);
				$meeting_list  = $logic->setData('meetingList:set_meeting_list', $meeting_list);
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->assign('meeting_list', $meeting_list);
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.MEETING.VIEW']);
		}

		public function meeting(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.MEETING.VIEW', $this->employeeID)){
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				$logic         = new ManagerLogic();
				$meeting_info  = $meeting_model->findMeeting(1, [
					'status' => 'not deleted',
					'id'     => I('get.mid', 0, 'int')
				]);
				$meeting_info  = $logic->setData('meeting:set_extend_column', $meeting_info);
				$statistics    = $logic->setData('meeting:set_statistics_data', $meeting_info);
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->assign('meeting', $meeting_info);
				$this->assign('statistics', $statistics);
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.MEETING.VIEW']);
		}

		public function myCenter(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$logic = new ManagerLogic();
			$info  = $logic->getEmployeeInformation($this->employeeID);
			$this->assign('info', $info);
			$this->display();
		}

		public function report1(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.REPORT-1', $this->employeeID)){
				$statistics = [
					'newClient' => 0,
					'oldClient' => 0,
					'signed'    => 0,
					'notSigned' => 0,
					'total'     => 0,
					'unit'      => 0,
					'price'     => 0
				];
				/** @var \Core\Model\JoinModel $join_model */
				$join_model = D('Core/Join');
				/** @var \Core\Model\ReceivablesModel $receivables_model */
				$receivables_model = D('Core/Receivables');
				/** @var \Core\Model\ReceivablesOptionModel $receivables_option_model */
				$receivables_option_model = D('Core/ReceivablesOption');
				/** @var \Core\Model\CouponModel $coupon_model */
				$coupon_model = D('Core/Coupon');
				/** @var \Core\Model\CouponItemModel $coupon_item_model */
				$coupon_item_model = D('Core/CouponItem');
				// 总数 会所
				$total_list          = $join_model->findRecord(2, [
					'mid'    => $this->meetingID,
					'status' => 1,
					'type'   => 'not employee'
				]);
				$statistics['total'] = count($total_list);
				$unit_arr            = [];
				foreach($total_list as $val){
					if(in_array($val['unit'], $unit_arr)) continue;
					$unit_arr[] = $val['unit'];
				}
				$statistics['unit'] = count($unit_arr);
				// 新客老客数
				$statistics['newClient'] = $join_model->findRecord(0, [
					'mid'    => $this->meetingID,
					'isNew'  => 1,
					'status' => 1
				]);
				$statistics['oldClient'] = $join_model->findRecord(0, [
					'mid'    => $this->meetingID,
					'isNew'  => 0,
					'status' => 1
				]);
				// 签到 未签到人数
				$statistics['signedPT']    = $join_model->findRecord(0, [
					'mid'         => $this->meetingID,
					'sign_status' => 1,
					'status'      => 1,
					'type'        => '陪同'
				]);
				$statistics['notSignedPT'] = $join_model->findRecord(0, [
					'mid'         => $this->meetingID,
					'sign_status' => 'not signed',
					'status'      => 1,
					'type'        => '陪同'
				]);
				$statistics['signedZD']    = $join_model->findRecord(0, [
					'mid'         => $this->meetingID,
					'sign_status' => 1,
					'status'      => 1,
					'type'        => '终端'
				]);
				$statistics['notSignedZD'] = $join_model->findRecord(0, [
					'mid'         => $this->meetingID,
					'sign_status' => 'not signed',
					'status'      => 1,
					'type'        => '终端'
				]);
				// 现场收款
				$receivables_list_at_meeting = $receivables_model->findRecord(2, [
					'mid'         => $this->meetingID,
					'status'      => 1,
					'source_type' => 2
				]);
				foreach($receivables_list_at_meeting as $receivables){
					$receivables_option = $receivables_option_model->findRecord(2, [
						'rid'    => $receivables['id'],
						'status' => 1
					]);
					foreach($receivables_option as $option) $statistics['price'] += $option['price'];
				}
				// 项目（代金券）
				//$coupon_list = $coupon_model->findCoupon(2, ['mid' => $this->meetingID, 'status' => 1]);
				/** @var \Mobile\Model\ManagerModel $my_model */
				$my_model    = D('Manager');
				$coupon_list = $my_model->getReceivablesPrice($this->meetingID);
				foreach($coupon_list as $key => $coupon){
					$coupon_item_list                = $coupon_item_model->findRecord(2, [
						'coupon_id' => $coupon['id'],
						'mid'       => $this->meetingID
					]);
					$sold_coupon_item_list           = $coupon_item_model->findRecord(2, [
						'coupon_id' => $coupon['id'],
						'mid'       => $this->meetingID,
						'status'    => 1
					]);
					$coupon_list[$key]['count']      = count($coupon_item_list);
					$coupon_list[$key]['sold_count'] = count($sold_coupon_item_list);
				}
				$this->assign('coupon_list', $coupon_list);
				$this->assign('statistics', $statistics);
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.REPORT-1']);
		}

		public function report1Client(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$logic            = new ManagerLogic();
			$permission_logic = new PermissionLogic();
			$client_list      = $logic->findData('clientList:find_client_list', ['mid' => I('get.mid', 0, 'int')]);
			if(!isset($_GET['sign'])) $title = '所有参会人员';
			elseif(I('get.sign', 0, 'int') === 0) $title = '未签到人员';
			elseif(I('get.sign', 0, 'int') === 1) $title = '已签到人员';
			else $title = '参会人员';
			$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
			$this->assign('title', $title);
			$this->assign('client_list', $client_list);
			$this->display();
		}

		public function report1Unit(){
			/** @var \Mobile\Model\ManagerModel $model */
			$model = D('Manager');
			$list  = $model->getUnitList(I('get.mid', 0, 'int'));
			$this->assign('list', $list);
			$this->display();
		}
	}