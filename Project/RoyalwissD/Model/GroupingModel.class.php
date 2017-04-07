<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-25
	 * Time: 17:28
	 */
	namespace RoyalwissD\Model;

	use CMS\Logic\Session;
	use Exception;
	use General\Logic\Time;

	class GroupingModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'grouping';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = ['meetingID' => 'mid'];

		/**
		 * 创建分组
		 *
		 * @param array $data 分组信息
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建分组成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建分组失败'
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
			if(isset($meeting_id)) $where .= " and mid = $meeting_id";
			$sql = "
SELECT * FROM (
	SELECT
		g.id,
		g.mid,
		g.name,
		g.name_pinyin,
		g.capacity,
		g.status,
		g.comment,
		g.creator creator_code,
		u1.name creator,
		g.creatime,
		(
			SELECT count(gm.cid)
			FROM meeting_royalwiss_deal.grouping_member gm
			JOIN meeting_royalwiss_deal.client c ON gm.cid = c.id AND c.status <> 2
			JOIN meeting_royalwiss_deal.attendee a ON a.cid = c.id AND a.status <> 2
			WHERE gm.mid = g.mid
			AND g.id = gm.gid
			AND gm.STATUS = 1
			AND a.mid = g.mid
			AND gm.process_status = 1
		) assigned
	FROM meeting_royalwiss_deal.grouping g
	LEFT JOIN meeting_common.user u1 ON u1.id = g.creator AND u1.status <> 2
) tab
$where
$order
";

			return $this->query($sql);
		}

		/**
		 * 获取分组的成员列表
		 *
		 * @param int      $meeting_id 会议ID
		 * @param int|null $group_id   分组ID
		 *
		 * @return array
		 */
		public function getMember($meeting_id, $group_id = null){
			if(!($group_id == null)) $group_filter = " AND g.id = $group_id";
			$sql    = "
SELECT
	c.*
FROM meeting_royalwiss_deal.grouping g
JOIN meeting_royalwiss_deal.grouping_member gm ON gm.gid = g.id AND gm.status <> 2
JOIN meeting_royalwiss_deal.client c ON c.id = gm.cid AND c.status <> 2
JOIN meeting_royalwiss_deal.attendee a ON a.cid = c.id AND a.status <> 2 AND a.mid = g.mid
WHERE g.status <> 2 AND gm.process_status = 1 AND g.mid = $meeting_id $group_filter
";
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
			$sql    = "
SELECT
	id value,
	concat(name, ' [', assigned, '/',capacity, ']') html,
	concat(name,',',name_pinyin)
FROM (
	SELECT
		g.*,
		(
			SELECT count(gm.cid)
			FROM meeting_royalwiss_deal.grouping_member gm
			JOIN meeting_royalwiss_deal.client c ON gm.cid = c.id AND c.status <> 2
			JOIN meeting_royalwiss_deal.attendee a ON a.cid = c.id AND a.status <> 2
			WHERE gm.mid = g.mid
			AND g.id = gm.gid
			AND gm.STATUS = 1
			AND gm.process_status = 1
			AND a.mid = g.mid
		) assigned
	FROM meeting_royalwiss_deal.grouping g
) tab
WHERE status = 1 AND mid = $meeting_id AND ((assigned < capacity AND capacity > 0) OR capacity = 0)
";
			$result = $this->query($sql);

			return $result;
		}

		/**
		 * 为分组添加组员
		 *
		 * @param int              $meeting_id 会议ID
		 * @param int              $group_id   分组ID
		 * @param int|string|array $client_id  客户ID
		 *
		 * @return array
		 */
		public function addMember($meeting_id, $group_id, $client_id){
			if(is_null($client_id) || $client_id == '' || !$client_id) return [
				'status'  => false,
				'message' => '没有选择任何客户'
			];
			elseif(is_numeric($client_id)) $client_id = [$client_id];
			elseif(is_string($client_id)) $client_id = explode(',', $client_id);
			elseif(is_array($client_id)) ;
			else return ['status' => false, 'message' => '参数类型错误'];
			// 判断是否满员
			if(!$this->fetch(['id' => $group_id])) return [
				'status'  => false,
				'message' => '找不到分组',
			];
			$group_record = $this->getObject();
			$member_list  = $this->getMember($meeting_id, $group_id);
			if(((count($member_list)+count($client_id))>$group_record['capacity']) && $group_record['capacity']>0) return [
				'status'  => false,
				'message' => '分组失败：人数超过上限',
			];
			/** @var \RoyalwissD\Model\GroupMemberModel $group_member_model */
			$group_member_model = D('RoyalwissD/GroupMember');
			// 删除这些成员在其他的分组的记录
			$group_member_model->modify([
				'mid' => $meeting_id,
				'cid' => ['in', $client_id]
			], [
				'quit_time'      => Time::getCurrentTime(),
				'process_status' => 0
			]);
			// 保存组员记录
			$group_member_data = [];
			foreach($client_id as $val) $group_member_data[] = [
				'mid'      => $meeting_id,
				'gid'      => $group_id,
				'cid'      => $val,
				'creatime' => Time::getCurrentTime(),
				'creator'  => Session::getCurrentUser()
			];
			$result = $group_member_model->addAll($group_member_data);

			return $result ? [
				'status'  => true,
				'message' => '分组成功',
			] : [
				'status'  => false,
				'message' => '分组失败'
			];
		}

		/**
		 * 为分组添加组员
		 *
		 * @param int             $meeting_id 会议ID
		 * @param int             $group_id   分组ID
		 * @param int|string|null $client_id  客户ID
		 * @param bool            $remove_all 是否从分组移除所有成员
		 *
		 * @return array
		 */
		public function removeMember($meeting_id, $group_id, $client_id, $remove_all = false){
			/** @var \RoyalwissD\Model\GroupMemberModel $group_member_model */
			$group_member_model = D('RoyalwissD/GroupMember');
			$result             = $remove_all ? $group_member_model->modify([
				'gid' => $group_id,
				'mid' => $meeting_id
			], [
				'quit_time'      => Time::getCurrentTime(),
				'process_status' => 0
			]) : $group_member_model->modify([
				'gid' => $group_id,
				'mid' => $meeting_id,
				'cid' => $client_id
			], [
				'quit_time'      => Time::getCurrentTime(),
				'process_status' => 0
			]);

			return $result['status'] ? [
				'status'  => true,
				'message' => '移除成员成功'
			] : [
				'status'  => false,
				'message' => '移除成员失败'
			];
		}
	}