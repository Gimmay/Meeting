<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-8
	 * Time: 9:49
	 */
	namespace CMS\Controller;

	use CMS\Logic\MyLogic;
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
			$my_logic = new MyLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $my_logic->handlerRequest($type);
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
			// 获取用户名
			/** @var \General\Model\UserModel $user_model */
			$user_model = D('General/User');
			if(!$user_model->fetch(['id' => Session::getCurrentUser()])) $this->error('找不到用户');
			$user = $user_model->getObject();
			$this->assign('user_name', $user['name']);
			$this->assign('password_hint', $user['password_hint']);
			// 获取操作日志
			/** @var \CMS\Model\SystemLogModel $system_log_model */
			$system_log_model = D('CMS/SystemLog');
			$log_list         = $system_log_model->getList([
				$system_log_model::CONTROL_COLUMN_PARAMETER_SELF['operator'] => ['=', Session::getCurrentUser()],
				$system_log_model::CONTROL_COLUMN_PARAMETER_SELF['object']   => ['=', Session::getCurrentUser()],
				$system_log_model::CONTROL_COLUMN_PARAMETER_SELF['action']   => [
					'in',
					"('MODIFY_PASSWORD', 'MODIFY_PASSWORD_BY_SELF', 'RESET_PASSWORD')"
				],
				$system_log_model::CONTROL_COLUMN_PARAMETER['order']         => ' creatime desc '
			]);
			$log_list         = $my_logic->setData('modify_password_log_list', $log_list);
			$last_modify_time         = $my_logic->setData('last_modify_password_time', $log_list);
			$this->assign('last_modify_time', $last_modify_time);
			$this->assign('log_list', $log_list);
			$this->display();
		}

		public function modifyPassword2(){
			$my_logic = new MyLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $my_logic->handlerRequest($type);
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
			$this->display();
		}

		/**
		 *  个人信息
		 */
		public function information(){
			$my_logic = new MyLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $my_logic->handlerRequest($type);
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
			$user_id = Session::getCurrentUser();
			/** @var \General\Model\UserModel $user_model */
			$user_model = D('General/User');
			$user_model->fetch(['id' => $user_id]);
			$this->assign('user', $user_model->getObject());
			$this->display();
		}
	}