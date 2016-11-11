<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 10:13
	 */
	namespace Manager\Controller;

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
			//查处当前会议的名称
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
	}