<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:34
	 */
	namespace Mobile\Controller;

	use Core\Logic\PermissionLogic;
	use Core\Logic\WxCorpLogic;
	use Mobile\Logic\ClientLogic;

	class ClientController extends MobileController{
		protected $weixinID   = 0;
		protected $clientID   = 0;
		protected $employeeID = 0;
		protected $meetingID  = 0;

		public function _initialize(){
			parent::_initialize();
			if($this->_getWeixinID()) $this->_initAttribute();
		}

		private function _initAttribute(){
			$logic            = new ClientLogic();
			$this->meetingID  = I('get.mid', 0, 'int');
			$this->clientID   = isset($_SESSION['MOBILE_CLIENT_ID']) ? I('session.MOBILE_CLIENT_ID', 0, 'int') : $logic->getVisitorID(1, $this->weixinID);
			$this->employeeID = isset($_SESSION['MOBILE_EMPLOYEE_ID']) ? I('session.MOBILE_EMPLOYEE_ID', 0, 'int') : $logic->getVisitorID(0, $this->weixinID);
			if(!$this->meetingID) $this->redirect('Error/requireMeeting');
		}

		/**
		 * 获取WeixinID
		 *
		 * @return bool
		 */
		private function _getWeixinID(){
			if(!isset($_SESSION['MOBILE_WEIXIN_ID'])){
				$wxcorp_logic   = new WxCorpLogic();
				$weixin_user_id = $wxcorp_logic->getUserID();
				if($weixin_user_id){
					session('MOBILE_WEIXIN_ID', $weixin_user_id);

					return true;
				}
				else return false;
			}
			else{
				$this->weixinID = I('session.MOBILE_WEIXIN_ID');

				return true;
			}
		}

		private function _isGetWeixin($redirect = true){
			if($this->weixinID == 0){
				if($redirect) $this->redirect('Weixin/isNotFollow');

				return false;
			}
			else return true;
		}

		private function _isGetClient($redirect = true){
			if($this->clientID == 0){
				if($redirect) $this->redirect('Error/notJoin');

				return false;
			}
			else return true;
		}

		private function _isGetEmployee($redirect = true){
			if($this->employeeID == 0){
				if($redirect) $this->redirect('Error/isNotRegister');

				return false;
			}
			else return true;
		}

		public function manage(){
			$logic = new ClientLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type, ['eid' => $this->employeeID]);
				if($result === -1){
				}
				else{
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->_isGetEmployee()){
				$permission_logic = new PermissionLogic();
				$permission       = $permission_logic->hasPermission('WEIXIN.VIEW-CLIENT', $this->employeeID);
				if($permission){
					$cid  = I('get.cid', 0, 'int');
					$mid  = I('get.mid', 0, 'int');
					$info = $logic->getUserInformation($cid, $mid);
					$this->assign('info', $info);
					$this->display();
				}
				else $this->redirect('Error/notPermission');
			}
		}

		public function myCenter(){
			$logic = new ClientLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type, ['cid' => $this->clientID]);
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
			if($this->_isGetClient()){
				$info = $logic->getUserInformation($this->clientID, $this->meetingID);
				$this->assign('info', $info);
				$this->display();
			}
		}
	}