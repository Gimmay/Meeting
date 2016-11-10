<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-20
	 * Time: 16:25
	 */
	namespace Manager\Logic;

	class ReportLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function extendData($type, $data, $option){
			switch($type){
				case 'exportExcel:joinReceivables':
					$data = array_merge([
						[
							'cid'                => '客户ID',
							'id'                 => '客户参会ID',
							'mid'                => '会议ID',
							'meeting_name'       => '会议名称',
							'meeting_time'       => '会议时间',
							'name'               => '参会人名称',
							'gender'             => '参会人性别',
							'age'                => '参会人年龄',
							'mobile'             => '参会人手机',
							'price'              => '收款总和',
							'unit'               => '参会人单位',
							'accompany'          => '陪同',
							'accompany_mobile'   => '陪同手机',
							'team'               => '团队',
							'service_consultant' => '服务顾问',
							'develop_consultant' => '开拓顾问',
							'type'               => '类型',
							'registration_type'  => '报名类型',
							'status'             => '状态',
							'comment'            => '备注',
						]
					], $data);
					if(isset($option['exceptColumn'])){
						foreach($data as $i => $record){
							foreach($record as $key => $val) if(in_array($key, $option['exceptColumn'])) unset($data[$i][$key]);
						}
					}

					return $data;
				break;
				default:
					return $data;
				break;
			}
		}
	}