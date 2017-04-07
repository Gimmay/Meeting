<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-31
	 * Time: 17:35
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class MessageConfigureModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'message_configure';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid',
			'type'      => 'type'
		];
		/** 消息关联操作 */
		const ACTION = [
			1 => '邀约信息',
			2 => '签到提示',
			3 => '取消签到提示'
		];

		/**
		 * 创建消息关联记录
		 *
		 * @param array $data 消息关联信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '消息关联成功', 'id' => $result] : [
					'status'  => false,
					'message' => '消息关联失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		public function getWechatOfficialConfigure($meeting_id){

		}

		public function getWechatEnterpriseConfigure($meeting_id){}

		public function getSMSMobsetConfigure($meeting_id){
			// todo
			if(!$this->fetch(['mid' => $meeting_id])) return [
				'status'   => false,
				'message'  => '找不到会议配置信息'
			];
			$meeting_configure = $this->getObject();
			/** @var \General\Model\ApiConfigureModel $api_configure_model */
			$api_configure_model = D('General/ApiConfigure');
			if($meeting_configure['wechat_enterprise_configure']){
				if(!$api_configure_model->fetch([
					'id'     => $meeting_configure['wechat_enterprise_configure'],
					'status' => ['neq', 2]
				])
				) return [
					'status'   => false,
					'message'  => '找不到微信企业号接口配置信息',
					'__ajax__' => true
				];
				$api_configure = $api_configure_model->getObject();
				print_r($api_configure);
				exit;
			}
		}

		public function getEmailConfigure($meeting_id){}
	}