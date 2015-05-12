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

            foreach($log as $i => $msg){
                $content[] = Html::tag('li', ++$i.') '.$msg );
            }

            return Html::tag('ul', implode('\n', $content), $this->options );
        }
        return null;
    }
}