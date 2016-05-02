<?php
use yii\helpers\Html;
use common\models\Subject;
use common\models\Student;
use common\models\Province;
use common\models\District;
use common\models\Commune;
use common\models\Ethnic;
use common\models\Object;
use common\models\School;
use common\models\YearEvaluation;
use common\models\SubjectScore;
use common\models\Achivement;
use common\models\TermEvaluation;
use common\models\Achievement;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\form\ActiveForm;
?>

<div id="SR_form">
    <?php $form = ActiveForm::begin([
        'type' => ActiveForm::TYPE_INLINE,
        'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL, 'showErrors' => true]
    ]); 
    ?>
    <div class=" sr_number">
        <span class="fLabel">Số học bạ: </span> 
        <?= $form->field($model, 'number')->textInput(['maxlength' => true, 'class' => 'input']) ?> 
    </div>
    <div id="page1">
        <div id="std_info">
            <div class="image"></div>
            <div class="name">
                <span class="fLabel">Họ và tên: </span> 
                <?= $form->field($model->student, 'name')->textInput(['maxlength' => true])?>
                <span class="fLabel">Giới tính: </span>
                <?= $form->field($model->student, 'gender')->dropDownList(Student::$gender,
                    ['prompt' => 'Choose gender']) ?>
            </div>
            <div class="field birthday">
                <span class="title_bold">Ngày sinh:</span> 
                <?php $model->student->birthday = date('d/m/Y', strtotime($model->student->birthday));
                    echo $form->field($model->student, 'birthday')->widget(DatePicker::className(), [
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'options' => ['placeholder' => 'Enter birthday'],
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'dd/mm/yyyy'
                        ]
                    ])
                 ?>
            </div>
            <div class="field native_address">
                <span class="fLabel">Nơi sinh: </span>
                <?php 
                    $provinces = ArrayHelper::map(Province::find('')->orderBy('name')->all(), 'id', 'name');
                    if($model->student->nativeAddress->district != null) { 
                        $districts = ArrayHelper::map(District::find()->where(['provinceID' => $model->student->nativeAddress->district->provinceID])->orderBy('name')->all(), 'id', 'name');
                    } else {
                        $districts = ArrayHelper::map(District::find('')->orderBy('name')->all(), 'id', 'name');
                    }
                    echo $form->field($addresses['nativeDistrict'], '[nativeDistrict]provinceID')
                        ->dropDownList($provinces, [
                            'prompt'=>'Select province',
                            'id' => 'nprovince',
                            'options' => [
                                $model->student->nativeAddress->district->provinceID => ['Selected' => true],
                            ],
                            'onchange'=>'
                                if($(this).val() >= 1) {
                                    $( "select#ndistrict" ).removeAttr("disabled");
                                } else {
                                    $( "select#ndistrict" ).attr("disabled", "true");
                                }
                                $.post( "'.Yii::$app->urlManager->createUrl(['address/load-districts']).'", { provinceID: $(this).val() } )
                                .done(function( data ) {
                                    $( "select#ndistrict" ).html( data );
                                });'
                        ]); 
                     echo $form->field($addresses['nativeAddress'], '[nativeAddress]districtID')
                        ->dropDownList($districts,[
                                'prompt'=>'Select district',
                                'id' => 'ndistrict',
                                'options' => [
                                    $model->student->nativeAddress->districtID => ['Selected' => true]
                                ],
                            ]);
                ?>
            </div>
            <div class="ethnic">
            <?php 
                $ethnics = ArrayHelper::map(Ethnic::find('')->orderBy('name')->all(), 'id', 'name');
                echo '<span class="fLabel">Dân tộc: </span>' . $form->field($model->student, 'ethnicID')->dropDownList($ethnics,['prompt' => 'Select ethnic']);
            ?>
            </div>    
            <div class="object">
            <?php
                $objects = ArrayHelper::map(Object::find('')->orderBy('content')->all(), 'id', 'content');
                echo '<span class="fLabel">Thuộc đối tượng chính sách: </span>' . $form->field($model->student, 'objects')
                ->widget(Select2::classname(), [
                    'data' => $objects,
                    'language' => 'en',
                    'options' => [
                        'placeholder' => 'Select objects ...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]); ?>
            </div>
            <div class="field current_address"><span class="fLabel">Chỗ ở hiện tại:</span> 
            <?php 
                if($model->student->currentAddress->district != null) { 
                    $districts = ArrayHelper::map(District::find()->where(['provinceID' => $model->student->currentAddress->district->provinceID])->orderBy('name')->all(), 'id', 'name');
                    $communes = ArrayHelper::map(Commune::find()->where(['districtID' => $model->student->currentAddress->districtID])->orderBy('name')->all(), 'id', 'name');
                } else {
                    $districts = ArrayHelper::map(District::find('')->orderBy('name')->all(), 'id', 'name');
                    $communes = ArrayHelper::map(Commune::find('')->orderBy('name')->all(), 'id', 'name');
                }
                $district_disabled = $model->student->currentAddress->district == null ? true : false;
                $commune_disabled = $model->student->currentAddress->district == null ? true : false;
                echo $form->field($addresses['currentDistrict'], '[currentDistrict]provinceID')
                    ->dropDownList($provinces, [
                        'prompt'=>'Select province',
                        'id' => 'cprovince',
                        'options' => [
                            $model->student->currentAddress->district->provinceID => ['Selected' => true],
                        ],
                        'onchange'=>'{
                            $( "#detailAddress" ).val("");
                            if($(this).val() >= 1) {
                                $( "select#cdistrict" ).removeAttr("disabled");
                            } else {
                                $( "select#cdistrict" ).attr("disabled", "true");
                                $( "select#ccommune" ).attr("disabled", "true");
                                $( "#detailAddress" ).attr("disabled", "true");
                            };
                            $.post( "'.Yii::$app->urlManager->createUrl(['address/load-districts']).'", 
                            { provinceID: $(this).val() } )
                            .done(function( data ) {
                                var data = JSON.parse(data);
                                $( "select#cdistrict" ).html( data["districts"] );
                                $( "select#ccommune" ).html( data["communes"] );
                            });}'
                    ]); 
                 echo $form->field($addresses['currentAddress'], '[currentAddress]districtID')
                    ->dropDownList($districts, [
                            'disabled' => $district_disabled,
                            'prompt'=>'Select district',
                            'id' => 'cdistrict',
                            'options' => [
                                $model->student->currentAddress->districtID => ['Selected' => true]
                            ],
                            'onchange'=>'
                            $( "#detailAddress" ).val("");
                            if($(this).val() >= 1) {
                                $( "select#ccommune" ).removeAttr("disabled");
                                $( "#detailAddress" ).removeAttr("disabled");
                            } else {
                                $( "select#ccommune" ).attr("disabled", "true");
                                $( "#detailAddress" ).attr("disabled", "true");
                            };
                            $.post( "'.Yii::$app->urlManager->createUrl(['address/load-communes']).'", 
                                { districtID: $(this).val() } )
                                .done(function( data ) {
                                    $( "select#ccommune" ).html( data );
                                });'
                        ]);
                echo $form->field($model->student->currentAddress, 'communeID')->dropDownList($communes,
                    [
                        'prompt' => 'Select commune',
                        'id' => 'ccommune'
                    ]);
                echo '<div class="detailAddress"><span class="title"> địa chỉ nhà </span>' . $form->field($model->student->currentAddress, 'detailAddress')->textInput(['id' => 'detailAddress']) . '</div>';
            ?>
            </div>
            <div class="father">
                <div class="name"><span class="fLabel">Họ và tên cha:</span> 
                <?= $form->field($model->student, 'fatherName')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="job"><span class="fLabel">Nghề nghiệp: </span>
                <?= $form->field($model->student, 'fatherJob')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="mother">
                <div class="name"><span class="fLabel">Họ và tên mẹ: </span>
                <?= $form->field($model->student, 'motherName')->textInput(['maxlength' => true])?>
                </div>
                <div class="job"><span class="fLabel">Nghề nghiệp: </span>
                <?= $form->field($model->student, 'motherJob')->textInput(['maxlength' => true])?>
                </div>
            </div>
            <div class="tutor">
                <div class="name"><span class="fLabel">Họ và tên người giám hộ: </span>
                <?= $form->field($model->student, 'tutorName')->textInput(['maxlength' => true])?>
                </div>
                <div class="job"><span class="fLabel">Nghề nghiệp: </span>
                <?= $form->field($model->student, 'tutorJob')->textInput(['maxlength' => true])?>
                </div>
            </div>
        </div>
        <div id="date">
        <?php 
            $model->date = date('d/m/Y', strtotime($model->date));
            echo '<span class="fLabel">Ngày tạo học bạ </span>' . 
                $form->field($model, 'date')->widget(DatePicker::className(), [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'options' => ['placeholder' => 'Enter date of create school report'],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd/mm/yyyy'
                    ]
            ]);?>
        </div>
        <div id="principal_name">
            <span class="fLabel">Hiệu trưởng lúc tạo học bạ: </span> 
            <?= $form->field($model, 'principalName')->textInput(['maxlength' => true]) ?>
        </div>

<!-- year evaluation here -->
    <hr class="horizon_line" />
    <?php 
    if($model->yearEvaluations != null) {
        for($i = 0; $i < sizeof($model->yearEvaluations); $i++) {  
            echo Yii::$app->view->render('_yearForm', [
                'model' => $model, 
                'i' => $i, 
                'studyProcess_model' => $studyProcess_model,
                'yearEvaluation' => $yearEvaluation,
                'form' => $form,
            ]);
        }
    }
    ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end();?>
</div>
