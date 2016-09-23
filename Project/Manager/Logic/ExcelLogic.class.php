<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-14
	 * Time: 16:09
	 */
	namespace Manager\Logic;

	use Core\Logic\UploadLogic;

	class ExcelLogic extends ManagerLogic{
		public function _initialize(){
			parent::_initialize();
		}

		public function exportCustomData($list, $options = []){
			$defaults = [
				'fileName'     => 'excel',
				'creator'      => 'Quasar',
				'lastModifier' => 'Quasar',
				'title'        => '无标题',
				'subject'      => '导出Excel数据',
				'description'  => '暂无描述',
				'keyword'      => '自动导出, PHPExcel, Quasar',
				'category'     => '自动导出',
				'company'      => '',
				'modifiedTime' => time(),
				'createdTime'  => time(),
				'manager'      => 'Quasar',
				'hasHead'      => false,
			];
			$opts     = array_merge($defaults, $options);
			vendor('PHPExcel.PHPExcel');
			$excel_obj       = new \PHPExcel();
			$excel_writer_07 = new \PHPExcel_Writer_Excel2007($excel_obj);
			$excel_obj->getProperties()->setCreator($opts['creator'])->setLastModifiedBy($opts['lastModifier'])->setTitle($opts['title'])->setSubject($opts['subject'])->setDescription($opts['description'])->setKeywords($opts['keyword'])->setCategory($opts['category'])->setCompany($opts['company'])->setModified($opts['modifiedTime'])->setCreated($opts['createdTime'])->setManager($opts['manager']);
			$excel_obj->setActiveSheetIndex(0)->setTitle('表格1');
			for($i = 1; $i<=count($list); $i++){
				$column = 'A';
				if($i == 1 && $opts['hasHead']) $excel_obj->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
				foreach($list[$i-1] as $key => $val){
					$excel_obj->getActiveSheet()->setCellValue("$column$i", $val);
					if($i == 1 && $opts['hasHead']){
						$excel_obj->getActiveSheet()->getStyle("$column$i")->getFont()->setBold(true);
						$excel_obj->getActiveSheet()->getStyle("$column$i")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$excel_obj->getActiveSheet()->getColumnDimension($column)->setWidth(15);
					}
					$column = chr(ord($column)+1);
				}
			}
			header('Content-Type: "application/vnd.ms-excel"');
			header("Content-Disposition: attachment;filename='$opts[fileName].xlsx'");
			header('Cache-Control: max-age=0');
			$excel_writer_07->save('php://output');
			exit;
		}

		public function importClientData($files){
			$getSavePath = function ($data){
				return UPLOAD_PATH.$data['data']['excel']['savepath'].$data['data']['excel']['savename'];
			};
			$getResult   = function ($data){
				return $data['data']['excel'];
			};
			vendor('PHPExcel.PHPExcel');
			$core_upload_logic = new UploadLogic();
			$upload_logic      = new \Manager\Logic\UploadLogic();
			$result1           = $core_upload_logic->upload($files, '/Excel/');
			$result2           = $upload_logic->create($getSavePath($result1), $getResult($result1));
			if($result2['status']){
				$header       = [];
				$content      = [];
				$excel_reader = \PHPExcel_IOFactory::createReaderForFile($getSavePath($result1));
				/** @var \PHPExcel $excel_object */
				$excel_object = $excel_reader->load($getSavePath($result1));
				$excel_object->setActiveSheetIndex(0);
				$line_number = 0;
				foreach($excel_object->getActiveSheet()->getRowIterator() as $val){
					$cellIterator = $val->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);
					foreach($cellIterator as $cell){
						if($line_number == 0) $header[] = $cell->getFormattedValue();
						else{
							if(isset($content[$line_number-1])) array_push($content[$line_number-1], $cell->getFormattedValue());
							else{
								$content[$line_number-1] = [];
								array_push($content[$line_number-1], $cell->getFormattedValue());
							}
						}
					}
					$line_number++;
				}

				return [
					'status'  => true,
					'message' => '读取文件成功',
					'data'    => [
						'head'    => $header,
						'body'    => $content,
						'dbIndex' => $result2['id']
					]
				];
			}
			else return $result2;
		}

		public function readClientData($path){
			vendor('PHPExcel.PHPExcel');
			$header       = [];
			$content      = [];
			$excel_reader = \PHPExcel_IOFactory::createReaderForFile($path);
			/** @var \PHPExcel $excel_object */
			$excel_object = $excel_reader->load($path);
			$excel_object->setActiveSheetIndex(0);
			$line_number = 0;
			foreach($excel_object->getActiveSheet()->getRowIterator() as $val){
				$cellIterator = $val->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false);
				foreach($cellIterator as $cell){
					if($line_number == 0) $header[] = $cell->getFormattedValue();
					else{
						if(isset($content[$line_number-1])) array_push($content[$line_number-1], $cell->getFormattedValue());
						else{
							$content[$line_number-1] = [];
							array_push($content[$line_number-1], $cell->getFormattedValue());
						}
					}
				}
				$line_number++;
			}

			return [
				'head' => $header,
				'body' => $content
			];
		}
	}