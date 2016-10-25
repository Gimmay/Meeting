<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 9:39
	 */
	namespace Core\Logic;

	use /** @noinspection PhpUndefinedClassInspection */
		QRcode;
	use Quasar\StringPlus;

	class QRCodeLogic extends CoreLogic{
		private $_config = [
			'path' => null
		];

		public function _initialize(){
			parent::_initialize();
			$str_obj               = new StringPlus();
			$file_name             = $str_obj->makeGuid('qrcode', false).'.png';
			$this->_config['path'] = UPLOAD_PATH.'/QRCode/'.date('Y-m-d')."/$file_name";
		}

		public function make($value, $config = []){
			$defaults = [
				'level'    => 'H',
				'size'     => 6,
				'margin'   => 2,
				'logo'     => null,
				'filePath' => null
			];
			$conf     = array_merge($defaults, $config);
			vendor('phpqrcode.phpqrcode');
			/** @noinspection PhpUndefinedClassInspection */
			$qrcode_obj = new \QRcode();
			$file_path  = $conf['filePath'] ? $conf['filePath'] : $this->_config['path'];
			if(!is_dir(dirname($file_path))) mkdir(dirname($file_path), 0777, true);
			$qrcode_obj->png($value, $file_path, $conf['level'], $conf['size'], $conf['margin']);
			if(file_exists($conf['logo'])){
				$qrcode       = imagecreatefromstring(file_get_contents($file_path));
				$logo         = imagecreatefromstring(file_get_contents($conf['logo']));
				$qrcode_width = imagesx($qrcode);// 二维码图片宽度
				//$qrcode_height = imagesy($qrcode); // 二维码图片高度
				$logo_width     = imagesx($logo); // logo 图片宽度
				$logo_height    = imagesy($logo); // logo 图片高度
				$logo_qr_width  = $qrcode_width/5;
				$scale          = $logo_width/$logo_qr_width;
				$logo_qr_height = $logo_height/$scale;
				$from_width     = ($qrcode_width-$logo_qr_width)/2; // 重新组合图片并调整大小
				imagecopyresampled($qrcode, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
				// 输出图片
				imagepng($qrcode, $file_path);
				imagedestroy($qrcode);
				imagedestroy($logo);
			}

			return $file_path;
		}
	}