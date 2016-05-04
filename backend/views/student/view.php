<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Student */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('View as pdf', ['school-report/view-pdf', 'id' => $model->schoolReport->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['school-report/update', 'id' => $model->schoolReport->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'label' => 'School Report Number',
                'value' => $model->schoolReport->number,
            ],
            [
                'attribute' => 'image',
                'value' => $model->image != null ? Yii::$app->params['imageUrl'].$model->image : null,
                'format' => ['image', ['width' => '100']],
            ],
            [
                'attribute' => 'gender',
                'value' => $model->getGenderText(),
            ],
            [
                'attribute' => 'birthday',
                'value' => date('d/m/Y', strtotime($model->birthday)),
            ],
            [
                'label' => 'Current Address',
                'value' => $model->currentAddress->getFullAddress(),
            ],
            [
                'label' => 'Native Address',
                'value' => $model->nativeAddress->getFullAddress(),
            ],
            [
                'label' => 'Ethnic',
                'value' => $model->ethnic->name,
            ],
            'fatherName',
            'fatherJob',
            'motherName',
            'motherJob',
            'tutorName',
            'tutorJob',
            'created_time',
            'updated_time',
        ],
    ]) ?>

</div>
