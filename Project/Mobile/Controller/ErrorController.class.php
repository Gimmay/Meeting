<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-26
	 * Time: 12:02
	 */
	namespace Mobile\Controller;

	use Core\Logic\PermissionLogic;

	class ErrorController extends MobileController{
		public function _initialize(){
			parent::_initialize();
			session_destroy();
			session_unset();
		}

		/**
		 * 可能的原因
		 * 1、未注册该员工记录
		 * 2、未同步微信企业号与系统的微信记录
		 * 3、企业号中的手机号与系统录入的手机号不匹配
		 */
		public function notRegister(){
			$this->display();
		}

		/**
		 * 可能的原因
		 * 1、没有参加该次会议
		 * 2、未同步微信企业号与系统的微信记录
		 * 3、企业号中的手机号与系统录入的手机号不匹配
		 */
		public function notJoin(){
			$meeting_id = I('get.mid', 0, 'int');
			if($meeting_id != 0){
				/** @var \Core\Model\MeetingManagerModel $meeting_manager_model */
				$meeting_manager_model = D('Core/MeetingManager');
				/** @var \Core\Model\EmployeeModel $employee_model */
				$employee_model   = D('Core/Employee');
				$permission_logic = new PermissionLogic();
				$manager_record   = $meeting_manager_model->findRecord(2, [
					'mid'    => $meeting_id,
					'status' => 'not deleted'
				]);
				$signer           = '';
				foreach($manager_record as $val){
					$flag = $permission_logic->hasPermission([
						'WEIXIN.CLIENT.VIEW',
						'WEIXIN.CLIENT.REVIEW',
						'WEIXIN.CLIENT.SIGN',
						'WEIXIN.MEETING.VIEW'
					], $val['eid']);
					if($flag){
						$employee = $employee_model->findEmployee(1, ['id' => $val['eid']]);
						$signer .= $employee['name'].'、';
					}
				}
				$signer = trim($signer, '、');
				$this->assign('signer', $signer);
			}
			$this->display();
		}

		/**
		 * URL缺少会议参数
		 */
		public function requireMeeting(){
			$this->display();
		}

		/**
		 * URL缺少参会人员参数
		 */
		public function requireClient(){
			$this->display();
		}

		/**
		 * 没有授予权限
		 */
		public function notPermission(){
			$this->display();
		}
	}