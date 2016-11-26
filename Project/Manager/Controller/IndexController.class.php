<?php
	namespace Manager\Controller;

	use Core\Logic\WxCorpLogic;
	use Quasar\StringPlus;
	use Think\Controller;

	class IndexController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function index(){
			$this->display();
		}

		public function updatePermissionPinyin(){
			$str_obj = new StringPlus();
			/** @var \Think\Model $permission_model */
			$permission_model = M('system_permission');
			$record           = $permission_model->select();
			foreach($record as $val){
				if(!$val['pinyin_code']) $permission_model->where(['id' => $val['id']])->save(['pinyin_code' => $str_obj->makePinyinCode($val['name'])]);
			}
		}

		public function updateClientWeixinInfo(){
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			$logic        = new WxCorpLogic();
			$wx_list      = $logic->getAllUserList(); //查出wx接口获取的所有用户信息
			/** @var \Core\Model\ClientModel $model */
			$model       = D('Core/Client');
			$client_list = $model->findClient(2);//查出所有客户或员工信息
			C('TOKEN_ON', false);
			$weixin_model->deleteRecord(['otype' => 1, 'wtype' => 1]);
			foreach($client_list as $k1 => $v1){ //循环输出当前系统的客户名单
				foreach($wx_list as $k2 => $v2){//循环输出微信获取的所有用户信息
					if($v1['mobile'] == $v2['mobile'] && $v2['status'] != 4){//判断 当前系统的手机和微信获取的用户信息的手机做匹配同时没有取消关注
						//$weixin_model->deleteRecord(['weixin_id'=>$v2['userid'], 'otype'=>1]);
						$department = '';
						foreach($v2['department'] as $v3) $department .= $v3.',';
						$department         = trim($department, ',');
						$data               = [];
						$data['otype']      = 1;    //对象类型
						$data['oid']        = $v1['id'];    //对象ID
						$data['wtype']      = 1;    //微信ID类型 企业号
						$data['department'] = $department;    //部门ID
						$data['weixin_id']  = $v2['userid'];    //微信ID
						$data['mobile']     = $v2['mobile'];    //手机号码
						$data['avatar']     = $v2['avatar'];    //头像地址
						$data['gender']     = $v2['gender'];    //性别
						$data['is_follow']  = $v2['status'];    //是否关注
						$data['nickname']   = $v2['name'];    //昵称
						$data['creatime']   = time();    //创建时间
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$weixin_model->createRecord($data);    //插入数据
						break;
					}
				}
			}
		}

		public function updateEmployeeWeixinInfo(){
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			$logic        = new WxCorpLogic();
			$wx_list      = $logic->getAllUserList(); //查出wx接口获取的所有用户信息
			/** @var \Core\Model\EmployeeModel $model */
			$model       = D('Core/Employee');
			$client_list = $model->findEmployee(2);//查出所有客户或员工信息
			C('TOKEN_ON', false);
			$weixin_model->deleteRecord(['otype' => 0, 'wtype' => 1]);
			foreach($client_list as $k1 => $v1){ //循环输出当前系统的员工名单
				foreach($wx_list as $k2 => $v2){//循环输出微信获取的所有用户信息
					if($v1['mobile'] == $v2['mobile'] && $v2['status'] != 4){//判断 当前系统的手机和微信获取的用户信息的手机做匹配
						//$weixin_model->deleteRecord(['weixin_id'=>$v2['userid'], 'otype'=>0]);
						$department = '';
						foreach($v2['department'] as $v3) $department .= $v3.',';
						$department         = trim($department, ',');
						$data               = [];
						$data['otype']      = 0;    //对象类型
						$data['oid']        = $v1['id'];    //对象ID
						$data['wtype']      = 1;    //微信ID类型 企业号
						$data['department'] = $department;    //部门ID
						$data['weixin_id']  = $v2['userid'];    //微信ID
						$data['mobile']     = $v2['mobile'];    //手机号码
						$data['avatar']     = $v2['avatar'];    //头像地址
						$data['gender']     = $v2['gender'];    //性别
						$data['is_follow']  = $v2['status'];    //是否关注
						$data['nickname']   = $v2['name'];    //昵称
						$data['creatime']   = time();    //创建时间
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
						$weixin_model->createRecord($data);    //插入数据
						break;
					}
				}
			}
		}
	}