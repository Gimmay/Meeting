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

		public function upload($file_object, $save_path = '/', $log = true){
			$object = new Upload();
			$object->__set('rootPath', UPLOAD_PATH);
			$object->__set('savePath', $save_path);
			$info   = $object->upload($file_object);
			$log_id = null;
			if($log && $info){
				foreach($info as $key => $val){
					$log_result = $this->log($info);
					if($log_result['status']) $log_id = $log_result['data'];
				}
			}
			if($info) return [
				'status'  => true,
				'data'    => [
					'filePath' => $this->getUploadedFilePath($info),
					'logID'    => $log_id
				],
				'message' => '上传成功'
			];
			else return ['status' => false, 'message' => $object->getError()];
		}

		public function getUploadedFilePath($upload_result){
			foreach($upload_result as $key => $val) return trim(UPLOAD_PATH.$upload_result[$key]['savepath'].$upload_result[$key]['savename'], '.');

			return null;
		}

		public function log($upload_result){
			/** @var \Core\Model\UploadModel $model */
			$model  = D('Core/Upload');
			$count  = 0;
			$new_id = null;
			foreach($upload_result as $key => $val){
				$data                = [];
				$data['file_type']   = $val['type'];
				$data['save_path']   = trim(UPLOAD_PATH.$val['savepath'].$val['savename'], '.');
				$data['creator']     = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
				$data['creatime']    = time();
				$data['suffix']      = $val['ext'];
				$data['md5']         = $val['md5'];
				$data['sha1']        = $val['sha1'];
				$data['size']        = $val['size'];
				$data['origin_name'] = $val['name'];
				C('TOKEN_ON', false);
				$result = $model->createRecord($data);
				if($result['status']){
					$count++;
					$new_id = $result['id'];
				}
			}
			if($count == count($upload_result)) return ['status' => true, 'message' => '记录成功', 'data' => $new_id];
			elseif($count == 0) return ['status' => false, 'message' => '记录失败'];
			else return ['status' => true, 'message' => '部分记录成功'];
		}
	}