<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-11
	 * Time: 16:46
	 */
	namespace Manager\Logic;

	use Core\Logic\LogLogic;
	use Core\Logic\UploadLogic;

	class BadgeLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $option = []){
			switch($type){
				case 'upload_image':
					$core_upload_logic = new UploadLogic();
					$result            = $core_upload_logic->upload($_FILES, '/Image/');
					$log_logic         = new LogLogic();
					$log_logic->create([
						'dbTable'  => 'system_upload',
						'dbColumn' => '*',
						'extend'   => 'PC',
						'action'   => '胸卡上传图片',
						'type'     => 'create'
					]);

					return array_merge($result, ['__ajax__' => true, 'imageUrl' => $result['data']['filePath']]);
				break;
				case 'create':
					if($this->permissionList['BADGE.CREATE']){
						/** @var \Core\Model\BadgeModel $model */
						$model              = D('Core/Badge');
						$data               = I('post.');
						$data['name']       = $data['data']['name'];
						$data['data']       = $_POST['data']['temp'];
						$data['attributes'] = json_encode($_POST['data']['attributes']);
						$data['creatime']   = time();
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						C('TOKEN_ON', false);
						$result    = $model->createBadge($data);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_badge',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '创建胸卡',
							'type'     => 'create'
						]);

						return array_merge($result, [
							'__ajax__'   => true,
							'__return__' => U('previewList', ['mid' => $option['mid']])
						]);
					}
					else return ['status' => false, 'message' => '您没有创建胸卡的权限', '__ajax__' => true];
				break;
				case 'assign_badge_for_meeting':
					if($this->permissionList['BADGE.SELECT']){
						/** @var \Core\Model\MeetingModel $meeting_model */
						$meeting_model = D('Core/Meeting');
						$data          = I('post.');
						$data['bid']   = I('post.id', 0, 'int');
						$mid           = I('get.mid', 0, 'int');
						unset($data['id']);
						$result    = $meeting_model->alterMeeting(['id' => $mid], $data);
						$log_logic = new LogLogic();
						$log_logic->create([
							'dbTable'  => 'workflow_meeting',
							'dbColumn' => '*',
							'extend'   => 'PC',
							'action'   => '选择胸卡',
							'type'     => 'modify'
						]);

						return array_merge($result, [
							'__ajax__'   => false,
							'__return__' => U('Badge/preview', ['mid' => $mid])
						]);
					}
					else return ['status' => false, 'message' => '您没有选择胸卡的权限', '__ajax__' => false];
				break;
				case 'delete':
					$bid = I('post.bid', 0, 'int');
					/** @var \Core\Model\BadgeModel $badge_model */
					$badge_model = D('Core/Badge');
					$result      = $badge_model->deleteBadge(['id' => $bid]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setData($type, $data, $option = []){
			switch($type){
				case 'preview:init_temp':
					//					$data['data'] = str_replace('<span class="client_name_badge"></span>', '<span class="client_name_badge">'.$option['client']['name'].'</span>', $data['data']);
					//					$data['data'] = str_replace('<span class="unit_badge"></span>', '<span class="unit_badge">'.$option['client']['unit'].'</span>', $data['data']);
					//					$data['data'] = str_replace('<span class="group"></span>', '<span class="group">'.$option['group']['code'].'</span>', $data['data']);
					//					$data['data'] = str_replace(COMMON_IMAGE_PATH.'/CheckIn_Code.jpg', $option['client']['sign_qrcode'], $data['data']);
					$data['data'] = str_replace('客户姓名', $option['client']['name'], $data['data']);
					$data['data'] = str_replace('单位', $option['client']['unit'], $data['data']);
					$data['data'] = str_replace('组号', $option['group']['code'], $data['data']);
					$data['data'] = str_replace(COMMON_IMAGE_PATH.'/CheckIn_Code.jpg', $option['client']['sign_qrcode'], $data['data']);

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}

		public function getBadge($mid, $cid){
			/** @var \Core\Model\BadgeModel $model */
			$model = D('Core/Badge');
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			/** @var \Core\Model\GroupMemberModel $group_member_model */
			$group_member_model = D('Core/GroupMember');
			$join_record        = $join_model->findRecord(1, ['cid' => $cid, 'mid' => $mid, 'status' => 1]);
			$meeting            = $meeting_model->findMeeting(1, ['id' => $mid]);
			$group              = $group_member_model->findRecord(1, ['cid' => $cid, 'mid' => $mid]);
			if($meeting && $meeting['bid']){
				$badge = $model->findBadge(1, ['id' => $meeting['bid']]);
				$badge = $this->setData('preview:init_temp', $badge, [
					'client'  => $join_record,
					'meeting' => $meeting,
					'group'   => $group
				]);

				return $badge;
			}
			else return null;
		}
	}