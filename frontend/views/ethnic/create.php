<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Ethnic */

$this->title = 'Create Ethnic';
$this->params['breadcrumbs'][] = ['label' => 'Ethnics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ethnic-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
