<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-28
	 * Time: 15:42
	 */
	namespace RoyalwissD\Logic;

	use CMS\Logic\ExcelLogic;
	use CMS\Logic\Session;
	use CMS\Logic\UploadLogic;
	use CMS\Logic\UserLogic;
	use CMS\Model\CMSModel;
	use General\Logic\Time;
	use General\Model\GeneralModel;
	use Quasar\Utility\StringPlus;
	use RoyalwissD\Model\RoomModel;

	class RoomLogic extends RoyalwissDLogic{
		/**
		 * 处理POST/GET请求
		 *
		 * @param string $type 请求类型
		 * @param array  $opt  相关参数
		 *
		 * @return mixed
		 */
		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'create':
					if(!UserLogic::isPermitted('SEVERAL-ROOM.CREATE')) return [
						'status'   => false,
						'message'  => '您没有创建房间的权限',
						'__ajax__' => true
					];
					$meeting_id    = I('get.mid', 0, 'int');
					$hotel_id      = I('get.hid', 0, 'int');
					$post          = I('post.');
					$client_id_str = I('post.client', '');
					$client_id_arr = explode(',', $client_id_str);
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$str_obj    = new StringPlus();
					$result     = $room_model->create(array_merge($post, [
						'hid'         => $hotel_id,
						'mid'         => $meeting_id,
						'creatime'    => Time::getCurrentTime(),
						'creator'     => Session::getCurrentUser(),
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, '')
					]));
					if(!($client_id_str == null || !$result['status'])){
						$result2 = $room_model->checkIn($meeting_id, $result['id'], $client_id_arr);
						if(!$result2['status']) return array_merge($result2, ['__ajax__' => true]);
					}

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'modify':
					if(!UserLogic::isPermitted('SEVERAL-ROOM.MODIFY')) return [
						'status'   => false,
						'message'  => '您没有修改房间的权限',
						'__ajax__' => true
					];
					$hotel_id   = I('get.hid', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					$room_id    = I('post.id', 0, 'int');
					$post       = I('post.');
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$str_obj    = new StringPlus();
					$result     = $room_model->modify([
						'id'  => $room_id,
						'mid' => $meeting_id,
						'hid' => $hotel_id
					], array_merge($post, [
						'name_pinyin' => $str_obj->getPinyin($post['name'], true, ''),
					]));

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'delete': // 删除项目
					if(!UserLogic::isPermitted('SEVERAL-ROOM.DELETE')) return [
						'status'   => false,
						'message'  => '您没有删除房间的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$hotel_id   = I('get.hid', 0, 'int');
					$id_str     = I('post.id', '');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$result     = $room_model->drop([
						'id'  => ['in', $id_arr],
						'mid' => $meeting_id,
						'hid' => $hotel_id
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'enable': // 启用项目
					if(!UserLogic::isPermitted('SEVERAL-ROOM.ENABLE')) return [
						'status'   => false,
						'message'  => '您没有启用房间的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$hotel_id   = I('get.hid', 0, 'int');
					$id_str     = I('post.id', '');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$result     = $room_model->enable([
						'id'  => ['in', $id_arr],
						'mid' => $meeting_id,
						'hid' => $hotel_id
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'disable': // 禁用项目
					if(!UserLogic::isPermitted('SEVERAL-ROOM.DISABLE')) return [
						'status'   => false,
						'message'  => '您没有禁用房间的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$hotel_id   = I('get.hid', 0, 'int');
					$id_str     = I('post.id', '');
					$id_arr     = explode(',', $id_str);
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$result     = $room_model->disable([
						'id'  => ['in', $id_arr],
						'mid' => $meeting_id,
						'hid' => $hotel_id
					]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'get_room_type_detail':
					$meeting_id   = I('get.mid', 0, 'int');
					$hotel_id     = I('get.hid', 0, 'int');
					$room_type_id = I('post.id', '');
					/** @var \RoyalwissD\Model\RoomTypeModel $room_type_model */
					$room_type_model = D('RoyalwissD/RoomType');
					if(!$room_type_model->fetch([
						'id'  => $room_type_id,
						'hid' => $hotel_id,
						'mid' => $meeting_id
					])
					) return [
						'status'   => false,
						'message'  => '找不到房间类型数据',
						'__ajax__' => true
					];
					$record = $room_type_model->getObject();

					return array_merge($record, ['__ajax__' => true]);
				break;
				case 'get_client':
					$keyword    = I('post.keyword', '');
					$meeting_id = I('get.mid', 0, 'int');
					$list       = $this->_getNotCheckInClientList($meeting_id, $keyword);

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'get_room_type_list':
					$meeting_id = I('get.mid', 0, 'int');
					$hotel_id   = I('get.hid', 0, 'int');
					/** @var \RoyalwissD\Model\RoomTypeModel $room_type_model */
					$room_type_model = D('RoyalwissD/RoomType');
					$room_type_list  = $room_type_model->getSelectedList($meeting_id, $hotel_id);

					return array_merge($room_type_list, ['__ajax__' => true]);
				break;
				// 修改时/点击详情时获取房间信息
				case 'get_room':
				case 'get_detail':
					$meeting_id = I('get.mid', 0, 'int');
					$hotel_id   = I('get.hid', 0, 'int');
					$room_id    = I('post.id', 0, 'int');
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model  = D('RoyalwissD/Room');
					$room_record = $room_model->getList([
						$room_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $meeting_id,
						$room_model::CONTROL_COLUMN_PARAMETER_SELF['hotelID']   => $hotel_id,
						$room_model::CONTROL_COLUMN_PARAMETER_SELF['roomID']    => ['=', $room_id]
					]);
					if(!isset($room_record[0])) return [
						'status'   => false,
						'message'  => '找不到房间信息',
						'__ajax__' => true
					];
					$room_record = $room_record[0];
					/** @var \RoyalwissD\Model\RoomTypeModel $room_type_model */
					$room_type_model               = D('RoyalwissD/RoomType');
					$room_type_list                = $room_type_model->getSelectedList($meeting_id, $hotel_id, $room_record['type_code']);
					$room_record['room_type_list'] = $room_type_list;
					$room_record['history']        = $room_model->getCheckInHistory($meeting_id, $room_id);

					return array_merge($room_record, ['__ajax__' => true]);
				break;
				case 'check_in':
					if(!UserLogic::isPermitted('SEVERAL-ROOM.MANAGE_DETAIL')) return [
						'status'   => false,
						'message'  => '您没有管理房间的权限',
						'__ajax__' => true
					];
					$meeting_id = I('get.mid', 0, 'int');
					$room_id    = I('post.rid', 0, 'int');
					$client_id  = I('post.cid', '');
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$result     = $room_model->checkIn($meeting_id, $room_id, $client_id);
					if(!$result['status']) return array_merge($result, ['__ajax__' => true]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'check_out':
					if(!UserLogic::isPermitted('SEVERAL-ROOM.MANAGE_DETAIL')) return [
						'status'   => false,
						'message'  => '您没有管理房间的权限',
						'__ajax__' => true
					];
					$meeting_id     = I('get.mid', 0, 'int');
					$room_id        = I('post.rid', 0, 'int');
					$check_out_time = I('post.check_out_time', '');
					$client_id      = I('post.cid', '');
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$result     = $room_model->checkOut($meeting_id, $room_id, $client_id, $check_out_time);
					if(!$result['status']) return array_merge($result, ['__ajax__' => true]);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'change_room':
					if(!UserLogic::isPermitted('SEVERAL-ROOM.MANAGE_DETAIL')) return [
						'status'   => false,
						'message'  => '您没有管理房间的权限',
						'__ajax__' => true
					];
					$client_id_a = I('post.cid_a', 0, 'int');
					$room_id_a   = I('post.rid_a', 0, 'int');
					$client_id_b = I('post.cid_b', 0, 'int');
					$room_id_b   = I('post.rid_b', 0, 'int');
					$meeting_id  = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$result     = $room_model->change($client_id_a, $room_id_a, $client_id_b, $room_id_b, $meeting_id);

					return array_merge($result, ['__ajax__' => true]);
				break;
				// 换房时获取房间列表并剔除客户自己的房间
				case 'get_room_list':
					$room_id    = I('post.rid', 0, 'int');
					$hotel_id   = I('get.hid', 0, 'int');
					$meeting_id = I('get.mid', 0, 'int');
					/** @var \RoyalwissD\Model\RoomModel $room_model */
					$room_model = D('RoyalwissD/Room');
					$room_list  = $room_model->getList([
						CMSModel::CONTROL_COLUMN_PARAMETER['status']            => ['<>', 2],
						$room_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID'] => $meeting_id,
						$room_model::CONTROL_COLUMN_PARAMETER_SELF['hotelID']   => $hotel_id
					]);
					$list       = [];
					foreach($room_list as $room){
						if($room['id'] == $room_id) continue;
						$room['client_list'] = [];
						if($room['client_code'] == null) ;
						else{
							$client_code_arr = explode(',', $room['client_code']);
							$client_arr      = explode($room_model::CLIENT_NAME_SEPARATOR, $room['client']);
							foreach($client_code_arr as $key => $client) $room['client_list'][] = [
								'id'   => $client_code_arr[$key],
								'name' => $client_arr[$key]
							];
						}
						$list[] = $room;
					}

					return array_merge($list, ['__ajax__' => true]);
				break;
				case 'import_excel':
					if(!UserLogic::isPermitted('SEVERAL-ROOM.IMPORT')) return [
						'status'   => false,
						'message'  => '您没有导入的权限',
						'__ajax__' => true
					];
					$meeting_id   = I('get.mid', 0, 'int');
					$hotel_id     = I('get.hid', 0, 'int');
					$upload_logic = new UploadLogic($meeting_id);
					$result       = $upload_logic->upload($_FILES, '/Excel/');

					return array_merge($result, [
						'__ajax__' => true,
						'nextPage' => U('importColumnContrast', [
							'mid'   => $meeting_id,
							'hid'   => $hotel_id,
							'logID' => $result['data']['logID']
						])
					]);
				break;
				case 'save_excel_data':
					if(!UserLogic::isPermitted('SEVERAL-ROOM.IMPORT')) return [
						'status'   => false,
						'message'  => '您没有导入的权限',
						'__ajax__' => true
					];
					set_time_limit(0);
					$str_obj     = new StringPlus();
					$excel_logic = new ExcelLogic();
					/** @var \General\Model\UploadLogModel $upload_log_model */
					$upload_log_model = D('General/UploadLog');
					$log_id           = I('get.logID', 0, 'int');
					$meeting_id       = I('get.mid', 0, 'int');
					$hotel_id         = I('get.hid', 0, 'int');
					$map              = I('post.map');
					$batch_id         = $str_obj->makeRandomString(64);
					/**
					 * 构建返回结果
					 *
					 * @param bool   $status          结果状态
					 * @param string $message         结果信息
					 * @param array  $excel_data      导入Excel数据
					 * @param array  $saved_room_list 已保存的房间数据列表
					 * @param array  $head_data       导入Excel的表头
					 *
					 * @return array
					 */
					$makeResult = function ($status, $message, $excel_data, $saved_room_list, $head_data) use ($str_obj, $excel_logic, $batch_id, $meeting_id, $hotel_id){
						// 保存日志数据
						/** @var \RoyalwissD\Model\RoomImportResultModel $room_import_result_model */
						$room_import_result_model = D('RoyalwissD/RoomImportResult');
						$total_count              = count($excel_data);
						$success_count            = count($saved_room_list);
						$repeat_count             = 0;
						$failure_count            = $total_count-$success_count-$repeat_count;
						$failure_count            = $failure_count<0 ? 0 : $failure_count;
						C('TOKEN_ON', false);
						if($failure_count>0) $failure_data_save_result = $excel_logic->writeCustomData([$head_data], [
							'fileName'    => $str_obj->makeRandomString(12),
							'title'       => '房间导入失败数据',
							'subject'     => '房间导入失败数据',
							'description' => '房间批量导入后的失败数据',
							'download'    => false,
							'savePath'    => UploadLogic::UPLOAD_PATH.'/Excel-Failure/',
							'hasHead'     => true
						]);
						else $failure_data_save_result = ['status' => false];
						$result3 = $room_import_result_model->create([
							'batch_save_id'          => $batch_id,
							'mid'                    => $meeting_id,
							'total_count'            => $total_count,
							'success_count'          => $success_count,
							'repeat_count'           => $repeat_count,
							'failure_count'          => $failure_count,
							'failure_data_file_path' => $failure_data_save_result['status'] ? $failure_data_save_result['filePath'] : null,
							'repeat_data_file_path'  => null,
							'creatime'               => Time::getCurrentTime(),
							'creator'                => Session::getCurrentUser()
						]);
						if(!$result3['status']) return [
							'status'   => false,
							'message'  => $result3['message'],
							'__ajax__' => true
						];

						return [
							'status'   => $status,
							'message'  => $message,
							'resultID' => $result3['id'],
							'nextPage' => U('importResult', [
								'resultID' => $result3['id'],
								'mid'      => $meeting_id,
								'hid'      => $hotel_id
							]),
							'__ajax__' => true
						];
					};
					if($upload_log_model->fetch(['id' => $log_id])){
						/**
						 * 获取房间类型名称在导入字段表头中的索引值
						 *
						 * @param array $column_list 导入字段表头
						 * @param array $map         映射表
						 *
						 * @return int|null
						 */
						$getRoomTypeIndex = function ($column_list, $map){
							$index = null;
							foreach($column_list as $key => $val){
								if(RoomModel::IMPORT_ROOM_TYPE_NAME == $val['column_name']){
									$index = $key;
									break;
								}
							}
							if($index === '_none') return null;
							else return $map[$index];
						};
						$upload_record    = $upload_log_model->getObject();
						// 获取导入的Excel数据
						$file_path   = trim($upload_record['save_path'], '/');
						$read_result = $excel_logic->readCustomData($file_path);
						$excel_data  = $read_result['data']['body'];
						/** @var \RoyalwissD\Model\RoomModel $room_model */
						$room_model      = D('RoyalwissD/Room');
						$column_list     = $room_model->getColumnList();
						$room_type_index = $getRoomTypeIndex($column_list, $map);
						if($room_type_index === null) return $makeResult(false, '缺少房间类型数据名称', $excel_data, [], $read_result['data']['head']);
						// 构建房间数据
						$room_data = [];
						foreach($excel_data as $index => $room){
							$temp_room_data = [
								'mid'      => $meeting_id,
								'hid'      => $hotel_id,
								'creatime' => Time::getCurrentTime(),
								'creator'  => Session::getCurrentUser(),
							];
							// 字段映射
							foreach($room as $key => $val){
								// 这是房间的字段
								$save_column_name = $column_list[$map[$key]]['column_name'];
								$kv               = $this->_convertColumnValue($save_column_name, $val); // 特殊处理字段数据
								$kv ? $temp_room_data = array_merge($temp_room_data, $kv) : null; // 拼接数据
							}
							$room_data[] = $temp_room_data;
						}
						// 构建房间类型数据
						$room_type_data = [
							'name' => [],
							'list' => []
						];
						if(count($room_data)>0 && isset($room_data[0][RoomModel::IMPORT_ROOM_TYPE_NAME])){
							foreach($room_data as $key => $room){
								if(!in_array($room[RoomModel::IMPORT_ROOM_TYPE_NAME], $room_type_data['name'])){
									$room_type_data['name'][] = $room[RoomModel::IMPORT_ROOM_TYPE_NAME];
									$room_type_data['list'][] = [
										'mid'              => $meeting_id,
										'hid'              => $hotel_id,
										'capacity'         => 0,
										'number'           => 0,
										'price'            => 0,
										'name'             => $room[RoomModel::IMPORT_ROOM_TYPE_NAME],
										'name_pinyin'      => $str_obj->getPinyin($room[RoomModel::IMPORT_ROOM_TYPE_NAME], true, ''),
										'creatime'         => Time::getCurrentTime(),
										'creator'          => Session::getCurrentUser(),
										'_batch_save_id'   => $batch_id,
										'_batch_column_id' => $key
									];
								}
								$index                               = array_search($room[RoomModel::IMPORT_ROOM_TYPE_NAME], $room_type_data['name']);
								$room_data[$key]['_batch_column_id'] = $room_type_data['list'][$index]['_batch_column_id'];
							}
						}
						// 开始保存
						/** @var \RoyalwissD\Model\RoomTypeModel $room_type_model */
						$room_type_model = D('RoyalwissD/RoomType');
						$result          = $room_type_model->addAll($room_type_data['list']);
						if(!$result) return $makeResult(false, '保存房间类型失败', $excel_data, [], $read_result['data']['head']);
						// 关联房间类型和房间
						$saved_room_type = $room_type_model->where("_batch_save_id = '$batch_id' and mid = $meeting_id and hid = $hotel_id")->select();
						foreach($room_data as $key => $room){
							foreach($saved_room_type as $k => $v){
								if($v['_batch_column_id'] == $room['_batch_column_id']){
									$room_data[$key]['type'] = $v['id'];
									break;
								}
							}
						}
						// 保存房间
						/** @var \RoyalwissD\Model\RoomModel $room_model */
						$room_model = D('RoyalwissD/Room');
						$result2    = $room_model->addAll($room_data);
						if(!$result2) return $makeResult(false, '保存房间失败', $excel_data, [], $read_result['data']['head']);

						return $makeResult(true, '保存成功', $excel_data, $room_data, $read_result['data']['head']);
					}
					else{
						return [
							'status'   => false,
							'message'  => '找不到上传数据',
							'__ajax__' => true
						];
					}
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true];
				break;
			}
		}

		/**
		 * 设定额外数据
		 *
		 * @param string $type 操作类型
		 * @param mixed  $data 处理数据
		 *
		 * @return mixed
		 */
		public function setData($type, $data){
			switch($type){
				case 'manage':
					foreach($data as $key => $project){
						$data[$key]['status_code'] = $project['status'];
						$data[$key]['status']      = GeneralModel::STATUS[$project['status']];
						if($data[$key]['client_code'] === null){
							$data[$key]['count']  = 0;
							$data[$key]['client'] = '';
						}
						else{
							$data[$key]['count']  = count(explode(',', $data[$key]['client_code']));
							$data[$key]['client'] = str_replace(RoomModel::CLIENT_NAME_SEPARATOR, ', ', $data[$key]['client']);
						}
					}

					return $data;
				break;
				case 'manage:statistics':
					$meeting_id = $data['meetingID'];
					$statistics = [
						'roomOfFully'        => 0,
						'roomOfNotFully'     => 0,
						'clientOfCheckIn'    => 0,
						'clientOfNotCheckIn' => count($this->_getNotCheckInClientList($meeting_id)),
					];
					foreach($data['total'] as $room){
						if($room['client_code'] == '') $check_in_client = [];
						else $check_in_client = explode(',', $room['client_code']);
						if($room['client_code'] != '' || $room['client_code']){
							$statistics['clientOfCheckIn'] += count($check_in_client);
							if(count($check_in_client)>=$room['capacity'] && $room['capacity']>0) $statistics['roomOfFully']++;
						}
					}
					$statistics['roomOfNotFully'] = count($data['total'])-$statistics['roomOfFully'];

					return $statistics;
				break;
				default:
					return $data;
				break;
			}
		}

		/**
		 * 特殊处理字段数据
		 *
		 * @param string $column_name  字段名称
		 * @param mixed  $column_value 字段值
		 *
		 * @return array|null
		 */
		private function _convertColumnValue($column_name, $column_value){
			switch($column_name){
				case 'name':
				case RoomModel::IMPORT_ROOM_TYPE_NAME:
					$str_obj = new StringPlus();
					$val     = $str_obj->getPinyin($column_value, true, '');

					return [$column_name => $column_value, $column_name.'_pinyin' => $val];
				break;
				case '':
					return null;
				break;
				default:
					return [$column_name => $column_value];
				break;
			}
		}

		private function _getNotCheckInClientList($meeting_id, $keyword = ''){
			/** @var \RoyalwissD\Model\ClientModel $client_model */
			$client_model = D('RoyalwissD/Client');
			/** @var \RoyalwissD\Model\RoomModel $room_model */
			$room_model    = D('RoyalwissD/Room');
			$customer_list = $room_model->getCustomer($meeting_id);
			$option        = [];
			if($customer_list){
				$customer_id_str = '';
				foreach($customer_list as $customer) $customer_id_str .= "$customer[id],";
				$customer_id_str                                                  = trim($customer_id_str, ',');
				$customer_id_str                                                  = "($customer_id_str)";
				$option[$client_model::CONTROL_COLUMN_PARAMETER_SELF['clientID']] = [
					'not in',
					$customer_id_str
				];
			}
			if(!($keyword == '')) $option[CMSModel::CONTROL_COLUMN_PARAMETER['keyword']] = $keyword;
			$list = $client_model->getList(array_merge($option, [
				CMSModel::CONTROL_COLUMN_PARAMETER['status']                 => ['=', 1],
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus'] => ['=', 1],
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['signStatus']   => ['=', 1],
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['meetingID']    => $meeting_id,
				$client_model::CONTROL_COLUMN_PARAMETER_SELF['type']         => true
			]));

			return $list;
		}
	}