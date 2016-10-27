<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-24
	 * Time: 9:28
	 */
	namespace Core\Model;

	use Exception;

	class AssignCarModel extends CoreModel{
		protected $tableName       = 'assign_car';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function createRecord($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '记录创建成功', 'id' => $result];
					else return ['status' => false, 'message' => '记录创建失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $message];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id'])) $where['main.id'] = $filter['id'];
			if(isset($filter['carID'])) $where['sub.id'] = $filter['carID'];
			if(isset($filter['jid'])) $where['main.jid'] = $filter['jid'];
			if(isset($filter['eid'])) $where['main.eid'] = $filter['eid'];
			if(isset($filter['mid'])) $where['main.mid'] = $filter['mid'];
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted'){
					$where['sub.status']  = ['neq', 3];
					//$where['main.status'] = ['neq', 2];
				}
				elseif($status == 'not available'){
					$where['sub.status']  = ['in', [2, 3, 0]];
					//$where['main.status'] = ['neq', 2];
				}
				else{
					$where['sub.status']  = $filter['status'];
					//$where['main.status'] = ['neq', 2];
				}
			}
			if(isset($filter['keyword']) && $filter['keyword']){
				$condition['sub.plate_number'] = ['like', "%$filter[keyword]%"];
				$condition['sub.type']         = ['like', "%$filter[keyword]%"];
				$condition['sub.color']        = ['like', "%$filter[keyword]%"];
				$condition['sub.model']        = ['like', "%$filter[keyword]%"];
				$condition['_logic']           = 'or';
				$where['_complex']             = $condition;
			}
			switch((int)$type){
				case 0: // count
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->limit($filter['_limit'])->count();
						else $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->count();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->limit($filter['_limit'])->where($where)->count();
						else $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->where($where)->count();
					}
				break;
				case 1: // find
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->field('sub.*, main.jid, main.id aid')->limit($filter['_limit'])->find();
						else $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->field('sub.*, main.jid, main.id aid')->find();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->field('sub.*, main.jid, main.id aid')->limit($filter['_limit'])->where($where)->find();
						else $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->field('sub.*, main.jid, main.id aid')->where($where)->find();
					}
				break;
				case 2: // select
				default:
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->field('sub.*, main.jid, main.id aid')->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->field('sub.*, main.jid, main.id aid')->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->field('sub.*, main.jid, main.id aid')->limit($filter['_limit'])->where($where)->order($filter['_order'])->select();
						else $result = $this->alias('main')->join('right join workflow_car sub on sub.id = main.car_id')->field('sub.*, main.jid, main.id aid')->where($where)->order($filter['_order'])->select();
					}
				break;
			}

			return $result;
		}
	}