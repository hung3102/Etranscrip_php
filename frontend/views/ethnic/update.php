<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Ethnic */

$this->title = 'Update Ethnic: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Ethnics', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="ethnic-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
