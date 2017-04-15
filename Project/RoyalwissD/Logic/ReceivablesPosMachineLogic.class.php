<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-16
	 * Time: 10:55
	 */
	namespace RoyalwissD\Logic;

	use CMS\Logic\Session;
	use CMS\Logic\UserLogic;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;

	class ReceivablesPosMachineLogic extends RoyalwissDLogic{
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
				case 'create': // 创建支付方式
					if(!UserLogic::isPermitted('SEVERAL-POS_MACHINE.CREATE')) return [
						'status'   => false,
						'message'  => '您没有创建POS机的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesPosMachineModel $pos_machine_model */
					$pos_machine_model = D('RoyalwissD/ReceivablesPosMachine');
					$str_obj           = new StringPlus();
					$post              = I('post.');
					$meeting_id        = I('get.mid', 0, 'int');
					$result            = $pos_machine_model->create(array_merge($post, [
						'mid'         => $meeting_id,
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'creator'     => Session::getCurrentUser(),
						'creatime'    => Time::getCurrentTime()
					]));

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify': // 修改支付方式
					if(!UserLogic::isPermitted('SEVERAL-POS_MACHINE.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改POS机的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesPosMachineModel $pay_method_model */
					$pay_method_model = D('RoyalwissD/ReceivablesPosMachine');
					$str_obj          = new StringPlus();
					$meeting_id       = I('get.mid', 0, 'int');
					$post             = I('post.');
					$pay_method_id    = $post['id'];
					$result           = $pay_method_model->modify([
						'mid' => $meeting_id,
						'id'  => $pay_method_id
					], [
						'name'        => $post['name'],
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'comment'     => $post['comment']
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete': // 删除支付方式
					if(!UserLogic::isPermitted('SEVERAL-POS_MACHINE.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除POS机的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesPosMachineModel $pay_method_model */
					$pay_method_model = D('RoyalwissD/ReceivablesPosMachine');
					$id_str           = I('post.id', '');
					$meeting_id       = I('get.mid', 0, 'int');
					$id_arr           = explode(',', $id_str);
					$result           = $pay_method_model->drop(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable': // 启用支付方式
					if(!UserLogic::isPermitted('SEVERAL-POS_MACHINE.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用POS机的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesPosMachineModel $pay_method_model */
					$pay_method_model = D('RoyalwissD/ReceivablesPosMachine');
					$meeting_id       = I('get.mid', 0, 'int');
					$id_str           = I('post.id', '');
					$id_arr           = explode(',', $id_str);
					$result           = $pay_method_model->enable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用支付方式
					if(!UserLogic::isPermitted('SEVERAL-POS_MACHINE.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用POS机的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesPosMachineModel $pay_method_model */
					$pay_method_model = D('RoyalwissD/ReceivablesPosMachine');
					$meeting_id       = I('get.mid', 0, 'int');
					$id_str           = I('post.id', '');
					$id_arr           = explode(',', $id_str);
					$result           = $pay_method_model->disable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

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
					foreach($data as $key => $pos_machine){
						$data[$key]['status_code'] = $pos_machine['status'];
						$data[$key]['status']      = GeneralModel::STATUS[$pos_machine['status']];
					}

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}
	}