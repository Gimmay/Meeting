<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-15
	 * Time: 10:49
	 */
	namespace RoyalwissD\Model;

	use Exception;

	class ReceivablesOrderModel extends RoyalwissDModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $tableName       = 'receivables_order';
		protected $autoCheckFields = true;
		protected $connection      = 'DB_CONFIG_ROYALWISS_DEAL';
		const CONTROL_COLUMN_PARAMETER_SELF = [
			'meetingID'    => 'mid',
			'orderID'      => 'oid',
			'clientID'     => 'cid',
			'detailID'     => 'did',
			'reviewStatus' => 'review_status'
		];
		/** 审核状态 */
		const REVIEW_STATUS = [
			0 => '未审核',
			1 => '已审核',
			2 => '取消审核'
		];

		/**
		 * 创建收款订单数据
		 *
		 * @param array $data 收款订单数据
		 *
		 * @return array
		 */
		public function create($data){
			try{
				$result = $this->add($data);

				return $result ? ['status' => true, 'message' => '创建收款订单数据成功', 'id' => $result] : [
					'status'  => false,
					'message' => '创建收款订单数据失败'
				];
			}catch(Exception $error){
				$message   = $error->getMessage();
				$exception = $this->handlerException($message);

				return !$exception['status'] ? $exception : ['status' => false, 'message' => $this->getError()];
			}
		}

		public function getList($control = []){
			$keyword       = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
			$order         = $control[self::CONTROL_COLUMN_PARAMETER['order']];
			$status        = $control[self::CONTROL_COLUMN_PARAMETER['status']];
			$meeting_id    = $control[self::CONTROL_COLUMN_PARAMETER_SELF['meetingID']];
			$client_id     = $control[self::CONTROL_COLUMN_PARAMETER_SELF['clientID']];
			$order_id      = $control[self::CONTROL_COLUMN_PARAMETER_SELF['orderID']];
			$detail_id     = $control[self::CONTROL_COLUMN_PARAMETER_SELF['detailID']];
			$review_status = $control[self::CONTROL_COLUMN_PARAMETER_SELF['reviewStatus']];
			$where         = ' WHERE 0 = 0 ';
			if(isset($order)) $order = " ORDER BY $order";
			else $order = ' ';
			if(isset($keyword)){
				$keyword = $control[self::CONTROL_COLUMN_PARAMETER['keyword']];
				$where .= "
				and (
					client like '%$keyword%'
					or client_pinyin like '%$keyword%'
					or unit like '%$keyword%'
					or unit_pinyin like '%$keyword%'
					or project like '%$keyword%'
					or project_pinyin like '%$keyword%'
					or project_type like '%$keyword%'
					or project_type_pinyin like '%$keyword%'
				)";
			}
			if(isset($status) && isset($status[0]) && isset($status[1])) $where .= " and status $status[0] $status[1] ";
			if(isset($client_id) && isset($client_id[0]) && isset($client_id[1])) $where .= " and cid $client_id[0] $client_id[1] ";
			if(isset($order_id) && isset($order_id[0]) && isset($order_id[1])) $where .= " and id $order_id[0] $order_id[1] ";
			if(isset($detail_id) && isset($detail_id[0]) && isset($detail_id[1])) $where .= " and did $detail_id[0] $detail_id[1] ";
			if(isset($review_status) && isset($review_status[0]) && isset($review_status[1])) $where .= " and review_status $review_status[0] $review_status[1] ";
			if(isset($meeting_id)) $where .= " and mid = $meeting_id ";
			$sql    = "
SELECT * FROM (
	SELECT
		ro.id,
		ro.order_number,
		ro.cid,
		ro.mid,
		c.name client,
		c.name_pinyin client_pinyin,
		c.unit,
		c.unit_pinyin unit_pinyin,
		ro.payee payee_code,
		u1.name payee,
		u1.name payee_pinyin,
		ro.place,
		ro.price total_price,
		ro.time,
		ro.status,
		ro.review_status,
		ro.creatime,
		ro.creator creator_code,
		u2.name creator,
		u2.name_pinyin creator_pinyin,
		rp.id prid,
		p.id project_code,
		p.name project,
		p.name_pinyin project_pinyin,
		pt.id project_type_code,
		pt.name project_type,
		pt.name_pinyin project_type_pinyin,
		rp.coupon_item_id,
		1 _type,
		rd.id did,
		rd.price,
		rd.pay_method pay_method_code,
		pam.name pay_method,
		pam.name_pinyin pay_method_pinyin,
		rd.pos_machine pos_machine_code,
		pom.name pos_machine,
		pom.name_pinyin pos_machine_pinyin,
		rd.source,
		rd.comment
	FROM meeting_royalwiss_deal.receivables_order ro
	JOIN meeting_royalwiss_deal.receivables_project rp ON ro.id = rp.oid AND rp.status <> 2
	JOIN meeting_royalwiss_deal.receivables_detail rd ON rp.id = rd.pid AND rd.status <> 2
	LEFT JOIN meeting_common.user u1 ON u1.id = ro.payee AND u1.status <> 2
	LEFT JOIN meeting_common.user u2 ON u2.id = ro.creator AND u2.status <> 2
	JOIN meeting_royalwiss_deal.client c ON c.id = ro.cid AND c.status <> 2
	JOIN meeting_royalwiss_deal.attendee a ON a.cid = c.id AND a.status <> 2 AND a.mid = ro.mid
	LEFT JOIN meeting_royalwiss_deal.project p ON p.id = rp.project_id AND p.status <> 2
	LEFT JOIN meeting_royalwiss_deal.project_type pt ON pt.id = p.type AND pt.status <> 2
	LEFT JOIN meeting_royalwiss_deal.receivables_pay_method pam ON pam.id = rd.pay_method AND pam.status <> 2
	LEFT JOIN meeting_royalwiss_deal.receivables_pos_machine pom ON pom.id = rd.pos_machine AND pom.status <> 2
	ORDER BY ro.id DESC, rp.id 
) tab
$where
$order
			";
			$result = $this->query($sql);

			return $result;
		}
	}