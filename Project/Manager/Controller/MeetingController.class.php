<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:55
	 */
	namespace Manager\Controller;

	use Manager\Logic\MeetingLogic;
	use Think\Page;

	class MeetingController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function create(){
			if(IS_POST){
				$logic         = new MeetingLogic();
				$post          = I('post.');
				$post['brief'] = $_POST['brief'];
				$result        = $logic->create($post);
				if($result['status']) $this->success($result['message'], U('manage'));
				else $this->error($result['message'], '', 3);
				exit;
			}
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
			$this->assign('employee_list', $employee_list);
			$this->display();
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
			/** @var \Core\Model\MeetingModel $model */
			$model       = D('Core/Meeting'); // 实例化表模型
			$list_total  = $model->findMeeting(0, [
				'keyword' => I('get.keyword', ''),
				'status'  => 'not deleted'
			]); // 查处所有的会议的个数
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT')); // 实例化分页类 传入总记录数和每页显示的记录数(10)
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$show         = $page_object->show();// 分页显示输出
			$meeting_list = $model->findMeeting(2, [
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 'not deleted'
			]); // 查出一页会议的内容
			$meeting_list = $meeting_logic->setExtendColumnForManage($meeting_list);
			$this->assign('content', $meeting_list); // 赋值数据集
			$this->assign('page', $show); // 赋值分页输出
			$this->display();
		}

		public function alter(){
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$setEmployee    = function ($data) use ($employee_model){
				$tmp                     = $employee_model->findEmployee(1, ['id' => $data['director_id']]);
				$tmp_one                 = $employee_model->findEmployee(1, ['id' => $data['contacts_1_id']]);
				$tmp_two                 = $employee_model->findEmployee(1, ['id' => $data['contacts_2_id']]);
				$data['director_name']   = $tmp['name'];
				$data['contacts_1_name'] = $tmp_one['name'];
				$data['contacts_2_name'] = $tmp_two['name'];

				return $data;
			};
			/** @var \Core\Model\MeetingModel $model */
			$model = D('Core/Meeting');
			$info  = $model->findMeeting(1, ['id' => I('get.id', 0, 'int'), 'status' => 'not deleted']);
			$info  = $setEmployee($info);
			if(IS_POST){
				$logic         = new MeetingLogic();
				$post          = I('post.');
				$post['brief'] = $_POST['brief'];
				$result        = $logic->alter(I('get.id', 0, 'int'), $post, $info);
				if($result['status']) $this->success($result['message'], U('manage')); // 判断status存在
				else $this->error($result['message']); // 判断status不存在
				exit;
			}
			$this->assign('info', $info);
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
			$this->assign('employee_list', $employee_list);
			$this->display();
		}
	}