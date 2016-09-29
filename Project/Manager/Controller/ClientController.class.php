<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-8-11
	 * Time: 9:54
	 */
	namespace Manager\Controller;

	use Core\Logic\WxCorpLogic;
	use Manager\Logic\ClientLogic;
	use Manager\Logic\ExcelLogic;
	use Manager\Logic\JoinLogic;
	use Think\Page;

	class ClientController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			if(IS_POST){
				$logic  = new ClientLogic();
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
				if($result === -1){
				}
				else{
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$mid = I('get.mid', 0, 'int');
			/** @var \Core\Model\ClientModel $core_model */
			$core_model = D('Core/Client');
			$options    = [];
			if(I('get.signed', 0, 'int') == 1) $options['sign_status'] = 1;
			if(I('get.reviewed', 0, 'int') == 1) $options['audit_status'] = 1;
			$list_total = $core_model->listClient(0, array_merge([
				'keyword' => I('get.keyword', ''),
				'status'  => 'not deleted',
				'mid'     => $mid
			], $options));
			/* 分页设置 */
			$page_object = new Page($list_total, C('PAGE_RECORD_COUNT'));
			\ThinkPHP\Quasar\Page\setTheme1($page_object);
			$page_show = $page_object->show();
			/* 当前页的员工记录列表 */
			$client_list = $core_model->listClient(2, array_merge([
				'keyword' => I('get.keyword', ''),
				'_limit'  => $page_object->firstRow.','.$page_object->listRows,
				'_order'  => I('get.column', 'main.creatime').' '.I('get.sort', 'desc'),
				'status'  => 'not deleted',
				'mid'     => $mid
			], $options));
			/* 统计数据 */
			$signed_count  = $core_model->listClient(0, [
				'mid'         => $mid,
				'sign_status' => 1,
				'status'      => 'not deleted'
			]);
			$audited_count = $core_model->listClient(0, [
				'mid'          => $mid,
				'audit_status' => 1,
				'status'       => 'not deleted'
			]);
			$all_count     = $core_model->listClient(0, [
				'mid'    => $mid,
				'status' => 'not deleted'
			]);
			$this->assign('statistics', [
				'signed'  => $signed_count,
				'audited' => $audited_count,
				'total'   => $all_count,
			]);
			$this->assign('list', $client_list);
			$this->assign('page_show', $page_show);
			$this->display();
		}

		public function create(){
			if(IS_POST){
				/** @var \Core\Model\JoinModel $join_model */
				$join_model = D('Core/Join');
				/** @var \Core\Model\WeixinIDModel $weixin_model */
				$weixin_model = D('Core/WeixinID');
				$logic        = new WxCorpLogic();
				$wx_list      = $logic->getAllUserList(); //查出wx接口获取的所有用户信息
				/** @var \Core\Model\ClientModel $model */
				$model  = D('Core/Client'); //实例化表
				$data   = I('post.', '');         //获取表单数据
				$result = $model->createClient($data); //用model 创建表插入数据
				if($result['status']){
					$data['cid']      = $result['id'];
					$data['mid']      = I('get.mid', 0, 'int');
					$data['creatime'] = time();
					$data['creator']  = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					C('TOKEN_ON', false);
					$join_model->createRecord($data);
					$this->success($result['message'], U('manage', ['mid' => I('get.mid', 0, 'int')]));
					foreach($wx_list as $k1 => $v1){
						if($v1['mobile'] == $data['mobile']){
							C('TOKEN_ON', false);
							$department = '';
							foreach($v1['department'] as $v3) $department .= $v3.',';
							$department         = trim($department, ',');
							$data               = [];
							$data['otype']      = 1;    //对象类型
							$data['oid']        = $result['id'];    //对象ID
							$data['userid']     = 1;    //
							$data['department'] = $department;    //部门ID
							$data['weixin_id']  = $v1['userid'];    //微信ID
							$data['mobile']     = $v1['mobile'];    //手机号码
							$data['avatar']     = $v1['avatar'];    //头像地址
							$data['gender']     = $v1['gender'];    //性别
							$data['nickname']   = $v1['name'];    //昵称
							$data['creatime']   = time();    //创建时间
							$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');    //当前创建者
							$weixin_model->createRecord($data);    //插入数据
						}
					}
				}
				else $this->error($result['message']);
				exit;
			}
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
			$this->assign('employee_list', $employee_list);
			$this->display();
		}

		public function exportClientDataTemplate(){
			/** @var \Manager\Model\ClientModel $client_model */
			$client_model = D('Client');
			$header       = $client_model->getColumn(true);
			$logic        = new ExcelLogic();
			$logic->exportCustomData($header, [
				'fileName'    => '导入客户数据模板',
				'title'       => '导入客户数据模板',
				'subject'     => '导入客户数据模板',
				'description' => '吉美会议系统导入客户数据模板',
				'company'     => '吉美集团',
				'hasHead'     => true
			]);
		}

		public function importClientDataTest(){
			if(IS_POST){
				$logic  = new ExcelLogic();
				$result = $logic->importClientData($_FILES);
				echo json_encode($result);
				exit;
			}
			echo "<form action='' method='post' enctype='multipart/form-data'>
	<input type='file' name='excel'>
	<button type='submit'>提交</button>
</form>";
		}

		public function createClientTest(){
			$logic            = new ClientLogic();
			$upload_record_id = 75;
			//$upload_record_id = I('post.id', 0, 'int');
			//$map    = I('post.map', '');
			$map    = [
				'data'   => [15, 13],
				'column' => [20, 21]
			];
			$result = $logic->createClientFromExcel($upload_record_id, $map);
			print_r($result);
			exit;
		}

		public function qrcodeTest(){
			$logic       = new JoinLogic();
			$client_list = [
				['id' => 224],
				['id' => 226]
			];
			$data        = [
				'mid'               => 63,
				'registration_time' => date('Y-m-d'),
				'invitor_id'        => 1
			];
			$result      = $logic->makeQRCode($client_list, $data);
			print_r($result);
			exit;
		}

		public function alter(){
			/** @var \Core\Model\ClientModel $model */
			$model      = D('Core/Client');
			$data['id'] = I('get.id', 0, 'int');
			$info       = $model->findClient(1, $data);
			/** @var \Manager\Model\EmployeeModel $employee_model */
			$employee_model = D('Employee');
			$employee_list  = $employee_model->getEmployeeSelectList();
			$this->assign('employee_list', $employee_list);
			$this->assign('info', $info);
			$this->display();
		}
	}