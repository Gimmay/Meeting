<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-28
	 * Time: 19:35
	 */
	namespace Mobile\Model;

	class ClientModel extends MobileModel{
		protected $tableName       = 'client';
		protected $tablePrefix     = 'user_';
		protected $autoCheckFields = true;

		public function _initialize(){
			parent::_initialize();
		}

		public function getClientSelectList($mid){
			$sql = "SELECT CONCAT(`name`) `html`, CONCAT( pinyin_code),
user_client.id `value`
FROM `workflow_join`
LEFT JOIN `user_client`
ON workflow_join.cid = user_client.id
WHERE MID = $mid AND workflow_join.status = 1";

			return $this->query($sql);
		}

		public function getClientListByGroup($mid, $keyword, $have_group = true){
			if($have_group) $sql = "SELECT *
FROM (
	SELECT
		DISTINCT user_client.id cid,
		workflow_group.id gid,
		workflow_group.code group_name,
		user_client.name client_name,
		user_client.mobile,
		user_client.unit,
		workflow_join.mid,
		workflow_join.sign_status
	FROM workflow_group
	JOIN workflow_group_member ON workflow_group_member.gid = workflow_group.id
	JOIN user_client ON user_client.id = workflow_group_member.cid
	JOIN workflow_join ON workflow_join.cid = user_client.id AND workflow_join.mid = $mid
	WHERE workflow_group.mid = $mid and workflow_join.status = 1 and workflow_join.review_status = 1 and workflow_group_member.status = 1
) temp
where unit like '%$keyword%' OR client_name like '%$keyword%' OR group_name like '%$keyword%'";
			else $sql = "
SELECT *
FROM(
		SELECT
			0 gid,
			'*未分组人员*' group_name,
			user_client. NAME client_name,
			user_client.id cid,
			user_client.mobile,
			user_client.unit,
			workflow_join.mid,
			workflow_join.sign_status
		FROM user_client
		JOIN workflow_join ON workflow_join.cid = user_client.id
		WHERE user_client.id NOT IN (
				SELECT user_client.id
				FROM workflow_group
				JOIN workflow_group_member ON workflow_group_member.gid = workflow_group.id
				JOIN user_client ON user_client.id = workflow_group_member.cid
				WHERE workflow_group.mid = $mid and workflow_group_member.status = 1
			) and workflow_join.review_status = 1 and workflow_join.status = 1
	) temp
WHERE mid = $mid AND (unit LIKE '%$keyword%' OR client_name LIKE '%$keyword%' or group_name like '%$keyword%')";

			return $this->query($sql);
		}
	}