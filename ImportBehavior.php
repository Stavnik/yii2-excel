<?php
/**
 * Created by PhpStorm.
 * User: yaroslav
 * Date: 08.05.2015
 * Time: 16:14
 */

namespace yarisrespect\excel;

use Yii;
use yii\base\Behavior;
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
        } else if(isset($_FILES['Dvk'])) {
            $this->owner->addError($fileAttribute, Yii::t('app', 'Choose file for import!') );
        }

        return false;
    }

    private function import_row( $data = [] ){
        if( is_string( $this->onImportRow ) && method_exists($this->owner, $this->onImportRow) ){
            return call_user_func_array ([ $this->owner, $this->onImportRow ], $data);
        } else if( $this->onImportRow instanceof \Closure ){
            return call_user_func_array ($this->onImportRow, $data);
        }
        return false;
    }

    private $_current_row = 0;

    public function importExcel($function=null){

        if( $this->upload_file() ){

            if( $function ){
                $this->onImportRow = $function;
            }

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

                $this->_current_row = $i;
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
        $this->_log_data_provider[] = $this->_current_row.': '.$msg;
    }
}