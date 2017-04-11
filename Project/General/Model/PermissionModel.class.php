<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-4
	 * Time: 9:38
	 */
	namespace General\Model;

	class PermissionModel extends GeneralModel{
		protected $tableName = 'permission';
		const TABLE_NAME = 'permission';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';

		public function _initialize(){
			parent::_initialize();
		}
	}