<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 9:18
	 */

	namespace CMS\Logic;

	use General\Logic\SystemLogic as GeneralSystemLogic;
	use General\Logic\UploadLogic;

	class SystemConfigureLogic extends CMSLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'set_configure':
					if(!in_array('GENERAL-SYSTEM.CONFIGURE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有系统配置的权限',
						'__ajax__' => true
					];
					/** @var \General\Model\SystemConfigureModel $system_configure_model */
					$system_configure_model = D('General/SystemConfigure');
					$data                   = I('post.');
					$result                 = $system_configure_model->setConfigure($data);

					return array_merge($result, ['__ajax__' => true]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true, '__redirect__' => ''];
				break;
			}
		}

		public function setData($type, $data){
		}
	}