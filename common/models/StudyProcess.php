<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseStudyProcess;

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
            [['class', 'principleName'], 'string', 'max' => 50],
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
            'principleName' => 'Principle Name',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
