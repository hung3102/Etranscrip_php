<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseSchoolReport;
use yii\behaviors\TimestampBehavior;

class SchoolReport extends BaseSchoolReport
{
    
    public static function tableName()
    {
        return '{{%school_report}}';
    }

    public function rules()
    {
        return [
            [['number', 'studentID', 'date', 'principalName'], 'required'],
            [['number'], 'unique'],
            [['studentID'], 'integer'],
            [['number', 'studentID', 'date', 'principalName', 'created_time', 'updated_time'], 'safe'],
            [['number', 'principalName'], 'string', 'max' => 50],
            [['studentID'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'studentID' => 'Student ID',
            'date' => 'Date',
            'principalName' => 'Principal Name',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
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

    public function getStudent() {
        return $this->hasOne(Student::className(), ['id' => 'studentID']);
    }

    public function getStudyProcesses() {
        return $this->hasMany(StudyProcess::className(), ['schoolReportID' => 'id']);
    }

    public function getYearEvaluations() {
        return $this->hasMany(YearEvaluation::className(), ['schoolReportID' => 'id']);
    }
}
