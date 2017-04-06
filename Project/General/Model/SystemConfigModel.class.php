<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-2
	 * Time: 15:23
	 */
	namespace General\Model;

	use CMS\Logic\Session;
	use Exception;

	class SystemConfigModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $connection       = 'DB_CONFIG_COMMON';
		protected $tableName        = 'system_configure';
		protected $autoCheckFields  = true;

		/**
		 * @param array $data
		 *   保存系统配置到数据库
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);
				return $result ? ['status' => true, '__ajax__' =>true] : [
					'status'  => false,
					'__ajax__' =>true
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError() ,'__ajax__' =>true];
			}
		}

		/**
		 * @param $id_arr array 需要更新内容的配置项id数组
		 * @param $content_arr  配置内容 数组
		 *   更新配置内容
		 * @return array
		 */
        public function updateContent($id_arr,$content_arr){
			try{
				$sql_half = '';
				//如果配置内容为空，则给默认值 null
				foreach($id_arr as $key => $value){
					foreach($content_arr as $k => $v){
						if($key == $k){
							if($v == ''){
								$v = 'NULL';
							}
							$sql_half .= ' WHEN '.$value. ' THEN '."'$v'";
						}
					}

				}
				$id_str = implode(',',$id_arr);
				$sql = 'UPDATE system_configure SET conf_content = CASE id'.$sql_half.' END WHERE id IN '."($id_str)";
				//die(var_dump($sql));
				$result = $this->execute($sql);
				if($result >= 0){
					return ['status' => true,'message'=>'更新成功'];
				}else{
					return ['status'  => false,'message' => '更新失败'];
				}

			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}


		}

		/**
		 * @param $id
		 * @param $data
		 *   更改排序
		 * @return array
		 */
        public function updateOrder($id,$data){
			try{
				$result = $this->where("id = $id")->save($data);
				return $result ? ['status' => true,'message'=>'排序更新成功'] : [
					'status'  => false,
					'message' => '排序更新失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}

		}

		/**
		 * @param $id
		 *   删除配置项目
		 * @return array
		 */
		public function deleteConfigure($id){
			try{
				$result = $this->where("id = $id")->delete();
				return $result ? ['status' => true,'message'=>'配置删除成功'] : [
					'status'  => false,
					'message' => '配置删除失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * @param $id
		 *   根据 id  查配置记录
		 * @return array
		 */
		public function findConfigure($id){
			try{
				$res = $this->where("id = $id")->select();
				if($res){
					$result['data'] = $res[0];
					return array_merge($result,['status' => true,'message'=>'信息查询成功']);
				}else{
					return ['status' => false,'message'=>'违法id'];
				}

			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * @param $id int
		 * @param $data array
		 *   修改配置信息
		 * @return array
		 */
		public function saveEditConfigure($id,$data){
			try{
				$result = $this->where("id = $id")->save($data);
				if($result){
					return ['status' => true,'message'=>'信息修改成功'];
				}else{
					return ['status' => false,'message'=>'信息修改失败'];
				}

			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}
	}