<?php

namespace backend\controllers;

use Yii;
use common\models\SchoolReport;
use common\models\Student;
use common\models\Ethnic;
use common\models\Province;
use common\models\District;
use common\models\Address;
use common\models\Object;
use common\models\RelationStudentObject;
use common\models\YearEvaluation;
use common\models\Achievement;
use common\models\TermEvaluation;
use common\models\Subject;
use common\models\SubjectScore;
use yii\base\Exception;
use common\models\search\SchoolReportSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use kartik\form\ActiveForm;

class SchoolReportController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index', 'view', 'create', 'update', 'delete', 'view-pdf'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ], 
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new SchoolReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionViewPdf($id)
    {
        $model = $this->findModel($id);
        $content = $this->renderPartial('viewPdf', [
            'model' => $model,
        ]);
 
        $pdf = new Pdf([
            'filename' => 'Học bạ ' . $model->student->name . '.pdf',
            'mode' => Pdf::MODE_UTF8, 
            'format' => Pdf::FORMAT_A4, 
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            'destination' => Pdf::DEST_BROWSER, 
            'content' => $content,  
            'cssFile' => 'css/school-report.css',
            'options' => [
                'title' => 'Học bạ ' . $model->student->name,
                'author' => 'Đào Văn Hùng',
                'creator' => 'Đào Văn Hùng',
            ],
            'methods' => [ 
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);
        return $pdf->render(); 
    }

    public function actionCreate()
    {
        $model = new SchoolReport();
        $student = new Student();
        $addresses = [
                'currentDistrict' => new District(),
                'currentAddress' => new Address(),
                'nativeDistrict' => new District(),
                'nativeAddress' => new Address(),
        ];
        $yearEvaluation = new YearEvaluation();

        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $student->currentAddressID = $this->saveCurrentAddress($post)->id;
                $student->nativeAddressID = $this->getNativeAddress($post)->id;
                $this->saveStudent($student, $post);
                $model->studentID = $student->id;
                $this->saveSchoolReport($model, $post);
                $this->saveObject($model, $post);
                $this->saveYearEvaluation($model, $post);
                $transaction->commit();
                return $this->redirect(['student/view', 'id' => $model->student->id]);
            } catch(Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'student' => $student,
                'addresses' => $addresses,
                'yearEvaluation' => $yearEvaluation,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $addresses = [
                'currentDistrict' => new District(),
                'currentAddress' => new Address(),
                'nativeDistrict' => new District(),
                'nativeAddress' => new Address(),
        ];
        $yearEvaluation = new YearEvaluation();
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->student->load($post)) {
            $this->saveImage($model->student);
            $model->student->birthday = \DateTime::createFromFormat('d/m/Y', $post['Student']['birthday'])->format('Y-m-d');
            $model->date = \DateTime::createFromFormat('d/m/Y', $post['SchoolReport']['date'])
                ->format('Y-m-d');
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->student->currentAddressID = $this->saveCurrentAddress($post)->id;
                $model->student->nativeAddressID = $this->getNativeAddress($post)->id;
                $this->saveObject($model, $post);
                $this->saveYearEvaluation($model, $post);
                if(!$model->save()) {
                    throw new Exception("Error save school report: ".reset($model->getErrors())[0], 1);
                }
                if(!$model->student->save()) {
                    throw new Exception("Error save student: ".reset($model->student->getErrors())[0], 1);
                }
                $transaction->commit();
                return $this->redirect(['student/view', 'id' => $model->student->id]);
            } catch(Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'addresses' => $addresses,
                'yearEvaluation' => $yearEvaluation,
            ]);
        }
    }

    protected function saveImage($student) {
        $image = UploadedFile::getInstance($student, 'imageFile');
        if($image != null) {
            $ext = end((explode(".", $image->name)));
            $newImageName = $student->name.'_'.Yii::$app->security->generateRandomString().".{$ext}";
            $image->saveAs(Yii::$app->params['imagePath'].$newImageName);
            $student->image = $newImageName;
        }
        return true;
    }

    protected function saveSchoolReport($model, $post) {
        $model->date = \DateTime::createFromFormat('d/m/Y', $post['SchoolReport']['date'])
                ->format('Y-m-d');
        if(!$model->save()) {
            throw new Exception("Error save school report: ".reset($model->getErrors())[0], 1);
        }
        return true;
    }

    protected function saveStudent($student_model, $post) {
        $student_model->load($post);
        $student_model->birthday = \DateTime::createFromFormat('d/m/Y', $post['Student']['birthday'])->format('Y-m-d');
        $this->saveImage($student_model);
        if(!$student_model->save()) {
            throw new Exception("Error save student: ".reset($student_model->getErrors())[0], 1);
        }
        return true;
    }

    protected function saveCurrentAddress($post) {
        $params = ['districtID' => $post['Address']['currentAddress']['districtID']];
        if($post['Address']['communeID'] != null ) {
            $params['communeID'] = $post['Address']['communeID'];
        }
        if($post['Address']['detailAddress'] != null) {
            $params['detailAddress'] = $post['Address']['detailAddress'];
        }
        $address = Address::findOne($params);
        if($address == null) {
            $address = new Address($params);
            if(!$address->save()) {
                throw new Exception("Error save current address: ".reset($address->getErrors())[0], 1);
            }
        }
        return $address;
    }

    protected function getNativeAddress($post) {
        $address = Address::findOne(['districtID' => $post['Address']['nativeAddress']['districtID']]);
        if($address == null) {
            throw new Exception("Error: Native Address not found with district and province given", 1);
        }
        return $address;
    }

    protected function saveObject($model, $post) {
        $this->delRelatedObject($model->student);
        if($post['Student']['objects'] != null) {
            foreach ($post['Student']['objects'] as $objectID) {
                $object = Object::findOne($objectID);
                if($object == null) {
                    throw new Exception("Error: Not found object given", 1);
                }
                $params = [
                    'studentID' => $model->studentID, 
                    'objectID' => $object->id,
                ];
                $relationObjStd = RelationStudentObject::findOne($params);
                if($relationObjStd == null) {
                    $relationObjStd = new RelationStudentObject($params);
                    if(!$relationObjStd->save()) {
                        throw new Exception("Error save object: ".reset($relationObjStd->getErrors())[0], 1);
                    }
                }
            }
        }
        return true;
    }

    protected function saveYearEvaluation($model, $post) {
        $this->delRelatedYearEvaluation($model);
        foreach ($post['YearEvaluation'] as $index => $yearEvaluation) {
            $newYearEvaluation = new YearEvaluation($yearEvaluation);//to do
            $newYearEvaluation->schoolReportID = $model->id;
            $newYearEvaluation->studyDepartment = YearEvaluation::$department[$yearEvaluation['studyDepartment']];
            \DateTime::createFromFormat('d/m/Y', $post['Student']['birthday'])->format('Y-m-d');
            $newYearEvaluation->date = \DateTime::createFromFormat('d/m/Y', $newYearEvaluation->date)->format('Y-m-d');
            if(!$newYearEvaluation->save()) {
                throw new Exception("Error save yearEvaluation: ".reset($newYearEvaluation->getErrors())[0], 1);
            }
            $this->saveAchievement($index, $newYearEvaluation, $post);
            $this->saveTermEvaluation($index, $newYearEvaluation, $post);
        }
        return true;
    }

    protected function delRelatedYearEvaluation($sr) {
        if($sr->yearEvaluations != null) {
            foreach ($sr->yearEvaluations as $yearEvaluation) {
                $this->delRelatedTermEvaluation($yearEvaluation);   
                $this->delRelatedAchievement($yearEvaluation);
                $sr->unlink('yearEvaluations', $yearEvaluation, true);
            }
        }
        return true;
    }

    protected function delRelatedTermEvaluation($yearEvaluation) {
        if($yearEvaluation->termEvaluations != null) {
            foreach ($yearEvaluation->termEvaluations as $termEvaluation) {
                $this->delRelatedSubjectScore($termEvaluation);
                $yearEvaluation->unlink('termEvaluations', $termEvaluation, true);
            }
        }
        return true;
    }

    protected function delRelatedAchievement($yearEvaluation) {
        if($yearEvaluation->achievements != null) {
            foreach ($yearEvaluation->achievements as $achievement) {
                $yearEvaluation->unlink('achievements', $achievement, true);
            }
        }
        return true;
    }    

    protected function delRelatedSubjectScore($termEvaluation) {
        if($termEvaluation->subjectScores != null) {
            foreach ($termEvaluation->subjectScores as $subjectScore) {
                $termEvaluation->unlink('subjectScores', $subjectScore, true);
            }
        }
        return true;
    }

    protected function saveAchievement($yearIndex, $yearEvaluation, $post) { //to do
        foreach ($post['Achievement'][$yearIndex] as $achievement) {
            if($achievement['name'] != null) {
                $newAchievement = new Achievement($achievement);
                $newAchievement->yearEvaluationID = $yearEvaluation->id;
                if(!$newAchievement->save()) {
                    throw new Exception("Error save achievement: ".reset($newAchievement->getErrors())[0], 1);
                }
            }
        }
        return true;
    }

    protected function saveTermEvaluation($yearIndex, $yearEvaluation, $post) {
        foreach ($post['TermEvaluation'][$yearIndex] as $term=>$termEvaluation) {
            $newTermEvaluation = new TermEvaluation($termEvaluation);
            $newTermEvaluation->term = $term;
            $newTermEvaluation->yearEvaluationID = $yearEvaluation->id;
            if(!$newTermEvaluation->save()) {
                throw new Exception("Error save termEvaluation: ".reset($newTermEvaluation->getErrors())[0], 1);
            }
            $this->saveSubjectScore($yearIndex, $term, $newTermEvaluation, $post);
        }
        return true;
    }

    protected function saveSubjectScore($yearIndex, $term, $termEvaluation, $post) {
        foreach ($post['SubjectScore'][$yearIndex][$term] as $subjectName => $score) {
            $subject = Subject::findOne(['name' => $subjectName]);
            if($subject == null) {
                throw new Exception("Error: Not found subject ".$subjectName." in database", 1);
            }
            $newSubjectScore = new SubjectScore($score);
            $newSubjectScore->termEvaluationID = $termEvaluation->id;
            $newSubjectScore->subjectID = $subject->id;
            $newSubjectScore->teacherName = $post['SubjectScore'][$yearIndex][$subjectName]['teacherName'];
            if(!$newSubjectScore->save()) {
                throw new Exception("Error save subjectScore: ".reset($newSubjectScore->getErrors())[0], 1);
            }
        }
        return true;
    }
    
    public function actionDelete($id)
    {
        $sr = $this->findModel($id);
        $this->delRelatedStudent($sr);
        $this->delRelatedYearEvaluation($sr);
        $sr->delete();

        return $this->redirect(['student/index']);
    }

    protected function delRelatedStudent($sr) {
        if($sr->student != null) {
            $this->delRelatedObject($sr->student);
            $sr->student->delete();
        }
        return true;
    }

    protected function delRelatedObject($student) {
        if($student->objects != null) {
            foreach ($student->objects as $object) {
                $student->unlink('objects', $object, true);
            }
        }
        return true;
    }

    protected function findModel($id)
    {
        if (($model = SchoolReport::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
