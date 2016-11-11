<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-26
	 * Time: 9:56
	 */
	namespace Mobile\Controller;

	use Core\Logic\MeetingLogic;
	use Mobile\Logic\ManagerLogic;
	use Mobile\Logic\ReceivablesLogic;

	class ReceivablesController extends MobileController{
		protected $weixinID   = 0;
		protected $employeeID = 0;
		protected $meetingID  = 0;

		public function _initialize(){
			parent::_initialize();
		}

		public function MeetingList(){
			$this->weixinID = $this->getWeixinID();
			$this->_getEmployeeID();
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			$logic         = new ManagerLogic();
			$meeting_list  = $meeting_model->findMeeting(2, ['status' => 'not deleted']);
			$meeting_list  = $logic->setData('meetingList:set_meeting_list', $meeting_list);
			$this->assign('meeting_list', $meeting_list);
			$this->display();
		}

		public function AddReceivables(){
			$receivables_logic = new ReceivablesLogic();
			if(I('post.')){
				$type   = strtolower(I('post.requestType', ''));
				$result = $receivables_logic->handlerRequest($type);
			}
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			$coupon_item_model = D('Core/CouponItem');
			/** @var \Core\Model\ClientModel $client_model */
			$client_model =D('Core/Client');
			/** @var \Core\Model\PayMethodModel $pay_method_model */
			$pay_method_model = D('Core/PayMethod');
			/** @var \Core\Model\ReceivablesTypeModel $receivables_type_model */
			$receivables_type_model = D('Core/ReceivablesType');
			$coupon_item_result = $coupon_item_model->findCouponItem(2,['mid'=>I('get.mid',0,'int'),'status'=>'not deleted']);
			$client_result = $client_model->findClient(1,['id'=>I('get.cid',0,'int')]);
			$pay_method_result = $pay_method_model->findPayMethod(2,['status'=>'not deleted']);
			$receivables_type_result = $receivables_type_model->findReceivablesType(2,['status'=>'not deleted']);
			$this->assign('client',$client_result);
			$this->assign('coupon_item',$coupon_item_result);
			$this->assign('method',$pay_method_result);
			$this->assign('receivables_type',$receivables_type_result);

			$this->display();
		}

		public function Client(){
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model  = D('Core/Receivables');
			$cid                = I('get.cid', 0, 'int');
			$mid                = I('get.mid', 0, 'int');
			$join_result        = $join_model->findRecord(1, ['cid' => $cid, 'mid' => $mid]);
			$receivables_result = $receivables_model->findRecord(2, ['cid' => $cid, 'mid' => $mid]);
			$this->assign('info', $join_result);
			$this->assign('list', $receivables_result);
			$this->display();
		}

		public function ClientList(){
			$receivables_logic = new ReceivablesLogic();
			/** @var \Core\Model\JoinModel $join_model */
			$join_model  = D('Core/Join');
			$join_result = $join_model->findRecord(2, [
				'mid'     => I('get.mid', 0, 'int'),
				'status'  => 'not deleted',
				'keyword' => I('get.keyword', '')
			]);
			if(isset($_GET['receivables'])){
				if(I('get.receivables', 0, 'int') == 1) $type = 1;
				else $type = 0;
			}
			else $type = 2;
			$join_receivables_type = $receivables_logic->SelectReceivablesType($join_result);
			switch($type){
				case 1:
					$this->assign('list', $join_receivables_type['receivables']);
				break;
				case 2:
					$list = array_merge($join_receivables_type['receivables'], $join_receivables_type['notReceivables']);
					$this->assign('list', $list);
				break;
				case 0:
				default:
					$this->assign('list', $join_receivables_type['notReceivables']);
				break;
			}
			$this->display();
		}

		public function Meeting(){
			/** @var \Core\Model\MeetingModel $meeting_model */
			$meeting_model = D('Core/Meeting');
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			/** @var \Mobile\Model\ReceivablesModel $receivables_model */
			$receivables_model        = D('Receivables');
			$meeting_result           = $meeting_model->findMeeting(1, ['id' => I('get.mid', 0, 'int')]);
			$join_result              = $join_model->findRecord(0, [
				'mid'    => I('get.mid', 0, 'int'),
				'status' => 'not deleted'
			]);
			$receivables_result_count = $receivables_model->findReceivablesCount(I('get.mid', 0, 'int'));
			$this->assign('meeting', $meeting_result);
			$this->assign('join_count', $join_result);
			$this->assign('count', $receivables_result_count);
			$this->display();
		}

		public function ReceivablesDetails(){
			$receivables_logic = new ReceivablesLogic();
			/** @var \Core\Model\ReceivablesModel $receivables_model */
			$receivables_model  = D('Core/Receivables');
			$receivables_result = $receivables_model->findRecord(1, ['id' => I('get.id', 0, 'int')]);
			$receivables_info   = $receivables_logic->SelectReceivablesMethod($receivables_result);
			$this->assign('list', $receivables_info);
			$this->display();
		}

		public function ReceivablesRecord(){
			$this->display();
		}

		private function _getEmployeeID($redirect = true){
			$get              = function (){
				$logic = new ManagerLogic();

				return $logic->getVisitorID($this->weixinID);
			};
			$this->employeeID = isset($_SESSION['MOBILE_EMPLOYEE_ID']) ? I('session.MOBILE_EMPLOYEE_ID', 0, 'int') : $get();
			if($this->employeeID == 0){
				if($redirect) $this->redirect('Error/isNotRegister');

				return false;
			}
			else return true;
		}
	}