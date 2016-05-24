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
use yii\base\DynamicModel;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Student;
use backend\components\FileSecure;
use common\models\Modal;

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

    public function actionRenderModal() {
        $schoolReportNumbers = $this->checkSr();
        $x12Model = new Modal();
        $x12Model->schoolReportNumbers = $schoolReportNumbers;
        return $this->renderAjax('renderModal', ['x12Model' => $x12Model]);
    }

    public function actionRenderModalAuto() {
        $schoolReportNumbers = $this->checkSr();
        $x12Model = new Modal(['scenario' => 'autoSyn']);
        $x12Model->schoolReportNumbers = $schoolReportNumbers;
        return $this->renderAjax('renderModal', ['x12Model' => $x12Model]);
    }

    private function checkSr() {
        $data = Yii::$app->request->post();
        if($data['allStd'] == 'true') {
            $schoolReportNumbers = ArrayHelper::getColumn(SchoolReport::find('')->all(), 'number');
            return $schoolReportNumbers;
        }
        if(!isset($data['studentIDs'])) {
            Yii::$app->session->setFlash('error', 'Error: Choose at least one student!');
            return $this->redirect(['student/index']);
        }
        $schoolReportNumbers = [];
        $nonSR = [];
        foreach ($data['studentIDs'] as $studentID) {
            $student = Student::findOne($studentID);
            if($student->schoolReport == null) {
                $nonSR[] = $student;
            } else {
                $schoolReportNumbers[] = Student::findOne($studentID)->schoolReport->number;
            }
        }
        if($nonSR != null) {
            $err_messages = "Error : The following students do not have school report: \n";
            foreach ($nonSR as $student) {
                $err_messages .= $student->name.', ';
            }
            $err_messages = substr($err_messages, 0, -2);
            Yii::$app->session->setFlash('error', $err_messages);
            return $this->redirect(['student/index']);
        }
        return $schoolReportNumbers;
    }

    public function actionSendData() {
        $post = Yii::$app->request->post('Modal');
        if (isset($post['fileName']) && isset($post['serverUrl'])) {
            $x12Model = new Modal();
            $x12Model->fileName = $post['fileName'];
            $x12Model->serverUrl = $post['serverUrl'];
        } else {
            $x12Model = new Modal(['scenario' => 'autoSyn']);
        }
        $x12Model->schoolReportNumbers = unserialize($post['schoolReportNumbers']);
        $x12Model->encryptType = $post['encryptType'];
        $x12Model->validate();
        $x12Creator = new X12Creator();
        $data = $x12Creator->create($x12Model->schoolReportNumbers);
        if(isset($post['fileName']) && isset($post['serverUrl'])) {
            file_put_contents(Yii::$app->params['x12resource'].'/x12/'.$x12Model->fileName, $data);
            $x12File = Yii::$app->params['x12resource'].'/x12/'.$x12Model->fileName;
            $serverUrl = $x12Model->serverUrl;
        } else {
            file_put_contents(Yii::$app->params['x12resource'].'/x12/data.edi', $data);
            $x12File = Yii::$app->params['x12resource'].'/x12/data.edi';
            $serverUrl = 'http://172.17.0.2/server/frontend/web/student/receive-file';
        }
        $sendData = $this->createSendData($x12File, $x12Model->encryptType, $x12Model->schoolReportNumbers);
        if($this->send($sendData, $serverUrl)) {
            Yii::$app->session->setFlash('success', 'Send file successfully.');
            return $this->redirect(['student/index']);
        }
    }

    protected function createSendData($filePath, $encryptType, $schoolReportNumbers) {
        $fileSecure = new FileSecure();
        $securedData = $fileSecure->createSecuredData($filePath, $encryptType);
        $fileName = substr($filePath,strrpos($filePath,'/')+1);
        file_put_contents(Yii::$app->params['x12resource'].'/encryptedX12/'.$fileName, 
            $securedData);
        $sendData['sr'] = new \CurlFile(Yii::$app->params['x12resource'].'/encryptedX12/'.$fileName, 'text/edi', $fileName);
        foreach ($schoolReportNumbers as $schoolReportNumber) {
            $sr = SchoolReport::findOne(['number' => $schoolReportNumber]);
            if($sr->student == null) {
                throw new Exception("Error: Not found student with schoolReport number of ".$schoolReportNumber, 1);
            }
            $student = $sr->student;
            if($student->image != null) {
                $sendData["images[$student->image]"] = new \CurlFile(Yii::$app->params['imagePath']
                    .$student->image, 'photo/image', $student->image);
            }
        }
        return $sendData;
    }

    protected function send($sendData, $serverUrl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_URL, $serverUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['Content-Type: multipart/form-data']);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $sendData);
        $response = curl_exec($ch);
        $curl_error = curl_errno($ch);
        $error_text = curl_error($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if(!$curl_error) {
            if($status_code == 200) {
                return true;
            } else {
                print_r($error_text);
            }
        } else {
            throw new Exception("Can not send file: ".$error_text, 1);
        }
        return false;
    }

}
