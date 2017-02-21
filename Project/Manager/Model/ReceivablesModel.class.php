<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-17
	 * Time: 11:54
	 */
	namespace Manager\Model;

	class ReceivablesModel extends ManagerModel{
		protected $tableName       = 'receivables';
		protected $tablePrefix     = 'workflow_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function getReceivablesTypeSelectList(){
			return $this->field('distinct type value, type html, type keyword')->select();
		}

		public function getReceivablesDetail($mid, $head = true){
			$sql    = "SELECT *
FROM(
		SELECT
			user_client.unit `unit`,
			user_client.team,
			user_client. NAME `client`,
			workflow_receivables_option.price `price`,
			concat(
				workflow_coupon.NAME,
				ifnull(concat('(' ,workflow_coupon_item.code, ')'), '')
			) `project`,
			user_employee. NAME `payee`,
			workflow_receivables.type,
			workflow_receivables.order_number,
			workflow_pay_method. NAME `pay_method`,
			(SELECT NAME FROM workflow_pos_machine WHERE workflow_receivables_option.pos_machine = workflow_pos_machine.id) `pos_machine`,
			workflow_receivables_option.`comment`,
			FROM_UNIXTIME(workflow_receivables.time) `time`,
			workflow_coupon_item.code
		FROM user_client
		JOIN workflow_receivables ON workflow_receivables.cid = user_client.id
		JOIN workflow_receivables_option ON workflow_receivables_option.rid = workflow_receivables.id
		JOIN workflow_pay_method ON workflow_pay_method.id = workflow_receivables_option.pay_method
		JOIN user_employee ON user_employee.id = workflow_receivables.payee_id
		JOIN workflow_coupon_item ON workflow_coupon_item.id IN ( workflow_receivables.coupon_ids )
		JOIN workflow_coupon ON workflow_coupon.id = workflow_coupon_item.coupon_id
		WHERE workflow_receivables.mid = $mid
		and workflow_receivables.status = 1 and workflow_receivables_option.status = 1
	) tab";
			$result = $this->query($sql);
			$head ? $result = array_merge([
				[
					'unit'         => '单位(会所)',
					'team'         => '团队',
					'client'       => '顾客',
					'price'        => '金额',
					'project'      => '项目',
					'payee'        => '收款人',
					'type'        => '收款类型',
					'order_number' => '单据号',
					'pay_method'   => '支付方式',
					'pos_machine'  => 'POS机',
					'comment'      => '备注',
					'time'         => '收款时间'
				]
			], $result) : null;

			return $result;
		}

		public function getColumn($just_desc = false, $demo = false){
			$result  = $this->query("
SELECT 'cid' `NAME`, '客户姓名' `DESC`, 'varchar(50)' `TYPE`
UNION SELECT 'payee_id' `NAME`, '收款人' `DESC`, 'varchar(50)' `TYPE`
UNION (SELECT
	COLUMN_NAME `NAME`,
	COLUMN_COMMENT `DESC`,
	COLUMN_TYPE `TYPE`
FROM information_schema.`COLUMNS`
WHERE TABLE_SCHEMA = 'gimmay_meeting' AND TABLE_NAME = 'workflow_receivables'
AND COLUMN_NAME NOT IN ('id', 'status', 'creatime', 'creator', 'cid', 'mid', 'method', 'type', 'pos_id', 'coupon_ids', 'order_number', 'payee_id'))
UNION SELECT 'method' `NAME`, '支付方式' `DESC`, 'varchar(50)' `TYPE`
UNION SELECT 'type' `NAME`, '收款类型' `DESC`, 'varchar(50)' `TYPE`
UNION SELECT 'pos_id' `NAME`, 'POS机' `DESC`, 'varchar(50)' `TYPE`
UNION SELECT 'coupon_ids' `NAME`, '代金券码' `DESC`, 'varchar(255)' `TYPE`
");
			$list[0] = [];
			$handler = false;
			if($just_desc){
				$handler = true;
				foreach($result as $val) array_push($list[0], $val['desc']);
			}
			if($demo){
				$handler = true;
				$list[]  = [
					'张三',
					'小王',
					'19800',
					'2016-2-22 2:22',
					'深圳福田机构',
					'会后收款',
					'',
					'刷卡',
					'参会费',
					'SRS-X11',
					'szq-1118'
				];
			}
			if(!$handler) $list = $result;

			return $list;
		}
	}