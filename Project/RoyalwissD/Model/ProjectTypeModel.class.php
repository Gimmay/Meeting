<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-15
	 * Time: 10:16
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class ProjectTypeModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'project_type';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID' => 'mid'
		];

		/**
		 * 创建项目类型
		 *
		 * @param array $data 项目类型信息
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

		public function getList($control = []){
			$keyword    = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order      = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status     = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$where      = ' WHERE 0 = 0 ';
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
		pt.id,
		pt.mid,
		pt.name,
		pt.name_pinyin,
		pt.status,
		pt.comment,
		pt.creator creator_code,
		pt.creatime,
		u1.name creator
	FROM meeting_royalwiss_deal.project_type pt
	LEFT JOIN meeting_common.user u1 ON u1.id = pt.creator AND u1.status <> 2
) tab
$where
$order";
			$result = $this->query($sql);

			return $result;
		}

		/**
		 * 获取Select插件的数据列表
		 *
		 * @param int $meeting_id 会议ID
		 *
		 * @return array
		 */
		public function getSelectedList($meeting_id){
			return $this->where([
				'status' => 1,
				'mid'    => $meeting_id
			])->field('id value, name html, concat(name,\',\',name_pinyin) keyword')->select();
		}
	}