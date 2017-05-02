<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-4-28
	 * Time: 15:02
	 */

	namespace General\Logic;

	class Tree{
		/** @var int 根节点的值 */
		private $_root = 0;
		/** @var array 映射表 */
		private $_reflect = [];
		/** @var string 父节点的数据索引 */
		private $_parentIndex = 'parent_id';
		/** @var string 自身节点的数据索引 */
		private $_selfIndex = 'id';
		/** 每个节点的子节点树的索引 */
		private $_listIndex = '_list';
		/** 每个节点的子节点树的长度 */
		private $_listLength = '_length';
		/** 映射表的结束成员 */
		const END_CHAR = '#$STOP$#';

		public function __construct($opt = []){
			if(isset($opt['root'])) $this->_root = $opt['root'];
			if(isset($opt['parentIndex'])) $this->_parentIndex = $opt['parentIndex'];
			if(isset($opt['selfIndex'])) $this->_selfIndex = $opt['selfIndex'];
			if(isset($opt['listIndex'])) $this->_listIndex = $opt['listIndex'];
			if(isset($opt['listLength'])) $this->_listLength = $opt['listLength'];
			$this->_reflect[$this->_root] = [self::END_CHAR];
		}

		/**
		 * 设定子节点
		 *
		 * @param int   $current_node_id   当前节点的ID
		 * @param array $level_list        层级列表
		 * @param array $result            数据列表
		 * @param array $current_node_data 当前节点的数据
		 *
		 * @return array
		 */
		private function _setChildren($current_node_id, $level_list, &$result, $current_node_data){
			if($level_list[$current_node_id] === self::END_CHAR){
				$result[$this->_listIndex][$current_node_data[$this->_selfIndex]] = $current_node_data;

				return $result;
			}
			else{
				if(isset($result[$this->_listIndex])) $parent = &$result[$this->_listIndex][$level_list[$current_node_id]];
				else $parent = &$result[$level_list[$current_node_id]];

				return $this->_setChildren($current_node_id+1, $level_list, $parent, $current_node_data);
			}
		}

		/**
		 * 设定映射表的节点层级列表
		 *
		 * @param int   $current_node_id   当前节点的ID
		 * @param array $current_node_data 当前节点数据
		 */
		private function _setLevelList($current_node_id, $current_node_data){
			$level_list                       = $this->_reflect[$current_node_data[$this->_parentIndex]];
			$end_index                        = array_search(self::END_CHAR, $level_list);
			$level_list[$end_index]           = $current_node_id;
			$level_list[]                     = self::END_CHAR;
			$this->_reflect[$current_node_id] = $level_list;
		}

		/**
		 * 生成树
		 *
		 * @param array $list 数据列表
		 *
		 * @return array
		 */
		public function makeTree($list){
			$parent_index = $this->_parentIndex;
			$self_index   = $this->_selfIndex;
			$result       = [];
			foreach($list as $key => $node){
				if($node[$parent_index] == $this->_root) $result[$node[$self_index]] = $node;
				else $this->_setChildren(0, $this->_reflect[$node[$parent_index]], $result, $node);
				$this->_setLevelList($node[$self_index], $node);
			}

			return $result;
		}

		/**
		 * 格式化树
		 *
		 * @param array $list 树数组
		 *
		 * @return array
		 */
		public function formatTree($list){
			$result = [];
			foreach($list as $value){
				if(isset($value[$this->_listIndex])){
					$result[] = $this->formatTree($value[$this->_listIndex]);
				}
				else{
					$result[] = $value;
				}
			}

			return $result;
		}
	}