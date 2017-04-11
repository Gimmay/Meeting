<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-4
	 * Time: 17:07
	 */
	namespace General\Model;

	class MeetingTypeModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'meeting_type';
		const TABLE_NAME = 'meeting_type';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_COMMON';
	}