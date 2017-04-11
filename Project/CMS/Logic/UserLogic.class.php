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
	use Quasar\Utility\IP;
	use Quasar\Utility\StringPlus;

	class UserLogic extends CMSLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'modify':
					/** @var \General\Model\UserModel $user_model */
					$user_model              = D('General/User');
					$str_obj                 = new StringPlus();
					$id                      = I('get.id', 0, 'int');
					$post                    = I('post.');
					$post['nickname_pinyin'] = $str_obj->getPinyin($post['nickname'], true, '');
					$result                  = $user_model->modifyInformation(['id' => $id], $post);

					return array_merge($result, ['__ajax__' => true, 'nextPage' => U('manage')]);
				break;
				case 'create':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$str_obj    = new StringPlus();
					$user_logic = new GeneralUserLogic();
					$data       = I('post.');
					// todo 手机号去重判断
					$result = $user_model->create(array_merge($data, [
						'password'        => $user_logic->makePassword($data['password'], $data['name']),
						'parent_id'       => Session::getCurrentUser(),
						'creatime'        => Time::getCurrentTime(),
						'creator'         => Session::getCurrentUser(),
						'name_pinyin'     => $str_obj->getPinyin($data['name'], true, ''),
						'nickname_pinyin' => $str_obj->getPinyin($data['nickname'], true, '')
					]));

					return array_merge($result, ['__ajax__' => true, 'nextPage' => U('manage')]);
				break;
				case 'delete':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$id_str     = I('post.id', '');
					$id         = explode(',', $id_str);
					$result     = $user_model->drop(['id' => ['in', $id]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$id_str     = I('post.id', '');
					$id         = explode(',', $id_str);
					$result     = $user_model->enable(['id' => ['in', $id]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$id_str     = I('post.id', '');
					$id         = explode(',', $id_str);
					$result     = $user_model->disable(['id' => ['in', $id]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'login':
					return $this->checkLogin($opt['username'], base64_decode($opt['password']));
				break;
				case 'get_password_hint':
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					$user_name  = I('post.username', '');
					if(!$user_model->fetch(['name' => $user_name, 'status' => 1])) return [
						'status'   => false,
						'message'  => '找不到用户',
						'__ajax__' => true
					];
					$user = $user_model->getObject();

					return [
						'status'   => true,
						'message'  => '获取成功',
						'data'     => $user['password_hint'],
						'__ajax__' => true
					];
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
							if($result['status'] == true){
								$ip_obj = new IP();
								// 保存日志
								$data = [
									'operator' => Session::getCurrentUser(),
									'object'   => $user_id,
									'action'   => 'MODIFY_PASSWORD',
									'creator'  => Session::getCurrentUser(),
									'creatime' => Time::getCurrentTime(),
									'ip'       => $ip_obj->getClientIP()
								];
								/** @var \General\Model\SystemLogModel $system_log_model */
								$system_log_model = D('General/SystemLog');
								$system_log_model->create($data);
							}

							return array_merge(['__ajax__' => true], $result);
						}
						else{
							return ['status' => false, 'message' => '密码错误', '__ajax__' => true];
						}
					}
					else{
						return ['status' => false, 'message' => '找不到该用户', '__ajax__' => true];
					}
				break;
				case 'reset_password':
					$user_id = I('post.id', 0, 'int');
					/** @var \General\Model\UserModel $user_model */
					$user_model = D('General/User');
					if($user_model->fetch(['id' => $user_id])){
						$result = $user_model->modifyPassword(['id' => $user_id], '', true);
						if($result['status'] == true){
							$ip_obj = new IP();
							// 保存日志
							$data = [
								'operator' => Session::getCurrentUser(),
								'object'   => $user_id,
								'action'   => 'RESET_PASSWORD',
								'creator'  => Session::getCurrentUser(),
								'creatime' => Time::getCurrentTime(),
								'ip'       => $ip_obj->getClientIP()
							];
							/** @var \General\Model\SystemLogModel $system_log_model */
							$system_log_model = D('General/SystemLog');
							$system_log_model->create($data);
						}

						return array_merge(['__ajax__' => true], $result);
					}
					else return ['status' => false, 'message' => '找不到该用户', '__ajax__' => true];
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
					session(Session::MUST_MODIFY_PASSWORD, 1);
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
					$list = [];
					$get  = $data['urlParam'];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					// 若指定了角色ID的情况
					if(isset($get[UserModel::CONTROL_COLUMN_PARAMETER_SELF['roleID']])) $role_id = $get[UserModel::CONTROL_COLUMN_PARAMETER_SELF['roleID']];
					// 若指定了固定的ClientID
					foreach($data['list'] as $key => $user){
						// 1、筛选数据
						if(isset($keyword)){
							// todo 获取筛选配置
							$found = 0;
							if($found == 0 && strpos($user['name'], $keyword) !== false) $found = 1;
							if($found == 0 && strpos($user['name_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						$role_id_list = explode(',', $user['role_id']);
						if(isset($role_id)){
							if(!in_array($role_id, $role_id_list)) continue;
						}
						$role_name_list = explode(UserModel::ROLE_NAME_SEPARATOR, $user['role_name']);
						$role_name      = '';
						foreach($role_name_list as $k => $role) $role_name .= "<a href='javascript:void(0)' data-role-id='$role_id_list[$key]'>$role</a>, ";
						$user['role']        = trim($role_name, ', ');
						$user['status_code'] = $user['status'];
						$user['status']      = GeneralModel::STATUS[$user['status']];
						$list[]              = $user;
					}

					return $list;
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