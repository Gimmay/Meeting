<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-12
	 * Time: 11:11
	 */
	namespace Manager\Model;

	class SignPlaceModel extends ManagerModel{
		protected $tableName       = 'sign_place';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function getRecordSelectList($mid){
			return $this->where(['mid'=>$mid])->field('name html, id value, name keyword')->select();
		}
	}