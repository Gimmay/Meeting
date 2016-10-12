<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-12
	 * Time: 8:54
	 */
	namespace Core\Model;

	class DiningBoardModel extends CoreModel{
		protected $tableName       = 'dining_board';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}
	}