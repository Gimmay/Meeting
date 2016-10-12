<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-12
	 * Time: 8:51
	 */
	namespace Core\Model;

	class CarModel extends CoreModel{
		protected $tableName       = 'car';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}
	}