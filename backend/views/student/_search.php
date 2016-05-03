<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\Student */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'imageURL') ?>

    <?= $form->field($model, 'gender') ?>

    <?= $form->field($model, 'birthday') ?>

    <?php // echo $form->field($model, 'currentAddressID') ?>

    <?php // echo $form->field($model, 'nativeAddressID') ?>

    <?php // echo $form->field($model, 'ethnicID') ?>

    <?php // echo $form->field($model, 'fatherName') ?>

    <?php // echo $form->field($model, 'fatherJob') ?>

    <?php // echo $form->field($model, 'motherName') ?>

    <?php // echo $form->field($model, 'motherJob') ?>

    <?php // echo $form->field($model, 'tutorName') ?>

    <?php // echo $form->field($model, 'tutorJob') ?>

    <?php // echo $form->field($model, 'created_time') ?>

    <?php // echo $form->field($model, 'updated_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
