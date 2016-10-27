<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-19
	 * Time: 10:45
	 */
	namespace Manager\Model;

	class HotelModel extends ManagerModel{
		protected $tableName       = 'hotel';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function findHotel(){
			
		}

	}