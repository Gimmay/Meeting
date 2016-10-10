<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 17:00
	 */
	namespace Manager\Logic;

	use Core\Logic\QRCodeLogic;
	use Quasar\StringPlus;

	class JoinLogic extends ManagerLogic{
		private $config = [
			'logo' => 'Project/Resources/Images/Common/profile_small.gif'
		];

		public function _initialize(){
			parent::_initialize();
		}

		public function create($data){
			/** @var \Core\Model\JoinModel $model */
			$model            = D('Core/Join');
			$data['creatime'] = time();
			$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			C('TOKEN_ON', false);

			return $model->createRecord($data);
		}

		public function alter(){
			$id = I('get.id', 0, 'int');
		}

		public function makeQRCode($client_list, $data){
			$qrcode_obj = new QRCodeLogic();
			$str_obj    = new StringPlus();
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			$length     = count($client_list);
			$count      = 0;
			$result     = ['status' => false, 'message' => '数据更新失败'];
			foreach($client_list as $val){
				$file_name   = $str_obj->makeGuid('qrcode', false).'.png';
				$file_path   = QRCODE_PATH.'/'.date('Y-m-d').'/'.$file_name;
				$url         = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]/Mobile/Client/manage/id/$val/mid/$data[mid]";
				$qrcode_file = $qrcode_obj->make($url, $file_path);
				$remote_url  = '/'.trim($qrcode_file, './');
				$join_record = $join_model->findRecord(1, ['cid' => $val, 'mid' => $data['mid']]);
				$result      = $join_model->alterRecord([$join_record['id']], [
					'sign_qrcode' => $remote_url,
					'sign_code'   => $str_obj->makeRandomString(8)
				]);
				if($result) $count++;
			}
			if($length == $count || $count == 0) return $result;
			else return ['status' => true, 'message' => '数据部分未写入成功'];
		}
	}