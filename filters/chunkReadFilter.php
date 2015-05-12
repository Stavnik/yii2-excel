<?php
/**
 * Created by PhpStorm.
 * User: yaroslav
 * Date: 08.05.2015
 * Time: 17:28
 */

namespace yarisrespect\excel\filters;


use PHPExcel_Reader_IReadFilter;

class chunkReadFilter implements PHPExcel_Reader_IReadFilter  {

    private $_startRow = 0;
    private $_endRow   = 0;


    public function setRows($startRow, $chunkSize) {
        $this->_startRow = $startRow;
        $this->_endRow   = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '') {
        if ( $row >= $this->_startRow && $row < $this->_endRow ) {
            return true;
        }
        return false;
    }

}