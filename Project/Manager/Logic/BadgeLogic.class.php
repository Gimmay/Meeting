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

		public function handlerRequest($type){
			switch($type){
				case 'upload_image':
					$getSavePath          = function ($data){
						return UPLOAD_PATH.$data['data']['uploadImage']['savepath'].$data['data']['uploadImage']['savename'];
					};
					$getResult            = function ($data){
						return $data['data']['uploadImage'];
					};
					$core_upload_logic    = new UploadLogic();
					$manager_upload_logic = new \Manager\Logic\UploadLogic();
					$result1              = $core_upload_logic->upload($_FILES, '/Image/');
					if(!$result1['status']) return array_merge($result1, ['__ajax__' => true]);
					$result2 = $manager_upload_logic->create($getSavePath($result1), $getResult($result1));

					return array_merge($result2, ['__ajax__' => true, 'imageUrl' => trim($getSavePath($result1), '.')]);
				break;
				case 'create':
					/** @var \Core\Model\BadgeModel $model */
					$model              = D('Core/Badge');
					$data               = I('post.');
					$data['creatime']   = time();
					$data['creator']    = I('session.MANAGER_EMPLOYEE_ID', 0, 'int');
					$data['data']       = $_POST['data']['temp'];
					$data['attributes'] = json_encode($_POST['data']['attributes']);
					C('TOKEN_ON', false);
					$result = $model->createBadge($data);

					return array_merge($result, ['__ajax__' => true]);
				break;
				case 'assign_badge_for_meeting':
					/** @var \Core\Model\MeetingModel $meeting_model */
					$meeting_model = D('Core/Meeting');
					$data          = I('post.');
					$data['bid']   = I('post.id', 0, 'int');
					$mid           = I('post.mid', 0, 'int');
					$result        = $meeting_model->alterMeeting([$mid], $data);

					return array_merge($result, ['__ajax__' => false]);
				break;
				default:
					return ['status' => false, 'message' => '参数错误'];
				break;
			}
		}
	}