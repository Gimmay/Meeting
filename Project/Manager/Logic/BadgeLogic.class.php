<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-11
	 * Time: 16:46
	 */
	namespace Manager\Logic;

	use Core\Logic\UploadLogic;

	class BadgeLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $option = []){
			switch($type){
				case 'upload_image':
					$core_upload_logic = new UploadLogic();
					$result            = $core_upload_logic->upload($_FILES, '/Image/');

					return array_merge($result, ['__ajax__' => true, 'imageUrl' => $result['data']['filePath']]);
				break;
				case 'create':
					if($this->permissionList['BADGE.CREATE']){
						/** @var \Core\Model\BadgeModel $model */
						$model              = D('Core/Badge');
						$data               = I('post.');
						$data['name']       = $data['data']['name'];
						$data['data']       = $_POST['data']['temp'];
						$data['attributes'] = json_encode($_POST['data']['attributes']);
						$data['creatime']   = time();
						$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
						C('TOKEN_ON', false);
						$result = $model->createBadge($data);

						return array_merge($result, [
							'__ajax__'   => true,
							'__return__' => U('previewList', ['mid' => $option['mid']])
						]);
					}
					else return ['status' => false, 'message' => '您没有创建胸卡的权限', '__ajax__' => true];
				break;
				case 'assign_badge_for_meeting':
					if($this->permissionList['BADGE.SELECT']){
						/** @var \Core\Model\MeetingModel $meeting_model */
						$meeting_model = D('Core/Meeting');
						$data          = I('post.');
						$data['bid']   = I('post.id', 0, 'int');
						$mid           = I('get.mid', 0, 'int');
						unset($data['id']);
						$result = $meeting_model->alterMeeting(['id' => $mid], $data);

						return array_merge($result, [
							'__ajax__'   => false,
							'__return__' => U('Badge/preview', ['mid' => $mid])
						]);
					}
					else return ['status' => false, 'message' => '您没有选择胸卡的权限', '__ajax__' => false];
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}

		public function setData($type, $data, $option = []){
			switch($type){
				case 'preview:init_temp':
					$data['data'] = str_replace('客户姓名', $option['client']['name'], $data['data']);
					$data['data'] = str_replace('会议名称', $option['meeting']['name'], $data['data']);
					$data['data'] = str_replace('单位', $option['client']['unit'], $data['data']);
					$data['data'] = str_replace('会议简介', $option['meeting']['brief'], $data['data']);
					$data['data'] = str_replace('会议时间', $option['meeting']['start_time'].' - '.$option['meeting']['end_time'], $data['data']);
					$data['data'] = str_replace(COMMON_IMAGE_PATH.'/CheckIn_Code.jpg', $option['client']['sign_qrcode'], $data['data']);

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}
	}