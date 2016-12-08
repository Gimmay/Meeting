<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-12-7
	 * Time: 14:58
	 */
	namespace Core\Logic;

	use Quasar\StringPlus;

	class JoinLogic extends CoreLogic{
		private $config = [
			'logo' => 'Project/Resources/Images/Common/profile_small.gif'
		];

		public function _initialize(){
			parent::_initialize();
		}

		public function makeQRCodeForSign($client_list, $data){
			$qrcode_obj = new QRCodeLogic();
			$str_obj    = new StringPlus();
			/** @var \Core\Model\JoinModel $join_model */
			$join_model = D('Core/Join');
			$length     = count($client_list);
			$count      = 0;
			$result     = ['status' => false, 'message' => '数据更新失败'];
			foreach($client_list as $val){
				$sign_url                   = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]".U('Mobile/Manager/client', [
						'cid' => $val,
						'mid' => $data['mid']
					]);
				$sign_code                  = $str_obj->makeRandomString(8);
				$sign_url_qrcode            = $qrcode_obj->make($sign_url);
				$sign_code_qrcode           = $qrcode_obj->make($sign_code);
				$sign_url_qrcode_file_path  = '/'.trim($sign_url_qrcode, './');
				$sign_code_qrcode_file_path = '/'.trim($sign_code_qrcode, './');
				$result                     = $join_model->alterRecord(['cid' => $val, 'mid' => $data['mid']], [
					'sign_qrcode'      => $sign_url_qrcode_file_path,
					'sign_code'        => $sign_code,
					'sign_code_qrcode' => $sign_code_qrcode_file_path
				]);
				if($result) $count++;
			}
			if($length == $count || $count == 0) return $result;
			else return ['status' => true, 'message' => '数据部分未写入成功'];
		}
	}