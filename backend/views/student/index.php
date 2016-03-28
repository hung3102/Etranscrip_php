<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Student;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\Student */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Student', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            'name',
            // 'imageURL:url',
            [
                'attribute' => 'gender',
                'filter' => Student::$gender,
                'value' => function($data) {
                    return $data->getGenderText();
                }
            ],
            'birthday',
            // [  
            //     'label' => 'Current Address',
            //     'value' => function($data) {
            //         return $data->currentAddress->getFullAddress();
            //     }
            // ],
            [  
                'label' => 'Native Address',
                'value' => function($data) {
                    return $data->nativeAddress->getFullAddress();
                }
            ],
            // 'ethnicID',
            // 'religionID',
            // 'fatherName',
            // 'fatherJob',
            // 'motherName',
            // 'motherJob',
            // 'tutorName',
            // 'tutorJob',
            // 'created_time',
            // 'updated_time',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
