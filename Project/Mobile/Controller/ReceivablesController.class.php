<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-26
	 * Time: 9:56
	 */
	namespace Mobile\Controller;

	use Core\Logic\PermissionLogic;
	use Core\Logic\WxCorpLogic;
	use Mobile\Logic\ManagerLogic;
	use Mobile\Logic\ReceivablesLogic;
	use Quasar\StringPlus;
	use Think\Model;

	class ReceivablesController extends MobileController{
		protected $wechatID   = 0;
		protected $employeeID = 0;
		protected $meetingID  = 0;

		public function _initialize(){
						$_SESSION['MOBILE_EMPLOYEE_ID'] = 2;
						$_SESSION['MOBILE_WECHAT_ID']   = 1090;
			parent::_initialize();
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

		public function addReceivables(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = (I('post.requestType', ''));
				$result = $receivables_logic->handlerRequest($type);
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
			if($permission_logic->hasPermission('WECHAT.CLIENT.RECEIVABLES', $this->employeeID)){
				/** @var \Core\Model\ClientModel $client_model */
				$client_model = D('Core/Client');
				/** @var \Core\Model\PayMethodModel $pay_method_model */
				$pay_method_model = D('Core/PayMethod');
				/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
				$receivables_type_model = D('Core/ReceivablesType');
				/** @var \Core\Model\PosMachineModel $pos_machine_model */
				$pos_machine_model = D('Core/PosMachine');
				/** @var \Core\Model\EmployeeModel $employee_model */
				$employee_model = D('Core/Employee');
				/** @var \Mobile\Model\ClientModel $client_model_type */
				$client_model_type       = D('Client');
				$coupon_item_type        = $receivables_logic->selectCouponItemType();
				$client_result           = $client_model->findClient(1, ['id' => I('get.cid', 0, 'int')]);
				$pay_method_result       = $pay_method_model->findRecord(2, ['status' => 1]);
				$receivables_type_result = $receivables_type_model->findRecord(2, ['status' => 1]);
				$pos_machine_result      = $pos_machine_model->findRecord(2, ['status' => 1]);
				$employee_result         = $employee_model->findEmployee(1, ['id' => $this->employeeID]);
				$mid                     = I('get.mid', 0, 'int');
				$client_result_type      = $client_model_type->getClientSelectList($mid);
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->assign('client_type', $client_result_type);
				$this->assign('employee', $employee_result);
				$this->assign('client', $client_result);
				$this->assign('coupon_item', $coupon_item_type);
				$this->assign('method', $pay_method_result);
				$this->assign('receivables_type', $receivables_type_result);
				$this->assign('pos', $pos_machine_result);
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.CLIENT.RECEIVABLES']);
		}

		public function client(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.CLIENT.VIEW', $this->employeeID)){
				/** @var \Core\Model\JoinModel $join_model */
				$join_model = D('Core/Join');
				/** @var \Core\Model\ReceivablesModel $receivables_model */
				$receivables_model = D('Core/Receivables');
				/** @var \Core\Model\WechatModel $wechat_id_model */
				$wechat_id_model = D('Core/Wechat');
				/** @var \Core\Model\PayMethodModel $pay_method_model */
				$pay_method_model = D('Core/PayMethod');
				/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
				$receivables_type_model = D('Core/ReceivablesType');
				$cid                    = I('get.cid', 0, 'int');
				$mid                    = I('get.mid', 0, 'int');
				$join_result            = $join_model->findRecord(1, ['cid' => $cid, 'mid' => $mid]);
				$wechat_record          = $wechat_id_model->findRecord(1, ['oid' => $cid, 'otype' => 1, 'wtype' => 1]);
				$receivables_result     = $receivables_model->findRecord(2, [
					'cid'      => $cid,
					'mid'      => $mid,
					'payee_id' => I('get.eid', 0, 'int')
				]);
				foreach($receivables_result as $k => $v){
					$pay_method_result                     = $pay_method_model->findRecord(1, [
						'id'     => $v['method'],
						'status' => 1
					]);
					$receivables_type_result               = $receivables_type_model->findRecord(1, [
						'id'     => $v['type'],
						'status' => 1
					]);
					$receivables_result[$k]['method_name'] = $pay_method_result['name'];
					$receivables_result[$k]['type_name']   = $receivables_type_result['name'];
				}
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->assign('info', $join_result);
				$this->assign('list', $receivables_result);
				$this->assign('wechat_info', $wechat_record);
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.CLIENT.VIEW']);
		}

		public function clientList(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.CLIENT.VIEW', $this->employeeID)){
				$receivables_logic = new ReceivablesLogic();
				/** @var \Core\Model\JoinModel $join_model */
				$join_model  = D('Core/Join');
				$join_result = $join_model->findRecord(2, [
					'mid'     => I('get.mid', 0, 'int'),
					'status'  => 'not deleted',
					'keyword' => I('get.keyword', '')
				]);
				if(isset($_GET['receivables'])){
					if(I('get.receivables', 0, 'int') == 1) $type = 1;
					else $type = 0;
				}
				else $type = 2;
				$join_receivables_type = $receivables_logic->selectReceivablesType($join_result);
				switch($type){
					case 1:
						$this->assign('list', $join_receivables_type['receivables']);
					break;
					case 2:
						$list = array_merge($join_receivables_type['receivables'], $join_receivables_type['notReceivables']);
						$this->assign('list', $list);
					break;
					case 0:
					default:
						$this->assign('list', $join_receivables_type['notReceivables']);
					break;
				}
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.CLIENT.VIEW']);
		}

		public function meeting(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.MEETING.VIEW', $this->employeeID)){
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				/** @var \Core\Model\JoinModel $join_model */
				$join_model = D('Core/Join');
				/** @var \Mobile\Model\ReceivablesModel $receivables_model */
				$receivables_model        = D('Receivables');
				$meeting_result           = $meeting_model->findMeeting(1, ['id' => I('get.mid', 0, 'int')]);
				$join_result              = $join_model->findRecord(0, [
					'mid'    => I('get.mid', 0, 'int'),
					'status' => 1
				]);
				$receivables_result_count = $receivables_model->findReceivablesCount(I('get.mid', 0, 'int'));
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->assign('meeting', $meeting_result);
				$this->assign('join_count', $join_result);
				$this->assign('count', $receivables_result_count);
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.MEETING.VIEW']);
		}

		public function receivablesDetails(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.RECEIVABLES.VIEW', $this->employeeID)){
				$receivables_logic = new ReceivablesLogic();
				/** @var \Core\Model\ReceivablesModel $receivables_model */
				$receivables_model  = D('Core/Receivables');
				$str_object         = new StringPlus();
				$wxc_logic          = new WxCorpLogic();
				$receivables_result = $receivables_model->findRecord(1, ['id' => I('get.id', 0, 'int')]);
				$receivables_info   = $receivables_logic->selectReceivablesMethod($receivables_result);
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->assign('list', $receivables_info);
				// ↓↓↓ 微信分享接口 ↓↓↓
				$random_str = $str_object->makeRandomString(8);
				$time       = time();
				$share_url  = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				$this->assign('wx_config', [
					'share_url'   => $share_url,
					'share_img'   => "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]/".trim(COMMON_IMAGE_PATH, '/.').'/logo.png',
					'share_title' => "收款详情",
					'share_desc'  => "收款单据$receivables_info[order_number]",
					'time'        => $time,
					'random_str'  => $random_str,
					'signature'   => $wxc_logic->getSignature($random_str, $time, $share_url),
					'wx_id'       => $wxc_logic->getCorpID()
				]);
				// ↑↑↑ 微信分享接口 ↑↑↑
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.RECEIVABLES.VIEW']);
		}

		public function receivablesRecord(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.RECEIVABLES.VIEW-ALL', $this->employeeID)){
				//				/** @var \Core\Model\ReceivablesModel $receivables_model */
				//				$receivables_model = D('Core/Receivables');
				//				/** @var \Core\Model\ClientModel $client_model */
				//				$client_model = D('Core/Client');
				//				/** @var \Core\Model\EmployeeModel $employee_model */
				//				$employee_model   = D('Core/Employee');
				$logic            = new ReceivablesLogic();
				$receivables_list = $logic->selectReceivablesListAll();
				//				$option           = [];
				//				if(isset($_GET['mid'])) $option['mid'] = I('get.mid', 0, 'int');
				//				$receivables_result = $receivables_model->findRecord(2, array_merge([
				//					'status' => 'not deleted'
				//				], $option));
				//				$cid                = [];
				//				$eid                = [];
				$count = [];
				foreach($receivables_list as $k => $v){
					$count['price_count'] += $v['price'];
					//					$cid[] = $v['cid'];
					//					$eid[] = $v['payee_id'];
				}
				//				foreach($cid as $k => $v){
				//					$client_result                    = $client_model->findClient(1, ['id' => $v]);
				//					$receivables_result[$k]['unit']   = $client_result['unit'];
				//					$receivables_result[$k]['c_name'] = $client_result['name'];
				//				}
				//				foreach($eid as $k => $v){
				//					$employee_result                  = $employee_model->findEmployee(1, ['id' => $v]);
				//					$receivables_result[$k]['e_name'] = $employee_result['name'];
				//				}
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->assign('count', $count);
				$this->assign('list', $receivables_list);
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.RECEIVABLES.VIEW-ALL']);
		}

		public function myReceivables(){
			$this->wechatID = $this->getWechatID();
			$this->_getEmployeeID();
			$permission_logic = new PermissionLogic();
			if($permission_logic->hasPermission('WECHAT.RECEIVABLES.VIEW', $this->employeeID)){
				$logic = new ReceivablesLogic();
				//				/** @var \Core\Model\ReceivablesModel $receivables_model */
				//				$receivables_model = D('Core/Receivables');
				//				/** @var \Core\Model\ClientModel $client_model */
				//				$client_model = D('Core/Client');
				//				/** @var \Core\Model\EmployeeModel $employee_model */
				//				$employee_model = D('Core/Employee');
				//				/** @var \Core\Model\DepartmentModel $department_model */
				//				$department_model = D('Core/Department');
				$option = [];
				//				if(isset($_GET['mid'])) $option['mid'] = I('get.mid', 0, 'int');
				//				$receivables_result = $receivables_model->findRecord(2, array_merge([
				//					'payee_id' => $this->employeeID,
				//					'status'   => 'not deleted'
				//				], $option));
				//				$employee_list      = $employee_model->findEmployee(2, ['status' => 'not deleted']);
				//				$department_list    = $department_model->findDepartment(2, ['status' => 'not deleted']);
				//				$client_list        = $client_model->findClient(2, ['status' => 'not deleted']);
				$receivables_list = $logic->selectReceivablesList($this->employeeID);
				$count            = [];
				foreach($receivables_list as $k => $v){
					//					$temp_employee = [];
					//					foreach($employee_list as $k1 => $v1){
					//						if($v['payee_id'] == $v1['id']){
					//							$temp_employee                    = $v1;
					//							$receivables_result[$k]['e_name'] = $v1['name'];
					//							break;
					//						}
					//					}
					//					foreach($client_list as $k1 => $v1){
					//						if($v1['id'] == $v['cid']){
					//							$receivables_result[$k]['c_name'] = $v1['name'];
					//							$receivables_result[$k]['unit']   = $v1['unit'];
					//							break;
					//						}
					//					}
					//					foreach($department_list as $k1 => $v1){
					//						if($v1['id'] == $temp_employee['did']){
					//							$receivables_result[$k]['d_name'] = $v1['name'];
					//							break;
					//						}
					//					}
					$count['price_count'] += $v['price'];
				}
				$this->assign('permission_list', $permission_logic->getPermissionList($this->employeeID));
				$this->assign('count', $count);
				$this->assign('list', $receivables_list);
				$this->display();
			}
			else $this->redirect('Error/notPermission', ['permission' => 'WECHAT.RECEIVABLES.VIEW']);
		}
	}