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
            file_put_contents(Yii::$app->basePath.'/x12resource/x12/'.$x12Model->fileName, $data);
            $x12File = Yii::$app->basePath.'/x12resource/x12/'.$x12Model->fileName;
            $serverUrl = $x12Model->serverUrl;
            $fileName = $post['fileName'];
        } else {
            file_put_contents(Yii::$app->basePath.'/x12resource/x12/data.edi', $data);
            $x12File = Yii::$app->basePath.'/x12resource/x12/data.edi';
            $serverUrl = 'http://172.17.0.2/Etranscript/frontend/web/student/receive-file';
            $fileName = 'data.edi';
        }
        if($this->sendFile($x12File, $fileName, $serverUrl, $x12Model->encryptType)) {
            Yii::$app->session->setFlash('success', 'Send file successfully.');
            return $this->redirect(['student/index']);
        }
    }

    protected function sendFile($filePath, $encryptedFileName, $serverUrl, $encryptType) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_URL, $serverUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $fileSecure = new FileSecure();
        $securedData = $fileSecure->createSecuredData($filePath, $encryptType);
        file_put_contents(Yii::$app->basePath.'/x12resource/encryptedX12/'.$encryptedFileName, 
            $securedData);
        $sendFile = Yii::$app->basePath.'/x12resource/encryptedX12/'.$encryptedFileName;
        $post = ["file_box" => $sendFile];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
        $response = curl_exec($ch);
        $curl_error = curl_errno($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if(!$curl_error) {
            if($status_code == 200) {
                return true;
            } else {
                print_r($response);
            }
        } else {
            throw new Exception("Error: Can not send file", 1);
        }
        return false;
    }

}
