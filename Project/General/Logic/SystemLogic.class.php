<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-6
	 * Time: 9:42
	 */
	namespace General\Logic;


	use CMS\Logic\Session;

	class SystemLogic extends GeneralLogic{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * @param $filter array post提交的数据
		 *    保存系统配置
		 * @return array
		 */
        public function saveSystemConfig($filter){
			$creator = Session::getCurrentUser();
			if($filter['conf_type'] == 'radio'){
				$filter['conf_value'] = '1|开启,0|关闭';
			}else{
				$filter['conf_value'] = 'default';
			}
			$data = [
				'id'           => 'default',
				'conf_title'   => $filter['conf_title'],
				'conf_name'    => $filter['conf_name'],
				'conf_type'    => $filter['conf_type'],
				'creator'      => $creator,
				'create_time'  => time(),
				'orders'       => $filter['orders'],
				'description'  => $filter['description'],
				'conf_value'   => $filter['conf_value'],
			];
			/** @var  \General\model\SystemConfigModel $sys_conf_model */
			$sys_conf_model = D('General/SystemConfig');
			$result = $sys_conf_model->create($data);
			return $result;
		}

		/**
		 * @param $data
		 *  更新配置内容
		 * @return array
		 */
		public function updateConfContent($data){
            $id_arr = $data['id']; //array
            $conf_content_arr = $data['conf_content'];
			//筛选勾选中的 内容
/*			$content_arr = '';
			foreach($conf_content_arr as $k =>$v){
				foreach($id_arr as $kk => $vv){
					if($kk == $k){
						$content_arr[] = $conf_content_arr[$kk];
					}
				}
			}*/
			//var_dump($content_arr);die;
			/** @var  \General\model\SystemConfigModel $sys_conf_model */
			$sys_conf_model = D('General/SystemConfig');
			$result = $sys_conf_model->updateContent($id_arr,$conf_content_arr);
			return $result;
		}

		/**
		 * @param $filter
		 *    更该 排序
		 * @return array
		 */
		public function changeOrder($filter){
			$id = $filter['id'];
			$orders = $filter['orders'];
			$data['orders'] = $orders;
			/** @var  \General\model\SystemConfigModel $sys_conf_model */
			$sys_conf_model = D('General/SystemConfig');
			$result = $sys_conf_model ->updateOrder($id,$data);
			return $result;
		}

		/**
		 * @param $id
		 *  删除配置
		 * @return array
		 */
		public function deleteConf($id){
           if($id == ''){
			   return ['status' => false, 'message' => 'id不存在'];
		   }else{
			   /** @var  \General\model\SystemConfigModel $sys_conf_model */
			   $sys_conf_model = D('General/SystemConfig');
			   $result = $sys_conf_model ->deleteConfigure($id);
			   return $result;
		   }
		}

		/**
		 * @param $filter array
		 *   保存修改配置
		 * @return array
		 */
		public function saveEditConfig($filter){
			$creator = Session::getCurrentUser();
			$id = $filter['id'];
			$data = [];
			if($filter['conf_type'] == 'radio'){
				$data['conf_value'] = '1|开启,0|关闭';
			}else{
				$data['conf_value'] = null;
			}
			$data = [
				'conf_title'   => $filter['conf_title'],
				'conf_name'    => $filter['conf_name'],
				'conf_type'    => $filter['conf_type'],
				'creator'      => $creator,
				'create_time'  => time(),
				'orders'       => $filter['orders'],
				'description'  => $filter['description'],
				'conf_value'   => $data['conf_value'],
			];
			/** @var  \General\model\SystemConfigModel $sys_conf_model */
			$sys_conf_model = D('General/SystemConfig');
			$result = $sys_conf_model->saveEditConfigure($id,$data);
			return $result;
		}

	}