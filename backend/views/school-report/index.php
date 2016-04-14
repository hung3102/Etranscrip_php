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
    
    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create School Report', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            
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
