<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\components\x12\X12Creator;
use backend\components\x12\X12Parser;
use backend\components\x12\Cf;
use common\models\SchoolReport;
use yii\base\Exception;
use common\models\X12Integration;

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

    public function actionCreate() {
        $file = Yii::$app->basePath . '/x12resource/data.edi';
        $action=Yii::$app->request->post('action');
        $schoolReportIDs=(array)Yii::$app->request->post('selection');
        if($schoolReportIDs == null) {
            Yii::$app->session->setFlash('error', 'Error: No checkbox was checked!');
            return $this->redirect(['school-report/index']);
        }
        $x12 = new X12Creator();
        if(file_put_contents($file, $x12->create($schoolReportIDs))) {
            Yii::$app->session->setFlash('success', 'x12 file was created successfully');
            return $this->redirect(['school-report/index']);
        }
    }

    public function actionParse() {
        $parser = new X12Parser($this->cf());
        $f1 = Yii::$app->basePath.'/x12resource/data.edi';
        $x12 = $parser->parse($f1);
        $x12Integration = new X12Integration();
        $x12Integration->integrate($x12);
        // $this->execute($x12);
        // return htmlspecialchars($x12->toXML());
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
        $cfSP = $cfSR->addChild("SP", "SP");
        $cfSP->addChild("SCH", "SCH");
        $cfYE = $cfSR->addChild("YE", "YE");
        $cfYE->addChild("ACV", "ACV");
        $cfTE = $cfYE->addChild("TE", "TE");
        $cfTE->addChild("SS", "SS");
        $cfGS->addChild("SE", "SE");
        $cfISA->addChild("GE", "GE");
        $cfX12->addChild("IEA", "IEA");
        return $cfX12;  
    }

    private function execute($x12) {
        $srLoops = $x12->findLoop("SR");
        if($srLoops == null) {
            throw new Exception("Not found SchoolReport in x12 file", 1);
        }
        foreach ($srLoops as $srLoop) {
            $yeLoop = $srLoop->findLoop("YE");
            
            echo "<pre>";
            var_dump($yeLoop[0]->getSegment(0));
            echo "</pre>";
            echo "\n=============\n";
        }
        exit();
    }

}
