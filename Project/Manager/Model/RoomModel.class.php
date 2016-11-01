<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 10:09
	 */
	namespace Manager\Model;

	class RoomModel extends ManagerModel{
		protected $tableName       = 'room';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function getRoomForSelect(){
			$id = I('get.hid',0,'int');
			return $this->field('code html, id value, type keyword')->where("hid=$id and status!=3 and status!=2")->select();
		}
	}
