<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-14
	 * Time: 18:03
	 */
	namespace RoyalwissD\Controller;

	use CMS\Logic\ExcelLogic;
	use CMS\Logic\PageLogic;
	use CMS\Model\CMSModel;
	use RoyalwissD\Logic\RoomLogic;
	use RoyalwissD\Logic\RoomTypeLogic;
	use Think\Page;

	class RoomController extends RoyalwissD{
		protected $hotelID   = 0;
		protected $hotelName = '';

		public function _initialize(){
			parent::_initialize();
			$this->initMeetingID();
			if(!isset($_GET['hid'])) $this->error('URL缺少酒店ID参数');
			/** @var \RoyalwissD\Model\HotelModel $hotel_model */
			$hotel_model = D('RoyalwissD/Hotel');
			if(!$hotel_model->fetch(['id' => I('get.hid', 0, 'int')])) $this->error('找不到酒店信息');
			$hotel_record    = $hotel_model->getObject();
			$this->hotelID   = $hotel_record['id'];
			$this->hotelName = $hotel_record['name'];
			$this->assign('hotel_name', $this->hotelName);
		}

		public function manage(){
			$room_logic = new RoomLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $room_logic->handlerRequest($type);
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
			/** @var \RoyalwissD\Model\RoomModel $room_model */
			$room_model           = D('RoyalwissD/Room');
			$model_control_column = $this->getModelControl();
			$list                 = $room_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']            => ['<>', 2],
				$room_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID,
				$room_model::CONTROL_COLUMN_PARAMETER_SELF['hotelID']   => $this->hotelID
			]));
			$page_object          = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$pagination = $page_object->show();
			$list       = $room_logic->setData('manage', $list);
			// 获取房间类型的可用数和总数
			/** @var \RoyalwissD\Model\RoomTypeModel $room_type_model */
			$room_type_model  = D('RoyalwissD/RoomType');
			$room_type_number = $room_type_model->getNumber($this->meetingID, $this->hotelID);
			$this->assign('room_type_number', $room_type_number);
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
			$this->display();
		}

		public function roomType(){
			$room_type_logic = new RoomTypeLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $room_type_logic->handlerRequest($type);
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
			/** @var \RoyalwissD\Model\RoomTypeModel $room_type_model */
			$room_type_model      = D('RoyalwissD/RoomType');
			$model_control_column = $this->getModelControl();
			$list                 = $room_type_model->getList(array_merge($model_control_column, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']                 => ['<>', 2],
				$room_type_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $this->meetingID,
				$room_type_model::CONTROL_COLUMN_PARAMETER_SELF['hotelID']   => $this->hotelID
			]));
			$page_object          = new Page(count($list), $this->getPageRecordCount());
			PageLogic::setTheme1($page_object);
			$list       = array_slice($list, $page_object->firstRow, $page_object->listRows);
			$pagination = $page_object->show();
			$list       = $room_type_logic->setData('manage', $list);
			// 获取房间类型的可用数和总数
			/** @var \RoyalwissD\Model\RoomTypeModel $room_type_model */
			$room_type_model  = D('RoyalwissD/RoomType');
			$room_type_number = $room_type_model->getNumber($this->meetingID, $this->hotelID);
			$this->assign('room_type_number', $room_type_number);
			$this->assign('list', $list);
			$this->assign('pagination', $pagination);
			$this->display();
		}

		public function exportTemplate(){
			/** @var \General\Model\MeetingModel $meeting_model */
			$meeting_model = D('General/Meeting');
			/** @var \RoyalwissD\Model\HotelModel $hotel_model */
			$hotel_model = D('RoyalwissD/Hotel');
			if(!($meeting_model->fetch(['id' => $this->meetingID]))){
				$this->error('找不到会议');
				exit;
			}
			if(!($hotel_model->fetch(['id' => $this->hotelID]))){
				$this->error('找不到酒店信息');
				exit;
			}
			$meeting      = $meeting_model->getObject();
			$meeting_name = $meeting['name'];
			$hotel        = $hotel_model->getObject();
			$hotel_name   = $hotel['name'];
			$excel_logic  = new ExcelLogic();
			/** @var \RoyalwissD\Model\RoomModel $room_model */
			$room_model       = D('RoyalwissD/Room');
			$column_head      = $room_model->getColumnList();
			$column_head_data = [];
			foreach($column_head as $val) $column_head_data[] = $val['column_comment'];
			$excel_logic->writeCustomData([$column_head_data], [
				'fileName'     => "[$meeting_name]-$hotel_name-房间数据导入模板",
				'creator'      => 'Quasar',
				'lastModifier' => 'Quasar',
				'title'        => "$meeting_name $hotel_name 房间数据模板",
				'subject'      => "房间数据导入模板",
				'keyword'      => "自动导出, PHPExcel, Quasar, $meeting_name, $hotel_name",
				'company'      => '吉美集团-瑞辉医疗',
				'hasHead'      => true,
				'download'     => true,
			]);
		}

		public function import(){
			$room_logic = new RoomLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $room_logic->handlerRequest($type);
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

		public function fieldContrast(){
			$room_logic = new RoomLogic();
			if(IS_POST){
				$type   = strtolower(I('post.requestType', ''));
				$result = $room_logic->handlerRequest($type);
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
			/** @var \General\Model\UploadLogModel $upload_log_model */
			$upload_log_model = D('General/UploadLog');
			$log_id           = I('get.logID', 0, 'int');
			if($upload_log_model->fetch(['id' => $log_id])){
				$upload_record = $upload_log_model->getObject();
				/** @var \RoyalwissD\Model\RoomModel $room_model */
				$room_model  = D('RoyalwissD/Room');
				$column_list = $room_model->getColumnList();
				// 获取导入的Excel数据
				$file_path   = trim($upload_record['save_path'], '/');
				$excel_logic = new ExcelLogic();
				$read_result = $excel_logic->readCustomData($file_path);
				$this->assign('data_head', $read_result['data']['head']);
				$this->assign('data_body', $read_result['data']['body']);
				$this->assign('column_list', $column_list);
				$this->display();
			}
			else{
				$this->error('缺少上传日志参数');
				exit;
			}
		}

		public function importResult(){
			$result_id = I('get.resultID', 0, 'int');
			/** @var \RoyalwissD\Model\RoomImportResultModel $room_import_result_model */
			$room_import_result_model = D('RoyalwissD/RoomImportResult');
			if($room_import_result_model->fetch(['id' => $result_id])){
				$record = $room_import_result_model->getObject();
				$this->assign('result', $record);
				$this->display();
			}
			else{
				$this->error('缺少导入结果参数');
				exit;
			}
		}
	}