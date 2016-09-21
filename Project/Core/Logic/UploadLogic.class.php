<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-14
	 * Time: 16:45
	 */
	namespace Core\Logic;

	use Think\Upload;

	class UploadLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function upload($files, $save_path = '/'){
			$object = new Upload();
			$object->__set('rootPath', UPLOAD_PATH);
			$object->__set('savePath', $save_path);
			$info = $object->upload($files);
			if($info) return ['status' => true, 'data' => $info, 'message' => '上传成功'];
			else return ['status' => false, 'message' => $object->getError()];
		}
	}
