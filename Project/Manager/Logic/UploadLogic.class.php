<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-23
	 * Time: 15:34
	 */
	namespace Manager\Logic;

	class UploadLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function create($path, $upload_result){
			/** @var \Core\Model\UploadModel $model */
			$model               = D('Core/Upload');
			$data['file_type']   = $upload_result['type'];
			$data['save_path']   = $path;
			$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
			$data['creatime']    = time();
			$data['suffix']      = $upload_result['ext'];
			$data['md5']         = $upload_result['md5'];
			$data['sha1']        = $upload_result['sha1'];
			$data['size']        = $upload_result['size'];
			$data['origin_name'] = $upload_result['name'];

			return $model->createRecord($data);
		}
	}