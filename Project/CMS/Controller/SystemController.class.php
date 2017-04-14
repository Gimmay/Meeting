<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-11
	 * Time: 14:51
	 */
	namespace CMS\Controller;

	use CMS\Logic\ApiConfigureLogic;
	use CMS\Logic\PageLogic;
	use CMS\Logic\SystemLogic;
	use CMS\Logic\UserLogic;
	use General\Logic\MeetingLogic;
	use Think\Page;

	class SystemController extends CMS{
		public function _initialize(){
			parent::_initialize();
		}

		public function configure(){
			$logic = new SystemLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $logic->handlerRequest($type);
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
			$this->display();
		}

		public function apiConfigure(){
			$api_configure_logic = new ApiConfigureLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $api_configure_logic->handlerRequest($type, [
					'username' => I('post.username', ''),
					'password' => I('post.password', '')
				]);
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
			if(!UserLogic::isPermitted('GENERAL-API_CONFIGURE.VIEW')) $this->error('您没有查看接口配置的权限');
			/** @var \CMS\Model\ApiConfigureModel $api_configure_model */
			$api_configure_model = D('CMS/ApiConfigure');
			// 获取可见的会议类型
			$meeting_logic     = new MeetingLogic();
			$meeting_type_list = $meeting_logic->getViewedMeetingTypeList();
			// 输出会议类型数据
			$viewed_meeting_type = $api_configure_logic->setData('get_meeting_type', $meeting_type_list);
			$model_control_column = $this->getModelControl();
			$option               = [];
			$list                 = $api_configure_model->getList(array_merge($model_control_column, $option, [
				$api_configure_model::CONTROL_COLUMN_PARAMETER['status']           => ['<>', 2],
				$api_configure_model::CONTROL_COLUMN_PARAMETER_SELF['meetingType'] => ['in', "($viewed_meeting_type)"]
			]));
			$page_object          = new Page(count($list), $this->getPageRecordCount()); // 实例化分页类 传入总记录数和每页显示的记录数
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$list       = $api_configure_logic->setData('manage', $list);
			$pagination = $page_object->show();// 分页显示输出
			$this->assign('list', $list);
			$this->assign('meeting_type_list', $meeting_type_list);
			$this->assign('pagination', $pagination);
			$this->display();
		}
	}