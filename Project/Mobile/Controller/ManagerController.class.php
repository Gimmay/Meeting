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
		protected $weixinID   = 0;
		protected $employeeID = 0;
		protected $meetingID  = 0;

		public function _initialize(){
			$_SESSION['MOBILE_WEIXIN_ID']   = 1090;
			$_SESSION['MOBILE_EMPLOYEE_ID'] = 2;
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

				return $logic->getVisitorID($this->weixinID);
			};
			$this->employeeID = isset($_SESSION['MOBILE_EMPLOYEE_ID']) ? I('session.MOBILE_EMPLOYEE_ID', 0, 'int') : $get();
			if($this->employeeID == 0){
				if($redirect) $this->redirect('Error/isNotRegister');

				return false;
			}
			else return true;
		}

		public function addClient(){
			$logic = new ManagerLogic();
			if(IS_POST){
				$type   = 'addClient:create_create';
				$result = $logic->handlerRequest($type, ['employeeID' => I('session.MOBILE_EMPLOYEE_ID', 0, 'int')]);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], U('clientList', ['mid'=>I('get.mid', 0, 'int')]));
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$this->display();
		}

		public function client(){
			$this->weixinID = $this->getWeixinID();
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
			$permission       = $permission_logic->hasPermission('WEIXIN.CLIENT.VIEW', $this->employeeID);
			if($permission){
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
				$this->assign('list', $join_result);
				$this->assign('info', $info);
				$this->assign('meeting', $meeting);
				$this->display();
			}
			else $this->redirect('Error/notPermission');
		}

		public function clientList(){
			$this->weixinID = $this->getWeixinID();
			$this->_getMeetingParam();
			$logic = new ManagerLogic();
			$client_list = $logic->findData('clientList:find_client_list', ['mid' => I('get.mid', 0, 'int')]);
			if(!isset($_GET['sign'])) $title = '所有参会人员';
			elseif(I('get.sign',0,'int')===0) $title = '未签到人员';
			elseif(I('get.sign',0,'int')===1) $title = '已签到人员';
			else $title = '参会人员';
			$this->assign('title', $title);
			$this->assign('client_list', $client_list);
			$this->display();
		}

		public function meetingList(){
			$this->weixinID = $this->getWeixinID();
			$this->_getEmployeeID();
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$logic         = new ManagerLogic();
			$meeting_list  = $meeting_model->findMeeting(2, ['status' => 'not deleted']);
			$meeting_list  = $logic->setData('meetingList:set_meeting_list', $meeting_list);
			$this->assign('meeting_list', $meeting_list);
			$this->display();
		}

		public function meeting(){
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$logic         = new ManagerLogic();
			$meeting_info  = $meeting_model->findMeeting(1, [
				'status' => 'not deleted',
				'id'     => I('get.mid', 0, 'int')
			]);
			$meeting_info  = $logic->setData('meeting:set_extend_column', $meeting_info);
			$statistics    = $logic->setData('meeting:set_statistics_data', $meeting_info);
			$this->assign('meeting', $meeting_info);
			$this->assign('statistics', $statistics);
			$this->display();
		}

		public function myCenter(){
			$this->weixinID = $this->getWeixinID();
			$this->_getEmployeeID();
			$logic = new ManagerLogic();
			$info  = $logic->getEmployeeInformation($this->employeeID);
			$this->assign('info', $info);
			$this->display();
		}
	}