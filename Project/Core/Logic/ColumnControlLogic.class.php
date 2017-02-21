<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-12-27
	 * Time: 10:15
	 */
	namespace Core\Logic;

	class ColumnControlLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function initClientColumn($mid){
			/** @var \Core\Model\ColumnControlModel $model */
			$model  = D('Core/ColumnControl');
			$result = $model->createMultiRecord([
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-NAME',
					'form'     => 'name',
					'name'     => '姓名',
					'view'     => 1,
					'must'     => 1,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-GENDER',
					'form'     => 'gender',
					'name'     => '性别',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-MOBILE',
					'form'     => 'mobile',
					'name'     => '手机号',
					'view'     => 1,
					'must'     => 1,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-UNIT',
					'form'     => 'unit',
					'name'     => '单位名称',
					'view'     => 1,
					'must'     => 1,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-BIRTHDAY',
					'form'     => 'birthday',
					'name'     => '生日',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-EMAIL',
					'form'     => 'email',
					'name'     => '电子邮箱',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-TITLE',
					'form'     => 'title',
					'name'     => '职称',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-POSITION',
					'form'     => 'position',
					'name'     => '职位',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-ADDRESS',
					'form'     => 'address',
					'name'     => '地址',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-ID_CARD_NUMBER',
					'form'     => 'id_card_number',
					'name'     => '身份证号',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-SERVICE_CONSULTANT',
					'form'     => 'service_consultant',
					'name'     => '服务顾问',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-DEVELOP_CONSULTANT',
					'form'     => 'develop_consultant',
					'name'     => '开拓顾问',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-IS_NEW',
					'form'     => 'is_new',
					'name'     => '是否新客',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-TEAM',
					'form'     => 'team',
					'name'     => '团队',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-TYPE',
					'form'     => 'type',
					'name'     => '类型',
					'view'     => 1,
					'must'     => 1,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-GROUP_NAME',
					'form'     => 'group_name',
					'name'     => '组别',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'workflow_join'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-COMMENT',
					'form'     => 'comment',
					'name'     => '备注',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'user_client'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-COLUMN1',
					'form'     => 'column1',
					'name'     => '备注字段1',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'workflow_join'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-COLUMN2',
					'form'     => 'column2',
					'name'     => '备注字段2',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'workflow_join'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-COLUMN3',
					'form'     => 'column3',
					'name'     => '备注字段3',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'workflow_join'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-COLUMN4',
					'form'     => 'column4',
					'name'     => '备注字段4',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'workflow_join'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-COLUMN5',
					'form'     => 'column5',
					'name'     => '备注字段5',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'workflow_join'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-COLUMN6',
					'form'     => 'column6',
					'name'     => '备注字段6',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'workflow_join'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-COLUMN7',
					'form'     => 'column7',
					'name'     => '备注字段7',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'workflow_join'
				],
				[
					'mid'      => $mid,
					'code'     => 'CLIENT-COLUMN8',
					'form'     => 'column8',
					'name'     => '备注字段8',
					'view'     => 1,
					'must'     => 0,
					'creatime' => time(),
					'table'    => 'workflow_join'
				]
			]);

			return $result;
		}

		public function initMeetingColumn(){
			/** @var \Core\Model\ColumnControlModel $model */
			$model = D('Core/ColumnControl');
		}

		public function saveClientColumn($mid, $data){
			/** @var \Core\Model\ColumnControlModel $model */
			$model   = D('Core/ColumnControl');
			$result1 = $model->deleteRecord(['mid' => $mid]);
			if(!$result1['status']) return ['status' => false, 'message' => '更新失败'];

			$result2 = $model->createMultiRecord($data);
			if(!$result2['status']) return ['status' => false, 'message' => '保存失败'];
			else return ['status' => true, 'message' => '更新成功'];
		}

		public function saveMeetingColumn(){
			/** @var \Core\Model\ColumnControlModel $model */
			$model = D('Core/ColumnControl');
		}
	}