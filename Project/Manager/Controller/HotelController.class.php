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
			if($this->permissionList['HOTEL.VIEW']){
				/** @var \Core\Model\HotelModel $model */
				$model = D('Core/Hotel');
				$logic = new HotelLogic();
				//			/** @var \Core\Model\MeetingModel $meeting_model */
				//			$meeting_model = D('Core/Meeting');
				$list = $model->findHotel(2, [
					'status'  => 'not deleted',
					'keyword' => I('get.keyword', ''),
					'mid'     => I('get.mid', 0, 'int')
				]);
				//			/** @var \Core\Model\MeetingModel $meeting_model */
				//			$meeting_model = D('Core/Meeting');
				//			/** @var \Core\Model\HotelModel $hotel_model */
				//			$hotel_model    = D('Core/Hotel');
				//			$meeting_result = $meeting_model->findMeeting(1, [
				//				'id'     => I('get.mid', 0, 'int'),
				//				'status' => 'not deleted'
				//			]);
				//			$hid            = explode(',', $meeting_result['hid']);
				//			$hotel_result   = [];
				//			foreach($hid as $k => $v){
				//				$hotel_result[] = $hotel_model->findHotel(1, ['id' => $v]);
				//			}
				$list = $logic->setData('manage:set_meeting_column', $list, ['meetingType' => I('get.type', '')]);
				$this->assign('list', $list);
				$this->display();
			}
			else $this->error('您没有查看酒店模块的权限');
		}
	}