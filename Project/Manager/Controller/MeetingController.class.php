<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:55
	 */
	namespace Manager\Controller;

	use Manager\Model\ManagerModel;
	use Think\Page;

	class MeetingController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function create(){
			if(IS_POST){
				$model  = D('Meeting');
				$result = $model->createMeeting();
				if($result['status']) $this->success($result['message']);
				else $this->error($result['message'], '', 3);
				exit;
			}
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
			$this->assign('employee_list', $employee_list);
			$this->display();
		}

		public function edit(){
			if(IS_POST){
				$model  = D('Meeting');
				$result = $model->saveMeeting();
				if($result['status']) $this->success($result['message']);
				else $this->error($result['message'], '', 3);
				exit;
			}
		}

		public function addMeeting(){
			if(IS_POST){
				/** @var \Core\Model\MeetingModel $model */
				$model  = D('Core/Meeting'); //实例化表
				$data   = I('post.', '');         //获取表单数据
				$result = $model->createMeeting($data); //用model 创建表插入数据
				$this->success('创建成功',U('manage'));
				exit;
			}
		}

		public function deleteMeeting(){
			if(IS_POST){
				$id   = I('post.id'); //post到id
				$news = D('Core/Meeting'); //实例化表
				$del  = $news->where('id='.$id)->delete($id); //查出对应id 删除
				$this->success('删除成功');
				exit;
			}
		}

		public function manage(){
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			$setDirector    = function ($list) use ($employee_model){
				foreach($list as $key => $val){
					$tmp                         = $employee_model->findEmployee(1, ['id' => $val['director_id']]);
					$list[$key]['director_name'] = $tmp['name'];
				}

				return $list;
			};
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
				'_order'   => I('get.column', 'creatime').' '.I('get.sort', 'desc'),
				'status'  => 'not deleted'
			]); // 查出一页会议的内容
			$meeting_list = $setDirector($meeting_list);
			$this->assign('content', $meeting_list); // 赋值数据集
			$this->assign('page', $show); // 赋值分页输出
			$this->display();
		}

		public function alter(){
			/** @var \Core\Model\MeetingModel $model */
			$model = D('Core/Meeting');
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
			if(IS_POST){
				$result = $model->alterMeeting(I('get.id', 0, 'int'), I('post.')); //传值到model里面操作
				if($result['status']) $this->success('写入成功',U('manage')); //判断status存在
				else $this->error($result['message']);			  //判断status不存在
				exit;
			}

			$info = $model->findMeeting(1, ['id' => I('get.id', 0, 'int'), 'status' => 'not deleted']);
			$info = $setEmployee($info);
			$this->assign('info', $info);

			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
			$this->assign('employee_list', $employee_list);
			$this->display();
		}
	}