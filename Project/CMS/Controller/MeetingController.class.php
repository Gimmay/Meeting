<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-2
	 * Time: 15:12
	 */
	namespace CMS\Controller;

	use General\Logic\MeetingLogic;

	class MeetingController extends CMS{
		public function _initialize(){
			parent::_initialize();
		}

		public function type(){
			$meeting_logic      = new MeetingLogic();
			$meeting_type_list  = $meeting_logic->getViewedMeetingTypeList();
			$this->assign('list', $meeting_type_list);
			$this->display();
		}
	}