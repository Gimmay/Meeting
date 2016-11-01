<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-1
	 * Time: 11:11
	 */
	namespace Mobile\Controller;

	use Core\Logic\PermissionLogic;
	use Core\Logic\WxCorpLogic;
	use Mobile\Logic\ClientLogic;
	use Mobile\Logic\ManagerLogic;

	class ManagerController extends MobileController{
		protected $weixinID   = 0;
		protected $employeeID = 0;
		protected $meetingID  = 0;

		public function _initialize(){
//			$_SESSION['MOBILE_WEIXIN_ID']   = 599;
//			$_SESSION['MOBILE_EMPLOYEE_ID'] = 2;
			parent::_initialize();
		}

		private function _getWeixinID($redirect = true){
			if(!isset($_SESSION['MOBILE_WEIXIN_ID'])){
				$wxcorp_logic   = new WxCorpLogic();
				$weixin_user_id = $wxcorp_logic->getUserID();
				if($weixin_user_id){
					session('MOBILE_WEIXIN_ID', $weixin_user_id);

					return true;
				}
				else{
					if($redirect) $this->redirect('Weixin/isNotFollow');

					return false;
				}
			}
			else{
				$this->weixinID = I('session.MOBILE_WEIXIN_ID', 0, 'int');

				return true;
			}
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

				return $logic->getVisitorID($this->weixinID);
			};
			$this->employeeID = isset($_SESSION['MOBILE_EMPLOYEE_ID']) ? I('session.MOBILE_EMPLOYEE_ID', 0, 'int') : $get();
			if($this->employeeID == 0){
				if($redirect) $this->redirect('Error/isNotRegister');

				return false;
			}
			else return true;
		}

		public function client(){
			$this->_getWeixinID();
			$this->_getEmployeeID();
			$this->_getMeetingParam();
			$logic = new ManagerLogic();
			if(IS_POST){
				$type   = (I('post.requestType', ''));
				$result = $logic->handlerRequest($type, ['eid' => $this->employeeID]);
				if($result === -1){
				}
				else{
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$permission_logic = new PermissionLogic();
			$permission       = $permission_logic->hasPermission('WEIXIN.VIEW-CLIENT', $this->employeeID);
			if($permission){
				$client_logic = new ClientLogic();
				$cid          = I('get.cid', 0, 'int');
				$mid          = I('get.mid', 0, 'int');
				$info         = $client_logic->getClientInformation($cid, $mid);
				$this->assign('info', $info);
				$this->display();
			}
			else $this->redirect('Error/notPermission');
		}
	}