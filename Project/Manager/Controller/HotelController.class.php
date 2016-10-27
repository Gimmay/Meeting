<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-27
	 * Time: 10:33
	 */
	namespace Manager\Controller;

	use Manager\Logic\HotelLogic;

	class HotelController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$hotel_logic = new HotelLogic();
			if(IS_POST){
				$type   = I('post.requestType');
				$result = $hotel_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$find_room = $hotel_logic->findHotel();
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			$meeting_list  = $meeting_model->getMeetingForSelect();
			$this->assign('info', $find_room);
			$this->assign('meeting', $meeting_list);
			$this->display();
		}
	}