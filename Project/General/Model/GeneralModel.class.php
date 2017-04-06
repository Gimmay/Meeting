<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-2
	 * Time: 15:06
	 */
	namespace General\Model;

	use Think\Model;

	class GeneralModel extends Model{
		public function _initialize(){
			parent::_initialize();
		}

		const DATABASE_NAME = 'meeting_common';
		// todo sql语句全部使用动态库名和表名
		const STATUS = [
			0 => '禁用',
			1 => '可用',
			2 => '删除'
		];
		/** @var array 调用fetch方法后若记录存在写入的记录数据 */
		protected $object = [];
		/** @var int 调用fetch方法后若记录存在写入的记录ID */
		protected $objectID = 0;

		public function getObject(){
			return $this->object;
		}

		public function getObjectID(){
			return $this->objectID;
		}

		public function handlerException($message){
			if(stripos($message, 'Duplicate entry') !== false) return [
				'status'  => false,
				'message' => "该记录已经存在"
			];
			if(stripos($message, 'a foreign key constraint fails') !== false) return [
				'status'  => false,
				'message' => "部分字段违反外键约束"
			];
			if(stripos($message, 'doesn\'t have a default value') !== false) return [
				'status'  => false,
				'message' => "未提交非空字段"
			];
			if(stripos($message, 'Incorrect decimal value') !== false) return [
				'status'  => false,
				'message' => "错误的浮点数据"
			];
			if(stripos($message, 'Incorrect date value') !== false) return [
				'status'  => false,
				'message' => "错误的日期类型"
			];
			if(stripos($message, 'Out of range value for column') !== false) return [
				'status'  => false,
				'message' => "数据超过指定范围"
			];
			if(stripos($message, 'Incorrect datetime value') !== false) return [
				'status'  => false,
				'message' => "错误的时间类型"
			];

			return ['status' => true];
		}

		/**
		 * 查询单条记录数据
		 *
		 * @param array $condition 查询条件
		 *
		 * @return bool 查询成功true 查询失败false
		 */
		public function fetch($condition = []){
			$record = $this->where($condition)->find();
			if($record){
				$this->object   = $record;
				$this->objectID = $record['id'];

				return true;
			}
			else return false;
		}

		/**
		 * 检索并计数
		 *
		 * @param array $condition 查询条件
		 *
		 * @return int
		 */
		public function tally($condition = []){
			return $this->where($condition)->count();
		}

		/**
		 * 修改记录
		 *
		 * @param array|string $condition 查询条件
		 * @param array        $data      修改数据
		 *
		 * @return array 执行结果
		 */
		public function modify($condition = [], $data = []){
			$result = $this->where($condition)->save($data);

			return $result ? ['status' => true, 'message' => '修改成功'] : ['status' => false, 'message' => '未做修改'];
		}

		/**
		 * 启用记录
		 *
		 * @param array $condition 筛选条件
		 *
		 * @return array
		 */
		public function enable($condition = []){
			$result = $this->where($condition)->save(['status' => 1]);

			return $result ? ['status' => true, 'message' => '启用成功'] : ['status' => false, 'message' => '启用失败'];
		}

		/**
		 * 禁用记录
		 *
		 * @param array $condition 筛选条件
		 *
		 * @return array
		 */
		public function disable($condition = []){
			$result = $this->where($condition)->save(['status' => 0]);

			return $result ? ['status' => true, 'message' => '禁用成功'] : ['status' => false, 'message' => '禁用失败'];
		}

		/**
		 * 删除记录
		 *
		 * @param array $condition 筛选条件
		 *
		 * @return array
		 */
		public function drop($condition = []){
			$result = $this->where($condition)->save(['status' => 2]);

			return $result ? ['status' => true, 'message' => '删除成功'] : ['status' => false, 'message' => '删除失败'];
		}

		/**
		 * 锁表
		 *
		 * @param string $type
		 *
		 * @return false|int
		 */
		public function lock($type = 'read'){
			$type  = strtolower($type);
			$table = $this->tableName;
			if($type == 'write') $type = 'write';
			else $type = 'read';

			return $this->execute("lock tables $table $type");
		}

		/**
		 * 解锁
		 *
		 * @return false|int
		 */
		public function unlock(){
			return $this->execute("unlock tables");
		}
	}