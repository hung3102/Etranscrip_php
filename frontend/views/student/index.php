<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Student;
use common\widgets\Alert;
use kartik\file\FileInput;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\Student */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-index">
    <?php if(Yii::$app->session->hasFlash('error')) {
        echo Alert::widget();
    } else if(Yii::$app->session->hasFlash('success')) {
        echo Alert::widget();
    } ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Student', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Auto Synchronise', ['x12/auto-syn'], ['class' => 'btn btn-info']) ?>
        <!-- <?= Html::beginForm(['x12/parse'],'get');?> -->
        <?php Modal::begin([
            'header'=>'<h3>Choose x12 file to synchronise data</h3>',
            'toggleButton' => [
                'label'=>'Synchronise data', 
                'class'=>'btn btn-info'
            ],
        ]);
        $form1 = ActiveForm::begin([
            'action' => ['x12/parse'],
            'options'=>['enctype'=>'multipart/form-data'] // important
        ]);
        echo $form1->field($x12Model, 'fileName')->widget(FileInput::className(), [
            'pluginOptions' => [
                'showUpload' => false,
            ]
        ]); 
        echo '<br />'.Html::submitButton('Synchronise', ['class' => 'btn btn-primary',]);
        ActiveForm::end();
        Modal::end(); ?>
    </p>
    <?php
    // echo FileInput::widget([
    //     'name' => 'attachment_30',
    //     'pluginOptions' => [
    //         'showPreview' => false,
    //         'showCaption' => false,
    //         // 'elCaptionText' => '#customCaption',
    //         'showUpload' => false,
    //         'browseLabel' => 'Synchronise data',
    //         'removeLabel' => '',
    //     ]
    // ]);
    ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            
            'id',
            'name',
            [
                'attribute' => 'schoolReport',
                'value' => function($data) {
                    if($data->schoolReport != null) {
                        return $data->schoolReport->number;
                    } else {
                        return 'No school report';
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