<?php
/**
 * Created by PhpStorm.
 * User: yaroslav
 * Date: 08.05.2015
 * Time: 16:14
 */

namespace yarisrespect\excel;

use yii\base\Behavior;
use yii\data\ArrayDataProvider;
use yii\validators\FileValidator;
use yii\web\UploadedFile;

class ImportBehavior extends Behavior {


    const XLS_FILE = 'excel_file';

    public $defaultFormat = 'Excel5';
    public $excel_file;
    public $onImportRow = 'onImportRow';

    private $_uploaded_file;
    private $_log_data_provider = [];

    private function upload_file($fileAttribute = self::XLS_FILE, array $extensions = null, $maxSize = 10485760 ){
        $this->_uploaded_file = (new UploadedFile())->getInstance($this->owner, $fileAttribute );
        if($this->_uploaded_file ){
            $fileValidator = new FileValidator([
                'extensions' => is_array($extensions) ? $extensions : ['xls','xlsx'],
                'maxSize' => $maxSize
            ]);

            if( $fileValidator->validate( $this->_uploaded_file, $error ) ){

                return true;
            } else $this->owner->addError($fileAttribute, $error);
        }

        return false;
    }

    private function import_row( $data = [] ){
        if( is_string( $this->onImportRow ) && method_exists($this->owner, $this->onImportRow) ){
            return call_user_func([ $this->owner, $this->onImportRow ], $data);
        } else if( $this->onImportRow instanceof \Closure ){
            return call_user_func($this->onImportRow, $data);
        }
        return false;
    }

    public function importExcel(){

        if( $this->upload_file() ){

            //$reader = \PHPExcel_IOFactory::createReader( /*$this->defaultFormat*/ );
            $objPHPExcel = \PHPExcel_IOFactory::load( $this->_uploaded_file->tempName );

            //$objPHPExcel->setActiveSheetIndex(0);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();

            foreach ($objWorksheet->getRowIterator() as $i => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $row = [];
                foreach ($cellIterator as $cell) $row[] = $cell->getValue();

                if( $this->import_row([
                    'row' => $row,
                    'index' => $i,
                    'max_row' => $highestRow
                ]) ) {

                } else {
                    break;
                }

            }

        }
    }

    public function getImportLog(){
        return $this->_log_data_provider;
    }

    public function addLog($msg) {
        $this->_log_data_provider[] = $msg;
    }
}