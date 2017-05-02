<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-26
	 * Time: 16:14
	 */

	namespace CMS\Logic;

	use CMS\Controller\CMS;
	use General\Logic\Tree;

	class DepartmentLogic extends CMSLogic{
		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'create':
				break;
				case 'modify':
				break;
				case 'delete':
					if(!UserLogic::isPermitted('GENERAL-DEPARTMENT.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除部门的权限',
						'__ajax__' => true
					];
					$id_str = I('post.id', '');
					$id_arr = explode(',', $id_str);
					/** @var \General\Model\DepartmentModel $department_model */
					$department_model = D('General/Department');
					$result           = $department_model->delete(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable':
					if(!UserLogic::isPermitted('GENERAL-DEPARTMENT.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用部门的权限',
						'__ajax__' => true
					];
					$id_str = I('post.id', '');
					$id_arr = explode(',', $id_str);
					/** @var \General\Model\DepartmentModel $department_model */
					$department_model = D('General/Department');
					$result           = $department_model->enable(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable':
					if(!UserLogic::isPermitted('GENERAL-DEPARTMENT.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用部门的权限',
						'__ajax__' => true
					];
					$id_str = I('post.id', '');
					$id_arr = explode(',', $id_str);
					/** @var \General\Model\DepartmentModel $department_model */
					$department_model = D('General/Department');
					$result           = $department_model->disable(['id' => ['in', $id_arr]]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true];
				break;
			}
		}

		public function setData($type, $data){
			switch($type){
				case 'manage:get_tree':
					$get    = $data['urlParam'];
					$list   = $data['list'];
					$result = [];
					// 若指定了关键字
					if(isset($get[CMS::URL_CONTROL_PARAMETER['keyword']])) $keyword = $get[CMS::URL_CONTROL_PARAMETER['keyword']];
					foreach($list as $department){
						// 1、筛选数据
						if(isset($keyword)){
							$found = 0;
							if($found == 0 && stripos($department['name'], $keyword) !== false) $found = 1;
							if($found == 0 && stripos($department['name_pinyin'], $keyword) !== false) $found = 1;
							if($found == 0) continue;
						}
						$result[] = $department;
					}
					$tree_obj = new Tree();
					$result   = $tree_obj->makeTree($result);

					return $result;
				break;
				default:
				break;
			}
		}
	}