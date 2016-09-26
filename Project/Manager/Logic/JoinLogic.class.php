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

			return $model->createRecord($data);
		}

		public function makeQRCode($client_list, $addition){
			$qrcode_obj = new QRCodeLogic();
			$str_obj    = new StringPlus();
			$length     = count($client_list);
			$count      = 0;
			foreach($client_list as $val){
				$file_name   = $str_obj->makeGuid('qrcode', false).'.png';
				$file_path   = QRCODE_PATH.'/'.date('Y-m-d').'/'.$file_name;
				$url         = "http://www.baidu.com/id/$val[id]";
				$qrcode_file = $qrcode_obj->make($url, $file_path, [
					'logo' => $this->config['logo']
				]);
				$remote_url  = $_SERVER['HTTP_HOST'].'/'.trim($qrcode_file, './');
				$result      = $this->create(array_merge([
					'cid'         => $val['id'],
					'sign_qrcode' => $remote_url,
					'sign_code'   => $str_obj->makeRandomString(8)
				], $addition));
				if($result) $count++;
			}
			if($length == $count){
				/** @noinspection PhpUndefinedVariableInspection */
				return $result;
			}
			elseif($count == 0) return ['status' => false, 'message' => '数据没有成功导入'];
			else return ['status' => false, 'message' => '数据部分未导入'];
		}

	}