<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-8
	 * Time: 17:25
	 */
	namespace RoyalwissD\Controller;

	use CMS\Controller\CMS;

	class RoyalwissD extends CMS{
		protected $meetingID = 0;

		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 初始化会议ID
		 */
		protected function initMeetingID(){
			$meeting_id = I('get.mid', 0, 'int');
			if($meeting_id === 0){
				echo "<h1>缺少会议参数！</h1>";
				exit;
			}
			else{
				$this->meetingID = $meeting_id;
			}
		}

	}