<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-12-2
	 * Time: 10:43
	 */
	namespace Open\Logic;

	use Core\Logic\WxCorpLogic;
	use Quasar\StringPlus;

	class MobileLogic extends OpenLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $option = []){
			switch($type){
				case 'create_client':
					$data = I('post.');
					/* 1.创建参会人员 */
					/** @var \Core\Model\ClientModel $model */
					$model        = D('Core/Client');
					$exist_client = $model->findClient(1, ['mobile' => $data['mobile']]);
					if($exist_client){
						$client_id           = $exist_client['id'];
						$str_obj             = new StringPlus();
						$data['is_new']      = 0;
						$data['status']      = 1;
						$data['creatime']    = time();
						$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
						$model->alterClient(['id' => $client_id], $data);
					}
					else{
						$str_obj             = new StringPlus();
						$data['creatime']    = time();
						$data['creator']     = $option['employeeID'];
						$data['pinyin_code'] = $str_obj->makePinyinCode($data['name']);
						$data['password']    = $str_obj->makePassword(C('DEFAULT_CLIENT_PASSWORD'), $data['mobile']);
						$result1             = $model->createClient($data);
						if($result1['status']) $client_id = $result1['id'];
						else return $result1;
					}
					/* 2.创建参会记录 */
					$mid = I('get.mid', 0, 'int');
					/** @var \Core\Model\JoinModel $join_model */
					$join_model  = D('Core/Join');
					$join_record = $join_model->findRecord(1, [
						'mid' => $mid,
						'cid' => $client_id
					]);
					$mobile      = $data['mobile'];
					if($join_record){
						if($join_record['join_status'] != 1){
							C('TOKEN_ON', false);
							$join_result = $join_model->alterRecord(['id' => $join_record['id']], [
								'status'            => 1,
								'registration_type' => '线上'
							]);
							if($join_result['status']) $result = [
								'status'  => true,
								'message' => '创建成功'
							];
							else $result = $join_result;
						}
						else $result = [
							'status'  => true,
							'message' => '创建成功'
						];
					}
					else{
						$data                      = [];
						$data['cid']               = $client_id;
						$data['registration_type'] = '线上';
						$data['mid']               = $mid;
						$data['creatime']          = time();
						$data['creator']           = $option['employeeID'];
						C('TOKEN_ON', false);
						$result = $join_model->createRecord($data);
					}
					/* 3.试图根据手机号创建微信用户记录 */
					$wechat_logic     = new WxCorpLogic();
					$wechat_user_list = $wechat_logic->getAllUserList();
					foreach($wechat_user_list as $val){
						if($val['mobile'] == $mobile && $val['status'] != 4){
							/** @var \Core\Model\WechatModel $wechat_model */
							$wechat_model = D('Core/Wechat');
							$department   = '';
							foreach($val['department'] as $val2) $department .= $val2.',';
							$department         = trim($department, ',');
							$data               = [];
							$data['otype']      = 1;// 对象类型 这里为客户(参会人员)
							$data['oid']        = $client_id; // 对象ID
							$data['wtype']      = 1; // 微信ID类型 企业号
							$data['wid']  = $val['userid']; // 微信ID
							$data['department'] = $department; // 部门ID
							$data['mobile']     = $val['mobile']; // 手机号码
							$data['avatar']     = $val['avatar']; // 头像地址
							$data['gender']     = $val['gender']; // 性别
							$data['is_follow']  = $val['status'];    //是否关注
							$data['nickname']   = $val['name']; // 昵称
							$data['creatime']   = time(); // 创建时间
							$data['creator']    = $option['employeeID']; // 当前创建者
							C('TOKEN_ON', false);
							$wechat_model->createRecord($data); // 插入数据
							break;
						}
					}

					return array_merge($result, ['__ajax__' => true]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数'];
				break;
			}
		}
	}