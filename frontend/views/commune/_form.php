<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Province;
use common\models\District;

/* @var $this yii\web\View */
/* @var $model common\models\Commune */
/* @var $form yii\widgets\ActiveForm */
?>
<?php 
	$provinces = ArrayHelper::map(Province::find('')->orderBy('name')->all(), 'id', 'name');
	$district = new District();
	$districts = ArrayHelper::map(District::find('')->orderBy('name')->all(), 'id', 'name');
?>

<div class="commune-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($district, 'provinceID')->dropDownList($provinces, 
    		[
                'prompt'=>'Select province',
                'id' => 'province',
                'options' => [
                    $model->district != null ? $model->district->provinceID : null => ['Selected' => true],
                ],
                'onchange'=>'{
                    if($(this).val() >= 1) {
                        $( "select#district" ).removeAttr("disabled");
                    } else {
                        $( "select#district" ).attr("disabled", "true");
                    };
                    $.post( "'.Yii::$app->urlManager->createUrl(['address/load-districts']).'", 
                    { provinceID: $(this).val() } )
                    .done(function( data ) {
                        var data = JSON.parse(data);
                        $( "select#district" ).html( data );
                    });}'
            ]);  ?>

    <?= $form->field($model, 'districtID')->dropDownList($districts, 
    	[
    		'disabled' => $model->district != null ? false : true,
            'prompt'=>'Select district',
            'id' => 'district',
            'options' => [
                $model->district != null ? $model->districtID : null=> ['Selected' => true]
            ],
    	]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
