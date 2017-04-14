<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-31
	 * Time: 12:10
	 */
	namespace CMS\Logic;

	use General\Logic\MeetingLogic;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;

	class ApiConfigureLogic extends CMSLogic{
		/**
		 * 处理POST/GET请求
		 *
		 * @param string $type 请求类型
		 * @param array  $opt  相关参数
		 *
		 * @return mixed
		 */
		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'create':
					if(!in_array('GENERAL-API_CONFIGURE.CREATE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有新增接口配置的权限',
						'__ajax__' => true
					];
					$post = I('post.');
					/** @var \General\Model\ApiConfigureModel $api_configure_model */
					$api_configure_model = D('General/ApiConfigure');
					$str_obj             = new StringPlus();
					$result              = $api_configure_model->create(array_merge([
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'creatime'    => Time::getCurrentTime(),
						'creator'     => Session::getCurrentUser()
					], $post));

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify':
					if(!in_array('GENERAL-API_CONFIGURE.MODIFY', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有修改接口配置的权限',
						'__ajax__' => true
					];
					$id   = I('post.id', 0, 'int');
					$post = I('post.');
					/** @var \General\Model\ApiConfigureModel $api_configure_model */
					$api_configure_model = D('General/ApiConfigure');
					$str_obj             = new StringPlus();
					unset($post['id']);
					$result = $api_configure_model->modify(['id' => $id], array_merge([
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, '')
					], $post));

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete':
					if(!in_array('GENERAL-API_CONFIGURE.DELETE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有删除接口配置的权限',
						'__ajax__' => true
					];
					$id     = I('post.id', '');
					$id_arr = explode(',', $id);
					/** @var \General\Model\ApiConfigureModel $api_configure_model */
					$api_configure_model = D('General/ApiConfigure');
					$result              = $api_configure_model->drop(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable':
					if(!in_array('GENERAL-API_CONFIGURE.ENABLE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有启用接口配置的权限',
						'__ajax__' => true
					];
					$id     = I('post.id', '');
					$id_arr = explode(',', $id);
					/** @var \General\Model\ApiConfigureModel $api_configure_model */
					$api_configure_model = D('General/ApiConfigure');
					$result              = $api_configure_model->enable(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable':
					if(!in_array('GENERAL-API_CONFIGURE.DISABLE', $_SESSION[Session::LOGIN_USER_PERMISSION_LIST])) return [
						'status'   => false,
						'message'  => '您没有禁用接口配置的权限',
						'__ajax__' => true
					];
					$id     = I('post.id', '');
					$id_arr = explode(',', $id);
					/** @var \General\Model\ApiConfigureModel $api_configure_model */
					$api_configure_model = D('General/ApiConfigure');
					$result              = $api_configure_model->disable(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_detail':
					$id = I('post.id', 0, 'int');
					/** @var \General\Model\ApiConfigureModel $api_configure_model */
					$api_configure_model = D('General/ApiConfigure');
					if(!$api_configure_model->fetch(['id' => $id])) return [
						'status'   => false,
						'message'  => '找不到配置信息',
						'__ajax__' => true
					];
					$record = $api_configure_model->getObject();

					return array_merge($record, ['__ajax__' => true]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true];
				break;
			}
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
			switch($type){
				case 'get_meeting_type':
					$result = [];
					foreach($data as $val) $result[] = $val['type'];

					return implode(',', $result);
				break;
				case 'manage':
					foreach($data as $key => $project){
						$data[$key]['status_code']  = $project['status'];
						$data[$key]['status']       = GeneralModel::STATUS[$project['status']];
						$data[$key]['meeting_type'] = MeetingLogic::TYPE[$data[$key]['mtype']];
					}

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}
	}