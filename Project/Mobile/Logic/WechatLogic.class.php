<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-26
	 * Time: 17:46
	 */

	namespace Mobile\Logic;

	use General\Logic\MeetingLogic;
	use General\Model\MeetingConfigureModel;
	use General\Model\SystemConfigureModel;
	use Quasar\ExternalInterface\Wechat\EnterpriseAccountLibrary;
	use Quasar\ExternalInterface\Wechat\OfficialAccountLibrary;

	class WechatLogic extends MobileLogic{
		public function handlerRequest($type, $opt = []){
		}

		public function setData($type, $data){
		}

		/**
		 * 获取访客微信身份
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array|null|string
		 */
		public function getWechatUser($meeting_id = 0){
			if($meeting_id == 0){
				/** @var \General\Model\SystemConfigureModel $system_configure_model */
				$system_configure_model = D('General/SystemConfigure');
				$system_configure       = $system_configure_model->getConfigure();
				switch($system_configure['wechat_mode']){
					case SystemConfigureModel::WECHAT_MODE_OFFICIAL:
						$wechat_configure = $system_configure_model->getWechatOfficialConfigure();
						if(!$wechat_configure['status']) return $wechat_configure;
						$wechat_official_lib = new OfficialAccountLibrary($wechat_configure['data']['appID'], $wechat_configure['data']['appSecret']);
						$wechat_info         = $wechat_official_lib->getUserFullSet(1);

						// todo 对接公众号接口信息
						print_r($wechat_info);
						exit;
					break;
					case SystemConfigureModel::WECHAT_MODE_ENTERPRISE:
						$wechat_configure = $system_configure_model->getWechatEnterpriseConfigure();
						if(!$wechat_configure['status']) return $wechat_configure;
						$wechat_enterprise_lib = new EnterpriseAccountLibrary($wechat_configure['data']['corpID'], $wechat_configure['data']['corpSecret']);
						$wechat_info           = $wechat_enterprise_lib->getID();

						return ['status' => true, 'message' => '获取成功', 'data' => $wechat_info];
					break;
					default:
						return ['status' => false, 'message' => '错误的微信模式'];
					break;
				}
			}
			else{
				// 获取会议信息
				/** @var \General\Model\MeetingModel $meeting_model */
				$meeting_model = D('General/Meeting');
				if(!$meeting_model->fetch(['id' => $meeting_id])) return ['status' => false, 'message' => '找不到会议信息'];
				$meeting = $meeting_model->getObject();
				// 获取会议类型
				$meeting_logic = new MeetingLogic();
				$meeting_type  = $meeting['type'];
				$module        = $meeting_logic->getModuleByType($meeting_type);
				// 获取会议配置
				/** @var MeetingConfigureModel $meeting_configure_model */
				$meeting_configure_model = D("$module/MeetingConfigure");
				/** @noinspection PhpUndefinedMethodInspection */
				$meeting_configure = $meeting_configure_model->where(['mid' => $meeting_id])->find();
				if(!$meeting_configure) return ['status' => false, 'message' => '找不到会议配置'];
				// 获取微信配置
				switch($meeting_configure['wechat_mode']){
					case MeetingConfigureModel::WECHAT_MODE_OFFICIAL:
						$meeting_configure_result = $meeting_configure_model->getWechatOfficialConfigure($meeting_id);
						if(!$meeting_configure_result['status']) return $meeting_configure_result;
						$wechat_official_lib = new OfficialAccountLibrary($meeting_configure_result['data']['appID'], $meeting_configure_result['data']['appSecret']);
						$wechat_info         = $wechat_official_lib->getUserFullSet(1);
						// todo 对接公众号接口信息
						print_r($wechat_info);
						exit;
					break;
					case MeetingConfigureModel::WECHAT_MODE_ENTERPRISE:
						$meeting_configure_result = $meeting_configure_model->getWechatEnterpriseConfigure($meeting_id);
						if(!$meeting_configure_result['status']) return $meeting_configure_result;
						$wechat_enterprise_lib = new EnterpriseAccountLibrary($meeting_configure_result['data']['corpID'], $meeting_configure_result['data']['corpSecret']);
						$wechat_info           = $wechat_enterprise_lib->getID();

						return ['status' => true, 'message' => '获取成功', 'data' => $wechat_info];
					break;
					default:
						return ['status' => false, 'message' => '错误的微信模式'];
					break;
				}
			}
		}
	}