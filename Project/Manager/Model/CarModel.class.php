<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-27
	 * Time: 17:19
	 */
	namespace Manager\Model;

	class CarModel extends ManagerModel{
		protected $tableName = 'car';
		protected $tablePrefix = 'workflow_';
		protected $autoCheckFields = true;
		public function _initialize(){
			parent::_initialize();
		}

		public function getTypeSelectList(){
			return $this->field('distinct type html, type value, type keyword')->select();
		}
	}