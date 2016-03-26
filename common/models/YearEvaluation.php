<?php

namespace common\models;

use Yii;
use common\models\bmodels\BaseYearEvaluation;

class YearEvaluation extends BaseYearEvaluation
{

    public static function tableName()
    {
        return '{{%year_evaluation}}';
    }

    public function rules()
    {
        return [
            [['schoolReportID', 'class', 'fromYear', 'toYear', 'studyDepartment', 'note', 'teacherName', 'missedLesson', 'upGradeType', 'principalApproval', 'principalName', 'date'], 'required'],
            [['schoolReportID', 'missedLesson'], 'integer'],
            [['schoolReportID', 'class', 'fromYear', 'toYear', 'studyDepartment', 'note', 'teacherName', 'missedLesson', 'principalApproval', 'principalName', 'date', 'created_time', 'updated_time'], 'safe'],
            [['note', 'teacherComment', 'principalApproval'], 'string'],
            [['class'], 'string', 'max' => 20],
            [['studyDepartment'], 'string', 'max' => 10],
            [['teacherName', 'principalName', 'upGradeType'], 'string', 'max' => 100],
            [['vocationalCertificate'], 'string', 'max' => 255],
            [['vocationalCertificateLevel'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schoolReportID' => 'School Report ID',
            'class' => 'Class',
            'fromYear' => 'From Year',
            'toYear' => 'To Year',
            'studyDepartment' => 'Study Department',
            'note' => 'Note',
            'teacherName' => 'Teacher Name',
            'missedLesson' => 'Missed Lesson',
            'upGradeType' => 'Up Grade Type',
            'vocationalCertificate' => 'Vocational Certificate',
            'vocationalCertificateLevel' => 'Vocational Certificate Level',
            'teacherComment' => 'Teacher Comment',
            'principalApproval' => 'Principal Approval',
            'principalName' => 'Principal Name',
            'date' => 'Date',
            'created_time' => 'Created Time',
            'updated_time' => 'Updated Time',
        ];
    }
}
