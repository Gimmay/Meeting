<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-11-29
	 * Time: 14:16
	 */
	namespace Open\Controller;

	use Open\Logic\MobileLogic;

	class MobileController extends OpenController{
		public function _initialize(){
			parent::_initialize();
		}

		public function invite(){
			if(IS_POST){
				$logic  = new MobileLogic();
				$type   = I('post.requestType', '');
				$result = $logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message'], U('manage'));
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$this->display();
		}
	}