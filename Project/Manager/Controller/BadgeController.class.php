<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-10
	 * Time: 11:59
	 */
	namespace Manager\Controller;

	use Manager\Logic\BadgeLogic;

	class BadgeController extends ManagerController{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$badge_logic = new BadgeLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $badge_logic->handlerRequest($type);
				if($result['__ajax__']){
					unset($result['__ajax__']);
					echo json_encode($result);
				}
				else{
					unset($result['__ajax__']);
					if($result['status']) $this->success($result['message']);
					else $this->error($result['message'], '', 3);
				}
				exit;
			}
			$this->display();
		}
	}