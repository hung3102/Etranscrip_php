<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Student;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;


$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('Create Student', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= 'With selected:'?>
    <?=Html::button('Auto send x12 file to server', 
        [
            'id' => 'autoModalButton', 
            'class' => 'btn btn-primary',
        ]
    );?>
    <?php 
        Modal::begin([
            'header' => '<h3>Choose server url and encrypt type to send</h3>',
            'id' => 'autoSendModal',
        ]);
        echo '<div id="autoModalContent"></div>';
        Modal::end();
    ?>
    
    <?=Html::button('Send x12 file to server', 
        [
            'id' => 'modalButton', 
            'class' => 'btn btn-primary',
        ]
    );?>
    <?php 
        Modal::begin([
            'header' => '<h3>Choose file name, server url and encrypt type to send</h3>',
            'id' => 'sendModal',
        ]);
        echo '<div id="modalContent"></div>';
        Modal::end();
    ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'std_grid',
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
            ],
            'id',
            'name',
            [
                'attribute' => 'schoolReport',
                'value' => function($data) {
                    if($data->schoolReport != null) {
                        return $data->schoolReport->number;
                    } else {
                        return 'Not set';
                    }
                }
            ],
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
                'attribute' => 'nativeAddressID',
                'value' => function($data) {
                    return $data->nativeAddress->getFullAddress();
                },
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

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            ['school-report/view', 'id' => $model->schoolReport->id],
                            [
                                'title' => 'View school report',
                                'data-pjax' => '0',
                            ]
                        );
                    }
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
