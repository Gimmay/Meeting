<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-16
	 * Time: 11:11
	 */
	namespace Manager\Logic;

	class RequestHandlerLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type){
			switch($type){
				case 'read_system_message':
					/** @var \Core\Model\SystemMessageModel $system_message_model */
					$system_message_model = D('Core/SystemMessage');
					$result               = $system_message_model->readMessage([
						'status' => 0,
						'id'     => I('get.id', 0, 'int')
					]);
					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'cancel_assign_message':
					if($this->permissionList['MESSAGE.SELECT']){
						/** @var \Core\Model\AssignMessageModel $assign_message_model */
						$assign_message_model = D('Core/AssignMessage');
						$result               = $assign_message_model->dropRecord([
							'mid'        => I('get.mid', 0, 'int'),
							'message_id' => I('get.message_id', 0, 'int'),
							'type'       => I('get.type', 0, 'int')
						]);
						if($result['status']) return ['status' => true, 'message' => '取消成功', '__ajax__' => false];
						else return ['status' => false, 'message' => '取消失败', '__ajax__' => false];
					}
					else return ['message' => '您没有取消分配消息的权限', 'status' => false, '__ajax__' => false];
				break;
				case 'get:disable_employee':
				case 'get:enable_employee':
					/** @var \Core\Model\EmployeeModel $model */
					$model  = D('Core/Employee');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterEmployee(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_role':
				case 'get:enable_role':
					/** @var \Core\Model\RoleModel $model */
					$model  = D('Core/Role');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterRole(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_meeting':
				case 'get:enable_meeting':
					/** @var \Core\Model\MeetingModel $model */
					$model  = D('Core/Meeting');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterMeeting(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_client':
				case 'get:enable_client':
					/** @var \Core\Model\ClientModel $model */
					$model = D('Core/Client');
					/** @var \Core\Model\JoinModel $join_model */
					$join_model = D('Core/Join');
					$id         = I('get.id', 0, 'int');
					$jid        = I('get.jid', 0, 'int');
					$status     = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result  = $model->alterClient(['id' => $id], ['status' => $status]);
					$result2 = $join_model->alterRecord(['id' => $jid], ['status' => $status]);

					return [
						'status'   => $result['status'] || $result2['status'],
						'message'  => $result['message'],
						'__ajax__' => false
					];
				break;
				case 'get:disable_sign_place':
				case 'get:enable_sign_place':
					/** @var \Core\Model\SignPlaceModel $model */
					$model  = D('Core/SignPlace');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterRecord(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_pay_method':
				case 'get:enable_pay_method':
					/** @var \Core\Model\PayMethodModel $model */
					$model  = D('Core/PayMethod');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterRecord(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_receivables_type':
				case 'get:enable_receivables_type':
					/** @var \Core\Model\ReceivablesTypeModel $model */
					$model  = D('Core/ReceivablesType');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterRecord(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_pos_machine':
				case 'get:enable_pos_machine':
					/** @var \Core\Model\PosMachineModel $model */
					$model  = D('Core/PosMachine');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterRecord(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_coupon':
				case 'get:enable_coupon':
					/** @var \Core\Model\CouponModel $model */
					$model  = D('Core/Coupon');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterCoupon(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_message':
				case 'get:enable_message':
					/** @var \Core\Model\MessageModel $model */
					$model  = D('Core/Message');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterMessage(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_car':
				case 'get:enable_car':
					/** @var \Core\Model\CarModel $model */
					$model  = D('Core/Car');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterCar(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				case 'get:disable_hotel':
				case 'get:enable_hotel':
					/** @var \Core\Model\HotelModel $model */
					$model  = D('Core/Hotel');
					$id     = I('get.id', 0, 'int');
					$status = strpos($type, 'disable') === false ? (strpos($type, 'enable') === false ? 0 : 1) : 0;
					C('TOKEN_ON', false);
					$result = $model->alterHotel(['id' => $id], ['status' => $status]);

					return array_merge($result, ['__ajax__' => false]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数'];
				break;
			}
		}
	}