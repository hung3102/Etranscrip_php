<?php

namespace backend\controllers;

use Yii;
use common\models\Address;
use common\models\search\AddressSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\District;
use common\models\Commune;
use yii\helpers\Json;

class AddressController extends Controller
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
        $searchModel = new AddressSearch();
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
        $model = new Address();

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

    protected function findModel($id)
    {
        if (($model = Address::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionLoadDistricts() {
        $provinceID = Yii::$app->request->post('provinceID');
        $districts = District::find()->where(['provinceID' => $provinceID])->orderBy('name')->all();
        $district_data = "<option value=''>Select district</option>";
        if($districts != null) {
            foreach ($districts as $district) {
                $district_data .= '<option value=' . $district->id . '>' . $district->name . '</option>';
            }
        }
        return Json::encode($district_data);
    }

    public function actionLoadCommunes() {
        $districtID = Yii::$app->request->post('districtID');
        $commune_data = "<option value=''>Select commune</option>";
        if($districtID != null) {
            $communes = Commune::find()->where(['districtID' => $districtID])->orderBy('name')->all();
            if($communes != null) {
                foreach ($communes as $commune) {
                    $commune_data .= '<option value=' . $commune->id . '>' 
                        . $commune->name . '</option>';
                }
            }
        }
        return Json::encode($commune_data);
    }
}
