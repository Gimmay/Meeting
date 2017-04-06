<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-8
	 * Time: 9:49
	 */
	namespace CMS\Controller;
	use CMS\Logic\UserLogic;
	use CMS\Logic\Session;
	class MyController extends CMS{
		public function _initialize(){
			parent::_initialize();
		}

		public function logout(){
			session_unset();
			session_destroy();
			$this->success('注销成功');
		}

		/**
		 *  修改密码
		 */
		public function modifyPassword(){
			$logic = new UserLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			//查询用户名称及修改密码时间
			$user_id =  Session::getCurrentUser();
			/** @var \General\Model\UserModel $user_model */
			$user_model = D('General/User');
			$user_model->fetch(['id' => $user_id]);
			$user_info = $user_model->getObject();
			$edit_time = $user_info['password_edit_time'];
			if($edit_time == ''){
				$edit_time = '您尚未修改过密码！';
			}else{
				$edit_time = date('Y-m-d H:i:s',$edit_time);
			}
			$end_time = $user_info['password_end_time']; //没有共用配置表  就直接存进数据库了
			$expire = intval(($end_time - $user_info['password_edit_time'])/(3600*24));
			//获取修改密码日志信息
			/** @var \General\Model\SystemLogModel $system_log_model */
			$system_log_model = D('General/SystemLog');
			$filter = [
                 'system_log.object_id' => $user_id,
				 'system_log.action'    => ['like','%PASSWORD%'],
			];
			$log_list = $system_log_model->findRecord($filter);
			$this->assign('username',$user_info['name']);
			$this->assign('edit_time',$edit_time); //密码修改时间
			$this->assign('expire',$expire);
			$this->assign('log_list',$log_list);
			$this->display();
		}

		/**
		 *  个人信息
		 */
		public function information(){
			$logic = new UserLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			$user_id =  Session::getCurrentUser();
			/** @var \General\Model\UserModel $user_model */
			$user_model = D('General/User');
			$user_model->fetch(['id' => $user_id]);
			$this->assign('user', $user_model->getObject());
			$this->display();
		}
	}