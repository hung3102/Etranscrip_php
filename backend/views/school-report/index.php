<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\widgets\Alert;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SchoolReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'School Reports';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="school-report-index">
    <?php if(Yii::$app->session->hasFlash('error')) {
        echo Alert::widget();
    } else if(Yii::$app->session->hasFlash('success')) {
        echo Alert::widget();
    } ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create School Report', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?=Html::beginForm(['x12/create'],'post');?>
    <?= 'With selected:'//Html::dropdownList('action','',[''=>'With selected', 'c'=>'Create x12 file'],['class'=>'dropdown',])?>
    <?=Html::submitButton('Create x12 file', ['class' => 'btn btn-info',]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            'id',
            'number',
            'studentID',
            'date',
            // 'created_time',
            // 'updated_time',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?= Html::endForm();?> 
</div>
