<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-20
	 * Time: 15:50
	 */
	namespace Manager\Logic;

	class ClientLogic extends ManagerLogic{

		public function _initialize(){
			parent::_initialize();
		}

		public function createClientFromExcel($upload_record_id, $map){
			/** @var \Core\Model\UploadModel $upload_model */
			$upload_model = D('Core/Upload');
			$excel_logic  = new ExcelLogic();
			/** @var \Manager\Model\ClientModel $model */
			$model         = D('Client');
			$upload_record = $upload_model->findRecord(1, ['id' => $upload_record_id]);
			$excel_content = $excel_logic->readClientData($upload_record['save_path']);

			return $model->createClientFromExcelData($excel_content['body'], $map);
		}
	}