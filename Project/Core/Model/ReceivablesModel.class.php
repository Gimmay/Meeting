<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-10
	 * Time: 16:46
	 */
	namespace Core\Model;

	use Core\Logic\ReceivablesLogic;
	use Exception;

	class ReceivablesModel extends CoreModel{
		protected $tableName       = 'receivables';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function createRecord($data){
			if($this->create($data)){
				try{
					$result = $this->add($data);
					if($result) return ['status' => true, 'message' => '收款成功', 'id' => $result];
					else return ['status' => false, 'message' => '收款失败'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $message];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function createMultiRecord($data){
			try{
				$result = $this->addAll($data);
				if($result) return ['status' => true, 'message' => '收款成功'];
				else return ['status' => false, 'message' => '收款失败'];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);
				if(!$exception['status']) return $exception;

				return ['status' => false, 'message' => $message];
			}
		}

		public function findRecord($type = 2, $filter = []){
			$where = [];
			if(isset($filter['id']) && $filter['id']) $where['id'] = $filter['id'];
			if(isset($filter['cid']) && $filter['cid']) $where['cid'] = $filter['cid'];
			if(isset($filter['mid']) && $filter['mid']) $where['mid'] = $filter['mid'];
			if(isset($filter['payee_id']) && $filter['payee_id']) $where['payee_id'] = $filter['payee_id'];
			if(isset($filter['keyword']) && $filter['keyword']){
			}
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where['status'] = ['neq', 2];
				else $where['status'] = $filter['status'];
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
					if(!isset($filter['_order'])) $filter['_order'] = 'creatime desc';
					if($where == []){
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->order($filter['_order'])->select();
						else $result = $this->order($filter['_order'])->select();
					}
					else{
						if(isset($filter['_limit'])) $result = $this->limit($filter['_limit'])->where($where)->order($filter['_order'])->select();
						else $result = $this->where($where)->order($filter['_order'])->select();
					}
				break;
			}

			return $result;
		}

		public function getData($option = []){
			if(!isset($option['status'])) $status = " and workflow_receivables.status = 1";
			elseif($option['status'] == 'not deleted') $status = ' and workflow_receivables.status <> 2';
			else $status = " and workflow_receivables.status = $option[status]";
			if(!isset($option['cid'])) $cid = '';
			else $cid = " and cid = $option[cid]";
			if(!isset($option['order_number'])) $order_number = '';
			else $order_number = " and order_number = '$option[order_number]'";
			if(!isset($option['_limit'])) $limit = '';
			else $limit = " limit $option[_limit]";
			if(!isset($option['_order'])) $order = '';
			else $order = " order by $option[_order]";
			$sql = "
select * from (
select
					workflow_receivables.id rid,
					workflow_receivables_option.id roid,
					user_client.id client_id,
					user_client.name client_name,
					user_client.unit,
					user_client.pinyin_code,
					order_number,
					workflow_receivables.time,
					payee_id,
					(select name from user_employee where id = payee_id) payee_name,
					workflow_receivables.place,
					workflow_receivables_option.price,
					workflow_receivables.type,
					workflow_receivables.coupon_ids,
					pay_method pay_method_id,
					(select name from workflow_pay_method where id = pay_method) pay_method,
					pos_machine pos_machine_id,
					(select name from workflow_pos_machine where id = pos_machine) pos_machine,
					workflow_receivables_option.type source_type,
					workflow_receivables_option.comment,
					workflow_receivables.status,
					workflow_receivables.creatime
					from workflow_receivables
join workflow_receivables_option on workflow_receivables_option.rid = workflow_receivables.id
join user_client on user_client.id = workflow_receivables.cid
where workflow_receivables.mid = $option[mid] $order_number $status $cid) tt
where (unit like '%$option[keyword]%' or client_name like '%$option[keyword]%' or pinyin_code like '%$option[keyword]%')
$order $limit
";
			/** @var \Core\Model\CouponItemModel $coupon_item_model */
			$coupon_item_model      = D('Core/CouponItem');
			$core_receivables_logic = new ReceivablesLogic();
			$result                 = M()->query($sql);
			foreach($result as $key => $val){
				$result[$key]['type_name']        = $core_receivables_logic->getReceivablesType($val['type']);
				$result[$key]['source_type_name'] = $core_receivables_logic->getReceivablesSourceType($val['source_type']);
				if($val['coupon_ids']) $coupon_list = $coupon_item_model->listRecord(2, ['ids' => $val['coupon_ids']]);
				else $coupon_list = [];
				$result[$key]['coupon_list'] = $coupon_list;
				if(isset($coupon_list[0])) $result[$key]['coupon_id'] = $coupon_list[0]['coupon_id'];
				else $result[$key]['coupon_id'] = 0;
			}

			return $result;
		}

		public function alterRecord($filter, $data){
			if($this->create($data)){
				try{
					$result = $this->where($filter)->save($data);
					if($result) return ['status' => true, 'message' => '修改成功'];
					else return ['status' => false, 'message' => '未做任何修改'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}

		public function sumPrice($filter = []){
			$where = [];
			if(isset($filter['cid']) && $filter['cid']) $where['cid'] = $filter['cid'];
			if(isset($filter['mid']) && $filter['mid']) $where['mid'] = $filter['mid'];
			if($where == []) $result = $this->sum('price');
			else $result = $this->where($where)->sum('price');

			return $result;
		}

		public function deleteRecord($filter){
			if($this->create()){
				try{
					$result = $this->where($filter)->save(['status' => 2]);
					if($result) return ['status' => true, 'message' => '删除成功'];
					else return ['status' => false, 'message' => '没有删除任何收款信息'];
				}catch(Exception $error){
					$message   = $error->getMessage();
					$exception = $this->handlerException($message);
					if(!$exception['status']) return $exception;
					else return ['status' => false, 'message' => $this->getError()];
				}
			}
			else return ['status' => false, 'message' => $this->getError()];
		}
	}