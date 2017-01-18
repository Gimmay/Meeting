<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-2
	 * Time: 17:24
	 */
	namespace Manager\Logic;

	class GroupingLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'add_group':
					if($this->permissionList['GROUP.CREATE']){
						C('TOKEN_ON', false);
						$data             = I('post.');
						$data['mid']      = I('get.mid', 0, 'int');
						$data['creatime'] = time();    //创建时间
						$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						if($_POST['leader_employee']){
							$data['leader'] = I('post.leader_employee');
						}
						elseif($_POST['leader_client']){
							$data['leader'] = I('post.leader_client');
						}
						if($_POST['deputy_leader_employee']){
							$data['deputy_leader'] = I('post.deputy_leader_employee');
						}
						elseif($_POST['deputy_leader_client']){
							$data['deputy_leader'] = I('post.deputy_leader_client');
						}
						/** @var \Core\Model\GroupModel $grouping_model */
						$grouping_model  = D('Core/Group');
						$grouping_result = $grouping_model->createRecord($data);

						return array_merge($grouping_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建分组的权限', '__ajax__' => false];
				break;
				case 'batch_add_group':
					if($this->permissionList['GROUP.CREATE']){
						/** @var \Core\Model\GroupModel $group_model */
						$group_model = D('Core/Group');
						$mid         = I('get.mid', 0, 'int');
						$code_arr    = explode(',', I('post.group_area', ''));
						$data        = [];
						foreach($code_arr as $code){
							$data[] = [
								'mid'      => $mid,
								'code'     => $code,
								'status'   => 1,
								'creatime' => time(),
								'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')
							];
						}
						$result = $group_model->createMultiRecord($data);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有创建分组的权限', '__ajax__' => false];
				break;
				case 'save_client':
					if($this->permissionList['GROUP.ASSIGN-CLIENT']){
						$id   = explode(',', I('post.id', ''));
						$gid  = I('get.gid', 0, 'int');
						$time = I('get.date', 0, 'int');
						$mid  = I('get.mid', 0, 'int');
						$data = [];
						foreach($id as $k => $v){
							$data[] = [
								'mid'      => $mid,
								'gid'      => $gid,
								'cid'      => $v,
								'time'     => $time,
								'creatime' => time(),    //创建时间
								'creator'  => I('session.MANAGER_EMPLOYEE_ID', 0, 'int'), //当前创建者
								'status'   => 1
							];
						}
						/** @var \Core\Model\GroupMemberModel $group_member_model */
						$group_member_model  = D('Core/GroupMember');
						$group_member_result = $group_member_model->createMultiMember($data);

						return array_merge($group_member_result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有分配人员的权限', '__ajax__' => true];
				break;
				case 'get_client':
					if($this->permissionList['GROUP.VIEW-CLIENT']){
						/** @var \Core\Model\JoinModel $join_model */
						$join_model = D('Core/Join');
						/** @var \Core\Model\GroupMemberModel $group_member_model */
						$group_member_model = D('Core/GroupMember');
						/** @var \Core\Model\GroupModel $group_model */
						$group_model         = D('Core/Group');
						$mid                 = I('get.mid', 0, 'int');
						$join_result         = $join_model->findRecord(2, [
							'mid'           => $mid,
							'status'        => 1,
							'review_status' => 1,
							'keyword'       => I('post.keyword', ''),
							'_order'        => 'unit asc,type asc'
						]);
						$group_member_result = $group_member_model->findRecord(2, [
							'status' => 1,
							'time'   => I('get.date', 0, 'int'),
							'gid'    => I('get.gid', 0, 'int')
						]);
						$cid                 = [];
						foreach($group_member_result as $k => $v){
							$cid[] = $v['cid'];
						}
						$new_list = [];
						foreach($join_result as $k => $v){
							if(in_array($v['cid'], $cid)) continue;
							else $new_list[] = $v;
						}
						$group_result  = $group_model->findRecord(2, [
							'mid'    => $mid,
							'status' => 1,
						]);
						$leader        = '';
						$deputy_leader = '';
						foreach($group_result as $k => $v){
							if($v['leader_type'] == 1){
								$leader .= rtrim($v['leader'].',');
							}
							if($v['deputy_leader_type'] == 1){
								$deputy_leader .= rtrim($v['deputy_leader'].',');
							}
						}
						$join_id   = explode(',', $leader.','.$deputy_leader);
						$news_list = [];
						foreach($new_list as $k => $v){
							if(in_array($v['cid'], $join_id)) continue;
							else $news_list[] = $v;
						}

						return array_merge($news_list, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有查看人员分组的权限', '__ajax__' => true];
				break;
				case'get_group_info':
					if($this->permissionList['GROUP.VIEW']){
						/** @var \Core\Model\GroupModel $group_model */
						$group_model = D('Core/Group');
						/** @var \Core\Model\ClientModel $client_model */
						$client_model = D('Core/Client');
						/** @var \Core\Model\EmployeeModel $employee_model */
						$employee_model = D('Core/Employee');
						$id             = I('post.gid', 0, 'int');
						$group_result   = $group_model->findRecord(1, ['id' => $id]);
						if($group_result['leader_type'] == 0){
							$employee_result             = $employee_model->findEmployee(1, ['id' => $group_result['leader']]);
							$group_result['leader_name'] = $employee_result['name'];
						}
						elseif($group_result['leader_type'] == 1){
							$client_result               = $client_model->findClient(1, ['id' => $group_result['leader']]);
							$group_result['leader_name'] = $client_result['name'];
						}
						if($group_result['deputy_leader_type'] == 0){
							$employee_result                    = $employee_model->findEmployee(1, ['id' => $group_result['deputy_leader']]);
							$group_result['deputy_leader_name'] = $employee_result['name'];
						}
						elseif($group_result['deputy_leader_type'] == 1){
							$client_result                      = $client_model->findClient(1, ['id' => $group_result['deputy_leader']]);
							$group_result['deputy_leader_name'] = $client_result['name'];
						}

						return array_merge($group_result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有查看分组的权限', '__ajax__' => true];
				break;
				case 'alter':
					if($this->permissionList['GROUP.ALTER']){
						/** @var \Core\Model\GroupModel $group_model */
						$group_model = D('Core/Group');
						C('TOKEN_ON', false);
						$data = I('post.');
						$id   = I('post.gid', 0, 'int');
						if($data['leader_type'] == 0){
							$data['leader'] = $data['leader_alter_employee'];
						}
						elseif($data['leader_type'] == 1){
							$data['leader'] = $data['leader_alter_client'];
						}
						if($data['deputy_leader_type'] == 0){
							$data['deputy_leader'] = $data['deputy_leader_alter_employee'];
						}
						elseif($data['deputy_leader_type'] == 1){
							$data['deputy_leader'] = $data['deputy_leader_alter_client'];
						}
						$group_result = $group_model->alterRecord(['id' => $id], $data);

						return array_merge($group_result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '您没有修改分组的权限', '__ajax__' => false];
				break;
				case 'delete_client':
					if($this->permissionList['GROUP.ASSIGN-CLIENT']){
						$id = I('post.cid', 0, 'int');
						/** @var \Core\Model\GroupMemberModel $group_member_model */
						$group_member_model = D('Core/GroupMember');
						C('TOKEN_ON', false);
						$group_member_result = $group_member_model->deleteRecord(['id' => $id]);

						return array_merge($group_member_result, ['__ajax__' => true]);
					}
					else return ['status' => true, 'message' => '您没有分配人员的权限', '__ajax__' => true];
				break;
				case 'empty':
					if($this->permissionList['GROUP.ASSIGN-CLIENT']){
						$gid  = I('post.gid', 0, 'int');
						$time = I('get.date', 0, 'int');
						C('TOKEN_ON', false);
						/** @var \Core\Model\GroupMemberModel $group_member_model */
						$group_member_model = D('Core/GroupMember');
						//					$group_member_result = $group_member_model->findRecord(2,['status'=>1,'gid'=>$gid]);
						//					$id = [];
						//					foreach ($group_member_result as $k=>$v){
						//						$id[].= $v['id'];
						//					}
						$group_member_delete = $group_member_model->deleteRecord(['gid' => $gid, 'time' => $time]);

						return array_merge($group_member_delete, ['__ajax__' => false]);
					}
					else return ['status' => true, 'message' => '您没有分配人员的权限', '__ajax__' => true];
				break;
				case 'write_score':
					if($this->permissionList['GROUP.ASSIGN-SCORE']){
						$data = I('post.');
						C('TOKEN_ON', false);
						$data['time']     = I('post.date', 0, 'int');
						$data['gid']      = I('post.gid', 0, 'int');
						$data['mid']      = I('get.mid', 0, 'int');
						$data['score']    = I('post.score', 0, 'int');
						$data['creatime'] = time();    //创建时间
						$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						/** @var \Core\Model\GroupScoreModel $group_score_model */
						$group_score_model = D('Core/GroupScore');
						$group_score_list  = $group_score_model->findRecord(1, [
							'gid'  => $data['gid'],
							'time' => $data['time']
						]);
						if($group_score_list){
							$group_score_result = $group_score_model->alterRecord(['id' => $group_score_list['id']], ['score' => $data['score']]);
						}
						else{
							$group_score_result = $group_score_model->createRecord($data);
						}

						return array_merge($group_score_result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '您没有分配分数的权限', '__ajax__' => true];
				break;
				case 'delete':
					if($this->permissionList['GROUP.DELETE']){
						/** @var \Core\Model\GroupModel $group_model */
						$group_model  = D('Core/Group');
						$id           = I('post.gid', 0, 'int');
						$group_result = $group_model->deleteRecord(['id' => $id]);

						return array_merge($group_result, [
							'__ajax__'   => false,
							'__return__' => U('Grouping/manage', ['mid' => I('get.mid', 0, 'int')])
						]);
					}
					else return ['status' => false, 'message' => '您没有删除分组的权限', '__ajax__' => false];
				break;
				case 'copy':
					$gid  = I('get.gid', 0, 'int');
					$mid  = I('get.mid', 0, 'int');
					$time = I('get.date', 0, 'int');
					C('TOKEN_ON', false);
					/** @var \Core\Model\GroupMemberModel $group_member_model */
					$group_member_model = D('Core/GroupMember');
					$group_member_info  = $group_member_model->findRecord(2, [
						'gid'    => $gid,
						'time'   => $time,
						'status' => 1
					]);
					if($group_member_info){
						return array_merge(['status' => false, 'message' => '请先清空当前已添加的参会人员'], ['__ajax__' => false]);
					}
					if($time != 1){
						$group_member_result = $group_member_model->findRecord(2, [
							'gid'    => $gid,
							'status' => 1,
							'time'   => $time-1
						]);
						$data                = [];
						foreach($group_member_result as $k => $v){
							$data[] = [
								'mid'      => $mid,
								'gid'      => $gid,
								'cid'      => $v['cid'],
								'time'     => $time,
								'creator'  => $v['creator'],
								'creatime' => $v['time']
							];
						}
						$group_member_list = $group_member_model->createMultiMember($data);
					}
					else{
						$group_member_list = ['status' => false, 'message' => '当前会议为开会第一天'];
					}

					return array_merge($group_member_list, ['__ajax__' => false]);
				break;
				case 'batch_delete':
					/** @var \Core\Model\GroupModel $group_model */
					$id_arr      = explode(',', I('post.gid', ''));
					$group_model = D('Core/Group');
					$result      = $group_model->deleteRecord(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function getGroupClient(){  //组合搜索  参会人姓名 单位
			/** @var \Core\Model\ClientModel $client_model */
			$client_model = D('Core/Client');
			/** @var \Core\Model\GroupMemberModel $group_member_model */
			$group_member_model = D('Core/GroupMember');
			/** @var \Core\Model\GroupModel $group_model */
			$group_model         = D('Core/Group');
			$group_result        = $group_model->findRecord(2, [
				'status'  => 'not deleted',
				'mid'     => I('get.mid', 0, 'int'),
				'keyword' => I('get.keyword', '')
			]);
			$new_list            = [];
			$id                  = [];
			$group_member_result = [];
			$group_info          = [];
			if($group_result[0] == ''){
				$group_result = $group_model->findRecord(2, ['mid' => I('get.mid', 0, 'int'), 'status' => 1]);
				foreach($group_result as $k => $v){
					$id[] .= $v['id'];
				}
				foreach($id as $k => $v){
					$group_member_result[] = $group_member_model->findRecord(2, [
						'gid'    => $v,
						'status' => 1,
						'time'   => I('get.date', 0, 'int')
					]);
					foreach($group_member_result[$k] as $k1 => $v1){
						$client_result                               = $client_model->findClient(1, [
							'id'      => $v1['cid'],
							'status'  => 1,
							'keyword' => I('get.keyword', '')
						]);
						$group_member_result[$k][$k1]['client_name'] = $client_result['name'];
						if($group_member_result[$k][$k1]['client_name'] == '') continue;
						$new_list[] = $v;
					}
				}
				foreach($new_list as $k => $v){
					$group_info[] = $group_model->findRecord(1, ['id' => $v, 'status' => 1]);
				}
				$group_info = array_unique($group_info);

				return $group_info;
			}
			else{
				return $group_result;
			}
		}
	}