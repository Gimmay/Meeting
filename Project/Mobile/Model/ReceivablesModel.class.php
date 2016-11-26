<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:35
	 */
	namespace Mobile\Model;

	class ReceivablesModel extends MobileModel{
		protected $tableName       = 'receivables';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function findReceivablesCount($mid){
			$count = $this->alias('main')->where([
				'mid'    => $mid,
				'status' => 1,
				'cid'    => [
					'exp',
					"in (select cid from workflow_join sub where main.mid = sub.mid and sub.status = 1)"
				]
			])->field('distinct cid')->select();

			return (count($count));
		}

		public function getClientReceivables($mid, $eid){
			if($mid>0) $option['mid'] = $mid;
			else $option = [];

			return $this->where(array_merge([
				'status'   => 1,
				'payee_id' => $eid
			], $option))->group('cid')->field('*, sum(price) sum_price')->select();
		}

		public function getClientReceivablesAll($mid){
			if($mid>0) $option['mid'] = $mid;
			else $option = [];
			return $this->where(array_merge([
				'status' => 1
			], $option))->group('cid')->field('*, sum(price) sum_price')->select();
		}
	}