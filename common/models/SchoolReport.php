<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseSchoolReport;

class SchoolReport extends BaseSchoolReport
{
    
    public static function tableName()
    {
        return '{{%school_report}}';
    }

    public function rules()
    {
        return [
            [['number', 'studentID', 'date'], 'required'],
            [['studentID'], 'integer'],
            [['number', 'studentID', 'date', 'created_time', 'updated_time'], 'safe'],
            [['number'], 'string', 'max' => 50],
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
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
