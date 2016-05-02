<?php

namespace backend\controllers;

use Yii;
use common\models\SchoolReport;
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
 
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            'filename' => 'Học bạ ' . $model->student->name . '.pdf',
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8, 
            // A4 paper format
            'format' => Pdf::FORMAT_A4, 
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER, 
            // your html content input
            'content' => $content,  
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => 'css/school-report.css',
            // any css to be embedded if required
            // 'cssInline' => '.kv-heading-1{font-size:18px}', 
             // set mPDF properties on the fly
            'options' => [
                'title' => 'Học bạ ' . $model->student->name,
                'author' => 'Đào Văn Hùng',
                'creator' => 'Đào Văn Hùng',
            ],
             // call mPDF methods on the fly
            'methods' => [ 
                // 'SetHeader'=>['Krajee Report Header'], 
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);
     
        // return the pdf output as per the destination setting
        return $pdf->render(); 
    }

    public function actionCreate()
    {
        $model = new SchoolReport();
        $model->student = new Student();
        $model->student->ethnic = new Ethnic();
        $model->student->nativeAddress = new Address();
        $model->student->currentAddress = new Address();


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
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
            $model->student->birthday = \DateTime::createFromFormat('d/m/Y', $post['Student']['birthday'])->format('Y-m-d');
            $model->date = \DateTime::createFromFormat('d/m/Y', $post['SchoolReport']['date'])
                ->format('Y-m-d');
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $this->saveCurrentAddress($model, $post);
                $this->saveNativeAddres($model, $post);
                $this->saveObject($model, $post);
                $this->saveYearEvaluation($model, $post);
                if($model->save() && $model->student->save()) {
                    $transaction->commit();
                    return $this->redirect(['student/view', 'id' => $model->id]);
                }
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

    protected function saveCurrentAddress($model, $post) {
        $params = ['districtID' => $post['Address']['currentAddress']['districtID']];
        if($post['Address']['communeID'] != null ) {
            $params['communeID'] = $post['Address']['communeID'];
        }
        if($post['Address']['detailAddress'] != null) {
            $params['detailAddress'] = $post['Address']['detailAddress'];
        }
        $address = Address::findOne($params);
        if($address != null) {
            $model->student->currentAddressID = $address->id;
        } else {
            $address = new Address($params);
            if(!$address->save()) {
                throw new Exception("Error: Can not save current address ", 1);
            }
            $model->student->currentAddressID = $address->id;
        }
        return true;
    }

    protected function saveNativeAddres($model, $post) {
        $address = Address::findOne(['districtID' => $post['Address']['nativeAddress']['districtID']]);
        if($address != null) {
            $model->student->nativeAddressID = $address->id;
        } else {
            throw new Exception("Error: Native Address not found with district and province given", 1);
        }
        return true;
    }

    protected function saveObject($model, $post) {
        if($model->student->objects != null) {
            foreach ($model->student->objects as $object) {
                $model->student->unlink('objects', $object, true);
            }
        }
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
                        throw new Exception("Error: Can not save object", 1);
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
                throw new Exception("Error: Can not save year evaluation", 1);
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
                    throw new Exception("Error: Can not save Achievement", 1);
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
                throw new Exception("Error: Can not save term evaluation", 1);
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
                throw new Exception("Error: Can not save subject score", 1);
            }
        }
        return true;
    }
    
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
