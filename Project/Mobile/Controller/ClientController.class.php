<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:34
	 */
	namespace Mobile\Controller;

	use Core\Logic\MeetingLogic;
	use Mobile\Logic\ClientLogic;

	class ClientController extends MobileController{
		protected $weixinID  = 0;
		protected $clientID  = 0;
		protected $meetingID = 0;

		public function _initialize(){
			// lqy
			//						$_SESSION['MOBILE_WEIXIN_ID'] = 1216;
			//						$_SESSION['MOBILE_CLIENT_ID'] = 599;
			// quasar
			//									$_SESSION['MOBILE_CLIENT_ID'] = '599';
			//									$_SESSION['MOBILE_WEIXIN_ID'] = '0967';
//									print_r($_SESSION);
			//						print_r($_COOKIE['WEIXIN_REDIRECT_URL']);
//															session_unset();
//															exit;
			parent::_initialize();
		}

		private function _getClientID($redirect = true){
			$get            = function (){
				$logic     = new ClientLogic();
				$client_id = $logic->getVisitorID($this->weixinID);
				if($client_id) $_SESSION['MOBILE_CLIENT_ID'] = $client_id;

				return $client_id;
			};
			$this->clientID = $_SESSION['MOBILE_CLIENT_ID'] ? $_SESSION['MOBILE_CLIENT_ID'] : $get();
			if($this->clientID == 0){
				if($redirect) $this->redirect('Error/notJoin');

				return false;
			}
			else return true;
		}

		private function _getClientParam($redirect = true){
			if(!isset($_GET['cid'])){
				if($redirect) $this->redirect('Error/requireClient');

				return false;
			}
			else{
				$this->clientID = I('get.cid', 0, 'int');

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

		public function myCenter(){
			$this->weixinID = $this->getWeixinID();
			$this->_getMeetingParam();
			$this->_getClientID();
			$logic = new ClientLogic();
			if(IS_POST){
				$type   = (I('post.requestType', ''));
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
			$info = $logic->getClientInformation($this->clientID, $this->meetingID);
			$this->assign('info', $info);
			$this->display();
		}

		/**
		 * 展示签到二维码页面
		 */
		public function myQRCode(){
			$this->_getClientParam();
			$this->_getMeetingParam();
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			$info         = $join_model->findRecord(1, ['cid' => $this->clientID, 'mid' => $this->meetingID]);
			$weixin_info  = $weixin_model->findRecord(1, ['oid' => $this->clientID, 'otype' => 1, 'wtype' => 1]);
			$this->assign('info', $info);
			$this->assign('weixin', $weixin_info);
			$this->display();
		}

		/**
		 * 会议列表
		 */
		public function myMeetingList(){
			$this->weixinID = $this->getWeixinID();
			$this->_getClientID();
			$core_logic = new MeetingLogic();
			$core_logic->initializeStatus();
			$logic        = new ClientLogic();
			$meeting_list = $logic->getMeeting($this->clientID);
			$this->assign('meeting_list', $meeting_list);
			$this->display();
		}

		/**
		 * 会议详情页
		 */
		public function myMeeting(){
			$logic = new ClientLogic();
			if(IS_POST){
				$result         = $logic->handlerRequest(I('post.requestType', ''), ['cid'=>$_SESSION['MOBILE_CLIENT_ID']]);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], I('post.redirectUrl', ''));
					else $this->error($result['message']);
				}
				exit;
			}
			$this->weixinID = $this->getWeixinID();
			$this->_getMeetingParam();
			$this->_getClientID();
			/** @var \Core\Model\JoinModel $join_model */
			$join_model  = D('Core/Join');
			$join_record = $join_model->findRecord(1, [
				'cid'    => $this->clientID,
				'mid'    => $this->meetingID,
				'status' => 'not deleted'
			]);
			if(!$join_record) $this->redirect('Error/notJoin');
			$core_logic = new MeetingLogic();
			$core_logic->initializeStatus();
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$meeting       = $meeting_model->findMeeting(1, ['id' => $this->meetingID, 'status' => 'not deleted']);
			$meeting       = $logic->setDate('myMeeting:set_meeting_column', $meeting);
			$this->assign('info', $join_record);
			$this->assign('meeting', $meeting);
			$this->display();
		}
	}