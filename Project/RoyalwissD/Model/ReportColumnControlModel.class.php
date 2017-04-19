<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-13
	 * Time: 9:36
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class ReportColumnControlModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'report_column_control';
		const TABLE_NAME = 'report_column_control';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid'
		];
		/** 客户相关字段 */
		const TYPE_CLIENT = 1;
		/** 收款相关字段 */
		const TYPE_RECEIVABLES = 2;
		/** 操作-读 */
		const ACTION_READ = 0;
		/** 操作-搜索 */
		const ACTION_SEARCH = 1;

		/**
		 * 创建报表字段控制记录
		 *
		 * @param array $data 报表控制字段数据
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建报表控制字段成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建报表控制字段失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 获取控制字段的信息
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getClientControlledColumn($meeting_id){
			$result = $this->where([
				'mid'    => $meeting_id,
				'action' => self::ACTION_READ,
				'type'   => self::TYPE_CLIENT
			])->select();

			return $result;
		}

		/**
		 * 获取可检索的控制字段
		 *
		 * @param int  $meeting_id    会议ID
		 * @param bool $just_selected 只输出被选中的检索字段
		 *
		 * @return array
		 */
		public function getClientSearchColumn($meeting_id, $just_selected = false){
			$option = [];
			if($just_selected) $option['search'] = 1;
			$result = $this->where(array_merge($option, [
				'mid'    => $meeting_id,
				'action' => self::ACTION_SEARCH,
				'type'   => self::TYPE_CLIENT
			]))->select();

			return $result;
		}
	}