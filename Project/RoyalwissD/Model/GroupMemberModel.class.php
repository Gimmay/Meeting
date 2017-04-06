<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-25
	 * Time: 17:28
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class GroupMemberModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'grouping_member';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';

		/**
		 * 创建分组成员
		 *
		 * @param array $data 分组成员信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '分组成功', 'id' => $result] : [
					'status'  => false,
					'message' => '分组失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
	}