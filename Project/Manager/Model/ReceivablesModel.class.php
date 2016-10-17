<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-17
	 * Time: 11:54
	 */
	namespace Manager\Model;

	class ReceivablesModel extends ManagerModel{
		protected $tableName       = 'receivables';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function getReceivablesTypeSelectList(){
			return $this->field('distinct type value, type html, type keyword')->select();
		}
	}