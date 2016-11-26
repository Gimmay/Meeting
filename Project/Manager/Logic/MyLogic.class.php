<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-2
	 * Time: 17:24
	 */
	namespace Manager\Logic;

	use Quasar\StringPlus;

	class MyLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'alter_password':
					$str = new StringPlus();
					/** @var \Core\Model\EmployeeModel $model */
					$model        = D('Core/Employee');
					$data         = I('post.');
					$id           = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$old_password = I('post.old_password'); // 旧密码
					$input_password =       $old_password;
					$new_password = I('post.new_password'); // 新密码
					$info         = $model->findEmployee(1, ['id' => $id]); //查询出这个ID的数据
					$old_password = $str->makePassword($old_password, $info['code']);    //旧密码加密
					$new_password = $str->makePassword($new_password, $info['code']);    //新密码加密
					if($info['password'] == $old_password){    //判断加密过后的密码和数据库的密码是否匹配
						$data['password'] = $new_password;
						/** @var \Core\Model\EmployeeModel $model */
						$model  = D('Core/Employee');
						$result = $model->alterEmployee(['id' => $id], $data); //成功后就更新到数据库
						if($input_password!='') unset($_SESSION['MANAGER_EMPLOYEE_EMPTY_PASSWORD']);
						else session('MANAGER_EMPLOYEE_EMPTY_PASSWORD', 1);

						return array_merge($result, ['__ajax__' => false]);
					}
					else return ['status' => false, 'message' => '密码错误', '__ajax__' => false];
				break;
				case 'alter_information':
					/** @var \Core\Model\EmployeeModel $model */
					$id     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$model  = D('Core/Employee');
					$result = $model->alterEmployee(['id' => $id], I('post.'));

					return array_merge($result, ['__ajax__' => false]);
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setData($type, $data){
			switch($type){
				case '':
					return $data;
				break;
				default:
					return $data;
				break;
			}
		}
	}