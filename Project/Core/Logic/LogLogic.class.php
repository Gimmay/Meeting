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

		/**
		 * 记录系统日志
		 *
		 * @param array $data    记录数据<br>
		 *                       [<br>
		 *                       'dbSchema'=>string,<br>
		 *                       'dbTable'=>string,<br>
		 *                       'dbColumn'=>string,<br>
		 *                       'extend'=>'string',<br>
		 *                       'action'=>'string',<br>
		 *                       'type'=>string<br>
		 *                       ]
		 * @param int   $type
		 *
		 * @return array
		 */
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
			C('TOKEN_ON', false);

			return $log_model->createLog($data);
		}
	}