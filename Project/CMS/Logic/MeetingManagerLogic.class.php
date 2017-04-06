<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-10
	 * Time: 16:02
	 */
	namespace CMS\Logic;

	use General\Logic\Time;

	class MeetingManagerLogic extends CMSLogic{
		public function handlerRequest($type, $opt = []){
		}

		public function setData($type, $data){
		}

		/**
		 * 创建/分配会务人员
		 *
		 * @param int $meeting_id 会议ID
		 * @param int $user_id    用户ID
		 * @param int $role_id    角色ID
		 *
		 * @return array
		 */
		public function create($meeting_id, $user_id, $role_id){
			/** @var \General\Model\MeetingManagerModel $meeting_manager_model */
			$meeting_manager_model = D('General/MeetingManager');
			if($meeting_manager_model->fetch([
				'mid' => $meeting_id,
				'uid' => $user_id,
				'rid' => $role_id
			])
			){ // 查询是否有分配记录
				$meeting_manager = $meeting_manager_model->getObject();
				if($meeting_manager['status'] == 1) return ['status' => false, 'message' => '该用户已经是会务人员']; // 记录正常
				else{ // 记录已被删除或禁用
					$result = $meeting_manager_model->modify([
						'mid' => $meeting_id,
						'uid' => $user_id,
						'rid' => $role_id
					], ['status' => 1]);
					if($result['status']) $result['message'] = '分配成功';
					else $result['message'] = '分配失败';

					return $result;
				}
			}
			else{
				C('TOKEN_ON', false);
				$result = $meeting_manager_model->create([
					'mid'      => $meeting_id,
					'uid'      => $user_id,
					'rid'      => $role_id,
					'creatime' => Time::getCurrentTime(),
					'creator'  => Session::getCurrentUser()
				]);

				return $result;
			}
		}

		/**
		 * 删除会务人员
		 *
		 * @param int $meeting_id 会议ID
		 * @param int $user_id    用户ID
		 * @param int $role_id    角色ID
		 *
		 * @return array
		 */
		public function delete($meeting_id, $user_id, $role_id){
			/** @var \General\Model\MeetingManagerModel $meeting_manager_model */
			$meeting_manager_model = D('General/MeetingManager');
			$result                = $meeting_manager_model->modify([
				'mid' => $meeting_id,
				'uid' => $user_id,
				'rid' => $role_id
			], ['status' => 2]);
			if($result['status']) $result['message'] = '删除成功';
			else $result['message'] = '删除失败';

			return $result;
		}
	}