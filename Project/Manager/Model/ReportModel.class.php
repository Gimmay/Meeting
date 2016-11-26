<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-20
	 * Time: 16:25
	 */
	namespace Manager\Model;

	class ReportModel extends ManagerModel{
		protected $autoCheckFields = false;

		public function _initialize(){
			parent::_initialize();
		}

		public function getJoinReceivablesList($type, $filter = []){
			$where = " where 1 = 1";
			if(isset($filter['cid'])) $where .= " and sub.id = $filter[cid]";
			if(isset($filter['id'])) $where .= " and main.id = $filter[id]";
			if(isset($filter['mid'])) $where .= " and main.mid = $filter[mid]";
			if(isset($filter['status'])){
				$status = strtolower($filter['status']);
				if($status == 'not deleted') $where .= " and sub.status != 2 and main.status != 2";
				else $where .= " and main.status = $filter[status]";
			};
			if(isset($filter['sign_status'])){
				$status = strtolower($filter['sign_status']);
				if($status == 'not signed') $where .= " and sign_status != 1";
				else $where .= " and sign_status = $filter[sign_status]";
			}
			if(isset($filter['review_status'])){
				$status = strtolower($filter['review_status']);
				if($status == 'not reviewed') $where .= " and review_status != 1";
				else $where .= " and review_status = $filter[review_status]";
			}
			if(isset($filter['keyword']) && $filter['keyword']){
				$keyword = addslashes($filter['keyword']);
				$where .= " and(";
				$where .= " sub.unit like '%$keyword%'".' or';
				$where .= " sub.mobile like '%$keyword%'".' or';
				$where .= " sub.name like '%$keyword%'".' or';
				$where .= " sub.pinyin_code like '%$keyword%'".' or';
				$where .= " meeting.name like '%$keyword%'";
				$where .= ")";
			}
			if(isset($filter['_limit'])) $limit = " limit $filter[_limit]";
			if(isset($filter['_order'])) $order = " order by $filter[_order]";
			$sql = "select * from (select
	main.cid,
	main.id,
	main.mid,
	meeting.name meeting_name,
	concat(meeting.start_time, ' — ', meeting.end_time) meeting_time,
	sub.name,
	sub.gender,
	(year(now()) - year(sub.birthday)) age,
	sub.mobile,
	(select sum(price) from workflow_receivables where workflow_receivables.mid = meeting.id and workflow_receivables.cid = main.cid) price,
	main.sign_status,
	sub.unit,
	sub.team,
	sub.service_consultant,
	sub.develop_consultant,
	sub.type,
	main.registration_type,
	main.status,
	sub.comment
from workflow_join main
join user_client sub on main.cid = sub.id
join workflow_meeting meeting on main.mid = meeting.id and meeting.status in (1, 2, 3, 4)
$where
) temp
$order
$limit
";
			$result = $this->query($sql);
			switch((int)$type){
				case 0:
					$result = count($result);
				break;
				case 1: // find
					if($result) $result = $result[0];
				break;
				case 2: // select
				default:
				break;
			}

			return $result;
		}
	}