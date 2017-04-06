<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-14
	 * Time: 18:03
	 */
	namespace RoyalwissD\Controller;

	use CMS\Logic\PageLogic;
	use CMS\Model\CMSModel;
	use RoyalwissD\Logic\HotelLogic;
	use Think\Page;

	class HotelController extends RoyalwissD{
		public function _initialize(){
			parent::_initialize();
			$this->initMeetingID();
		}
		
		public function manage(){
			$hotel_logic = new HotelLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $hotel_logic->handlerRequest($type);
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
			// 获取项目数据
			/** @var \RoyalwissD\Model\HotelModel $hotel_model */
			$hotel_model = D('RoyalwissD/Hotel');
			$model_control_column = $this->getModelControl();
			$list                 = $hotel_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']                    => ['<>', 2],
				$hotel_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID
			]));
			$page_object          = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$pagination = $page_object->show();
			$list       = $hotel_logic->setData('manage', $list);
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
			$this->display();
		}
	}