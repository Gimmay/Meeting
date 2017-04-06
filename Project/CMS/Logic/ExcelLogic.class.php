<?php
	/**
	 * Created by PhpStorm.
	 * User: 0967
	 * Date: 2016-9-14
	 * Time: 16:09
	 */
	namespace CMS\Logic;

	use PHPExcel;
	use PHPExcel_Exception;
	use PHPExcel_IOFactory;
	use PHPExcel_Reader_Exception;
	use PHPExcel_Writer_Excel2007;

	class ExcelLogic extends CMSLogic{
		public function _initialize(){
			parent::_initialize();
		}

		/**
		 * 获取Excel下一列的表头值
		 *
		 * @param string $column 列名
		 * @param int    $index  索引值
		 *
		 * @return string
		 */
		private function _getNextColumn($column, $index = 0){
			$column = strrev($column);
			if($column[$index] == 'Z'){
				$column[$index] = 'A';

				return $this->_getNextColumn($column, $index+1);
			}
			else{
				if($column[$index]){
					$column[$index] = chr(ord($column[$index])+1);

					return strrev($column);
				}
				else return strrev('A'.$column);
			}
		}

		/**
		 * 导入自定义数据
		 *
		 * @param array $list    数据列表
		 * @param array $options 配置
		 *
		 * @return bool
		 */
		public function writeCustomData($list, $options = []){
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
				'download'     => true,
				'savePath'     => ''
			];
			$opts     = array_merge($defaults, $options);
			vendor('PHPExcel.PHPExcel');
			try{
				$excel_obj       = new PHPExcel();
				$excel_writer_07 = new PHPExcel_Writer_Excel2007($excel_obj);
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
						$column = $this->_getNextColumn($column);
					}
				}
				if($opts['download']){
					header('Content-Type: "application/vnd.ms-excel"');
					header("Content-Disposition: attachment;filename=$opts[fileName].xlsx");
					header('Cache-Control: max-age=0');
					$excel_writer_07->save('php://output');

					return ['status' => true, 'message' => '保存成功'];
				}
				else{
					$excel_writer_07->save("$opts[savePath]$opts[fileName].xlsx");

					return ['status' => true, 'message' => '保存成功', 'filePath' => trim("$opts[savePath]$opts[fileName].xlsx", '.')];
				}
			}catch(PHPExcel_Reader_Exception $error){
				return ['status' => false, 'message' => $error];
			}catch(PHPExcel_Exception $error){
				return ['status' => false, 'message' => $error];
			}
		}

		/**
		 * 获取客户导入文件的数据
		 *
		 * @param string $file_path 文件路径
		 * @param bool   $has_head  是否包含列表头
		 *
		 * @return array
		 * @throws PHPExcel_Exception
		 * @throws PHPExcel_Reader_Exception
		 */
		public function readCustomData($file_path, $has_head = true){
			vendor('PHPExcel.PHPExcel');
			$header  = [];
			$content = [];
			try{
				$excel_reader = PHPExcel_IOFactory::createReaderForFile($file_path);
				/** @var PHPExcel $excel_object */
				$excel_object = $excel_reader->load($file_path);
				$excel_object->setActiveSheetIndex(0);
				$line_number = 0;
				foreach($excel_object->getActiveSheet()->getRowIterator() as $val){
					$cellIterator = $val->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);
					foreach($cellIterator as $cell){
						if($has_head){
							if($line_number == 0) $header[] = $cell->getFormattedValue();
							else{
								if(!isset($content[$line_number-1])) $content[$line_number-1] = [];
								array_push($content[$line_number-1], $cell->getFormattedValue());
							}
						}
						else $content[] = $cell->getFormattedValue();
					}
					$line_number++;
				}
			}catch(PHPExcel_Reader_Exception $error){
				return [
					'status'  => false,
					'message' => '读取Excel文件失败',
				];
			}catch(PHPExcel_Exception $error){
				return [
					'status'  => false,
					'message' => '读取Excel文件异常错误',
				];
			}

			return [
				'status'  => true,
				'message' => '读取文件成功',
				'data'    => $has_head ? [
					'head' => $header,
					'body' => $content
				] : $content
			];
		}

		/**
		 * 处理POST/GET请求
		 *
		 * @param string $type 请求类型
		 * @param array  $opt  相关参数
		 *
		 * @return mixed
		 */
		public function handlerRequest($type, $opt = []){
		}

		/**
		 * 设定额外数据
		 *
		 * @param string $type 操作类型
		 * @param mixed  $data 处理数据
		 *
		 * @return mixed
		 */
		public function setData($type, $data){
		}
	}