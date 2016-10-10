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
		}

		public function create(){
			if(IS_POST){
				$logic  = new SignPlaceLogic();
				$result = $logic->create(I('post.'));
				if($result['status']) $this->success('添加签到点成功', U('manage', ['mid' => I('get.mid')]));
				else $this->error($result['message']);
				exit;
			}
			//查处当前会议的名称
			/** @var \Manager\Model\MeetingModel $meeting_model */
			$meeting_model = D('Meeting');
			/** @var \Core\Model\MeetingModel $meeting_object */
			$meeting_object = D('Core/Meeting');
			$data['id']     = I('get.mid', 0, 'int');
			$meeting_name   = $meeting_object->findMeeting(1, $data);
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$meeting_list   = $meeting_model->getMeetingForSelect();
			$employee_list  = $employee_model->getEmployeeSelectList();
			$this->assign('meeting_name', $meeting_name);
			$this->assign('meeting_list', $meeting_list);
			$this->assign('employee_list', $employee_list);
			$this->display();
		}

		public function manage(){
			$logic = new SignPlaceLogic();
			/** @var \Core\Model\SignPlaceModel $model */
			$model       = D('Core/SignPlace');
			$list_total  = $model->findRecord(0, ['keyword' => I('get.keyword', ''), 'status' => 'not deleted']);
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show   = $page_object->show();
			$record_list = $model->findRecord(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 'not deleted',
				'mid'     => I('get.mid', 0, 'int')
			]);
			if(IS_POST){
				$data       = I('post.id');
				$data_total = $model->deleteSignPlace($data);
				if($data_total['status']) $this->success($data_total['message']);
				else $this->error($data_total['message'], '', 3);
				exit;
			}
			$record_list = $logic->setExtendColumn($record_list);
			$this->assign('page_show', $page_show);
			$this->assign('list', $record_list);
			$this->display();
		}

		public function alter(){
			/** @var \Core\Model\SignPlaceModel $model */
			$logic      = new SignPlaceLogic();
			$model      = D('Core/Sign_place');
			$data['id'] = I('get.sid');
			$list_total = $model->findRecord(1, $data);
			$info       = $logic->setExtendColumnForAlter($list_total);
			if(IS_POST){
				C('TOKEN_ON', false);
				//				print_r($data['id']);exit;
				$sign_alter = $model->alterRecord($data['id'], I('post.'), $info['place']);
				if($sign_alter['status']) $this->success($sign_alter['message'],U('manage',['mid'=>I('get.mid',0,'int')]));
				else $this->error($sign_alter['message']);
				exit;
			}
			/** @var \Core\Model\MeetingModel $result */
			$result         = D('Core/Meeting');
			$data_mid['id'] = I('get.mid', 0, 'int');
			$record_name    = $result->findMeeting(1, $data_mid);
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
			$this->assign('employee_list', $employee_list);
			$this->assign('info', $info);
			$this->assign('list', $list_total);
			$this->assign('record_name', $record_name);
			$this->display();
		}
	}