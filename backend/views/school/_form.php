<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\District;
use common\models\Province;
use common\models\Commune;
use common\models\Address;

?>
<?php
    $provinces = ArrayHelper::map(Province::find('')->orderBy('name')->all(), 'id', 'name');
    $districts = ArrayHelper::map(District::find('')->orderBy('name')->all(), 'id', 'name');
    $communes = ArrayHelper::map(Commune::find('')->orderBy('name')->all(), 'id', 'name');
    $district = new District();
    $address = new  Address();
?>

<div class="school-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($district, 'provinceID')->dropDownList($provinces, 
            [
                'prompt'=>'Select province',
                'id' => 'province',
                'options' => [
                    $model->address != null ? $model->address->district->provinceID : null => ['Selected' => true],
                ],
                'onchange'=>'{
                    $( "#detailAddress" ).val("");
                    if($(this).val() >= 1) {
                        $( "select#district" ).removeAttr("disabled");
                    } else {
                        $( "select#district" ).attr("disabled", "true");
                        $( "select#commune" ).attr("disabled", "true");
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

    <?= $form->field($address, 'districtID')->dropDownList($districts, 
                [
                    'disabled' => $model->address != null ? false : true,
                    'prompt'=>'Select district',
                    'id' => 'district',
                    'options' => [
                        $model->address != null ? $model->address->districtID : null=> ['Selected' => true]
                    ],
                    'onchange'=>'
                    $( "#detailAddress" ).val("");
                    if($(this).val() >= 1) {
                        $( "select#commune" ).removeAttr("disabled");
                    } else {
                        $( "select#commune" ).attr("disabled", "true");
                    };
                    $.post( "'.Yii::$app->urlManager->createUrl(['address/load-communes']).'", 
                        { districtID: $(this).val() } )
                        .done(function( data ) {
                            $( "select#commune" ).html( data );
                        });'
                ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
