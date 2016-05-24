<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Student;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('var autoUrl = "'.Url::toRoute(['x12/render-modal-auto']).'";', \yii\web\View::POS_HEAD);
$this->registerJs('var sendUrl = "'.Url::toRoute(['x12/render-modal']).'";', \yii\web\View::POS_HEAD);
?>
<div class="student-index">
    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?= Html::a('Create new School Report', ['school-report/create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <?= $this->render('_advanced_search_form') ?>
    <?= 'With selected:'?>
    <?=Html::button('Auto send x12 file to server', 
        [
            'id' => 'autoModalButton', 
            'class' => 'btn btn-primary',
        ]
    );?>
    <?php 
        Modal::begin([
            'header' => '<h3>Choose encrypt type to send</h3>',
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
    <?= '<div>'.Html::checkbox('all_std', false, ['label' => "Select all $dataProvider->totalCount student", 'id' => 'checkAll']).'</div>' ?>

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
                'attribute' => 'schoolReportNumber',
                'value' => function($data) {
                    if($data->schoolReport != null) {
                        return $data->schoolReport->number;
                    } else {
                        return 'Not set';
                    }
                }
            ],
            [
                'attribute' => 'image',
                'format' => 'html',
                'value' => function($data) {
                    return $data->image != null ? Html::img(Yii::$app->params['imageUrl'].$data->image, ['width' => '60px']) : null;
                }
            ],
            [
                'attribute' => 'gender',
                'filter' => Student::$gender,
                'value' => function($data) {
                    return $data->getGenderText();
                }
            ],
            [
                'attribute' => 'birthday',
                'value' => function($data) {
                    return date('d/m/Y', strtotime($data->birthday));
                },
                'filterInputOptions' => [
                    'class'       => 'form-control',
                    'placeholder' => 'd/m/y'
                ]
            ],
            [  
                'attribute' => 'currentAddress',
                'value' => function($data) {
                    return $data->currentAddress->getFullReverseAddress();
                }
            ],
            // 'ethnicID',
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
                    'update' => function($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            ['school-report/update', 'id' => $model->schoolReport->id],
                            [
                                'title' => 'Update School Report',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                    'delete' => function($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-trash"></span>',
                            ['school-report/delete', 'id' => $model->schoolReport->id],
                            [
                                'title' => 'Delete school report of this student',
                                'data-pjax' => '0',
                                'data-confirm' => 'Are you sure to delete school report of this student?',
                                'data-method' => 'post',
                            ]
                        );
                    }
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
