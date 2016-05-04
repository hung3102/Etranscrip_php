<?php

namespace frontend\controllers;

use Yii;
use common\models\Student;
use common\models\search\StudentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\DynamicModel;
use backend\components\FileSecure;

class StudentController extends Controller
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

    public function beforeAction($action)
    {            
        if ($action->id == 'receive-file') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $searchModel = new StudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $x12Model = new DynamicModel(['fileName']);
        $x12Model->addRule(['fileName'], 'file')
            ->addRule(['fileName'], 'required');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'x12Model' => $x12Model,
        ]);
    }

    /**
     * Displays a single Student model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Student model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Student();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Student model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionReceiveFile() {
        $this->saveImages();
        $srFile = $_FILES['sr']['tmp_name'];
        $fileSecure = new FileSecure();
        $decryptData = $fileSecure->decryptSecuredFile($srFile);
        file_put_contents(Yii::$app->params['x12resource'].'/x12/'.$_FILES['sr']['name'], $decryptData);
    }

    protected function saveImages() {
        if($_FILES['images']) {
            foreach($_FILES['images']['error'] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    move_uploaded_file($_FILES['images']['tmp_name'][$key], Yii::$app->params['imagePath']
                        .$_FILES["images"]["name"][$key]);
                }
            }
        }
    }

    protected function findModel($id)
    {
        if (($model = Student::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
