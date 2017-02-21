<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:34
	 */
	namespace Mobile\Controller;

	use Core\Logic\MeetingLogic;
	use Core\Logic\PermissionLogic;
	use Mobile\Logic\ClientLogic;
	use Mobile\Logic\MobileLogic;

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
				$logic  = new ClientLogic();
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
			$meeting_logic = new MeetingLogic();
			$signer        = implode('、', $meeting_logic->getSigner($this->meetingID));
			$info          = $join_model->findRecord(1, ['cid' => $this->clientID, 'mid' => $this->meetingID]);
			$wechat_info   = $wechat_model->findRecord(1, ['oid' => $this->clientID, 'otype' => 1, 'wtype' => 1]);
			$meeting       = $meeting_model->findMeeting(1, ['id' => $this->meetingID, 'status' => 'not deleted']);
			$this->assign('meeting', $meeting);
			$this->assign('info', $info);
			$this->assign('wechat', $wechat_info);
			$this->assign('signer', $signer);
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


		public function report2(){
			$default_order_column = 'unit';
			$default_order_method = 'asc';
			$keyword = I('get.keyword', '');
			$area = I('get.area', '');
			$no = I('get.no', '');
			$order_column = I('get._orderColumn', $default_order_column);
			$order_method = I('get._orderMethod', $default_order_method);
			$mid = I('get.mid', 0, 'int');
			$mobile_logic = new MobileLogic();
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$report2 = $mobile_logic->getReport2($mid, $order_column, $order_method, $area, $no, $keyword);
			$report2_area = $mobile_logic->getReport2Area($mid);
			$report2_no = $mobile_logic->getReport2No($mid);
			$report2_statistics = $mobile_logic->getReport2Statistics($mid, $area, $no, $keyword);
			$meeting = $meeting_model->findMeeting(1, ['id'=>$mid]);
			$this->assign('report', $report2);
			$this->assign('area_list', $report2_area);
			$this->assign('no_list', $report2_no);
			$this->assign('statistics', $report2_statistics);
			$this->assign('meeting', $meeting);
			$this->assign('default_order_column', isset($_GET['_orderColumn'])?$_GET['_orderColumn']:$default_order_column);
			$this->assign('default_order_method', isset($_GET['_orderMethod'])?$_GET['_orderMethod']:$default_order_method);
			$this->display();
		}

		public function report2Client(){
			$default_order_column = 'unit';
			$default_order_method = 'asc';
			$unit = I('get.unit', '');
			$order_column = I('get._orderColumn', $default_order_column);
			$order_method = I('get._orderMethod', $default_order_method);
			$mid = I('get.mid', 0, 'int');
			$mobile_logic = new MobileLogic();
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$meeting = $meeting_model->findMeeting(1, ['id'=>$mid]);
			$report2 = $mobile_logic->getReport2Client($mid, $order_column, $order_method, $unit);
			$report2_statistics = $mobile_logic->getReport2ClientStatistics($mid, $unit);
			$this->assign('report', $report2);
			$this->assign('statistics', $report2_statistics);
			$this->assign('meeting', $meeting);
			$this->display();
		}
		
		public function report2Group(){
			$mergeRecord = function($list){
				$new_list = [];
				foreach($list as $key => $val){
					if(!isset($new_list[$val['gid']])) $new_list[$val['gid']] = ['list'=>[]];
					$new_list[$val['gid']]['list'][] = $val;
					$new_list[$val['gid']]['group_name'] = $val['code'];
				}
				return $new_list;
			};
			$mobile_logic = new MobileLogic();
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$keyword = I('get.keyword','');
			$mid = I('get.mid', 0, 'int');
			$list = $mobile_logic->getReport2Group($mid, $keyword);
			$meeting = $meeting_model->findMeeting(1, ['id'=>$mid]);
			$list = $mergeRecord($list);
			$this->assign('list', $list);
			$this->assign('meeting', $meeting);
			$this->display();
		}
		
		public function report2Receivables(){
			$mid = I('get.mid', 0, 'int');
			$order_column = I('get._orderColumn', 'price');
			$order_method = I('get._orderMethod', 'desc');
			$result = M()->query("select * from (select *,
(select SUM(workflow_receivables_option.price)
from workflow_coupon_item
join workflow_receivables on workflow_receivables.mid = workflow_coupon_item.mid
join workflow_receivables_option on workflow_receivables_option.rid = workflow_receivables.id
where workflow_coupon_item.coupon_id = project_list.id and workflow_receivables.mid = $mid and workflow_receivables.status = 1 and workflow_coupon_item.id like workflow_receivables.coupon_ids) price,
(select count(*)
from workflow_coupon_item
join workflow_receivables on workflow_receivables.mid = workflow_coupon_item.mid
join workflow_receivables_option on workflow_receivables_option.rid = workflow_receivables.id
where workflow_coupon_item.coupon_id = project_list.id and workflow_receivables.mid = $mid and workflow_receivables.status = 1 and workflow_coupon_item.id like workflow_receivables.coupon_ids) total
from (select
workflow_coupon.id, workflow_coupon.name
from workflow_coupon
where workflow_coupon.mid = $mid and workflow_coupon.status = 1 
 -- and workflow_coupon.id in (152,153,150,151,157,154,155)
) project_list) tt
order by $order_column $order_method");
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$meeting = $meeting_model->findMeeting(1, ['id'=>$mid]);
			$this->assign('meeting', $meeting);
			$this->assign('list', $result);
			$this->assign('default_order_column', $order_column);
			$this->assign('default_order_method', $order_method);
			$this->display();
		}
	}