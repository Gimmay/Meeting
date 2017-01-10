<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-1-10
	 * Time: 11:34
	 */
	namespace Core\Logic;

	class GroupLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function getMemberNumber($gid, $time){
			/** @var \Core\Model\GroupMemberModel $group_member_model */
			$group_member_model = D('Core/GroupMember');

			return $group_member_model->findRecord(0, ['gid' => $gid, 'status' => 1, 'time' => $time]);
		}

		public function isFull($gid, $time){
			/** @var \Core\Model\GroupModel $group_model */
			$group_model = D('Core/Group');
			$group_info  = $group_model->findRecord(1, ['id' => $gid]);

			return ($group_info['capacity']>$this->getMemberNumber($gid, $time)) ? false : true;
		}
	}
