<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-10
	 * Time: 11:59
	 */
	namespace Manager\Controller;

	use Manager\Logic\BadgeLogic;

	class BadgeController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function manage(){
			$badge_logic = new BadgeLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $badge_logic->handlerRequest($type, ['mid' => $this->meetingID]);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['__return__']) $url = $result['__return__'];
					else $url = '';
					unset($result['__return__']);
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['BADGE.VIEW'] && $this->permissionList['BADGE.CREATE']){
				/** @var \Core\Model\BadgeModel $model */
				$model = D('Core/Badge');
				$list  = $model->findBadge(2, ['status' => 'not deleted']);
				$this->assign('list', $list);
				$this->display();
			}
			else $this->error('您没有查看和创建胸卡模块的权限');
		}

		public function preview(){
			//			/** @var \Core\Model\BadgeModel $model */
			//			$model = D('Core/Badge');
			//			/** @var \Core\Model\MeetingModel $meeting_model */
			//			$meeting_model = D('Core/Meeting');
			//			/** @var \Core\Model\JoinModel $join_model */
			//			$join_model     = D('Core/Join');
			//			$logic          = new BadgeLogic();
			//			$cid            = I('get.cid', 0, 'int');
			//			$meeting_record = $meeting_model->findMeeting(1, ['id' => $this->meetingID]);
			//			$client_record  = $join_model->findRecord(1, ['mid' => $this->meetingID, 'cid' => $cid]);
			//			$info           = $model->findBadge(1, ['id' => $meeting_record['bid']]);
			//			$info           = $logic->setData('preview:init_temp', $info, [
			//				'client'  => $client_record,
			//				'meeting' => $meeting_record
			//			]);
			//			$this->assign('info', $info);print_r($info);exit;
			//			$this->display();
			if($this->permissionList['BADGE.VIEW']){
				/** @var \Core\Model\BadgeModel $model */
				$model = D('Core/Badge');
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				$logic         = new BadgeLogic();
				$meeting       = $meeting_model->findMeeting(1, ['id' => $this->meetingID, 'status' => 'not deleted']);
				$info          = $model->findBadge(1, ['id' => $meeting['bid'], 'status' => 'not deleted']);
				if(isset($_GET['cid'])){
					/** @var \Core\Model\JoinModel $join_model */
					$join_model = D('Core/Join');
					$client     = $join_model->findRecord(1, [
						'mid'    => $this->meetingID,
						'cid'    => I('get.cid', 0, 'int'),
						'status' => 'not deleted'
					]);
					$info       = $logic->setData('preview:init_temp', $info, [
						'client'  => $client,
						'meeting' => $meeting
					]);
				}
				$this->assign('info', $info);
				$this->display();
			}
			else $this->error('您没有查看胸卡模块的权限');
		}

		public function previewList(){
			if(IS_POST){
				$badge_logic = new BadgeLogic();
				$type        = strtolower(I('post.requestType', ''));
				$result      = $badge_logic->handlerRequest($type, ['mid' => $this->meetingID]);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['__return__']) $url = $result['__return__'];
					else $url = '';
					unset($result['__return__']);
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['BADGE.VIEW']){
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				/** @var \Core\Model\BadgeModel $model */
				$model       = D('Core/Badge');
				$cur_meeting = $meeting_model->findMeeting(1, ['mid' => $this->meetingID, 'status' => 'not deleted']);
				$list        = $model->findBadge(2, ['status' => 'not deleted']);
				$this->assign('list', $list);
				$this->assign('cur_bid', $cur_meeting['bid']);
				$this->display();
			}
			else $this->error('您没有查看胸卡模块的权限');
		}
	}