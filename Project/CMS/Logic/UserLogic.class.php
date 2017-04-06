<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 9:18
	 */
	namespace CMS\Logic;

	use CMS\Controller\CMS;
	use CMS\Model\UserModel;
	use General\Logic\Time;
	use General\Logic\UserLogic as GeneralUserLogic;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;

	class UserLogic extends CMSLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'change_password':
					$old_password = base64_decode(I('post.old_password', ''));
					$new_password = base64_decode(I('post.new_password', ''));
					$remark       = I('post.remark', '');
					$user_id      = Session::getCurrentUser();
					$user_logic   = new GeneralUserLogic();
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					if($user_model->fetch(['id' => $user_id])){
						$user_information = $user_model->getObject();
						if($user_logic->verifyPassword($old_password, $user_information['name'], $user_information['password'])){
							$result = $user_model->modifyPassword(['id' => $user_id], $new_password);
							if($result['status'] == true){
								//写操作日志
								$data = [
									'id'          => 'default',
									'operator_id' => $user_id,
									'object_id'   => $user_id,
									'creator'     => $user_id,
									'action'      => 'EDIT_PASSWORD',
									'creatime'    => Time::getCurrentTime(),
									'remark'      => $remark,
								];
								/** @var \General\Model\SystemLogModel $system_log_model */
								$system_log_model = D('General/SystemLog');
								$system_log_model->create($data);
							}

							return array_merge(['__ajax__' => false], $result);
						}
						else{
							return ['status' => false, 'message' => '原密码错误'];
						}
					}
					else{
						return ['status' => false, 'message' => '找不到该用户'];
					}
				break;
				case 'modify':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$str_obj    = new StringPlus();
					$post       = I('post.');
					$id         = I('get.id', 0, 'int');
					$data       = [
						'nickname'        => $post['nickname'],
						'nickname_pinyin' => $str_obj->getPinyin($post['nickname'], true, ''),
						'comment'         => $post['comment'],
						C('TOKEN_NAME')   => $post[C('TOKEN_NAME')]
					];
					$res        = $user_model->modifyInformation(['id' => $id], $data);

					return $res;
				break;
				case 'create':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$str_obj    = new StringPlus();
					$user_logic = new GeneralUserLogic();
					$data       = I('post.');
					$result     = $user_model->create(array_merge($data, [
						'password'        => $user_logic->makePassword($data['password'], $data['name']),
						'parent_id'       => Session::getCurrentUser(),
						'creatime'        => Time::getCurrentTime(),
						'creator'         => Session::getCurrentUser(),
						'name_pinyin'     => $str_obj->getPinyin($data['name'], true, ''),
						'nickname_pinyin' => $str_obj->getPinyin($data['nickname'], true, '')
					]));

					return array_merge($result, ['__ajax__' => false, '__return__' => U('manage')]);
				break;
				case 'delete':
					/** @var \General\Model\UserModel $model */
					$model  = D('General/User');
					$id     = I('post.id', 0, 'int');
					$result = $model->drop(['id' => $id]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'login':
					return $this->checkLogin($opt['username'], base64_decode($opt['password']));
				break;
				case 'get_unassigned_role':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$user_id    = I('post.id', 0, 'int');
					$role_list  = $user_model->getRole($user_id, false);

					return array_merge(['__ajax__' => true], $role_list);
				break;
				case 'get_assigned_role':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$user_id    = I('post.id', 0, 'int');
					$role_list  = $user_model->getRole($user_id);

					return array_merge(['__ajax__' => true], $role_list);
				break;
				case 'assign_role':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$user_id    = I('post.id', 0, 'int');
					$role_id    = I('post.rid', 0, 'int');
					C('TOKEN_ON', false);
					$result = $user_model->assignRole($role_id, $user_id);

					return array_merge(['__ajax__' => true], $result);
				break;
				case 'anti_assign_role':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$user_id    = I('post.id', 0, 'int');
					$role_id    = I('post.rid', 0, 'int');
					C('TOKEN_ON', false);
					$result = $user_model->cancelRole($role_id, $user_id);

					return array_merge(['__ajax__' => true], $result);
				break;
				case 'modify_password':
					$old_password = base64_decode(I('post.old_password', ''));
					$new_password = base64_decode(I('post.new_password', ''));
					$user_id      = I('post.id', 0, 'int');
					$user_logic   = new GeneralUserLogic();
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					if($user_model->fetch(['id' => $user_id])){
						$user_information = $user_model->getObject();
						if($user_logic->verifyPassword($old_password, $user_information['name'], $user_information['password'])){
							$result = $user_model->modifyPassword(['id' => $user_id], $new_password);

							return array_merge(['__ajax__' => false], $result);
						}
						else{
							return ['status' => false, 'message' => '密码错误'];
						}
					}
					else{
						return ['status' => false, 'message' => '找不到该用户'];
					}
				break;
				case 'reset_password':
					$user_id = I('post.id', 0, 'int');
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					if($user_model->fetch(['id' => $user_id])){
						$result = $user_model->modifyPassword(['id' => $user_id], '', true);

						return array_merge(['__ajax__' => false], $result);
					}
					else return ['status' => false, 'message' => '找不到该用户'];
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true, '__redirect__' => ''];
				break;
			}
		}

		public function checkLogin($user_name, $password){
			$user_logic = new GeneralUserLogic();
			/** @var \General\Model\UserModel $user_model */
			$user_model = D('General/User');
			if($user_model->fetch(['name' => $user_name])){
				$user = $user_model->getObject();
				if(!$user_logic->verifyPassword($password, $user_name, $user['password'])){
					return [
						'status'   => false,
						'message'  => '密码错误',
						'__ajax__' => false
					];
				}
				if($user['status'] != 1) return [
					'status'   => false,
					'message'  => '该用户已删除或者被禁用',
					'__ajax__' => false,
				];
				if($password == ''){
					// todo 密码不能为空
				}
				session(Session::LOGIN_USER_ID, $user['id']);
				session(Session::LOGIN_USER_NAME, $user['name']);
				session(Session::LOGIN_USER_NICKNAME, $user['nickname']);

				return [
					'status'       => true,
					'message'      => '登入成功',
					'__redirect__' => U(CMS::FIRST_PAGE),
					'__ajax__'     => false
				];
			}
			else return [
				'status'   => false,
				'message'  => '该用户不存在',
				'__ajax__' => false
			];
		}

		public function setData($type, $data){
			switch($type){
				case 'manage':
					foreach($data as $key => $user){
						$role_name_list = explode(UserModel::ROLE_NAME_SEPARATOR, $user['role_name']);
						$role_id_list   = explode(',', $user['role_id']);
						$role_name      = '';
						foreach($role_name_list as $k => $role) $role_name .= "<a href='javascript:void(0)' data-role-id='$role_id_list[$key]'>$role</a>, ";
						$data[$key]['role'] = trim($role_name, ', ');
						$data[$key]['status_code'] = $user['status'];
						$data[$key]['status'] = GeneralModel::STATUS[$user['status']];
					}

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}

		/**
		 * 判断登入用户是否具有某项权限
		 *
		 * @param string|array $permission 权限码或权限码列表
		 *
		 * @return bool
		 */
		public static function isPermitted($permission){
			$permission_list = session(Session::LOGIN_USER_PERMISSION_LIST);
			if(is_string($permission)){
				return in_array($permission, $permission_list);
			}
			elseif(is_array($permission)){
				$count = 0;
				foreach($permission as $val){
					if(in_array($val, $permission_list)) $count++;
				}

				return $count>=count($permission_list) ? true : false;
			}
			else return false;
		}
	}