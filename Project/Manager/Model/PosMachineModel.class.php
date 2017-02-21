<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-18
	 * Time: 17:21
	 */
	namespace Manager\Model;

	class PosMachineModel extends ManagerModel{
		protected $tableName       = 'pos_machine';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function getPosMachineSelectList($mid){
			return $this->where(['status' => 1, 'mid'=>$mid])->field('CONCAT(`name`) `html`, CONCAT(pinyin_code, \',\', `name`) `keyword`, `id` `value`')->select();
		}
	}