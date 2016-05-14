<?php

namespace backend\controllers;

use Yii;
use common\models\School;
use common\models\search\SchoolSearch;
use common\models\Address;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

class SchoolController extends Controller
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
                'only' => ['index', 'view', 'create', 'update', 'delete'],
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
        $searchModel = new SchoolSearch();
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

    public function actionCreate()
    {
        $model = new School();

        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $address_params = ['districtID' => $post['Address']['districtID']];
            $address = Address::findOne($address_params);
            $model->addressID = $address->id;
            if($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $address_params = ['districtID' => $post['Address']['districtID']];
            $address = Address::findOne($address_params);
            $model->addressID = $address->id;
            if($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
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

    public function actionLoadSchools() {
        $provinceID = Yii::$app->request->post('provinceID');
        $districtID = Yii::$app->request->post('districtID');
        $allSchools = School::find('')->orderBy('name')->all();
        $schools = [];
        if($districtID != null) {
            foreach ($allSchools as $school) {
                if($school->address->districtID == $districtID) {
                    $schools[] = $school;
                }
            }
        } else if($provinceID != null) {
            foreach ($allSchools as $school) {
                if($school->address->district->provinceID == $provinceID) {
                    $schools[] = $school;
                }
            }
        }
        $school_data = "<option value=''>Select School</option>";
        if(!empty($schools)) {
            foreach ($schools as $school) {
                $school_data .= '<option value=' . $school->id . '>' . $school->name . '</option>';
            }   
        }

        return Json::encode($school_data);
    }

    protected function findModel($id)
    {
        if (($model = School::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
