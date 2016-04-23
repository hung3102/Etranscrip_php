<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SchoolReport */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'School Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="school-report-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('View as pdf', ['view-pdf', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'number',
            'studentID',
            'date',
            'created_time',
            'updated_time',
        ],
    ]) ?>

</div>
