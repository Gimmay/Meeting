<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-2
	 * Time: 15:23
	 */
	namespace General\Model;

	use CMS\Logic\Session;
	use Exception;

	class SystemConfigModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $connection       = 'DB_CONFIG_COMMON';
		protected $tableName        = 'system_configure'; const TABLE_NAME = 'system_configure';
		protected $autoCheckFields  = true;
		
	}