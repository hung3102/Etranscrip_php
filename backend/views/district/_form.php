<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Province;

?>
<?php 
	$provinces = ArrayHelper::map(Province::find('')->orderBy('name')->all(), 'id', 'name');
?>

<div class="district-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'provinceID')->dropDownList($provinces, 
    		[
    			'prompt'=>'Select province',
                'id' => 'province',
                'options' => [
                    $model->province != null ? $model->provinceID : null => ['Selected' => true],
                ]
            ])
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
