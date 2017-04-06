<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 14:40
	 */
	namespace General\Logic;

	class UploadLogic{
		/**
		 * 获取当前时间字符串
		 *
		 * @return string
		 */
		public static function getCurrentTime(){
			return date('Y-m-d H:i:s');
		}

		/**
		 * 判断时间字符串是否为空
		 *
		 * @param string $str 字符串
		 *
		 * @return null|string 是则返回空 否则返回原字符串
		 */
		public static function isNull($str){
			return $str == '' ? null : $str;
		}
		public function uploadFile(){
			$targetFolder = '/Project/CMS/Upload/'; //上传的目标路径

			if (!empty($_FILES)) {
				$tempFile = $_FILES['Filedata']['tmp_name'];
				$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
				$targetFile = rtrim($targetPath,'/') . '/' . md5($_FILES['Filedata']['name']).'.png'; //相同名称将覆盖
				// Validate the file type
				$fileTypes = array('jpg','jpeg','gif','png','bmp'); // File extensions
				$fileParts = pathinfo($_FILES['Filedata']['name']);
                $filePath = $targetFolder.md5($_FILES['Filedata']['name']).'.png';
				if (in_array($fileParts['extension'],$fileTypes)) {
					move_uploaded_file($tempFile,$targetFile);
					return ['status' => true,'message'=>'上传成功','path' => $filePath];

				} else {
					return ['status' => false,'message'=>'上传失败'];
				}
			}
		}
	}