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
            [['schoolReportID', 'fromYear', 'toYear', 'principalName', 'class', 'schoolID'], 'required'],
            [['schoolReportID', 'schoolID', 'fromYear', 'toYear'], 'integer'],
            [['fromYear', 'toYear'], 'in', 'range' => range(1900, 2200)],
            [['schoolReportID', 'fromYear', 'toYear', 'class', 'schoolID', 'principalName', 'created_time', 'updated_time'], 'safe'],
            [['class', 'principalName'], 'string', 'max' => 50],
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
