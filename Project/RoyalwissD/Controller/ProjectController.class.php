<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-14
	 * Time: 18:01
	 */
	namespace RoyalwissD\Controller;

	use CMS\Logic\PageLogic;
	use CMS\Model\CMSModel;
	use RoyalwissD\Logic\ProjectLogic;
	use RoyalwissD\Logic\ProjectTypeLogic;
	use Think\Page;

	class ProjectController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
			$this->initMeetingID();
		}

		public function manage(){
			$project_logic = new ProjectLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $project_logic->handlerRequest($type);
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
			// 获取项目类型
			/** @var \RoyalwissD\Model\ProjectTypeModel $project_type_model */
			$project_type_model = D('RoyalwissD/ProjectType');
			$project_type_list  = $project_type_model->getList([
				CMSModel::CONTROL_COLUMN_PARAMETER['status']                    => ['=', 1],
				CMSModel::CONTROL_COLUMN_PARAMETER['order']                     => ' name_pinyin ',
				$project_type_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]);
			// 获取项目数据
			/** @var \RoyalwissD\Model\ProjectModel $project_model */
			$project_model = D('RoyalwissD/Project');
			$model_control_column = $this->getModelControl();
			$list                 = $project_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']                    => ['<>', 2],
				$project_type_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			$page_object          = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$pagination = $page_object->show();
			$list       = $project_logic->setData('manage', $list);
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
			$this->assign('project_type_list', $project_type_list);
			$this->display();
		}

		public function type(){
			$project_type_logic = new ProjectTypeLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $project_type_logic->handlerRequest($type);
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
			/** @var \RoyalwissD\Model\ProjectTypeModel $project_type_model */
			$project_type_model = D('RoyalwissD/ProjectType');
			$model_control_column = $this->getModelControl();
			$list                 = $project_type_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']                    => ['<>', 2],
				$project_type_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			$page_object          = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$list       = $project_type_logic->setData('manage', $list);
			$pagination = $page_object->show();
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
			$this->display();
		}
	}