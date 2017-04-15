<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-14
	 * Time: 18:09
	 */
	namespace RoyalwissD\Controller;

	use CMS\Logic\PageLogic;
	use CMS\Logic\Session;
	use CMS\Logic\UserLogic;
	use CMS\Model\CMSModel;
	use Quasar\Utility\StringPlus;
	use RoyalwissD\Logic\ReceivablesLogic;
	use RoyalwissD\Logic\ReceivablesPayMethodLogic;
	use RoyalwissD\Logic\ReceivablesPosMachineLogic;
	use RoyalwissD\Model\ClientModel;
	use RoyalwissD\Model\ReceivablesDetailModel;
	use Think\Page;

	class ReceivablesController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
			$this->initMeetingID();
		}

		// todo 导出
		// todo 排序

		public function create(){
			if(IS_POST){
				$receivables_logic = new ReceivablesLogic();
				$type              = strtolower(I('post.requestType', ''));
				$result            = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.CREATE')) $this->error('您没有创建收款的权限');
			// 获取客户创建的控制字段
			/** @var \RoyalwissD\Model\ClientColumnControlModel $client_column_control_model */
			$client_column_control_model = D('RoyalwissD/ClientColumnControl');
			$column_list                 = $client_column_control_model->getClientControlledColumn($this->meetingID, $client_column_control_model::ACTION_WRITE);
			$this->assign('column_list', $column_list);
			// 输出创建客户必要的数据
			$this->assign('gender_list', ClientModel::GENDER);
			$this->assign('is_new_list', ClientModel::IS_NEW);
			$this->assign('type_list', ClientModel::TYPE);
			// 输出客户列表
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			$client_list  = $client_model->getSelectedList($this->meetingID, true);
			$this->assign('client_list', $client_list);
			// 输出用户列表
			/** @var \CMS\Model\UserModel $cms_user_model */
			$cms_user_model = D('CMS/User');
			$user_list      = $cms_user_model->getSelectedList();
			$this->assign('user_list', $user_list);
			$this->assign('current_user_id', Session::getCurrentUser());
			$this->assign('current_user_name', $_SESSION[Session::LOGIN_USER_NICKNAME] ? $_SESSION[Session::LOGIN_USER_NAME].' ('.$_SESSION[Session::LOGIN_USER_NICKNAME].')' : $_SESSION[Session::LOGIN_USER_NAME]);
			// 输出项目列表
			/** @var \RoyalwissD\Model\ProjectModel $project_model */
			$project_model = D('RoyalwissD/Project');
			$project_list  = $project_model->getSelectedList($this->meetingID);
			$this->assign('project_list', $project_list);
			// 输出POS机列表
			/** @var \RoyalwissD\Model\ReceivablesPosMachineModel $receivables_pos_machine_model */
			$receivables_pos_machine_model = D('RoyalwissD/ReceivablesPosMachine');
			$pos_machine_list              = $receivables_pos_machine_model->getSelectedList($this->meetingID);
			$this->assign('pos_machine_list', $pos_machine_list);
			// 输出支付方式
			/** @var \RoyalwissD\Model\ReceivablesPayMethodModel $receivables_pay_method_model */
			$receivables_pay_method_model = D('RoyalwissD/ReceivablesPayMethod');
			$pay_method_list              = $receivables_pay_method_model->getSelectedList($this->meetingID);
			$this->assign('pay_method_list', $pay_method_list);
			// 获取收款来源
			$this->assign('receivables_source', ReceivablesDetailModel::RECEIVABLES_SOURCE);
			// 生成单据号
			$str_obj            = new StringPlus();
			$receivables_number = $str_obj->makeRandomString(6, 'NW+');
			$this->assign('order_number', "SJ$receivables_number");
			// 根据URL参数获取客户信息
			$client_id = I('get.cid', 0, 'int');
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			if($client_model->fetch(['id' => $client_id])){
				$client = $client_model->getObject();
				$this->assign('current_client_id', $client['id']);
				$this->assign('current_client_name', "[$client[unit]] $client[name]");
			}
			// 输出代金券列表
			$this->display();
		}

		public function manage(){
			$receivables_logic = new ReceivablesLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $receivables_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			if(!UserLogic::isPermitted('SEVERAL-RECEIVABLES.VIEW')) $this->error('您没有查看收款的权限');
			/** @var \RoyalwissD\Model\ReceivablesOrderModel $receivables_order_model */
			$receivables_order_model = D('RoyalwissD/ReceivablesOrder');
			// 获取列表数据
			$model_control_column = $this->getModelControl();
			$total_list           = $list = $receivables_order_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']                         => ['!=', 2],
				$receivables_order_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			$list                 = $receivables_logic->setData('manage', [
				'list'     => $list,
				'urlParam' => I('get.')
			]);
			$page_object = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$statistics = $receivables_logic->setData('manage:statistics', [
				'list'      => $list,
				'mid'       => $this->meetingID,
				'totalList' => $total_list
			]);
			$pagination = $page_object->show();
			// 输出客户列表
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			$client_list  = $client_model->getSelectedList($this->meetingID, true);
			$this->assign('client_list', $client_list);
			// 输出用户列表
			/** @var \CMS\Model\UserModel $user_model */
			$user_model = D('CMS/User');
			$user_list  = $user_model->getSelectedList();
			$this->assign('user_list', $user_list);
			// 输出项目列表
			/** @var \RoyalwissD\Model\ProjectModel $project_model */
			$project_model = D('RoyalwissD/Project');
			$project_list  = $project_model->getSelectedList($this->meetingID);
			$this->assign('project_list', $project_list);
			// 输出POS机列表
			/** @var \RoyalwissD\Model\ReceivablesPosMachineModel $receivables_pos_machine_model */
			$receivables_pos_machine_model = D('RoyalwissD/ReceivablesPosMachine');
			$pos_machine_list              = $receivables_pos_machine_model->getSelectedList($this->meetingID);
			$this->assign('pos_machine_list', $pos_machine_list);
			// 输出支付方式
			/** @var \RoyalwissD\Model\ReceivablesPayMethodModel $receivables_pay_method_model */
			$receivables_pay_method_model = D('RoyalwissD/ReceivablesPayMethod');
			$pay_method_list              = $receivables_pay_method_model->getSelectedList($this->meetingID);
			$this->assign('pay_method_list', $pay_method_list);
			// 获取收款来源
			$this->assign('receivables_source', ReceivablesDetailModel::RECEIVABLES_SOURCE);
			// 获取打印收据logo
			/** @var \RoyalwissD\Model\MeetingConfigureModel $meeting_configure_model */
			$meeting_configure_model = D('RoyalwissD/MeetingConfigure');
			if($meeting_configure_model->fetch(['mid' => $this->meetingID])){
				$meeting_configure_record = $meeting_configure_model->getObject();
				$this->assign('receivables_order_logo', $meeting_configure_record['receivables_order_logo']);
			}
			$this->assign('list', $list);
			$this->assign('statistics', $statistics);
			$this->assign('pagination', $pagination);
			$this->display();
		}

		public function posMachine(){
			$pos_machine_logic = new ReceivablesPosMachineLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $pos_machine_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			if(!UserLogic::isPermitted('SEVERAL-POS_MACHINE.VIEW')) $this->error('您没有查看POS机的权限');
			/** @var \RoyalwissD\Model\ReceivablesPosMachineModel $pos_machine_model */
			$pos_machine_model    = D('RoyalwissD/ReceivablesPosMachine');
			$model_control_column = $this->getModelControl();
			$list                 = $pos_machine_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']                   => ['<>', 2],
				$pos_machine_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			$page_object          = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$list       = $pos_machine_logic->setData('manage', $list);
			$pagination = $page_object->show();
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
			$this->display();
		}

		public function payMethod(){
			$pay_method_logic = new ReceivablesPayMethodLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $pay_method_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					$url = $result['__return__'] ? $result['__return__'] : '';
					if($result['status']) $this->success($result['message'], $url);
					else $this->error($result['message'], $url, 3);
				}
				exit;
			}
			if(!UserLogic::isPermitted('SEVERAL-PAY_METHOD.VIEW')) $this->error('您没有查看支付方式的权限');
			/** @var \RoyalwissD\Model\ReceivablesPayMethodModel $pay_method_model */
			$pay_method_model     = D('RoyalwissD/ReceivablesPayMethod');
			$model_control_column = $this->getModelControl();
			$list                 = $pay_method_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']                  => ['<>', 2],
				$pay_method_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			$page_object          = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$list       = $pay_method_logic->setData('manage', $list);
			$pagination = $page_object->show();
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
			$this->display();
		}
	}