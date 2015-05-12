# yii2-import-excel

Import *.xls, *.xlsx by PHPExcel for Yii 2 framework

Model.php:

    public function behaviors() {
        return [
          ImportBehavior::className()
        ]
    }
    public function onImportRow($row, $index, $max_row) {
        $this->addLog( implode(', ', $row). " ($index/$max_row)" );
        return true; // return FALSE to stop import
    }

Controller.php:

    public function actionImport() {
        $model = new Model();
        $model->importExcel();
        return $this->render('import', [ 'model' => $model ]);
    }

View import.php:

    <? $form = ActiveForm::begin([ 'options' => [ 'enctype' => "multipart/form-data", ] ]); ?>
    
        <?= \yarisrespect\excel\ImportFileWidget::widget([
            'model' => $model, 'form' => $form, 'label' => 'File'
        ])?>
        <?= Html::submitButton('Import') ?>
        <?= \yarisrespect\excel\ImportLogWidget::widget([ 'model' => $model, ])?>
        
    <? ActiveForm::end(); ?>
