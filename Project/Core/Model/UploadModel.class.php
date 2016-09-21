<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-17
	 * Time: 17:35
	 */
	namespace Core\Model;

	class UploadModel extends CoreModel{
		protected $tableName   = 'upload';
		protected $tablePrefix = 'system_';

		public function _initialize(){
			parent::_initialize();
		}

		public function createRecord($path, $upload_result){
			$data['file_type']   = $upload_result['type'];
			$data['save_path']   = $path;
			$data['creator']     = I('session.MANAGER_USER_ID', 0, 'int');
			$data['creatime']    = time();
			$data['suffix']      = $upload_result['ext'];
			$data['md5']         = $upload_result['md5'];
			$data['sha1']        = $upload_result['sha1'];
			$data['size']        = $upload_result['size'];
			$data['origin_name'] = $upload_result['name'];
			C('TOKEN_ON', false);
			if($this->create($data)){
				$result = $this->add($data);
				if($result) return ['status' => true, 'message' => '记录成功', 'id' => $result];
				else return ['status' => false, 'message' => $this->getError()];
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id']) && $filter['id']) $where['id'] = $filter['id'];
			if(isset($filter['keyword']) && $filter['keyword']){
				$where['origin_name'] = ['like', "%$filter[keyword]%"];
				$where['suffix']      = ['like', "%$filter[keyword]%"];
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->count();
						else $result = $this->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->where($where)->count();
						else $result = $this->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->find();
						else $result = $this->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->where($where)->find();
						else $result = $this->where($where)->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['order'])) $filter['order'] = 'id desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->order($filter['order'])->select();
						else $result = $this->order($filter['order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->where($where)->order($filter['order'])->select();
						else $result = $this->where($where)->order($filter['order'])->select();
					}
				break;
			}

			return $result;
		}
	}