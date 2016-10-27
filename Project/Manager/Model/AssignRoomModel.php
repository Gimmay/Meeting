<?php
	/**
	 * Created by PhpStorm.
	 * User: 1350
	 * Date: 2016-10-26
	 * Time: 15:31
	 */
	namespace Manager\Model;

	class AssignRoomModel extends ManagerModel{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 获取指定会议的参会人员并以type参数指定是未入住还是已入住
		 *
		 * @param  int $mid 会议ID
		 * @param int $type 类型 0:未入住 1:已入住
		 */
		public function getClient($mid, $type){

		}
	}