<?php
/**
 * Created by PhpStorm.
 * User: yaroslav
 * Date: 08.05.2015
 * Time: 12:35
 */

namespace yarisrespect\excel;

use yii\base\Widget;
use yii\helpers\Html;

class ImportLogWidget extends Widget {

    public $model;
    public $options = [
        'class' => 'import-log'
    ];

    public function run(){
        if( $log = $this->model->getImportLog() ){

            $content = [];
            foreach($log as $msg) $content[] = Html::tag('li', $msg );
            return Html::tag('ul', implode('', $content), $this->options );
        }
        return null;
    }
}