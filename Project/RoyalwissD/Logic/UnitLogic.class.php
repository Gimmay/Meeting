<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-17
	 * Time: 14:48
	 */
	namespace RoyalwissD\Logic;

	use CMS\Controller\CMS;
	use CMS\Logic\UserLogic;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;
	use RoyalwissD\Model\UnitModel;

	class UnitLogic extends RoyalwissDLogic{
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
				case 'modify':
					$post    = I('post.');
					$unit_id = I('post.id', 0, 'int');
					/** @var \RoyalwissD\Model\UnitModel $unit_model */
					$unit_model = D('RoyalwissD/Unit');
					$result     = $unit_model->modify(['id' => $unit_id], $post);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_detail':
					$unit_id = I('post.id', 0, 'int');
					/** @var \RoyalwissD\Model\UnitModel $unit_model */
					$unit_model = D('RoyalwissD/Unit');
					if(!$unit_model->fetch(['id' => $unit_id])) return ['__ajax__' => true];
					$record = $unit_model->getObject();

					return array_merge($record, ['__ajax__' => true]);
				break;
				case 'enable': // 启用会所
					if(!UserLogic::isPermitted('SEVERAL-UNIT.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用会所的权限',
						'__ajax__' => true
					];
					$id_str = I('post.id', '');
					$id_arr = explode(',', $id_str);
					/** @var \RoyalwissD\Model\UnitModel $unit_model */
					$unit_model = D('RoyalwissD/Unit');
					$result     = $unit_model->enable(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用会所
					if(!UserLogic::isPermitted('SEVERAL-UNIT.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用会所的权限',
						'__ajax__' => true
					];
					$id_str = I('post.id', '');
					$id_arr = explode(',', $id_str);
					/** @var \RoyalwissD\Model\UnitModel $unit_model */
					$unit_model = D('RoyalwissD/Unit');
					$result     = $unit_model->disable(['id' => ['in', $id_arr]]);

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
				case 'unit:set_data':
					$list = [];
					$get  = $data['urlParam'];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					foreach($data['list'] as $index => $unit){
						// 1、筛选数据
						if(isset($keyword)){
							$found = 0;
							if($found == 0 && stripos($unit['name'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($unit['area'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($unit['name_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						// 2、映射替换
						$unit['status_code'] = $unit['status'];
						$unit['status']      = GeneralModel::STATUS[$unit['status']];
						$unit['is_new_code'] = $unit['is_new'];
						$unit['is_new']      = UnitModel::IS_NEW[$unit['is_new']];
						$list[]              = $unit;
					}

					return $list;
				break;
				default:
					return $data;
				break;
			}
		}
	}