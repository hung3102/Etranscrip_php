<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'value' => function($data) {
                   return User::$status[$data->status];
                },
                'filter' => User::$status,
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return date("d/m/Y", $model->created_at);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
            ]
        ],
    ]); ?>
</div>
