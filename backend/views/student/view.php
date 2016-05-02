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
            'imageURL:url',
            'gender',
            'birthday',
            'currentAddressID',
            'nativeAddressID',
            'ethnicID',
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
