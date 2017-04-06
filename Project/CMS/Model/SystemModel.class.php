<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-6
	 * Time: 12:07
	 */
	namespace CMS\Model;

	class SystemModel extends CMSModel{
		public function _initialize(){
			parent::_initialize();
		}

		const ROLE_NAME_SEPARATOR = '&$@#,#@$&';
		protected $tableName       = 'system_configure';
		protected $autoCheckFields = true;
		protected $connection      = "DB_CONFIG_COMMON";

		/**
		 *  获取系统配置项
		 *
		 * @return array
		 */
		public function getConfList(){
			$res = $this->where('status=1')->order('orders asc')->select();
            return $res;
		}
	}