<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-26
	 * Time: 16:13
	 */

	namespace CMS\Controller;

	use CMS\Logic\DepartmentLogic;
	use CMS\Model\CMSModel;

	class DepartmentController extends CMS{
		public function _initialize(){
			parent::_initialize();
		}

		public function manage(){
			$department_logic = new DepartmentLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $department_logic->handlerRequest($type);
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
			/** @var \CMS\Model\DepartmentModel $department_model */
			$department_model = D('CMS/Department');
			$list             = $department_model->getList([
				CMSModel::CONTROL_COLUMN_PARAMETER['status'] => ['=', 1]
			]);
			$list             = $department_logic->setData('manage:get_tree', [
				'list'     => $list,
				'urlParam' => I('get.')
			]);
			$this->assign('department_tree', $list);
			$this->display();
		}
	}