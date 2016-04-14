<?php
namespace common\models;

class Modal extends \yii\base\Model
{
    public $fileName;
    public $serverUrl;
    public $schoolReportNumbers;
    public $encryptType;

    public function rules()
    {
        return [
            [['fileName', 'serverUrl', 'schoolReportNumbers', 'encryptType'], 'required'],
            [['schoolReportNumbers', 'encryptType'], 'required', 'on' => 'autoSyn'],
            [['fileName', 'serverUrl'], 'string', 'max' => 100],
            ['serverUrl', 'url', 'defaultScheme' => 'http'],
            ['fileName', 'validateName', 'skipOnEmpty' => false, 'skipOnError' => false]
        ];
    }

    public function validateName($attribute, $params) {
        if(in_array($this->$attribute, ['data.edi'])) {
            $this->addError($attribute, 'File name must exclude "data.edi"');
        }
    }

}
