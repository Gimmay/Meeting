<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-15
	 * Time: 9:49
	 */
	namespace RoyalwissD\Logic;

	use CMS\Controller\CMS;
	use CMS\Logic\Session;
	use CMS\Logic\UserLogic;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;
	use RoyalwissD\Model\ProjectInventoryModel;

	class ProjectLogic extends RoyalwissDLogic{
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
				case 'create': // 创建项目
					if(!UserLogic::isPermitted('SEVERAL-PROJECT.CREATE')) return [
						'status'   => false,
						'message'  => '您没有创建项目的权限',
						'__ajax__' => true
					];
					$str_obj = new StringPlus();
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$post          = I('post.');
					$meeting_id    = I('get.mid', 0, 'int');
					$price         = $post['price'];
					unset($post['price']);
					// 1、创建项目记录
					$result = $project_model->create(array_merge($post, [
						'name_pinyin'    => $str_obj->getPinyin($post['name'], true, ''),
						'mid'            => $meeting_id,
						'total'          => $post['stock'],
						'price'          => $price == '' ? 0 : $price,
						'creator'        => Session::getCurrentUser(),
						'creatime'       => Time::getCurrentTime(),
						'is_stock_limit' => $post['is_stock_limit'] ? 1 : 0
					]));
					if($result['status'] && isset($post['is_stock_limit'])){
						// 2、创建库存入库明细
						/** @var \RoyalwissD\Model\ProjectInventoryModel $project_inventory_model */
						$project_inventory_model = D('RoyalwissD/ProjectInventory');
						$project_inventory_model->create([
							'creator'    => Session::getCurrentUser(),
							'creatime'   => Time::getCurrentTime(),
							'mid'        => $meeting_id,
							'project_id' => $result['id'],
							'number'     => $post['stock'],
							'type'       => $project_inventory_model::TYPE_IN,
							'comment'    => $post['comment']
						]);
					}

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify': // 修改项目信息（不包括库存）
					if(!UserLogic::isPermitted('SEVERAL-PROJECT.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改项目的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$str_obj       = new StringPlus();
					$project_id    = I('post.id', 0, 'int');
					$post          = I('post.');
					$result        = $project_model->modify(['id' => $project_id], [
						'name'        => $post['name'],
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'comment'     => $post['comment'],
						'type'        => $post['type'],
						'price'       => $post['price'] == '' ? 0 : $post['price']
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify_stock': // 修改库存信息
					if(!UserLogic::isPermitted('SEVERAL-PROJECT.UPDATE_STOCK')) return [
						'status'   => false,
						'message'  => '您没有更新项目库存的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$meeting_id    = I('get.mid', 0, 'int');
					$post          = I('post.');
					$project_id    = $post['id'];
					if(isset($post['is_stock_limit']) && $post['is_stock_limit']){
						// 1、修改信息
						$project_model->modify(['id' => $project_id], ['is_stock_limit' => 1]);
						// 2、判断修改库存
						if($post['type'] == 1){
							$result = $project_model->input($project_id, $post['number']);
							$type   = ProjectInventoryModel::TYPE_IN;
						}
						else{
							$result = $project_model->output($project_id, $post['number']);
							$type   = ProjectInventoryModel::TYPE_OUT;
						}
						if(!$result['status']) return array_merge($result, ['__ajax__' => true]);
						// 3、新增库存出入库记录
						/** @var \RoyalwissD\Model\ProjectInventoryModel $project_inventory_model */
						$project_inventory_model = D('RoyalwissD/ProjectInventory');
						$project_inventory_model->create([
							'creator'    => Session::getCurrentUser(),
							'creatime'   => Time::getCurrentTime(),
							'mid'        => $meeting_id,
							'project_id' => $project_id,
							'number'     => $post['number'],
							'type'       => $type,
							'comment'    => $post['comment']
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
					else{
						// 1、修改信息
						$result = $project_model->modify(['id' => $project_id], [
							'is_stock_limit' => 0,
							'total'          => 0,
							'stock'          => 0
						]);
						// 2、新增出入库记录
						/** @var \RoyalwissD\Model\ProjectInventoryModel $project_inventory_model */
						$project_inventory_model = D('RoyalwissD/ProjectInventory');
						$project_inventory_model->create([
							'creator'    => Session::getCurrentUser(),
							'creatime'   => Time::getCurrentTime(),
							'mid'        => $meeting_id,
							'project_id' => $project_id,
							'number'     => 0,
							'type'       => $project_inventory_model::TYPE_CLEAN,
							'comment'    => $post['comment']
						]);

						return array_merge($result, ['__ajax__' => true]);
					}
				break;
				case 'delete': // 删除项目
					if(!UserLogic::isPermitted('SEVERAL-PROJECT.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除项目的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$result        = $project_model->drop(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable': // 启用项目
					if(!UserLogic::isPermitted('SEVERAL-PROJECT.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用项目的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$result        = $project_model->enable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用项目
					if(!UserLogic::isPermitted('SEVERAL-PROJECT.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用项目的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$result        = $project_model->disable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_inventory_history':
					$project_id = I('post.id', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\ProjectInventoryModel $project_inventory_model */
					$project_inventory_model = D('RoyalwissD/ProjectInventory');
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					if(!$project_model->fetch(['id' => $project_id])) return ['__ajax__' => true];
					$project = $project_model->getObject();
					$list    = $project_inventory_model->where([
						'project_id' => $project_id,
						'mid'        => $meeting_id
					])->select();
					foreach($list as $key => $record){
						$list[$key]['type_code'] = $record['type'];
						$list[$key]['type']      = $project_inventory_model::TYPE[$record['type']];
					}

					return array_merge(['list' => $list, 'project' => $project['name'], '__ajax__' => true]);
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
					$get  = $data['urlParam'];
					$data = $data['list'];
					$list = [];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					foreach($data as $key => $project){
						// 1、筛选数据
						if(isset($keyword)){
							$found = 0;
							if($found == 0 && stripos($project['name'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($project['name_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						$project['status_code'] = $project['status'];
						$project['status']      = GeneralModel::STATUS[$project['status']];
						$list[]                 = $project;
					}

					return $list;
				break;
				default:
					return $data;
				break;
			}
		}
	}