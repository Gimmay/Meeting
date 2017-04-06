<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-15
	 * Time: 9:49
	 */
	namespace RoyalwissD\Logic;

	use CMS\Logic\Session;
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
					$str_obj = new StringPlus();
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$post          = I('post.');
					$meeting_id    = I('get.mid', 0, 'int');
					// 1、创建项目记录
					$result = $project_model->create(array_merge($post, [
						'name_pinyin'    => $str_obj->getPinyin($post['name'], true, ''),
						'mid'            => $meeting_id,
						'total'          => $post['stock'],
						'creator'        => Session::getCurrentUser(),
						'creatime'       => Time::getCurrentTime(),
						'is_stock_limit' => $post['is_stock_limit'] ? 1 : 0
					]));
					if($result['status'] && isset($post['is_stock_limit'])){
						// 2、创建库存入库明细
						/** @var \RoyalwissD\Model\ProjectInventoryModel $project_inventory_model */
						$project_inventory_model = D('RoyalwissD/ProjectInventory');
						C('TOKEN_ON', false);
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
						'price'       => $post['price']
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify_stock': // 修改库存信息
					$post = I('post.');
					if(isset($post['is_stock_limit']) && $post['is_stock_limit']){
						// 1、判断修改库存
						/** @var \RoyalwissD\Model\ProjectModel $project_model */
						$project_model = D('RoyalwissD/Project');
						$project_id    = $post['id'];
						$meeting_id    = I('get.mid', 0, 'int');
						if($post['type'] == 1){
							$result = $project_model->input($project_id, $post['number']);
							$type   = ProjectInventoryModel::TYPE_IN;
						}
						else{
							$result = $project_model->output($project_id, $post['number']);
							$type   = ProjectInventoryModel::TYPE_OUT;
						}
						if($result['status']){
							// 2、新增库存出入库记录
							/** @var \RoyalwissD\Model\ProjectInventoryModel $project_inventory_model */
							$project_inventory_model = D('RoyalwissD/ProjectInventory');
							C('TOKEN_ON', false);
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
						else return array_merge($result, ['__ajax__' => true]);
					}
					else return ['status' => false, 'message' => '未做任何修改', '__ajax__' => true];
				break;
				case 'delete': // 删除项目
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$result        = $project_model->drop(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable': // 启用项目
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$result        = $project_model->enable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用项目
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\ProjectModel $project_model */
					$project_model = D('RoyalwissD/Project');
					$result        = $project_model->disable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

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
					foreach($data as $key => $project){
						$data[$key]['status_code'] = $project['status'];
						$data[$key]['status']      = GeneralModel::STATUS[$project['status']];
					}

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}
	}