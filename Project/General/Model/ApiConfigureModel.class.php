<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-31
	 * Time: 12:01
	 */

	namespace General\Model;

	use Exception;

	class ApiConfigureModel extends GeneralModel{
		protected $tableName = 'api_configure';
		const TABLE_NAME = 'api_configure';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';

		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 创建接口配置信息
		 *
		 * @param array $data 接口配置信息
		 *
		 * @return array 执行结果
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建接口配置成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建接口配置失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 获取Select插件的数据列表
		 *
		 * @param int $meeting_type 会议类型
		 *
		 * @return array
		 */
		public function getSelectedList($meeting_type = null){
			$option = $meeting_type == null ? [] : ['mtype' => $meeting_type];

			return $this->where(array_merge($option, [
				'status' => 1
			]))->field("id value, name html, concat(name,',',name_pinyin)")->select();
		}
	}