<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-15
	 * Time: 10:18
	 */
	namespace RoyalwissD\Logic;

	use CMS\Logic\Session;
	use CMS\Logic\UserLogic;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;

	class ProjectTypeLogic extends RoyalwissDLogic{
		public function _initialize(){
			parent::_initialize();
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
			switch($type){
				case 'create': // 创建项目类型
					if(!UserLogic::isPermitted('SEVERAL-PROJECT_TYPE.CREATE')) return [
						'status'   => false,
						'message'  => '您没有创建项目类型的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ProjectTypeModel $project_type_model */
					$project_type_model = D('RoyalwissD/ProjectType');
					$str_obj            = new StringPlus();
					$meeting_id         = I('get.mid', 0, 'int');
					$post               = I('post.');
					$result             = $project_type_model->create(array_merge($post, [
						'mid'         => $meeting_id,
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'creator'     => Session::getCurrentUser(),
						'creatime'    => Time::getCurrentTime()
					]));

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify': // 修改项目类型
					if(!UserLogic::isPermitted('SEVERAL-PROJECT_TYPE.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改项目类型的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ProjectTypeModel $project_type_model */
					$project_type_model = D('RoyalwissD/ProjectType');
					$str_obj            = new StringPlus();
					$post               = I('post.');
					$meeting_id         = I('get.mid', 0, 'int');
					$project_type_id    = $post['id'];
					$result             = $project_type_model->modify([
						'mid' => $meeting_id,
						'id'  => $project_type_id
					], [
						'name'        => $post['name'],
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'comment'     => $post['comment']
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete': // 删除项目类型
					if(!UserLogic::isPermitted('SEVERAL-PROJECT_TYPE.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除项目类型的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\ProjectTypeModel $project_type_model */
					$project_type_model = D('RoyalwissD/ProjectType');
					$result             = $project_type_model->drop(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable': // 启用项目类型
					if(!UserLogic::isPermitted('SEVERAL-PROJECT_TYPE.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用项目类型的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\ProjectTypeModel $project_type_model */
					$project_type_model = D('RoyalwissD/ProjectType');
					$result             = $project_type_model->enable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用项目类型
					if(!UserLogic::isPermitted('SEVERAL-PROJECT_TYPE.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用项目类型的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\ProjectTypeModel $project_type_model */
					$project_type_model = D('RoyalwissD/ProjectType');
					$result             = $project_type_model->disable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
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
				case 'manage':
					foreach($data as $key => $project_type){
						$data[$key]['status_code'] = $project_type['status'];
						$data[$key]['status']      = GeneralModel::STATUS[$project_type['status']];
					}

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}
	}