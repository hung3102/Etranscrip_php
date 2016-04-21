<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'imageURL')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gender')->textInput() ?>

    <?= $form->field($model, 'birthday')->textInput() ?>

    <?= $form->field($model, 'currentAddressID')->textInput() ?>

    <?= $form->field($model, 'nativeAddressID')->textInput() ?>

    <?= $form->field($model, 'ethnicID')->textInput() ?>

    <?= $form->field($model, 'religionID')->textInput() ?>

    <?= $form->field($model, 'fatherName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fatherJob')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'motherName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'motherJob')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tutorName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tutorJob')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_time')->textInput() ?>

    <?= $form->field($model, 'updated_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
