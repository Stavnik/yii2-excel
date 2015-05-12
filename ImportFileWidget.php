<?php
/**
 * Created by PhpStorm.
 * User: yaroslav
 * Date: 08.05.2015
 * Time: 12:35
 */

namespace yarisrespect\excel;

use yii\base\Widget;

class ImportFileWidget extends Widget {

    public $model;
    public $form;
    public $options = [];

    public function run(){
        return $this->form
            ->field($this->model, ImportBehavior::XLS_FILE )
            ->fileInput( $this->options );
    }
}