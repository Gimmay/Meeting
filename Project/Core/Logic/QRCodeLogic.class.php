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

	class QRCodeLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function make($value, $file_path, $config = []){
			$defaults = [
				'level'  => 'H',
				'size'   => 6,
				'margin' => 2,
				'logo'   => null
			];
			$conf     = array_merge($defaults, $config);
			vendor('phpqrcode.phpqrcode');
			/** @noinspection PhpUndefinedClassInspection */
			$qrcode_obj = new \QRcode();
			if(!is_dir(dirname($file_path))) mkdir(dirname($file_path));
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