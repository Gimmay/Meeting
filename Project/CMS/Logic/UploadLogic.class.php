<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-14
	 * Time: 16:45
	 */
	namespace CMS\Logic;

	use General\Logic\Time;
	use Think\Upload;

	class UploadLogic extends CMSLogic{
		const UPLOAD_PATH = APP_PATH.MODULE_NAME.'/Upload';
		private $_meetingID = 0;

		public function _initialize(){
			parent::_initialize();
		}

		public function __construct($meeting_id = null){
			parent::__construct();
			$this->_meetingID = $meeting_id;
		}

		/**
		 * 上传文件
		 *
		 * @param array  $file_object $_FILES表单
		 * @param string $save_path   存储路径
		 *
		 * @return array
		 */
		public function upload($file_object, $save_path = '/'){
			$getUploadedFilePath = function ($upload_result){
				foreach($upload_result as $key => $val) return trim(self::UPLOAD_PATH.$upload_result[$key]['savepath'].$upload_result[$key]['savename'], '.');

				return null;
			};
			$think_upload_obj    = new Upload();
			$think_upload_obj->__set('rootPath', self::UPLOAD_PATH);
			$think_upload_obj->__set('savePath', $save_path);
			$info   = $think_upload_obj->upload($file_object);
			$log_id = null;
			if($info){
				foreach($info as $key => $val){
					$log_result = $this->_log($info);
					if($log_result['status']) $log_id = $log_result['data'];
				}
			}
			if($log_id) return [
				'status'  => true,
				'data'    => [
					'filePath' => $getUploadedFilePath($info),
					'logID'    => $log_id
				],
				'message' => '上传成功'
			];
			else return ['status' => false, 'message' => $think_upload_obj->getError()];
		}

		/**
		 * 将上传记录写入数据库日志
		 *
		 * @param array $upload_result 上传结果
		 *
		 * @return array
		 */
		private function _log($upload_result){
			/** @var \General\Model\UploadLogModel $upload_log_model */
			$upload_log_model = D('General/UploadLog');
			$count            = 0;
			$new_id           = null;
			foreach($upload_result as $key => $val){
				$data   = [
					'file_type'   => $val['type'],
					'save_path'   => trim(self::UPLOAD_PATH.$val['savepath'].$val['savename'], '.'),
					'suffix'      => $val['ext'],
					'origin_name' => $val['name'],
					'size'        => $val['size'],
					'md5'         => $val['md5'],
					'sha1'        => $val['sha1'],
					'mid'         => $this->_meetingID,
					'creator'     => Session::getCurrentUser(),
					'creatime'    => Time::getCurrentTime()
				];
				$result = $upload_log_model->create($data);
				if($result['status']){
					$count++;
					$new_id = $result['id'];
				}
			}
			if($count == count($upload_result)) return ['status' => true, 'message' => '记录成功', 'data' => $new_id];
			elseif($count == 0) return ['status' => false, 'message' => '记录失败'];
			else return ['status' => true, 'message' => '部分记录成功'];
		}

		/**
		 * 处理POST/GET请求
		 *
		 * @param string $type 请求类型
		 * @param array  $opt  相关参数
		 *
		 * @return mixed
		 */
		public function handlerRequest($type, $opt = []){
		}

		/**
		 * 设定额外数据
		 *
		 * @param string $type 操作类型
		 * @param mixed  $data 处理数据
		 *
		 * @return mixed
		 */
		public function setData($type, $data){
		}
	}