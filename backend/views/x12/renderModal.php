<?php 
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<div class="renderModal">
<?php
$form = ActiveForm::begin([
	'method' => 'post',
	'action' => ['x12/send-data'],
]);
echo $form->field($x12Model, 'fileName')->textInput(['maxlength' => true]);
echo $form->field($x12Model, 'serverUrl')->textInput(['maxlength' => true]);
echo $form->field($x12Model, 'schoolReportNumbers')->hiddenInput(['value'=>serialize($x12Model->schoolReportNumbers)])->label(false);
echo Html::submitButton('Send', ['class' => 'btn btn-primary']);
ActiveForm::end();
?>
</div>