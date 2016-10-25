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
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model = D('Employee');
				$employee_list  = $employee_model->getEmployeeSelectList();
				$this->assign('employee_list', $employee_list);
				$this->display();
			}else $this->error('您没有创建会议的权限');
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
				$model       = D('Core/Meeting'); // 实例化表模型
				$list_total  = $model->findMeeting(0, [
					'keyword' => I('get.keyword', ''),
					'status'  => 'not deleted'
				]); // 查处所有的会议的个数
				$page_object = new Page($list_total, C('PAGE_RECORD_COUNT')); // 实例化分页类 传入总记录数和每页显示的记录数
				\ThinkPHP\Quasar\Page\setTheme1($page_object);
				$show         = $page_object->show();// 分页显示输出
				$meeting_list = $model->findMeeting(2, [
					'keyword' => I('get.keyword', ''),
					'_limit'  => $page_object->firstRow.','.$page_object->listRows,
					'_order'  => I('get._column', 'creatime').' '.I('get._sort', 'desc'),
					'status'  => 'not deleted'
				]); // 查出一页会议的内容
				$meeting_list = $meeting_logic->setExtendColumnForManage($meeting_list);
				$this->assign('content', $meeting_list); // 赋值数据集
				$this->assign('page', $show); // 赋值分页输出
				$this->assign('message', $message);
				$this->display();
			}else $this->error('您没有查看会议的权限');
		}

		public function alter(){
			if($this->permissionList['MEETING.ALTER']){
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
				$info  = $model->findMeeting(1, ['id' => I('get.id', 0, 'int'), 'status' => 'not deleted']);
				$info  = $setEmployee($info);
				if(IS_POST){
					$type   = strtolower(I('post.requestType', ''));
					$result = $meeting_logic->handlerRequest($type, ['info' => $info]);
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
				/** @var \Manager\Model\EmployeeModel $employee_model */
				$employee_model = D('Employee');
				$employee_list  = $employee_model->getEmployeeSelectList();
				$this->assign('info', $info);
				$this->assign('employee_list', $employee_list);
				$this->display();
			}else $this->error('您没有修改会议的权限');
		}
	}