<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 14:54
	 */
	namespace General\Model;
	

	class SystemLogModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'system_log';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';

		/**
		 *  记录日志
		 *
		 * @param array $data 日志数据
		 *
		 * @return int 插入的弟N条记录
		 */
		public function create($data){
			$result = $this->add($data);
			return $result;
		}

		/**
		 * @param $filter
		 *  查询修改密码日志
		 * @return mixed
		 */
		public function findRecord($filter){
			$result = $this->field('t2.name,system_log.creatime,system_log.action,system_log.remark')->join('user t2 ON t2.id = system_log.operator_id')->where($filter)->limit(3)->order('system_log.id desc')->select();
			return $result;
		}

	}