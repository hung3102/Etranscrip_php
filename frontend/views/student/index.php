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
    <?= $this->render('_advanced_search_form') ?>

    <p>
        <?= Html::a('Auto Synchronise', ['x12/auto-syn'], ['class' => 'btn btn-success']) ?>
        <!-- <?= Html::beginForm(['x12/parse'],'get');?> -->
        <?php Modal::begin([
            'header'=>'<h3>Choose x12 file to synchronise data</h3>',
            'toggleButton' => [
                'label'=>'Synchronise data', 
                'class'=>'btn btn-success'
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
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            
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
                'template' => '{view}',
            ],
        ],
    ]); ?>

</div>
