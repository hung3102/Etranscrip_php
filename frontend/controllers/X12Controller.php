<?php
namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\components\x12\X12Creator;
use backend\components\x12\X12Parser;
use backend\components\x12\Cf;
use common\models\SchoolReport;
use yii\base\Exception;
use common\models\X12Integration;
use yii\base\DynamicModel;
use yii\web\UploadedFile;

class X12Controller extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create'],
                'rules' => [
                    [
                        'actions' => ['create', 'error'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionParse() {
        $x12Model = new DynamicModel(['fileName']);
        $x12Model->addRule(['fileName'], 'file')
            ->addRule(['fileName'], 'required');
        if($x12Model->load(Yii::$app->request->post())) {
            $fileName = UploadedFile::getInstance($x12Model, 'fileName');
            $filePath = Yii::$app->params['x12resource'].'/x12/'.$fileName;
            $parser = new X12Parser($this->cf());
            $x12 = $parser->parse($filePath);
            $x12Integration = new X12Integration();
            if($x12Integration->integrate($x12)) {
                Yii::$app->session->setFlash('success', 'Success : Synchronise school reports from x12 file successfully');
                return $this->redirect(['student/index']);
            }
        }
    }

    public function actionAutoSyn() {
        $filePath = Yii::$app->params['x12resource'].'/x12/data.edi';
        $parser = new X12Parser($this->cf());
        $x12 = $parser->parse($filePath);
        $x12Integration = new X12Integration();
        if($x12Integration->integrate($x12)) {
            Yii::$app->session->setFlash('success', 'Success : Synchronise school reports from x12 file successfully');
            return $this->redirect(['student/index']);
        }
    }

    private function cf() {
        $cfX12 = new Cf("X12");
        $cfISA = $cfX12->addChild("ISA", "ISA");
        $cfGS = $cfISA->addChild("GS", "GS");
        $cfST = $cfGS->addChild("ST", "ST");
        $cfSR = $cfST->addChild("SR", "SR");
        $cfSTD = $cfSR->addChild("STD", "STD");
        $cfSTD->addChild("CA", "CA");
        $cfSTD->addChild("NA", "NA");
        $cfSTD->addChild("OJ", "OJ");
        $cfYE = $cfSR->addChild("YE", "YE");
        $cfYE->addChild("SCH", "SCH");
        $cfYE->addChild("ACV", "ACV");
        $cfTE = $cfYE->addChild("TE", "TE");
        $cfTE->addChild("SS", "SS");
        $cfGS->addChild("SE", "SE");
        $cfISA->addChild("GE", "GE");
        $cfX12->addChild("IEA", "IEA");
        return $cfX12;  
    }

}
