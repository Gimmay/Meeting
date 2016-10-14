<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-10-6
	 * Time: 11:18
	 */
	namespace Manager\Logic;

	class RecycleLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function FindEmployee ($list){
			/** @var \Core\Model\EmployeeModel $employee_model */
			$employee_model = D('Core/Employee');
			foreach ($list as $k=>$v){
				$create = $employee_model->findEmployee(1,['id'=>$list[$k]['creator']]);

				$list[$k]['creator'] = $create['name'];
			
			}
			return $list;
		}
	}