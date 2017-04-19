<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-16
	 * Time: 10:55
	 */
	namespace RoyalwissD\Logic;

	use CMS\Controller\CMS;
	use CMS\Logic\Session;
	use CMS\Logic\UserLogic;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;

	class ReceivablesPayMethodLogic extends RoyalwissDLogic{
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
					if(!UserLogic::isPermitted('SEVERAL-PAY_METHOD.CREATE')) return [
						'status'   => false,
						'message'  => '您没有创建支付方式的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesPayMethodModel $pay_method_model */
					$pay_method_model = D('RoyalwissD/ReceivablesPayMethod');
					$str_obj          = new StringPlus();
					$post             = I('post.');
					$meeting_id       = I('get.mid', 0, 'int');
					$result           = $pay_method_model->create(array_merge($post, [
						'mid'         => $meeting_id,
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
						'creator'     => Session::getCurrentUser(),
						'creatime'    => Time::getCurrentTime()
					]));

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify': // 修改支付方式
					if(!UserLogic::isPermitted('SEVERAL-PAY_METHOD.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改支付方式的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesPayMethodModel $pay_method_model */
					$pay_method_model = D('RoyalwissD/ReceivablesPayMethod');
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
					if(!UserLogic::isPermitted('SEVERAL-PAY_METHOD.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除支付方式的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesPayMethodModel $pay_method_model */
					$pay_method_model = D('RoyalwissD/ReceivablesPayMethod');
					$id_str           = I('post.id', '');
					$meeting_id       = I('get.mid', 0, 'int');
					$id_arr           = explode(',', $id_str);
					$result           = $pay_method_model->drop(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable': // 启用支付方式
					if(!UserLogic::isPermitted('SEVERAL-PAY_METHOD.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用支付方式的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesPayMethodModel $pay_method_model */
					$pay_method_model = D('RoyalwissD/ReceivablesPayMethod');
					$meeting_id       = I('get.mid', 0, 'int');
					$id_str           = I('post.id', '');
					$id_arr           = explode(',', $id_str);
					$result           = $pay_method_model->enable(['id' => ['in', $id_arr], 'mid' => $meeting_id]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用支付方式
					if(!UserLogic::isPermitted('SEVERAL-PAY_METHOD.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用支付方式的权限',
						'__ajax__' => true
					];
					/** @var \RoyalwissD\Model\ReceivablesPayMethodModel $pay_method_model */
					$pay_method_model = D('RoyalwissD/ReceivablesPayMethod');
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
					$get  = $data['urlParam'];
					$data = $data['list'];
					$list = [];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					foreach($data as $key => $pay_method){
						// 1、筛选数据
						if(isset($keyword)){
							$found = 0;
							if($found == 0 && stripos($pay_method['name'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($pay_method['name_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						$pay_method['status_code'] = $pay_method['status'];
						$pay_method['status']      = GeneralModel::STATUS[$pay_method['status']];
						$list[]                    = $pay_method;
					}

					return $list;
				break;
				default:
					return $data;
				break;
			}
		}
	}