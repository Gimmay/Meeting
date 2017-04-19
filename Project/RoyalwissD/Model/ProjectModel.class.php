<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-15
	 * Time: 9:51
	 */
	namespace RoyalwissD\Model;

	use Exception;
	use General\Model\GeneralModel;
	use General\Model\UserModel;

	class ProjectModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName = 'project';
		const TABLE_NAME = 'project';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid'
		];

		/**
		 * 创建项目
		 *
		 * @param array $data 项目信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建项目成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建项目失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		/**
		 * 获取项目列表
		 *
		 * @param array $control 查询条件
		 *
		 * @return mixed
		 */
		public function getList($control = []){
			$table_project      = $this->tableName;
			$table_user         = UserModel::TABLE_NAME;
			$table_project_type = ProjectTypeModel::TABLE_NAME;
			$common_database    = GeneralModel::DATABASE_NAME;
			$this_database      = self::DATABASE_NAME;
			$keyword            = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order              = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status             = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id         = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$where              = ' WHERE 0 = 0 ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				and (
					name like '%$keyword%'
					or name_pinyin like '%$keyword%'
				)";
			}
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			$sql    = "
SELECT * FROM (
	SELECT
		p1.id,
		p1.mid,
		p1.type type_code,
		p1.name,
		p1.name_pinyin,
		p1.price,
		p1.stock,
		p1.total,
		p1.is_stock_limit,
		p1.status,
		p1.comment,
		p1.creator creator_code,
		p1.creatime,
		pt1.name type,
		u1.name creator
	FROM $this_database.$table_project p1
	JOIN $this_database.$table_project_type pt1 ON p1.type = pt1.id
	LEFT JOIN $common_database.$table_user u1 ON u1.id = p1.creator AND u1.status <> 2
) tab
$where
$order";
			$result = $this->query($sql);

			return $result;
		}

		/**
		 * 入库
		 *
		 * @param int $project_id 项目ID
		 * @param int $number     数量
		 *
		 * @return array
		 */
		public function input($project_id, $number){
			if($this->fetch(['id' => $project_id, 'status' => 1])){
				$project = $this->getObject();
				$result  = $this->where(['id' => $project_id])->save([
					'stock' => $project['stock']+$number,
					'total' => $project['total']+$number
				]);

				return $result ? ['status' => true, 'message' => '入库成功'] : ['status' => false, 'message' => '入库失败'];
			}
			else return ['status' => false, 'message' => '找不到项目'];
		}

		/**
		 * 出库
		 *
		 * @param int $project_id 项目ID
		 * @param int $number     数量
		 *
		 * @return array
		 */
		public function output($project_id, $number){
			if($this->fetch(['id' => $project_id, 'status' => 1])){
				$project = $this->getObject();
				$stock   = $project['stock']-$number;
				$total   = $project['total']-$number;
				$result  = $this->where(['id' => $project_id])->save([
					'stock' => $stock<0 ? 0 : $stock,
					'total' => $total<0 ? 0 : $total
				]);

				return $result ? ['status' => true, 'message' => '出库成功'] : ['status' => false, 'message' => '出库失败'];
			}
			else return ['status' => false, 'message' => '找不到项目'];
		}

		/**
		 * 售卖
		 *
		 * @param int $project_id 项目ID
		 * @param int $number     数量
		 *
		 * @return array
		 */
		public function sell($project_id, $number = 1){
			if($this->fetch(['id' => $project_id, 'status' => 1])){
				$project = $this->getObject();
				$stock   = $project['stock']-$number;
				if($stock<0 && $project['is_stock_limit'] == 1) return ['status' => false, 'message' => '售出失败：库存不足'];
				$result = $this->where(['id' => $project_id])->save(['stock' => $stock,]);

				return $result ? ['status' => true, 'message' => '售出成功'] : ['status' => false, 'message' => '售出失败'];
			}
			else return ['status' => false, 'message' => '找不到项目'];
		}

		/**
		 * 获取Select插件的数据列表
		 *
		 * @param int      $meeting_id      会议ID
		 * @param bool     $just_show_available 是否只显示有库存的可用项目
		 * @param int|null $project_type_id 项目类型ID
		 *
		 * @return array
		 */
		public function getSelectedList($meeting_id, $just_show_available = true, $project_type_id = null){
			$table_project      = $this->tableName;
			$table_project_type = ProjectTypeModel::TABLE_NAME;
			$this_database      = self::DATABASE_NAME;
			$condition1 = $condition2 = '';
			if($project_type_id) $condition1 = " and p.type = $project_type_id ";
			if($just_show_available) $condition2 = " AND IF(p.is_stock_limit=1, p.stock>0, TRUE) ";
			$sql    = "
SELECT p.id value, concat('[',pt.name,'] ',p.name) html, concat(p.name,',',pt.name,',',p.name_pinyin,',',pt.name_pinyin) keyword, concat(pt.id,',',p.price) ext
FROM $this_database.$table_project p
JOIN $this_database.$table_project_type pt on pt.id = p.type AND p.mid = pt.mid
WHERE p.status = 1
AND pt.status = 1
AND p.mid = $meeting_id
$condition1
$condition2
";
			$result = $this->query($sql);

			return $result;
		}

		/**
		 * 带库存检测以及失败退回的售出
		 *
		 * @param array $project_data 售出的项目数据<br>结构示例：['id'=>[number, number, ...], 'number'=>[number, number, ...]]
		 *
		 * @return array
		 */
		public function sellSafely($project_data){
			// 参数检测
			if(!isset($project_data['id']) || !isset($project_data['number'])) return [
				'status'  => false,
				'message' => '缺少必要参数'
			];
			if(!is_array($project_data['id']) || !is_array($project_data['number'])) return [
				'status'  => false,
				'message' => '参数数据类型错误'
			];
			if(count($project_data['id']) != count($project_data['number'])) return [
				'status'  => false,
				'message' => '项目数和扣减库存参数数目不相等'
			];
			// 合并相同的项目ID
			$temp_project_data = [];
			foreach($project_data['id'] as $key => $project_id){
				if(!isset($temp_project_data[$project_id])) $temp_project_data[$project_id] = 0;
				$temp_project_data[$project_id] += $project_data['number'][$key];
			}
			// 获取有库存限制的项目
			$this->lock('write');
			$stock_limited_project = $this->where([
				'id'             => ['in', $project_data['id']],
				'is_stock_limit' => 1
			])->select();
			// 开始售出
			$sold_project             = [];
			$not_enough_stock_project = [];
			foreach($stock_limited_project as $project){
				$sell_number = $temp_project_data[$project['id']];
				$result      = $this->sell($project['id'], $sell_number);
				if($result['status']) $sold_project[] = $project['id'];
				else{
					$not_enough_stock_project = $project;
					break;
				}
			}
			// 判定是否全部售出
			if(count($sold_project) != count($stock_limited_project)){
				// 回退
				foreach($sold_project as $project_id) $this->where(['id' => $project_id])->setInc('stock', $temp_project_data[$project_id]);
				$this->unlock();

				return ['status' => false, 'message' => "[$not_enough_stock_project[name]]项目库存不足"];
			}
			else{
				$this->unlock();

				return ['status' => true, 'message' => '售出成功'];
			}
		}

		/**
		 * 归还项目
		 *
		 * @param array $project_data 归还的项目数据<br>结构示例：['id'=>[number, number, ...], 'number'=>[number, number, ...]]
		 *
		 * @return array
		 */
		public function refund($project_data){
			// 参数检测
			if(!isset($project_data['id']) || !isset($project_data['number'])) return [
				'status'  => false,
				'message' => '缺少必要参数'
			];
			if(!is_array($project_data['id']) || !is_array($project_data['number'])) return [
				'status'  => false,
				'message' => '参数数据类型错误'
			];
			if(count($project_data['id']) != count($project_data['number'])) return [
				'status'  => false,
				'message' => '项目数和扣减库存参数数目不相等'
			];
			// 合并相同的项目ID
			$temp_project_data = [];
			foreach($project_data['id'] as $key => $project_id){
				if(!isset($temp_project_data[$project_id])) $temp_project_data[$project_id] = 0;
				$temp_project_data[$project_id] += $project_data['number'][$key];
			}
			// 获取有库存限制的项目
			$stock_limited_project = $this->where([
				'id'             => ['in', $project_data['id']],
				'is_stock_limit' => 1
			])->select();
			$result_count          = 0;
			foreach($stock_limited_project as $project){
				$refund_number = $temp_project_data[$project['id']];
				$result        = $this->where(['id' => $project['id']])->setInc('stock', $refund_number);
				if($result) $result_count++;
			}

			return $result_count>=count($stock_limited_project) ? [
				'status'  => true,
				'message' => '操作成功'
			] : ['status' => false, 'message' => '操作失败'];
		}
	}