<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:55
	 */
	namespace Manager\Controller;

	class MeetingController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function create(){
			if(IS_POST){
				$model = D('Meeting');
				$result = $model->createMeeting();
				if($result['status']) $this->success($result['message']);
				else $this->error($result['message'], '', 3);
				exit;
			}
			$this->display();
		}

		public function edit(){
			if(IS_POST){
				$model = D('Meeting');
				$result = $model->saveMeeting();
				if($result['status']) $this->success($result['message']);
				else $this->error($result['message'], '', 3);
				exit;
			}
		}
	}