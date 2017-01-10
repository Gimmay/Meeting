<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-12-16
	 * Time: 10:35
	 */
	namespace Mobile\Model;

	class ManagerModel extends MobileModel{
		protected $autoCheckFields = false;

		public function getReceivablesPrice($meeting_id){
			$sql = "select *, (
	select sum(workflow_receivables_option.price)
	from workflow_receivables
	join workflow_receivables_option on workflow_receivables_option.rid = workflow_receivables.id
	join workflow_coupon_item on workflow_coupon_item.id in (workflow_receivables.coupon_ids)
	where workflow_coupon.id = workflow_coupon_item.coupon_id
	) price
from workflow_coupon
where mid = $meeting_id
";
			return $this->query($sql);
		}

		public function getUnitList($mid){
			$sql = "SELECT
	`user_client`.`unit` AS `unit`
FROM
	(
		`user_client`
		JOIN `workflow_join` ON (
			(
				`workflow_join`.`cid` = `user_client`.`id`
			)
		)
	)
WHERE
	(
		(`workflow_join`.`mid` = $mid)
		AND (`workflow_join`.`status` = 1)
		AND (`user_client`.`status` = 1)
		AND (
			`user_client`.`type` <> '内部员工'
		)
	)
GROUP BY
	`user_client`.`unit` ";

			$result = $this->query($sql);
			return $result;
		}
	}