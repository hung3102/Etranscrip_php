<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\District;
use common\models\Province;
use common\models\Commune;
?>
<?php
    $provinces = ArrayHelper::map(Province::find('')->orderBy('name')->all(), 'id', 'name');
    $districts = ArrayHelper::map(District::find('')->orderBy('name')->all(), 'id', 'name');
    $communes = ArrayHelper::map(Commune::find('')->orderBy('name')->all(), 'id', 'name');
    $district = new District();
?>
<div class="address-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($district, 'provinceID')->dropDownList($provinces, 
            [
                'prompt'=>'Select province',
                'id' => 'province',
                'options' => [
                    $model->district != null ? $model->district->provinceID : null => ['Selected' => true],
                ],
                'onchange'=>'{
                    $( "#detailAddress" ).val("");
                    if($(this).val() >= 1) {
                        $( "select#district" ).removeAttr("disabled");
                    } else {
                        $( "select#district" ).attr("disabled", "true");
                        $( "select#commune" ).attr("disabled", "true");
                        $( "#detailAddress" ).attr("disabled", "true");
                    };
                    $.post( "'.Yii::$app->urlManager->createUrl(['address/load-districts']).'", 
                    { provinceID: $(this).val() } )
                    .done(function( data ) {
                        var data = JSON.parse(data);
                        $( "select#district" ).html( data );
                    });
                }'
            ]);
    ?>
    <?= $form->field($model, 'districtID')->dropDownList($districts, 
                [
                    'disabled' => $model->district != null ? false : true,
                    'prompt'=>'Select district',
                    'id' => 'district',
                    'options' => [
                        $model->district != null ? $model->districtID : null=> ['Selected' => true]
                    ],
                    'onchange'=>'
                    $( "#detailAddress" ).val("");
                    if($(this).val() >= 1) {
                        $( "select#commune" ).removeAttr("disabled");
                        $( "#detailAddress" ).removeAttr("disabled");
                    } else {
                        $( "select#commune" ).attr("disabled", "true");
                        $( "#detailAddress" ).attr("disabled", "true");
                    };
                    $.post( "'.Yii::$app->urlManager->createUrl(['address/load-communes']).'", 
                        { districtID: $(this).val() } )
                        .done(function( data ) {
                            $( "select#commune" ).html( data );
                        });'
                ]);
    ?>
    <?= $form->field($model, 'communeID')->dropDownList($communes,
            [
                'prompt' => 'Select commune',
                'id' => 'commune',
                'disabled' => $model->commune != null ? false : true,
                'options' => [$model->commune != null ? $model->communeID : null => ['Selected' => true]]
            ]);
    ?>

    <?= $form->field($model, 'detailAddress')->textInput(['maxlength' => true, 'id' => 'detailAddress']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
