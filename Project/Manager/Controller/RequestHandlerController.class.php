<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-16
	 * Time: 11:10
	 */
	namespace Manager\Controller;

	use Manager\Logic\RequestHandlerLogic;

	class RequestHandlerController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function getHandler(){
			$request_type = I('get.requestType', '');
			if($request_type == ''){
				$this->error('缺少必要的请求参数');
				exit;
			}
			$logic  = new RequestHandlerLogic();
			$result = $logic->handlerRequest($request_type);
			if($result['__ajax__']){
				unset($result['__ajax__']);
				echo json_encode($result);
			}
			else{
				if(isset($result['__return__'])) $redirect_url = $result['__return__'];
				else $redirect_url = '';
				unset($result['__ajax__']);
				unset($result['__return__']);
				if($result['status']) $this->success($result['message'], $redirect_url);
				else $this->error($result['message'], $redirect_url);
			}
		}
	}