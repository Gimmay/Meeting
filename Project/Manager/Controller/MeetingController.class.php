<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:55
	 */
	namespace Manager\Controller;

	class MeetingController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function create(){
			if(IS_POST){
				$model = D('Meeting');
				$result = $model->createMeeting();
				if($result['status']) $this->success($result['message']);
				else $this->error($result['message'], '', 3);
				exit;
			}
			$this->display();
		}

		public function edit(){
			if(IS_POST){
				$model = D('Meeting');
				$result = $model->saveMeeting();
				if($result['status']) $this->success($result['message']);
				else $this->error($result['message'], '', 3);
				exit;
			}
		}

		public function meetingadd(){

			if(IS_POST){
				$model    = D('Core/Meeting'); //实例化表
				$data = I('post.', '');		 //获取表单数据
				$result   = $model->createMeeting($data); //用model 创建表插入数据
				$this->success('创建成功');
				exit;
			}
		}

		public function manage(){
			$data    = D('Core/Meeting'); //实例化表
			$content = $data->select(); 	//查询出数据
			$this->assign ('content',$content); //遍历到模版输出
			$this->display();
		}
	}