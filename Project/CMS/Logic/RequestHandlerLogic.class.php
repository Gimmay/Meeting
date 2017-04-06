<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-16
	 * Time: 11:11
	 */
	namespace CMS\Logic;

	class RequestHandlerLogic extends CMSLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'get:disable_user':
				case 'get:enable_user':
					/** @var \General\Model\UserModel $model */
					$model  = D('General/User');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					if($status == 1) $result = $model->enable(['id' => $id]);
					else $result = $model->disable(['id' => $id]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_role':
				case 'get:enable_role':
					/** @var \General\Model\RoleModel $model */
					$model  = D('General/Role');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					if($status == 1) $result = $model->enable(['id' => $id]);
					else $result = $model->disable(['id' => $id]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true, '__redirect__' => ''];
				break;
			}
		}

		public function setData($type, $data){
		}
	}