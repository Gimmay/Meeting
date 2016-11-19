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
			$this->meetingID = $this->initMeetingID($this);
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
			/** @var \Core\Model\HotelModel $model */
			$model = D('Core/Hotel');
			$logic = new HotelLogic();
			$list  = $model->findHotel(2, ['status' => 'not deleted']);
			$list  = $logic->setData('manage:set_meeting_column', $list, ['meetingType' => I('get.type', '')]);
			$this->assign('list', $list);
			$this->display();
		}
	}