<?php
	namespace Manager\Controller;

	use Core\Logic\WxCorpLogic;
	use Think\Controller;

	class IndexController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function index(){
			$this->display();
		}

		public function test(){
			/** @var \Core\Model\WeixinIDModel $weixin_model */
			$weixin_model = D('Core/WeixinID');
			$logic        = new WxCorpLogic();
			$wx_list      = $logic->getAllUserList(); //查出wx接口获取的所有用户信息
			/** @var \Core\Model\ClientModel $model */
			$model       = D('Core/Client');
			$client_list = $model->findClient(2);//查出所有客户或员工信息
			C('TOKEN_ON', false);
			foreach($client_list as $k1 => $v1){
				foreach($wx_list as $k2 => $v2){
					if($v1['mobile'] == $v2['mobile']){
						$department = '';
						foreach($v2['department'] as $v3) $department .= $v3.',';
						$department         = trim($department, ',');
						$data               = [];
						$data['otype']      = 1;
						$data['oid']        = $v1['id'];
						$data['userid']     = 1;
						$data['department'] = $department;
						$data['weixin_id']  = $v2['userid'];
						$data['mobile']     = $v2['mobile'];
						$data['avatar']     = $v2['avatar'];
						$data['gender']     = $v2['gender'];
						$data['nickname']   = $v2['name'];
						$data['creatime']   = time();
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						$weixin_model->createRecord($data);
					}
				}
			}
		}

	}