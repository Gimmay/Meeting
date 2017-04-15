<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-14
	 * Time: 18:02
	 */
	namespace RoyalwissD\Controller;

	use CMS\Logic\PageLogic;
	use CMS\Logic\UserLogic;
	use CMS\Model\CMSModel;
	use RoyalwissD\Logic\GroupingLogic;
	use Think\Page;

	class GroupingController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
			$this->initMeetingID();
		}

		public function manage(){
			$grouping_logic = new GroupingLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $grouping_logic->handlerRequest($type);
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
			if(!UserLogic::isPermitted('SEVERAL-GROUPING.VIEW')) $this->error('您没有查看分组的权限');
			/** @var \RoyalwissD\Model\GroupingModel $group_model */
			$group_model = D('RoyalwissD/Grouping');
			// 获取列表数据
			$model_control_column = $this->getModelControl();
			$list                 = $group_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']             => ['!=', 2],
				$group_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			$page_object          = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows); // 分页
			$list       = $grouping_logic->setData('manage', $list);
			$pagination = $page_object->show();
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
			$this->display();
		}
	}