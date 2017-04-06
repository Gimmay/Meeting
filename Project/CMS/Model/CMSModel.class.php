<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-6
	 * Time: 11:50
	 */
	namespace CMS\Model;

	use General\Model\GeneralModel;

	class CMSModel extends GeneralModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $autoCheckFields = false;
		/** 模型类查询检索的控制字段 */
		const CONTROL_COLUMN_PARAMETER = [
			'keyword' => '_keyword', // 关键字检索参数
			'order'   => '_order', // 排序参数
			'status'  => 'status', // 状态参数
		];
	}