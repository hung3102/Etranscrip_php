<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseStudyProcess;
use yii\behaviors\TimestampBehavior;

class StudyProcess extends BaseStudyProcess
{

    public static function tableName()
    {
        return '{{%study_process}}';
    }

    public function rules()
    {
        return [
            [['schoolReportID', 'fromYear', 'toYear', 'class', 'schoolID'], 'required'],
            [['schoolReportID', 'schoolID'], 'integer'],
            [['schoolReportID', 'fromYear', 'toYear', 'class', 'schoolID', 'created_time', 'updated_time'], 'safe'],
            [['class', 'principalName'], 'string', 'max' => 50],
        ];
    }

    public function behaviors()
    {
        date_default_timezone_set('Asia/Saigon');
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => date('Y:m:d h:i:s'),
                'createdAtAttribute' => 'created_time',
                'updatedAtAttribute' => 'updated_time',
            ],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolReportID' => 'School Report ID',
            'fromYear' => 'From Year',
            'toYear' => 'To Year',
            'class' => 'Class',
            'schoolID' => 'School ID',
            'principalName' => 'Principle Name',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }

    public function getSchool() {
        return $this->hasOne(School::className(), ['id' => 'schoolID']);
    }
}
