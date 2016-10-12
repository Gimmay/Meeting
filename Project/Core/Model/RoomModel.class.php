<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-12
	 * Time: 8:53
	 */
	namespace Core\Model;

	class RoomModel extends CoreModel{
		protected $tableName       = 'room';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}
	}