<?php 
use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\School;
use common\models\YearEvaluation;
use common\models\Subject;
use common\models\TermEvaluation;
use common\models\Achievement;
use kartik\date\DatePicker;
use common\models\SubjectScore;
?>
<?php 
// if(!isset($form)) {
//     $form = ActiveForm::begin([
//         'type' => ActiveForm::TYPE_INLINE,
//         'id' => 'w'.($i+1),
//         'action' => '/Etranscript/backend/web/school-report/update?id=1',
//         'formConfig' => ['labelSpan' => 1, 'deviceSize' => ActiveForm::SIZE_SMALL, 'showErrors' => true]
//     ]); 
//     $initForm++;
// }
$studyProcess = (isset($model->studyProcesses[$i]) && $model->studyProcesses[$i] != null) ? $model->studyProcesses[$i] : null;
$yearE = isset($model->yearEvaluations[$i]) ? $model->yearEvaluations[$i] : null;
?>
<div class="year_evaluation">
    <!-- <div class="remove_button"><?= Html::button('Remove this year', ['class' => 'removeYear_button btn btn-info']) ?></div> -->
    <div class="title"><?= 'Đánh giá năm học'?></div>
    <div class="part1">
        <div class="fromYear"><span class="fLabel">Từ năm</span> <?= $form->field($studyProcess_model, "[mark][$i]fromYear")->textInput(['value' => $studyProcess != null ? $studyProcess->fromYear : null]) ?>
        <span class="toYear"><span class="fLabel">đến năm</span> <?= $form->field($studyProcess_model, "[mark][$i]toYear")->textInput(['value' => $studyProcess != null ? $studyProcess->toYear : null]) ?>
        </span>
        </div>
        <div class="class"><span class="fLabel">Lớp</span> <?= $form->field($studyProcess_model, "[mark][$i]class")->textInput(['value' => $studyProcess != null ? $studyProcess->class : null]) ?>
        <span class="school"><span class="fLabel">trường</span>
        <?php 
            $schools = ArrayHelper::map(School::find('')->orderBy('name')->all(), 'id', 'name');
            $schoolID = isset($model->studyProcesses[$i]) ? $model->studyProcesses[$i]->schoolID : null;
            echo $form->field($studyProcess_model, "[mark][$i]schoolID")->dropDownList(
                $schools, [
                    'prompt' => 'Select school', 
                    'class' => 'input',
                    'options' => [$schoolID => ['Selected' => true]]
                ]
            );
        ?>
        </span>
        </div>
        <span class="department">
        <span class="fLabel">Ban </span>
            <?= $form->field($yearEvaluation, "[mark][$i]studyDepartment")->dropDownList(
                YearEvaluation::$department, [
                    'options' => [$yearE != null ? $yearE->studyDepartment : null => ['Selected' => true]]
                ]) ?>
        </span>
        <table class="year_table">
            <thead>
                <tr>
                    <th class="subject_name" rowspan="2">Môn học/Hoạt động GD</th>
                    <th class="score" colspan="3">Điểm trung bình hoặc xếp loại các môn</th>
                    <th class="score_after" rowspan="2">Điểm hoặc xếp loại sau KT lại (nếu có)</th>
                    <th class="teacher" rowspan="2">Giáo viên bộ môn</th>
                </tr>
                <tr>
                    <th class="term1">H.kỳ I</th>
                    <th class="term2">H.kỳ II</th>
                    <th class="term3">CN</th>
                </tr>
            </thead>
            <tbody>
                <?php $subjects = Subject::find('')->all();
                    foreach ($subjects as $subject) { ?>
                <tr class="subject_row">
                    <td><?= $subject->name ?></td>
                    <td>
                    <?php
                        if(($yearE != null) && $yearE->checkTermExist(1)){
                            $termEvaluation = $yearE->getTerm(1);
                            $subjectScore = $termEvaluation->getSubjectScore($subject->name);
                        } else {
                            $termEvaluation = new TermEvaluation();
                            $subjectScore = new SubjectScore();
                        }
                        echo $form->field($subjectScore, "[mark][$i][1][$subject->name]score")->textInput(['class' => 'input']);
                    ?>
                    </td>
                    <td>
                    <?php
                        if($yearE != null && $yearE->checkTermExist(2)){
                            $termEvaluation = $yearE->getTerm(2);
                            $subjectScore = $termEvaluation->getSubjectScore($subject->name);
                        } else {
                            $termEvaluation = new TermEvaluation();
                            $subjectScore = new SubjectScore();
                        }
                        echo $form->field($subjectScore, "[mark][$i][2][$subject->name]score")->textInput(['class' => 'input']);
                    ?>  
                    </td>
                    <td>
                    <?php
                        if($yearE != null && $yearE->checkTermExist(0)){
                            $termEvaluation = $yearE->getTerm(0);
                            $subjectScore = $termEvaluation->getSubjectScore($subject->name);
                        } else {
                            $termEvaluation = new TermEvaluation();
                            $subjectScore = new SubjectScore();
                        }
                        echo $form->field($subjectScore, "[mark][$i][0][$subject->name]score")->textInput(['class' => 'input']);
                    ?>  
                    </td>
                    <td></td>
                    <td><?= $form->field($subjectScore, "[mark][$i][$subject->name]teacherName")->textInput([
                        'class' => 'input']) ?>
                    </td>
                </tr>
                <?php } ?>
                <tr class="comment">
                    <td colspan="6">
                        <div class="teacher_comment">
                        <?= '<div class="label1">Ghi chú của giáo viên: </div>' . $form->field($yearEvaluation, "[mark][$i]note")->textArea(['class' => 'input', 'value' => $yearE != null ? $yearE->note : null]) ?>
                        </div>
                        <div class="teacher_name">
                        <?= '<span class="label2">Tên giáo viên chủ nhiệm: </span>' . $form->field($yearEvaluation, "[mark][$i]teacherName")->textInput(['maxlength' => true, 'class' => 'input', 'value' => $yearE != null ? $yearE->teacherName : null]) ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="conduct_capacity">
        <table>
            <tr class="title_row">
                <td rowspan="2" class="title1">HỌC KỲ</td>
                <td colspan="2" class="title2">Kết quả xếp loại</td>
                <td rowspan="2">TS buổi nghỉ học cả năm</td>
                <td colspan="2" class="order_after">Xếp loại sau KT lại các môn học hoặc rèn luyện thêm về HK</td>
                <td class="upgrade" rowspan="5">
                    <?php 
                    $array = [
                        'Được lên lớp thẳng' => 'Được lên lớp thẳng',
                        'Được lên lớp sau kiểm tra lại các môn học hoặc rèn luyện thêm về hạnh kiểm' => "Được lên lớp sau kiểm tra lại các môn học hoặc rèn luyện thêm về hạnh kiểm",
                        'Không được lên lớp' => 'Không được lên lớp'
                    ];
                    echo 'Loại lên lớp ' . $form->field($yearEvaluation, "[mark][$i]upGradeType")
                        ->dropDownList($array, ['class' => 'upgrade_input', 'options' => [($yearE != null ? $yearE->upGradeType : null) => ['Selected' => true]]]) ?>
                </td>
            </tr>
            <tr class="sub_title">
                <td>HK</td>
                <td>HL</td>
                <td>HK</td>
                <td>HL</td>
            </tr>
            <tr class="term1">
                <td class="name1">Học kỳ I</td>
                <td><?= $form->field($termEvaluation, "[mark][$i][1]conduct")->textInput(['class' => 'input', 'value' => $yearE != null ? $yearE->getConduct(1) : null]) ?></td>
                <td><?= $form->field($termEvaluation, "[mark][$i][1]learnCapacity")->textInput(['value' => $yearE != null ? $yearE->getLearnCapacity(1) : null, 'class' => 'input']) ?></td>
                <td rowspan="3" class="miss_content">
                    <?= $form->field($yearEvaluation, "[mark][$i]missedLesson")->textInput(['class' => 'input', 'value' => $yearE != null ? $yearE->missedLesson : null])?>
                </td>
                <td rowspan="3"></td>
                <td rowspan="3"></td>
            </tr>
            <tr class="term2">
                <td class="name2">Học kỳ II</td>
                <td><?= $form->field($termEvaluation, "[mark][$i][2]conduct")->textInput(['class' => 'input', 'value' => ($yearE != null ? $yearE->getConduct(2) : null)]) ?></td>
                <td><?= $form->field($termEvaluation, "[mark][$i][2]learnCapacity")->textInput(['value' => ($yearE != null ? $yearE->getLearnCapacity(2) : null), 'class' => 'input']) ?></td>
            </tr>
            <tr class="whole_year">
                <td class="name1">Cả năm</td>
                <td><?= $form->field($termEvaluation, "[mark][$i][0]conduct")->textInput(['class' => 'input', 'value' => ($yearE != null ? $yearE->getConduct(0) : null)]) ?></td>
                <td><?= $form->field($termEvaluation, "[mark][$i][0]learnCapacity")->textInput(['value' => ($yearE != null ? $yearE->getLearnCapacity(0) : null), 'class' => 'input']) ?></td>
            </tr>
            <tr class="add_info">
                <td colspan="7">
                    <div class="certificate"><span class="title1">- Có chứng chỉ nghề phổ thông: </span>
                    <?= $form->field($yearEvaluation, "[mark][$i]vocationalCertificate")->textInput(['class' => 'content_input', 'value' => ($yearE != null ? $yearE->vocationalCertificate : null)]) ?>
                    <span class="level"><span class="title2">Loại: </span>
                    <?= $form->field($yearEvaluation, "[mark][$i]vocationalCertificateLevel")->textInput(['value' => ($yearE != null ? $yearE->vocationalCertificateLevel : null)]) ?>
                    </span>
                    </div>
                    <div class="achivement">
                        <span class="title">- Các giải thưởng và khen thưởng: </span>
                        <?php
                            if($yearE != null) {
                                for ($j=0; $j < sizeof($yearE->achievements); $j++) { 
                                    $achievement = $yearE->achievements[$j];
                                    echo $form->field($achievement, "[mark][$i][$j]name")->textInput(['class' => 'input']);
                                }
                            } else {
                                $achievement = new Achievement();
                                echo $form->field($achievement, 'name')->textInput(['class' => 'input']);
                            }
                        ?>
                    </div>
                </td>
            </tr>
            <tr class="comment">
                <td colspan="7">
                    <div class="teacher_comment">
                        <div class="title1">Nhận xét của giáo viên chủ nhiệm: </div>
                        <?= $form->field($yearEvaluation, "[mark][$i]teacherComment")->textArea(['class' => 'content', 'value' => $yearE != null ? $yearE->teacherComment : null]) ?>
                    </div>
                    <div class="principal_approval">
                        <div class="title2">Phê duyệt của hiệu trưởng: </div>
                        <?= $form->field($yearEvaluation, "[mark][$i]principalApproval")->textArea(['class' => 'content', 'value' => $yearE != null ? $yearE->principalApproval : null]) ?>
                    </div>
                    <div class="date">
                    <?php 
                        $date = $yearE != null ? date('d/m/Y', strtotime($yearE->date)) : null;
                        $yearEvaluation->date = $date;
                        echo '<span class="fLabel"> Ngày </span>' . $form->field($yearEvaluation, "[mark][$i]date")->widget(DatePicker::className(), [
                                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                'options' => ['placeholder' => 'Choose date'],
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd/mm/yyyy'
                                ]
                            ]);
                    ?>        
                    </div>
                </td>
            </tr>   
        </table>
    </div>
    <hr class="horizon_line" />
</div>