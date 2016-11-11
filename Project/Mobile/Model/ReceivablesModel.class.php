<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:35
	 */
	namespace Mobile\Model;

	class ReceivablesModel extends MobileModel{
		protected $tableName   = 'receivables';
		protected $tablePrefix = 'workflow_';
		protected $autoCheckFields = true;
		public function _initialize(){
			parent::_initialize();
		}

		public function findReceivablesCount($mid){
			$count = $this->where(['mid'=>$mid,'status'=>1])->field('distinct cid')->select();
			return(count($count));
		}
	}