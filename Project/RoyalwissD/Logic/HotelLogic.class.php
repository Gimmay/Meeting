<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-28
	 * Time: 15:04
	 */
	namespace RoyalwissD\Logic;

	use CMS\Logic\Session;
	use CMS\Logic\UserLogic;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;

	class HotelLogic extends RoyalwissDLogic{
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
					if(!UserLogic::isPermitted('SEVERAL-HOTEL.CREATE')) return [
						'status'   => false,
						'message'  => '您没有创建酒店的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$post       = I('post.');
					/** @var \RoyalwissD\Model\HotelModel $hotel_model */
					$hotel_model = D('RoyalwissD/Hotel');
					$str_obj     = new StringPlus();
					unset($post['brief']);
					$result = $hotel_model->create(array_merge($post, [
						'mid'         => $meeting_id,
						'creatime'    => Time::getCurrentTime(),
						'creator'     => Session::getCurrentUser(),
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'brief'       => $_POST['brief']
					]));

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_hotel':
					$hotel_id   = I('post.id', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\HotelModel $hotel_model */
					$hotel_model = D('RoyalwissD/Hotel');
					if(!$hotel_model->fetch(['mid' => $meeting_id, 'id' => $hotel_id])) return [
						'status'   => false,
						'message'  => '找不到酒店',
						'__ajax__' => true
					];
					$record = $hotel_model->getObject();

					return array_merge($record, ['__ajax__' => true]);
				break;
				case 'modify':
					if(!UserLogic::isPermitted('SEVERAL-HOTEL.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改酒店的权限',
						'__ajax__' => true
					];
					$hotel_id   = I('post.id', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					$post       = I('post.');
					/** @var \RoyalwissD\Model\HotelModel $hotel_model */
					$hotel_model = D('RoyalwissD/Hotel');
					$str_obj     = new StringPlus();
					unset($post['brief']);
					$result = $hotel_model->modify(['id' => $hotel_id, 'mid' => $meeting_id], array_merge($post, [
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'brief'       => $_POST['brief']
					]));

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete': // 删除项目
					if(!UserLogic::isPermitted('SEVERAL-HOTEL.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除酒店的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\HotelModel $hotel_model */
					$hotel_model = D('RoyalwissD/Hotel');
					$result      = $hotel_model->drop(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable': // 启用项目
					if(!UserLogic::isPermitted('SEVERAL-HOTEL.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有删除酒店的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\HotelModel $hotel_model */
					$hotel_model = D('RoyalwissD/Hotel');
					$result      = $hotel_model->enable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用项目
					if(!UserLogic::isPermitted('SEVERAL-HOTEL.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用酒店的权限',
						'__ajax__' => true
					];
					$id_str     = I('post.id', '');
					$meeting_id = I('get.mid', 0, 'int');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\HotelModel $hotel_model */
					$hotel_model = D('RoyalwissD/Hotel');
					$result      = $hotel_model->disable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

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