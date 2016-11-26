<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:55
	 */
	namespace Manager\Controller;

	use Manager\Logic\MeetingLogic;
	use Manager\Logic\MessageLogic;
	use Think\Page;

	class MeetingController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
			$core_logic = new \Core\Logic\MeetingLogic();
			$core_logic->initializeStatus();
		}

		public function index(){
			if($this->permissionList['MEETING.VIEW']){
				$this->meetingID = $this->initMeetingID($this);
				/** @var \Core\Model\JoinModel $join_model */
				$join_model = D('Core/Join');
				/** @var \Core\Model\ReceivablesTypeModel $receivables_model */
				$receivables_model = D('Core/Receivables');
				// 获取参会统计数据
				$join_record = $join_model->findRecord(2, [
					'mid'    => $this->meetingID,
					'status' => 1
				]);
				// 获取签到统计数据
				$sign_count = $join_model->findRecord(0, [
					'mid'         => $this->meetingID,
					'status'      => 1,
					'sign_status' => 1
				]);
				// 获取收款统计数据
				$receivables_record       = $receivables_model->findRecord(2, [
					'mid'    => $this->meetingID,
					'status' => 'not deleted'
				]);
				$cid_id_arr               = [];
				$receivables_client_count = $price = 0;
				foreach($receivables_record as $val){
					$cid_id_arr[] = $val['cid'];
					$price        += $val['price'];
				}
				foreach($join_record as $val){
					if(in_array($val['cid'], $cid_id_arr)) $receivables_client_count++;
				}
				$this->assign('statistics', [
					'joined'             => count($join_record),
					'signed'             => $sign_count,
					'receivables'        => count($receivables_record),
					'receivables_client' => $receivables_client_count,
					'receivables_price'  => $price
				]);
				$this->display();
			}
			else $this->error('您没有查看会议详情的权限');
		}

		public function create(){
			if($this->permissionList['MEETING.CREATE']){
				$meeting_logic = new MeetingLogic();
				if(IS_POST){
					$type   = strtolower(I('post.requestType', ''));
					$result = $meeting_logic->handlerRequest($type);
					if($result['__ajax__']){
						unset($result['__ajax__']);
						echo json_encode($result);
					}
					else{
						unset($result['__ajax__']);
						if($result['status']) $this->success($result['message'], U('manage'));
						else $this->error($result['message'], '', 3);
					}
					exit;
				}
				/** @var \Core\Model\HotelModel $hotel_model */
				$hotel_model  = D('Core/Hotel');
				$hotel_result = $hotel_model->findHotel(2, [
					'status' => 'not deleted'
				]);
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model = D('Employee');
				$employee_list  = $employee_model->getEmployeeSelectList();
				$this->assign('employee_list', $employee_list);
				$this->assign('info', $hotel_result);
				$this->display();
			}
			else $this->error('您没有创建会议的权限');
		}

		public function manage(){
			$meeting_logic = new MeetingLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $meeting_logic->handlerRequest($type);
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
			if($this->permissionList['MEETING.VIEW']){
				/** @var \Manager\Model\MessageModel $message_logic */
				$message_logic = D('Message');
				$message       = $message_logic->getMessageSelectList();
				/** @var \Core\Model\MeetingModel $model */
				$model      = D('Core/Meeting'); // 实例化表模型
				$list_total = $model->findMeeting(0, [
					'keyword' => I('get.keyword', ''),
					'status'  => 'not deleted'
				]); // 查处所有的会议的个数
				$option     = [];
				switch(I('get.type', '')){
					case 'ing':
						$option['status'] = 'ing';
					break;
					case 'fin':
						$option['status'] = 4;
					break;
				}
				$page_object = new Page($list_total, C('PAGE_RECORD_COUNT')); // 实例化分页类 传入总记录数和每页显示的记录数
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$show         = $page_object->show();// 分页显示输出
				$meeting_list = $model->findMeeting(2, array_merge([
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
					'status'  => 'not deleted'
				], $option)); // 查出一页会议的内容
				$meeting_list = $meeting_logic->setExtendColumnForManage($meeting_list);
				/** @var \Core\Model\HotelModel $hotel_model */
				$hotel_model  = D('Core/Hotel');
				$hotel_result = $hotel_model->findHotel(2, ['status' => 'not deleted']);
				$this->assign('info', $hotel_result);
				$this->assign('content', $meeting_list); // 赋值数据集
				$this->assign('page', $show); // 赋值分页输出
				$this->assign('message', $message);
				$this->display();
			}
			else $this->error('您没有查看会议的权限', U('My/information'));
		}

		public function alter(){
			if($this->permissionList['MEETING.VIEW']){
				$setEmployee   = function ($data){
					/** @var \Core\Model\EmployeeModel $employee_model */
					$employee_model          = D('Core/Employee');
					$tmp                     = $employee_model->findEmployee(1, ['id' => $data['director_id']]);
					$tmp_one                 = $employee_model->findEmployee(1, ['id' => $data['contacts_1_id']]);
					$tmp_two                 = $employee_model->findEmployee(1, ['id' => $data['contacts_2_id']]);
					$data['director_name']   = $tmp['name'];
					$data['contacts_1_name'] = $tmp_one['name'];
					$data['contacts_2_name'] = $tmp_two['name'];

					return $data;
				};
				$meeting_logic = new MeetingLogic();
				/** @var \Core\Model\MeetingModel $model */
				$model = D('Core/Meeting');
				if(IS_POST){
					$type   = strtolower(I('post.requestType', ''));
					$result = $meeting_logic->handlerRequest($type);
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
				$mid  = I('get.mid', 0, 'int');
				$info = $model->findMeeting(1, ['id' => $mid, 'status' => 'not deleted']);
				$info = $setEmployee($info);
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model = D('Employee');
				$employee_list  = $employee_model->getEmployeeSelectList();
				$this->assign('info', $info);
				$this->assign('employee_list', $employee_list);
				$this->display();
			}
			else $this->error('您没有查看会议的权限');
		}
	}