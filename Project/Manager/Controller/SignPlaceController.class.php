<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 10:13
	 */
	namespace Manager\Controller;

	use Manager\Logic\ClientLogic;
	use Manager\Logic\SignPlaceLogic;
	use Think\Page;

	class SignPlaceController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$this->meetingID = $this->initMeetingID($this);
		}

		public function create(){
			if(IS_POST){
				$logic  = new SignPlaceLogic();
				$result = $logic->handlerRequest('create');
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], U('manage', ['mid' => I('get.mid')]));
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['SIGN_PLACE.CREATE']){            //查处当前会议的名称
				/** @var \Manager\Model\MeetingModel $meeting_model */
				$meeting_model = D('Meeting');
				/** @var \Core\Model\MeetingModel $meeting_object */
				$meeting_object = D('Core/Meeting');
				$meeting        = $meeting_object->findMeeting(1, ['id' => $this->meetingID]);
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model = D('Employee');
				$meeting_list   = $meeting_model->getMeetingForSelect();
				$employee_list  = $employee_model->getEmployeeSelectList();
				$this->assign('meeting', $meeting);
				$this->assign('meeting_list', $meeting_list);
				$this->assign('employee_list', $employee_list);
				$this->display();
			}
			else $this->error('您没有创建签到点的权限');
		}

		public function manage(){
			$logic = new SignPlaceLogic();
			/** @var \Core\Model\SignPlaceModel $model */
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
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
			if($this->permissionList['SIGN_PLACE.VIEW']){
				$model       = D('Core/SignPlace');
				$list_total  = $model->findRecord(0, ['keyword' => I('get.keyword', ''), 'status' => 'not deleted']);
				$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show   = $page_object->show();
				$record_list = $model->findRecord(2, [
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
					'status'  => 'not deleted',
					'mid'     => $this->meetingID
				]);
				$record_list = $logic->setData('manage:get_extend_column', $record_list);
				$this->assign('page_show', $page_show);
				$this->assign('list', $record_list);
				$this->display();
			}
			else $this->error('您没有查看签到点的权限');
		}

		public function alter(){
			$logic = new SignPlaceLogic();
			if(IS_POST){
				$result = $logic->handlerRequest('alter');
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], U('manage', ['mid' => I('get.mid')]));
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['SIGN_PLACE.ALTER']){
				/** @var \Core\Model\SignPlaceModel $model */
				$model = D('Core/SignPlace');
				/** @var \Core\Model\MeetingModel $result */
				$result = D('Core/Meeting');
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model = D('Employee');
				$meeting        = $result->findMeeting(1, ['id' => $this->meetingID, 'status' => 'not deleted']);
				$employee_list  = $employee_model->getEmployeeSelectList();
				$info           = $model->findRecord(1, ['id' => I('get.id', 0, 'int')]);
				$info           = $logic->setData('alter:get_extend_column', $info);
				$this->assign('employee_list', $employee_list);
				$this->assign('info', $info);
				$this->assign('meeting', $meeting);
				$this->display();
			}
			else $this->error('您没有修改签到点的权限');
		}

		public function clientList(){
			$logic = new SignPlaceLogic();
			/* 处理POST提交请求 */
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['__return__']) $url = $result['__return__'];
					else $url = '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			if($this->permissionList['SIGN_PLACE-CLIENT.VIEW']){
				$client_logic = new ClientLogic();
				/** @var \Manager\Model\SignPlaceModel $sign_place_model */
				$sign_place_model = D('Manager/SignPlace');
				/** @var \Core\Model\CouponItemModel $coupon_item_model */
				$coupon_item_model = D('Core/CouponItem');
				/** @var \Manager\Model\PayMethodModel $pay_method_model */
				$pay_method_model = D('PayMethod');
				/** @var \Manager\Model\ReceivablesTypeModel $receivables_type_model */
				$receivables_type_model = D('ReceivablesType');
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model = D('Employee');
				/** @var \Manager\Model\PosMachineModel $pos_machine_model */
				$pos_machine_model = D('PosMachine');
				/** @var \Core\Model\EmployeeModel $employee_personal_model */
				$employee_personal_model = D('Core/Employee');
				/** @var \Core\Model\JoinSignPlaceModel $model */
				$model   = D('Core/JoinSignPlace');
				$options = [];
				$sid     = I('get.sid', 0, 'int');
				/* 处理URL参数 */
				if(isset($_GET['signed'])) $options['sign_status'] = I('get.signed', 0, 'int') == 1 ? 1 : 'not signed';
				if(isset($_GET['reviewed'])) $options['review_status'] = I('get.reviewed', 0, 'int') == 1 ? 1 : 'not reviewed';
				/* 获取记录总数 */
				$total_list = $model->findRecord(2, array_merge([
					'keyword' => I('get.keyword', ''),
					'status'  => 'not deleted',
					'mid'     => $this->meetingID,
					'sid'     => $sid
				], $options));
				/* 特殊处理收款列表和统计 */
				if(isset($_GET['receivables'])) $total_list = $client_logic->getReceivablesList($total_list, I('get.receivables', 1, 'int'));
				/* 分页设置 */
				$page_object = new Page(count($total_list), I('get._page_count', C('PAGE_RECORD_COUNT'), 'int'));
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$page_show = $page_object->show();
				/* 当前页记录 */
				$client_list = $model->findRecord(2, array_merge([
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => I('get._column', 'main.creatime').' '.I('get._sort', 'desc'),
					'status'  => 'not deleted',
					'mid'     => $this->meetingID,
					'sid'     => $sid
				], $options));
				/* 统计数据 */
				$signed_count   = $model->findRecord(0, [
					'sign_status' => 1,
					'status'      => 'not deleted',
					'sid'         => $sid,
					'mid'         => $this->meetingID
				]);
				$reviewed_count = $model->findRecord(0, [
					'review_status' => 1,
					'status'        => 'not deleted',
					'sid'           => $sid,
					'mid'           => $this->meetingID
				]);
				$all_count      = $model->findRecord(0, [
					'status' => 'not deleted',
					'sid'    => $sid,
					'mid'    => $this->meetingID
				]);
				/* 特殊处理收款列表和统计 */
				$receivables_count = $not_receivables_count = 0;
				if(isset($_GET['receivables'])){
					/* 获取记录总数 */
					$temp_total_list  = $model->findRecord(2, [
						'keyword' => I('get.keyword', ''),
						'status'  => 'not deleted',
						'sid'     => $sid,
						'mid'     => $this->meetingID
					]);
					$temp_client_list = $client_logic->getReceivablesList($temp_total_list, I('get.receivables', 1, 'int'));
					if(I('get.receivables') == 1){
						$receivables_count     = count($temp_client_list);
						$not_receivables_count = ($all_count-count($temp_client_list));
					}
					if(I('get.receivables') == 0){
						$not_receivables_count = count($temp_client_list);
						$receivables_count     = ($all_count-count($temp_client_list));
					}
					$client_list = $client_logic->getReceivablesList($client_list, I('get.receivables', 1, 'int'));
				}
				else{
					/* 获取记录总数 */
					$temp_total_list       = $model->findRecord(2, [
						'keyword' => I('get.keyword', ''),
						'status'  => 'not deleted',
						'sid'     => $sid,
						'mid'     => $this->meetingID
					]);
					$receivables_count     = count($client_logic->getReceivablesList($temp_total_list, 1));
					$not_receivables_count = count($client_logic->getReceivablesList($temp_total_list, 0));
					$client_list           = $client_logic->getReceivablesList($client_list, 1, false);
				}
				//支付类型
				$pay_method_list = $pay_method_model->getPayMethodSelectList();
				//收款类型
				$receivables_type_list = $receivables_type_model->getReceivablesTypeSelectList();
				//POS机
				$pos_machine_list = $pos_machine_model->getPosMachineSelectList();
				//当前收款人
				$employee_personal_result = $employee_personal_model->findEmployee(1, ['id' => I('session.MANAGER_EMPLOYEE_ID', 0, 'int')]);
				/* 会议对应的券记录 */
				$coupon_item_result = $coupon_item_model->findCouponItem(2, ['mid' => $this->meetingID, 'status' => 0]);
				/* 员工列表(for select component) */
				$employee_list = $employee_model->getEmployeeSelectList();
				/* 获取签到点列表(for select component) */
				$sign_place_list = $sign_place_model->getRecordSelectList($this->meetingID);
				/* 向视图输出数据 */
				$this->assign('sign_place_list', $sign_place_list);
				$this->assign('statistics', [
					'signed'          => $signed_count,
					'not_signed'      => $all_count-$signed_count,
					'reviewed'        => $reviewed_count,
					'not_reviewed'    => $all_count-$reviewed_count,
					'total'           => $all_count,
					'receivables'     => $receivables_count,
					'not_receivables' => $not_receivables_count
				]);
				$this->assign('employee_info', $employee_personal_result);
				$this->assign('pay_method_list', $pay_method_list);
				$this->assign('pos_machine_list', $pos_machine_list);
				$this->assign('receivables_type_list', $receivables_type_list);
				$this->assign('employee_list', $employee_list);
				$this->assign('coupon_code_list', $coupon_item_result);
				$this->assign('list', $client_list);
				$this->assign('page_show', $page_show);
				$this->display();
			}
			else $this->error('您没有查看签到点下参会人员的权限');
		}
	}