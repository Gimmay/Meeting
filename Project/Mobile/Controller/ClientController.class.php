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
		protected $wechatID  = 0;
		protected $clientID  = 0;
		protected $meetingID = 0;

		public function _initialize(){
			// quasar
			//			$_SESSION['MOBILE_CLIENT_ID'] = 599;
			//			$_SESSION['MOBILE_WECHAT_ID'] = 1090;
			//			session_destroy();
			//			session_unset();
			//			exit;
			//			$_SESSION['MOBILE_CLIENT_ID'] = '573';
			//			$_SESSION['MOBILE_WECHAT_ID'] = '0018';
			$meeting_logic = new MeetingLogic();
			$meeting_logic->initializeStatus();
			parent::_initialize();
		}

		private function _getClientID($redirect = true){
			$get            = function (){
				$logic     = new ClientLogic();
				$client_id = $logic->getVisitorID($this->wechatID);
				if($client_id) $_SESSION['MOBILE_CLIENT_ID'] = $client_id;

				return $client_id;
			};
			$this->clientID = $_SESSION['MOBILE_CLIENT_ID'] ? $_SESSION['MOBILE_CLIENT_ID'] : $get();
			if($this->clientID == 0){
				if($redirect) $this->redirect('Error/notJoin', ['mid' => $this->meetingID]);

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
			$this->wechatID = $this->getWechatID();
			$this->_getClientID();
			$this->_getMeetingParam();
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
			$info = $logic->getClientInformation($this->clientID);
			$this->assign('info', $info);
			$this->display();
		}

		/**
		 * 展示签到二维码页面
		 */
		public function myQRCode(){
			$this->_getClientParam();
			$this->_getMeetingParam();
			if(IS_POST){
				$logic = new ClientLogic();
				$result = $logic->handlerRequest(I('post.requestType', ''), [
					'cid' => $this->clientID,
					'mid' => $this->meetingID
				]);
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
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			/** @var \Core\Model\WechatModel $wechat_model */
			$wechat_model = D('Core/Wechat');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$info          = $join_model->findRecord(1, ['cid' => $this->clientID, 'mid' => $this->meetingID]);
			$wechat_info   = $wechat_model->findRecord(1, ['oid' => $this->clientID, 'otype' => 1, 'wtype' => 1]);
			$meeting       = $meeting_model->findMeeting(1, ['id' => $this->meetingID, 'status' => 'not deleted']);
			$this->assign('meeting', $meeting);
			$this->assign('info', $info);
			$this->assign('wechat', $wechat_info);
			$this->display();
		}

		/**
		 * 会议列表
		 */
		public function myMeetingList(){
			$this->wechatID = $this->getWechatID();
			$this->_getClientID();
			$core_logic = new MeetingLogic();
			$core_logic->initializeStatus();
			$logic        = new ClientLogic();
			$meeting_list = $logic->getMeeting($this->clientID);
			$this->assign('meeting_list', $meeting_list);
			$this->display();
		}

		//		private function _sign($meeting, $join_record){
		//			/** @var \Core\Model\JoinModel $join_model */
		//			$join_model      = D('Core/Join');
		//			$sign_start_time = strtotime($meeting['sign_start_time']);
		//			$sign_end_time   = strtotime($meeting['sign_end_time']);
		//			$cur_time        = time();
		//			if($join_record['sign_status'] != 1 && $join_record['review_status'] == 1 && $sign_start_time<=$cur_time && $sign_end_time>=$cur_time){
		//				$join_model->alterRecord([
		//					'id' => $join_record['id']
		//				], [
		//					'sign_time'        => $cur_time,
		//					'sign_status'      => 1,
		//					'sign_director_id' => $this->clientID,
		//					'sign_type'        => 2
		//				]);
		//			}
		//		}
		/**
		 * 会议详情页
		 */
		public function myMeeting(){
			$logic = new ClientLogic();
			if(IS_POST){
				$result = $logic->handlerRequest(I('post.requestType', ''), ['cid' => $_SESSION['MOBILE_CLIENT_ID']]);
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
			$this->wechatID = $this->getWechatID();
			$this->_getMeetingParam();
			$this->_getClientID();
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$meeting       = $meeting_model->findMeeting(1, ['id' => $this->meetingID, 'status' => 'not deleted']);
			$meeting       = $logic->setData('myMeeting:set_meeting_column', $meeting);
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
			//			$this->_sign($meeting, $join_record);
			$sign_start_time = strtotime($meeting['sign_start_time']);
			$sign_end_time   = strtotime($meeting['sign_end_time']);
			$cur_time        = time();
			$this->assign('can_sign', (I('get.sign', 0, 'int') == 1 && $sign_start_time<=$cur_time && $sign_end_time>=$cur_time && $join_record['review_status'] && $join_record['sign_status'] != 1) ? 1 : 0);
			$this->assign('info', $join_record);
			$this->assign('meeting', $meeting);
			$this->display();
		}
	}