<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2017-3-9
	 * Time: 11:17
	 */
	namespace RoyalwissD\Model;

	use CMS\Model\CMSModel;

	class RoyalwissDModel extends CMSModel{
		public function _initialize(){
			parent::_initialize();
		}

		protected $autoCheckFields = false;
	}