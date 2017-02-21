<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-2-7
	 * Time: 19:33
	 */
	namespace Core\Model;

	class ReportModel extends CoreModel{
		protected $autoCheckFields = false;
		public function _initialize(){
			parent::_initialize();
		}

		public function report2($mid){
				$sql = "select (
	SELECT count(*)
	FROM user_client
	JOIN workflow_join ON workflow_join.cid=user_client.id
	WHERE workflow_join.mid=$mid
		  AND workflow_join.column4 LIKE '%深圳%'
	      AND user_client.type <> '会所'
	      and workflow_join.status = 1
) shen_zhen_area_count, -- 深圳区域人数
(
	SELECT count(*)
	FROM user_client
	JOIN workflow_join ON workflow_join.cid=user_client.id
	WHERE workflow_join.mid=$mid
		  AND workflow_join.column4 LIKE '%深圳%'
	      AND user_client.type <> '会所'
	       and workflow_join.status = 1
	AND workflow_join.sign_status = 1
) shen_zhen_area_sign_count, -- 深圳到场区域人数
	(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column4 LIKE '%深圳%'
				  AND user_client.type <> '会所'
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) shen_zhen_area_unit, -- 深圳区域店家
	(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column4 LIKE '%深圳%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) shen_zhen_area_sign_unit, -- 深圳区域签到店家
	(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column4 LIKE '%深圳%'
				  AND workflow_join.column5 LIKE '%新店%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) shen_zhen_area_sign_new_unit, -- 深圳区域签到新店
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%深圳%'
			  AND workflow_join.column5 LIKE '%新店%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) shen_zhen_area_sign_new, -- 深圳区域签到新店人数
	(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column4 LIKE '%深圳%'
				  AND workflow_join.column5 LIKE '%老店%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) shen_zhen_area_sign_old_unit, -- 深圳区域签到老店
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%深圳%'
			  AND workflow_join.column5 LIKE '%老店%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) shen_zhen_area_sign_old, -- 深圳区域签到老店人数
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%广州%'
			  AND user_client.type <> '会所'
			   and workflow_join.status = 1
	) guang_zhou_area_count, -- 广州区域人数
 (
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%广州%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) guang_zhou_area_sign_count, -- 广州区域人数
	(
		select count(*) from (
								 SELECT unit
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND workflow_join.column4 LIKE '%广州%'
									   AND user_client.type <> '会所'
									    and workflow_join.status = 1
								 GROUP BY user_client.unit
							 ) unit_table
	) guang_zhou_area_unit, -- 广州区域店家
	(
		select count(*) from (
								 SELECT unit
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND workflow_join.column4 LIKE '%广州%'
									   AND user_client.type <> '会所'
									   AND workflow_join.sign_status = 1
									    and workflow_join.status = 1
								 GROUP BY user_client.unit
							 ) unit_table
	) guang_zhou_area_sign_unit, -- 广州区域签到店家
		(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column4 LIKE '%广州%'
				  AND workflow_join.column5 LIKE '%新店%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) guang_zhou_area_sign_new_unit, -- 广州区域签到新店
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%广州%'
			  AND workflow_join.column5 LIKE '%新店%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) guang_zhou_area_sign_new, -- 广州区域签到新店人数
	(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column4 LIKE '%广州%'
				  AND workflow_join.column5 LIKE '%老店%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) guang_zhou_area_sign_old_unit, -- 广州区域签到老店
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%广州%'
			  AND workflow_join.column5 LIKE '%老店%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) guang_zhou_area_sign_old, -- 广州区域签到老店人数

	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%上海%'
			  AND user_client.type <> '会所'
			   and workflow_join.status = 1
	) shang_hai_area_count, -- 上海区域人数
 (
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%上海%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) shang_hai_area_sign_count, -- 上海区域人数
	(
		select count(*) from (
								 SELECT unit
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND workflow_join.column4 LIKE '%上海%'
									   AND user_client.type <> '会所'
									    and workflow_join.status = 1
								 GROUP BY user_client.unit

							 ) unit_table
	) shang_hai_area_unit, -- 上海区域店家
	(
		select count(*) from (
								 SELECT unit
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND workflow_join.column4 LIKE '%上海%'
									   AND user_client.type <> '会所'
									   AND workflow_join.sign_status = 1
									    and workflow_join.status = 1
								 GROUP BY user_client.unit
							 ) unit_table
	) shang_hai_area_sign_unit, -- 上海区域签到店家
			(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column4 LIKE '%上海%'
				  AND workflow_join.column5 LIKE '%新店%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) shang_hai_area_sign_new_unit, -- 上海区域签到新店
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%上海%'
			  AND workflow_join.column5 LIKE '%新店%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) shang_hai_area_sign_new, -- 上海区域签到新店人数
	(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column4 LIKE '%上海%'
				  AND workflow_join.column5 LIKE '%老店%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) shang_hai_area_sign_old_unit, -- 上海区域签到老店
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%上海%'
			  AND workflow_join.column5 LIKE '%老店%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) shang_hai_area_sign_old, -- 上海区域签到老店人数

	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%外省%'
			  AND user_client.type <> '会所'
			   and workflow_join.status = 1
	) wai_sheng_area_count, -- 外省区域人数
 (
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%外省%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) wai_sheng_area_sign_count, -- 外省区域人数
	(
		select count(*) from (
								 SELECT unit
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND workflow_join.column4 LIKE '%外省%'
									   AND user_client.type <> '会所'
									    and workflow_join.status = 1
								 GROUP BY user_client.unit

							 ) unit_table
	) wai_sheng_area_unit, -- 外省区域店家
	(
		select count(*) from (
								 SELECT unit
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND workflow_join.column4 LIKE '%外省%'
									   AND user_client.type <> '会所'
									   AND workflow_join.sign_status = 1
									    and workflow_join.status = 1
								 GROUP BY user_client.unit
							 ) unit_table
	) wai_sheng_area_sign_unit, -- 外省区域签到店家
				(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column4 LIKE '%外省%'
				  AND workflow_join.column5 LIKE '%新店%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) wai_sheng_area_sign_new_unit, -- 外省区域签到新店
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%外省%'
			  AND workflow_join.column5 LIKE '%新店%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) wai_sheng_area_sign_new, -- 外省区域签到新店人数
	(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column4 LIKE '%外省%'
				  AND workflow_join.column5 LIKE '%老店%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) wai_sheng_area_sign_old_unit, -- 外省区域签到老店
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column4 LIKE '%外省%'
			  AND workflow_join.column5 LIKE '%老店%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) wai_sheng_area_sign_old, -- 外省区域签到老店人数

	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND user_client.team LIKE '%青春谷%'
			  AND user_client.type <> '会所'
			   and workflow_join.status = 1
	) qing_chun_gu_area_count, -- 青春谷团队人数
(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND user_client.team LIKE '%青春谷%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) qing_chun_gu_area_sign_count, -- 青春谷团队签到人数
	(
		select count(*) from (
								 SELECT count(*)
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND user_client.team LIKE '%青春谷%'
									   AND user_client.type <> '会所'
									    and workflow_join.status = 1
								 GROUP BY user_client.unit
							 ) unit_table
	) qing_chun_gu_area_unit, -- 青春谷团队店家
	(
		select count(*) from (
								 SELECT count(*)
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND user_client.team LIKE '%青春谷%'
									   AND user_client.type <> '会所'
									   AND workflow_join.sign_status = 1
									    and workflow_join.status = 1
								 GROUP BY user_client.unit
							 ) unit_table
	) qing_chun_gu_area_sign_unit, -- 青春谷团队签到店家
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
			where workflow_join.mid=$mid
				  AND user_client.type <> '会所'
				   and workflow_join.status = 1
	) total_count, -- 参会总数
 (
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
			where workflow_join.mid=$mid
				  AND user_client.type <> '会所'
				AND workflow_join.sign_status = 1
				 and workflow_join.status = 1
	) total_sign_count, -- 签到总数
	(
		select count(*) from (
								 SELECT count(*)
								 FROM user_client
								 JOIN workflow_join ON workflow_join.cid=user_client.id
								 WHERE workflow_join.mid=$mid
									   AND user_client.type <> '会所'
									    and workflow_join.status = 1
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
								 GROUP BY user_client.unit
							 ) unit_table
	) total_unit_sign_count, --  签到会所数
	(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column5 LIKE '%新店%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) total_sign_new_unit, -- 外省区域签到新店
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column5 LIKE '%新店%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) total_sign_new, -- 外省区域签到新店人数
	(
		select count(*) from (
			SELECT count(*)
			FROM user_client
			JOIN workflow_join ON workflow_join.cid=user_client.id
			WHERE workflow_join.mid=$mid
				  AND workflow_join.column5 LIKE '%老店%'
				  AND user_client.type <> '会所'
				  AND workflow_join.sign_status = 1
				   and workflow_join.status = 1
			GROUP BY user_client.unit
		) unit_table
	) total_sign_old_unit, -- 外省区域签到老店
	(
		SELECT count(*)
		FROM user_client
		JOIN workflow_join ON workflow_join.cid=user_client.id
		WHERE workflow_join.mid=$mid
			  AND workflow_join.column5 LIKE '%老店%'
			  AND user_client.type <> '会所'
			  AND workflow_join.sign_status = 1
			   and workflow_join.status = 1
	) total_sign_old -- 外省区域签到老店人数
	";
			$result = M()->query($sql);
			return $result[0];
		}

		
	}