<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-14
	 * Time: 16:17
	 */
	namespace Mobile\Logic;

	use Core\Logic\CoreLogic;

	class MobileLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function getReport2($mid,$order_column, $order_method, $area = '', $no = '', $keyword = ''){
			$signed = $no==''?'':' and b.sign_status = 1';
			$sql = "SELECT *, total-signed nosigned
FROM(
		SELECT convert(unit using gbk) unit, count(*) total, convert(column4 using gbk) area, convert(column5 using gbk) `no`,
			(
				SELECT count(*)
				FROM user_client a1
				JOIN workflow_join b1 ON b1.cid = a1.id
				WHERE mid = $mid AND a1.type <> '会所' AND b1.`status` = 1 AND b1.sign_status = 1 AND a.unit = a1.unit
			) signed
		FROM user_client a
		JOIN workflow_join b ON b.cid = a.id
		WHERE b.mid = $mid AND a.type <> '会所' AND b.`status` = 1 and b.review_status = 1 $signed
		GROUP BY unit
	) temp_table
where area like '%$area%' and `no` like '%$no%' and unit like '%$keyword%'
order by $order_column $order_method, total $order_method";
			$result = M()->query($sql);
			return $result;
		}
		
		public function getReport2Area($mid){
			$sql = "SELECT DISTINCT column4 area
				FROM user_client a
				JOIN workflow_join b ON b.cid = a.id
				where b.mid = $mid AND a.type <> '会所' AND b.`status` = 1";
			return M()->query($sql);
		}

		public function getReport2No($mid){
			$sql = "SELECT DISTINCT column5 `no`
				FROM user_client a
				JOIN workflow_join b ON b.cid = a.id
				where b.mid = $mid AND a.type <> '会所' AND b.`status` = 1";
			return M()->query($sql);
		}
		public function getReport2Statistics($mid, $area, $no, $keyword){
			$sql = "select (
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
			where workflow_join.mid=$mid
				  AND user_client.type <> '会所'
				   and workflow_join.status = 1
				   and workflow_join.review_status = 1
				   and workflow_join.column4 like '%$area%'
and user_client.unit like '%$keyword%'
) total_count, -- 参会总数
 (
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
			where workflow_join.mid=$mid
				  AND user_client.type <> '会所'
				AND workflow_join.sign_status = 1
				 and workflow_join.status = 1
				   and workflow_join.column4 like '%$area%'
and user_client.unit like '%$keyword%'
	) total_sign_count, -- 签到总数
	(
		select count(*) from (
								 SELECT count(*)
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND user_client.type <> '会所'
									    and workflow_join.status = 1
				   and workflow_join.column4 like '%$area%'
and user_client.unit like '%$keyword%'
								 GROUP BY user_client.unit
							 ) unit_table
	) total_unit_count, -- 会所总数
	(
		select count(*) from (
								 SELECT unit
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND user_client.type <> '会所'
									 and workflow_join.sign_status = 1
									  and workflow_join.status = 1
				   and workflow_join.column4 like '%$area%'
and user_client.unit like '%$keyword%'
								 GROUP BY user_client.unit
							 ) unit_table
	) total_unit_sign_count, --  签到会所数,
	(select count(*) from workflow_join
join user_client on user_client.id = workflow_join.cid
where mid = $mid and workflow_join.`status` = 1 and workflow_join.sign_status = 1
and cid in (select id from user_client where type <> '会所')
and column4 like '%$area%' -- 区域
and column5 like '%$no%' -- 新老客
	) total_sign_no_count,
	(select count(*) from (select count(*) from workflow_join
join user_client on user_client.id = workflow_join.cid
where mid = $mid and workflow_join.`status` = 1 and workflow_join.sign_status = 1
and cid in (select id from user_client where type <> '会所')
and column4 like '%$area%' -- 区域
and column5 like '%$no%' -- 新老客
group by unit
	) tabs) total_unit_sign_no_count
	";
			$result = M()->query($sql);
			return $result[0];
		}

		//		public function getReport2Statistics($mid){
//			$sql = "select (
//		SELECT count(*)
//		FROM user_client
//		JOIN workflow_join ON workflow_join.cid=user_client.id
//			where workflow_join.mid=$mid
//				  AND user_client.type <> '会所'
//				   and workflow_join.status = 1
//	) total_count, -- 参会总数
// (
//		SELECT count(*)
//		FROM user_client
//		JOIN workflow_join ON workflow_join.cid=user_client.id
//			where workflow_join.mid=$mid
//				  AND user_client.type <> '会所'
//				AND workflow_join.sign_status = 1
//				 and workflow_join.status = 1
//	) total_sign_count, -- 签到总数
//	(
//		select count(*) from (
//								 SELECT count(*)
//								 FROM user_client
//								 JOIN workflow_join ON workflow_join.cid=user_client.id
//								 WHERE workflow_join.mid=$mid
//									   AND user_client.type <> '会所'
//									    and workflow_join.status = 1
//								 GROUP BY user_client.unit
//							 ) unit_table
//	) total_unit_count, -- 会所总数
//	(
//		select count(*) from (
//								 SELECT unit
//								 FROM user_client
//								 JOIN workflow_join ON workflow_join.cid=user_client.id
//								 WHERE workflow_join.mid=$mid
//									   AND user_client.type <> '会所'
//									 and workflow_join.sign_status = 1
//									  and workflow_join.status = 1
//								 GROUP BY user_client.unit
//							 ) unit_table
//	) total_unit_sign_count --  签到会所数";
//			$result = M()->query($sql);
//			return $result[0];
//		}

		public function getReport2Client($mid,$order_column, $order_method, $unit = ''){
			$sql = "
				SELECT user_client.*, sign_status
				FROM user_client a1
				JOIN workflow_join b1 ON b1.cid = a1.id
				WHERE mid = $mid AND a1.type <> '会所' AND b1.`status` = 1 and unit = '$unit'
order by $order_column $order_method";
			$result = M()->query($sql);
			return $result;
		}

		public function getReport2ClientStatistics($mid, $unit){
			$sql = "select (
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
			where workflow_join.mid=$mid
				  AND user_client.type <> '会所'
				   and workflow_join.status = 1
				   and user_client.unit = '$unit'
	) total_count, -- 参会总数
 (
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
			where workflow_join.mid=$mid
				  AND user_client.type <> '会所'
				AND workflow_join.sign_status = 1
				 and workflow_join.status = 1
				  and user_client.unit = '$unit'
	) total_sign_count -- 签到总数
	";
			$result = M()->query($sql);
			return $result[0];
		}

		public function getReport2Group($mid, $keyword){
			$sql  = "select * from (select user_client.*, workflow_group.code, workflow_group.id gid from workflow_group_member
join workflow_group on workflow_group_member.gid = workflow_group.id
join user_client on workflow_group_member.cid = user_client.id
where workflow_group.mid = $mid
and workflow_group.status = 1
and workflow_group_member.status = 1) tabs
where unit like '%$keyword%' or name like '%$keyword%' or code like '%$keyword%'";
			$result = M()->query($sql);
			return $result;
		}
	}