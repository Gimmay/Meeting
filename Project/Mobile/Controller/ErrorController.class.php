<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-26
	 * Time: 12:02
	 */
	namespace Mobile\Controller;

	use Core\Logic\MeetingLogic;
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
				$meeting_logic = new MeetingLogic();
				$signer        = implode('、', $meeting_logic->getSigner($meeting_id));
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
			$permission = I('get.permission', '');
			switch(strtoupper($permission)){
				case 'WECHAT.CLIENT.VIEW':
					$this->assign('permission', '查看参会人员');
				break;
				case 'WECHAT.CLIENT.REVIEW':
					$this->assign('permission', '审核参会人员');
				break;
				case 'WECHAT.CLIENT.SIGN':
					$this->assign('permission', '签到');
				break;
				case 'WECHAT.CLIENT.RECEIVABLES':
					$this->assign('permission', '收款');
				break;
				case 'WECHAT.CLIENT.CREATE':
					$this->assign('permission', '创建参会人员');
				break;
				case 'WECHAT.CLIENT.ANTI-REVIEW':
					$this->assign('permission', '取消审核');
				break;
				case 'WECHAT.CLIENT.ANTI-SIGN':
					$this->assign('permission', '取消签到');
				break;
				case 'WECHAT.MEETING.VIEW':
					$this->assign('permission', '查看会议');
				break;
				case 'WECHAT.RECEIVABLES.VIEW':
					$this->assign('permission', '查看收款记录');
				break;
				case 'WECHAT.RECEIVABLES.VIEW-ALL':
					$this->assign('permission', '查看所有收款记录');
				break;
			}
			$this->display();
		}
	}