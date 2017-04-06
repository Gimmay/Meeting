<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-3
	 * Time: 9:18
	 */
	namespace CMS\Logic;
    use General\Logic\SystemLogic as GeneralSystemLogic;
	use General\Logic\UploadLogic;
	class SystemLogic extends CMSLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function handlerRequest($type, $opt = []){
			switch($type){
				case 'upload_img':
					/** @var \General\Logic\UploadLogic $upload_logic */
					$upload_logic = new UploadLogic();
					$result = $upload_logic->uploadFile();
					return array_merge($result,['__ajax__' =>true]);
				break;
				case 'save_edit_config':
					$post = I('post.','');
					/** @var \General\Logic\SystemLogic $system_logic */
					$system_logic = new GeneralSystemLogic();
					$result = $system_logic->saveEditConfig($post);
					return array_merge($result,['__ajax__' =>true]);
			    break;
				case 'find_config_by_id':
					$id = I('post.id','');
					/** @var  \General\model\SystemConfigModel $sys_conf_model */
					$sys_conf_model = D('General/SystemConfig');
					$result = $sys_conf_model->findConfigure($id);
					return array_merge($result,['__ajax__' =>true]);
				break;
				case 'delete_config':
					$id = I('post.id','');
					/** @var \General\Logic\SystemLogic $system_logic */
					$system_logic = new GeneralSystemLogic();
					$result = $system_logic->deleteConf($id);
					return array_merge($result,['__ajax__' =>true]);
				break;
				case 'change_config_order':
					$post = I('post.','');
					/** @var \General\Logic\SystemLogic $system_logic */
					$system_logic = new GeneralSystemLogic();
					$result = $system_logic->changeOrder($post);
					return array_merge($result,['__ajax__' =>true]);
				break;
				case 'save_conf':
				$post = I('post.','');
				/** @var \General\Logic\SystemLogic $system_logic */
				$system_logic = new GeneralSystemLogic();
				$result = $system_logic->updateConfContent($post);
					//var_dump($result);die;
                return array_merge($result,['__ajax__' =>false]);

				break;
				case 'add_system_config':
                $post = I('post.','');
				/** @var \General\Logic\SystemLogic $system_logic */
				$system_logic = new GeneralSystemLogic();
				$result = $system_logic->saveSystemConfig($post);
				return $result;
				break;
				default:
					return ['status' => false, 'message' => '缺少必要参数', '__ajax__' => true, '__redirect__' => ''];
				break;
			}
		}



		public function setData($type, $data){

		}


	}