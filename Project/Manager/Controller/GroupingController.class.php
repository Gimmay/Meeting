<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-27
	 * Time: 10:33
	 */
	namespace Manager\Controller;

	use Manager\Logic\ClientLogic;
	use Manager\Logic\GroupingLogic;

	class GroupingController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function manage(){
			$logic = new GroupingLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['__return__']) $url = $result['__return__'];
					else $url = '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['GROUP.VIEW']){
				/** @var \Manager\Model\ClientModel $client */
				$client      = D('Client');
				$client_list = $client->getClientSelectList(I('get.mid', 0, 'int'));
				/** @var \Manager\Model\EmployeeModel $employee */
				$employee      = D('Employee');
				$employee_list = $employee->getEmployeeSelectList();
				/** @var \Core\Model\EmployeeModel $employee_model */
				$employee_model = D('Core/Employee');
				/** @var \Core\Model\GroupModel $group_model */
				$group_model = D('Core/Group');
				/** @var \Core\Model\GroupMemberModel $group_member_model */
				$group_member_model = D('Core/GroupMember');
				/** @var \Core\Model\ClientModel $client_model */
				$client_model = D('Core/Client');
				/** @var \Core\Model\MeetingModel $meeting_model */
				$meeting_model = D('Core/Meeting');
				/** @var \Core\Model\GroupScoreModel $group_score_model */
				$group_score_model   = D('Core/GroupScore');
				$meeting_result      = $meeting_model->findMeeting(1, ['id' => I('get.mid', 0, 'int')]);
				$start_date          = strtotime($meeting_result['end_time']);
				$end_date            = strtotime($meeting_result['start_time']);
				$time                = round(($start_date-$end_date)/86400)+1;
				$group_result        = $group_model->findRecord(2, [
					'status'  => 'not deleted',
					'mid'     => I('get.mid', 0, 'int'),
					'keyword' => I('get.keyword', '')
				]);
				$group_member_result = $group_member_model->findRecord(2, [
					'status' => 'not deleted',
					'gid'    => I('get.gid', 0, 'int'),
					'time'   => I('get.date', 0, 'int')
				]);
				$group_member_count  = $group_member_model->findRecord(0, [
					'status' => 'not deleted',
					'gid'    => I('get.gid', 0, 'int'),
					'time'   => I('get.date', 0, 'int')
				]);
				foreach($group_member_result as $k => $v){
					$client_result                          = $client_model->findClient(1, ['id' => $v['cid']]);
					$group_member_result[$k]['client_name'] = $client_result['name'];
				}
				$group_leader = $group_model->findRecord(1, ['id' => I('get.gid', 0, 'int'), 'status' => 1]);
				if($group_leader['leader_type'] == 0){
					$employee_result                  = $employee_model->findEmployee(1, ['id' => $group_leader['leader']]);
					$group_leader['leader_name_name'] = $employee_result['name'];
				}
				elseif($group_leader['leader_type'] == 1){
					$client_result                    = $client_model->findClient(1, ['id' => $group_leader['leader']]);
					$group_leader['leader_name_name'] = $client_result['name'];
				}
				if($group_leader['deputy_leader_type'] == 0){
					$employee_result                    = $employee_model->findEmployee(1, ['id' => $group_leader['deputy_leader']]);
					$group_leader['deputy_leader_name'] = $employee_result['name'];
				}
				elseif($group_leader['deputy_leader_type'] == 1){
					$client_result                      = $client_model->findClient(1, ['id' => $group_leader['deputy_leader']]);
					$group_leader['deputy_leader_name'] = $client_result['name'];
				}
				foreach($group_result as $k => $v){
					$group_score_result = [];
					for($i = 1; $i<=$time; $i++){
						$score_temp   = 0;
						$score_result = $group_score_model->findRecord(2, [
							'gid'    => $v['id'],
							'status' => 1,
							'time'   => $i
						]);
						foreach($score_result as $k1 => $v1){
							$score_temp += $v1['score'];
						}
						$group_score_result[] = [
							'gid'   => $v['id'],
							'score' => $score_temp,
							'time'  => $i,
						];
					}
					//				$group_score_result        = $group_score_model->findRecord(2, [
					//					'gid'    => $v['id'],
					//					'status' => 1,
					//					'_order' => 'time asc'
					//				]);
					$group_result[$k]['score'] = $group_score_result;
					foreach($group_result[$k]['score'] as $k1 => $v1){
						$group_result[$k]['count_score'] += $v1['score'];
					}
				}
				$this->assign('time', $time);
				$this->assign('leader', $group_leader);
				$this->assign('count', $group_member_count);
				$this->assign('member', $group_member_result);
				$this->assign('group', $group_result);
				if(!I('get.gid', 0, 'int') && isset($group_result[0])){
					$this->redirect('', [
						'mid'  => I('get.mid', 0, 'int'),
						'gid'  => $group_result[0]['id'],
						'date' => 1
					]);
				}
				$this->assign('employee', $employee_list);
				$this->assign('client', $client_list);
				$this->display();
			}
			else $this->error('您没有查看分组的权限');
		}
	}