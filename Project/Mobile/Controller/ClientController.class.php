<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:34
	 */
	namespace Mobile\Controller;

	use Core\Logic\WxCorpLogic;
	use Mobile\Logic\ClientLogic;

	class ClientController extends MobileController{
		public function _initialize(){
			parent::_initialize();
		}

		public function myCenter(){
			$logic = new ClientLogic();
			if(IS_POST){
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
			$id   = I('get.id', 0, 'int');
			$mid   = I('get.mid', 0, 'int');
			$info = $logic->getInformation($id, $mid);
			$this->assign('info', $info);
			$this->display();
		}

		public function manage(){
			$logic = new ClientLogic();
			if(IS_POST){
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
			$wxcorp_logic   = new WxCorpLogic();
			$weixin_user_id = $wxcorp_logic->getUserID();
			$result         = $logic->isSigner($weixin_user_id);
			if(!$result) $this->redirect('blank');
			$id   = I('get.id', 0, 'int');
			$mid   = I('get.mid', 0, 'int');
			$info = $logic->getInformation($id, $mid);
			$this->assign('info', $info);
			$this->display();
		}

		public function verify(){
			//header("Location: $_COOKIE[MOBILE_SIGN_PAGE]&weixin_id=$weixin_user_id");
		}

		public function blank(){
		}
	}