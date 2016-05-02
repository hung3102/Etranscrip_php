<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SchoolReport */

$this->title = 'Create School Report';
$this->params['breadcrumbs'][] = ['label' => 'School Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="school-report-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'student' => $student,
        'addresses' => $addresses,
        'yearEvaluation' => $yearEvaluation,
    ]) ?>

</div>
