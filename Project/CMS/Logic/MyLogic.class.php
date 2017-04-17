<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-6
	 * Time: 15:52
	 */
	namespace CMS\Logic;

	use General\Logic\Time;
	use General\Logic\UserLogic;
	use General\Model\SystemLogModel;
	use Quasar\Utility\IP;
	use Quasar\Utility\StringPlus;

	class MyLogic extends CMSLogic{
		/**
		 * 处理POST/GET请求
		 *
		 * @param string $type 请求类型
		 * @param array  $opt  相关参数
		 *
		 * @return mixed
		 */
		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'modify_password':
					$old_password    = base64_decode(I('post.old_password', ''));
					$new_password    = base64_decode(I('post.new_password', ''));
					$current_user_id = Session::getCurrentUser();
					$password_hint   = I('post.password_hint', '');
					$user_logic      = new UserLogic();
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					if($user_model->fetch(['id' => $current_user_id])){
						$user_information = $user_model->getObject();
						if($user_logic->verifyPassword($old_password, $user_information['name'], $user_information['password'])){
							$result = $user_model->modifyPassword(['id' => $current_user_id], $new_password);
							$user_model->modifyInformation(['id' => $current_user_id], ['password_hint' => $password_hint]);
							if($result['status'] == true){
								$ip_obj = new IP();
								// 保存日志
								$data = [
									'operator' => $current_user_id,
									'object'   => $current_user_id,
									'action'   => 'MODIFY_PASSWORD_BY_SELF',
									'creator'  => $current_user_id,
									'creatime' => Time::getCurrentTime(),
									'ip'       => $ip_obj->getClientIP()
								];
								/** @var \General\Model\SystemLogModel $system_log_model */
								$system_log_model = D('General/SystemLog');
								$system_log_model->create($data);
								// 清除Session
								Session::cleanAll();
							}

							return array_merge(['__ajax__' => true], $result);
						}
						else{
							return ['status' => false, 'message' => '原密码错误', '__ajax__' => true];
						}
					}
					else{
						return ['status' => false, 'message' => '找不到该用户', '__ajax__' => true];
					}
				break;
				case 'modify_information':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$str_obj    = new StringPlus();
					$post       = I('post.');
					$data       = [
						'nickname'        => $post['nickname'],
						'nickname_pinyin' => $str_obj->getPinyin($post['nickname'], true, ''),
						'comment'         => $post['comment']
					];
					$result     = $user_model->modifyInformation(['id' => Session::getCurrentUser()], $data);

					return array_merge($result, ['__ajax__' => true]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true];
				break;
			}
		}

		/**
		 * 设定额外数据
		 *
		 * @param string $type 操作类型
		 * @param mixed  $data 处理数据
		 *
		 * @return mixed
		 */
		public function setData($type, $data){
			switch($type){
				case 'modify_password_log_list':
					foreach($data as $key => $val){
						$data[$key]['action_code'] = $val['action'];
						$data[$key]['action']      = SystemLogModel::ACTION[$val['action']];
					}

					return $data;
				break;
				case 'last_modify_password_time':
					foreach($data as $val){
						if($val['action_code'] == 'MODIFY_PASSWORD_BY_SELF') return $val['creatime'];
					}

					return '';
				break;
				default:
					return $data;
				break;
			}
		}
	}