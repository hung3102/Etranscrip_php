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

    public function actionCheckSr() {
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
        
        $x12Model = new DynamicModel(['fileName', 'serverUrl', 'schoolReportNumbers']);
        $x12Model->addRule(['fileName', 'serverUrl'], 'string', ['max'=> 255])
            ->addRule(['fileName', 'serverUrl'], 'required')
            ->addRule('serverUrl', 'url', ['defaultScheme' => 'http']);
        $x12Model->schoolReportNumbers = $schoolReportNumbers;
        return $this->renderAjax('renderModal', ['x12Model' => $x12Model]);
    }

    public function actionSendData() {
        $x12Model = new DynamicModel(['fileName', 'serverUrl', 'schoolReportNumbers']);
        $x12Model->addRule(['fileName', 'serverUrl'], 'string', ['max'=> 255])
            ->addRule(['fileName', 'serverUrl'], 'required')
            ->addRule('serverUrl', 'url', ['defaultScheme' => 'http']);

        $post = Yii::$app->request->post('DynamicModel');
        if ($post['fileName']!=null && $post['serverUrl']!=null) {
            $x12Model->fileName = $post['fileName'];
            $x12Model->serverUrl = $post['serverUrl'];
            $x12Model->schoolReportNumbers = unserialize($post['schoolReportNumbers']);
            $x12Model->validate();
            $x12Creator = new X12Creator();
            $data = $x12Creator->create($x12Model->schoolReportNumbers);
            file_put_contents(Yii::$app->basePath.'/x12resource/x12/'.$x12Model->fileName, $data);
            $x12File = Yii::$app->basePath.'/x12resource/x12/'.$x12Model->fileName;
            if($this->sendFile($x12File, $x12Model->fileName, $x12Model->serverUrl)) {
                Yii::$app->session->setFlash('success', 'Send file successfully.');
                return $this->redirect(['student/index']);
            }
        } else {
            return $this->renderAjax('renderModal', ['x12Model' => $x12Model]);
        }
    }

    protected function sendFile($filePath, $encryptedFileName, $serverUrl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
        curl_setopt($ch, CURLOPT_URL, $serverUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $fileSecure = new FileSecure();
        $securedData = $fileSecure->createSecuredData($filePath);
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
                // print_r($response);
                return true;
            } else {
                print_r($response);
            }
        }
        return false;
    }

}
