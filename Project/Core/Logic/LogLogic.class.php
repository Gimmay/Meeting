<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-24
	 * Time: 10:51
	 */
	namespace Core\Logic;

	class LogLogic extends CoreLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function create($data, $type = 0){
			$setDatabase = function ($data){
				$result = '';
				$result = isset($data['dbSchema']) ? "$result$data[dbSchema]." : "$result".C('DB_NAME').'.';
				if(isset($data['dbTable'])) $result = "$result$data[dbTable].";
				if(isset($data['dbColumn'])) $result = "$result$data[dbColumn]";

				return $result;
			};
			$setPosition = function ($data){
				$result = MODULE_NAME.':'.CONTROLLER_NAME.'/'.ACTION_NAME;
				if(isset($data['extend'])) $result = "$result ($data[extend])";

				return $result;
			};
			/** @var \Core\Model\LogModel $log_model */
			$log_model            = D('Core/Log');
			$data['database']     = $setDatabase($data);
			$data['position']     = $setPosition($data);
			$data['creatime']     = time();
			$data['creator_type'] = $type;
			return $log_model->createLog($data);
		}
	}