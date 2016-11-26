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